<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

$codeLangue = langue('', 'navigateur');

// Nécessaire à la traduction.
phpGettext('.', $codeLangue);

$baliseTitle = T_("Déconnexion de la section d'administration de Squeletml");
$description = T_("Déconnexion de la section d'administration de Squeletml");
$robots = 'noindex, nofollow, noarchive';
$enTetesHttp = "header('Cache-Control: no-cache, must-revalidate'); /* HTTP/1.1. */ header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); /* Date dans le passé. */";
include $racine . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Déconnexion de la section d'administration de Squeletml"); ?></h1>

<p><?php echo T_("Pour vous assurer de la déconnexion, visitez l'accueil de la section d'administration. Si une fenêtre de connexion vous demande de vous identifier, la déconnexion a été effectuée avec succès."); ?></p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
