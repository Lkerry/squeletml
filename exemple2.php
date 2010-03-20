<?php
$baliseTitle = "Exemple de page simple";
$robots = "noindex, follow, noarchive"; // Empêche la présence de l'exemple dans les moteurs de recherche.
include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion.
?>

<h1>Exemple de page simple</h1>

<?php
$phrase = "L'intéressant test. Fin du test.inc.php";
echo filtreChaine($racine, $phrase);
?>

<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc. Quisque sit amet mi sit amet magna faucibus luctus. Ut pellentesque sodales arcu. Phasellus a elit. Maecenas rhoncus lorem id quam. Sed sed arcu et quam fermentum ultrices. Aenean pulvinar molestie magna. Vestibulum bibendum? Nullam libero arcu, ultrices a; aliquet quis, adipiscing sit amet, neque.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
