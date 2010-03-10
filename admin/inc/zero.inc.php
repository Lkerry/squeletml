<?php
// Inclusions 1 de 2.

include_once dirname(__FILE__) . '/../../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

// Affectations.

if (!isset($langue))
{
	$langue = '';
}

$urlRacineAdmin = $urlRacine . '/' . $dossierAdmin;

// Inclusions 2 de 2.

include_once $racineAdmin . '/inc/fonctions.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

$nomPage = nomPage();
$url = url();
$urlSansGet = url(FALSE);
$urlAvecIndexSansGet = url(FALSE, TRUE, TRUE);
$urlDeconnexion = adminUrlDeconnexion($urlRacine);
$urlFichiers = $urlRacine . '/site/fichiers';
$urlSite = $urlRacine . '/site';

foreach (adminAinclureDebut($racineAdmin) as $fichier)
{
	include_once $fichier;
}

phpGettext('..', langue($adminLangueParDefaut, '')); // Nécessaire à la traduction.

// Traitement personnalisé optionnel.
if (file_exists("$racine/site/$dossierAdmin/inc/zero.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/zero.inc.php";
}
?>
