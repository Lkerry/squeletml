<?php
include_once '../init.inc.php';

if (file_exists($racine . '/site/inc/page.en.galerie.inc.php'))
{
	include $racine . '/site/inc/page.en.galerie.inc.php';
}
else
{
	include $racine . '/inc/page.en.galerie.inc.php';
}
?>
