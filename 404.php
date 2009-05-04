<?php
include_once 'init.inc.php';

if (file_exists($racine . '/site/inc/page.404.inc.php'))
{
	include $racine . '/site/inc/page.404.inc.php';
}
else
{
	include $racine . '/inc/page.404.inc.php';
}

?>
