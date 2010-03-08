<?php
$baliseTitle = "Authorization Required";
$description = "The URL you requested requires authentication.";
$langue = 'en';
include $racine . '/inc/premier.inc.php';
?>

<h1 id="titrePage401">Authorization Required</h1>

<p>The URL you requested requires authentication.</p>

<p>You can <?php echo lienAccueil(ACCUEIL, $estAccueil, "visit the home page"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
