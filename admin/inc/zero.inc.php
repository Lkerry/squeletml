<?php
// Début des insertions
include_once dirname(__FILE__) . '/../../init.inc.php';
if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}
include_once $racine . '/admin/inc/fonctions.inc.php';
foreach (adminInit($racine) as $fichier)
{
	include_once $fichier;
}
// Fin des insertions

// Nécessaire à la traduction
phpGettext('..', langue($langueParDefaut, $langue));
?>
