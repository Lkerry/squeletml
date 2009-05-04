<?php
$title = "Galerie";
$description = "Galerie";
include $racine . '/inc/galerie.inc.php'; // Important d'insérer avant premier.inc.php, pour permettre la modification des balises de l'en-tête
include $racine . '/inc/premier.inc.php';
?>

<h1>Galerie</h1>

<?php echo $corpsGalerie; ?>

<?php include $racine . '/inc/dernier.inc.php'; ?>
