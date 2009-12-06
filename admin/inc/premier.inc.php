<?php
########################################################################
##
## Initialisations et inclusions.
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

$boitesDeroulantesTableau = boitesDeroulantes('', $boitesDeroulantes);

$cheminAncres = $racineAdmin . '/xhtml/ancres.inc.php';

$cheminMenu = $racineAdmin . '/xhtml/menu.inc.php';

$doctype = doctype($adminXhtmlStrict);

$idBody = adminBodyId();

if (!empty($idBody))
{
	$idBody = ' id="' . $idBody . '"';
}

$locale = locale(LANGUE);

########################################################################
##
## Ajouts dans `$adminBalisesLinkScript`.
##
########################################################################

if (!adminEstIe())
{
	$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php#js#$urlRacineAdmin/js/wz_dragdrop/wz_dragdrop.js";
}

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau))
{
	$adminBalisesLinkScript[] = url(FALSE) . "#css#$urlRacine/css/boites-deroulantes.css";
	$adminBalisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.min.js";
	$adminBalisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.cookie.js";
	$jsDirect = '';
	
	foreach ($boitesDeroulantesTableau as $boiteDeroulante)
	{
		$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante');});\n";
	}
	
	$adminBalisesLinkScript[] = url(FALSE) . "#jsDirect#$jsDirect";
}

// Table des matières.

if ($tableDesMatieres)
{
	$adminBalisesLinkScript[] = url(FALSE) . "#css#$urlRacine/css/table-des-matieres.css";
	$adminBalisesLinkScript[] = url(FALSE) . "#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	
	$adminBalisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/Gettext/lib/Gettext.js";
	
	if (file_exists($racine . '/locale/' . $locale))
	{
		$adminBalisesLinkScript[] = url(FALSE) . "#po#$urlRacine/locale/$locale/LC_MESSAGES/squeletml.po";
	}
	
	$adminBalisesLinkScript[] = url(FALSE) . "#jsDirect#var gt = new Gettext({'domain': 'squeletml'});";
	
	$adminBalisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.min.js";
	$adminBalisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery-tableofcontents/jquery.tableofcontents.js";
	$adminBalisesLinkScript[] = url(FALSE) . "#jsDirect#tableDesMatieres('interieurContenu', 'ul');";
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
