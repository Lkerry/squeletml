<?php
include_once 'init.inc.php';

if (file_exists($racine . '/site/inc/page.fr.galerie.inc.php'))
{
	include $racine . '/site/inc/page.fr.galerie.inc.php';
}
else
{
	include $racine . '/inc/page.fr.galerie.inc.php';
}
?>
