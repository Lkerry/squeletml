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

eval(variablesAaffecterAuDebut());
$urlDeconnexion = adminUrlDeconnexion($urlRacine);

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
