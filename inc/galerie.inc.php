<?php
/*
Ce fichier génère les variables nécessaires à l'affiche d'une galerie ou d'une page individuelle d'une oeuvre. Aucun code XHTML n'est envoyé au navigateur.
*/

// Nécessaire à la traduction.
phpGettext($racine, LANGUE);

// Liste des oeuvres à afficher.
if ($idGalerie && $idGalerie == 'démo')
{
	// Galerie démo par défaut.
	$galerie = tableauGalerie($racine . '/fichiers/galeries/démo/config.ini.txt', TRUE);
	$urlImgSrc = $urlRacine . '/fichiers/galeries/démo';
	$racineImgSrc = $racine . '/fichiers/galeries/démo';
}
elseif ($idGalerie && cheminConfigGalerie($racine, $idGalerie))
{
	$galerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
	$urlImgSrc = $urlRacine . '/site/fichiers/galeries/' . rawurlencode($idGalerie);
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerie;
}
else
{
	$nomGalerie = $idGalerie;
	$idGalerie = FALSE;
}

// Initialisation du corps de la galerie.
$corpsGalerie = '';

// Nombre d'oeuvres dans la galerie.
if ($idGalerie)
{
	$nombreDoeuvres = count($galerie);
}

// Par défaut, on suppose que l'image demandée n'existe pas. On ajustera plus loin s'il y a lieu.
$imageExiste = FALSE;

########################################################################
##
## Une oeuvre en particulier est demandée.
##
########################################################################

