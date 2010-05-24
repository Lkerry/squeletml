<?php
/*
Ce fichier génère les variables nécessaires à l'affiche d'une galerie ou d'une page individuelle d'une image. Aucun code XHTML n'est envoyé au navigateur.
*/

// Liste des images à afficher.
if ($idGalerie == 'démo')
{
	// Galerie démo par défaut.
	$tableauGalerie = tableauGalerie($racine . '/fichiers/galeries/démo/config.ini.txt', TRUE);
	$urlImgSrc = $urlRacine . '/fichiers/galeries/démo';
	$racineImgSrc = $racine . '/fichiers/galeries/démo';
}
elseif (!empty($idGalerie) && cheminConfigGalerie($racine, $idGalerie))
{
	$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
	$urlImgSrc = $urlRacine . '/site/fichiers/galeries/' . rawurlencode($idGalerie);
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerie;
}
else
{
	$nomGalerie = $idGalerie;
	$idGalerie = '';
}

// Initialisation du titre et du corps de la galerie.
$sousTitreGalerie = '';
$corpsGalerie = '';

// Nombre d'images dans la galerie.
if (!empty($idGalerie))
{
	$nombreDimages = count($tableauGalerie);
}

// Par défaut, on suppose que l'image demandée n'existe pas. On ajustera plus loin s'il y a lieu.
$imageExiste = FALSE;

########################################################################
##
## Une image en particulier est demandée.
##
########################################################################

