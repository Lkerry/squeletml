<?php
$robots = 'noindex, nofollow, noarchive';
$enTetesHttp = "header('Cache-Control: no-cache, must-revalidate'); /* HTTP/1.1. */ header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); /* Date dans le passé. */";
include 'inc/premier.inc.php';
?>

<p><?php echo T_("Pour vous assurer de la déconnexion, visitez l'accueil de la section d'administration. Si une fenêtre de connexion vous demande de vous identifier, la déconnexion a été effectuée avec succès."); ?></p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