if ($idGalerie && isset($_GET['oeuvre']))
{
	// On vérifie si l'oeuvre demandée existe. Si oui, `$indice` contient la valeur de son indice dans le tableau de la galerie.
	
	$indice = 0;
	
	foreach($galerie as $oeuvre)
	{
		// On récupère l'`id` de chaque image.
		$id = idOeuvre($oeuvre);
		
		if ($id == sansEchappement($_GET['oeuvre']))
		{
			// A: l'image existe.
			
			$imageExiste = TRUE;
			
			// On écrase les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable `$galerie`, on génère une valeur automatiquement.
			
			if (!empty($galerie[$indice]['pageIntermediaireBaliseTitle']))
			{
				$baliseTitle = $galerie[$indice]['pageIntermediaireBaliseTitle'];
			}
			else
			{
				$baliseTitle = sprintf(T_("Oeuvre %1\$s de la galerie %2\$s"), $id, $idGalerie);
			}
			
			if (!empty($galerie[$indice]['pageIntermediaireDescription']))
			{
				$description = $galerie[$indice]['pageIntermediaireDescription'];
			}
			elseif (!empty($galerie[$indice]['legendeIntermediaire']))
			{
				$description = $galerie[$indice]['legendeIntermediaire'];
			}
			else
			{
				$description = sprintf(T_("Oeuvre %1\$s en version intermediaire, galerie %2\$s"), $id, $idGalerie) . ' | ' . $baliseTitleComplement[LANGUE];
			}
			
			if (!isset($galerie[$indice]['pageIntermediaireMotsCles']))
			{
				$galerie[$indice]['pageIntermediaireMotsCles'] = '';
			}
			
			$motsCles = motsCles($galerie[$indice]['pageIntermediaireMotsCles'], $description);
			$motsCles .= ', ' . $id;
			break; // On arrête la recherche de l'image, car elle a été trouvée.
		}
		
		$indice++;
	}

	// Si l'oeuvre existe, on génère le code pour l'affichage de la version intermediaire ainsi que du système de navigation dans la galerie.
	if ($imageExiste)
	{
		$indiceOeuvreEnCours = $indice;
		$typeMime = typeMime($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
		// On récupère le code de l'oeuvre demandée en version intermediaire.
		$oeuvreIntermediaire = '<div id="galerieIntermediaire">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $galerie[$indice], $typeMime, 'intermediaire', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";

		// On recherche l'oeuvre précédente pour la navigation (si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente), et on récupère son code.
		if (array_key_exists($indice - 1, $galerie))
		{
			$indiceOeuvrePrecedente = $indice - 1; // `$indiceOeuvrePrecedente` contient l'indice de l'oeuvre précédente.
			$typeMime = typeMime($racineImgSrc . '/' . $galerie[$indiceOeuvrePrecedente]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
			$oeuvrePrecedente = '<div class="galerieNavigationPrecedent">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $galerie[$indiceOeuvrePrecedente], $typeMime, 'vignette', 'precedent', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
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
		if (array_key_exists($indice + 1, $galerie))
		{
			$indiceOeuvreSuivante = $indice + 1; // `$indiceOeuvreSuivante` est l'indice de l'oeuvre suivante.
			$typeMime = typeMime($racineImgSrc . '/' . $galerie[$indiceOeuvreSuivante]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
			$oeuvreSuivante = '<div class="galerieNavigationSuivant">' . oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $galerie[$indiceOeuvreSuivante], $typeMime, 'vignette', 'suivant', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE) . "</div>\n";
			
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
			$corpsGalerie .= $oeuvreIntermediaire . $oeuvrePrecedente . $oeuvreSuivante;
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
			$corpsMinivignettes .= '<div id="galerieMinivignettes" class="sep">' . "\n";
			
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
				
				$typeMime = typeMime($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$corpsMinivignettes .= oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, FALSE, $nombreDeColonnes, $galerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, FALSE, $minivignetteOeuvreEnCours);
				$minivignetteOeuvreEnCours = FALSE;
			}
			
			$corpsMinivignettes .= '</div><!-- /#galerieMinivignettes -->' . "\n";
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
	}
	// Si l'oeuvre n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie dans le but d'éviter le contenu dupliqué.
	else
	{
		$estPageDerreur = TRUE;
		$id = securiseTexte($_GET['oeuvre']);
		$corpsGalerie .= '<p>' . sprintf(T_("L'oeuvre demandée est introuvable. <a href=\"%1\$s\">Voir toutes les oeuvres</a>."), $urlSansGetSansServeur) . "</p>\n";
		
		// Ajustement des métabalises.
		$baliseTitle = sprintf(T_("L'Oeuvre %1\$s est introuvable"), $id);
		$description = sprintf(T_("L'Oeuvre %1\$s est introuvable"), $id) . ' | ' . $baliseTitleComplement[LANGUE];
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
elseif ($idGalerie)
{
	if ($galerieVignettesParPage)
	{
		// Calcul de la pagination.
		
		$nombreDePages = ceil($nombreDoeuvres / $galerieVignettesParPage);
	
		if (isset($_GET['page']))
		{
			$page = intval($_GET['page']);
			
			if ($page > $nombreDePages)
			{
				$page = $nombreDePages;
			}
			elseif ($page < 1)
			{
				$page = 1;
			}
		}
		else
		{
			$page = 1;
		}
		
		// Ajustement des métabalises.
		if (isset($page) && $page != 1)
		{
			$baliseTitle .= " - Page $page";
			$description .= " - Page $page";
		}
		
		$indicePremiereOeuvre = ($page - 1) * $galerieVignettesParPage;
		$indiceDerniereOeuvre = $indicePremiereOeuvre + $galerieVignettesParPage - 1;
		
		// Construction de la pagination.
		
		$pagination = '';
		$pagination .= '<div class="galeriePagination">' . "\n";
	
		// `$lien` va être utilisée pour construire l'URL de la page précédente ou suivante.
		$lien = $urlSansGet . '?';
	
		// On récupère les variables GET pour les ajouter au lien, sauf `page`.
		if (!empty($_GET))
		{
			foreach ($_GET as $cle => $valeur)
			{
				if ($cle != 'page')
				{
					$lien .= $cle . '=' . $valeur . '&';
				}
			}
		}
	
		if ($page > 1)
		{
			$numeroPagePrecedent = $page - 1;
			
			// Si la page précédente n'est pas la première, on y fait tout simplement un lien.
			if ($numeroPagePrecedent != 1)
			{
				$lienPrecedent = $lien . 'page=' . $numeroPagePrecedent;
			}
			// Sinon on n'ajoute pas de variable GET `page` et on supprime le dernier caractère (`&` ou `?`).
			else
			{
				$lienPrecedent = substr($lien, 0, -1);
			}
			
			$pagination .= '<a href="' . rawurlencode($lienPrecedent) . '">' . T_("Page précédente") . '</a>';
		}
		
		if ($page < $nombreDePages)
		{
			$numeroPageSuivant = $page + 1;
			$lienSuivant = $lien . 'page=' . $numeroPageSuivant;
			
			if (isset($lienPrecedent))
			{
				$pagination .= ' | ';
			}
			
			$pagination .= '<a href="' . rawurlencode($lienSuivant) . '">' . T_("Page suivante") . '</a>';
		}
	
		$pagination .= '</div><!-- /#pagination -->' . "\n";
	}
	else
	{
		$nombreDePages = 1;
		$indicePremiereOeuvre = 0;
		$indiceDerniereOeuvre = count($galerie) - 1;
	}
	
	for ($indice = $indicePremiereOeuvre; $indice <= $indiceDerniereOeuvre && $indice < $nombreDoeuvres; $indice++)
	{
		$typeMime = typeMime($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom'], $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		$corpsGalerie .= oeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, TRUE, $nombreDeColonnes, $galerie[$indice], $typeMime, 'vignette', '', $galerieQualiteJpg, $galerieExifAjout, $galerieExifInfos, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown, $galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger, $galerieAccueilJavascript, $galerieNavigation, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, TRUE, FALSE);
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
		
		if (!preg_match('|' . $urlSansGetSansServeur . '$|', $urlSansGetSansServeur))
		{
			$galerieInfo .= ' <a href="' . $urlSansGetSansServeur . '">' . T_("Voir l'accueil de la galerie."). "</a></p>\n";
		}
		
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
}
########################################################################
##
## Aucune oeuvre en particulier n'est demandée, et la galerie n'existe pas.
##
########################################################################
else
{
	$estPageDerreur = TRUE;
	$corpsGalerie .= '<p>' . T_("La galerie demandée est introuvable.") . "</p>\n";
	
	// Ajustement des métabalises.
	$baliseTitle = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie);
	$description = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie) . ' | ' . $baliseTitleComplement[LANGUE];
	$motsCles = motsCles('', $description);
	$motsCles .= ', ' . $nomGalerie;
	$robots = "noindex, follow, noarchive";
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/galerie.inc.php'))
{
	include_once $racine . '/site/inc/galerie.inc.php';
}
?>
