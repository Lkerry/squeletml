<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

define('ACCUEIL', accueil($accueil, array ($langue, $langueParDefaut)));
define('LANGUE', langue($langueParDefaut, $langue));
define('PAGE', page());
define('URL', url());
?>
