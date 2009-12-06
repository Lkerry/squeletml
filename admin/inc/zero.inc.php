<?php
// Inclusions 1 de 2.

include_once dirname(__FILE__) . '/../../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

// Initialisations.

if (!isset($langue))
{
	$langue = FALSE;
}

// Inclusions 2 de 2.

include_once $racineAdmin . '/inc/fonctions.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (adminAinclureDebut($racineAdmin) as $fichier)
{
	include_once $fichier;
}

// Nécessaire à la traduction.
phpGettext('..', langue($adminLangueParDefaut, $adminLangueParDefaut));
?>
