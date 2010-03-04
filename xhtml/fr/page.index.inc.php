<?php
$baliseTitle = "Bienvenue sur votre site Squeletml";
$description = "Page d'accueil par défaut de Squeletml";
$langue = 'fr';
$robots = "noindex, follow, noarchive"; // Empêche la présence de la page par défaut dans les moteurs de recherche.
include $racine . '/inc/premier.inc.php';
?>

<?php include_once $racine . '/xhtml/message-accueil-par-defaut.inc.php'; ?>

<?php include $racine . '/inc/dernier.inc.php'; ?>
