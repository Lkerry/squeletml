<?php
include 'inc/zero.inc.php';
super_set_time_limit($delaiExpirationScript);
$baliseTitle = T_("Galeries");
$boitesDeroulantes = '.fichierConfigAdminGaleries #ajoutParametresAdminGaleries .aideAdminGaleries';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des galeries"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_POST['id']))
	{
		$id = securiseTexte(superBasename($_POST['id']));
	}
	
	if (!empty($adminFiltreAccesDossiers))
	{
		$tableauFiltresAccesDossiers = explode('|', $adminFiltreAccesDossiers);
		$tableauFiltresAccesDossiers = adminTableauCheminsCanoniques($tableauFiltresAccesDossiers);
	}
	else
	{
		$tableauFiltresAccesDossiers = array ();
	}
	
	########################################################################
	##
	## Listage des galeries existantes.
	##
	########################################################################
	
	if (isset($_POST['lister']))
	{
		$messagesScript = '';
		
		if ($fic = @opendir($racine . '/site/fichiers/galeries'))
		{
			$i = 0;
			
			while ($fichier = @readdir($fic))
			{
				if (is_dir($racine . '/site/fichiers/galeries/' . $fichier) && $fichier != '.' && $fichier != '..')
				{
					$i++;
					$fichier = sansEchappement($fichier);
					$idLien = rawurlencode($fichier);
					$cheminConfigGalerie = cheminConfigGalerie($racine, $fichier);
					
					if ($cheminConfigGalerie)
					{
						if ($adminPorteDocumentsDroits['editer'])
						{
							$fichierDeConfiguration = '<li><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $idLien . '/' . superBasename($cheminConfigGalerie) . '&amp;dossierCourant=../site/fichiers/galeries/' . $idLien . '#messages">' . T_("Modifier le fichier de configuration") . "</a></li>\n";
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
					
					if ($cheminConfigGalerie)
					{
						$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $fichier), TRUE);
						$racineImgSrc = $racine . '/site/fichiers/galeries/' . $fichier;
						$nombreDoeuvres = count($tableauGalerie);
						$corpsMinivignettes = '';
						
						for ($j = 0; $j <= ($nombreDoeuvres - 1) && $j < $nombreDoeuvres; $j++)
						{
							$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$j]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							$minivignette = oeuvre($racine, $urlRacine, dirname($cheminConfigGalerie), $urlRacine . '/site/fichiers/galeries/' . $fichier, FALSE, $nombreDeColonnes, $tableauGalerie[$j], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, FALSE);
							preg_match('|(<img[^>]+/>)|', $minivignette, $resultat);
							$minivignette = $resultat[1];
							
							if ($adminActiverInfobulle['apercuGalerie'])
							{
								$infobulle = adminInfobulle($racineAdmin, $urlRacineAdmin, dirname($cheminConfigGalerie) . '/' . $tableauGalerie[$j]['intermediaireNom'], FALSE, $adminTailleCache, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							}
							else
							{
								$infobulle = '';
							}
							
							$config = '';
							
							foreach ($tableauGalerie[$j] as $parametre => $valeur)
							{
								if ($parametre == 'intermediaireNom')
								{
									$sectionConfig = "[$valeur]<br />\n";
								}
								else
								{
									$config .= "$parametre=$valeur<br />\n";
								}
							}
							
							$config = "<br />\n<strong>" . T_("Configuration:") . "</strong><br />\n" . $sectionConfig . $config;
							$infobulle = str_replace('</span>', $config . '</span>', $infobulle);
							$minivignette = preg_replace('|(<img[^>]+/>)|', $minivignette, $infobulle);
							$corpsMinivignettes .= $minivignette;
						}
						
						if (!empty($corpsMinivignettes))
						{
							$corpsMinivignettes = '<div class="sepGalerieMinivignettes"></div>' . "\n" . '<div class="galerieMinivignettes">' . "\n" . $corpsMinivignettes;
							$corpsMinivignettes .= '</div><!-- /.galerieMinivignettes -->' . "\n";
							$corpsMinivignettes .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
							$corpsMinivignettes .= '<p class="note">' . sprintf(T_("Note: si l'affichage de l'aperçu est très lent, il est possible de le désactiver (variable <code>%1\$s</code> dans le fichier de configuration de l'administration)."), '$adminActiverInfobulle[\'apercuGalerie\']') . "</p>\n";
							$apercu = '<li>' . sprintf(T_ngettext("Aperçu (%1\$s image): %2\$s", "Aperçu (%1\$s images): %2\$s", $nombreDoeuvres), $nombreDoeuvres, $corpsMinivignettes) . "</li>\n";
						}
						else
						{
							$apercu = '';
						}
					}
					else
					{
						$apercu = '';
					}
					
					$messagesScript .= '<li>' . sprintf(T_("Galerie %1\$s:"), $i) . "\n";
					$messagesScript .= "<ul>\n";
					$messagesScript .= '<li>' . sprintf(T_("Identifiant: %1\$s"), $fichier) . "</li>\n";
					$messagesScript .= $fichierDeConfiguration;
					$messagesScript .= $parcoursDossier;
					$messagesScript .= $apercu;
					$messagesScript .= "</ul></li>\n";
				}
			}
			
			closedir($fic);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$racine/site/fichiers/galeries</code>") . "</li>\n";
		}
		
		if (empty($messagesScript))
		{
			$messagesScript .= '<li>' . T_("Aucune galerie.") . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript, T_("Liste des galeries"));
	}
	
	########################################################################
	##
	## Ajout d'images.
	##
	########################################################################
	
	if (isset($_POST['ajouter']) || (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > adminPhpIniOctets(ini_get('post_max_size'))))
	{
		$messagesScript = '';
		
		if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > adminPhpIniOctets(ini_get('post_max_size')))
		{
			$messagesScript .= '<li class="erreur">' . T_("Le fichier téléchargé excède la taille de <code>post_max_size</code>, configurée dans le <code>php.ini</code>.") . "</li>\n";
		}
		elseif ($id == 'nouvelleGalerie' && empty($_POST['idNouvelleGalerie']))
		{
			$messagesScript .= '<li class="erreur">' . T_("Vous avez choisi de créer une nouvelle galerie, mais vous n'avez pas saisi de nom pour cette dernière.") . "</li>\n";
		}
		elseif (empty($_FILES['fichier']['name']))
		{
			$messagesScript .= '<li class="erreur">' . T_("Aucun fichier spécifié.") . "</li>\n";
		}
		elseif ($_FILES['fichier']['error'])
		{
			$messagesScript .= adminMessageFilesError($_FILES['fichier']['error']);
		}
		else
		{
			if ($id == 'nouvelleGalerie')
			{
				$id = securiseTexte(superBasename($_POST['idNouvelleGalerie']));
			}
			
			$cheminGaleries = $racine . '/site/fichiers/galeries';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
			
			if (!file_exists($cheminGalerie))
			{
				$messagesScript .= adminMkdir($cheminGalerie, octdec(755), TRUE);
			}
			
			if (file_exists($cheminGalerie) && isset($_FILES['fichier']))
			{
				$nomArchive = superBasename(securiseTexte($_FILES['fichier']['name']));
				
				if (file_exists($cheminGaleries . '/' . $nomArchive))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), "<code>$nomArchive</code>", "<code>$cheminGaleries</code>") . "</li>\n";
				}
				elseif (move_uploaded_file($_FILES['fichier']['tmp_name'], $cheminGaleries . '/' . $nomArchive))
				{
					$typeMime = typeMime($cheminGaleries . '/' . $nomArchive, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					
					if (!adminTypeMimePermis($typeMime, $adminFiltreTypesMime, $adminTypesMimePermis))
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), "<code>$nomArchive</code>", "<code>$typeMime</code>") . "</li>\n";
					}
					elseif ($typeMime != 'application/zip' && $typeMime != 'application/x-tar')
					{
						if (@rename($cheminGaleries . '/' . $nomArchive, $cheminGalerie . '/' . $nomArchive))
						{
							$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . $nomArchive . '</code>', '<code>' . $cheminGalerie . '</code>') . "</li>\n";
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
						}
					}
					elseif ($typeMime == 'application/zip' && !function_exists('gzopen'))
					{
						$messagesScript .= '<li class="erreur">' . T_("Les archives au format <code>ZIP</code> ne sont pas supportées.") . "</li>\n";
					}
					elseif ($typeMime == 'application/zip')
					{
						$resultatArchive = 0;
						$archive = new PclZip($cheminGaleries . '/' . $nomArchive);
						$resultatArchive = $archive->extract(PCLZIP_OPT_PATH, $cheminGaleries . '/' . $id . '/');
				
						if ($resultatArchive == 0)
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s: %2\$s"), '<code>' . $nomArchive . '</code>', $archive->errorInfo(true)) . "</li>\n";
						}
						else
						{
							foreach ($resultatArchive as $infoImage)
							{
								$nomFichier = superBasename($infoImage['filename']);
								$cheminFichier = $cheminGaleries . '/' . $id . '/' . $nomFichier;
								
								if ($infoImage['status'] == 'ok')
								{
									$typeMimeFichier = typeMime($cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
									
									if (!adminTypeMimePermis($typeMimeFichier, $adminFiltreTypesMime, $adminTypesMimePermis))
									{
										@unlink($cheminFichier);
										$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), '<code>' . $nomFichier . '</code>', '<code>' . $typeMimeFichier . '</code>') . "</li>\n";
									}
									else
									{
										$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . $nomFichier . '</code>', '<code>' . $cheminGaleries . '/' . $id . '</code>') . "</li>\n";
									}
								}
								elseif ($infoImage['status'] == 'newer_exist')
								{
									$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà, et est plus récent que celui de l'archive. Il n'y a donc pas eu extraction."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
								}
								else
								{
									$messagesScript .= '<li class="erreur">' . sprintf(T_("Attention: une erreur a eu lieu avec le fichier %1\$s. Vérifiez son état sur le serveur (s'il s'y trouve), et ajoutez-le à la main si nécessaire."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
								}
							}
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
								$nomFichier = $listeFichiers[$i]['filename'];
								$cheminFichier = $cheminGalerie . '/' . $nomFichier;
								
								if ($listeFichiers[$i]['filetype'] == 'directory')
								{
									if (file_exists($cheminFichier))
									{
										$messagesScript .= '<li class="erreur">' . sprintf(T_("Un dossier %1\$s existe déjà. Il n'a donc pas été créé."), '<code>' . $cheminGalerie . '/' . $nomFichier . '</code>') . "</li>\n";
									}
									else
									{
										$messagesScript .= adminMkdir($cheminFichier, octdec(755), TRUE);
									}
								}
								else
								{
									if (file_exists($cheminFichier))
									{
										$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà. Il n'y a donc pas eu extraction."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
									}
									elseif ($fic = @fopen($cheminFichier, 'w'))
									{
										$donnees = $fichierTar->extract($nomFichier);
										
										if (fwrite($fic, $donnees))
										{
											fclose($fic);
											$typeMimeFichier = typeMime($cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
											
											if (!adminTypeMimePermis($typeMimeFichier, $adminFiltreTypesMime, $adminTypesMimePermis))
											{
												@unlink($cheminFichier);
												$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), '<code>' . $nomFichier . '</code>', '<code>' . $typeMimeFichier . '</code>') . "</li>\n";
											}
											else
											{
												$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans le dossier %2\$s effectué."), '<code>' . $nomFichier . '</code>', '<code>' . $cheminGalerie . '</code>') . "</li>\n";
											}
										}
										else
										{
											$messagesScript .= '<li class="erreur">' . sprintf(T_("Attention: une erreur a eu lieu avec le fichier %1\$s. Vérifiez son état sur le serveur (s'il s'y trouve), et ajoutez-le à la main si nécessaire."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
										}
									}
									else
									{
										$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>";
									}
								}
							}
							
							unset($fichierTar);
							@unlink($cheminGalerie . '/' . $nomArchive);
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
						}
					}
					
					if (file_exists($cheminGaleries . '/' . $nomArchive))
					{
						@unlink($cheminGaleries . '/' . $nomArchive);
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</li>\n";
				}
			}
		}
		
		if (!empty($_FILES) && file_exists($_FILES['fichier']['tmp_name']))
		{
			@unlink($_FILES['fichier']['tmp_name']);
		}
		
		if (empty($messagesScript))
		{
			$messagesScript .= '<li class="erreur">' . T_("Aucune image n'a été extraite. Veuillez vérifier les instructions.") . "</li>\n";
		}
		
		if (isset($id))
		{
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		}
		else
		{
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "") . "</li>\n" . $messagesScript;
		}
		
		echo adminMessagesScript($messagesScript, T_("Ajout d'images"));
	}

	########################################################################
	##
	## Redimensionnement des images originales.
	##
	########################################################################

	if (isset($_POST['redimensionner']))
	{
		$messagesScript = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		$erreur = FALSE;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
			$erreur = TRUE;
		}
		else
		{
			$galerieQualiteJpg = securiseTexte($_POST['qualiteJpg']);
			
			if (isset($_POST['redimensionnerRenommer']) && in_array('analyserConfig', $_POST['redimensionnerRenommer']))
			{
				$analyserConfig = TRUE;
			}
			else
			{
				$analyserConfig = FALSE;
			}
			
			if (isset($_POST['redimensionnerRenommer']) && in_array('renommer', $_POST['redimensionnerRenommer']))
			{
				if (isset($_POST['redimensionnerRenommer']) && in_array('nePasRenommerMotifs', $_POST['redimensionnerRenommer']))
				{
					$renommerTout = FALSE;
				}
				else
				{
					$renommerTout = TRUE;
				}
				
				if ($fic = @opendir($cheminGalerie))
				{
					while ($fichier = @readdir($fic))
					{
						if (!is_dir($cheminGalerie . '/' . $fichier))
						{
							$infoFichier = pathinfo(superBasename($fichier));
							
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							
							$renommer = FALSE;
							$typeMime = typeMime($cheminGalerie . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							
							if (($renommerTout || (!preg_match('/-original\.' . $infoFichier['extension'] . '$/', $fichier) && !preg_match('/-vignette\.' . $infoFichier['extension'] . '$/', $fichier))) && adminImageValide($typeMime))
							{
								$renommer = TRUE;
							}
							
							if ($renommer && $analyserConfig)
							{
								$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, superBasename($cheminGalerie)));
								
								if (adminImageEstDeclaree($fichier, $tableauGalerie))
								{
									$renommer = FALSE;
								}
							}
							
							if ($renommer)
							{
								$nouveauNom = nomSuffixe($fichier, '-original');
								
								if (!file_exists($cheminGalerie . '/' . $nouveauNom))
								{
									$messagesScript .= adminRename($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom);
								}
							}
						}
					}
			
					closedir($fic);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
					$erreur = TRUE;
				}
			}
		
			// A: les images à traiter ont la forme `nom-original.extension`.
		
			if (!$erreur)
			{
				if ($fic2 = @opendir($cheminGalerie))
				{
					while ($fichier = @readdir($fic2))
					{
						$aTraiter = TRUE;
						
						if ($analyserConfig)
						{
							$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, superBasename($cheminGalerie)));
							
							if (adminImageEstDeclaree($fichier, $tableauGalerie))
							{
								$aTraiter = FALSE;
							}
						}
						
						if ($aTraiter)
						{
							$infoFichier = pathinfo(superBasename($fichier));
							
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							
							$nouveauNom = preg_replace('/-original\..{3,4}$/', '.', $fichier) . $infoFichier['extension'];
			
							if (!is_dir($cheminGalerie . '/' . $fichier) && preg_match('/-original\.' . $infoFichier['extension'] . '$/', $fichier) && !file_exists($cheminGalerie . '/' . $nouveauNom))
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
								
								$typeMime = typeMime($cheminGalerie . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
								$messagesScript .= nouvelleImage($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom, $typeMime, $imageIntermediaireDimensionsVoulues, FALSE, $galerieQualiteJpg, $nettete);
							}
						}
					}
				
					closedir($fic2);
				
					if (empty($messagesScript))
					{
						$messagesScript .= '<li>' . T_("Aucune modification.") . "</li>\n";
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
				}
			}
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Redimensionnement des images"));
	}

	########################################################################
	##
	## Suppression d'images.
	##
	########################################################################

	if (isset($_POST['supprimerImages']))
	{
		$messagesScript = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		elseif (empty($_POST['listeAsupprimer']))
		{
			$messagesScript .= '<li class="erreur">' . T_("La manière de générer la liste des fichiers à supprimer n'a pas été spécifiée.") . "</li>\n";
		}
		elseif ($_POST['listeAsupprimer'] != 'config' && $_POST['listeAsupprimer'] != 'motifs' && $_POST['listeAsupprimer'] != 'sansMotif')
		{
			$messagesScript .= '<li class="erreur">' . T_("La manière spécifiée de générer la liste des fichiers à supprimer n'est pas valide.") . "</li>\n";
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
				$configAsupprimer = FALSE;
				
				while ($fichier = @readdir($fic))
				{
					if (!is_dir($cheminGalerie . '/' . $fichier))
					{
						$typeMime = typeMime($cheminGalerie . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
						$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime);
						
						if (isset($_POST['supprimer']) && ((in_array('vignettes', $_POST['supprimer']) && $versionImage == 'vignette') || (in_array('intermediaires', $_POST['supprimer']) && $versionImage == 'intermediaire') || (in_array('original', $_POST['supprimer']) && $versionImage == 'original')))
						{
							$messagesScript .= adminUnlink($cheminGalerie . '/' . $fichier);
						}
						elseif (isset($_POST['supprimer']) && in_array('config', $_POST['supprimer']) && ($fichier == 'config.ini.txt' || $fichier == 'config.ini'))
						{
							$configAsupprimer = $cheminGalerie . '/' . $fichier;
						}
					}
				}
				
				closedir($fic);
				
				if ($configAsupprimer !== FALSE)
				{
					$messagesScript .= adminUnlink($configAsupprimer);
				}
				
				if (isset($_POST['supprimer']) && in_array('vignettesAvecTatouage', $_POST['supprimer']))
				{
					$cheminTatouage = $racine . '/site/fichiers/galeries/' . $id . '/tatouage';
				
					if (!file_exists($cheminTatouage))
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Le dossier des vignettes avec tatouage %1\$s n'existe pas."), "<code>$cheminTatouage</code>") . "</li>\n";
					}
					elseif ($fic = @opendir($cheminTatouage))
					{
						while ($fichier = @readdir($fic))
						{
							if (!is_dir($cheminTatouage . '/' . $fichier))
							{
								$infoFichier = pathinfo(superBasename($fichier));
								
								if (!isset($infoFichier['extension']))
								{
									$infoFichier['extension'] = '';
								}
							
								$typeMime = typeMime($cheminTatouage . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
							
								if (preg_match('/-vignette-(precedent|suivant)\.' . $infoFichier['extension'] . '$/', $fichier) && adminImageValide($typeMime))
								{
									$messagesScript .= adminUnlink($cheminTatouage . '/' . $fichier);
								}
							}
						}
					
						closedir($fic);
					
						if (adminDossierEstVide($cheminTatouage))
						{
							$messagesScript .= adminRmdir($cheminTatouage);
						}
						else
						{
							$messagesScript .= '<li>' . sprintf(T_("Le dossier %1\$s n'est pas vide, il ne sera donc pas supprimé."), "<code>$cheminTatouage</code>") . "</li>\n";
						}
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminTatouage</code>") . "</li>\n";
					}
				}
				
				if (isset($_POST['supprimer']) && in_array('dossier', $_POST['supprimer']))
				{
					if (adminDossierEstVide($cheminGalerie))
					{
						$messagesScript .= adminRmdir($cheminGalerie);
					}
					else
					{
						$messagesScript .= '<li>' . sprintf(T_("Le dossier %1\$s n'est pas vide, il ne sera donc pas supprimé."), "<code>$cheminGalerie</code>") . "</li>\n";
					}
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
			}
		}
		
		if (empty($messagesScript))
		{
			$messagesScript .= '<li>' . T_("Aucune image à traiter.") . "</li>\n";
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Suppression d'images"));
	}
	
	########################################################################
	##
	## Renommage d'une galerie.
	##
	########################################################################

	if (isset($_POST['renommer']))
	{
		$messagesScript = '';
		$nouvelId = securiseTexte($_POST['idNouveauNomGalerie']);
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		$nouveauCheminGalerie = $racine . '/site/fichiers/galeries/' . $nouvelId;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		elseif (file_exists($nouveauCheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s existe déjà."), "<code>$nouvelId</code>") . "</li>\n";
		}
		else
		{
			$messagesScript .= adminRename($cheminGalerie, $nouveauCheminGalerie);
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Renommage d'une galerie"));
	}
	
	########################################################################
	##
	## Sauvegarde d'une galerie.
	##
	########################################################################

	if ($adminPorteDocumentsDroits['telecharger'] && isset($_POST['sauvegarder']))
	{
		$messagesScript = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			$messagesScript .= '<li><a href="telecharger.admin.php?fichier=' . $cheminGalerie . '&amp;action=date">' . sprintf(T_("Cliquer sur ce lien pour obtenir une copie de sauvegarde de la galerie %1\$s."), "<code>$id</code>") . "</a></li>\n";
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Sauvegarde d'une galerie"));
	}
	
	########################################################################
	##
	## Création d'une page web de galerie.
	##
	########################################################################

	if (isset($_POST['creerPage']))
	{
		$messagesScript = '';
		$page = superBasename(securiseTexte($_POST['page']));
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
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		elseif (!adminEmplacementPermis($cheminPage, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la création d'une page web de galerie (%1\$s) n'est pas gérable par le porte-documents."), "<code>$cheminPage</code>") . "</li>\n";
		}
		else
		{
			$cheminConfigGalerie = cheminConfigGalerie($racine, $id);
		
			if (!$cheminConfigGalerie)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'a pas de fichier de configuration."), "<code>$id</code>") . "</li>\n";
			}
			else
			{
				if (!file_exists($cheminPage))
				{
					$messagesScript .= adminMkdir($cheminPage, octdec(0755), TRUE);
				}
				
				if (file_exists($cheminPage))
				{
					if (file_exists($cheminPage . '/' . $page))
					{
						if ($adminPorteDocumentsDroits['editer'])
						{
							$messagesScript .= '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href=\"%2\$s\">éditer le fichier</a> ou <a href=\"%3\$s\">visiter la page</a>."), '<code>' . $cheminPage . '/' . $page . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '&amp;dossierCourant=' . rawurlencode(dirname($cheminPage . '/' . $page)) . '#messages', $urlRacine . '/' . superRawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
						}
						else
						{
							$messagesScript .= '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href=\"%2\$s\">visiter la page</a>."), '<code>' . $cheminPage . '/' . $page . '</code>', $urlRacine . '/' . superRawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
						}
					}
					else
					{
						if ($fic = @fopen($cheminPage . '/' . $page, 'a'))
						{
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$contenu .= '$idGalerie = "' . $id . '";' . "\n";
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
							fclose($fic);
							
							if ($adminPorteDocumentsDroits['editer'])
							{
								$messagesScript .= '<li>' . sprintf(T_("Le modèle de page a été créé. Vous pouvez <a href=\"%1\$s\">éditer le fichier</a> ou <a href=\"%2\$s\">visiter la page</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '&amp;dossierCourant=' . rawurlencode(dirname($cheminPage . '/' . $page)) . '#messages', $urlRacine . '/' . superRawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
							}
							else
							{
								$messagesScript .= '<li>' . sprintf(T_("Le modèle de page a été créé. Vous pouvez <a href=\"%1\$s\">visiter la page</a>."), $urlRacine . '/' . superRawurlencode(substr($cheminPage . '/' . $page, 3))) . "</li>\n";
							}
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>\n";
						}
					}
				}
			}
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Création d'une page web de galerie"));
	}

	########################################################################
	##
	## Affichage d'un modèle de fichier de configuration.
	##
	########################################################################

	if (isset($_POST['modeleConf']))
	{
		$messagesScript = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			if (isset($_POST['config']) && in_array('exclureMotifsCommeIntermediaires', $_POST['config']))
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
				
				while ($fichier = @readdir($fic))
				{
					if (!is_dir($cheminGalerie . '/' . $fichier))
					{
						$typeMime = typeMime($cheminGalerie . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
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
						foreach ($_POST['info'] as $parametre)
						{
							$listeFichiers .= securiseTexte($parametre) . "=";
							
							if ($parametre == 'auteurAjout' && $galerieFluxRssAuteurEstAuteurParDefaut)
							{
								$listeFichiers .= $auteurParDefaut;
							}
							elseif ($parametre == 'dateAjout')
							{
								$listeFichiers .= date('Y-m-d H:i');
							}
							
							elseif ($parametre == 'exclure')
							{
								$listeFichiers .= 'oui';
							}
							
							$listeFichiers .= "\n";
						}
					}
					
					$listeFichiers .= "\n";
				}
				
				if (!empty($listeFichiers))
				{
					$messagesScript .= '<li><pre id="listeFichiers">' . $listeFichiers . "</pre></li>\n";
					$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), "<code>$cheminGalerie</code>") . "</li>\n";
			}
		}
		
		if (empty($messagesScript))
		{
			$messagesScript .= '<li>' . T_("Aucune image dans la galerie.") . "</li>\n";
		}
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		echo adminMessagesScript($messagesScript, T_("Modèle de fichier de configuration"));
	}

	########################################################################
	##
	## Création ou mise à jour d'un fichier de configuration.
	##
	########################################################################

	$sousBoiteFichierConfigDebut = FALSE;

	if (isset($_POST['config']) && in_array('maj', $_POST['config']))
	{
		$messagesScript = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if (!file_exists($cheminGalerie))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), "<code>$id</code>") . "</li>\n";
		}
		else
		{
			if (isset($_POST['config']) && in_array('exclureMotifsCommeIntermediaires', $_POST['config']))
			{
				$exclureMotifsCommeIntermediaires = TRUE;
			}
			else
			{
				$exclureMotifsCommeIntermediaires = FALSE;
			}
			
			$cheminConfigGalerie = cheminConfigGalerie($racine, $id);
		
			if ($cheminConfigGalerie)
			{
				$configExisteAuDepart = TRUE;
			}
			else
			{
				$configExisteAuDepart = FALSE;
				$cheminConfigGalerie = cheminConfigGalerie($racine, $id, TRUE);
			}
			
			$parametresNouvellesImages = array ();
			
			if (isset($_POST['ajouter']) && !empty($_POST['parametres']))
			{
				foreach ($_POST['parametres'] as $parametre => $valeur)
				{
					if (!empty($valeur))
					{
						$parametre = securiseTexte(trim($parametre));
						$valeur = securiseTexte(trim($valeur));
						$parametresNouvellesImages[$parametre] = $valeur;
					}
				}
			}
			
			if (adminMajConfigGalerie($racine, $id, '', TRUE, $exclureMotifsCommeIntermediaires, FALSE, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance, $parametresNouvellesImages))
			{
				if ($configExisteAuDepart)
				{
					$messagesScript .= '<li>' . sprintf(T_("Mise à jour du fichier de configuration %1\$s effectuée."), '<code>' . $cheminConfigGalerie . '</code>') . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Création du fichier de configuration %1\$s effectuée."), '<code>' . $cheminConfigGalerie . '</code>') . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de la création ou de la mise à jour du fichier de configuration %1\$s. Veuillez vérifier manuellement son contenu."), "<code>$cheminConfigGalerie</code>") . "</li>\n";
			}
		}
		
		$sousBoiteFichierConfigDebut = TRUE;
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
		
		$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), "<code>$id</code>") . "</li>\n" . $messagesScript;
		
		echo '<h4>' . T_("Actions effectuées") . "</h4>\n" ;
		
		echo "<ul>\n";
		echo $messagesScript;
		echo "</ul>\n";
	}
	
	if ((isset($_POST['modeleConf']) || (isset($_POST['config']) && in_array('maj', $_POST['config']))) && cheminConfigGalerie($racine, $id))
	{
		if (!$sousBoiteFichierConfigDebut)
		{
			$sousBoiteFichierConfigDebut = TRUE;
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
		}
		
		$cheminConfigGalerie = cheminConfigGalerie($racine, $id);
		$id = rawurlencode($id);
		
		echo '<h4>' . T_("Information") . "</h4>\n" ;
		
		echo "<ul>\n";
		
		if ($adminPorteDocumentsDroits['editer'])
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $id . '/' . superBasename($cheminConfigGalerie) . '&amp;dossierCourant=../site/fichiers/galeries/' . $id . '#messages">' . T_("Modifier le fichier.") . "</a></li>\n";
		}
		else
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . "</li>\n";
		}
		
		echo "</ul>\n";
	}
	
	if ($sousBoiteFichierConfigDebut)
	{
		echo "</div><!-- /.sousBoite -->\n";
	}
	?>
