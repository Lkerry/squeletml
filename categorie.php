<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAvantConfig());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include $cheminFichier;
}

if (!empty($_GET['langue']))
{
	$getLangue = securiseTexte($_GET['langue']);
}

if (!empty($_GET['id']))
{
	$getId = $_GET['id'];
	$idCategorie = $getId;
	
	$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
	
	if (!empty($categories))
	{
		$idReel = idCategorie($categories, $idCategorie);
		
		if (!empty($idReel))
		{
			$idCategorie = $idReel;
		}
	}
	
	if (!empty($getLangue))
	{
		$langue = $getLangue;
	}
	elseif (!empty($categories[$idCategorie]['langue']))
	{
		$langue = $categories[$idCategorie]['langue'];
	}
	else
	{
		$langue = $langueParDefaut;
	}
	
	phpGettext('.', $langue); // Nécessaire à la traduction.
	
	if ($categories !== FALSE && estCatSpeciale($idCategorie) && !empty($getLangue))
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $getLangue, $categories, array ($idCategorie), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
}

if (
	empty($idCategorie) ||
	!isset($categories[$idCategorie]) ||
	(empty($getLangue) && estCatSpeciale($idCategorie)) ||
	(!empty($getLangue) && !estCatSpeciale($idCategorie)) ||
	(!empty($categories[$idCategorie]['url']) && (strpos($categories[$idCategorie]['url'], 'categorie.php?') !== 0 || !preg_match('/(\?|&|&amp;)id=' . preg_quote(filtreChaine($idCategorie), '/') . '(&|$)/', $categories[$idCategorie]['url']))) || // Empêcher la duplication de contenu dans les moteurs de recherche.
	($getId != filtreChaine($getId)) // Idem.
)
{
	$erreur404 = TRUE;
}

include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
