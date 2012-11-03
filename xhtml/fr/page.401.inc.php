<?php
$baliseTitle = "Authentification requise";
$description = "La page demandée requiert une authentification.";
$langue = 'fr';
$classesBody = 'erreur401';
include $racine . '/inc/premier.inc.php';
?>

<h1 id="titrePage401">Authentification requise</h1>

<p>La page demandée requiert un authentification.</p>

<p>Voulez-vous <?php echo lienAccueil(eval(ACCUEIL), $estAccueil, "retourner à la page d'accueil"); ?>?</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
