<?php
$baliseTitle = "Version de Squeletml";
include 'inc/premier.inc.php';
?>

<h1>Version de Squeletml</h1>

<p>La version en cours est <?php echo versionLogiciel($racine); ?>.</p>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
