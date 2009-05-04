<?php
include_once 'init.inc.php';

if (file_exists($racine . '/site/inc/page.galerie.inc.php'))
{
	include $racine . '/site/inc/page.galerie.inc.php';
}
else
{
	include $racine . '/inc/page.galerie.inc.php';
}

?>
