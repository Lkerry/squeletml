<?php
/*
Ce fichier génère les variables nécessaires à l'affiche d'une galerie ou d'une page individuelle d'une oeuvre. Aucun code XHTML n'est envoyé au navigateur.
*/

// Liste des oeuvres à afficher.
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
$titreGalerie = '';
$corpsGalerie = '';

// Nombre d'oeuvres dans la galerie.
if (!empty($idGalerie))
{
	$nombreDoeuvres = count($tableauGalerie);
}

// Par défaut, on suppose que l'image demandée n'existe pas. On ajustera plus loin s'il y a lieu.
$imageExiste = FALSE;

########################################################################
##
## Une oeuvre en particulier est demandée.
##
########################################################################

if (!empty($idGalerie) && isset($_GET['oeuvre']))
{
	// On vérifie si l'oeuvre demandée existe. Si oui, `$indice` contient la valeur de son indice dans le tableau de la galerie.
	
	$indice = 0;
	
	foreach($tableauGalerie as $oeuvre)
	{
		// On récupère l'`id` de chaque image.
		$id = idOeuvre($racine, $oeuvre);
		
		if ($id == sansEchappement($_GET['oeuvre']))
		{
			// A: l'image existe.
			
			$imageExiste = TRUE;
			
			if (!empty($tableauGalerie[$indice]['licence']))
			{
				$licence = $tableauGalerie[$indice]['licence'];
			}
			
			$titreOeuvre = titreOeuvre($oeuvre);
			
			// On écrase les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable `$tableauGalerie`, on génère une valeur automatiquement.
			
			if (!empty($tableauGalerie[$indice]['pageIntermediaireBaliseTitle']))
			{
				$baliseTitle = $tableauGalerie[$indice]['pageIntermediaireBaliseTitle'];
			}
			else
			{
				$baliseTitle = sprintf(T_("Oeuvre %1\$s de la galerie %2\$s"), $titreOeuvre, $idGalerie);
			}
			
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
				$description = sprintf(T_("Oeuvre %1\$s en version intermediaire, galerie %2\$s"), $titreOeuvre, $idGalerie) . $baliseTitleComplement;
			}
			
			if ($inclureMotsCles)
			{
				if (!isset($tableauGalerie[$indice]['pageIntermediaireMotsCles']))
				{
					$tableauGalerie[$indice]['pageIntermediaireMotsCles'] = '';
				}
			
				$motsCles = motsCles($tableauGalerie[$indice]['pageIntermediaireMotsCles'], $description);
				$motsCles .= ', ' . $titreOeuvre;
			}
			
			break; // On arrête la recherche de l'image, car elle a été trouvée.
		}
		
		$indice++;
	}
	
	// Si l'oeuvre existe, on génère le code pour l'affichage de la version intermediaire ainsi que du système de navigation dans la galerie.
	if ($imageExiste)
	{
		// Titre de la galerie.
		if ($galerieGenererTitrePages)
		{
			$titreGalerie = '<h1>' . sprintf(T_("Oeuvre %1\$s&nbsp;| Galerie %2\$s"), "<em>$titreOeuvre</em>", "<em>$idGalerie</em>") . "</h1>\n";
		}
		
		// On vérifie si l'oeuvre existe en cache ou si le cache est expiré.
		
		$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-oeuvre-$id-" . LANGUE . '.cache.html');
		
		if ($dureeCache['galerie'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['galerie']))
		{
			$corpsGalerie .= file_get_contents("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$indiceOeuvreEnCours = $indice;
			$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
			// On récupère le code de l'oeuvre demandée en version intermediaire.
			$oeuvreIntermediaire = '<div id="galerieIntermediaire">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'intermediaire', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";

			// On recherche l'oeuvre précédente pour la navigation (si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente), et on récupère son code.
			if (array_key_exists($indice - 1, $tableauGalerie))
			{
				$indiceOeuvrePrecedente = $indice - 1; // `$indiceOeuvrePrecedente` contient l'indice de l'oeuvre précédente.
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceOeuvrePrecedente]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$oeuvrePrecedente = '<div class="galerieNavigationPrecedent">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceOeuvrePrecedente], $typeMime, 'vignette', 'precedent', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
				// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
				if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
				{
					$oeuvrePrecedente = vignetteTatouee($oeuvrePrecedente, 'precedent', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				}
				// Sinon si une flèche est souhaitée à côté des vignettes, on ajoute une image.
				elseif ($galerieNavigationAccompagnerVignettes && $galerieNavigation == 'vignettes')
				{
					$oeuvrePrecedente = vignetteAccompagnee($oeuvrePrecedente, 'precedent', $racine, $urlRacine);
				}
			}
			else
			{
				$oeuvrePrecedente = '';
			}

			// On recherche l'oeuvre suivante pour la navigation (si l'oeuvre demandée est la dernière, il n'y a pas d'oeuvre suivante), et on récupère son code.
			if (array_key_exists($indice + 1, $tableauGalerie))
			{
				$indiceOeuvreSuivante = $indice + 1; // `$indiceOeuvreSuivante` est l'indice de l'oeuvre suivante.
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indiceOeuvreSuivante]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$oeuvreSuivante = '<div class="galerieNavigationSuivant">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indiceOeuvreSuivante], $typeMime, 'vignette', 'suivant', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
				// Si une flèche de navigation par-dessus la vignette est souhaitée, on modifie l'attribut `src` de l'image.
				if ($galerieNavigationTatouerVignettes && $galerieNavigation == 'vignettes')
				{
					$oeuvreSuivante = vignetteTatouee($oeuvreSuivante, 'suivant', $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				}
				// Sinon si une flèche est souhaitée à côté des vignettes, on ajoute une image.
				elseif ($galerieNavigationAccompagnerVignettes && $galerieNavigation == 'vignettes')
				{
					$oeuvreSuivante = vignetteAccompagnee($oeuvreSuivante, 'suivant', $racine, $urlRacine);
				}
			}
			else
			{
				$oeuvreSuivante = '';
			}
		
			// On simule l'espace de la flèche de navigation pour que l'image demeure centrée dans le cas où on se trouve à une extrémité de la galerie (début ou fin).
			if (empty($oeuvrePrecedente))
			{
				$class = "galerieNavigationPrecedent galerieFleche";
			
				if ($galerieNavigation == 'vignettes')
				{
					$style = styleDivVideNavigation($oeuvreSuivante);
				}
				else
				{
					$class .= " galerieNavigationVide";
					$style = '';
				}
			
				$oeuvrePrecedente = "<div class=\"$class\"$style></div>\n";
			}
		
			if (empty($oeuvreSuivante))
			{
				$class = "galerieNavigationSuivant galerieFleche";
			
				if ($galerieNavigation == 'vignettes')
				{
					$style = styleDivVideNavigation($oeuvrePrecedente);
				}
				else
				{
					$class .= " galerieNavigationVide";
					$style = '';
				}
			
				$oeuvreSuivante = "<div class=\"$class\"$style></div>\n";
			}
		
			// On crée le corps de la galerie.
			if ($galerieNavigationEmplacement == 'haut')
			{
				$corpsGalerie .= $oeuvrePrecedente . $oeuvreSuivante . $oeuvreIntermediaire;
			}
			elseif ($galerieNavigationEmplacement == 'bas')
			{
				$corpsGalerie .= $oeuvreIntermediaire . $oeuvrePrecedente . $oeuvreSuivante . "<div class=\"sep\"></div>\n";
			}
		
			// `$galerieInfo`.
			$galerieInfo = '';
		
			if ($galerieInfoAjout)
			{
				$galerieInfo .= '<div id="galerieInfo">' . "\n";
				$galerieInfo .= '<p>' . sprintf(T_("Affichage de l'oeuvre %1\$s sur un total de %2\$s."), $indice + 1, $nombreDoeuvres) . ' <a href="' . $urlSansGet . '">' . T_("Aller à l'accueil de la galerie.") . "</a></p>\n";
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
					$indicePremiereOeuvre = 0;
					$indiceDerniereOeuvre = $nombreDoeuvres - 1;
				}
				else
				{
					$oeuvreCourante = $indice + 1;
				
					if ($galerieMinivignettesNombre >= $nombreDoeuvres)
					{
						$indicePremiereOeuvre = 0;
						$indiceDerniereOeuvre = $nombreDoeuvres - 1;
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
								$indicePremiereOeuvre = $indiceGauche;
							}
							else
							{
								$indicePremiereOeuvre = 0;
								$nombreAdroite += abs($indiceGauche);
							}
						
							$indiceDroit = $indiceMilieu + $nombreAdroite;
						
							if ($indiceDroit <= ($nombreDoeuvres - 1))
							{
								$indiceDerniereOeuvre = $indiceDroit;
							}
							else
							{
								$indiceDerniereOeuvre = $nombreDoeuvres - 1;
								$indicePremiereOeuvre -= $indiceDroit - ($nombreDoeuvres - 1);
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
								$indicePremiereOeuvre = $indiceGauche;
							}
							else
							{
								$indicePremiereOeuvre = 0;
								$nombreAdroite += abs($indiceGauche);
							}
						
							$indiceDroit = $indiceMilieu + $nombreAdroite;
						
							if ($indiceDroit <= ($nombreDoeuvres - 1))
							{
								$indiceDerniereOeuvre = $indiceDroit;
							}
							else
							{
								$indiceDerniereOeuvre = $nombreDoeuvres - 1;
								$indicePremiereOeuvre -= $indiceDroit - ($nombreDoeuvres - 1);
							}
						}
					}
				}
			
				$minivignetteOeuvreEnCours = FALSE;
			
				for ($indice = $indicePremiereOeuvre; $indice <= $indiceDerniereOeuvre && $indice < $nombreDoeuvres; $indice++)
				{
					if ($indice == $indiceOeuvreEnCours)
					{
						$minivignetteOeuvreEnCours = TRUE;
					}
				
					$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					$corpsMinivignettes .= oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, $minivignetteOeuvreEnCours);
					$minivignetteOeuvreEnCours = FALSE;
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
	// Si l'oeuvre n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie dans le but d'éviter le contenu dupliqué.
	else
	{
		$erreur404 = TRUE;
		
		$id = securiseTexte($_GET['oeuvre']);
		
		// Titre de la galerie.
		if ($galerieGenererTitrePages)
		{
			$titreGalerie = '<h1>' . sprintf(T_("Oeuvre %1\$s&nbsp;| Galerie %2\$s"), "<em>$id</em>", "<em>$idGalerie</em>") . "</h1>\n";
		}
		
		$corpsGalerie .= '<p>' . sprintf(T_("L'oeuvre %1\$s est introuvable. <a href=\"%2\$s\">Voir toutes les oeuvres</a>."), "<em>$id</em>", $urlSansGet) . "</p>\n";
		
		// Ajustement des métabalises.
		$baliseTitle = sprintf(T_("L'oeuvre %1\$s est introuvable"), $id);
		$description = $baliseTitle . $baliseTitleComplement;
		$motsCles = motsCles('', $description);
		$motsCles .= ', ' . $id;
		$robots = "noindex, follow, noarchive";
	}
}
########################################################################
##
## Aucune oeuvre en particulier n'est demandée. On affiche donc la galerie, si elle existe.
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
		$description = sprintf(T_("Galerie %1\$s"), $idGalerie);
	}
	
	if ($inclureMotsCles && empty($motsCles))
	{
		$motsCles = motsCles('', $description);
	}
	
	// Titre de la galerie.
	if ($galerieGenererTitrePages)
	{
		$titreGalerie = '<h1>' . sprintf(T_("Galerie %1\$s"), "<em>$idGalerie</em>") . "</h1>\n";
	}
	
	if ($galerieVignettesParPage)
	{
		$pagination = pagination($nombreDoeuvres, $galerieVignettesParPage, $urlSansGet, $baliseTitle, $description);
	
		if ($pagination['estPageDerreur'])
		{
			$erreur404 = TRUE;
		}
		else
		{
			$nombreDePages = $pagination['nombreDePages'];
			$indicePremiereOeuvre = $pagination['indicePremierElement'];
			$indiceDerniereOeuvre = $pagination['indiceDernierElement'];
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
		$indicePremiereOeuvre = 0;
		$indiceDerniereOeuvre = $nombreDoeuvres - 1;
	}

	if ($erreur404)
	{
		$corpsGalerie .= '<p>' . sprintf(T_("La page %1\$s est introuvable."), securiseTexte($_GET['page'])) . "</p>\n";
	
		// Ajustement des métabalises.
		
		$baliseTitle = sprintf(T_("La page %1\$s est introuvable"), securiseTexte($_GET['page']));
		$description = $baliseTitle . $baliseTitleComplement;
	
		if ($inclureMotsCles)
		{
			$motsCles = motsCles('', $description);
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
			for ($indice = $indicePremiereOeuvre; $indice <= $indiceDerniereOeuvre && $indice < $nombreDoeuvres; $indice++)
			{
				$typeMime = typeMime($racineImgSrc . '/' . $tableauGalerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$corpsGalerie .= oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, TRUE, $nombreDeColonnes, $tableauGalerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
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
				$galerieInfo .= '<p>' . sprintf(T_ngettext("Cette galerie contient %1\$s oeuvre", "Cette galerie contient %1\$s oeuvres", $nombreDoeuvres), $nombreDoeuvres) . sprintf(T_ngettext(" (sur %1\$s page).", " (sur %1\$s pages).", $nombreDePages), $nombreDePages);
	
				if ($url != $urlSansGet)
				{
					$galerieInfo .= ' <a href="' . $urlSansGet . '">' . T_("Voir l'accueil de la galerie."). "</a>";
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
## Aucune oeuvre en particulier n'est demandée, et la galerie n'existe pas.
##
########################################################################
else
{
	$erreur404 = TRUE;
	
	// Titre de la galerie.
	if ($galerieGenererTitrePages)
	{
		$titreGalerie = '<h1>' . sprintf(T_("Galerie %1\$s"), "<em>$nomGalerie</em>") . "</h1>\n";
	}
	
	$corpsGalerie .= '<p>' . sprintf(T_("La galerie %1\$s est introuvable."), "<em>$nomGalerie</em>") . "</p>\n";
	
	// Ajustement des métabalises.
	
	$baliseTitle = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie);
	$description = $baliseTitle . $baliseTitleComplement;
	
	if ($inclureMotsCles)
	{
		$motsCles = motsCles('', $description);
		$motsCles .= ', ' . $nomGalerie;
	}
	
	$robots = "noindex, follow, noarchive";
}

$tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $nombreDeColonnes, $blocsArrondisParDefaut, $blocsArrondisSpecifiques, $nombreDeColonnes);
$tableauCorpsGalerie['corpsGalerie'] = $titreGalerie . $tableauCorpsGalerie['corpsGalerie'];

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/galerie.inc.php'))
{
	include_once $racine . '/site/inc/galerie.inc.php';
}
?>