</div><!-- /#boiteMessages -->

<?php
########################################################################
##
## Formulaires.
##
########################################################################
?>

<div class="boite">
	<h2 id="lister"><?php echo T_("Lister les galeries existantes"); ?></h2>
	
	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<?php if ($adminPorteDocumentsDroits['editer']): ?>
				<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes. Si la galerie a un fichier de configuration, un lien vous permettra de modifier ce dernier dans le porte-documents."); ?></p>
			<?php else: ?>
				<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes, qu'elles aient ou non un fichier de configuration."); ?></p>
			<?php endif; ?>

			<p><input type="submit" name="lister" value="<?php echo T_('Lister les galeries'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="ajouter"><?php echo T_("Ajouter des images"); ?></h2>
	
	<?php if (function_exists('gzopen')): ?>
		<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (<code>.tar</code>) ou ZIP (<code>.zip</code>). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>
	<?php else: ?>
		<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (<code>.tar</code>). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>
	<?php endif; ?>
	
	<p><?php echo T_("Vous pouvez également ajouter une seule image en choisissant un fichier image au lieu d'une archive."); ?></p>

	<p><?php printf(T_("<strong>Taille maximale d'un transfert de fichier:</strong> %1\$s Mio (%2\$s octets)."), octetsVersMio($adminTailleMaxFichiers), $adminTailleMaxFichiers); ?></p>

	<form action="<?php echo $adminAction; ?>#messages" method="post" enctype="multipart/form-data">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="ajouterSelectId"><?php echo T_("Identifiant de la galerie (il est possible de créer une nouvelle galerie):"); ?></label><br />
				<select id="ajouterSelectId" name="id">
					<option value="nouvelleGalerie"><?php echo T_("Nouvelle galerie:"); ?></option>
					<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
					
					<?php if (!empty($listeGaleries)): ?>
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select> <input type="text" name="idNouvelleGalerie" /></p>

				<p><label for="ajouterInputFichier"><?php echo T_("Fichier:"); ?></label><br />
				<input id="ajouterInputFichier" type="file" name="fichier" size="25"/></p>
			</fieldset>

			<fieldset class="fichierConfigAdminGaleries">
				<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
				<div class="bDcorps afficher">
					<ul>
						<li><input id="ajouterInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="ajouterInputConfig"><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
						<ul>
							<li><input id="ajouterInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="ajouterInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
						</ul></li>
					</ul>
				
					<fieldset id="ajoutParametresAdminGaleries">
						<legend class="bDtitre"><?php echo T_("Paramètres"); ?></legend>
					
						<?php $parametres = adminParametresImage(); ?>
					
						<div class="bDcorps afficher">
							<p><?php echo T_("Ajouter les paramètres suivants pour chaque image (un paramètre vide ne sera pas ajouté):"); ?></p>
					
							<ul>
								<?php foreach ($parametres as $parametre): ?>
									<li><input id="ajouterInputParametres-<?php echo $parametre; ?>" class="long" type="text" name="parametres[<?php echo $parametre; ?>]" value="" /> <label for="ajouterInputParametres-<?php echo $parametre; ?>"><?php echo "<code>$parametre</code>"; ?></label></li>
								<?php endforeach; ?>
							</ul>
						</div><!-- /.bDcorps -->
					</fieldset>
				</div><!-- /.bDcorps -->
			</fieldset>
			
			<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter des images'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="redimensionner"><?php echo T_("Créer des images de taille intermédiaire à partir des images originales"); ?></h2>

	<p><?php echo T_("Vous pouvez générer automatiquement une copie réduite (qui sera utilisée comme étant la version intermédiaire dans la galerie) de chaque image originale. Aucune image au format original ne sera modifiée."); ?></p>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="redimensionnerSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="redimensionnerSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			
				<p><?php printf(T_("Taille maximale de la version intermédiaire (<label for=\"%1\$s\">largeur</label> × <label for=\"%2\$s\">hauteur</label>):"), "redimensionnerInputLargeur", "redimensionnerInputHauteur"); ?><br />
				<?php echo T_("La plus grande taille possible contenable dans les dimensions données sera utilisée, sans toutefois dépasser la taille originale. Si une seule dimension est précisée, l'autre sera calculée à partir de la dimension donnée ainsi que des dimensions de l'image source. Les proportions de l'image sont conservées. Au moins une dimension doit être donnée."); ?><br />
				<input id="redimensionnerInputLargeur" type="text" name="largeur" size="4" value="500" /> <?php echo T_("px de largeur"); ?> <?php echo T_("×"); ?> <input id="redimensionnerInputHauteur" type="text" name="hauteur" size="4" value="500" /> <?php echo T_("px de hauteur"); ?></p>
				
				<p><label for="redimensionnerInputQualiteJpg"><?php echo T_("S'il y a lieu, qualité des images JPG générées (0-100):"); ?></label><br />
				<input id="redimensionnerInputQualiteJpg" type="text" name="qualiteJpg" value="90" size="2" /></p>

				<p><input id="redimensionnerInputNettete" type="checkbox" name="actions" value="nettete" /> <label for="redimensionnerInputNettete"><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)."); ?></label></p>
				
				<p><?php echo T_("La liste des images originales redimensionnables est consitituée des images dont le nom satisfait le motif <code>nom-original.extension</code>. Voici des options relatives à cette liste:"); ?></p>
				<ul>
					<li><input id="redimensionnerInputRenommer" type="checkbox" name="redimensionnerRenommer[]" value="renommer" checked="checked" /> <label for="redimensionnerInputRenommer"><?php echo T_("Renommer préalablement les images de la galerie en <code>nom-original.extension</code>."); ?></label></li>
					
					<li><input id="redimensionnerInputNePasRenommerMotifs" type="checkbox" name="redimensionnerRenommer[]" value="nePasRenommerMotifs" checked="checked" /> <label for="redimensionnerInputNePasRenommerMotifs"><?php echo T_("S'il y a lieu, ignorer lors du renommage les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>."); ?></label></li>
					
					<li><input id="redimensionnerInputAnalyserConfig" type="checkbox" name="redimensionnerRenommer[]" value="analyserConfig" /> <label for="redimensionnerInputAnalyserConfig"><?php echo T_("Ignorer lors du renommage (s'il y a lieu) ainsi que lors du redimensionnement les images déclarées dans le fichier de configuration (s'il existe). Toute image déjà présente comme valeur d'un des paramètres <code>intermediaireNom</code>, <code>vignetteNom</code> ou <code>originalNom</code> du fichier de configuration est nécessairement une version intermédiaire ou a nécessairement une version intermédiaire associée."); ?></label></li>
				</ul>
				
				<p><?php echo T_("Dans tous les cas, il n'y a pas de création d'image intermédiaire si les fichiers <code>nom-original.extension</code> et <code>nom.extension</code> existent déjà tous les deux."); ?></p>
			</fieldset>

			<fieldset class="fichierConfigAdminGaleries">
				<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
				<ul class="bDcorps afficher">
					<li><input id="redimensionnerInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="redimensionnerInputConfig"><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
					<ul>
						<li><input id="redimensionnerInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="redimensionnerInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
					</ul></li>
				</ul>
			</fieldset>

			<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

			<p><input type="submit" name="redimensionner" value="<?php echo T_('redimensionner les images originales'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="supprimer"><?php echo T_("Supprimer des images"); ?></h2>
	
	<div class="aideAdminGaleries">
		<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
	
		<div class="bDcorps afficher">
			<p><?php echo T_("La liste des images potentiellement supprimables peut être générée de trois manières différentes:"); ?></p>
		
			<ul>
				<li><?php echo T_("seulement par analyse du fichier de configuration, s'il existe. Les images intermédiaires sont les images déclarées par le paramètre <code>intermediaireNom</code>. Les vignettes sont les images déclarées par le paramètre <code>vignetteNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-vignette.extension</code> (à moins que ces derniers soient déclarés dans le fichier de configuration comme étant une version différente) et pour lesquels une image intermédiaire sans le motif <code>-vignette</code> existe. Les fichiers au format original sont ceux déclarés par le paramètre <code>originalNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-original.extension</code> (à moins d'une délcaration différente dans le fichier de configuration) et pour lesquels une image intermédiaire sans le motif <code>-original</code> existe;"); ?></li>
	
				<li><?php echo T_("par reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Les vignettes sont les images dont le nom satisfait le motif <code>nom-vignette.extension</code>. Les fichiers au format original sont ceux dont le nom satisfait le motif <code>nom-original.extension</code>. Les images intermédiaires sont les images dont le nom ne satisfait aucun motif (<code>nom-vignette.extension</code> ou <code>nom-original.extension</code>)."); ?></li>
	
				<li><?php echo T_("sans reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Toutes les images sont considérées comme étant des images intermédiaires."); ?></li>
			</ul>

			<p><?php echo T_("Tout d'abord, vous pouvez supprimer les vignettes d'une galerie pour forcer leur regénération automatique."); ?></p>

			<p><?php echo T_("Aussi, si la navigation entre les oeuvres d'une galerie est réalisée avec des vignettes et si <code>\$galerieNavigationTatouerVignettes</code> vaut <code>TRUE</code>, de nouvelles vignettes de navigation vers les oeuvres précédente et suivante sont générées, et contiennent une petite image (par défaut une flèche) au centre. Vous pouvez supprimer ces vignettes de navigation avec tatouage."); ?></p>

			<p><?php echo T_("Vous pouvez également supprimer les images de taille intermédiaires ou au format original."); ?></p>

			<p><?php echo T_("Il est aussi possible de supprimer le fichier de configuration de la galerie ainsi que le dossier de la galerie si ce dernier est vide."); ?></p>
		</div><!-- .bDcorps -->
	</div><!-- .aideAdminGaleries -->
	
	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="supprimerSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="supprimerSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
				
				<p><?php echo T_("Génération de la liste des images potentiellement supprimables:"); ?></p>
				
				<ul>
					<li><input id="supprimerInputListeAsupprimerConfig" type="radio" name="listeAsupprimer" value="config" checked="checked" /> <label for="supprimerInputListeAsupprimerConfig"><?php echo T_("seulement par analyse du fichier de configuration;"); ?></label></li>
					
					<li><input id="supprimerInputListeAsupprimerMotifs" type="radio" name="listeAsupprimer" value="motifs" /> <label for="supprimerInputListeAsupprimerMotifs"><?php echo T_("par reconnaissance d'un motif dans le nom des fichiers;"); ?></label></li>
					
					<li><input id="supprimerInputListeAsupprimerSansMotif" type="radio" name="listeAsupprimer" value="sansMotif" /> <label for="supprimerInputListeAsupprimerSansMotif"><?php echo T_("sans reconnaissance de motif dans le nom des fichiers."); ?></label></li>
				</ul>
				
				<ul>
					<li><input id="supprimerInputVignettes" type="checkbox" name="supprimer[]" value="vignettes" /> <label for="supprimerInputVignettes"><?php echo T_("Supprimer les vignettes."); ?></label></li>
					
					<li><input id="supprimerInputVignettesAvecTatouage" type="checkbox" name="supprimer[]" value="vignettesAvecTatouage" /> <label for="supprimerInputVignettesAvecTatouage"><?php echo T_("Supprimer les vignettes de navigation avec tatouage."); ?></label></li>
					
					<li><input id="supprimerInputIntermediaires" type="checkbox" name="supprimer[]" value="intermediaires" /> <label for="supprimerInputIntermediaires"><?php echo T_("Supprimer les images intermédiaires."); ?></label></li>
					
					<li><input id="supprimerInputOriginal" type="checkbox" name="supprimer[]" value="original" /> <label for="supprimerInputOriginal"><?php echo T_("Supprimer les images originales."); ?></label></li>
					
					<li><input id="supprimerInputConfig" type="checkbox" name="supprimer[]" value="config" /> <label for="supprimerInputConfig"><?php echo T_("Supprimer le fichier de configuration."); ?></label></li>
					
					<li><input id="supprimerInputDossier" type="checkbox" name="supprimer[]" value="dossier" /> <label for="supprimerInputDossier"><?php echo T_("Supprimer le dossier de la galerie s'il est vide."); ?></label></li>
				</ul>
			</fieldset>
			
			<fieldset class="fichierConfigAdminGaleries">
				<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
				<ul class="bDcorps afficher">
					<li><input id="supprimerInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="supprimerInputConfig"><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie."); ?></label>
					<ul>
						<li><input id="supprimerInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="supprimerInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
					</ul></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="supprimerImages" value="<?php echo T_('Supprimer les images'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="renommer"><?php echo T_("Renommer une galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez renommer une galerie. S'il s'agit d'une galerie déjà utilisée sur votre site, ne pas oublier de modifier la valeur de la variable <code>\$idGalerie</code> dans la page web de votre galerie."); ?></p>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><?php printf(T_("<label for=\"\">Identifiant actuel de la galerie</label> et son <label for=\"\">nouvel identifiant</label>:"), "renommerSelectId", "renommerInputIdNouveauNomGalerie"); ?><br />
				<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="renommerSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select> <input id="renommerInputIdNouveauNomGalerie" type="text" name="idNouveauNomGalerie" />
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			</fieldset>
			
			<p><input type="submit" name="renommer" value="<?php echo T_('Renommer la galerie'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php if ($adminPorteDocumentsDroits['telecharger']): ?>
	<!-- .boite -->

	<div class="boite">
		<h2 id="sauvegarder"><?php echo T_("Sauvegarder une galerie"); ?></h2>

		<p><?php echo T_("Vous pouvez sauvegarder une galerie en choisissant son identifiant ci-dessous."); ?></p>

		<form action="<?php echo $adminAction; ?>#messages" method="post">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
				
					<p><label for="sauvegarderSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
					<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
					
					<?php if (!empty($listeGaleries)): ?>
						<select id="sauvegarderSelectId" name="id">
							<?php foreach ($listeGaleries as $listeGalerie): ?>
								<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
					<?php endif; ?>
					</p>
				</fieldset>
			
				<p><input type="submit" name="sauvegarder" value="<?php echo T_('Sauvegarder la galerie'); ?>" /></p>
			</div>
		</form>
	</div><!-- /.boite -->
