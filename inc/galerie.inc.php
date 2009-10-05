<?php
// Insertion des fichiers nécessaires
include_once $racine . '/inc/fonctions.inc.php';
if (!isset($idGalerie))
{
	$idGalerie = FALSE;
}
foreach (init($racine, langue($langue), $idGalerie) as $fichier)
{
	include_once $fichier;
}

// Nécessaire à la traduction du module
phpGettext($racine, langue($langue));

// Insertion du tableau contenant la liste des oeuvres à afficher
if ($idGalerie && $idGalerie == 'démo')
{
	// Galerie démo par défaut
	$galerie = construitTableauGalerie($racine . '/fichiers/galeries/démo/config.pc', TRUE);
	$urlImgSrc = $urlRacine . '/fichiers/galeries/démo';
	$racineImgSrc = $racine . '/fichiers/galeries/démo';
}
elseif ($idGalerie && file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.pc'))
{
	$galerie = construitTableauGalerie($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.pc', TRUE);
	$urlImgSrc = $urlRacine . '/site/fichiers/galeries/' . rawurlencode($idGalerie);
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerie;
}
else
{
	$nomGalerie = $idGalerie;
	$idGalerie = FALSE;
}

// Initialisation du corps de la galerie
$corpsGalerie = '';

// Récupération d'informations sur la galerie
if ($idGalerie)
{
	$nombreDoeuvres = count($galerie);
}

// Par défaut, on suppose que l'image demandée n'existe pas
$imageExiste = FALSE;

########################################################################
##
## Une oeuvre en particulier est demandée
##
########################################################################

if ($idGalerie && isset($_GET['oeuvre']))
{
	// On vérifie si l'oeuvre demandée existe. Si oui, $i va contenir la valeur de son indice dans le tableau.
	$i = 0;
	foreach($galerie as $oeuvre)
	{
		// On récupère l'id de chaque image
		$id = idOeuvre($oeuvre);
		
		if ($id == sansEchappement($_GET['oeuvre']))
		{
			$imageExiste = TRUE;
			// L'image existe, et on écrase les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable $galerie, on donne une valeur automatiquement.
			if (!empty($galerie[$i]['pageIntermediaireBaliseTitle']))
			{
				$baliseTitle = $galerie[$i]['pageIntermediaireBaliseTitle'];
			}
			else
			{
				$baliseTitle = sprintf(T_("Oeuvre %1\$s de la galerie %2\$s"), $id, $idGalerie);
			}

			if (!empty($galerie[$i]['pageIntermediaireDescription']))
			{
				$description = $galerie[$i]['pageIntermediaireDescription'];
			}
			elseif (!empty($galerie[$i]['legendeIntermediaire']))
			{
				$description = $galerie[$i]['legendeIntermediaire'];
			}
			else
			{
				$description = sprintf(T_("Oeuvre %1\$s en version intermediaire, galerie %2\$s"), $id, $idGalerie) . ' | ' . $baliseTitleComplement[langue($langue)];
			}
			
			if (!isset($galerie[$i]['pageIntermediaireMotsCles']))
			{
				$galerie[$i]['pageIntermediaireMotsCles'] = '';
			}
			
			$motsCles = construitMotsCles($galerie[$i]['pageIntermediaireMotsCles'], $description);
			$motsCles .= ', ' . $id;

			break; // On arrête la boucle
		}
		
		$i++;
	}

	// Si l'oeuvre existe, on affiche la version intermediaire ainsi que le système de navigation dans la galerie.
	if ($imageExiste)
	{
		$indiceOeuvreIntermediaireEnCours = $i;
		
		// On récupère le code de l'oeuvre demandée en version intermediaire
		$oeuvreIntermediaire = '<div id="galerieIntermediaire">' . afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, FALSE, 'intermediaire', $i, FALSE, 'aucun', $galerieHauteurVignette, $galerieTelechargeOriginal, TRUE, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal) . '</div>' . "\n";

		// On recherche l'oeuvre précédente pour la navigation. Si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente
		if (array_key_exists($i - 1, $galerie))
		{
			$op = $i - 1; // $op est l'indice de l'oeuvre précédente

			// On récupère le code de la vignette de l'oeuvre précédente
			$oeuvrePrecedente = '<div class="galerieNavigationPrecedent">' . afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, FALSE, 'vignette', $op, FALSE, 'precedent', $galerieHauteurVignette, $galerieTelechargeOriginal, TRUE, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal) . '</div>' . "\n";
			
			// Si une flèche de navigation par-dessus la vignette est souhaitée, on change l'attribut `src` de l'image
			if ($galerieNavigationVignettesTatouage && $galerieNavigation == 'vignettes')
			{
				$oeuvrePrecedente = vignetteTatouage($oeuvrePrecedente, 'precedent', $racine, $racineImgSrc, $urlImgSrc, $qualiteJpg);
			}
			
			// Si une flèche est souhaitée à côté des vignettes, on ajoute une image
			elseif ($galerieNavigationVignettesAccompagnees && $galerieNavigation == 'vignettes')
			{
				$oeuvrePrecedente = vignetteAccompagnee($oeuvrePrecedente, 'precedent', $racine, $urlRacine);
			}
		}
		else
		{
			$oeuvrePrecedente = '';
		}

		// On recherche l'oeuvre suivante pour la navigation
		if (array_key_exists($i + 1, $galerie))
		{
			$os = $i + 1;

			// On récupère le code de la vignette de l'oeuvre suivante
			$oeuvreSuivante = '<div class="galerieNavigationSuivant">' . afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, FALSE, 'vignette', $os, FALSE, 'suivant', $galerieHauteurVignette, $galerieTelechargeOriginal, TRUE, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal) . '</div>' . "\n";
			
			// Si une flèche de navigation par-dessus la vignette est souhaitée, on change l'attribut `src` de l'image
			if ($galerieNavigationVignettesTatouage && $galerieNavigation == 'vignettes')
			{
				$oeuvreSuivante = vignetteTatouage($oeuvreSuivante, 'suivant', $racine, $racineImgSrc, $urlImgSrc, $qualiteJpg);
			}
			
			// Si une flèche est souhaitée à côté des vignettes, on ajoute une image
			elseif ($galerieNavigationVignettesAccompagnees && $galerieNavigation == 'vignettes')
			{
				$oeuvreSuivante = vignetteAccompagnee($oeuvreSuivante, 'suivant', $racine, $urlRacine);
			}
		}
		else
		{
			$oeuvreSuivante = '';
		}
		
		// On simule l'espace de la flèche de navigation pour que l'image demeure centrée dans le cas où on se trouve à un extrême de la galerie (début ou fin)
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
			
			$oeuvrePrecedente = '<div class="' . $class . '"' . $style . '></div>' . "\n";
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
			
			$oeuvreSuivante = '<div class="' . $class . '"' . $style . '></div>' . "\n";
		}
		
		// On crée le corps de la galerie
		if ($galerieNavigationEmplacement == 'haut')
		{
			$corpsGalerie .= $oeuvrePrecedente . $oeuvreSuivante . $oeuvreIntermediaire;
		}
		elseif ($galerieNavigationEmplacement == 'bas')
		{
			$corpsGalerie .= $oeuvreIntermediaire . $oeuvrePrecedente . $oeuvreSuivante;
		}
		
		// $galerieInfo
		$galerieInfo = '';
		if ($galerieInfoAjout)
		{
			$galerieInfo .= '<div id="galerieInfo">' . "\n";
			$galerieInfo .= '<p>' . sprintf(T_('Affichage de l\'oeuvre %1$s sur un total de %2$s.'), $i + 1, $nombreDoeuvres) . ' <a href="' . $_SERVER['PHP_SELF'] . '">' . T_("Aller à l'accueil de la galerie.") . '</a></p>' . "\n";
			$galerieInfo .= '</div><!-- /galerieInfo -->' . "\n";
		}
		
		//$corpsMinivignettes
		$corpsMinivignettes = '';
		if ($galerieMinivignettes)
		{
			$corpsMinivignettes .= '<div id="sepGalerieMinivignettes"></div>' . "\n";
			$corpsMinivignettes .= '<div id="galerieMinivignettes">' . "\n";
			
			// Calcul des minivignettes à afficher
			if (!$galerieMinivignettesNombre)
			{
				$indicePremiereOeuvre = 0;
				$indiceDerniereOeuvre = $nombreDoeuvres - 1;
			}
			else
			{
				$oeuvreCourante = $i + 1;
				if ($galerieMinivignettesNombre >= $nombreDoeuvres)
				{
					$indicePremiereOeuvre = 0;
					$indiceDerniereOeuvre = $nombreDoeuvres - 1;
				}
				else
				{
					$idMilieu = $i;
					if ($galerieMinivignettesNombre % 2)
					{
						// A: nombre impair
						$nombreAgauche = floor($galerieMinivignettesNombre / 2); // Arrondi à la baisse
						$nombreAdroite = $nombreAgauche;
						
						$idGauche = $idMilieu - $nombreAgauche;
						if ($idGauche >= 0)
						{
							$indicePremiereOeuvre = $idGauche;
						}
						else
						{
							$indicePremiereOeuvre = 0;
							$nombreAdroite += abs($idGauche);
						}
						
						$idDroite = $idMilieu + $nombreAdroite;
						if ($idDroite <= ($nombreDoeuvres - 1))
						{
							$indiceDerniereOeuvre = $idDroite;
						}
						else
						{
							$indiceDerniereOeuvre = $nombreDoeuvres - 1;
							$indicePremiereOeuvre -= $idDroite - ($nombreDoeuvres - 1);
						}
					}
					
					else
					{
						// A: nombre pair
						$nombreAgauche = $galerieMinivignettesNombre / 2;
						$nombreAdroite = $nombreAgauche - 1;
						
						$idGauche = $idMilieu - $nombreAgauche;
						if ($idGauche >= 0)
						{
							$indicePremiereOeuvre = $idGauche;
						}
						else
						{
							$indicePremiereOeuvre = 0;
							$nombreAdroite += abs($idGauche);
						}
						
						$idDroite = $idMilieu + $nombreAdroite;
						if ($idDroite <= ($nombreDoeuvres - 1))
						{
							$indiceDerniereOeuvre = $idDroite;
						}
						else
						{
							$indiceDerniereOeuvre = $nombreDoeuvres - 1;
							$indicePremiereOeuvre -= $idDroite - ($nombreDoeuvres - 1);
						}
					}
				}
			}
			
			$minivignetteOeuvreEnCours = FALSE;
			
			for ($i = $indicePremiereOeuvre; $i <= $indiceDerniereOeuvre && $i < $nombreDoeuvres; $i++)
			{
				if ($i == $indiceOeuvreIntermediaireEnCours)
				{
					$minivignetteOeuvreEnCours = TRUE;
				}
				
				$corpsMinivignettes .= afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, FALSE, 'vignette', $i, $minivignetteOeuvreEnCours, 'aucun', $galerieHauteurVignette, $galerieTelechargeOriginal, FALSE, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal);
				
				$minivignetteOeuvreEnCours = FALSE;
			}
			
			$corpsMinivignettes .= '</div><!-- /galerieMinivignettes -->' . "\n";
			$corpsMinivignettes .= '<div id="sepGalerieMinivignettes"></div>' . "\n";
		}
		
		// Variable $corpsGalerie finale
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

	// Si l'oeuvre n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie pour éviter le contenu dupliqué.
	else
	{
		$pageDerreur = TRUE;
		$id = securiseTexte($_GET['oeuvre']);
		$corpsGalerie .= '<p>' . sprintf(T_('L\'oeuvre demandée est introuvable. <a href="%1$s">Voir toutes les oeuvres</a>.'), nomFichierGalerie()) . '</p>';
		
		// Ajustement des métabalises
		$baliseTitle = sprintf(T_("L'Oeuvre %1\$s est introuvable"), $id);
		$description = sprintf(T_("L'Oeuvre %1\$s est introuvable"), $id) . ' | ' . $baliseTitleComplement[langue($langue)];
		$motsCles = construitMotsCles('', $description);
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
		// Calcul de la pagination
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
		
		// Ajustement des métabalises
		if (isset($page) && $page != 1)
		{
			$baliseTitle .= " - Page $page";
			$description .= " - Page $page";
		}
		
		$indicePremiereOeuvre = ($page - 1) * $galerieVignettesParPage;
		$indiceDerniereOeuvre = $indicePremiereOeuvre + $galerieVignettesParPage - 1;
		
		// Construction de la pagination
		$pagination = '';
	
		$pagination .= '<div class="galeriePagination">' . "\n";
	
		// $lien va être utilisée pour construire l'URL de la page précédente ou suivante
		$lien = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?';
	
		// On récupère les variables GET pour les ajouter au lien, sauf page
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
			$noPagePrecedent = $page - 1;
			// Si la page précédente n'est pas la première, on y fait tout simplement un lien
			if ($noPagePrecedent != 1)
			{
				$lienPrecedent = $lien . 'page=' . $noPagePrecedent;
			}
			// Sinon on n'ajoute pas de variable GET page et on supprime le dernier caractère (& ou ?)
			else
			{
				$lienPrecedent = substr($lien, 0, -1);
			}
			$pagination .= '<a href="' . $lienPrecedent . '">' . T_("Page précédente") . '</a>';
		}
		if ($page < $nombreDePages)
		{
			$noPageSuivant = $page + 1;
			$lienSuivant = $lien . 'page=' . $noPageSuivant;
			if (isset($lienPrecedent))
			{
				$pagination .= ' | ';
			}
			$pagination .= '<a href="' . $lienSuivant . '">' . T_("Page suivante") . '</a>';
		}
	
		$pagination .= '</div><!-- /pagination -->' . "\n";
	}
	
	else
	{
		$nombreDePages = 1;
		$indicePremiereOeuvre = 0;
		$indiceDerniereOeuvre = count($galerie) - 1;
	}
	
	for ($i = $indicePremiereOeuvre; $i <= $indiceDerniereOeuvre && $i < $nombreDoeuvres; $i++)
	{
		$corpsGalerie .= afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, TRUE, 'vignette', $i, FALSE, 'aucun', $galerieHauteurVignette, $galerieTelechargeOriginal, TRUE, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal);
	}
	
	if ($galerieVignettesParPage)
	{
		if ($galeriePaginationAuDessus)
		{
			$corpsGalerie = $pagination . $corpsGalerie;
		}
		
		if ($galeriePaginationAuDessous)
		{
			$corpsGalerie .= $pagination;
		}
	}
	
	$galerieInfo = '';
	
	if ($galerieInfoAjout)
	{
		$galerieInfo .= '<div id="galerieInfo">' . "\n";
		$galerieInfo .= '<p>' . sprintf(T_ngettext('Cette galerie contient %1$s oeuvre', 'Cette galerie contient %1$s oeuvres', $nombreDoeuvres), $nombreDoeuvres) . sprintf(T_ngettext(' (sur %1$s page).', ' (sur %1$s pages).', $nombreDePages), $nombreDePages);
		
		if (!preg_match('|' . nomFichierGalerie() . '$|', $_SERVER['PHP_SELF']))
		{
			$galerieInfo .= ' <a href="' . $_SERVER['PHP_SELF'] . '">' . T_("Voir l'accueil de la galerie."). '</a></p>' . "\n";
		}
		
		$galerieInfo .= '</div><!-- /galerieInfo -->' . "\n";
	}
	
	// Variable $corpsGalerie finale
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
	$pageDerreur = TRUE;
	
	$corpsGalerie .= '<p>' . T_('La galerie demandée est introuvable.') . '</p>';
	
	// Ajustement des métabalises
	$baliseTitle = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie);
	$description = sprintf(T_("La galerie %1\$s est introuvable"), $nomGalerie) . ' | ' . $baliseTitleComplement[langue($langue)];
	$motsCles = construitMotsCles('', $description);
	$motsCles .= ', ' . $nomGalerie;
	$robots = "noindex, follow, noarchive";
}

?>
