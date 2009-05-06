<?php
$baliseTitle = "Documentation de Squeletml";
include 'inc/premier.inc.php';

$fic = fopen('../LISEZ-MOI.mdtxt', 'r');
$documentationMdtxt = fread($fic, filesize('../LISEZ-MOI.mdtxt'));
fclose($fic);
$documentationHtml = Markdown($documentationMdtxt);

echo $documentationHtml;

include 'inc/dernier.inc.php';
?>
