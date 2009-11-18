<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Galeries");
include 'inc/premier.inc.php';

include '../init.inc.php';
?>

<h1><?php echo T_("Gestion des galeries"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_POST['id']))
	{
		$id = securiseTexte(basename($_POST['id']));
	}

	########################################################################
	##
	## Lister les galeries existantes
	##
	########################################################################

	if (isset($_POST['lister']))
	{
		$messagesScript = array ();
		
		if ($fic = @opendir($racine . '/site/fichiers/galeries'))
		{
			$i = 0;
			while($fichier = @readdir($fic))
			{
				if(is_dir($racine . '/site/fichiers/galeries/' . $fichier) && $fichier != '.' && $fichier != '..')
				{
					$i++;
					$fichier = sansEchappement($fichier);
					$idLien = rawurlencode($fichier);
					$cheminConfigGalerie = adminCheminConfigGalerie($racine, $fichier);
					
					if ($cheminConfigGalerie !== FALSE)
					{
						if ($porteDocumentsDroits['editer'])
						{
							$fichierDeConfiguration = '<li><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $idLien . '/' . basename($cheminConfigGalerie) . '&amp;dossierCourant=../site/fichiers/galeries/' . $idLien . '#messagesPorteDocuments">' . T_("Modifier le fichier de configuration") . "</a></li>\n";
						}
						else
						{
							$fichierDeConfiguration = '<li>' . T_("La galerie a un fichier de configuration") . "</li>\n";
						}
					}
					else
					{
						$fichierDeConfiguration = '<li>' . T_("Aucun fichier de configuration") . "</li>\n";
					}
					
					$parcoursDossier = '<li><a href="porte-documents.admin.php?action=parcourir&amp;valeur=../site/fichiers/galeries/' . $idLien . '&amp;dossierCourant=../site/fichiers/galeries/' . $idLien . '#fichiersEtDossiers">' . T_("Parcourir le dossier") . "</a></li>\n";
					
					$apercu = '';
					
					$messagesScript[] = '<li>' . sprintf(T_("Galerie %1\$s:"), $i) . "\n";
					$messagesScript[] = "<ul>\n";
					$messagesScript[] = "<li>" . sprintf(T_("Identifiant: %1\$s"), $fichier) . "</li>\n";
					$messagesScript[] = $fichierDeConfiguration;
					$messagesScript[] = $parcoursDossier;
					$messagesScript[] = $apercu;
					$messagesScript[] = "</ul></li>\n";
				}
			}
			
			closedir($fic);
		}
		else
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$racine/site/fichiers/galeries</code>") . "</li>\n";
		}
		
		if (empty($messagesScript))
		{
			$messagesScript[] = '<li>' . T_("Aucune galerie.") . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript, T_("Liste des galeries"));
	}

	########################################################################
	##
	## Ajouter des images
	##
	########################################################################

	if (isset($_POST['ajouter']))
	{
		$messagesScript = array ();
		
		if ($id == 'nouvelleGalerie' && empty($_POST['idNouvelleGalerie']))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Vous avez choisi de créer une nouvelle galerie, mais vous n'avez pas saisi de nom pour cette dernière.") . "</li>\n";
		}
		elseif (empty($_FILES['fichier']['name']))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier spécifié.") . "</li>\n";
		}
		elseif ($_FILES['fichier']['error'])
		{
			$messagesScript[] = adminMessageFilesError($_FILES['fichier']['error']);
		}
		else
		{
			if ($id == 'nouvelleGalerie')
			{
				$id = securiseTexte(basename($_POST['idNouvelleGalerie']));
			}
			
			$cheminGaleries = $racine . '/site/fichiers/galeries';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
			
			if (!file_exists($cheminGalerie))
			{
				$messagesScript[] = adminMkdir($cheminGalerie, octdec(755), TRUE);
			}
			
			if (file_exists($cheminGalerie) && isset($_FILES['fichier']))
			{
				$nomArchive = basename(securiseTexte($_FILES['fichier']['name']));
				
				if (file_exists($cheminGaleries . '/' . $nomArchive))
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), '<code>' . $nomArchive . '</code>', '<code>' . $cheminGaleries . '</code>') . "</li>\n";
				}
				elseif (move_uploaded_file($_FILES['fichier']['tmp_name'], $cheminGaleries . '/' . $nomArchive))
				{
					$typeMime = mimedetect_mime(array ('filepath' => $cheminGaleries . '/' . $nomArchive, 'filename' => $nomArchive), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					
					if ($typeMime != 'application/zip' && $typeMime != 'application/x-tar')
					{
						if (@rename($cheminGaleries . '/' . $nomArchive, $cheminGalerie . '/' . $nomArchive))
						{
							$messagesScript[] = '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . $nomArchive . '</code>', '<code>' . $cheminGalerie . '</code>') . "</li>\n";
						}
						else
						{
							$messagesScript[] = '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
						}
					}
					elseif ($typeMime == 'application/zip' && !function_exists('gzopen'))
					{
						$messagesScript[] = '<li class="erreur">' . T_("Les archives au format <code>ZIP</code> ne sont pas supportées.") . "</li>\n";
					}
					elseif ($typeMime == 'application/zip')
					{
						$resultatArchive = 0;
						$archive = new PclZip($cheminGaleries . '/' . $nomArchive);
						$resultatArchive = $archive->extract(PCLZIP_OPT_PATH, $cheminGaleries . '/' . $id . '/');
				
						if ($resultatArchive == 0)
						{
							$messagesScript[] = '<li class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s: %2\$s"), '<code>' . $nomArchive . '</code>', $archive->errorInfo(true)) . "</li>\n";
							$messagesScript[] = adminUnlink($cheminGaleries . '/' . $nomArchive);
						}
						else
						{
							foreach ($resultatArchive as $infoImage)
							{
								if ($infoImage['status'] == 'ok')
								{
									$messagesScript[] = '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . substr($infoImage['filename'], strlen($cheminGaleries . '/' . $id) + 1) . '</code>', '<code>' . $cheminGaleries . '/' . $id . '</code>') . "</li>\n";
								}
								elseif ($infoImage['status'] == 'newer_exist')
								{
									$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà, et est plus récent que celui de l'archive. Il n'y a donc pas eu extraction."), '<code>' . $infoImage['filename'] . '</code>') . "</li>\n";
								}
								else
								{
									$messagesScript[] = '<li class="erreur">' . sprintf(T_("Attention: une erreur a eu lieu avec le fichier %1\$s. Vérifiez son état sur le serveur (s'il s'y trouve), et ajoutez-le à la main si nécessaire."), '<code>' . $infoImage['filename'] . '</code>') . "</li>\n";
								}
							}
							$messagesScript[] =adminUnlink($cheminGaleries . '/' . $nomArchive);
						}
					}
					elseif ($typeMime == 'application/x-tar')
					{
						if (@rename($cheminGaleries . '/' . $nomArchive, $cheminGalerie . '/' . $nomArchive))
						{
							$fichierTar = new untar($cheminGalerie . '/' . $nomArchive);
							$listeFichiers = $fichierTar->getfilelist();
							for ($i = 0; $i < count($listeFichiers); $i++)
							{
								if ($listeFichiers[$i]['filetype'] == 'directory')
								{
									if (file_exists($cheminGalerie . '/' . $listeFichiers[$i]['filename']))
									{
										$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un dossier %1\$s existe déjà. Il n'a donc pas été créé."), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</li>\n";
									}
									else
									{
										$messagesScript[] = adminMkdir($cheminGalerie . '/' . $listeFichiers[$i]['filename'], octdec(755), TRUE);
									}
								}
								else
								{
									if (file_exists($cheminGalerie . '/' . $listeFichiers[$i]['filename']))
									{
										$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà. Il n'y a donc pas eu extraction."), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</li>\n";
									}
									elseif ($fic = @fopen($cheminGalerie . '/' . $listeFichiers[$i]['filename'], 'w'))
									{
										$donnees = $fichierTar->extract($listeFichiers[$i]['filename']);
										if (fwrite($fic, $donnees))
										{
											fclose($fic);
											$messagesScript[] = '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . $listeFichiers[$i]['filename'] . '</code>', '<code>' . $cheminGalerie . '</code>') . "</li>\n";
										}
										else
										{
											$messagesScript[] = '<li class="erreur">' . sprintf(T_("Attention: une erreur a eu lieu avec le fichier %1\$s. Vérifiez son état sur le serveur (s'il s'y trouve), et ajoutez-le à la main si nécessaire."), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</li>\n";
										}
									}
									else
									{
										$messagesScript[] = '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</li>";
									}
								}
							}
							unset($fichierTar);
							$messagesScript[] = adminUnlink($cheminGalerie . '/' . $nomArchive);
						}
						else
						{
							$messagesScript[] = '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
						}
					}
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
				}
			}
		}
		
		if (file_exists($_FILES['fichier']['tmp_name']))
		{
			@unlink($_FILES['fichier']['tmp_name']);
		}
		
		if (empty($messagesScript))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Aucune image n'a été extraite. Veuillez vérifier les instructions.") . "</li>\n";
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Ajout d'images"));
	}

	########################################################################
	##
	## Retailler les images originales
	##
	########################################################################

	if (isset($_POST['retailler']))
	{
		$messagesScript = array ();
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		$erreur = FALSE;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
			$erreur = TRUE;
		}
		else
		{
			$qualiteJpg = securiseTexte($_POST['qualiteJpg']);
			if ($_POST['retaillerAnalyserConfig'] == 'analyserConfig')
			{
				$analyserConfig = TRUE;
			}
			else
			{
				$analyserConfig = FALSE;
			}
			
			if (isset($_POST['retaillerRenommer']) && $_POST['retaillerRenommer'] == 'renommer')
			{
				if (isset($_POST['retaillerNePasRenommerMotifs']) && $_POST['retaillerNePasRenommerMotifs'] == 'nePasRenommerMotifs')
				{
					$renommerTout = FALSE;
				}
				else
				{
					$renommerTout = TRUE;
				}
				
				if ($fic = @opendir($cheminGalerie))
				{
					while($fichier = @readdir($fic))
					{
						if(!is_dir($cheminGalerie . '/' . $fichier))
						{
							$infoFichier = pathinfo(basename($fichier));
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							
							$renommer = FALSE;
							
							$typeMime = mimedetect_mime(array ('filepath' => $cheminGalerie . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							
							if (($renommerTout || (!preg_match('/-original\.' . $infoFichier['extension'] . '$/', $fichier) && !preg_match('/-vignette\.' . $infoFichier['extension'] . '$/', $fichier))) && adminImageValide($typeMime))
							{
								$renommer = TRUE;
							}
							
							if ($renommer && $analyserConfig)
							{
								$galerie = tableauGalerie(adminCheminConfigGalerie($racine, basename($cheminGalerie)));
								if (adminImageEstDeclaree($fichier, $galerie))
								{
									$renommer = FALSE;
								}
							}
							
							if ($renommer)
							{
								$nouveauNom = basename($fichier, '.' . $infoFichier['extension']);
								$nouveauNom .= '-original.' . $infoFichier['extension'];
								if (!file_exists($cheminGalerie . '/' . $nouveauNom))
								{
									$messagesScript[] = adminRename($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom);
								}
							}
						}
					}
			
					closedir($fic);
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
					$erreur = TRUE;
				}
			}
		
			// A: les images à traiter ont la forme `nom-original.extension`
		
			if (!$erreur)
			{
				if ($fic2 = @opendir($cheminGalerie))
				{
					while($fichier = @readdir($fic2))
					{
						$aTraiter = TRUE;
						
						if ($analyserConfig)
						{
							$galerie = tableauGalerie(adminCheminConfigGalerie($racine, basename($cheminGalerie)));
							if (adminImageEstDeclaree($fichier, $galerie))
							{
								$aTraiter = FALSE;
							}
						}
						
						if ($aTraiter)
						{
							$infoFichier = pathinfo(basename($fichier));
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							$nouveauNom = preg_replace('/-original\..{3,4}$/', '.', $fichier) . $infoFichier['extension'];
			
							if(!is_dir($cheminGalerie . '/' . $fichier) && preg_match('/-original\.' . $infoFichier['extension'] . '$/', $fichier) && !file_exists($cheminGalerie . '/' . $nouveauNom))
							{
								if (isset($_POST['actions']) && $_POST['actions'] == 'nettete')
								{
									$nettete = TRUE;
								}
								else
								{
									$nettete = FALSE;
								}
				
								$imageIntermediaireDimensionsVoulues = array ();
				
								if (isset($_POST['largeur']))
								{
									if (!empty($_POST['largeur']))
									{
										$imageIntermediaireDimensionsVoulues['largeur'] = securiseTexte($_POST['largeur']);
									}
									else
									{
										$imageIntermediaireDimensionsVoulues['largeur'] = 0;
									}
								}
				
								if (isset($_POST['hauteur']))
								{
									if (!empty($_POST['hauteur']))
									{
										$imageIntermediaireDimensionsVoulues['hauteur'] = securiseTexte($_POST['hauteur']);
									}
									else
									{
										$imageIntermediaireDimensionsVoulues['hauteur'] = 0;
									}
								}
								
								$typeMime = mimedetect_mime(array ('filepath' => $cheminGalerie . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
								
								$messagesScript[] = nouvelleImage($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom, $imageIntermediaireDimensionsVoulues, $qualiteJpg, $nettete, FALSE, $typeMime);
							}
						}
					}
				
					closedir($fic2);
				
					if (empty($messagesScript))
					{
						$messagesScript[] = '<li>' . T_("Aucune modification.") . "</li>\n";
					}
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
				}
			}
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Retaillage des images"));
	}

	########################################################################
	##
	## Supprimer des images
	##
	########################################################################

	if (isset($_POST['supprimerImages']))
	{
		$messagesScript = array ();
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		elseif (empty($_POST['listeAsupprimer']))
		{
			$messagesScript[] = '<li class="erreur">' . T_("La manière de générer la liste des fichiers à supprimer n'a pas été spécifiée.") . "</li>\n";
		}
		elseif ($_POST['listeAsupprimer'] != 'config' && $_POST['listeAsupprimer'] != 'motifs' && $_POST['listeAsupprimer'] != 'sansMotif')
		{
			$messagesScript[] = '<li class="erreur">' . T_("La manière spécifiée de générer la liste des fichiers à supprimer n'est pas valide.") . "</li>\n";
		}
		else
		{
			if ($_POST['listeAsupprimer'] == 'config')
			{
				$analyserConfig = TRUE;
				$analyserSeulementConfig = TRUE;
				$exclureMotifsCommeIntermediaires = TRUE;
			}
			elseif ($_POST['listeAsupprimer'] == 'motifs')
			{
				$analyserConfig = FALSE;
				$analyserSeulementConfig = FALSE;
				$exclureMotifsCommeIntermediaires = TRUE;
			}
			elseif ($_POST['listeAsupprimer'] == 'sansMotif')
			{
				$analyserConfig = FALSE;
				$analyserSeulementConfig = FALSE;
				$exclureMotifsCommeIntermediaires = FALSE;
			}
			
			if ($fic = @opendir($cheminGalerie))
			{
				while($fichier = @readdir($fic))
				{
					$fichierAsupprimer = FALSE;
					if(!is_dir($cheminGalerie . '/' . $fichier))
					{
						$typeMime = mimedetect_mime(array ('filepath' => $cheminGalerie . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
						
						$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime);
						
						if (
							(isset($_POST['supprimerImagesVignettes']) && $_POST['supprimerImagesVignettes'] == 'supprimer' && $versionImage == 'vignette') ||
							(isset($_POST['supprimerImagesIntermediaires']) && $_POST['supprimerImagesIntermediaires'] == 'supprimer' && $versionImage == 'intermediaire') ||
							(isset($_POST['supprimerImagesOriginal']) && $_POST['supprimerImagesOriginal'] == 'supprimer' && $versionImage == 'original') ||
							(isset($_POST['supprimerImagesConfig']) && $_POST['supprimerImagesConfig'] == 'supprimer' && ($fichier == 'config.ini.txt' || $fichier == 'config.ini'))
						)
						{
							$fichierAsupprimer = TRUE;
						}
					
						if ($fichierAsupprimer)
						{
							$messagesScript[] = adminUnlink($cheminGalerie . '/' . $fichier);
						}
					}
				}
				
				closedir($fic);
				
				if (isset($_POST['supprimerImagesDossier']) && $_POST['supprimerImagesDossier'] == 'supprimer')
				{
					if (adminDossierEstVide($cheminGalerie))
					{
						$messagesScript[] = adminRmdir($cheminGalerie);
					}
					else
					{
						$messagesScript[] = '<li>' . sprintf(T_("Le dossier %1\$s n'est pas vide, il ne sera donc pas supprimé."), "<code>$cheminGalerie</code>") . "</li>\n";
					}
				}
			}
			else
			{
				$messagesScript[] = "<li class='erreur'>" . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
			}
		
			// Vignettes de navigation tatouées
			if (isset($_POST['supprimerImagesVignettesAvecTatouage']) && $_POST['supprimerImagesVignettesAvecTatouage'] == 'supprimer')
			{
				$cheminTatouage = $racine . '/site/fichiers/galeries/' . $id . '/tatouage';
				
				if (!file_exists($cheminTatouage))
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le dossier des vignettes avec tatouage %1\$s n'existe pas."), "<code>$cheminTatouage</code>") . "</li>\n";
				}
				elseif ($fic = @opendir($cheminTatouage))
				{
					while($fichier = @readdir($fic))
					{
						if(!is_dir($cheminTatouage . '/' . $fichier))
						{
							$infoFichier = pathinfo(basename($fichier));
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							
							$typeMime = mimedetect_mime(array ('filepath' => $cheminTatouage . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							
							if (preg_match('/-vignette-(precedent|suivant)\.' . $infoFichier['extension'] . '$/', $fichier) && adminImageValide($typeMime))
							{
								$messagesScript[] = adminUnlink($cheminTatouage . '/' . $fichier);
							}
						}
					}
					
					closedir($fic);
					
					if (adminDossierEstVide($cheminTatouage))
					{
						$messagesScript[] = adminRmdir($cheminTatouage);
					}
					else
					{
						$messagesScript[] = '<li>' . sprintf(T_("Le dossier %1\$s n'est pas vide, il ne sera donc pas supprimé."), "<code>$cheminTatouage</code>") . "</li>\n";
					}
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminTatouage</code>") . "</li>\n";
				}
			}
		}
		
		if (empty($messagesScript))
		{
			$messagesScript[] = '<li>' . T_("Aucune image à traiter.") . "</li>\n";
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Suppression d'images"));
	}
	
	########################################################################
	##
	## Renommer une galerie
	##
	########################################################################

	if (isset($_POST['renommer']))
	{
		$messagesScript = array ();
		$nouvelId = securiseTexte($_POST['idNouveauNomGalerie']);
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		$nouveauCheminGalerie = $racine . '/site/fichiers/galeries/' . $nouvelId;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		elseif (file_exists($nouveauCheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s existe déjà."), "<code>$nouvelId</code>") . "</li>\n";
		}
		else
		{
			$messagesScript[] = adminRename($cheminGalerie, $nouveauCheminGalerie);
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Renommage d'une galerie"));
	}
	
	########################################################################
	##
	## Créer une page web de galerie
	##
	########################################################################

	if (isset($_POST['creerPage']))
	{
		$messagesScript = array ();
		$page = basename(securiseTexte($_POST['page']));
		$cheminPage = '../' . dirname(securiseTexte($_POST['page']));
		if ($cheminPage == '../.')
		{
			$cheminPage = '..';
		}
		$cheminInclude = preg_replace('|[^/]+/|', '../', $cheminPage);
		$cheminInclude = dirname($cheminInclude);
		if ($cheminInclude == '.')
		{
			$cheminInclude = '';
		}
		if (!empty($cheminInclude))
		{
			$cheminInclude .= '/';
		}
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id);
		
			if (!file_exists($cheminConfigGalerie))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'a pas de fichier de configuration."), "<code>$id</code>") . "</li>\n";
			}
			else
			{
				if (!file_exists($cheminPage))
				{
					$messagesScript[] = adminMkdir($cheminPage, octdec(0755), TRUE);
				}
				
				if (file_exists($cheminPage))
				{
					if (file_exists($cheminPage . '/' . $page))
					{
						if ($porteDocumentsDroits['editer'])
						{
							$messagesScript[] = '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href='%2\$s'>éditer le fichier</a> ou <a href='%3\$s'>visiter la page</a>."), '<code>' . $cheminPage . '/' . $page . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '&amp;dossierCourant=' . rawurlencode(dirname($cheminPage . '/' . $page)) . '#messagesPorteDocuments', $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
						}
						else
						{
							$messagesScript[] = '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href='%2\$s'>visiter la page</a>."), '<code>' . $cheminPage . '/' . $page . '</code>', $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
						}
					}
					else
					{
						if ($fic = @fopen($cheminPage . '/' . $page, 'a'))
						{
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$contenu .= '$baliseTitle = "Galerie ' . $id . '";' . "\n";
							$contenu .= '$description = "Galerie ' . $id . '";' . "\n";
							$contenu .= '$idGalerie = "' . $id . '";' . "\n";
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							$contenu .= '<h1>Galerie <em>' . $id . '</em></h1>' . "\n";
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
						
							fclose($fic);
							
							if ($porteDocumentsDroits['editer'])
							{
								$messagesScript[] = '<li>' . sprintf(T_("Le modèle de page a été créé. Vous pouvez <a href='%1\$s'>éditer le fichier</a> ou <a href='%2\$s'>visiter la page</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '&amp;dossierCourant' . rawurlencode(dirname($cheminPage . '/' . $page)) . '=#messagesPorteDocuments', $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
							}
							else
							{
								$messagesScript[] = '<li>' . sprintf(T_("Le modèle de page a été créé. Vous pouvez <a href='%1\$s'>visiter la page</a>."), $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
							}
						}
						else
						{
							$messagesScript[] = '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>\n";
						}
					}
				}
			}
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Création d'une page web de galerie"));
	}

	########################################################################
	##
	## Afficher un modèle de fichier de configuration
	##
	########################################################################

	if (isset($_POST['modeleConf']))
	{
		$messagesScript = array ();
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			if (isset($_POST['configExclureMotifsCommeIntermediaires']) && $_POST['configExclureMotifsCommeIntermediaires'] == 'activer')
			{
				$exclureMotifsCommeIntermediaires = TRUE;
			}
			else
			{
				$exclureMotifsCommeIntermediaires = FALSE;
			}
			
			if ($fic = @opendir($cheminGalerie))
			{
				$listeFichiers = '';
				$tableauFichiers = array ();
				while($fichier = @readdir($fic))
				{
					if(!is_dir($cheminGalerie . '/' . $fichier))
					{
						$typeMime = mimedetect_mime(array ('filepath' => $cheminGalerie . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
						
						$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, FALSE, $exclureMotifsCommeIntermediaires, FALSE, $typeMime);
						
						if (adminImageValide($typeMime) && $versionImage != 'vignette' && $versionImage != 'original')
						{
							$tableauFichiers[] = $fichier;
						}
					}
				}
				closedir($fic);
				
				natcasesort($tableauFichiers);
				$listeFichiers = '';
				
				foreach ($tableauFichiers as $cle)
				{
					$listeFichiers .= "[$cle]\n";
					
					if (isset($_POST['info']) && $_POST['info'][0] != 'aucun')
					{
						foreach ($_POST['info'] as $champ)
						{
							$listeFichiers .= securiseTexte($champ) . "=\n";
						}
					}
					
					$listeFichiers .= "\n";
				}
				
				if (!empty($listeFichiers))
				{
					$messagesScript[] = '<li><pre id="listeFichiers">' . $listeFichiers . "</pre></li>\n";
					$messagesScript[] = "<li><a href=\"javascript:adminSelectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				}
			}
			else
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
			}
		}
		
		if (empty($messagesScript))
		{
			$messagesScript[] = '<li>' . T_("Aucune image dans la galerie.") . "</li>\n";
		}
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		echo adminMessagesScript($messagesScript, T_("Modèle de fichier de configuration"));
	}

	########################################################################
	##
	## Créer ou mettre à jour le fichier de configuration
	##
	########################################################################

	$sousBoiteFichierConfigDebut = FALSE;

	if (isset($_POST['config']) && $_POST['config'] == 'maj')
	{
		$messagesScript = array ();
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			if (isset($_POST['configExclureMotifsCommeIntermediaires']) && $_POST['configExclureMotifsCommeIntermediaires'] == 'activer')
			{
				$exclureMotifsCommeIntermediaires = TRUE;
			}
			else
			{
				$exclureMotifsCommeIntermediaires = FALSE;
			}
			
			$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id);
		
			if (file_exists($cheminConfigGalerie))
			{
				$configExisteAuDepart = TRUE;
			}
			else
			{
				$configExisteAuDepart = FALSE;
			}
			
			if (adminMajConfigGalerie($racine, $id, '', TRUE, $exclureMotifsCommeIntermediaires, FALSE, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance))
			{
				if ($configExisteAuDepart)
				{
					$messagesScript[] = '<li>' . sprintf(T_("Mise à jour du fichier de configuration %1\$s effectuée."), '<code>' . $cheminConfigGalerie . '</code>') . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li>' . sprintf(T_("Création du fichier de configuration %1\$s effectuée."), '<code>' . $cheminConfigGalerie . '</code>') . "</li>\n";
				}
			}
			else
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("Erreur lors de la création ou de la mise à jour du fichier de configuration %1\$s. Veuillez vérifier manuellement son contenu."), "<code>$cheminConfigGalerie</code>") . "</li>\n";
			}
		}
		
		$sousBoiteFichierConfigDebut = TRUE;
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
		
		array_unshift($messagesScript, '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n");
		
		echo '<h4>' . T_("Actions effectuées") . "</h4>\n" ;
		
		echo "<ul>\n";
		
		foreach ($messagesScript as $messageScript)
		{
			echo $messageScript;
		}
		
		echo "</ul>\n";
	}
	
	if ((isset($_POST['modeleConf']) || (isset($_POST['config']) && $_POST['config'] == 'maj')) && adminCheminConfigGalerie($racine, $id) !== FALSE)
	{
		if (!$sousBoiteFichierConfigDebut)
		{
			$sousBoiteFichierConfigDebut = TRUE;
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
		}
	
		$id = rawurlencode($id);
		$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id);
		echo '<h4>' . T_("Information") . "</h4>\n" ;
		
		echo "<ul>\n";
		if ($porteDocumentsDroits['editer'])
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $id . '/' . basename($cheminConfigGalerie) . '&amp;dossierCourant=../site/fichiers/galeries/' . $id . '#messagesPorteDocuments">' . T_("Modifier le fichier.") . "</a></li>\n";
		}
		else
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . "</li>\n";
		}
		echo "</ul>\n";
	}
	
	if ($sousBoiteFichierConfigDebut)
	{
		echo "</div><!-- /class=sousBoite -->\n";
	}
	?>
</div><!-- /boiteMessages -->

<?php
########################################################################
##
## Formulaires
##
########################################################################
?>

<div class="boite">
	<h2><?php echo T_("Lister les galeries existantes"); ?></h2>
	
	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<?php if ($porteDocumentsDroits['editer']): ?>
				<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes. Si la galerie a un fichier de configuration, un lien vous permettra de modifier ce dernier dans le porte-documents."); ?></p>
			<?php else: ?>
				<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes, qu'elles aient ou non un fichier de configuration."); ?></p>
			<?php endif; ?>

			<p><input type="submit" name="lister" value="<?php echo T_('Lister les galeries'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Ajouter des images"); ?></h2>
	
	<?php if (function_exists('gzopen')): ?>
		<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (<code>.tar</code>) ou ZIP (<code>.zip</code>). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>
	<?php else: ?>
		<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (<code>.tar</code>). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>
	<?php endif; ?>
	
	<p><?php echo T_("Vous pouvez également ajouter une seule image en choisissant un fichier image au lieu d'une archive."); ?></p>

	<p><?php printf(T_("<strong>Taille maximale d'un transfert de fichier:</strong> %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichiers), $tailleMaxFichiers); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post" enctype="multipart/form-data">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie (il est possible de créer une nouvelle galerie):"); ?></label><br />
				<select name="id">
					<option value="nouvelleGalerie"><?php echo T_("Nouvelle galerie:"); ?></option>
					<?php $galeries = adminListeGaleries($racine, FALSE); ?>
					<?php if (!empty($galeries)): ?>
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select> <input type="text" name="idNouvelleGalerie" /></p>

				<p><label><?php echo T_("Fichier:"); ?></label><br />
				<input type="file" name="fichier" size="25"/></p>
			</fieldset>

			<fieldset>
				<legend><?php echo T_("Fichier de configuration"); ?></legend>
				
				<ul>
					<li><input type="checkbox" name="config" value="maj" checked="checked" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
					<ul>
						<li><input type="checkbox" name="configExclureMotifsCommeIntermediaires" value="activer" checked="checked" /> <label><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
					</ul></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter des images'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer des images de taille intermédiaire à partir des images originales"); ?></h2>

	<p><?php echo T_("Vous pouvez générer automatiquement une copie réduite (qui sera utilisée comme étant la version intermédiaire dans la galerie) de chaque image originale. Aucune image au format original ne sera modifiée."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, FALSE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			
				<p><label><?php echo T_("Taille maximale de la version intermédiaire (largeur × hauteur):"); ?></label><br />
				<?php echo T_("La plus grande taille possible contenable dans les dimensions données sera utilisée, sans toutefois dépasser la taille originale. Si une seule dimension est précisée, l'autre sera calculée à partir de la dimension donnée ainsi que des dimensions de l'image source. Les proportions de l'image sont conservées. Au moins une dimension doit être donnée."); ?><br />
				<input type="text" name="largeur" size="4" value="500" /> <?php echo T_("px de largeur"); ?> <?php echo T_("×"); ?> <input type="text" name="hauteur" size="4" value="500" /> <?php echo T_("px de hauteur"); ?></p>
				
				<p><label><?php echo T_("S'il y a lieu, qualité des images JPG générées (0-100):"); ?></label><br />
				<input type="text" name="qualiteJpg" value="90" size="2" /></p>

				<p><input type="checkbox" name="actions" value="nettete" /> <label><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)."); ?></label></p>
				
				<p><?php echo T_("La liste des images originales retaillables est consitituée des images dont le nom satisfait le motif <code>nom-original.extension</code>. Voici des options relatives à cette liste:"); ?></p>
				<ul>
					<li><input type="checkbox" name="retaillerRenommer" value="renommer" checked="checked" /> <label><?php echo T_("Renommer préalablement les images de la galerie en <code>nom-original.extension</code>."); ?></label></li>
					
					<li><input type="checkbox" name="retaillerNePasRenommerMotifs" value="nePasRenommerMotifs" checked="checked" /> <label><?php echo T_("S'il y a lieu, ignorer lors du renommage les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>."); ?></label></li>
					
					<li><input type="checkbox" name="retaillerAnalyserConfig" value="analyserConfig" checked="checked" /> <label><?php echo T_("Ignorer lors du renommage (s'il y a lieu) ainsi que lors du retaillage les images déclarées dans le fichier de configuration (s'il existe). Toute image déjà présente dans un des champs <code>intermediaireNom</code>, <code>vignetteNom</code> ou <code>originalNom</code> du fichier de configuration est nécessairement une version intermédiaire ou a nécessairement une version intermédiaire associée."); ?></label></li>
				</ul>
				
				<p><?php echo T_("Dans tous les cas, il n'y a pas de création d'image intermédiaire si les fichiers <code>nom-original.extension</code> et <code>nom.extension</code> existent déjà tous les deux."); ?></p>
			</fieldset>

			<fieldset>
				<legend><?php echo T_("Fichier de configuration"); ?></legend>
				
				<ul>
					<li><input type="checkbox" name="config" value="maj" checked="checked" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
					<ul>
						<li><input type="checkbox" name="configExclureMotifsCommeIntermediaires" value="activer" checked="checked" /> <label><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
					</ul></li>
				</ul>
			</fieldset>

			<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

			<p><input type="submit" name="retailler" value="<?php echo T_('Retailler les images originales'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Supprimer des images"); ?></h2>
	
	<p><?php echo T_("La liste des images potentiellement supprimables peut être générée de trois manières différentes:"); ?></p>
	
	<ul>
		<li><?php echo T_("seulement par analyse du fichier de configuration, s'il existe. Les images intermédiaires sont les images déclarées dans le champ <code>intermediaireNom</code>. Les vignettes sont les images déclarées dans le champ <code>vignetteNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-vignette.extension</code> (à moins que ces derniers soient déclarés dans le fichier de configuration comme étant une version différente) et pour lesquels une image intermédiaire sans le motif <code>-vignette</code> existe. Les fichiers au format original sont ceux déclarés dans le champ <code>originalNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-original.extension</code> (à moins d'une délcaration différente dans le fichier de configuration) et pour lesquels une image intermédiaire sans le motif <code>-original</code> existe;"); ?></li>
		
		<li><?php echo T_("par reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Les vignettes sont les images dont le nom satisfait le motif <code>nom-vignette.extension</code>. Les fichiers au format original sont ceux dont le nom satisfait le motif <code>nom-original.extension</code>. Les images intermédiaires sont les images dont le nom ne satisfait aucun motif (<code>nom-vignette.extension</code> ou <code>nom-original.extension</code>)."); ?></li>
		
		<li><?php echo T_("sans reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Toutes les images sont considérées comme étant des images intermédiaires."); ?></li>
	</ul>
	
	<p><?php echo T_("Tout d'abord, vous pouvez supprimer les vignettes d'une galerie pour forcer leur regénération automatique."); ?></p>
	
	<p><?php echo T_("Aussi, si la navigation entre les oeuvres d'une galerie est réalisée avec des vignettes et si <code>\$galerieNavigationVignettesTatouage</code> vaut <code>TRUE</code>, de nouvelles vignettes de navigation vers les oeuvres précédente et suivante sont générées, et contiennent une petite image (par défaut une flèche) au centre. Vous pouvez supprimer ces vignettes de navigation avec tatouage."); ?></p>
	
	<p><?php echo T_("Vous pouvez également supprimer les images de taille intermédiaires ou au format original."); ?></p>
	
	<p><?php echo T_("Il est aussi possible de supprimer le fichier de configuration de la galerie ainsi que le dossier de la galerie si ce dernier est vide."); ?></p>
	
	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, FALSE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
				
				<p><?php echo T_("Génération de la liste des images potentiellement supprimables:"); ?></p>
				
				<ul>
					<li><input type="radio" name="listeAsupprimer" value="config" checked="checked" /> <?php echo T_("seulement par analyse du fichier de configuration;"); ?></li>
					
					<li><input type="radio" name="listeAsupprimer" value="motifs" /> <?php echo T_("par reconnaissance d'un motif dans le nom des fichiers;"); ?></li>
					
					<li><input type="radio" name="listeAsupprimer" value="sansMotif" /> <?php echo T_("sans reconnaissance de motif dans le nom des fichiers."); ?></li>
				</ul>
				
				<ul>
					<li><input type="checkbox" name="supprimerImagesVignettes" value="supprimer" /> <label><?php echo T_("Supprimer les vignettes."); ?></label></li>
					
					<li><input type="checkbox" name="supprimerImagesVignettesAvecTatouage" value="supprimer" /> <label><?php echo T_("Supprimer les vignettes de navigation avec tatouage."); ?></label></li>
					
					<li><input type="checkbox" name="supprimerImagesIntermediaires" value="supprimer" /> <label><?php echo T_("Supprimer les images intermédiaires."); ?></label></li>
					
					<li><input type="checkbox" name="supprimerImagesOriginal" value="supprimer" /> <label><?php echo T_("Supprimer les images originales."); ?></label></li>
					
					<li><input type="checkbox" name="supprimerImagesConfig" value="supprimer" /> <label><?php echo T_("Supprimer le fichier de configuration."); ?></label></li>
					
					<li><input type="checkbox" name="supprimerImagesDossier" value="supprimer" /> <label><?php echo T_("Supprimer le dossier de la galerie s'il est vide."); ?></label></li>
				</ul>
			</fieldset>
			
			<fieldset>
				<legend><?php echo T_("Fichier de configuration"); ?></legend>
				
				<ul>
					<li><input type="checkbox" name="config" value="maj" checked="checked" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
					<ul>
						<li><input type="checkbox" name="configExclureMotifsCommeIntermediaires" value="activer" checked="checked" /> <label><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
					</ul></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="supprimerImages" value="<?php echo T_('Supprimer les images'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Renommer une galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez renommer une galerie. S'il s'agit d'une galerie déjà utilisée sur votre site, ne pas oublier de modifier la valeur de la variable <code>\$idGalerie</code> dans la page web de votre galerie."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post" enctype="multipart/form-data">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant actuel de la galerie et son nouvel identifiant:"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, FALSE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select> <input type="text" name="idNouveauNomGalerie" />
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			</fieldset>
			
			<p><input type="submit" name="renommer" value="<?php echo T_('Renommer la galerie'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer une page web de galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez ajouter une page sur votre site pour présenter une galerie."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie (ayant un fichier de configuration):"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, TRUE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer au moins une galerie ayant un fichier de configuration."); ?></strong>
				<?php endif; ?>
				</p>

				<p><label><?php echo T_("Emplacement de la page web:"); ?></label><br />
				<?php echo $urlRacine . '/'; ?><input type="text" name="page" /></p>
			</fieldset>
			
			<p><input type="submit" name="creerPage" value="<?php echo T_('Créer une page web'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer ou mettre à jour un fichier de configuration"); ?></h2>

	<p><?php echo T_("Crée ou met à jour le fichier de configuration de cette galerie."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, FALSE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			</fieldset>
			
			<fieldset>
				<legend><?php echo T_("Fichier de configuration"); ?></legend>
				
				<p><input type="checkbox" name="configExclureMotifsCommeIntermediaires" value="activer" checked="checked" /> <label><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></p>
			</fieldset>
			
			<p><input type="submit" name="majConf" value="<?php echo T_('Créer ou mettre à jour'); ?>" /></p>
			
			<input type="hidden" name="config" value="maj" />
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Afficher un modèle de fichier de configuration"); ?></h2>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $galeries = adminListeGaleries($racine, FALSE); ?>
				<?php if (!empty($galeries)): ?>
					<select name="id">
						<?php foreach ($galeries as $galerie): ?>
							<option value="<?php echo $galerie; ?>"><?php echo $galerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>

				<p><label><?php echo T_("Pour chaque image intermédiaire, ajouter des paramètres vides:"); ?></label><br />
				<select name="info[]" multiple="multiple" size="4">
					<option value="aucun" selected="selected"><?php echo T_("Aucun"); ?></option>
					<option value="id">id</option>
					<option value="vignetteNom">vignetteNom</option>
					<option value="vignetteLargeur">vignetteLargeur</option>
					<option value="vignetteHauteur">vignetteHauteur</option>
					<option value="vignetteAlt">vignetteAlt</option>
					<option value="intermediaireLargeur">intermediaireLargeur</option>
					<option value="intermediaireHauteur">intermediaireHauteur</option>
					<option value="intermediaireAlt">intermediaireAlt</option>
					<option value="intermediaireLegende">intermediaireLegende</option>
					<option value="pageIntermediaireBaliseTitle">pageIntermediaireBaliseTitle</option>
					<option value="pageIntermediaireDescription">pageIntermediaireDescription</option>
					<option value="pageIntermediaireMotsCles">pageIntermediaireMotsCles</option>
					<option value="originalNom">originalNom</option>
					<option value="exclure">exclure</option>
				</select></p>
			</fieldset>
			
			<fieldset>
				<legend><?php echo T_("Fichier de configuration"); ?></legend>
				
				<p><input type="checkbox" name="configExclureMotifsCommeIntermediaires" value="activer" checked="checked" /> <label><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>."); ?></label></p>
			</fieldset>
			
			<p><input type="submit" name="modeleConf" value="<?php echo T_('Afficher un fichier de configuration'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
