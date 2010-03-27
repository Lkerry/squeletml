<?php
$baliseTitle = "Page introuvable";
$description = "La page demandée n'existe pas.";
$langue = 'fr';
$classesBody = 'erreur404';
include $racine . '/inc/premier.inc.php';
?>

<h1 id="titrePage404">Page introuvable</h1>

<p>La page demandée n'existe pas.</p>

<p>Vous pouvez <?php echo lienAccueil(ACCUEIL, $estAccueil, "retourner à la page d'accueil"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
