<?php
$baliseTitle = "File not found";
$description = "The URL you requested was not found.";
$langue = 'en';
$classesBody = 'erreur404';
include $racine . '/inc/premier.inc.php';
?>

<h1 id="titrePage404">File not found</h1>

<p>The URL you requested was not found.</p>

<p>You can <?php echo lienAccueil(eval(ACCUEIL), $estAccueil, "visit the home page"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
