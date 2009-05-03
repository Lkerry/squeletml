<?php
$title = "Page introuvable";
$description = "La page demandée n'existe pas.";
include $racine . '/inc/premier.inc.php';
?>

<h1>Page introuvable</h1>

<p>La page demandée n'existe pas.</p>

<p>Vous pouvez <?php echo construitLienVersAccueil($accueil, estAccueil(), "retourner à la page d'accueil"); ?>.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
