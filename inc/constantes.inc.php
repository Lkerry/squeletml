<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

define("ACCUEIL", accueil($accueil, $langueParDefaut, $langue));

define("LANGUE", langue($langueParDefaut, $langue));

define("URL", url());
?>
