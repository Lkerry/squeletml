<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Documentation de Squeletml");
$tableDesMatieres = TRUE;
include $racineAdmin . '/inc/premier.inc.php';
?>

<?php echo mdtxt($racine . '/documentation.mdtxt'); ?>

<?php echo mdtxtChaine(annexesDocumentation($racineAdmin)); ?>

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
