<?php
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

$galerie = array (

array (
#	'id'                     => 1,
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "lucertola_architetto_fra_01.png",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

array (
	'id'                     => 2,
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "lucertola_maya_architett_01.png",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

array (
	'id'                     => 3,
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "lucertolona_architetto_f_01.png",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

array (
	'id'                     => 4,
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "lucertolone_architetto_f_01.png",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

array (
	'id'                     => 5,
	'vignetteNom'            => "",
	'vignetteLargeur'        => "",
	'vignetteHauteur'        => "",
	'vignetteAlt'            => "",
	'grandeNom'              => "ramarro_architetto_franc_01.png",
	'grandeLargeur'          => "",
	'grandeHauteur'          => "",
	'grandeAlt'              => "",
	'grandeCommentaire'      => "",
	'pageGrandeTitle'        => "",
	'pageGrandeDescription'  => "",
	'pageGrandeKeywords'     => "",
),

);

?>
