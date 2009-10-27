<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Lisez-moi - Introduction à Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';

echo mdtxt($racine . '/LISEZ-MOI.mdtxt');

include $racine . '/admin/inc/dernier.inc.php';
?>