<?php endif; ?>

<!-- .boite -->

<div class="boite">
	<h2 id="pageWeb"><?php echo T_("Créer une page web de galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez ajouter une page sur votre site pour présenter une galerie."); ?></p>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="pageWebSelectId"><?php echo T_("Identifiant de la galerie (ayant un fichier de configuration):"); ?></label><br />
				<?php $listeGaleries = adminListeGaleries($racine, TRUE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="pageWebSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer au moins une galerie ayant un fichier de configuration."); ?></strong>
				<?php endif; ?>
				</p>

				<p><label for="pageWebInputPage"><?php echo T_("Emplacement de la page web:"); ?></label><br />
				<?php echo $urlRacine . '/'; ?><input id="pageWebInputPage" type="text" name="page" /></p>
			</fieldset>
			
			<p><input type="submit" name="creerPage" value="<?php echo T_('Créer une page web'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="config"><?php echo T_("Créer ou mettre à jour un fichier de configuration"); ?></h2>

	<p><?php echo T_("Crée ou met à jour le fichier de configuration de cette galerie."); ?></p>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="configSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="configSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>
			</fieldset>
			
			<fieldset class="fichierConfigAdminGaleries">
				<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
				<p class="bDcorps afficher"><input for="configInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="configInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></p>
			</fieldset>
			
			<p><input type="submit" name="majConf" value="<?php echo T_('Créer ou mettre à jour'); ?>" /></p>
			
			<input type="hidden" name="config" value="maj" />
		</div>
	</form>
