<?php
include_once 'init.inc.php';
include_once 'inc/fonctions.inc.php';
include_once $racine . '/inc/config.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

$langueNavigateur = langue('navigateur');
$langue[1] = $langueNavigateur;
// Nécessaire à la traduction
phpGettext('.', $langueNavigateur);

if (isset($_GET['idGalerie']) && !empty($_GET['idGalerie']) && file_exists("$racine/site/fichiers/galeries/" . $_GET['idGalerie']) && file_exists("$racine/site/inc/galerie-" . $_GET['idGalerie'] . ".txt"))
{
	echo rssGalerie($racine, $urlRacine, 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], $_GET['idGalerie'], baliseTitleComplement($baliseTitleComplement, $langue), $nbreItemsFlux);
}

?>
