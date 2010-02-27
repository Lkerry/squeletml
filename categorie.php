<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

if (!empty($_GET['id']))
{
	$idCategorie = securiseTexte($_GET['id']);
}
else
{
	$erreur404 = TRUE;
}

$cheminFichier = cheminConfigCategories($racine);

if ($cheminFichier)
{
	$categories = super_parse_ini_file($cheminFichier, TRUE);
	
	if (isset($categories[$idCategorie]['urlCategorie']))
	{
		// Empêcher la duplication de contenu dans les moteurs de recherche.
		$erreur404 = TRUE;
	}
}

include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
