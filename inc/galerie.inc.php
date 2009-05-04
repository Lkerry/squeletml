<?php
// Insertion des fonctions
include_once $racine . '/inc/fonctions.inc.php';

// Insertion de la config
include_once $racine . '/inc/config.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

// Insertion du tableau contenant la liste des oeuvres à afficher
// Recherche `site/inc/galerie-$idGalerie.inc.php`, sinon inclut `inc/galerie-0.inc.php`
if (isset($idGalerie) && file_exists($racine . '/site/inc/galerie-' . $idGalerie . '.inc.php'))
{
	include $racine . '/site/inc/galerie-' . $idGalerie . '.inc.php';
	$racineImgSrc = $accueil . '/site/images/galeries/' . $idGalerie;
}
else
{
	include $racine . '/inc/galerie-0.inc.php';
	$racineImgSrc = $accueil . '/images/galeries/0';
}

// Une oeuvre en particulier est demandée
if (isset($_GET['oeuvre']))
{
	// On vérifie si l'oeuvre demandée existe. Si oui, $i va contenir la valeur de son indice dans le tableau.
	$i = 0;
	foreach($galerie as $oeuvre)
	{
		if ($oeuvre['id'] == $_GET['oeuvre'])
		{
			$imageExiste = TRUE;
			// L'image existe, on peut donc écraser les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises dans la variable $galerie, on donne une valeur automatiquement.
			if (!empty($galerie[$i]['pageGrandeTitle']))
			{
				$title = $galerie[$i]['pageGrandeTitle'];
			}
			else
			{
				$title = 'Oeuvre ' . $galerie[$i]['id'] . ' de la galerie';
			}

			if (!empty($galerie[$i]['pageGrandeDescription']))
			{
				$description = $galerie[$i]['pageGrandeDescription'];
			}
			else
			{
				$description = "Taille maximale de l'oeuvre " . $galerie[$i]['id'] . ' de la galerie' . ' | ' . $titleComplement;
			}

			$keywords = construitMotsCles($galerie[$i]['pageGrandeKeywords'], $description);
			$keywords .= ', ' . $galerie[$i]['id'];

			break; // On arrête la boucle
		}
		
		$i++;
	}

	// Si l'oeuvre existe, on affiche la grande version ainsi que le système de navigation dans la galerie . 
	if ($imageExiste)
	{
		// On récupère le code de l'oeuvre demandée en grande version
		$oeuvreGrande = '<p id="galerieGrande">' . afficheOeuvre($accueil, $racineImgSrc, $galerie, $galerieNavigation, 'grande', $i, 'aucun') . '</p>';

		// On recherche l'oeuvre précédente pour la navigation .  Si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente
		if (array_key_exists($i - 1, $galerie))
		{
			$op = $i - 1; // $op est l'indice de l'oeuvre précédente

			// On récupère le code de la vignette de l'oeuvre précédente
			$oeuvrePrecedente = '<p class="galerieNavigationPrecedent">' . afficheOeuvre($accueil, $racineImgSrc, $galerie, $galerieNavigation, 'vignette', $op, 'precedent') . '</p>';
		}

		// On recherche l'oeuvre suivante pour la navigation
		if (array_key_exists($i + 1, $galerie))
		{
			$os = $i + 1;

			// On récupère le code de la vignette de l'oeuvre suivante
			$oeuvreSuivante .= '<p class="galerieNavigationSuivant">' . afficheOeuvre($accueil, $racineImgSrc, $galerie, $galerieNavigation, 'vignette', $os, 'suivant') . '</p>';
		}

		// On crée le corps de la galerie
		if ($galerieNavigationEmplacement == 'haut')
		{
			$corpsGalerie = $oeuvrePrecedente . $oeuvreSuivante . $oeuvreGrande;
		}
		elseif ($galerieNavigationEmplacement == 'bas')
		{
			$corpsGalerie = $oeuvreGrande . $oeuvrePrecedente . $oeuvreSuivante;
		}
	}

	// Si l'oeuvre n'existe pas, on affiche un message d'erreur. On n'affiche pas toutes les images de la galerie pour éviter le contenu dupliqué.
	else
	{
		$corpsGalerie = "<p>L'oeuvre demandée est introuvable. " . '<a href="' . nomFichierGalerie() . '">Voir toutes les oeuvres</a>.</p>';
	}
}

// Aucune oeuvre en particulier n'est demandée. On affiche donc la galerie.
else
{
	$compteurGalerie = 0;
	$corpsGalerie = '';
	foreach($galerie as $oeuvre)
	{
		$corpsGalerie .= afficheOeuvre($accueil, $racineImgSrc, $galerie, $galerieNavigation, 'vignette', $compteurGalerie, 'aucun');
		$compteurGalerie++;
	}
}

$corpsGalerie = '<div id="galerie">' . "\n" . $corpsGalerie . "\n" . '</div><!-- /galerie -->' . "\n";
?>
