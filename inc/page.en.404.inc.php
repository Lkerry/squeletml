<?php
$baliseTitle = "File not found";
$description = "The URL you requested was not found.";
$langue[1] = 'en';
include $racine . '/inc/premier.inc.php';
?>

<h1>File not found</h1>

<p>The URL you requested was not found.</p>

<p>You can <?php echo construitLienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), "visit the home page"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
