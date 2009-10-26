<?php
$baliseTitle = "Authorization Required";
$description = "Authorization Required";
$langue = 'en';
include $racine . '/inc/premier.inc.php';
?>

<h1 id="titrePage401">Authorization Required</h1>

<p>The URL you requested was not found.</p>

<p>You can <?php echo lienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), "visit the home page"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
