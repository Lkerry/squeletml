<?php
include_once 'init.inc.php';
include_once 'inc/fonctions.inc.php';

$pageDerreur = TRUE;
$codeLangue = langue($langueParDefaut, 'navigateur');

if (file_exists($racine . '/site/inc/page.' . $codeLangue . '.401.inc.php'))
{
	include $racine . '/site/inc/page.' . $codeLangue . '.401.inc.php';
}
elseif (file_exists($racine . '/inc/page.' . $codeLangue . '.401.inc.php'))
{
	include $racine . '/inc/page.' . $codeLangue . '.401.inc.php';
}
else
{
	include_once $racine . '/inc/config.inc.php';
	
	if (file_exists($racine . '/site/inc/config.inc.php'))
	{
		include_once $racine . '/site/inc/config.inc.php';
	}
	
	if (file_exists($racine . '/site/inc/page.' . $langueParDefaut . '.401.inc.php'))
	{
		include $racine . '/site/inc/page.' . $langueParDefaut . '.401.inc.php';
	}
	else
	{
		include $racine . '/inc/page.' . $langueParDefaut . '.401.inc.php';
	}
}
?>
