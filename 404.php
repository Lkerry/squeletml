<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAaffecterAuDebut());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

$estPageDerreur = TRUE;
$codeLangue = langue('navigateur', $langueParDefaut);

include_once cheminXhtml($racine, array($codeLangue, $langueParDefaut), 'page.404');
?>
