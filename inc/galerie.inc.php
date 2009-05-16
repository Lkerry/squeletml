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
// Recherche `site/inc/galerie-$idGalerie.txt`, sinon inclut `inc/galerie-demo.txt`
if (isset($idGalerie)
	&& file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/')
	&& file_exists($racine . '/site/inc/galerie-' . $idGalerie . '.txt'))
{
	$galerie = construitTableauGalerie($racine . '/site/inc/galerie-' . $idGalerie . '.txt');
	$urlImgSrc = $urlRacine . '/site/fichiers/galeries/' . $idGalerie;
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerie;
}
else
{
	// Galerie démo par défaut
	$galerie = construitTableauGalerie($racine . '/inc/galerie-demo.txt');
	$urlImgSrc = $urlRacine . '/fichiers/galeries/demo';
	$racineImgSrc = $racine . '/fichiers/galeries/demo';
}

// Une oeuvre en particulier est demandée
if (isset($_GET['oeuvre']))
{
	// On vérifie si l'oeuvre demandée existe. Si oui, $i va contenir la valeur de son indice dans le tableau.
	$i = 0;
	foreach($galerie as $oeuvre)
	{
		// On récupère l'id de chaque image
		if (!empty($oeuvre['id']))
		{
			$id = $oeuvre['id'];
		}
		else
		{
			$id = $oeuvre['grandeNom'];
		}
		
		if ($id == $_GET['oeuvre'])
		{
			$imageExiste = TRUE;
			// L'image existe, on peut donc écraser les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable $galerie, on donne une valeur automatiquement.
			if (!empty($galerie[$i]['pageGrandeBaliseTitle']))
			{
				$baliseTitle = $galerie[$i]['pageGrandeBaliseTitle'];
			}
			else
			{
				$baliseTitle = sprintf(T_("Oeuvre %1\$s de la galerie"), $id);
			}

			if (!empty($galerie[$i]['pageGrandeDescription']))
			{
				$description = $galerie[$i]['pageGrandeDescription'];
			}
			else
			{
				$description = sprintf(T_("Taille maximale de l'oeuvre %1\$s de la galerie"), $id) . ' | ' . $baliseTitleComplement[langue($langue)];
			}
			
			if (!isset($galerie[$i]['pageGrandeMotsCles']))
			{
				$galerie[$i]['pageGrandeMotsCles'] = '';
			}
			
			$motsCles = construitMotsCles($galerie[$i]['pageGrandeMotsCles'], $description);
			$motsCles .= ', ' . $id;

			break; // On arrête la boucle
		}
		
		$i++;
	}

	// Si l'oeuvre existe, on affiche la grande version ainsi que le système de navigation dans la galerie . 
	if ($imageExiste)
	{
		// On récupère le code de l'oeuvre demandée en grande version
		$oeuvreGrande = '<p id="galerieGrande">' . afficheOeuvre($urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'grande', $i, 'aucun', $galerieHauteurVignette, $galerieTelechargeOrig) . '</p>';

		// On recherche l'oeuvre précédente pour la navigation .  Si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente
		if (array_key_exists($i - 1, $galerie))
		{
			$op = $i - 1; // $op est l'indice de l'oeuvre précédente

			// On récupère le code de la vignette de l'oeuvre précédente
			$oeuvrePrecedente = '<p class="galerieNavigationPrecedent">' . afficheOeuvre($urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $op, 'precedent', $galerieHauteurVignette, $galerieTelechargeOrig) . '</p>';
		}
		else
		{
			// Simule l'espace de la flèche de navigation pour que l'image demeure centrée
			$oeuvrePrecedente = '<p class="galerieNavigationPrecedent galerieFleche"></p>';
		}

		// On recherche l'oeuvre suivante pour la navigation
		if (array_key_exists($i + 1, $galerie))
		{
			$os = $i + 1;

			// On récupère le code de la vignette de l'oeuvre suivante
			$oeuvreSuivante = '<p class="galerieNavigationSuivant">' . afficheOeuvre($urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $os, 'suivant', $galerieHauteurVignette, $galerieTelechargeOrig) . '</p>';
		}
		else
		{
			// Simule l'espace de la flèche de navigation pour que l'image demeure centrée
			$oeuvreSuivante = '<p class="galerieNavigationSuivant galerieFleche"></p>';
		}
		
		//On crée un lien vers l'accueil de la galerie
		$lienVersGalerie = '<p id="galerieLienAccueil"><a href="' . $_SERVER['PHP_SELF'] . '">' . T_("Accueil de la galerie"). '</a></p>' . "\n\n";
		
		// On crée le corps de la galerie
		if ($galerieNavigationEmplacement == 'haut')
		{
			$corpsGalerie = $lienVersGalerie . $oeuvrePrecedente . $oeuvreSuivante . $oeuvreGrande;
		}
		elseif ($galerieNavigationEmplacement == 'bas')
		{
			$corpsGalerie = $lienVersGalerie . $oeuvreGrande . $oeuvrePrecedente . $oeuvreSuivante;
		}
	}

	// Si l'oeuvre n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie pour éviter le contenu dupliqué.
	else
	{
		$corpsGalerie = '<p>' . sprintf(T_("L'oeuvre demandée est introuvable. <a href='%1$s'>Voir toutes les oeuvres</a>."), nomFichierGalerie()) . '</p>';
	}
}

// Aucune oeuvre en particulier n'est demandée. On affiche donc la galerie.
else
{
	$compteurGalerie = 0;
	$corpsGalerie = '';
	foreach($galerie as $oeuvre)
	{
		$corpsGalerie .= afficheOeuvre($urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $compteurGalerie, 'aucun', $galerieHauteurVignette, $galerieTelechargeOrig);
		$compteurGalerie++;
	}
}

?>
