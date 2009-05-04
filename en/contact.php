<?php
include_once '../init.inc.php';

if (file_exists($racine . '/site/inc/page.en.contact.inc.php'))
{
	include $racine . '/site/inc/page.en.contact.inc.php';
}
else
{
	include $racine . '/inc/page.en.contact.inc.php';
}

?>
