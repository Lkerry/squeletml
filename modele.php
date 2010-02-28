<?php
$baliseTitle = "Titre (contenu de la balise `title`)";
$description = "Description de la page";
include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion.
?>

<h1>Titre de la page</h1>

<?php
echo publicationsRecentes($racine, $urlRacine, LANGUE, 'categorie', "L'intéressante générale", 5, TRUE, $dureeCache, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
?>

<?php include $racine . '/inc/dernier.inc.php'; ?>
