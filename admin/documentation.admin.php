<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Documentation de Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';
?>

<?php echo mdtxt($racine . '/documentation.mdtxt'); ?>

<?php echo mdtxtChaine(presentationEtContenuFichierDeConfiguration($racine)); ?>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