if (!empty($idGalerie) && isset($_GET['image']))
{
	// On vérifie si l'image demandée existe. Si oui, `$indice` contient la valeur de son indice dans le tableau de la galerie.
	
	$indice = 0;
	
	foreach($tableauGalerie as $image)
	{
		// On récupère l'`id` de chaque image.
		$id = idImage($racine, $image);
		
		if ($id == sansEchappement($_GET['image']))
		{
			// A: l'image existe.
			
			$imageExiste = TRUE;
			
			if (!empty($tableauGalerie[$indice]['licence']))
			{
				$licence = $tableauGalerie[$indice]['licence'];
			}
			
			$titreImage = titreImage($image);
			
			// On écrase les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable `$tableauGalerie`, on génère une valeur automatiquement.
			
			if (!empty($tableauGalerie[$indice]['pageIntermediaireBaliseTitle']))
			{
				$baliseTitle = $tableauGalerie[$indice]['pageIntermediaireBaliseTitle'];
			}
			else
			{
				$baliseTitle = $titreImage;
			}
			
			$baliseTitle = sprintf(T_("%1\$s – Galerie %2\$s"), $baliseTitle, $idGalerie);
			
			if (!empty($tableauGalerie[$indice]['pageIntermediaireDescription']))
			{
				$description = $tableauGalerie[$indice]['pageIntermediaireDescription'];
			}
			elseif (!empty($tableauGalerie[$indice]['legendeIntermediaire']))
			{
				$description = $tableauGalerie[$indice]['legendeIntermediaire'];
			}
			else
			{
				$description = sprintf(T_("Voir l'image %1\$s, faisant partie de la galerie %2\$s."), $titreImage, $idGalerie) . $baliseTitleComplement;
			}
			
			if ($inclureMotsCles)
			{
				if (!isset($tableauGalerie[$indice]['pageIntermediaireMotsCles']))
				{
					$tableauGalerie[$indice]['pageIntermediaireMotsCles'] = '';
				}
			
				$motsCles = motsCles($tableauGalerie[$indice]['pageIntermediaireMotsCles'], $description);
			}
			
			break; // On arrête la recherche de l'image, car elle a été trouvée.
		}
		
		$indice++;
	}
	
	// Si l'image existe, on génère le code pour l'affichage de la version intermediaire ainsi que du système de navigation dans la galerie.
	if ($imageExiste)
	{
		// Titre de la galerie.
		if ($galerieGenererTitrePages)
		{
			if ($galerieSeparerTitreImageEtNomGalerie)
			{
				$baliseH1 = $titreImage;

				if ($galerieTitreAvecMotGalerie['page-image'])
				{
					$sousTitreGalerie = sprintf(T_("Galerie %1\$s"), "<em>$idGalerie</em>");
				}
				else
				{
					$sousTitreGalerie = "<em>$idGalerie</em>";
				}
			}
			elseif ($galerieTitreAvecMotGalerie['page-image'])
			{
				$baliseH1 = sprintf(T_("%1\$s – Galerie %2\$s"), "<em>$titreImage</em>", "<em>$idGalerie</em>");
			}
			else
			{
				$baliseH1 = sprintf(T_("%1\$s – %2\$s"), "<em>$titreImage</em>", "<em>$idGalerie</em>");
			}

			$titreGalerieGenere = TRUE;
		}
		
		// On vérifie si l'image existe en cache ou si le cache est expiré.
		
		$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-image-$id-" . LANGUE . '.cache.html');
		
		if ($dureeCache['galerie'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['galerie']))
		{
			$corpsGalerie .= file_get_contents("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$indiceImageEnCours = $indice;
			$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
			// On récupère le code de l'image demandée en version intermediaire.
			$imageIntermediaire = '<div id="galerieIntermediaire">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'intermediaire', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";

			// On recherche l'image précédente pour la navigation (si l'image demandée est la première, il n'y a pas d'image précédente), et on récupère son code.
			if (array_key_exists($indice - 1, $tableauGalerie))
			{
				$indiceImagePrecedente = $indice - 1; // `$indiceImagePrecedente` contient l'indice de l'image précédente.
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceImagePrecedente]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$imagePrecedente = '<div class="galerieNavigationPrecedent">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceImagePrecedente], $typeMime, 'vignette', 'precedent', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
				// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
				if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
				{
					$imagePrecedente = vignetteTatouee($imagePrecedente, 'precedent', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				}
				// Sinon si une flèche est souhaitée à côté des vignettes, on ajoute une image.
				elseif ($galerieNavigationAccompagnerVignettes && $galerieNavigation == 'vignettes')
				{
					$imagePrecedente = vignetteAccompagnee($imagePrecedente, 'precedent', $racine, $urlRacine);
				}
			}
			else
			{
				$imagePrecedente = '';
			}

			// On recherche l'image suivante pour la navigation (si l'image demandée est la dernière, il n'y a pas d'image suivante), et on récupère son code.
			if (array_key_exists($indice + 1, $tableauGalerie))
			{
				$indiceImageSuivante = $indice + 1; // `$indiceImageSuivante` est l'indice de l'image suivante.
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceImageSuivante]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$imageSuivante = '<div class="galerieNavigationSuivant">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceImageSuivante], $typeMime, 'vignette', 'suivant', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
				// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
				if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
				{
					$imageSuivante = vignetteTatouee($imageSuivante, 'suivant', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				}
				// Sinon si une flèche est souhaitée à côté des vignettes, on ajoute une image.
				elseif ($galerieNavigationAccompagnerVignettes && $galerieNavigation == 'vignettes')
				{
					$imageSuivante = vignetteAccompagnee($imageSuivante, 'suivant', $racine, $urlRacine);
				}
			}
			else
			{
				$imageSuivante = '';
			}
		
			// On simule l'espace de la flèche de navigation pour que l'image demeure centrée dans le cas où on se trouve à une extrémité de la galerie (début ou fin).
			if (empty($imagePrecedente))
			{
				$class = "galerieNavigationPrecedent";
			
				if ($galerieNavigation == 'vignettes')
				{
					$class .= " galerieNavigationVideVignette";
				}
				elseif ($galerieNavigation == 'fleches')
				{
					$class .= " galerieNavigationVideFleche";
				}
			
				$imagePrecedente = "<div class=\"$class\"></div>\n";
			}
		
			if (empty($imageSuivante))
			{
				$class = "galerieNavigationSuivant";
			
				if ($galerieNavigation == 'vignettes')
				{
					$class .= " galerieNavigationVideVignette";
				}
				elseif ($galerieNavigation == 'fleches')
				{
					$class .= " galerieNavigationVideFleche";
				}
			
				$imageSuivante = "<div class=\"$class\"></div>\n";
			}
		
			// On crée le corps de la galerie.
			if ($galerieNavigationEmplacement == 'haut')
			{
				$corpsGalerie .= $imagePrecedente . $imageSuivante . $imageIntermediaire;
			}
			elseif ($galerieNavigationEmplacement == 'bas')
			{
				$corpsGalerie .= $imageIntermediaire . $imagePrecedente . $imageSuivante . "<div class=\"sep\"></div>\n";
			}
		
			// `$galerieInfo`.
			$galerieInfo = '';
		
			if ($galerieInfoAjout)
			{
				$galerieInfo .= '<div id="galerieInfo">' . "\n";
				$galerieInfo .= '<p>' . sprintf(T_ngettext("Affichage de l'image %1\$s sur un total de %2\$s image.", "Affichage de l'image %1\$s sur un total de %2\$s images.", $nombreDimages), $indice + 1, $nombreDimages) . ' <a href="' . $urlSansGet . '">' . sprintf(T_("Aller à l'accueil de la galerie %1\$s."), "<em>$idGalerie</em>") . "</a></p>\n";
				$galerieInfo .= '</div><!-- /#galerieInfo -->' . "\n";
			}
		
			// `$corpsMinivignettes`.
			$corpsMinivignettes = '';
		
			if ($galerieAfficherMinivignettes)
			{
				$corpsMinivignettes .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
				$corpsMinivignettes .= '<div id="galerieMinivignettes">' . "\n";
			
				// Calcul des minivignettes à afficher.
				if (!$galerieMinivignettesNombre)
				{
					$indicePremiereImage = 0;
					$indiceDerniereImage = $nombreDimages - 1;
				}
				else
				{
					$imageCourante = $indice + 1;
				
					if ($galerieMinivignettesNombre >= $nombreDimages)
					{
						$indicePremiereImage = 0;
						$indiceDerniereImage = $nombreDimages - 1;
					}
					else
					{
						$indiceMilieu = $indice;
					
						if ($galerieMinivignettesNombre % 2)
						{
							// A: nombre impair.
						
							$nombreAgauche = floor($galerieMinivignettesNombre / 2); // Arrondi à la baisse.
							$nombreAdroite = $nombreAgauche;
						
							$indiceGauche = $indiceMilieu - $nombreAgauche;
						
							if ($indiceGauche >= 0)
							{
								$indicePremiereImage = $indiceGauche;
							}
							else
							{
								$indicePremiereImage = 0;
								$nombreAdroite += abs($indiceGauche);
							}
						
							$indiceDroit = $indiceMilieu + $nombreAdroite;
						
							if ($indiceDroit <= ($nombreDimages - 1))
							{
								$indiceDerniereImage = $indiceDroit;
							}
							else
							{
								$indiceDerniereImage = $nombreDimages - 1;
								$indicePremiereImage -= $indiceDroit - ($nombreDimages - 1);
							}
						}
						else
						{
							// A: nombre pair.
						
							$nombreAgauche = $galerieMinivignettesNombre / 2;
							$nombreAdroite = $nombreAgauche - 1;
							$indiceGauche = $indiceMilieu - $nombreAgauche;
						
							if ($indiceGauche >= 0)
							{
								$indicePremiereImage = $indiceGauche;
							}
							else
							{
								$indicePremiereImage = 0;
								$nombreAdroite += abs($indiceGauche);
							}
						
							$indiceDroit = $indiceMilieu + $nombreAdroite;
						
							if ($indiceDroit <= ($nombreDimages - 1))
							{
								$indiceDerniereImage = $indiceDroit;
							}
							else
							{
								$indiceDerniereImage = $nombreDimages - 1;
								$indicePremiereImage -= $indiceDroit - ($nombreDimages - 1);
							}
						}
					}
				}
			
				$minivignetteImageEnCours = FALSE;
			
				for ($indice = $indicePremiereImage; $indice <= $indiceDerniereImage && $indice < $nombreDimages; $indice++)
				{
					if ($indice == $indiceImageEnCours)
					{
						$minivignetteImageEnCours = TRUE;
					}
				
					$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					$corpsMinivignettes .= image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, $minivignetteImageEnCours);
					$minivignetteImageEnCours = FALSE;
				}
			
				$corpsMinivignettes .= '</div><!-- /#galerieMinivignettes -->' . "\n";
				$corpsMinivignettes .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
			}
		
			// Variable `$corpsGalerie` finale.
			if ($galerieMinivignettesEmplacement == 'haut' && $galerieInfoEmplacement == 'haut')
			{
				$corpsGalerie = $galerieInfo . $corpsMinivignettes . $corpsGalerie;
			}
			elseif ($galerieMinivignettesEmplacement == 'haut' && $galerieInfoEmplacement == 'bas')
			{
				$corpsGalerie = $corpsMinivignettes . $corpsGalerie . $galerieInfo;
			}
			elseif ($galerieMinivignettesEmplacement == 'bas' && $galerieInfoEmplacement == 'haut')
			{
				$corpsGalerie = $galerieInfo . $corpsGalerie . $corpsMinivignettes;
			}
			elseif ($galerieMinivignettesEmplacement == 'bas' && $galerieInfoEmplacement == 'bas')
			{
				$corpsGalerie = $corpsGalerie . $corpsMinivignettes . $galerieInfo;
			}
			
			if ($dureeCache['galerie'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $corpsGalerie);
			}
		}
	}
	// Si l'image n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie dans le but d'éviter le contenu dupliqué.
	else
	{
		$erreur404 = TRUE;
		
		$id = securiseTexte($_GET['image']);
		
		// Titre de la galerie.
		if ($galerieGenererTitrePages)
		{
			if ($galerieSeparerTitreImageEtNomGalerie)
			{
				$baliseH1 = $id;

				if ($galerieTitreAvecMotGalerie['page-image'])
				{
					$sousTitreGalerie = sprintf(T_("Galerie %1\$s"), "<em>$idGalerie</em>");
				}
				else
				{
					$sousTitreGalerie = "<em>$idGalerie</em>";
				}
			}
			elseif ($galerieTitreAvecMotGalerie['page-image'])
			{
				$baliseH1 = sprintf(T_("%1\$s – Galerie %2\$s"), "<em>$id</em>", "<em>$idGalerie</em>");
			}
			else
			{
				$baliseH1 = sprintf(T_("%1\$s – %2\$s"), "<em>$id</em>", "<em>$idGalerie</em>");
			}

			$titreGalerieGenere = TRUE;
		}
		
		$corpsGalerie .= '<p>' . sprintf(T_("L'image %1\$s est introuvable. <a href=\"%2\$s\">Voir toutes les images de la galerie %3\$s</a>."), "<em>$id</em>", $urlSansGet, "<em>$idGalerie</em>") . "</p>\n";
		
		// Ajustement des métabalises.
		
		$baliseTitle = sprintf(T_("L'image %1\$s est introuvable dans la galerie %2\$s"), $id, $idGalerie);
		$description = '';
		
		if ($inclureMotsCles)
		{
			$motsCles = '';
		}
		
		$robots = "noindex, follow, noarchive";
	}
}
########################################################################
##
## Aucune image en particulier n'est demandée. On affiche donc la galerie, si elle existe.
##
########################################################################
elseif (!empty($idGalerie))
{
	// Si aucune valeur n'a été donnée aux balises de l'en-tête de la page, on génère une valeur automatiquement.
	
	if (empty($baliseTitle))
	{
		$baliseTitle = sprintf(T_("Galerie %1\$s"), $idGalerie);
	}
	
	if (empty($description))
	{
		$description = sprintf(T_("Voir toutes les images de la galerie %1\$s."), $idGalerie);
	}
	
	if ($inclureMotsCles && empty($motsCles))
	{
		$motsCles = motsCles('', $description);
	}
	
	// Titre de la galerie.
	if ($galerieGenererTitrePages)
	{
		if ($galerieTitreAvecMotGalerie['accueil'])
		{
			$baliseH1 = sprintf(T_("Galerie %1\$s"), "<em>$idGalerie</em>");
		}
		else
		{
			$baliseH1 = "<em>$idGalerie</em>";
		}
		
		$titreGalerieGenere = TRUE;
	}
	
	if ($galerieVignettesParPage)
	{
		$pagination = pagination($racine, $urlRacine, $galerieTypePagination, $nombreDimages, $galerieVignettesParPage, $urlSansGet, $baliseTitle, $description);
	
		if ($pagination['estPageDerreur'])
		{
			$erreur404 = TRUE;
		}
		else
		{
			$nombreDePages = $pagination['nombreDePages'];
			$indicePremiereImage = $pagination['indicePremierElement'];
			$indiceDerniereImage = $pagination['indiceDernierElement'];
			$baliseTitle = $pagination['baliseTitle'];
			$description = $pagination['description'];
			$pagination = $pagination['pagination'];
		}
	}
	elseif (isset($_GET['page']) && $_GET['page'] != 1)
	{
		$erreur404 = TRUE;
	}
	else
	{
		$nombreDePages = 1;
		$indicePremiereImage = 0;
		$indiceDerniereImage = $nombreDimages - 1;
	}

	if ($erreur404)
	{
		$corpsGalerie .= '<p>' . sprintf(T_("La page %1\$s est introuvable."), securiseTexte($_GET['page'])) . "</p>\n";
	
		// Ajustement des métabalises.
		
		$baliseTitle = sprintf(T_("La page %1\$s est introuvable – Galerie %2\$s"), securiseTexte($_GET['page']), $idGalerie);
		$description = '';
		
		if ($inclureMotsCles)
		{
			$motsCles = '';
		}
		
		$robots = "noindex, follow, noarchive";
	}
	else
	{
		if (!empty($_GET['page']))
		{
			$getPage = securiseTexte($_GET['page']);
		}
		else
		{
			$getPage = '1';
		}
		
		$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-$getPage-" . LANGUE . '.cache.html');
	
		if ($dureeCache['galerie'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['galerie']))
		{
			$corpsGalerie .= file_get_contents("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			for ($indice = $indicePremiereImage; $indice <= $indiceDerniereImage && $indice < $nombreDimages; $indice++)
			{
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$corpsGalerie .= image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, TRUE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
			}

			$corpsGalerie .= "<div class=\"sep\"></div>\n";
			$lienSansJavascript = '';
			
			if ($galerieAccueilJavascript && $galerieAccueilLienSansJavascript)
			{
				if ($galerieAccueilLienSansJavascriptEmplacement == 'haut' || $galerieAccueilLienSansJavascriptEmplacement == 'bas')
				{
					$lienSansJavascript .= '<div id="galerieLienSansJavascript">' . "\n";
					$lienSansJavascript .= "<p>";
				}
				elseif ($galerieAccueilLienSansJavascriptEmplacement == 'info')
				{
					$lienSansJavascript .= '<span id="galerieLienSansJavascript">';
				}
				
				$ancre = ancreDeNavigationGalerie($galerieAncreDeNavigation);
				$lienSansJavascript .= "<a href=\"$urlSansGet?image=" . filtreChaine($racine, titreImage($tableauGalerie[$indicePremiereImage])) . $ancre . '">' . T_("Voir plus d'information pour chaque image (navigation sans fenêtre Javascript).") . "</a>";
				
				if ($galerieAccueilLienSansJavascriptEmplacement == 'haut' || $galerieAccueilLienSansJavascriptEmplacement == 'bas')
				{
					$lienSansJavascript .= "</p>\n";
					$lienSansJavascript .= '</div><!-- /#galerieLienSansJavascript -->' . "\n";
				}
				elseif ($galerieAccueilLienSansJavascriptEmplacement == 'info')
				{
					$lienSansJavascript .= '</span>';
				}

				if ($galerieAccueilLienSansJavascriptEmplacement == 'haut')
				{
					$corpsGalerie = $lienSansJavascript . $corpsGalerie;
				}
				elseif ($galerieAccueilLienSansJavascriptEmplacement == 'bas')
				{
					$corpsGalerie .= $lienSansJavascript;
				}
			}
			
			if ($galerieVignettesParPage)
			{
				if ($galeriePagination['au-dessus'])
				{
					$corpsGalerie = $pagination . $corpsGalerie;
				}
	
				if ($galeriePagination['au-dessous'])
				{
					$corpsGalerie .= $pagination;
				}
			}

			$galerieInfo = '';

			if ($galerieInfoAjout)
			{
				$galerieInfo .= '<div id="galerieInfo">' . "\n";
				$galerieInfo .= '<p>' . sprintf(T_ngettext("Cette galerie contient %1\$s image", "Cette galerie contient %1\$s images", $nombreDimages), $nombreDimages) . sprintf(T_ngettext(" (sur %1\$s page).", " (sur %1\$s pages).", $nombreDePages), $nombreDePages);
	
				if ($url != $urlSansGet)
				{
					$galerieInfo .= ' <a href="' . $urlSansGet . '">' . T_("Voir l'accueil de la galerie."). "</a>";
				}

				if (!empty($lienSansJavascript) && $galerieAccueilLienSansJavascriptEmplacement == 'info')
				{
					$galerieInfo .= " $lienSansJavascript";
				}
				
				$galerieInfo .= "</p>\n";
				$galerieInfo .= '</div><!-- /#galerieInfo -->' . "\n";
			}
			
			// Variable `$corpsGalerie` finale.
			if ($galerieInfoEmplacement == 'haut')
			{
				$corpsGalerie = $galerieInfo . $corpsGalerie;
			}
			elseif ($galerieInfoEmplacement == 'bas')
			{
				$corpsGalerie .= $galerieInfo;
			}
		
			if ($dureeCache['galerie'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $corpsGalerie);
			}
		}
	}
}
########################################################################
##
## Aucune image en particulier n'est demandée, et la galerie n'existe pas.
##
########################################################################
else
{
	$erreur404 = TRUE;
	
	// Titre de la galerie.
	if ($galerieGenererTitrePages)
	{
		if ($galerieTitreAvecMotGalerie['accueil'])
		{
			$baliseH1 = sprintf(T_("Galerie %1\$s"), "<em>$nomGalerie</em>");
		}
		else
		{
			$baliseH1 = "<em>$nomGalerie</em>";
		}
		
		$titreGalerieGenere = TRUE;
	}
	
	$corpsGalerie .= '<p>' . sprintf(T_("La galerie %1\$s est introuvable."), "<em>$nomGalerie</em>") . "</p>\n";
	
	// Ajustement des métabalises.
	
	$baliseTitle = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie);
	$description = '';
	
	if ($inclureMotsCles)
	{
		$motsCles = '';
	}
	
	$robots = "noindex, follow, noarchive";
}

$tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $nombreDeColonnes, $blocsArrondisParDefaut, $blocsArrondisSpecifiques, $nombreDeColonnes);

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/galerie.inc.php'))
{
	include_once $racine . '/site/inc/galerie.inc.php';
}
?>
