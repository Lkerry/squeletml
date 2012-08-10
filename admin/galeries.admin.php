<?php
include 'inc/zero.inc.php';

$baliseTitle = T_("Galeries");
$boitesDeroulantes = '#ajoutParametresAdminGaleries';
$boitesDeroulantes .= ' .aideAdminGaleries .autresParametres .configGraphiqueListeParametres';
$boitesDeroulantes .= ' .contenuFichierPourSauvegarde';
$boitesDeroulantes .= ' .fichierConfigAdminGaleries .galeriesAdminModifierConfig';
$boitesDeroulantes .= ' .optionsAvanceesAdminGaleries';
$boitesDeroulantes .= ' #optionsNouvelleGalerieAdminGaleries';
include $racineAdmin . '/inc/premier.inc.php';
?>

<div id="sousMenu">
	<ul>
		<li><a href="#messages"><?php echo T_("Messages"); ?></a></li>
		<li><a href="#lister"><?php echo T_("Lister"); ?></a></li>
		<li><a href="#ajouter"><?php echo T_("Ajouter"); ?></a></li>
		<li><a href="#redimensionner"><?php echo T_("Redimensionner"); ?></a></li>
		<li><a href="#supprimer"><?php echo T_("Supprimer"); ?></a></li>
		<li><a href="#renommer"><?php echo T_("Renommer"); ?></a></li>
		<li><a href="#configGraphique"><?php echo T_("Configuration graphique"); ?></a></li>
		<li><a href="#configAutomatique"><?php echo T_("Configuration automatique"); ?></a></li>
		<li><a href="#modele"><?php echo T_("Modèle"); ?></a></li>
		<li><a href="#sauvegarder"><?php echo T_("Sauvegarder"); ?></a></li>
	</ul>
</div>

