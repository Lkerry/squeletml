<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Documentation de Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';

echo mdtxt($racine . '/documentation.mdtxt');

$texte = highlight_file($racine . '/inc/config.inc.php', TRUE);

echo str_replace('&nbsp;', ' ', $texte);

include $racine . '/admin/inc/dernier.inc.php';
?>
