<?php
$baliseTitle = "Authentification requise";
$description = "La page demandée requiert un authentification.";
$langue = 'fr';
include $racine . '/inc/premier.inc.php';
?>

<h1>Authentification requise</h1>

<p>La page demandée requiert un authentification.</p>

<p>Voulez-vous <?php echo lienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), "retourner à la page d'accueil"); ?>?</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
