<?php
$baliseTitle = "Lisez-moi - Introduction à Squeletml";
include 'inc/premier.inc.php';

$fic = fopen('../LISEZ-MOI.mdtxt', 'r');
$lisezMoiMdtxt = fread($fic, filesize('../LISEZ-MOI.mdtxt'));
fclose($fic);
$lisezMoiHtml = Markdown($lisezMoiMdtxt);

echo $lisezMoiHtml;

include 'inc/dernier.inc.php';
?>
