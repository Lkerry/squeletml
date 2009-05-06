<?php
// Insertion des fichiers nécessaires
include_once $racine . '/inc/fonctions.inc.php';
foreach (init($racine, langue($langue)) as $fichier)
{
	include_once $fichier;
}

// Nécessaire à la traduction du module
phpGettext($racine, langue($langue));

// Insertion du tableau contenant la liste des oeuvres à afficher
// Recherche `site/inc/galerie-$idGalerie.inc.php`, sinon inclut `inc/galerie-0.inc.php`
/*
Chaque oeuvre de la galerie possède sa propre entrée dans le tableau $galerie. Voici la structure d'une entrée:

 id: id image. L'id est utilisé dans l'URL au lieu de l'indice de l'oeuvre dans le tableau pour identifier l'oeuvre, ce qui permet de facilement déplacer des oeuvres dans la galerie sans modifier leur URL. Si vide, l'id sera généré automatiquement à partir du nom de fichier image.

 vignetteNom: nom Vignette. Si vide, correspond à grandeNom(sans extension)-vignette.extension

 vignetteLargeur: largeur Vignette. Si vignetteLargeur ou vignetteHauteur sont renseignées, seulement la ou les informations renseignées seront affichées. Si les deux sont vides, les attributs `width` et `height` seront calculés automatiquement.

 vignetteHauteur: hauteur Vignette. Voir vignetteLargeur.

 vignetteAlt: alt Vignette. Si vide, génération automatique.

 grandeNom: nom Grande. Champ obligatoire.

 grandeLargeur: largeur Grande. Voir vignetteLargeur.

 grandeHauteur: hauteur Grande. Voir grandeLargeur.

 grandeAlt: alt Grande. Si vide, génération automatique.

 grandeCommentaire: commentaire Grande. Message affiché sous l'image.

 pageGrandeTitle: title page Grande ; laisser vide pour génération automatique.

 pageGrandeDescription: description page Grande ; laisser vide pour génération automatique.

 pageGrandeKeywords: keywords page Grande ; laisser vide pour génération automatique.

Voici une entrée vide qu'il est possible de copier/coller:

array (
	'id'                     => "",
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "*",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

*/

if (isset($idGalerie)
	&& file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/')
	&& file_exists($racine . '/site/inc/galerie-' . $idGalerie . '.txt'))
{
	$galerie = construitTableauGalerie($racine . '/site/inc/galerie-' . $idGalerie . '.txt');
	$urlImgSrc = $squeletmlAccueil . '/site/fichiers/galeries/' . $idGalerie;
	$racineImgSrc = $racine . '/site/fichiers/galeries/' . $idGalerie;
}
else
{
	// Galerie démo par défaut
	$galerie = construitTableauGalerie($racine . '/inc/galerie-0.txt');
	$urlImgSrc = $squeletmlAccueil . '/fichiers/galeries/0';
	$racineImgSrc = $racine . '/fichiers/galeries/0';
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
			if (!empty($galerie[$i]['pageGrandeTitle']))
			{
				$baliseTitle = $galerie[$i]['pageGrandeTitle'];
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

			$motsCles = construitMotsCles($galerie[$i]['pageGrandeKeywords'], $description);
			$motsCles .= ', ' . $id;

			break; // On arrête la boucle
		}
		
		$i++;
	}

	// Si l'oeuvre existe, on affiche la grande version ainsi que le système de navigation dans la galerie . 
	if ($imageExiste)
	{
		// On récupère le code de l'oeuvre demandée en grande version
		$oeuvreGrande = '<p id="galerieGrande">' . afficheOeuvre($squeletmlAccueil, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'grande', $i, 'aucun', $galerieHauteurVignette) . '</p>';

		// On recherche l'oeuvre précédente pour la navigation .  Si l'oeuvre demandée est la première, il n'y a pas d'oeuvre précédente
		if (array_key_exists($i - 1, $galerie))
		{
			$op = $i - 1; // $op est l'indice de l'oeuvre précédente

			// On récupère le code de la vignette de l'oeuvre précédente
			$oeuvrePrecedente = '<p class="galerieNavigationPrecedent">' . afficheOeuvre($squeletmlAccueil, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $op, 'precedent', $galerieHauteurVignette) . '</p>';
		}

		// On recherche l'oeuvre suivante pour la navigation
		if (array_key_exists($i + 1, $galerie))
		{
			$os = $i + 1;

			// On récupère le code de la vignette de l'oeuvre suivante
			$oeuvreSuivante .= '<p class="galerieNavigationSuivant">' . afficheOeuvre($squeletmlAccueil, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $os, 'suivant', $galerieHauteurVignette) . '</p>';
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
		$corpsGalerie .= afficheOeuvre($squeletmlAccueil, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, 'vignette', $compteurGalerie, 'aucun', $galerieHauteurVignette);
		$compteurGalerie++;
	}
}

$corpsGalerie = '<div id="galerie">' . "\n" . $corpsGalerie . "\n" . '</div><!-- /galerie -->' . "\n";
?>
