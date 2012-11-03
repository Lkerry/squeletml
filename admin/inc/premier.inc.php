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

extract(init('', 'baliseH1', 'baliseTitle', 'getValeur', 'h1'), EXTR_SKIP);

if (!isset($actionEditer))
{
	$actionEditer = FALSE;
}

if (!isset($adminBalisesLinkScriptFinales))
{
	$adminBalisesLinkScriptFinales = array ();
}

if (!empty($baliseTitle))
{
	$baliseTitle = securiseTexte(supprimeBalisesHtml($baliseTitle));
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
list ($contenuDoctype, $ouvertureBaliseHtml) = doctype($adminDoctype, eval(LANGUE_ADMIN));
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
	
	if ($contenuPiwik !== FALSE && preg_match('#var pkBaseURL.+?' . preg_quote($urlRacine, '#') . '/([^/]+)#', $contenuPiwik, $resultat))
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
if ($adminColorationSyntaxique && (($actionEditer && !empty($getValeur)) || (isset($_POST['porteDocumentsCreation']) && isset($_POST['porteDocumentsCreationType']) && ($_POST['porteDocumentsCreationType'] == 'FichierVide' || $_POST['porteDocumentsCreationType'] == 'FichierModeleHtml'))))
{
	$valeurAcomparer = $getValeur;
	
	if (isset($_POST['porteDocumentsCreation']))
	{
		$retourAdminCheminFichierAcreerPorteDocuments = adminCheminFichierAcreerPorteDocuments($adminDossierRacinePorteDocuments);
		$valeurAcomparer = $retourAdminCheminFichierAcreerPorteDocuments['cheminFichier'];
	}
	
	$mode = '';
	$modesAinclure = array ();
	
	if (preg_match('/\.css$/', $valeurAcomparer))
	{
		$mode = 'css';
		$modesAinclure = array ('css');
	}
	elseif (preg_match('/\.js$/', $valeurAcomparer))
	{
		$mode = 'javascript';
		$modesAinclure = array ('javascript');
	}
	elseif (preg_match('/\.xml$/', $valeurAcomparer))
	{
		$mode = 'xml';
		$modesAinclure = array ('xml');
	}
	elseif (preg_match('/\.ini(\.txt)?$/', $valeurAcomparer))
	{
		$mode = 'properties';
		$modesAinclure = array ('properties');
	}
	elseif (preg_match('/\.php$/', $valeurAcomparer))
	{
		$mode = 'php';
		$modesAinclure = array ('xml', 'javascript', 'css', 'clike', 'php');
	}
	elseif (preg_match('/\.(markdown|md|mkd)$/', $valeurAcomparer))
	{
		$mode = 'markdown';
		$modesAinclure = array ('xml', 'markdown');
	}
	elseif (preg_match('/\.html?$/', $valeurAcomparer))
	{
		$mode = 'htmlmixed';
		$modesAinclure = array ('xml', 'javascript', 'css', 'htmlmixed');
	}
	
	if (!empty($mode))
	{
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/CodeMirror/lib/codemirror.js";
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#css#$urlRacineAdmin/js/CodeMirror/lib/codemirror.css";
		$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#css#$urlRacineAdmin/js/CodeMirror/theme/gedit.css";
		
		foreach ($modesAinclure as $modeAinclure)
		{
			$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/CodeMirror/mode/$modeAinclure/$modeAinclure.js";
		}
		
		$jsDirect = "var editor = CodeMirror.fromTextArea(document.getElementById('code'), {
			mode: '$mode',
			theme: 'gedit',
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
