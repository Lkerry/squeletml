<?php
include 'inc/zero.inc.php';
$tableDesMatieres = TRUE;

if (isset($_GET['type']) && $_GET['type'] == 'lisez-moi')
{
	$baliseH1 = T_("Lisez-moi - Introduction à Squeletml");
	include $racineAdmin . '/inc/premier.inc.php';
	
	echo '<p><em>' . sprintf(T_("<a href=\"%1\$s\">Consulter la documentation complète de Squeletml</a> au lieu de l'introduction ci-dessous."), "$urlSansGet") . "</em></p>\n";
	echo mkd($racine . '/doc/LISEZ-MOI.mkd');
}
else
{
	$baliseH1 = T_("Documentation de Squeletml");
	include $racineAdmin . '/inc/premier.inc.php';
	
	echo '<p><em>' . sprintf(T_("<a href=\"%1\$s\">Consulter une introduction à Squeletml</a> au lieu de la documentation complète ci-dessous."), "$urlSansGet?type=lisez-moi") . "</em></p>\n";
	echo mkd($racine . '/doc/documentation.mkd');
	echo annexesDocumentation($racineAdmin);
}

include $racineAdmin . '/inc/dernier.inc.php';
?>
