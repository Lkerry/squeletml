<?php
########################################################################
##
## Traitement personnalisé optionnel 1 de 2.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/premier-pre.inc.php"))
{
	include "$racine/site/$dossierAdmin/inc/premier-pre.inc.php";
}

########################################################################
##
## Affectations et inclusions.
##
########################################################################

extract(init('', 'baliseH1', 'baliseTitle', 'h1'), EXTR_SKIP);

if (!isset($adminBalisesLinkScriptFinales))
{
	$adminBalisesLinkScriptFinales = array ();
}

$baliseTitle = baliseTitle($baliseTitle, $baliseH1) . ' | ' . T_("Administration de Squeletml");

if (!isset($boitesDeroulantes))
{
	$boitesDeroulantes = '';
}

if (!isset($boitesDeroulantesAlaMain))
{
	$boitesDeroulantesAlaMain = $adminBoitesDeroulantesAlaMainParDefaut;
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = FALSE;
}

if ($tableDesMatieres)
{
	$boitesDeroulantes .= ' #tableDesMatieres';
}

$boitesDeroulantesTableau = boitesDeroulantes($adminBoitesDeroulantesParDefaut, $boitesDeroulantes);
$cheminAncres = adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'ancres');
$cheminLienBas = adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'lien-bas');
$cheminRaccourcis = adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'raccourcis');
list ($contenuDoctype, $ouvertureBaliseHtml) = doctype($adminDoctype, LANGUE_ADMIN);
$classesBody = adminClassesBody($tableDesMatieresAvecFond, $tableDesMatieresArrondie);
$idBody = adminIdBody();

if (!empty($baliseH1))
{
	$h1 = '<h1>' . $baliseH1 . '</h1>';
}

if (!empty($classesBody))
{
	$classesBody = ' class="' . $classesBody . '"';
}

if (!empty($idBody))
{
	$idBody = ' id="' . $idBody . '"';
}

$siteEstEnMaintenance = siteEstEnMaintenance($racine . '/.htaccess');

if ($siteEstEnMaintenance)
{
	$noticeMaintenance = noticeMaintenance();
}

$lienPiwik = '';
$cheminPiwik = cheminXhtml($racine, array ($langue, $langueParDefaut), 'piwik');

if (!empty($cheminPiwik))
{
	$contenuPiwik = @file_get_contents($cheminPiwik);
	
	if ($contenuPiwik !== FALSE && preg_match('#var pkBaseURL.+?' . preg_quote($urlRacine) . '/([^/]+)#', $contenuPiwik, $resultat))
	{
		$lienPiwik = '<li><a href="' . $urlRacine . '/' . $resultat[1] . '">' . T_("Piwik") . "</a> | </li>\n";
	}
}

// Menu.
ob_start();
include adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'menu');
$menu = ob_get_contents();
ob_end_clean();
$menu = lienActif($urlRacine, $menu, FALSE);

########################################################################
##
## Ajouts dans `$adminBalisesLinkScript`.
##
########################################################################

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau) || $boitesDeroulantesAlaMain)
{
	$adminBalisesLinkScript[] = "$url#css#$urlRacine/css/boites-deroulantes.css";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.cookie.js";
	
	if (!empty($boitesDeroulantesTableau))
	{
		$jsDirect = '';
		
		foreach ($boitesDeroulantesTableau as $boiteDeroulante)
		{
			$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante', '');});\n";
		}
		
		$adminBalisesLinkScript[] = "$url#jsDirect#$jsDirect";
	}
}

// Coloration syntaxique lors de l'édition.
if ($adminColorationSyntaxique && isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']))
{
	$mode = '';
	$modesAinclure = array ();
	
	if (preg_match('/\.css$/', $_GET['valeur']))
	{
		$mode = 'css';
		$modesAinclure = array ('css');
	}
	elseif (preg_match('/\.js$/', $_GET['valeur']))
	{
		$mode = 'javascript';
		$modesAinclure = array ('javascript');
	}
	elseif (preg_match('/\.xml$/', $_GET['valeur']))
	{
		$mode = 'xml';
		$modesAinclure = array ('xml');
	}
	elseif (preg_match('/\.ini(\.txt)?$/', $_GET['valeur']))
	{
		$mode = 'properties';
		$modesAinclure = array ('properties');
	}
	elseif (preg_match('/\.php$/', $_GET['valeur']))
	{
		$mode = 'php';
		$modesAinclure = array ('xml', 'javascript', 'css', 'clike', 'php');
	}
	elseif (preg_match('/\.(markdown|md|mkd)$/', $_GET['valeur']))
	{
		$mode = 'markdown';
		$modesAinclure = array ('xml', 'markdown');
	}
	elseif (preg_match('/\.html?$/', $_GET['valeur']))
	{
		$mode = 'htmlmixed';
		$modesAinclure = array ('xml', 'javascript', 'css', 'htmlmixed');
	}
	
	if (!empty($mode))
	{
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/CodeMirror/lib/codemirror.js";
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#css#$urlRacineAdmin/js/CodeMirror/lib/codemirror.css";
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#css#$urlRacineAdmin/js/CodeMirror/theme/rubyblue.css";
		
		foreach ($modesAinclure as $modeAinclure)
		{
			$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/CodeMirror/mode/$modeAinclure/$modeAinclure.js";
		}
		
		$jsDirect = "var editor = CodeMirror.fromTextArea(document.getElementById('code'), {
			mode: '$mode',
			theme: 'rubyblue',
			lineNumbers: true,
			indentUnit: 4,
			smartIndent: false,
			indentWithTabs: true,
			lineWrapping: true
		});";
		$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php*#jsDirect#$jsDirect";
	}
}

// Table des matières.

if ($tableDesMatieres)
{
	$adminBalisesLinkScript[] = "$url#css#$urlRacine/css/table-des-matieres.css";
	$adminBalisesLinkScript[] = "$url#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	$adminBalisesLinkScript[] = "$url#csslteIE7#$urlRacine/css/table-des-matieres-ie6-7.css";
	$adminBalisesLinkScript[] = "$url#cssIE8#$urlRacine/css/table-des-matieres-ie8.css";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery-tableofcontents/jquery.tableofcontents.js";
	$adminBalisesLinkScript[] = "$url#jsDirect#tableDesMatieres('interieurContenu', '$tDmBaliseTable', '$tDmBaliseTitre', $tDmNiveauDepart, $tDmNiveauArret, '$langue', '$adminLangueParDefaut');";
}

// Variable finale.

$linkScript = linkScript($racine, $urlRacine, $adminFusionnerCssJs, $dossierAdmin, $adminBalisesLinkScript);

########################################################################
##
## Traitement personnalisé optionnel 2 de 2.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/premier.inc.php"))
{
	include "$racine/site/$dossierAdmin/inc/premier.inc.php";
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'page.premier');
?>
