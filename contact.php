<?php
include 'init.inc.php';
if (file_exists($racine . '/site/inc/page.contact.inc.php'))
{
	include $racine . '/site/inc/page.contact.inc.php';
}
else
{
	include $racine . '/inc/page.contact.inc.php';
}

?>
