<?php
include_once 'init.inc.php';

if (file_exists($racine . '/site/inc/page.index.inc.php'))
{
	include $racine . '/site/inc/page.index.inc.php';
}
else
{
	include $racine . '/inc/page.index.inc.php';
}

?>
