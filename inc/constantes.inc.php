<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

define('ACCUEIL', accueil($accueil, array ($langue, $langueParDefaut)));
define('LANGUE', langue($langue, $langueParDefaut));
define('URL_DERNIERE_VERSION_SQUELETML', 'http://www.squeletml.net/version.txt');
define('URL_SQUELETML', 'http://www.squeletml.net/');
?>
