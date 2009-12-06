<?php
########################################################################
##
## Ajouts dans `$adminBalisesLinkScriptFinales`.
##
########################################################################

if (!adminEstIe())
{
	$adminBalisesLinkScriptFinales[] = "$urlRacineAdmin/porte-documents.admin.php#jsDirect#SET_DHTML('redimensionnable' + RESIZABLE);";
}

if ($adminColorationSyntaxique)
{
	if (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.css$|', $_GET['valeur']))
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: "parsecss.js",
				stylesheet: "inc/CodeMirror/css/csscolors.css",
				path: "inc/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacine/porte-documents.admin.php#jsDirect#$jsDirect";
	}
	elseif (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.js$|', $_GET['valeur']))
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
				stylesheet: "inc/CodeMirror/css/jscolors.css",
				path: "inc/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacine/porte-documents.admin.php#jsDirect#$jsDirect";
	}
	else
	{
		$jsDirect = <<<JS
			var editor = CodeMirror.fromTextArea('code', {
				parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
				stylesheet: ["inc/CodeMirror/css/xmlcolors.css", "inc/CodeMirror/css/jscolors.css", "inc/CodeMirror/css/csscolors.css", "inc/CodeMirror/contrib/php/css/phpcolors.css"],
				path: "inc/CodeMirror/js/",
				continuousScanning: 500,
				disableSpellcheck: false,
				indentUnit: 4,
				tabMode: "shift",
				height: "93%",
				iframeClass: "editeur"
			});
JS;
		$adminBalisesLinkScriptFinales[] = "$urlRacine/porte-documents.admin.php#jsDirect#$jsDirect";
	}
}

// Variable finale.
$linkScriptFin = linkScript($adminBalisesLinkScriptFinales);

########################################################################
##
## Code XHTML 2 de 2.
##
########################################################################

include_once adminCheminXhtml($racineAdmin, 'page.dernier');
?>
