<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

define('ACCUEIL', accueil($accueil, array ($langue, $langueParDefaut)));
define('LANGUE', langue($langueParDefaut, $langue));
define('URL_DERNIERE_VERSION_SQUELETML', 'http://www.jpfleury.net/fichiers/derniere-version-squeletml.txt');
define('URL_SQUELETML', 'http://www.jpfleury.net/squeletml');
?>
