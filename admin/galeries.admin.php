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
		$id = securiseTexte($_POST['id']);
	}

	########################################################################
	##
	## Lister les galeries existantes
	##
	########################################################################

	if (isset($_POST['lister']))
	{
		if ($fic = opendir($racine . '/site/fichiers/galeries'))
		{
			$trad = '';
			$listeFichiers = '';
			$i = 0;
			while($fichier = @readdir($fic))
			{
				if(is_dir($racine . '/site/fichiers/galeries/' . $fichier) && $fichier != '.' && $fichier != '..' && file_exists($racine . '/site/fichiers/galeries/' . $fichier . '/config.pc'))
				{
					$i++;
					$fichier = sansEchappement($fichier);
					$idLien = rawurlencode($fichier);
					$listeFichiers .= '<li>' . sprintf(T_('Galerie %1$s:'), $i) . '<ul><li><em>' . T_("identifiant:") . '</em> ' . $fichier . '</li><li><em>' . T_("dossier:") . '</em> <a href="porte-documents.admin.php?action=parcourir&amp;valeur=../site/fichiers/galeries/' . $idLien . '#fichiersEtDossiers">' . $fichier . '</a></li><li><em>' . T_("Fichier de configuration:") . '</em> <a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $idLien . '/config.pc#messagesPorteDocuments">config.pc</a></li></ul></li>' . "\n";
				}
			}
	
			closedir($fic);
		}
		else
		{
			$trad = "<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), "<code>$racine/site/fichiers/galeries</code>") . "</p>\n";
		}
	
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Liste des galeries") . '</h3>' . "\n";
	
		if (!empty($trad))
		{
			echo $trad;
		}
		else
		{
			echo "<ul>\n";
			if (!empty($listeFichiers))
			{
				echo $listeFichiers;
			}
			else
			{
				echo '<li>' . T_("Aucune galerie") . "</li>\n";
			}
			echo "</ul>\n";
		}
	
		echo "</div><!-- /class=boite2 -->\n";
	}

	########################################################################
	##
	## Ajouter des images
	##
	########################################################################

	if (isset($_POST['ajouter']))
	{
		$cheminGaleries = $racine . '/site/fichiers/galeries';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
	
		$listeModifs = array ();
	
		if (!file_exists($cheminGalerie))
		{
			if (mkdir($cheminGalerie, 0755, TRUE))
			{
				$listeModifs[] = sprintf(T_('Création du dossier %1$s.'), '<code>' . $cheminGalerie . '</code>');
			}
			else
			{
				unlink($_FILES['fichier']['tmp_name']);
				$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Impossible de créer le dossier %1$s.'), '<code>' . $cheminGalerie . '</code>') . "</span>\n";
				$listeModifs[] = sprintf(T_('Fichier %1\$s supprimé.'), '<code>' . securiseTexte($_FILES['fichier']['tmp_name']) . '</code>');
			}
		}
	
		if (file_exists($cheminGalerie))
		{
			if (isset($_FILES['fichier']))
			{
				$nomArchive = basename(securiseTexte($_FILES['fichier']['name']));
			
				if (preg_match('/\.zip$/i', $nomArchive))
				{
					$fichierEstImage = FALSE;
					$cheminDeplacement = $cheminGaleries;
				}
				elseif (preg_match('/\.tar$/i', $nomArchive))
				{
					$fichierEstImage = FALSE;
					$cheminDeplacement = $cheminGalerie;
				}
				else
				{
					$fichierEstImage = TRUE;
					$cheminDeplacement = $cheminGalerie;
				}
			
				if (file_exists($cheminDeplacement . '/' . $nomArchive))
				{
					unlink($_FILES['fichier']['tmp_name']);
					$listeModifs[] = '<span class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), '<code>' . $nomArchive . '</code>', '<code>' . $cheminDeplacement . '/</code>') . "</span>\n";
					$listeModifs[] = T_('Le fichier que vous avez téléversé sur le serveur a été supprimé.');
				}
				else
				{
					if (move_uploaded_file($_FILES['fichier']['tmp_name'], $cheminDeplacement . '/' . $nomArchive))
					{
						if ($fichierEstImage)
						{
							$listeModifs[] = sprintf(T_('Ajout de %1$s dans le dossier %2$s.'), '<code>' . $nomArchive . '</code>', '<code>' . $cheminGaleries . '/' . $id . '/</code>');
						}
						else
						{
							if (preg_match('/\.zip$/i', $nomArchive))
							{
								$resultatArchive = 0;
								$archive = new PclZip($cheminGaleries . '/' . $nomArchive);
								$resultatArchive = $archive->extract(PCLZIP_OPT_PATH, $cheminGaleries . '/' . $id . '/');
							
								if ($resultatArchive == 0)
								{
									unlink($cheminGaleries . '/' . $nomArchive);
									$listeModifs[] = sprintf(T_('Fichier %1\$s supprimé.'), '<code>' . $cheminGaleries . '/' . $nomArchive . '</code>');
									$listeModifs[] = '<span class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s: "), '<code>' . $nomArchive . '</code>') . $archive->errorInfo(true) . "</span>\n";
								}
								else
								{
									foreach ($resultatArchive as $infoImage)
									{
										if ($infoImage['status'] == 'ok')
										{
											$listeModifs[] = sprintf(T_('Ajout de %1$s dans le dossier %2$s.'), '<code>' . substr($infoImage['filename'], strlen($cheminGaleries . '/' . $id) + 1) . '</code>', '<code>' . $cheminGaleries . '/' . $id . '/</code>');
										}
										elseif ($infoImage['status'] == 'newer_exist')
										{
											$listeModifs[] = '<span class="erreur">' . sprintf(T_('Un fichier %1$s existe déjà, et est plus récent que celui de l\'archive. Il n\'y a donc pas eu extraction.'), '<code>' . $infoImage['filename'] . '</code>') . "</span>\n";
										}
										else
										{
											$listeModifs[] = '<span class="erreur">' . sprintf(T_('Attention: une erreur a eu lieu avec le fichier %1$s. Vérifiez son état sur le serveur (s\'il s\'y trouve), et ajoutez-le à la main si nécessaire.'), '<code>' . $infoImage['filename'] . '</code>') . "</span>\n";
										}
									}
									unlink($cheminGaleries . '/' . $nomArchive);
									$listeModifs[] = sprintf(T_('Fichier %1\$s supprimé.'), '<code>' . $cheminGaleries . '/' . $nomArchive . '</code>');
								}
							}
							elseif (preg_match('/\.tar$/i', $nomArchive))
							{
								$fichierTar = new untar($cheminGalerie . '/' . $nomArchive);
								$listeFichiers = $fichierTar->getfilelist();
								for ($i = 0; $i < count($listeFichiers); $i++)
								{
									if ($listeFichiers[$i]['filetype'] == 'directory')
									{
										if (file_exists($cheminGalerie . '/' . $listeFichiers[$i]['filename']))
										{
											$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Un dossier %1$s existe déjà. Il n\'a donc pas été créé.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</span>\n";
										}
										elseif (mkdir($cheminGalerie . '/' . $listeFichiers[$i]['filename'], 0755, TRUE))
										{
											$listeModifs[] = sprintf(T_('Création du dossier %1$s.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>');
										}
										else
										{
											$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Impossible de créer le dossier %1$s.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</span>\n";
										}
									}
									else
									{
										if (file_exists($cheminGalerie . '/' . $listeFichiers[$i]['filename']))
										{
											$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Un fichier %1$s existe déjà. Il n\'y a donc pas eu extraction.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</span>\n";
										}
										elseif ($fic = fopen($cheminGalerie . '/' . $listeFichiers[$i]['filename'], 'w'))
										{
											$donnees = $fichierTar->extract($listeFichiers[$i]['filename']);
											if (fwrite($fic, $donnees))
											{
												fclose($fic);
												$listeModifs[] = sprintf(T_('Ajout de %1$s dans le dossier %2$s.'), '<code>' . $listeFichiers[$i]['filename'] . '</code>', '<code>' . $cheminGalerie . '/</code>');
											}
											else
											{
												$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Attention: une erreur a eu lieu avec le fichier %1$s. Vérifiez son état sur le serveur (s\'il s\'y trouve), et ajoutez-le à la main si nécessaire.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</span>\n";
											}
										}
										else
										{
											$listeModifs[] = "<span class='erreur'>" . sprintf(T_('Impossible de créer le fichier %1$s.'), '<code>' . $cheminGalerie . '/' . $listeFichiers[$i]['filename'] . '</code>') . "</span>";
										}
									}
								}
								unset($fichierTar);
								unlink($cheminGalerie . '/' . $nomArchive);
								$listeModifs[] = sprintf(T_('Fichier %1\$s supprimé.'), '<code>' . $cheminGalerie . '/' . $nomArchive . '</code>');
							}
						}
					}
					else
					{
						unlink($_FILES['fichier']['tmp_name']);
						$listeModifs[] = '<span class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</span>\n";
						$listeModifs[] = T_('Le fichier que vous avez téléversé sur le serveur a été supprimé.');
					}
				}
			}
		
			if (empty($listeModifs))
			{
				$listeModifs[] = '<span class="erreur">' . T_("Aucune image n'a été extraite. Veuillez vérifier les instructions.") . "</span>\n";
			}
		}
	
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Ajout d'images") . '</h3>' . "\n";
		echo '<ul>' . "\n";
		foreach ($listeModifs as $modif)
		{
			echo '<li>' . $modif . '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '</div><!-- /class=boite2 -->' . "\n";
	}

	########################################################################
	##
	## Retailler les images originales
	##
	########################################################################

	if (isset($_POST['retailler']))
	{
		$qualiteJpg = securiseTexte($_POST['qualiteJpg']);
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id . '/';
		$trad = '';
		
		if (!file_exists($cheminGalerie))
		{
			$trad = "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), "<code>$id</code>") . "</p>\n";
		}
		else
		{
			$listeModifs = array ();
		
			if ($_POST['manipulerOriginal'] == 'renommerOriginal')
			{
				if ($fic = opendir($cheminGalerie))
				{
					while($fichier = @readdir($fic))
					{
						if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
						{
							$infoFichier = pathinfo(basename($fichier));
							if (!isset($infoFichier['extension']))
							{
								$infoFichier['extension'] = '';
							}
							if (!preg_match('/-original\.' . $infoFichier['extension'] . '/', $fichier) && !preg_match('/-vignette\.' . $infoFichier['extension'] . '/', $fichier) && preg_match('/\.(gif|png|jpg|jpeg)$/i', $fichier))
							{
								$nouveauNom = basename($fichier, '.' . $infoFichier['extension']);
								$nouveauNom .= '-original.' . $infoFichier['extension'];
								if (!file_exists($cheminGalerie . '/' . $nouveauNom) && rename($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom))
								{
									$listeModifs[] = sprintf(T_('Renommage de %1$s en %2$s'), "<code>$fichier</code>", "<code>$nouveauNom</code>") . "\n";
								}
								else
								{
									$listeModifs[] = sprintf(T_('Impossible de renommer %1$s en %2$s'), "<code>$fichier</code>", "<code>$nouveauNom</code>") . "\n";
								}
							}
						}
					}
			
					closedir($fic);
				}
				else
				{
					$trad = "<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), "<code>$cheminGalerie</code>") . "</p>\n";
				}
			}
		
			// A: les images à traiter ont la forme `nom-original.extension`
		
			if (empty($trad) && $fic2 = opendir($cheminGalerie))
			{
				while($fichier = @readdir($fic2))
				{
					$infoFichier = pathinfo(basename($fichier));
					if (!isset($infoFichier['extension']))
					{
						$infoFichier['extension'] = '';
					}
					$nouveauNom = preg_replace('/-original\..{3,4}$/', '.', $fichier) . $infoFichier['extension'];
			
					if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..' && preg_match('/-original\..{3,4}$/', $fichier) && !file_exists($cheminGalerie . '/' . $nouveauNom))
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
				
						$listeModifs[] = nouvelleImage($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom, $imageIntermediaireDimensionsVoulues, $qualiteJpg, $nettete, $galerieForcerDimensionsVignette);
					}
				}
				
				closedir($fic2);
				
				if (empty($listeModifs))
				{
					$listeModifs[] = T_("Aucune modification.");
				}
			}
		}
		
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Retaillage des images") . '</h3>' . "\n";
		
		if (!empty($trad))
		{
			echo $trad;
		}
		
		if (isset($listeModifs) && !empty($listeModifs))
		{
			echo '<ul>' . "\n";
			foreach ($listeModifs as $modif)
			{
				echo '<li>' . $modif . '</li>' . "\n";
			}
			echo '</ul>' . "\n";
		}
		
		echo '</div><!-- /class=boite2 -->' . "\n";
	}

	########################################################################
	##
	## Supprimer les vignettes d'une galerie
	##
	########################################################################

	if (isset($_POST['supprimerVignettes']))
	{
		$trad = array ();
		
		// Vignettes des oeuvres
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		if ($fic = opendir($cheminGalerie))
		{
			while($fichier = @readdir($fic))
			{
				if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
				{
					if (preg_match('/-vignette\.(gif|png|jpg|jpeg)$/', $fichier))
					{
						if (unlink($cheminGalerie . '/' . $fichier))
						{
							$trad[] = '<li class="succes">' . sprintf(T_('Suppression de %1$s'), "<code>$cheminGalerie/$fichier</code>") . '</li>';
						}
						else
						{
							$trad[] = '<li class="erreur">' . sprintf(T_('Impossible de supprimer %1$s'), "<code>$cheminGalerie/$fichier</code>") . '</li>';
						}
					}
				}
			}
			closedir($fic);
		}
		else
		{
			$trad[] = "<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), "<code>$cheminGalerie</code>") . "</p>\n";
		}
		
		// Vignettes de navigation tatouées
		if (isset($_POST['supprimerVignettesAvecTatouage']) && $_POST['supprimerVignettesAvecTatouage'] == 'supprimer')
		{
			$cheminTatouage = $racine . '/site/fichiers/galeries/' . $id . '/tatouage';
			
			if ($fic = opendir($cheminTatouage))
			{
				while($fichier = @readdir($fic))
				{
					if(!is_dir($cheminTatouage . '/' . $fichier) && $fichier != '.' && $fichier != '..')
					{
						if (preg_match('/-vignette-(precedent|suivant)\.(gif|png|jpg|jpeg)$/', $fichier))
						{
							if (unlink($cheminTatouage . '/' . $fichier))
							{
								$trad[] = '<li class="succes">' . sprintf(T_('Suppression de %1$s'), "<code>$cheminTatouage/$fichier</code>") . '</li>';
							}
							else
							{
								$trad[] = '<li class="erreur">' . sprintf(T_('Impossible de supprimer %1$s'), "<code>$cheminTatouage/$fichier</code>") . '</li>';
							}
						}
					}
				}
				closedir($fic);
			}
			else
			{
				$trad[] = "<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), "<code>$cheminTatouage</code>") . "</p>\n";
			}
		}
	
		// Messages
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Suppression de vignettes") .'</h3>' ."\n" ;
	
		if (!empty($trad))
		{
			echo "<ul>\n";
			foreach ($trad as $messageSuppression)
			{
				echo "\t" . $messageSuppression . "\n";
			}
			echo "</ul>\n";
		}
		else
		{
			echo '<p>' . T_("Aucune vignette traitée.") . "</p>\n";
		}
	
		echo "</div><!-- /class=boite2 -->\n";
	}

	########################################################################
	##
	## Créer une page web de galerie
	##
	########################################################################

	if (isset($_POST['creerPage']))
	{
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
			echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), "<code>$id</code>") . "</p>";
		}
		else
		{
			$fichierConfigChemin = $racine . '/site/fichiers/galeries/' . $id . '/config.pc';
		
			if (!file_exists($fichierConfigChemin))
			{
				echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'a pas de fichier de configuration.'), "<code>$id</code>") . "</p>";
			}
			else
			{
				if (!file_exists($cheminPage))
				{
					echo "<p class='erreur'>" . sprintf(T_('Le chemin %1$s vers la page à créer n\'existe pas. Veuillez vous assurer que les dossiers existent.'), '<code>' . $cheminPage . '</code>') . "</p>";
				}
				else
				{
					if (file_exists($cheminPage . '/' . $page))
					{
						$trad = sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href='%2\$s'>éditer le fichier</a> ou <a href='%3\$s'>visiter la page</a>."), '<code>' . $cheminPage . '/' . $page . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '#messagesPorteDocuments', $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3)));
					}
					else
					{
						if ($fic = fopen($cheminPage . '/' . $page, 'a'))
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
							$trad = sprintf(T_("Le modèle de page a été créé. Vous pouvez <a href='%1\$s'>éditer le fichier</a> ou <a href='%2\$s'>visiter la page</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . rawurlencode($cheminPage . '/' . $page) . '#messagesPorteDocuments', $urlRacine . '/' . rawurlencode(substr($cheminPage . '/' . $page, 3)));
						}
						else
						{
							echo "<p class='erreur'>" . sprintf(T_('Impossible de créer le fichier %1$s.'), '<code>' . $cheminPage . '/' . $page . '</code>') . "</p>";
						}
					}
				
					if (file_exists($cheminPage . '/' . $page))
					{
						echo '<div class="boite2">' . "\n";
						echo '<h3>' . T_("Page web") . '</h3>' . "\n";
				
						echo '<p>' . $trad . '</p>';
						echo "</div><!-- /class=boite2 -->\n";
					}
				}
			}
		}
	}

	########################################################################
	##
	## Afficher un modèle de fichier de configuration
	##
	########################################################################

	if (isset($_POST['modeleConf']))
	{
		$trad = '';
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		
		if ($fic = opendir($cheminGalerie))
		{
			$listeFichiers = '';
			$tableauFichiers = array ();
			while($fichier = @readdir($fic))
			{
				if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
				{
					if (!preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier) && !preg_match('/-original\.[[:alpha:]]{3,4}$/', $fichier) && preg_match('/\.(gif|png|jpg|jpeg)$/i', $fichier))
					{
						$tableauFichiers[] = $fichier;
					}
				}
			}
			closedir($fic);
	
			sort($tableauFichiers);
			$listeFichiers = '';
	
			foreach ($tableauFichiers as $cle)
			{
				$listeFichiers .= "intermediaireNom=$cle\n";
		
				if (isset($_POST['info']) && $_POST['info'][0] != 'aucun')
				{
					foreach ($_POST['info'] as $champ)
					{
						$listeFichiers .= securiseTexte($champ) . "=\n";
					}
				}
		
				$listeFichiers .= "#IMG\n";
			}
		}
		else
		{
			$trad = "<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), "<code>$cheminGalerie</code>") . "</p>\n";
		}
		
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Modèle") .'</h3>' ."\n" ;
		
		if (!empty($trad))
		{
			echo $trad;
		}
		else
		{
			echo '<pre id="listeFichiers">' . $listeFichiers . '</pre>' . "\n";
			echo "<ul>\n";
			echo "<li><a href=\"javascript:selectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			echo "</ul>\n";
		}
		
		echo "</div><!-- /class=boite2 -->\n";
	}

	########################################################################
	##
	## Créer ou mettre à jour le fichier de configuration
	##
	########################################################################

	$boite2FichierConfigDebut = FALSE;

	if (isset($_POST['conf']) && $_POST['conf'] == 'maj')
	{
		$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
		if (!file_exists($cheminGalerie))
		{
			echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), "<code>$id</code>") . "</p>";
		}
		else
		{
			$fichierConfigChemin = $racine . '/site/fichiers/galeries/' . $id . '/config.pc';
		
			if (file_exists($fichierConfigChemin))
			{
				$configExisteAuDepart = TRUE;
			}
			else
			{
				$configExisteAuDepart = FALSE;
			}
		
			if (adminMajConfGalerie($racine, $id, ''))
			{
				$listeModifs = array ();
				if ($configExisteAuDepart)
				{
					$listeModifs[] = sprintf(T_("Mise à jour du fichier de configuration %1\$s."), '<code>' . $fichierConfigChemin . '</code>');
				}
				else
				{
					$listeModifs[] = sprintf(T_("Création du fichier de configuration %1\$s."), '<code>' . $fichierConfigChemin . '</code>');
				}
			}
			else
			{
				echo "<p class='erreur'>" . sprintf(T_('Erreur lors de la création ou de la mise à jour du fichier de configuration %1$s. Veuillez vérifier manuellement son contenu.'), "<code>$fichierConfigChemin</code>") . "</p>";
			}
		
			$boite2FichierConfigDebut = TRUE;
			echo '<div class="boite2">' . "\n";
			echo '<h3>' . T_("Fichier de configuration") . '</h3>' . "\n";
			if (!empty($listeModifs))
			{
				echo '<h4>' . T_("Actions effectuées") .'</h4>' ."\n" ;
				echo "<ul>\n";
				foreach ($listeModifs as $modif)
				{
					echo "<li>$modif</li>\n";
				}
				echo "</ul>\n";
			}
		}
	}

	########################################################################

	if ((isset($_POST['modeleConf']) || (isset($_POST['conf']) && $_POST['conf'] == 'maj')) && file_exists($racine . '/site/fichiers/galeries/' . $id . '/config.pc'))
	{
		if (!$boite2FichierConfigDebut)
		{
			echo '<div class="boite2">' . "\n";
			echo '<h3>' . T_("Fichier de configuration") . '</h3>' . "\n";
		}
	
		$id = rawurlencode($id);
		$fichierConfigChemin = $racine . '/site/fichiers/galeries/' . $id . '/config.pc';
		echo '<h4>' . T_("Information") .'</h4>' ."\n" ;
		echo "<ul>\n";
		echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=editer&amp;valeur=../site/fichiers/galeries/' . $id . '/config.pc#messagesPorteDocuments">' . T_("Modifier le fichier.") . '</a></li>' . "\n";
		echo "</ul>\n";
	}

	########################################################################

	if ($boite2FichierConfigDebut)
	{
		echo "</div><!-- /class=boite2 -->\n";
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

	<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes. Chaque galerie dans la liste aura un lien vous permettant de modifier son fichier de configuration dans le porte-documents."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><input type="submit" name="lister" value="<?php echo T_('Lister les galeries'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Ajouter des images"); ?></h2>

	<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (.tar) ou ZIP (.zip). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>

	<p><?php echo T_("Vous pouvez également ajouter une seule image en choisissant un fichier image au lieu d'une archive."); ?></p>

	<p><?php printf(T_('<strong>Taille maximale d\'un transfert de fichier:</strong> %1$s octets (%2$s Mio).'), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post" enctype="multipart/form-data">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie (si la galerie n'existe pas, elle sera créée):"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><label><?php echo T_("Fichier:"); ?></label><br />
			<input type="file" name="fichier" size="25"/></p>

			<p><input type="checkbox" name="conf" value="maj" checked="checked" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie avec les paramètres par défaut (les fichiers <code>-vignette.extension</code> et <code>-original.extension</code> sont ignorés, les autres sont considérés comme étant la version intermediaire à afficher)."); ?></label></p>

			<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter des images'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer des images de taille intermédiaire à partir des images originales"); ?></h2>

	<p><?php echo T_("Vous pouvez faire générer automatiquement une copie réduite (qui sera utilisée comme étant la version intermediaire dans la galerie) de chaque image originale. Aucune image au format original ne sera modifiée."); ?></p>

	<p><?php echo T_("Note: pour chaque image originale, une image en version intermediaire sans le suffixe <code>-original</code> sera créée, si un tel fichier n'existe pas déjà. Les fichiers <code>-vignette.extension</code> sont ignorés. Si <code>nom-original.extension</code> et <code>nom.extension</code> existent tous les deux, il n'y aura pas de création de version intermédiaire."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><label><?php echo T_("Taille maximale de la version intermediaire (largeur × hauteur):"); ?></label><br />
			<?php echo T_("La plus grande taille possible contenable dans les dimensions données sera utilisée, sans toutefois dépasser la taille originale. Si une seule dimension est précisée, l'autre sera calculée à partir de la dimension donnée ainsi que des dimensions de l'image source. Les proportions de l'image sont conservées. Au moins une dimension doit être donnée."); ?><br />
			<input type="text" name="largeur" size="4" value="500" /> <?php echo T_("px de largeur"); ?> <?php echo T_("×"); ?> <input type="text" name="hauteur" size="4" value="500" /> <?php echo T_("px de hauteur"); ?></p>

			<p><label><?php echo T_("Comment manipuler les images du dossier?"); ?></label><br />
			<input type="radio" name="manipulerOriginal" value="renommerOriginal" checked="checked" /> <?php echo T_("Renommer préalablement les images du dossier en <code>nom-original.extension</code>. Les fichiers <code>-vignette.extension</code> et <code>-original.extension</code> sont ignorés lors du renommage."); ?><br />
			<input type="radio" name="manipulerOriginal" value="original" /> <?php echo T_("Le nom des images au format original se termine déjà par <code>-original.extension</code>."); ?></p>

			<p><label><?php echo T_("S'il y a lieu, qualité des images JPG générées (0-100):"); ?></label><br />
			<input type="text" name="qualiteJpg" value="90" size="2" /></p>

			<p><input type="checkbox" name="actions" value="nettete" /> <label><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)."); ?></label></p>

			<p><input type="checkbox" name="conf" value="maj" checked="checked" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie avec les paramètres par défaut (les fichiers <code>-vignette.extension</code> et <code>-original.extension</code> sont ignorés, les autres sont considérés comme étant la version intermediaire à afficher)."); ?></label></p>

			<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

			<p><input type="submit" name="retailler" value="<?php echo T_('Retailler les images originales'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Supprimer les vignettes d'une galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez supprimer les vignettes d'une galerie pour forcer leur regénération automatique. Seules les vignettes avec la forme par défaut (<code>-vignette.extension</code>) seront supprimées."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><?php echo T_("Si la navigation entre les oeuvres d'une galerie est réalisée avec des vignettes et si <code>\$galerieNavigationVignettesTatouage</code> vaut <code>TRUE</code>, de nouvelles vignettes de navigation vers les oeuvres précédente et suivante sont générées, et contiennent une petite image (par défaut une flèche) au centre."); ?></p>

			<p><input type="checkbox" name="supprimerVignettesAvecTatouage" value="supprimer" /> <label><?php echo T_("Supprimer également les vignettes de navigation avec tatouage."); ?></label></p>

			<p><input type="submit" name="supprimerVignettes" value="<?php echo T_('Supprimer les vignettes'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer une page web de galerie"); ?></h2>

	<p><?php echo T_("Vous pouvez ajouter une page sur votre site pour présenter une galerie."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><label><?php echo T_("Emplacement de la page web:"); ?></label><br />
			<?php echo $urlRacine . '/'; ?><input type="text" name="page" /></p>

			<p><input type="submit" name="creerPage" value="<?php echo T_('Créer une page web'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Créer ou mettre à jour un fichier de configuration"); ?></h2>

	<p><?php echo T_("Crée ou met à jour le fichier de configuration de cette galerie avec les paramètres par défaut (les fichiers <code>-vignette.extension</code> et <code>-original.extension</code> sont ignorés, les autres sont considérés comme étant la version intermediaire à afficher)."); ?></p>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><input type="submit" name="majConf" value="<?php echo T_('Créer ou mettre à jour'); ?>" /></p>

			<input type="hidden" name="conf" value="maj" />
		</div>
	</form>
</div><!-- /class=boite -->

<!-- class=boite -->

<div class="boite">
	<h2><?php echo T_("Afficher un modèle de fichier de configuration"); ?></h2>

	<form action="<?php echo $action; ?>#messages" method="post">
		<div>
			<p><label><?php echo T_("Identifiant de la galerie:"); ?></label><br />
			<input type="text" name="id" /></p>

			<p><label><?php echo T_("En plus du champ obligatoire <code>intermediaireNom</code>, ajouter des champs vides:"); ?></label><br />
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

			<p><?php echo T_("Note: les fichiers <code>-vignette.extension</code> et <code>-original.extension</code> sont ignorés, les autres sont considérés comme étant la version intermediaire à afficher."); ?></p>

			<p><input type="submit" name="modeleConf" value="<?php echo T_('Afficher un fichier de configuration'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
