<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

$estPageDerreur = TRUE;
$codeLangue = langue($langueParDefaut, 'navigateur');

include_once cheminXhtmlLangue($racine, array($codeLangue, $langueParDefaut), 'page.401');
?>
