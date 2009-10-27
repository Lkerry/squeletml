<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Documentation de Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';

echo mdtxt($racine . '/documentation.mdtxt');

include $racine . '/admin/inc/dernier.inc.php';
?>
