<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Version de Squeletml");
include 'inc/premier.inc.php';
?>

<h1><?php echo T_("Version de Squeletml"); ?></h1>

<p><?php printf(T_('La version en cours est %1$s.'), adminVersionLogiciel($racine)); ?></p>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