<div id="contenuPrincipal">
	<h1><?php echo T_("Gestion des galeries"); ?></h1>
	
	<div id="boiteMessages" class="boite">
		<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

		<?php
		$tableauParametres = adminParametresImage();
		$tailleMaxFichier = adminPhpIniOctets(ini_get('upload_max_filesize'));
		$ajoutNouvelleGalerie = FALSE;
		$id = '';
		$idDossier = '';
		
		if (isset($_POST['id']))
		{
			$id = superBasename(decodeTexte($_POST['id']));
		}
		elseif (isset($_GET['id']))
		{
			$id = superBasename($_GET['id']);
			$listeGaleries = listeGaleries($racine);
			
			foreach ($listeGaleries as $idGalerie => $infosGalerie)
			{
				if ($id == filtreChaine($idGalerie))
				{
					$id = $idGalerie;
					
					if (!empty($infosGalerie['dossier']))
					{
						$idDossier = $infosGalerie['dossier'];
					}
					
					break;
				}
			}
		}
		
		if (!empty($id) && empty($idDossier))
		{
			$idDossier = idGalerieDossier($racine, $id);
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
	
		$rotationSansPerteActivee = FALSE;
		$suppressionExifActivee = FALSE;
		
		if (@is_executable($adminCheminExiftran) || (@is_executable($adminCheminJpegtran) && function_exists('exif_read_data')))
		{
			$rotationSansPerteActivee = TRUE;
		}
		
		if (@is_executable($adminCheminJpegtran))
		{
			$suppressionExifActivee = TRUE;
		}
		
		########################################################################
		##
		## Listage des galeries existantes.
		##
		########################################################################
		
		if (isset($_POST['lister']) || (isset($_GET['action']) && $_GET['action'] == 'lister'))
		{
			$messagesScript = '';
			
			$listeGaleries = listeGaleries($racine);
			$tableauInfosGaleries = array ();
			$i = 0;
			
			foreach ($listeGaleries as $idGalerie => $infosGalerie)
			{
				$i++;
				$idGalerieDossier = '';
				
				if (!empty($infosGalerie['dossier']))
				{
					$idGalerieDossier = $infosGalerie['dossier'];
				}
				
				$cheminConfigGalerie = cheminConfigGalerie($racine, $idGalerieDossier);
				$fichierDeConfiguration = '';
				
				if ($cheminConfigGalerie)
				{
					$fichierDeConfiguration .= '<li><a href="galeries.admin.php?action=configGraphique&amp;id=' . filtreChaine($idGalerie) . '#messages">' . T_("Modifier graphiquement le fichier de configuration.") . "</a></li>\n";
					$fichierDeConfiguration .= '<li><a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte('../site/fichiers/galeries/' . $idGalerieDossier . '/' . superBasename($cheminConfigGalerie)) . '&amp;dossierCourant=' . encodeTexte('../site/fichiers/galeries/' . $idGalerieDossier) . '#messages">' . T_("Modifier manuellement le fichier de configuration dans le porte-documents.") . "</a></li>\n";
				}
				else
				{
					$fichierDeConfiguration .= '<li>' . T_("Aucun fichier de configuration.") . "</li>\n";
				}
				
				if ($cheminConfigGalerie && gdEstInstallee())
				{
					$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerieDossier), TRUE);
					$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerieDossier;
					$nombreDimages = count($tableauGalerie);
					$corpsMinivignettes = '';
				
					for ($j = 0; $j <= ($nombreDimages - 1) && $j < $nombreDimages; $j++)
					{
						$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$j]['intermediaireNom']);
						$minivignette = image($racine, $urlRacine, dirname($cheminConfigGalerie), $urlRacine . '/site/fichiers/galeries/' . encodeTexte($idGalerieDossier), FALSE, $nombreDeColonnes, $tableauGalerie[$j], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, '', $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, FALSE);
						preg_match('|(<img[^>]+/>)|', $minivignette, $resultat);
						$minivignette = $resultat[1];
					
						if ($adminActiverInfobulle['apercuGalerie'])
						{
							$infobulle = adminInfobulle($racineAdmin, $urlRacineAdmin, dirname($cheminConfigGalerie) . '/' . $tableauGalerie[$j]['intermediaireNom'], FALSE, $adminTailleCache, $galerieQualiteJpg, $galerieCouleurAlloueeImage);
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
						$apercu = '<li class="apercuGalerie">' . sprintf(T_ngettext("Aperçu (%1\$s image): %2\$s", "Aperçu (%1\$s images): %2\$s", $nombreDimages), $nombreDimages, $corpsMinivignettes) . "</li>\n";
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
				
				$tableauInfosGaleries[$idGalerieDossier] = '<li class="listeGaleriesTitre">' . sprintf(T_("Galerie %1\$s:"), $i) . "\n";
				$tableauInfosGaleries[$idGalerieDossier] .= "<ul>\n";
				$tableauInfosGaleries[$idGalerieDossier] .= '<li>' . sprintf(T_("Identifiant: %1\$s"), securiseTexte($idGalerie)) . "</li>\n";
				$tableauInfosGaleries[$idGalerieDossier] .= '<li>' . sprintf(T_("Dossier: %1\$s"), '<a href="porte-documents.admin.php?action=parcourir&amp;valeur=' . encodeTexte('../site/fichiers/galeries/' . $idGalerieDossier) . '&amp;dossierCourant=' . encodeTexte('../site/fichiers/galeries/' . $idGalerieDossier) . '#fichiersEtDossiers"><code>' . securiseTexte($idGalerieDossier) . '</code></a>') . "</li>\n";
				
				if (!empty($infosGalerie['url']))
				{
					$urlGalerieAafficher = $infosGalerie['url'];
				}
				else
				{
					$urlGalerieAafficher = urlGalerie(0, $racine, $urlRacine, $idGalerie, '', FALSE);
					$urlGalerieAafficher = supprimeUrlRacine($urlRacine, $urlGalerieAafficher);
				}
				
				$urlGalerie = urlGalerie(1, '', $urlRacine, $urlGalerieAafficher, LANGUE_ADMIN);
				$tableauInfosGaleries[$idGalerieDossier] .= '<li>' . sprintf(T_("URL: %1\$s"), '<a href="' . $urlGalerie . '"><code>' . securiseTexte($urlGalerieAafficher) . '</code></a>');
				
				if (strpos($urlGalerieAafficher, 'galerie.php?') !== 0)
				{
					$cheminPageGalerie = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($urlGalerieAafficher));
					$tableauInfosGaleries[$idGalerieDossier] .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminPageGalerie) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminPageGalerie)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPageGalerie)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPageGalerie)) . '" width="16" height="16" /></a>';
				}
				
				$tableauInfosGaleries[$idGalerieDossier] .= "</li>\n";
				$tableauInfosGaleries[$idGalerieDossier] .= '<li>';
				
				if (!empty($infosGalerie['rss']) && $infosGalerie['rss'] == 1)
				{
					$tableauInfosGaleries[$idGalerieDossier] .= T_("RSS: activé");
				}
				else
				{
					$tableauInfosGaleries[$idGalerieDossier] .= T_("RSS: désactivé");
				}
				
				$tableauInfosGaleries[$idGalerieDossier] .= "</li>\n";
				$tableauInfosGaleries[$idGalerieDossier] .= $fichierDeConfiguration;
				$tableauInfosGaleries[$idGalerieDossier] .= $apercu;
				$tableauInfosGaleries[$idGalerieDossier] .= "</ul></li>\n";
			}
			
			if (!empty($tableauInfosGaleries))
			{
				natcasesort($tableauInfosGaleries);
				
				foreach ($tableauInfosGaleries as $infosGalerie)
				{
					$messagesScript .= $infosGalerie;
				}
			}
			
			if (empty($messagesScript))
			{
				$fic = @opendir($racine . '/site/fichiers/galeries');
				
				if (!$fic)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte("$racine/site/fichiers/galeries") . '</code>') . "</li>\n";
				}
				else
				{
					closedir($fic);
				}
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
			$idNouvelleGalerie = '';
			$idNouvelleGalerieDossier = '';
			
			if (!empty($_POST['idNouvelleGalerie']))
			{
				$idNouvelleGalerie = superBasename($_POST['idNouvelleGalerie']);
			}
			
			if (!empty($_POST['idNouvelleGalerieDossier']))
			{
				$idNouvelleGalerieDossier = superBasename($_POST['idNouvelleGalerieDossier']);
			}
			
			if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > adminPhpIniOctets(ini_get('post_max_size')))
			{
				$messagesScript .= '<li class="erreur">' . T_("Le fichier téléchargé excède la taille de <code>post_max_size</code>, configurée dans le <code>php.ini</code>.") . "</li>\n";
			}
			elseif (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif ($id == 'nouvelleGalerie' && empty($idNouvelleGalerie))
			{
				$messagesScript .= '<li class="erreur">' . T_("Vous avez choisi de créer une nouvelle galerie, mais vous n'avez pas saisi de nom pour cette dernière.") . "</li>\n";
			}
			elseif ($id == 'nouvelleGalerie' && !empty($idNouvelleGalerieDossier) && file_exists($racine . '/site/fichiers/galeries/' . $idNouvelleGalerieDossier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Vous avez choisi de créer une nouvelle galerie dans le dossier %1\$s, mais ce dernier existe déjà."), '<code>' . securiseTexte($racine . '/site/fichiers/galeries/' . $idNouvelleGalerieDossier) . '</code>') . "</li>\n";
			}
			elseif ($id == 'nouvelleGalerie' && empty($idNouvelleGalerieDossier) && file_exists($racine . '/site/fichiers/galeries/' . filtreChaine($idNouvelleGalerie)))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Vous avez choisi de créer une nouvelle galerie, mais le dossier %1\$s existe déjà."), '<code>' . securiseTexte($racine . '/site/fichiers/galeries/' . filtreChaine($idNouvelleGalerie)) . '</code>') . "</li>\n";
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
					$ajoutNouvelleGalerie = TRUE;
					$id = $idNouvelleGalerie;
					
					if (!empty($idNouvelleGalerieDossier))
					{
						$idDossier = $idNouvelleGalerieDossier;
					}
					else
					{
						$idDossier = filtreChaine($id);
					}
				}
				
				$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
				
				if (!file_exists($cheminGalerie))
				{
					$messagesScript .= adminMkdir($cheminGalerie, octdec(755), TRUE);
				}
				
				if (file_exists($cheminGalerie) && isset($_FILES['fichier']))
				{
					$nomFichier = superBasename($_FILES['fichier']['name']);
					$casse = '';
					$filtrerNom = FALSE;
					
					if (isset($_POST['filtrerNom']) && in_array('filtrer', $_POST['filtrerNom']))
					{
						$filtrerNom = TRUE;
						$ancienNomFichier = $nomFichier;
						
						if (in_array('min', $_POST['filtrerNom']))
						{
							$casse = 'min';
						}
						
						$nomFichier = filtreChaine($nomFichier, $casse);
						
						if ($nomFichier != $ancienNomFichier)
						{
							$messagesScript .= '<li>' . sprintf(T_("Filtrage de la chaîne de caractères %1\$s en %2\$s effectué."), '<code>' . securiseTexte($ancienNomFichier) . '</code>', '<code>' . securiseTexte($nomFichier) . '</code>') . "</li>\n";
						}
					}
					
					if (file_exists($cheminGalerie . '/' . $nomFichier))
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
					}
					else
					{
						$typeMime = typeMime($_FILES['fichier']['tmp_name']);
						
						if (!adminTypeMimePermis($typeMime, $adminFiltreTypesMime, $adminTypesMimePermis))
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), '<code>' . securiseTexte($nomFichier) . '</code>', "<code>$typeMime</code>") . "</li>\n";
						}
						elseif (@move_uploaded_file($_FILES['fichier']['tmp_name'], $cheminGalerie . '/' . $nomFichier))
						{
							$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans %2\$s effectué."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
							
							$rotationAuto = FALSE;
							$suppressionExif = FALSE;
							
							if (isset($_POST['rotationAuto']))
							{
								$rotationAuto = TRUE;
							}
							
							if (isset($_POST['suppressionExif']))
							{
								$suppressionExif = TRUE;
							}
							
							if ($typeMime == 'image/jpeg')
							{
								if ($rotationAuto && $rotationSansPerteActivee)
								{
									$messagesScript .= adminRotationJpegSansPerte($cheminGalerie . '/' . $nomFichier, $adminCheminExiftran, $adminCheminJpegtran, $suppressionExif);
								}
								elseif ($suppressionExif && $suppressionExifActivee)
								{
									$messagesScript .= adminSupprimeExif($cheminGalerie . '/' . $nomFichier, $cheminJpegtran);
								}
							}
							elseif ($typeMime == 'application/x-tar' || $typeMime == 'application/x-bzip2' || $typeMime == 'application/x-gzip' || $typeMime == 'application/zip')
							{
								$retourAdminExtraitArchive = adminExtraitArchive($cheminGalerie . '/' . $nomFichier, $cheminGalerie, $adminFiltreTypesMime, $adminTypesMimePermis, $filtrerNom, $casse);
								$messagesScript .= $retourAdminExtraitArchive['messagesScript'];
								
								foreach ($retourAdminExtraitArchive['fichiersExtraits'] as $fichierExtrait)
								{
									$typeMimeFichierExtrait = $fichierExtrait;
									
									if ($typeMimeFichierExtrait == 'image/jpeg')
									{
										if ($rotationAuto && $rotationSansPerteActivee)
										{
											$messagesScript .= adminRotationJpegSansPerte($fichierExtrait, $adminCheminExiftran, $adminCheminJpegtran, $suppressionExif);
										}
										elseif ($suppressionExif && $suppressionExifActivee)
										{
											$messagesScript .= adminSupprimeExif($fichierExtrait, $cheminJpegtran);
										}
									}
								}
								
								$messagesScript .= adminUnlink($cheminGalerie . '/' . $nomFichier);
							}
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout de %1\$s dans %2\$s impossible."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($cheminGaleries) . '</code>') . "</li>\n";
						}
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
				$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
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
		
		if (isset($_POST['redimensionner']) && gdEstInstallee())
		{
			$messagesScript = '';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			$erreur = FALSE;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
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
								$typeMime = typeMime($cheminGalerie . '/' . $fichier);
							
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
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
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
									$nettete = array (
										'nettete' => FALSE,
										'gain' => 100,
										'rayon' => 1,
										'seuil' => 3,
									);
								
									if (isset($_POST['nettete']))
									{
										$nettete['nettete'] = TRUE;
									
										if (!empty($_POST['netteteGain']))
										{
											$nettete['gain'] = securiseTexte($_POST['netteteGain']);
										}

										if (!empty($_POST['netteteRayon']))
										{
											$nettete['rayon'] = securiseTexte($_POST['netteteRayon']);
										}

										if (!empty($_POST['netteteSeuil']))
										{
											$nettete['seuil'] = securiseTexte($_POST['netteteSeuil']);
										}
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
								
									$typeMime = typeMime($cheminGalerie . '/' . $fichier);
									$messagesScript .= nouvelleImage($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom, $typeMime, $imageIntermediaireDimensionsVoulues, FALSE, $galerieQualiteJpg, $galerieCouleurAlloueeImage, $nettete);
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
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
					}
				}
			}
		
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
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
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
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
					while ($fichier = @readdir($fic))
					{
						if (!is_dir($cheminGalerie . '/' . $fichier))
						{
							$typeMime = typeMime($cheminGalerie . '/' . $fichier);
							$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime);
						
							if (isset($_POST['supprimer']) && ((in_array('vignettes', $_POST['supprimer']) && $versionImage == 'vignette') || (in_array('intermediaires', $_POST['supprimer']) && $versionImage == 'intermediaire') || (in_array('original', $_POST['supprimer']) && $versionImage == 'original')))
							{
								$messagesScript .= adminUnlink($cheminGalerie . '/' . $fichier);
							}
						}
					}
				
					closedir($fic);
					
					if (isset($_POST['supprimer']) && in_array('vignettesAvecTatouage', $_POST['supprimer']))
					{
						$cheminTatouage = $racine . '/site/fichiers/galeries/' . $idDossier . '/tatouage';
				
						if (!file_exists($cheminTatouage))
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Le dossier des vignettes avec tatouage %1\$s n'existe pas."), '<code>' . securiseTexte($cheminTatouage) . '</code>') . "</li>\n";
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
							
									$typeMime = typeMime($cheminTatouage . '/' . $fichier);
							
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
								$messagesScript .= '<li>' . sprintf(T_("Le dossier %1\$s n'est pas vide, il ne sera donc pas supprimé."), '<code>' . securiseTexte($cheminTatouage) . '</code>') . "</li>\n";
							}
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte($cheminTatouage) . '</code>') . "</li>\n";
						}
					}
					
					// Suppression de la galerie s'il ne reste plus d'images.
					
					$listeFichiersGalerie = adminListeFichiers($cheminGalerie);
					unset($listeFichiersGalerie[0]);
					$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
					
					if ($cheminConfigGalerie !== FALSE && count($listeFichiersGalerie) == 1 && $listeFichiersGalerie[1] == $cheminConfigGalerie)
					{
						$messagesScript .= adminUnlink($cheminConfigGalerie);
					}
					
					if (adminDossierEstVide($cheminGalerie))
					{
						$messagesScript .= adminRmdir($cheminGalerie);
						$cheminConfigGaleries = cheminConfigGaleries($racine, TRUE);
						
						if (adminMajConfigGaleries($racine, array ($id => array ())))
						{
							$messagesScript .= '<li>' . sprintf(T_("La galerie %1\$s a été supprimée du fichier de configuration des galeries %2\$s."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de la suppression de la galerie %1\$s du fichier de configuration des galeries %2\$s."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
						}
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
				}
			}
		
			if (empty($messagesScript))
			{
				$messagesScript .= '<li>' . T_("Aucune image à traiter.") . "</li>\n";
			}
		
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Suppression d'images"));
		}
	
		########################################################################
		##
		## Renommage ou déplacement d'une galerie.
		##
		########################################################################

		if (isset($_POST['renommer']))
		{
			$messagesScript = '';
			$nouvelId = '';
			$nouveauNomDossier = '';
			$nouvelleUrl = '';
			$listeGaleries = listeGaleries($racine);
			
			if (!empty($_POST['idNouveauNomGalerie']))
			{
				$nouvelId = superBasename($_POST['idNouveauNomGalerie']);
			}
			
			if (!empty($_POST['idNouveauNomDossier']))
			{
				$nouveauNomDossier = superBasename($_POST['idNouveauNomDossier']);
			}
			
			if (!empty($_POST['nouvelleUrl']))
			{
				$nouvelleUrl = supprimeUrlRacine($urlRacine, $_POST['nouvelleUrl']);
				$nouvelleUrl = superBasename($nouvelleUrl);
			}
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!isset($listeGaleries[$id]))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			elseif (empty($nouvelId) && empty($nouveauNomDossier) && empty($nouvelleUrl))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune option sélectionnée.") . "</li>\n";
			}
			elseif (!empty($nouvelId) && isset($listeGaleries[$nouvelId]))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("L'identifiant de galerie %1\$s existe déja. Renommage de %2\$s impossible."), '<code>' . securiseTexte($nouvelId) . '</code>', '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			elseif (!empty($nouveauNomDossier) && file_exists($racine. '/site/fichiers/galeries/' . $nouveauNomDossier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Le dossier %1\$s existe déja. Renommage de %2\$s impossible."), '<code>' . securiseTexte($racine. '/site/fichiers/galeries/' . $nouveauNomDossier) . '</code>', '<code>' . securiseTexte($racine . '/site/fichiers/galeries/' . $dossierActuel) . '</code>') . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
				$listeModifs = array ();
				$listeModifs[$id] = $listeGaleries[$id];
				
				if (!empty($nouveauNomDossier) && !empty($listeGaleries[$id]['dossier']))
				{
					$messagesScript .= adminRename($racine . '/site/fichiers/galeries/' . $listeGaleries[$id]['dossier'], $racine . '/site/fichiers/galeries/' . $nouveauNomDossier);
					$listeModifs[$id]['dossier'] = $nouveauNomDossier;
				}
				
				if (!empty($nouvelleUrl))
				{
					$messagesScript .= '<li>' . sprintf(T_("URL %1\$s modifiée pour %2\$s."), '<code>' . securiseTexte($listeModifs[$id]['url']) . '</code>', '<code>' . securiseTexte($nouvelleUrl) . '</code>') . "</li>\n";
					$listeModifs[$id]['url'] = $nouvelleUrl;
				}
				
				if (!empty($nouvelId))
				{
					$messagesScript .= '<li>' . sprintf(T_("Identifiant %1\$s modifié pour %2\$s."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($nouvelId) . '</code>') . "</li>\n";
					$listeModifs[$nouvelId] = $listeModifs[$id];
					$listeModifs[$id] = array ();
				}
				
				$cheminConfigGaleries = cheminConfigGaleries($racine, TRUE);
				
				if (adminMajConfigGaleries($racine, $listeModifs))
				{
					$messagesScript .= '<li>' . sprintf(T_("Mise à jour des données de la galerie %1\$s dans le fichier de configuration des galeries %2\$s effectuée."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de la mise à jour des données de la galerie %1\$s dans le fichier de configuration des galeries %2\$s."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
				}
			}
			
			echo adminMessagesScript($messagesScript, T_("Renommage ou déplacement d'une galerie"));
		}
		
		########################################################################
		##
		## Mise à jour graphique d'une galerie.
		##
		########################################################################
		
		/* Formulaire. */
		
		if ((isset($_POST['listeConfigGraphique']) && $_POST['listeConfigGraphique'] == 'configGraphique') || (isset($_GET['action']) && $_GET['action'] == 'configGraphique'))
		{
			$messagesScript = '';
			$messagesScript .= '<p>' . T_("<strong>Important:</strong> ne pas oublier de cliquer sur le bouton «Mettre à jour» pour sauvegarder les modifications.") . "</p>\n";
			
			$messagesScript .= '<p><a href="galeries.admin.php?action=configGraphiqueSimplifiee&amp;id=' . filtreChaine($id) . '#messages">' . T_("Utiliser la version simplifiée du formulaire de mise à jour graphique.") . "</a></p>\n";
			
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);

			$messagesScript .= "<ul>\n";
			$messagesScript .= '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			$messagesScript .= "</ul>\n";
			$corpsGalerie = '';
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</p>\n";
			}
			elseif (!file_exists($cheminConfigGalerie))
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La galerie %1\$s n'a pas de fichier de configuration."), '<code>' . securiseTexte($id) . '</code>') . "</p>\n";
			}
			else
			{
				$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idDossier));
				
				if (!empty($tableauGalerie))
				{
					$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idDossier;
					$nombreDimages = count($tableauGalerie);
					$corpsGalerie .= '<div id="galeriesAdminConfigGraphique">' . "\n";
					$corpsGalerie .= "<form action=\"$adminAction#messages\" method=\"post\">\n";
					$corpsGalerie .= "<div>\n";
					$corpsGalerie .= '<input type="hidden" name="configGraphiqueIdGalerie" value="' . encodeTexte($id) . '" />' . "\n";
					$corpsGalerie .= '<ul class="triable">' . "\n";
					
					for ($i = 0; $i <= ($nombreDimages - 1) && $i < $nombreDimages; $i++)
					{
						if (gdEstInstallee())
						{
							$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$i]['intermediaireNom']);
							$vignette = image($racine, $urlRacine, dirname($cheminConfigGalerie), $urlRacine . '/site/fichiers/galeries/' . encodeTexte($idDossier), FALSE, $nombreDeColonnes, $tableauGalerie[$i], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, '', $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
							preg_match('|(<img[^>]+/>)|', $vignette, $resultat);
							$vignette = '<div class="configGraphiqueVignette">' . $resultat[1] . "</div><!-- /.configGraphiqueVignette -->\n";
						}
						else
						{
							$vignette = '';
						}
						
						$intermediaireNom = $tableauGalerie[$i]['intermediaireNom'];
						
						$config = '';
						$config .= "<div class=\"configGraphiqueListeParametres\">\n";
						$config .= '<input type="hidden" name="configGraphiqueVignettes[]" value="' . encodeTexte($intermediaireNom) . '" />' . "\n";
						$config .= '<input type="hidden" name="indiceIntermediaireNom[' . encodeTexte($intermediaireNom) . ']" value="' . $i . '" />' . "\n";
						$config .= '<p class="bDtitre"><code>' . securiseTexte($intermediaireNom) . "</code></p>\n";
						
						$config .= "<ul class=\"nonTriable bDcorps\">\n";
						
						foreach ($tableauParametres[0] as $parametre)
						{
							$contenuParametre = '';
							
							if (!empty($tableauGalerie[$i][$parametre]))
							{
								$contenuParametre = $tableauGalerie[$i][$parametre];
							}
							
							$config .= '<li><input id="configGraphiqueInput-' . $i . '-' . $parametre . '" class="long" type="text" name="parametres[' . $i . '][' . $parametre . ']" value="' . securiseTexte($contenuParametre) . '" /> <label for="configGraphiqueInput-' . $i . '-' . $parametre . '"><code>' . $parametre . "</code></label></li>\n";
						}
						
						$config .= '<li class="autresParametres"><span class="bDtitre">' . T_("Afficher plus de paramètres") . '</span>';
						$config .= "<ul class=\"bDcorps\">\n";
						
						foreach ($tableauParametres[1] as $parametre)
						{
							$contenuParametre = '';
							
							if (!empty($tableauGalerie[$i][$parametre]))
							{
								$contenuParametre = $tableauGalerie[$i][$parametre];
							}
							
							$config .= '<li><input id="configGraphiqueInput-' . $i . '-' . $parametre . '" class="long" type="text" name="parametres[' . $i . '][' . $parametre . ']" value="' . securiseTexte($contenuParametre) . '" /> <label for="configGraphiqueInput-' . $i . '-' . $parametre . '"><code>' . $parametre . "</code></label></li>\n";
						}
						
						$config .= "</ul></li>\n";
						$config .= "</ul>\n";
						$config .= "</div><!-- /.configGraphiqueListeParametres -->\n";
						
						$corpsGalerie .= '<li class="configGraphiqueListeVignettes">';
						$corpsGalerie .= $vignette;
						$corpsGalerie .= $config;
						
						$corpsGalerie .= '<p class="configGraphiqueSuppressionImage"><input id="configGraphiqueInputSupprimer-' . $i . '" class="long" type="checkbox" name="configGraphiqueInputSupprimer-' . $i . '" value="supprimer" /> <label for="configGraphiqueInputSupprimer-' . $i . '">' . T_("Supprimer") . "</label></p>\n";
						
						$corpsGalerie .= '<p class="configGraphiqueRenommageImage"><label for="configGraphiqueInputRenommer-' . $i . '">' . T_("Renommer:") . '</label> <input id="configGraphiqueInputRenommer-' . $i . '" class="tresLong" type="text" name="configGraphiqueInputRenommer-' . $i . '" value="" />' . "</p>\n";
						
						$corpsGalerie .= '<p class="configGraphiqueLienMaj"><a href="#configGraphiqueMaj">' . T_("Lien vers «Mettre à jour»") . "</a></p>\n";
						$corpsGalerie .= "</li><!-- /.configGraphiqueListeVignettes -->\n";
					}
					
					$corpsGalerie .= "</ul>\n";
					
					$corpsGalerie .= "<div class=\"sep\"></div>\n";
					
					$corpsGalerie .= '<p><input id="configGraphiqueMaj" type="submit" name="configGraphiqueMaj" value="' . T_('Mettre à jour') . '" /></p>' . "\n";
					$corpsGalerie .= "</div>\n";
					$corpsGalerie .= "</form>\n";
					$corpsGalerie .= "</div><!-- /#galeriesAdminConfigGraphique -->\n";
				}
				else
				{
					$messagesScript .= '<p>' . T_("Aucune image dans la galerie.") . "</p>\n";
				}
			}
			
			$messagesScript .= $corpsGalerie;
			
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Mise à jour graphique d'une galerie") . "</h3>\n";

			echo $messagesScript;
			echo "</div><!-- /.sousBoite -->\n";
		}
		
		/* Mise en action. */
		
		if (isset($_POST['configGraphiqueMaj']))
		{
			$messagesScript = '';
			$id = decodeTexte($_POST['configGraphiqueIdGalerie']);
			$idDossier = idGalerieDossier($racine, $id);
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			else
			{
				$contenuFichier = '';
				$contenuFichierAafficher = '';
				
				foreach ($_POST['configGraphiqueVignettes'] as $intermediaireNomEncode)
				{
					$intermediaireNom = decodeTexte($intermediaireNomEncode);
					
					if (isset($_POST['indiceIntermediaireNom'][$intermediaireNomEncode]))
					{
						$i = $_POST['indiceIntermediaireNom'][$intermediaireNomEncode];
					
						if (isset($_POST['configGraphiqueInputSupprimer-' . $i]))
						{
							$imagesAsupprimer = array ();
						
							if (file_exists($cheminGalerie . '/' . $intermediaireNom))
							{
								$imagesAsupprimer[] = $cheminGalerie . '/' . $intermediaireNom;
							}
						
							if (!empty($_POST['parametres'][$i]['originalNom']))
							{
								$nomOriginal = $_POST['parametres'][$i]['originalNom'];
							}
							else
							{
								$nomOriginal = nomSuffixe($intermediaireNom, '-original');
							}
						
							if (file_exists($cheminGalerie . '/' . $nomOriginal))
							{
								$imagesAsupprimer[] = $cheminGalerie . '/' . $nomOriginal;
							}
						
							if (!empty($_POST['parametres'][$i]['vignetteNom']))
							{
								$nomVignette = $_POST['parametres'][$i]['vignetteNom'];
							}
							else
							{
								$nomVignette = nomSuffixe($intermediaireNom, '-vignette');
							}
						
							if (file_exists($cheminGalerie . '/' . $nomVignette))
							{
								$imagesAsupprimer[] = $cheminGalerie . '/' . $nomVignette;
							}
						
							$nomTatouagePrecedent = nomSuffixe($nomVignette, '-precedent');
						
							if (file_exists($cheminGalerie . '/tatouage/' . $nomTatouagePrecedent))
							{
								$imagesAsupprimer[] = $cheminGalerie . '/tatouage/' . $nomTatouagePrecedent;
							}
						
							$nomTatouageSuivant = nomSuffixe($nomVignette, '-suivant');
						
							if (file_exists($cheminGalerie . '/tatouage/' . $nomTatouageSuivant))
							{
								$imagesAsupprimer[] = $cheminGalerie . '/tatouage/' . $nomTatouageSuivant;
							}
						
							foreach ($imagesAsupprimer as $imageAsupprimer)
							{
								$messagesScript .= adminUnlink($imageAsupprimer);
							}
						}
						else
						{
							if (!empty($_POST['configGraphiqueInputRenommer-' . $i]))
							{
								$imagesArenommer = array ();
								$ancienNom = $intermediaireNom;
								$ancienChemin = $cheminGalerie . '/' . $ancienNom;
								$nouveauNom = superBasename($_POST['configGraphiqueInputRenommer-' . $i]);
								$nouveauChemin = $cheminGalerie . '/' . $nouveauNom;
							
								if (file_exists($ancienChemin))
								{
									$imagesArenommer[$ancienChemin] = $nouveauChemin;
								}
							
								if (empty($_POST['parametres'][$i]['originalNom']))
								{
									$ancienCheminOriginal = $cheminGalerie . '/' . nomSuffixe($ancienNom, '-original');
								
									if (file_exists($ancienCheminOriginal))
									{
										$nouveauCheminOriginal = $cheminGalerie . '/' . nomSuffixe($nouveauNom, '-original');
										$imagesArenommer[$ancienCheminOriginal] = $nouveauCheminOriginal;
									}
								}
							
								if (empty($_POST['parametres'][$i]['vignetteNom']))
								{
									$ancienCheminVignette = $cheminGalerie . '/' . nomSuffixe($ancienNom, '-vignette');
								
									if (file_exists($ancienCheminVignette))
									{
										$nouveauCheminVignette = $cheminGalerie . '/' . nomSuffixe($nouveauNom, '-vignette');
										$imagesArenommer[$ancienCheminVignette] = $nouveauCheminVignette;
									}
								
									$ancienCheminTatouagePrecedent = $cheminGalerie . '/tatouage/' . nomSuffixe($ancienNom, '-vignette-precedent');
								
									if (file_exists($ancienCheminTatouagePrecedent))
									{
										$nouveauCheminTatouagePrecedent = $cheminGalerie . '/tatouage/' . nomSuffixe($nouveauNom, '-vignette-precedent');
										$imagesArenommer[$ancienCheminTatouagePrecedent] = $nouveauCheminTatouagePrecedent;
									}
								
									$ancienCheminTatouageSuivant = $cheminGalerie . '/tatouage/' . nomSuffixe($ancienNom, '-vignette-suivant');
								
									if (file_exists($ancienCheminTatouageSuivant))
									{
										$nouveauCheminTatouageSuivant = $cheminGalerie . '/tatouage/' . nomSuffixe($nouveauNom, '-vignette-suivant');
										$imagesArenommer[$ancienCheminTatouageSuivant] = $nouveauCheminTatouageSuivant;
									}
								}
							
								$renommageActif = TRUE;
							
								foreach ($imagesArenommer as $ancienCheminImage => $nouveauCheminImage)
								{
									if (file_exists($nouveauCheminImage))
									{
										$renommageActif = FALSE;
										$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Renommage de %2\$s impossible."), '<code>' . securiseTexte($nouveauCheminImage) . '</code>', '<code>' . securiseTexte($ancienCheminImage) . '</code>') . "</li>\n";
										$messagesScript .= '<li class="erreur">' . sprintf(T_("La demande de renommage de %1\$s a été annulée."), '<code>' . securiseTexte($intermediaireNom) . '</code>') . "</li>\n";
										break;
									}
								}
							
								if ($renommageActif)
								{
									foreach ($imagesArenommer as $ancienCheminImage => $nouveauCheminImage)
									{
										$messagesScript .= adminRename($ancienCheminImage, $nouveauCheminImage);
									}
								
									if (file_exists($nouveauChemin))
									{
										$intermediaireNom = $nouveauNom;
									}
								}
							}
						
							$contenuFichier .= "[$intermediaireNom]\n";
						
							foreach ($_POST['parametres'][$i] as $parametre => $valeur)
							{
								if (!empty($valeur))
								{
									if ($parametre == 'id')
									{
										$valeur = filtreChaine($valeur);
									}
									
									$contenuFichier .= "$parametre=$valeur\n";
								}
							}
						
							$contenuFichier .= "\n";
						}
					}
				}
				
				$contenuFichier = trim($contenuFichier);
				$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
				
				if (file_exists($cheminConfigGalerie))
				{
					if (@file_put_contents($cheminConfigGalerie, $contenuFichier) !== FALSE)
					{
						$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

						$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</p>\n";
					}
					else
					{
						$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</p>\n";
				
						$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
					}
				}
				else
				{
					$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				}

				$messagesScript .= "<div class=\"bDcorps\">\n";
				$messagesScript .= '<pre id="contenuFichierConfigGraphique">' . securiseTexte($contenuFichier) . "</pre>\n";
		
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierConfigGraphique');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</div><!-- /.bDcorps -->\n";
				$messagesScript .= "</li>\n";
			}
			
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Mise à jour graphique d'une galerie"));
		}
		
		########################################################################
		##
		## Mise à jour graphique simplifiée d'une galerie.
		##
		########################################################################
		
		/* Formulaire. */
		
		if ((isset($_POST['listeConfigGraphique']) && $_POST['listeConfigGraphique'] == 'configGraphiqueSimplifiee') || (isset($_GET['action']) && $_GET['action'] == 'configGraphiqueSimplifiee'))
		{
			$messagesScript = '';
			$messagesScript .= '<p>' . T_("<strong>Important:</strong> ne pas oublier de cliquer sur le bouton «Mettre à jour» pour sauvegarder les modifications.") . "</p>\n";
			
			$messagesScript .= '<p><a href="galeries.admin.php?action=configGraphique&amp;id=' . filtreChaine($id) . '#messages">' . T_("Utiliser la version complète du formulaire de mise à jour graphique.") . "</a></p>\n";
			
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);

			$messagesScript .= "<ul>\n";
			$messagesScript .= '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			$messagesScript .= "</ul>\n";
			$corpsGalerie = '';
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</p>\n";
			}
			elseif (!file_exists($cheminConfigGalerie))
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La galerie %1\$s n'a pas de fichier de configuration."), '<code>' . securiseTexte($id) . '</code>') . "</p>\n";
			}
			else
			{
				$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idDossier));
				
				if (!empty($tableauGalerie))
				{
					$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idDossier;
					$nombreDimages = count($tableauGalerie);
					$corpsGalerie .= '<div id="galeriesAdminConfigGraphiqueSimplifiee">';
					$corpsGalerie .= "<form action=\"$adminAction#messages\" method=\"post\">\n";
					$corpsGalerie .= '<div>';
					$corpsGalerie .= '<input type="hidden" name="configGraphiqueSimplifieeIdGalerie" value="' . $id . '" />' . "\n";
					$corpsGalerie .= '<ul class="triable">';
					
					for ($i = 0; $i <= ($nombreDimages - 1) && $i < $nombreDimages; $i++)
					{
						if (gdEstInstallee())
						{
							$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$i]['intermediaireNom']);
							$vignette = image($racine, $urlRacine, dirname($cheminConfigGalerie), $urlRacine . '/site/fichiers/galeries/' . encodeTexte($idDossier), FALSE, $nombreDeColonnes, $tableauGalerie[$i], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, '', $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
							preg_match('|(<img[^>]+/>)|', $vignette, $resultat);
							$vignette = '<div class="configGraphiqueSimplifieeVignette">' . $resultat[1] . "</div><!-- /.configGraphiqueSimplifieeVignette -->\n";
						}
						else
						{
							$vignette = '';
						}
						
						$intermediaireNom = $tableauGalerie[$i]['intermediaireNom'];
						
						$config = '';
						$config .= '<input type="hidden" name="configGraphiqueSimplifieeVignettes[]" value="' . encodeTexte($intermediaireNom) . '" />' . "\n";
						$config .= '<input type="hidden" name="indiceIntermediaireNom[' . encodeTexte($intermediaireNom) . ']" value="' . $i . '" />' . "\n";
						
						$corpsGalerie .= '<li class="configGraphiqueListeVignettes">';
						$corpsGalerie .= $vignette;
						$corpsGalerie .= $config;
						$corpsGalerie .= "</li><!-- /.configGraphiqueListeVignettes -->\n";
					}
					
					$corpsGalerie .= "</ul>\n";
					
					$corpsGalerie .= "<div class=\"sep\"></div>\n";
					
					$corpsGalerie .= '<p><input id="configGraphiqueSimplifieeMaj" type="submit" name="configGraphiqueSimplifieeMaj" value="' . T_('Mettre à jour') . '" /></p>' . "\n";
					$corpsGalerie .= "</div>\n";
					$corpsGalerie .= "</form>\n";
					$corpsGalerie .= "</div><!-- /#galeriesAdminConfigGraphiqueSimplifiee -->\n";
				}
				else
				{
					$messagesScript .= '<p>' . T_("Aucune image dans la galerie.") . "</p>\n";
				}
			}
			
			$messagesScript .= $corpsGalerie;
			
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Mise à jour graphique simplifiée d'une galerie") . "</h3>\n";

			echo $messagesScript;
			echo "</div><!-- /.sousBoite -->\n";
		}
		
		/* Mise en action. */
		
		if (isset($_POST['configGraphiqueSimplifieeMaj']))
		{
			$messagesScript = '';
			$id = decodeTexte($_POST['configGraphiqueSimplifieeIdGalerie']);
			$idDossier = idGalerieDossier($racine, $id);
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			else
			{
				$contenuFichier = '';
				$tableauGalerie = super_parse_ini_file($cheminConfigGalerie, TRUE);
				
				foreach ($_POST['configGraphiqueSimplifieeVignettes'] as $intermediaireNomEncode)
				{
					$intermediaireNom = decodeTexte($intermediaireNomEncode);
					$i = $_POST['indiceIntermediaireNom'][$intermediaireNomEncode];
					
					$contenuFichier .= "[$intermediaireNom]\n";
					
					if (isset($tableauGalerie[$intermediaireNom]))
					{
						foreach ($tableauGalerie[$intermediaireNom] as $parametre => $valeur)
						{
							$contenuFichier .= "$parametre=$valeur\n";
						}
					}
					
					$contenuFichier .= "\n";
				}
				
				$contenuFichier = trim($contenuFichier);
				$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
				
				if (file_exists($cheminConfigGalerie))
				{
					if (@file_put_contents($cheminConfigGalerie, $contenuFichier) !== FALSE)
					{
						$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

						$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</p>\n";
					}
					else
					{
						$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</p>\n";
				
						$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
					}
				}
				else
				{
					$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				}

				$messagesScript .= "<div class=\"bDcorps\">\n";
				$messagesScript .= '<pre id="contenuFichierConfigGraphiqueSimplifiee">' . securiseTexte($contenuFichier) . "</pre>\n";
		
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierConfigGraphiqueSimplifiee');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</div><!-- /.bDcorps -->\n";
				$messagesScript .= "</li>\n";
			}
			
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Mise à jour graphique simplifiée d'une galerie"));
		}
		
		########################################################################
		##
		## Affichage d'un modèle de fichier de configuration.
		##
		########################################################################

		if (isset($_POST['modeleConf']))
		{
			$messagesScript = '';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
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
							$typeMime = typeMime($cheminGalerie . '/' . $fichier);
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
								$listeFichiers .= "$parametre=";
							
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
									$listeFichiers .= 1;
								}
							
								$listeFichiers .= "\n";
							}
						}
					
						$listeFichiers .= "\n";
					}
				
					if (!empty($listeFichiers))
					{
						$messagesScript .= '<li><pre id="listeFichiers">' . securiseTexte($listeFichiers) . "</pre></li>\n";
						$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du dossier %1\$s impossible."), '<code>' . securiseTexte($cheminGalerie) . '</code>') . "</li>\n";
				}
			}
		
			if (empty($messagesScript))
			{
				$messagesScript .= '<li>' . T_("Aucune image dans la galerie.") . "</li>\n";
			}
		
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Modèle de fichier de configuration"));
		}

		########################################################################
		##
		## Création ou mise à jour automatique d'un fichier de configuration.
		##
		########################################################################

		$sousBoiteFichierConfigDebut = FALSE;

		if (isset($_POST['config']) && in_array('maj', $_POST['config']))
		{
			$messagesScript = '';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
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
			
				$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
		
				if ($cheminConfigGalerie)
				{
					$configExisteAuDepart = TRUE;
				}
				else
				{
					$configExisteAuDepart = FALSE;
					$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier, TRUE);
				}
			
				$parametresNouvellesImages = array ();
			
				if (isset($_POST['ajouter']) && !empty($_POST['parametres']))
				{
					foreach ($_POST['parametres'] as $parametre => $valeur)
					{
						if (!empty($valeur))
						{
							$parametre = trim($parametre);
							$valeur = trim($valeur);
							
							if ($parametre == 'id')
							{
								$valeur = filtreChaine($valeur);
							}
							
							$parametresNouvellesImages[$parametre] = $valeur;
						}
					}
				}
			
				if (adminMajConfigGalerie($racine, $idDossier, '', TRUE, $exclureMotifsCommeIntermediaires, FALSE, $parametresNouvellesImages))
				{
					if ($configExisteAuDepart)
					{
						$messagesScript .= '<li>' . sprintf(T_("Mise à jour automatique du fichier de configuration %1\$s effectuée."), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</li>\n";
					}
					else
					{
						$messagesScript .= '<li>' . sprintf(T_("Création du fichier de configuration %1\$s effectuée."), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</li>\n";
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de la création ou de la mise à jour automatique du fichier de configuration %1\$s. Veuillez vérifier manuellement son contenu."), '<code>' . securiseTexte($cheminConfigGalerie) . '</code>') . "</li>\n";
				}
			}
		
			$sousBoiteFichierConfigDebut = TRUE;
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
		
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
		
			echo '<h4>' . T_("Actions effectuées") . "</h4>\n" ;
		
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
		}
	
		if ((isset($_POST['modeleConf']) || (isset($_POST['config']) && in_array('maj', $_POST['config']))) && cheminConfigGalerie($racine, $idDossier))
		{
			if (!$sousBoiteFichierConfigDebut)
			{
				$sousBoiteFichierConfigDebut = TRUE;
				echo '<div class="sousBoite">' . "\n";
				echo '<h3>' . T_("Fichier de configuration") . "</h3>\n";
			}
		
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
		
			echo '<h4>' . T_("Information") . "</h4>\n" ;
		
			echo "<ul>\n";

			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie:");
			echo "<ul>\n";
			echo '<li><a href="galeries.admin.php?action=configGraphique&amp;id=' . filtreChaine($id) . '#messages">' . T_("Modifier graphiquement le fichier de configuration.") . "</a></li>\n";
			echo '<li><a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte('../site/fichiers/galeries/' . $idDossier . '/' . superBasename($cheminConfigGalerie)) . '&amp;dossierCourant=' . encodeTexte('../site/fichiers/galeries/' . $idDossier) . '#messages">' . T_("Modifier manuellement le fichier de configuration dans le porte-documents.") . "</a></li>\n";
			echo "</ul></li>\n";
		
			echo "</ul>\n";
		}
	
		if ($sousBoiteFichierConfigDebut)
		{
			echo "</div><!-- /.sousBoite -->\n";
		}
		
		########################################################################
		##
		## Mise en ligne d'une nouvelle galerie.
		##
		########################################################################
		
		if ($ajoutNouvelleGalerie)
		{
			$messagesScript = '';
			$actionValide = FALSE;
			$urlNouvelleGalerie = '';
			
			if (!empty($_POST['urlNouvelleGalerie']))
			{
				$urlNouvelleGalerie = supprimeUrlRacine($urlRacine, $_POST['urlNouvelleGalerie']);
			}
			
			if (!empty($urlNouvelleGalerie))
			{
				$page = superBasename(decodeTexte($urlNouvelleGalerie));
				$cheminPage = '../' . dirname(decodeTexte($urlNouvelleGalerie));
				
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
			}
			else
			{
				$page = 'galerie.php?id=' . filtreChaine($id) . '&amp;langue={LANGUE}' ;
				$cheminPage = '..';
			}
			
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			elseif (!adminEmplacementPermis($cheminPage, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la création d'une page web de galerie (%1\$s) n'est pas gérable par le porte-documents."), '<code>' . securiseTexte($cheminPage) . '</code>') . "</li>\n";
			}
			else
			{
				$urlRelativeGalerie = substr($cheminPage . '/' . $page, 3);
				$urlGalerie = urlGalerie(1, '', $urlRacine, $urlRelativeGalerie, LANGUE_ADMIN);
				$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
				
				if (!$cheminConfigGalerie)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'a pas de fichier de configuration."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
				}
				else
				{
					$cheminConfigGaleries = cheminConfigGaleries($racine, TRUE);
					
					if (!empty($_POST['mettreEnLigneRss']))
					{
						$rssGalerie = securiseTexte($_POST['mettreEnLigneRss']);
					}
					else
					{
						$rssGalerie = 0;
					}
					
					if (adminMajConfigGaleries($racine, array ($id => array ('dossier' => $idDossier, 'url' => $urlRelativeGalerie, 'rss' => $rssGalerie))))
					{
						$messagesScript .= '<li>' . sprintf(T_("Ajout de la galerie %1\$s dans le fichier de configuration des galeries %2\$s effectué."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de l'ajout de la galerie %1\$s dans le fichier de configuration des galeries %2\$s."), '<code>' . securiseTexte($id) . '</code>', '<code>' . securiseTexte($cheminConfigGaleries) . '</code>') . "</li>\n";
					}
					
					if (!empty($urlNouvelleGalerie))
					{
						if (!file_exists($cheminPage))
						{
							$messagesScript .= adminMkdir($cheminPage, octdec(755), TRUE);
						}
						
						if (file_exists($cheminPage . '/' . $page))
						{
							$actionValide = TRUE;
							$messagesScript .= '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href=\"%2\$s\">éditer le fichier</a> ou <a href=\"%3\$s\">visiter la page</a>."), '<code>' . securiseTexte($cheminPage . '/' . $page) . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminPage . '/' . $page) . '&amp;dossierCourant=' . encodeTexte($cheminPage) . '#messages', $urlGalerie) . "</li>\n";
						}
						elseif ($fic = @fopen($cheminPage . '/' . $page, 'a'))
						{
							$actionValide = TRUE;
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$contenu .= '$idGalerie = \'' . str_replace("'", "\'", $id) . "';\n";
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
							fclose($fic);
							$messagesScript .= '<li>' . sprintf(T_("Le modèle de page %1\$s a été créé. Vous pouvez <a href=\"%2\$s\">éditer le fichier</a> ou <a href=\"%3\$s\">visiter la page</a>."), "<code>$cheminPage/$page</code>", 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminPage . '/' . $page) . '&amp;dossierCourant=' . encodeTexte($cheminPage) . '#messages', $urlGalerie) . "</li>\n";
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminPage . '/' . $page) . '</code>') . "</li>\n";
						}
					}
					else
					{
						$actionValide = TRUE;
						$messagesScript .= '<li>' . sprintf(T_("La galerie est accessible sur la <a href=\"%1\$s\">page Web globale des galeries</a>."), $urlGalerie) . "</li>\n";
					}
				}
			}
			
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Mise en ligne d'une nouvelle galerie"));
		}
		
		########################################################################
		##
		## Sauvegarde d'une galerie.
		##
		########################################################################

		if (isset($_POST['sauvegarder']))
		{
			$messagesScript = '';
			$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
			
			if (empty($id))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucune galerie sélectionnée.") . "</li>\n";
			}
			elseif (!file_exists($cheminGalerie))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La galerie %1\$s n'existe pas."), '<code>' . securiseTexte($id) . '</code>') . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li><a href="telecharger.admin.php?fichier=' . encodeTexte($cheminGalerie) . '&amp;action=date">' . sprintf(T_("Cliquer sur ce lien pour obtenir une copie de sauvegarde de la galerie %1\$s."), '<code>' . securiseTexte($id) . '</code>') . "</a></li>\n";
			}
		
			$messagesScript = '<li>' . sprintf(T_("Galerie sélectionnée: %1\$s"), '<code>' . securiseTexte($id) . '</code>') . "</li>\n" . $messagesScript;
			echo adminMessagesScript($messagesScript, T_("Sauvegarde d'une galerie"));
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
				<div class="aideAdminGaleries aide">
					<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
					
					<div class="bDcorps">
						<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes. Si la galerie a un fichier de configuration, un lien vous permettra de modifier ce dernier."); ?></p>
					</div><!-- .bDcorps -->
				</div><!-- .aideAdminGaleries -->
				<p><input type="submit" name="lister" value="<?php echo T_('Lister les galeries'); ?>" /></p>
			</div>
		</form>
	</div><!-- /.boite -->

	<!-- .boite -->

	<div class="boite">
		<h2 id="ajouter"><?php echo T_("Ajouter des images"); ?></h2>
		
		<div class="aideAdminGaleries aide">
			<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
			
			<div class="bDcorps">
				<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive (<code>.tar</code>, <code>.tar.bz2</code>, <code>.tar.gz</code> ou <code>.zip</code>). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier."); ?></p>
				
				<p><?php echo T_("Vous pouvez également ajouter une seule image en choisissant un fichier image au lieu d'une archive."); ?></p>
				
				<p><?php echo T_("Prendre note que si vous précisez l'URL d'une nouvelle galerie et que cette URL contient des caractères spéciaux, elle devra être fournie sous forme encodée. Le plus simple est de copier l'adresse dans la barre de navigation du navigateur utilisé et de coller le résultat dans le champ de saisie. L'URL racine sera automatiquement supprimée pour convertir l'adresse fournie en adresse relative."); ?></p>
				
				<p><?php echo T_("Voici un exemple:"); ?></p>
				
				<p><code>http://www.monsite.ext/animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></p>
				
				<p><?php echo T_("Le résultat sera semblable à ci-dessous:"); ?></p>
				
				<p><code>url=animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></p>
			</div><!-- .bDcorps -->
		</div><!-- .aideAdminGaleries -->
		
		<p><?php printf(T_("<strong>Taille maximale d'un transfert de fichier:</strong> %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichier), $tailleMaxFichier); ?></p>
		
		<form action="<?php echo $adminAction; ?>#messages" method="post" enctype="multipart/form-data">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
					
					<p><?php printf(T_("<label for=\"%1\$s\">Identifiant de la galerie</label> ou <label for=\"%2\$s\">création d'une nouvelle galerie</label>:"), "ajouterSelectId", "ajouterInputIdNouvelleGalerie"); ?><br />
					<select id="ajouterSelectId" name="id">
						<option value="nouvelleGalerie"><?php echo T_("Nouvelle galerie:"); ?></option>
						<?php $listeGaleries = listeGaleries($racine); ?>
						
						<?php if (!empty($listeGaleries)): ?>
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select> <input id="ajouterInputIdNouvelleGalerie" class="long" type="text" name="idNouvelleGalerie" /></p>
					
					<fieldset id="optionsNouvelleGalerieAdminGaleries">
						<legend class="bDtitre"><?php echo T_("Nouvelle galerie"); ?></legend>
						
						<div class="bDcorps afficher">
							<p><label for="nouvelleGalerieDossier"><?php echo T_("Si nouvelle galerie, nom du dossier (laisser vide pour génération automatique):"); ?></label><br />
							<input id="nouvelleGalerieDossier" class="long" type="text" name="idNouvelleGalerieDossier" /></p>
					
							<p><label for="mettreEnLigneInputUrl"><?php echo T_("Si nouvelle galerie, URL relative de la page Web (laisser vide pour génération automatique):"); ?></label><br />
							<input id="mettreEnLigneInputUrl" class="long" type="text" name="urlNouvelleGalerie" /></p>
							
							<p><label for="nouvelleGalerieRss"><?php echo T_("Si nouvelle galerie, RSS:"); ?></label><br />
							<select id="nouvelleGalerieRss" name="mettreEnLigneRss">
								<option value="1" selected="selected"><?php echo T_("Activé"); ?></option>
								<option value="0"><?php echo T_("Désactivé"); ?></option>
							</select></p>
						</div><!-- /.bDcorps -->
					</fieldset>
					
					<p><label for="ajouterInputFichier"><?php echo T_("Fichier (archive <code>.tar</code>, <code>.tar.bz2</code>, <code>.tar.gz</code> ou <code>.zip</code>, ou fichier image unique):"); ?></label><br />
					<input id="ajouterInputFichier" type="file" name="fichier" size="25"/></p>
					
					<fieldset class="optionsAvanceesAdminGaleries">
						<legend class="bDtitre"><?php echo T_("Options avancées"); ?></legend>
						
						<div class="bDcorps">
							<ul>
								<li><input id="ajouterInputFiltrerNom" type="checkbox" name="filtrerNom[]" value="filtrer" /> <label for="ajouterInputFiltrerNom"><?php printf(T_("Filtrer le nom de chaque image. Le filtre convertit automatiquement les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e») et ensuite les caractères différents de %1\$s par un tiret."), '<code>a-zA-Z0-9.-_+</code>'); ?></label>
								<ul>
									<li><input id="ajouterInputFiltrerCasse" type="checkbox" name="filtrerNom[]" value="min" /> <label for="ajouterInputFiltrerCasse"><?php echo T_("Filtrer également les majuscules en minuscules."); ?></label></li>
								</ul></li>
							</ul>
				
							<?php if ($rotationSansPerteActivee || $suppressionExifActivee): ?>
								<ul>
									<li><?php echo T_("S'applique aux fichiers JPG:"); ?>
									<ul>
										<?php if ($rotationSansPerteActivee): ?>
											<li><input id="ajouterInputRotationAuto" type="checkbox" name="rotationAuto" value="rotation" checked="checked" /> <label for="ajouterInputRotationAuto"><?php echo T_("Tenter d'effectuer une rotation automatique et sans perte de qualité, basée sur l'orientation déclarée dans les données Exif, si cette information existe."); ?></label></li>
										<?php endif; ?>
							
										<?php if ($suppressionExifActivee): ?>
											<li><input id="ajouterInputSuppressionExif" type="checkbox" name="suppressionExif" value="suppression" /> <label for="ajouterInputSuppressionExif"><?php echo T_("Supprimer sans perte de qualité les données Exif, si elles existent."); ?></label></li>
										<?php endif; ?>
									</ul></li>
								</ul>
							<?php endif; ?>
						</div><!-- /.bDcorps -->
					</fieldset>
				</fieldset>

				<fieldset class="fichierConfigAdminGaleries">
					<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
					<div class="bDcorps">
						<ul>
							<li><input id="ajouterInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="ajouterInputConfig"><?php echo T_("Créer ou mettre à jour automatiquement le fichier de configuration de cette galerie."); ?></label>
							<ul>
								<li><input id="ajouterInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="ajouterInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
							</ul></li>
						</ul>
				
						<fieldset id="ajoutParametresAdminGaleries">
							<legend class="bDtitre"><?php echo T_("Paramètres"); ?></legend>
					
							<div class="bDcorps afficher">
								<p><?php echo T_("Ajouter les paramètres suivants pour chaque image (un paramètre vide ne sera pas ajouté):"); ?></p>
					
								<ul>
									<?php foreach ($tableauParametres[0] as $parametre): ?>
										<li><input id="ajouterInputParametres-<?php echo $parametre; ?>" class="long" type="text" name="parametres[<?php echo $parametre; ?>]" value="" /> <label for="ajouterInputParametres-<?php echo $parametre; ?>"><?php echo "<code>$parametre</code>"; ?></label></li>
									<?php endforeach; ?>
									
									<li class="autresParametres"><span class="bDtitre"><?php echo T_("Afficher plus de paramètres"); ?></span>
									<ul class="bDcorps">
										<?php foreach ($tableauParametres[1] as $parametre): ?>
											<li><input id="ajouterInputParametres-<?php echo $parametre; ?>" class="long" type="text" name="parametres[<?php echo $parametre; ?>]" value="" /> <label for="ajouterInputParametres-<?php echo $parametre; ?>"><?php echo "<code>$parametre</code>"; ?></label></li>
										<?php endforeach; ?>
									</ul></li>
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
		
		<?php if (gdEstInstallee()): ?>
			<div class="aideAdminGaleries aide">
				<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
			
				<div class="bDcorps">
					<p><?php echo T_("Vous pouvez générer automatiquement une copie réduite (qui sera utilisée comme étant la version intermédiaire dans la galerie) de chaque image originale. Aucune image au format original ne sera modifiée."); ?></p>
				</div><!-- .bDcorps -->
			</div><!-- .aideAdminGaleries -->

			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<fieldset>
						<legend><?php echo T_("Options"); ?></legend>
				
						<p><label for="redimensionnerSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
						<?php $listeGaleries = listeGaleries($racine); ?>
				
						<?php if (!empty($listeGaleries)): ?>
							<select id="redimensionnerSelectId" name="id">
								<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
									<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else: ?>
							<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
						<?php endif; ?>
						</p>
			
						<p><?php printf(T_("Taille maximale de la version intermédiaire (<label for=\"%1\$s\">largeur</label> × <label for=\"%2\$s\">hauteur</label>):"), "redimensionnerInputLargeur", "redimensionnerInputHauteur"); ?><br />
						<?php echo T_("La plus grande taille possible contenable dans les dimensions données sera utilisée, sans toutefois dépasser la taille originale. Si une seule dimension est précisée, l'autre sera calculée à partir de la dimension donnée ainsi que des dimensions de l'image source. Les proportions de l'image sont conservées. Au moins une dimension doit être donnée."); ?><br />
						<?php
						if (!empty($imageIntermediaireDimensionsVoulues['largeur']))
						{
							$valeurLargeur = $imageIntermediaireDimensionsVoulues['largeur'];
						}
						elseif (!empty($adminTailleParDefautRedimensionnement['largeur']))
						{
							$valeurLargeur = $adminTailleParDefautRedimensionnement['largeur'];
						}
						else
						{
							$valeurLargeur = 500;
						}
						
						if (!empty($imageIntermediaireDimensionsVoulues['hauteur']))
						{
							$valeurHauteur = $imageIntermediaireDimensionsVoulues['hauteur'];
						}
						elseif (!empty($adminTailleParDefautRedimensionnement['hauteur']))
						{
							$valeurHauteur = $adminTailleParDefautRedimensionnement['hauteur'];
						}
						else
						{
							$valeurHauteur = 500;
						}
						?>
						<input id="redimensionnerInputLargeur" type="text" name="largeur" size="4" value="<?php echo $valeurLargeur; ?>" /> <?php echo T_("px de largeur"); ?> <?php echo T_("×"); ?> <input id="redimensionnerInputHauteur" type="text" name="hauteur" size="4" value="<?php echo $valeurHauteur; ?>" /> <?php echo T_("px de hauteur"); ?></p>
						
						<fieldset class="optionsAvanceesAdminGaleries">
							<legend class="bDtitre"><?php echo T_("Options avancées"); ?></legend>
							
							<div class="bDcorps">
								<p><label for="redimensionnerInputQualiteJpg"><?php echo T_("S'il y a lieu, qualité des images JPG générées (0-100):"); ?></label><br />
								<input id="redimensionnerInputQualiteJpg" type="text" name="qualiteJpg" value="<?php echo $galerieQualiteJpg; ?>" size="2" /></p>

								<ul>
									<li><input id="redimensionnerInputNettete" type="checkbox" name="nettete" value="renforcerNettete" /> <label for="redimensionnerInputNettete"><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)."); ?></label>
									<ul>
										<li><label for="redimensionnerInputNetteteGain"><?php echo T_("Gain:"); ?></label> <input id="redimensionnerInputNetteteGain" type="text" name="netteteGain" size="4" value="100" /></li>
										<li><label for="redimensionnerInputNetteteRayon"><?php echo T_("Rayon:"); ?></label> <input id="redimensionnerInputNetteteRayon" type="text" name="netteteRayon" size="4" value="1" /></li>
										<li><label for="redimensionnerInputNetteteSeuil"><?php echo T_("Seuil:"); ?></label> <input id="redimensionnerInputNetteteSeuil" type="text" name="netteteSeuil" size="4" value="3" /></li>
									</ul></li>
								</ul>
				
								<p><?php echo T_("La liste des images originales redimensionnables est consitituée des images dont le nom satisfait le motif <code>nom-original.extension</code>. Voici des options relatives à cette liste:"); ?></p>
								<ul>
									<li><input id="redimensionnerInputRenommer" type="checkbox" name="redimensionnerRenommer[]" value="renommer" checked="checked" /> <label for="redimensionnerInputRenommer"><?php echo T_("Renommer préalablement les images de la galerie en <code>nom-original.extension</code>."); ?></label></li>
					
									<li><input id="redimensionnerInputNePasRenommerMotifs" type="checkbox" name="redimensionnerRenommer[]" value="nePasRenommerMotifs" checked="checked" /> <label for="redimensionnerInputNePasRenommerMotifs"><?php echo T_("S'il y a lieu, ignorer lors du renommage les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>."); ?></label></li>
					
									<li><input id="redimensionnerInputAnalyserConfig" type="checkbox" name="redimensionnerRenommer[]" value="analyserConfig" /> <label for="redimensionnerInputAnalyserConfig"><?php echo T_("Ignorer lors du renommage (s'il y a lieu) ainsi que lors du redimensionnement les images déclarées dans le fichier de configuration (s'il existe). Toute image déjà présente comme titre de section ou comme valeur d'un des paramètres <code>vignetteNom</code> ou <code>originalNom</code> du fichier de configuration est nécessairement une version intermédiaire ou a nécessairement une version intermédiaire associée."); ?></label></li>
								</ul>
				
								<p><?php echo T_("Dans tous les cas, il n'y a pas de création d'image intermédiaire si les fichiers <code>nom-original.extension</code> et <code>nom.extension</code> existent déjà tous les deux."); ?></p>
							</div><!-- /.bDcorps -->
						</fieldset>
					</fieldset>

					<fieldset class="fichierConfigAdminGaleries">
						<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
						<ul class="bDcorps">
							<li><input id="redimensionnerInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="redimensionnerInputConfig"><?php echo T_("Créer ou mettre à jour automatiquement le fichier de configuration de cette galerie."); ?></label>
							<ul>
								<li><input id="redimensionnerInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="redimensionnerInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></li>
							</ul></li>
						</ul>
					</fieldset>
			
					<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

					<p><input type="submit" name="redimensionner" value="<?php echo T_('Redimensionner les images originales'); ?>" /></p>
				</div>
			</form>
		<?php else: ?>
			<p><?php echo T_("La bibliothèque GD doit être installée pour pouvoir utiliser cette fonctionnalité."); ?></p>
		<?php endif; ?>
	</div><!-- /.boite -->

	<!-- .boite -->

	<div class="boite">
		<h2 id="supprimer"><?php echo T_("Supprimer des images"); ?></h2>
	
		<div class="aideAdminGaleries aide">
			<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
	
			<div class="bDcorps">
				<p><?php echo T_("La liste des images potentiellement supprimables peut être générée de trois manières différentes:"); ?></p>
		
				<ul>
					<li><?php echo T_("seulement par analyse du fichier de configuration, s'il existe. Les images intermédiaires sont les images déclarées par le paramètre <code>intermediaireNom</code>. Les vignettes sont les images déclarées par le paramètre <code>vignetteNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-vignette.extension</code> (à moins que ces derniers soient déclarés dans le fichier de configuration comme étant une version différente) et pour lesquels une image intermédiaire sans le motif <code>-vignette</code> existe. Les fichiers au format original sont ceux déclarés par le paramètre <code>originalNom</code>, ou les fichiers dont le nom satisfait le motif <code>nom-original.extension</code> (à moins d'une délcaration différente dans le fichier de configuration) et pour lesquels une image intermédiaire sans le motif <code>-original</code> existe;"); ?></li>
	
					<li><?php echo T_("par reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Les vignettes sont les images dont le nom satisfait le motif <code>nom-vignette.extension</code>. Les fichiers au format original sont ceux dont le nom satisfait le motif <code>nom-original.extension</code>. Les images intermédiaires sont les images dont le nom ne satisfait aucun motif (<code>nom-vignette.extension</code> ou <code>nom-original.extension</code>)."); ?></li>
	
					<li><?php echo T_("sans reconnaissance d'un motif dans le nom des fichiers. Le fichier de configuration n'est pas analysé. Toutes les images sont considérées comme étant des images intermédiaires."); ?></li>
				</ul>

				<p><?php echo T_("Tout d'abord, vous pouvez supprimer les vignettes d'une galerie pour forcer leur regénération automatique."); ?></p>

				<p><?php echo T_("Aussi, si la navigation entre les images d'une galerie est réalisée avec des vignettes et si <code>\$galerieNavigationTatouerVignettes</code> vaut <code>TRUE</code>, de nouvelles vignettes de navigation vers les images précédente et suivante sont générées, et contiennent une petite image (par défaut une flèche) au centre. Vous pouvez supprimer ces vignettes de navigation avec tatouage."); ?></p>

				<p><?php echo T_("Vous pouvez également supprimer les images de taille intermédiaires ou au format original."); ?></p>

				<p><?php echo T_("S'il ne reste plus d'images, le fichier de configuration de la galerie ainsi que le dossier de la galerie seront supprimés, et la galerie sera enlevée du fichier de configuration des galeries."); ?></p>
			</div><!-- .bDcorps -->
		</div><!-- .aideAdminGaleries -->
	
		<form action="<?php echo $adminAction; ?>#messages" method="post">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
				
					<p><label for="supprimerSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
					<?php $listeGaleries = listeGaleries($racine); ?>
				
					<?php if (!empty($listeGaleries)): ?>
						<select id="supprimerSelectId" name="id">
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
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
					</ul>
				</fieldset>
			
				<fieldset class="fichierConfigAdminGaleries">
					<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
					<ul class="bDcorps">
						<li><input id="supprimerInputConfig" type="checkbox" name="config[]" value="maj" checked="checked" /> <label for="supprimerInputConfig"><?php echo T_("Créer ou mettre à jour automatiquement le fichier de configuration de cette galerie."); ?></label>
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
		<h2 id="renommer"><?php echo T_("Renommer ou déplacer une galerie"); ?></h2>
		
		<div class="aideAdminGaleries aide">
			<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
			
			<div class="bDcorps">
				<p><?php echo T_("Vous pouvez renommer une galerie ou modifier son dossier ou son URL. S'il s'agit du renommage d'une galerie ayant sa propre page Web, ne pas oublier de modifier la valeur de la variable <code>\$idGalerie</code> dans la page Web en question."); ?></p>
			</div><!-- .bDcorps -->
		</div><!-- .aideAdminGaleries -->

		<form action="<?php echo $adminAction; ?>#messages" method="post">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
					
					<p><?php printf(T_("<label for=\"%1\$s\">Identifiant de la galerie</label>:"), "renommerSelectId"); ?><br />
					<?php $listeGaleries = listeGaleries($racine); ?>
				
					<?php if (!empty($listeGaleries)): ?>
						<select id="renommerSelectId" name="id">
							
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
					<?php endif; ?>
					</p>
					
					<?php if (!empty($listeGaleries)): ?>
						<p><?php printf(T_("<label for=\"%1\$s\">Nouvel identifiant</label>:"), "renommerInputIdNouveauNomGalerie"); ?><br />
						<input id="renommerInputIdNouveauNomGalerie" class="long" type="text" name="idNouveauNomGalerie" />
						</p>
						
						<p><?php printf(T_("<label for=\"%1\$s\">Nouveau nom du dossier</label>:"), "renommerInputIdNouveauNomDossier"); ?><br />
						<input id="renommerInputIdNouveauNomDossier" class="long" type="text" name="idNouveauNomDossier" />
						</p>
						
						<p><?php printf(T_("<label for=\"%1\$s\">Nouvelle URL relative</label>:"), "renommerInputNouvelleUrl"); ?><br />
						<input id="renommerInputNouvelleUrl" class="long" type="text" name="nouvelleUrl" />
						</p>
					<?php endif; ?>
				</fieldset>
			
				<p><input type="submit" name="renommer" value="<?php echo T_('Renommer ou déplacer la galerie'); ?>" /></p>
			</div>
		</form>
	</div><!-- /.boite -->
	
	<!-- .boite -->
	
	<div class="boite">
		<h2 id="configGraphique"><?php echo T_("Mettre à jour graphiquement un fichier de configuration"); ?></h2>
		
		<div class="aideAdminGaleries aide">
			<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
			
			<div class="bDcorps">
				<p><em><?php printf(T_("Note: il est possible de modifier manuellement dans le porte-documents le fichier de configuration d'une galerie. Consulter la <a href=\"%1\$s\">liste des galeries</a> pour obtenir les liens de modification à la main."), 'galeries.admin.php?action=lister#messages'); ?></em></p>
		
				<p><?php echo T_("Vous pouvez modifier la configuration d'une galerie en passant par une interface graphique."); ?></p>
			</div><!-- .bDcorps -->
		</div><!-- .aideAdminGaleries -->
		
		<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<fieldset>
						<legend><?php echo T_("Options"); ?></legend>
				
						<p><label for="configGraphiqueSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
						<?php $listeGaleries = listeGaleries($racine); ?>
					
						<?php if (!empty($listeGaleries)): ?>
							<select id="configGraphiqueSelectId" name="id">
								<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
									<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else: ?>
							<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
						<?php endif; ?>
						</p>
						
						<ul>
							<li><input id="configGraphiqueInputConfigGraphique" type="radio" name="listeConfigGraphique" value="configGraphique" checked="checked" /> <label for="configGraphiqueInputConfigGraphique"><?php echo T_("Configuration graphique complète avec réordonnement par glisser-déposer et modification des paramètres de chaque image."); ?></label></li>
					
							<li><input id="configGraphiqueInputConfigGraphiqueSimplifiee" type="radio" name="listeConfigGraphique" value="configGraphiqueSimplifiee" /> <label for="configGraphiqueInputConfigGraphiqueSimplifiee"><?php echo T_("Configuration graphique simplifiée utile pour réordonner rapidement les images par glisser-déposer."); ?></label></li>
						</ul>
					</fieldset>
					
					<p><input type="submit" name="configGraphique" value="<?php echo T_('Mettre à jour graphiquement'); ?>" /></p>
				</div>
			</form>
	</div><!-- /.boite -->
	
	<!-- .boite -->
	
	<div class="boite">
		<h2 id="configAutomatique"><?php echo T_("Créer ou mettre à jour automatiquement un fichier de configuration"); ?></h2>

		<form action="<?php echo $adminAction; ?>#messages" method="post">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
				
					<p><label for="configSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
					<?php $listeGaleries = listeGaleries($racine); ?>
				
					<?php if (!empty($listeGaleries)): ?>
						<select id="configSelectId" name="id">
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
					<?php endif; ?>
					</p>
				</fieldset>
			
				<fieldset class="fichierConfigAdminGaleries">
					<legend class="bDtitre"><?php echo T_("Fichier de configuration"); ?></legend>
				
					<p class="bDcorps">
					<input type="hidden" name="config[]" value="maj" />
					<input id="configInputConfigExclureMotifsCommeIntermediaires" type="checkbox" name="config[]" value="exclureMotifsCommeIntermediaires" checked="checked" /> <label for="configInputConfigExclureMotifsCommeIntermediaires"><?php echo T_("Ignorer dans la liste des images intermédiaires les images dont le nom satisfait le motif <code>nom-vignette.extension</code> ou <code>nom-original.extension</code>, à moins qu'il y ait une déclaration différente pour ces dernières dans le fichier de configuration, s'il existe."); ?></label></p>
				</fieldset>
			
				<p><input type="submit" name="majConf" value="<?php echo T_('Créer ou mettre à jour automatiquement'); ?>" /></p>
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
					<?php $listeGaleries = listeGaleries($racine); ?>
				
					<?php if (!empty($listeGaleries)): ?>
						<select id="modeleSelectId" name="id">
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<strong><?php echo T_("Veuillez auparavant créer une galerie."); ?></strong>
					<?php endif; ?>
					</p>

					<p><label for="modeleSelectInfo"><?php echo T_("Pour chaque image intermédiaire, ajouter des paramètres:"); ?></label><br />
					<select id="modeleSelectInfo" name="info[]" multiple="multiple" size="4">
						<option value="aucun" selected="selected"><?php echo T_("Aucun"); ?></option>
					
						<?php foreach ($tableauParametres as $parametres): ?>
							<?php foreach ($parametres as $parametre): ?>
								<option value="<?php echo $parametre; ?>"><?php echo $parametre; ?></option>
							<?php endforeach; ?>
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
	
	<!-- .boite -->

	<div class="boite">
		<h2 id="sauvegarder"><?php echo T_("Sauvegarder une galerie"); ?></h2>
		
		<div class="aideAdminGaleries aide">
			<h3 class="bDtitre"><?php echo T_("Aide"); ?></h3>
			
			<div class="bDcorps">
				<p><?php echo T_("Vous pouvez sauvegarder une galerie en choisissant son identifiant ci-dessous."); ?></p>
			</div><!-- .bDcorps -->
		</div><!-- .aideAdminGaleries -->

		<form action="<?php echo $adminAction; ?>#messages" method="post">
			<div>
				<fieldset>
					<legend><?php echo T_("Options"); ?></legend>
			
					<p><label for="sauvegarderSelectId"><?php echo T_("Identifiant de la galerie:"); ?></label><br />
					<?php $listeGaleries = listeGaleries($racine); ?>
				
					<?php if (!empty($listeGaleries)): ?>
						<select id="sauvegarderSelectId" name="id">
							<?php foreach ($listeGaleries as $listeGalerie => $listeGalerieInfos): ?>
								<option value="<?php echo encodeTexte($listeGalerie); ?>"><?php echo securiseTexte($listeGalerie); ?></option>
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
</div><!-- /#contenuPrincipal -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
