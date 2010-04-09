<?php
// Inclusions et affectations.

include_once dirname(__FILE__) . '/../../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

if (file_exists("$racine/site/$dossierAdmin/inc/devel.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/devel.inc.php";
}

include_once $racineAdmin . '/inc/fonctions.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAaffecterAuDebut());
$urlDeconnexion = adminUrlDeconnexion($urlRacine);
$urlRacineAdmin = $urlRacine . '/' . $dossierAdmin;

if (!isset($langue))
{
	$langue = '';
}

foreach (adminFichiersAinclureAuDebut($racineAdmin) as $fichier)
{
	include_once $fichier;
}

phpGettext('..', LANGUE_ADMIN); // Nécessaire à la traduction.

// Traitement personnalisé optionnel.
if (file_exists("$racine/site/$dossierAdmin/inc/zero.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/zero.inc.php";
}
?>
