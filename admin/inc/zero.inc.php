<?php
// Début des insertions
include_once dirname(__FILE__) . '/../../init.inc.php';
include_once $racine . '/admin/inc/fonctions.inc.php';
foreach (adminInit($racine) as $fichier)
{
	include_once $fichier;
}
// Fin des insertions

// Nécessaire à la traduction
$langueNavigateur = langue('navigateur');
$langue = $langueNavigateur;
phpGettext('..', $langue);
?>
