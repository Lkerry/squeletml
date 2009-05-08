<?php
$baliseTitle = "Documentation de Squeletml";
include 'inc/premier.inc.php';

$fic = fopen('../documentation.mdtxt', 'r');
$documentationMdtxt = fread($fic, filesize('../documentation.mdtxt'));
fclose($fic);
$documentationHtml = Markdown($documentationMdtxt);

echo $documentationHtml;

include 'inc/dernier.inc.php';
?>
