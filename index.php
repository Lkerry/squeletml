<?php
include_once 'inc/fonctions.inc.php';

if (file_exists('site/inc/squeletml-est-installe.txt'))
{
	include 'init.inc.php';
	include cheminXhtml($racine, array ('fr'), 'page.index');
}
else
{
	header('Location: ' . urlParente() . '/installation.php', TRUE, 302);
}
?>