</div><!-- /.boite -->

<!-- .boite -->

<div class="boite">
	<h2 id="modele"><?php echo T_("Afficher un modèle de fichier de configuration"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p><label for="modeleSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
				<?php $listeGaleries = adminListeGaleries($racine, FALSE); ?>
				
				<?php if (!empty($listeGaleries)): ?>
					<select id="modeleSelectId" name="id">
						<?php foreach ($listeGaleries as $listeGalerie): ?>
							<option value="<?php echo $listeGalerie; ?>"><?php echo $listeGalerie; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
				<?php endif; ?>
				</p>

				<p><label for="modeleSelectInfo"><?php echo T_("Pour chaque image intermédiaire, ajouter des paramètres:"); ?></label><br />
				<select id="modeleSelectInfo" name="info[]" multiple="multiple" size="4">
					<option value="aucun" selected="selected"><?php echo T_("Aucun"); ?></option>
					
					<?php foreach (adminParametresImage() as $parametre): ?>
						<option value="<?php echo $parametre; ?>"><?php echo $parametre; ?></option>
					<?php endforeach; ?>
				</select></p>
			</fieldset>
			
			<fieldset class="fichierConfigAdminGaleries">
				<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
				<p class="bDcorps"><input id="modeleInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="modeleInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>."); ?></label></p>
			</fieldset>
			
			<p><input type="submit" name="modeleConf" value="<?php echo T_('Afficher un fichier de configuration'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
