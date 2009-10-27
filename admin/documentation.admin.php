<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Documentation de Squeletml");
$tableDesMatieres = TRUE;
include 'inc/premier.inc.php';

$fic = fopen('../documentation.mdtxt', 'r');
$documentationMdtxt = fread($fic, filesize('../documentation.mdtxt'));
fclose($fic);
$documentationHtml = Markdown($documentationMdtxt);

echo $documentationHtml;

include $racine . '/admin/inc/dernier.inc.php';
?>
