<?php
########################################################################
##
## Affectations et inclusions.
##
########################################################################

$baliseTitle .= ' | ' . T_("Administration de Squeletml");

if (!isset($boitesDeroulantes))
{
	$boitesDeroulantes = '';
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = FALSE;
}

if ($tableDesMatieres)
{
	$boitesDeroulantes .= '|tableDesMatieres';
}

$boitesDeroulantesTableau = boitesDeroulantes($adminBoitesDeroulantesParDefaut, $boitesDeroulantes);
$cheminAncres = adminCheminXhtml($racineAdmin, 'ancres');
$cheminRaccourcis = adminCheminXhtml($racineAdmin, 'raccourcis');
$doctype = doctype($adminXhtmlStrict);
$idBody = adminBodyId();

if (!empty($idBody))
{
	$idBody = ' id="' . $idBody . '"';
}

$locale = locale(LANGUE);

// Menu.
ob_start();
include_once adminCheminXhtml($racineAdmin, 'menu');
$menu = ob_get_contents();
ob_end_clean();
$menu = lienActif($menu, FALSE);

$nomPage = nomPage();
$url = url();
$urlFichiers = $urlRacine . '/site/fichiers';
$urlSansGet = url(FALSE);
$urlSite = $urlRacine . '/site';

########################################################################
##
## Ajouts dans `$adminBalisesLinkScript`.
##
########################################################################

if (!adminEstIe())
{
	$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/wz_dragdrop/wz_dragdrop.js";
}

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau))
{
	$adminBalisesLinkScript[] = $urlSansGet . "#css#$urlRacine/css/boites-deroulantes.css";
	$adminBalisesLinkScript[] = $urlSansGet . "#js#$urlRacine/js/jquery.min.js";
	$adminBalisesLinkScript[] = $urlSansGet . "#js#$urlRacine/js/jquery.cookie.js";
	$jsDirect = '';
	
	foreach ($boitesDeroulantesTableau as $boiteDeroulante)
	{
		$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante');});\n";
	}
	
	$adminBalisesLinkScript[] = $urlSansGet . "#jsDirect#$jsDirect";
}

// Table des matières.

if ($tableDesMatieres)
{
	$adminBalisesLinkScript[] = $urlSansGet . "#css#$urlRacine/css/table-des-matieres.css";
	$adminBalisesLinkScript[] = $urlSansGet . "#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	
	$adminBalisesLinkScript[] = $urlSansGet . "#js#$urlRacine/js/Gettext/lib/Gettext.js";
	
	if (file_exists($racine . '/locale/' . $locale))
	{
		$adminBalisesLinkScript[] = $urlSansGet . "#po#$urlRacine/locale/$locale/LC_MESSAGES/squeletml.po";
	}
	
	$adminBalisesLinkScript[] = $urlSansGet . "#jsDirect#var gt = new Gettext({'domain': 'squeletml'});";
	
	$adminBalisesLinkScript[] = $urlSansGet . "#js#$urlRacine/js/jquery.min.js";
	$adminBalisesLinkScript[] = $urlSansGet . "#js#$urlRacine/js/jquery-tableofcontents/jquery.tableofcontents.js";
	$adminBalisesLinkScript[] = $urlSansGet . "#jsDirect#tableDesMatieres('interieurContenu', 'ul', 'h2');";
}

// Variable finale.

$linkScript = linkScript($adminBalisesLinkScript, '', TRUE);

########################################################################
##
## Traitement personnalisé optionnel.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/premier.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/premier.inc.php";
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include_once adminCheminXhtml($racineAdmin, 'page.premier');
?>
