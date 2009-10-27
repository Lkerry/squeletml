<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Lisez-moi - Introduction à Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';

$fic = fopen('../LISEZ-MOI.mdtxt', 'r');
$lisezMoiMdtxt = fread($fic, filesize('../LISEZ-MOI.mdtxt'));
fclose($fic);
$lisezMoiHtml = Markdown($lisezMoiMdtxt);

echo $lisezMoiHtml;

include $racine . '/admin/inc/dernier.inc.php';
?>
