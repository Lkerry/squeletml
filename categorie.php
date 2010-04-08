<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAvantConfig());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

if (!empty($_GET['langue']))
{
	$getLangue = securiseTexte($_GET['langue']);
}

if (!empty($_GET['id']))
{
	$idCategorie = securiseTexte($_GET['id']);
	$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
	
	if (!empty($getLangue))
	{
		$langue = $getLangue;
	}
	elseif (isset($categories[$idCategorie]))
	{
		$langue = langueCat($categories[$idCategorie], $langueParDefaut);
	}
	else
	{
		$langue = $langueParDefaut;
	}
	
	phpGettext('.', $langue); // Nécessaire à la traduction.
	
	if ($categories !== FALSE && estCatSpeciale($idCategorie) && !empty($getLangue))
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $getLangue, $categories, array($idCategorie), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
}

if (
	empty($idCategorie) ||
	!isset($categories[$idCategorie]) ||
	(empty($getLangue) && estCatSpeciale($idCategorie)) ||
	(!empty($getLangue) && !estCatSpeciale($idCategorie)) ||
	(!empty($categories[$idCategorie]['urlCat']) && strpos($categories[$idCategorie]['urlCat'], "categorie.php?id=$idCategorie") === FALSE) // Empêcher la duplication de contenu dans les moteurs de recherche.
)
{
	$erreur404 = TRUE;
}

include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
