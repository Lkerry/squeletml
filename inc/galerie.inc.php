<?php
/*
Ce fichier génère les variables nécessaires à l'affiche d'une galerie ou d'une page individuelle d'une image. Aucun code XHTML n'est envoyé au navigateur.
*/

// Tableau de configuration des galeries.
if (!isset($galeries))
{
	$galeries = super_parse_ini_file(cheminConfigGaleries($racine), TRUE);
}

// Dossier.
$idGalerieDossier = idGalerieDossier($racine, $idGalerie);

// RSS.
$rssGalerie = rssGalerieActif($racine, $idGalerie);

// URL.
$urlGalerie = urlGalerie(0, $racine, $urlRacine, $idGalerie, LANGUE);

// Empêcher la duplication de contenu dans les moteurs de recherche.
if (!$pageGlobaleGalerie && (isset($_GET['id']) || isset($_GET['langue'])))
{
	$erreur404 = TRUE;
}

// Liste des images à afficher.
if (!$erreur404 && $idGalerie == 'démo')
{
	// Galerie démo.
	$tableauGalerie = tableauGalerie($racine . '/fichiers/galeries/' . $idGalerieDossier . '/config.ini.txt', TRUE);
	$urlImgSrc = $urlRacine . '/fichiers/galeries/' . encodeTexte($idGalerieDossier);
	$racineImgSrc = $racine . '/fichiers/galeries/' . $idGalerieDossier;
	$ajoutCommentaires = FALSE;
}
elseif (!$erreur404 && !empty($idGalerie) && cheminConfigGalerie($racine, $idGalerieDossier))
{
	$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerieDossier), TRUE);
	$urlImgSrc = $urlRacine . '/site/fichiers/galeries/' . encodeTexte($idGalerieDossier);
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerieDossier;
}
else
{
	$nomGalerie = $idGalerie;
	$idGalerie = '';
	$idGalerieDossier = '';
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
		if (idImage($image) == $_GET['image'])
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
			
			$baliseTitle = sprintf(T_("%1\$s – Galerie %2\$s"), securiseTexte($baliseTitle), securiseTexte($idGalerie));
			
			if (!empty($tableauGalerie[$indice]['pageIntermediaireDescription']))
			{
				$description = securiseTexte($tableauGalerie[$indice]['pageIntermediaireDescription']);
			}
			elseif (!empty($tableauGalerie[$indice]['intermediaireLegende']))
			{
				$description = securiseTexte($tableauGalerie[$indice]['intermediaireLegende']);
			}
			else
			{
				$description = sprintf(T_("Voir l'image %1\$s, classée dans la galerie %2\$s."), securiseTexte($titreImage), securiseTexte($idGalerie)) . $baliseTitleComplement;
			}
			
			if ($inclureMotsCles)
			{
				if (!isset($tableauGalerie[$indice]['pageIntermediaireMotsCles']))
				{
					$tableauGalerie[$indice]['pageIntermediaireMotsCles'] = '';
				}
			
				$motsCles = motsCles(securiseTexte($tableauGalerie[$indice]['pageIntermediaireMotsCles']), $description);
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
				$baliseH1 = securiseTexte($titreImage);
				
				if ($galerieTitreAvecMotGalerie['page-image'])
				{
					$sousTitreGalerie = sprintf(T_("Galerie %1\$s"), '<em>' . securiseTexte($idGalerie) . '</em>');
				}
				else
				{
					$sousTitreGalerie = '<em>' . securiseTexte($idGalerie) . '</em>';
				}
			}
			elseif ($galerieTitreAvecMotGalerie['page-image'])
			{
				$baliseH1 = sprintf(T_("%1\$s – Galerie %2\$s"), '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . securiseTexte($idGalerie) . '</em>');
			}
			else
			{
				$baliseH1 = sprintf(T_("%1\$s – %2\$s"), '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . securiseTexte($idGalerie) . '</em>');
			}

			$titreGalerieGenere = TRUE;
		}
		
		$indiceImageEnCours = $indice;
		$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom']);
	
		// On récupère le code de l'image demandée en version intermediaire.
		$imageIntermediaire = '<div id="galerieIntermediaire">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'intermediaire', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
		
		// Si le bloc de lien vers la page est actif, on génère le code que ce bloc utilisera.
		if ($lienPage && !$erreur404 && !$estPageDerreur && empty($courrielContact))
		{
			$lienPageIntermediaire = $imageIntermediaire;
			$lienPageVignette = image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
		}
		
		// On recherche l'image précédente pour la navigation (si l'image demandée est la première, il n'y a pas d'image précédente), et on récupère son code.
		if (array_key_exists($indice - 1, $tableauGalerie))
		{
			$indiceImagePrecedente = $indice - 1; // `$indiceImagePrecedente` contient l'indice de l'image précédente.
			$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceImagePrecedente]['intermediaireNom']);
			$imagePrecedente = '<div class="galerieNavigationPrecedent">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceImagePrecedente], $typeMime, 'vignette', 'precedent', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
		
			// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
			if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
			{
				$imagePrecedente = vignetteTatouee($imagePrecedente, 'precedent', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg);
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
			$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceImageSuivante]['intermediaireNom']);
			$imageSuivante = '<div class="galerieNavigationSuivant">' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceImageSuivante], $typeMime, 'vignette', 'suivant', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
		
			// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
			if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
			{
				$imageSuivante = vignetteTatouee($imageSuivante, 'suivant', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg);
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
		
		$infoEtMinivignettesEnsemble = FALSE;
		$infoEtMinivignettesEnsembleCodeDebut = '';
		$infoEtMinivignettesEnsembleCodeFin = '';
		
		if ($galerieInfoAjout && $galerieAfficherMinivignettes && (($galerieMinivignettesEmplacement == 'haut' && $galerieInfoEmplacement == 'haut') || ($galerieMinivignettesEmplacement == 'bas' && $galerieInfoEmplacement == 'bas')))
		{
			$infoEtMinivignettesEnsemble = TRUE;
			$infoEtMinivignettesEnsembleCodeDebut .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
			$infoEtMinivignettesEnsembleCodeDebut .= '<div class="galerieInfoEtMinivignettesEnsemble">' . "\n";
			$infoEtMinivignettesEnsembleCodeFin .= "</div><!-- /.galerieInfoEtMinivignettesEnsemble -->\n";
			$infoEtMinivignettesEnsembleCodeFin .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
		}
		
		// `$galerieInfo`.
		$galerieInfo = '';
	
		if ($galerieInfoAjout)
		{
			$galerieInfo .= '<div id="galerieInfo">' . "\n";
			$galerieInfo .= '<p>' . sprintf(T_ngettext("Affichage de l'image %1\$s sur un total de %2\$s image.", "Affichage de l'image %1\$s sur un total de %2\$s images.", $nombreDimages), $indice + 1, $nombreDimages) . ' <a href="' . $urlGalerie . '">' . sprintf(T_("Aller à l'accueil de la galerie %1\$s."), '<em>' . securiseTexte($idGalerie) . '</em>') . "</a></p>\n";
			$galerieInfo .= '</div><!-- /#galerieInfo -->' . "\n";
		}
	
		// `$corpsMinivignettes`.
		$corpsMinivignettes = '';
	
		if ($galerieAfficherMinivignettes)
		{
			if (!$infoEtMinivignettesEnsemble)
			{
				$corpsMinivignettes .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
			}
			
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
			$corpsMinivignettesImages = '';
			
			for ($indice = $indicePremiereImage; $indice <= $indiceDerniereImage && $indice < $nombreDimages; $indice++)
			{
				if ($indice == $indiceImageEnCours)
				{
					$minivignetteImageEnCours = TRUE;
				}
			
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom']);
				$corpsMinivignettesImages .= '<li>' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, $minivignetteImageEnCours) . "</li>\n";
				$minivignetteImageEnCours = FALSE;
			}
			
			if (!empty($corpsMinivignettesImages))
			{
				$corpsMinivignettes .= "<ul class=\"galerieListeImages\">\n";
				$corpsMinivignettes .= $corpsMinivignettesImages;
				$corpsMinivignettes .= "</ul><!-- /.galerieListeImages -->\n</div><!-- /#galerieMinivignettes -->\n";
				
				if (!$infoEtMinivignettesEnsemble)
				{
					$corpsMinivignettes .= '<div class="sepGalerieMinivignettes"></div>' . "\n";
				}
			}
		}
		
		// Variable `$corpsGalerie` finale.
		
		if ($galerieMinivignettesEmplacement == 'haut' && $galerieInfoEmplacement == 'haut')
		{
			$corpsGalerie = $infoEtMinivignettesEnsembleCodeDebut . $galerieInfo . $corpsMinivignettes . $infoEtMinivignettesEnsembleCodeFin . $corpsGalerie;
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
			$corpsGalerie = $corpsGalerie . $infoEtMinivignettesEnsembleCodeDebut . $galerieInfo . $corpsMinivignettes . $infoEtMinivignettesEnsembleCodeFin;
		}
		
		if (!empty($galeries[$idGalerie]['description']))
		{
			$corpsGalerie = descriptionGalerieTableauVersTexte($galeries[$idGalerie]['description']) . $corpsGalerie;
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
				$baliseH1 = securiseTexte($id);

				if ($galerieTitreAvecMotGalerie['page-image'])
				{
					$sousTitreGalerie = sprintf(T_("Galerie %1\$s"), '<em>' . securiseTexte($idGalerie) . '</em>');
				}
				else
				{
					$sousTitreGalerie = '<em>' . securiseTexte($idGalerie) . '</em>';
				}
			}
			elseif ($galerieTitreAvecMotGalerie['page-image'])
			{
				$baliseH1 = sprintf(T_("%1\$s – Galerie %2\$s"), '<em>' . securiseTexte($id) . '</em>', '<em>' . securiseTexte($idGalerie) . '</em>');
			}
			else
			{
				$baliseH1 = sprintf(T_("%1\$s – %2\$s"), '<em>' . securiseTexte($id) . '</em>', '<em>' . securiseTexte($idGalerie) . '</em>');
			}

			$titreGalerieGenere = TRUE;
		}
		
		$corpsGalerie .= '<p>' . sprintf(T_("L'image %1\$s est introuvable. <a href=\"%2\$s\">Voir toutes les images de la galerie %3\$s</a>."), '<em>' . securiseTexte($id) . '</em>', $urlGalerie, '<em>' . securiseTexte($idGalerie) . '</em>') . "</p>\n";
		
		// Ajustement des métabalises.
		
		$baliseTitle = sprintf(T_("L'image %1\$s est introuvable dans la galerie %2\$s"), securiseTexte($id), securiseTexte($idGalerie));
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
		$baliseTitle = sprintf(T_("%1\$s – Toutes les images classées dans la galerie %2\$s"), securiseTexte($idGalerie), securiseTexte($idGalerie));
	}
	
	if (empty($description))
	{
		$description = sprintf(T_("Voir toutes les images de la galerie %1\$s."), securiseTexte($idGalerie));
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
			$baliseH1 = sprintf(T_("Galerie %1\$s"), '<em>' . securiseTexte($idGalerie) . '</em>');
		}
		else
		{
			$baliseH1 = '<em>' . securiseTexte($idGalerie) . '</em>';
		}
		
		$titreGalerieGenere = TRUE;
	}
	
	if ($galerieVignettesParPage)
	{
		$pagination = pagination($racine, $urlRacine, $galerieTypePagination, $galeriePaginationAvecFond, $galeriePaginationArrondie, $nombreDimages, $galerieVignettesParPage, $urlSansGet, $baliseTitle, $description);
	
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
		
		$baliseTitle = sprintf(T_("La page %1\$s est introuvable – Galerie %2\$s"), securiseTexte($_GET['page']), securiseTexte($idGalerie));
		$description = '';
		
		if ($inclureMotsCles)
		{
			$motsCles = '';
		}
		
		$robots = "noindex, follow, noarchive";
	}
	else
	{
		$corpsGalerieImages = '';
		
		for ($indice = $indicePremiereImage; $indice <= $indiceDerniereImage && $indice < $nombreDimages; $indice++)
		{
			$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom']);
			$corpsGalerieImages .= '<li>' . image($racine, $urlRacine, $racineImgSrc, $urlImgSrc, TRUE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieCouleurAlloueeImage, $galerieExifAjout, $galerieExifDonnees, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</li>\n";
		}
		
		$lienSansJavascript = '';
		
		if (!empty($corpsGalerieImages))
		{
			$corpsGalerie .= "<ul class=\"galerieListeImages\">\n";
			$corpsGalerie .= $corpsGalerieImages;
			$corpsGalerie .= "</ul>\n";
			$corpsGalerie .= "<div class=\"sep\"></div>\n";
			
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
				$hrefSansJavascript = variableGet(2, $urlGalerie, 'image', filtreChaine(titreImage($tableauGalerie[$indicePremiereImage]))) . $ancre;
				$lienSansJavascript .= "<a href=\"$hrefSansJavascript\">" . T_("Voir plus d'information pour chaque image (navigation sans fenêtre Javascript).") . "</a>";
				
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
		}
		
		$galerieInfo = '';
		
		if ($galerieInfoAjout)
		{
			$galerieInfo .= '<div id="galerieInfo">' . "\n";
			$galerieInfo .= '<p>' . sprintf(T_ngettext("Cette galerie contient %1\$s image", "Cette galerie contient %1\$s images", $nombreDimages), $nombreDimages) . sprintf(T_ngettext(" (sur %1\$s page).", " (sur %1\$s pages).", $nombreDePages), $nombreDePages);
			
			if (variableGet(0, $url, 'action') != $urlGalerie)
			{
				$galerieInfo .= ' <a href="' . $urlGalerie . '">' . T_("Voir l'accueil de la galerie."). "</a>";
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
		
		if (!empty($galeries[$idGalerie]['description']))
		{
			$corpsGalerie = descriptionGalerieTableauVersTexte($galeries[$idGalerie]['description']) . $corpsGalerie;
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
			$baliseH1 = sprintf(T_("Galerie %1\$s"), '<em>' . securiseTexte($nomGalerie) . '</em>');
		}
		else
		{
			$baliseH1 = '<em>' . securiseTexte($nomGalerie) . '</em>';
		}
		
		$titreGalerieGenere = TRUE;
	}
	
	$corpsGalerie .= '<p>' . sprintf(T_("La galerie %1\$s est introuvable."), '<em>' . securiseTexte($nomGalerie) . '</em>') . "</p>\n";
	
	// Ajustement des métabalises.
	
	$baliseTitle = sprintf(T_("La galerie %1\$s est introuvable"), securiseTexte($nomGalerie));
	$description = '';
	
	if ($inclureMotsCles)
	{
		$motsCles = '';
	}
	
	$robots = "noindex, follow, noarchive";
}

if (!$erreur404 && count($accueil) > 1)
{
	// Versions en d'autres langues.
	foreach ($accueil as $codeLangue => $urlAccueilLangue)
	{
		$balisesLinkScript[] = "$url#hreflang#" . variableGet(1, $url, 'langue', $codeLangue) . "#$codeLangue";
	}
}

$tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $nombreDeColonnes, $blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $nombreDeColonnes);

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/galerie.inc.php'))
{
	include $racine . '/site/inc/galerie.inc.php';
}
?>
