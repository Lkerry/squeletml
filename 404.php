<?php
$desactiverCache = TRUE;
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAvantConfig());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include $cheminFichier;
}

$estPageDerreur = TRUE;
$codeLangue = langue('navigateur', $langueParDefaut);

include cheminXhtml($racine, array ($codeLangue, $langueParDefaut), 'page.404');
?>
