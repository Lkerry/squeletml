<?php
########################################################################
##
## Affectations.
##
########################################################################

$cheminBasDePage = adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'bas-de-page');

########################################################################
##
## Ajouts dans `$adminBalisesLinkScriptFinales`.
##
########################################################################

if (!adminEstIe() && $adminAideEdition != 'BUEditor')
{
	$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php*#jsDirect#SET_DHTML('redimensionnable' + RESIZABLE);";
}

if ($adminAideEdition == 'CodeMirror')
{
	if (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.css$|', $_GET['valeur']))
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: "parsecss.js",
				stylesheet: "js/CodeMirror/css/csscolors.css",
				path: "js/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php*#jsDirect#$jsDirect";
	}
	elseif (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.js$|', $_GET['valeur']))
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
				stylesheet: "js/CodeMirror/css/jscolors.css",
				path: "js/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php*#jsDirect#$jsDirect";
	}
	else
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
				stylesheet: ["js/CodeMirror/css/xmlcolors.css", "js/CodeMirror/css/jscolors.css", "js/CodeMirror/css/csscolors.css", "js/CodeMirror/contrib/php/css/phpcolors.css"],
				path: "js/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php*#jsDirect#$jsDirect";
	}
}

// Variable finale.
$linkScriptFin = linkScript($adminBalisesLinkScriptFinales);

########################################################################
##
## Traitement personnalisé optionnel.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/dernier.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/dernier.inc.php";
}

########################################################################
##
## Code XHTML 2 de 2.
##
########################################################################

include_once adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'page.dernier');
?>
