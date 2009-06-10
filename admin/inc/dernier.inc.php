	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

</div><!-- /page -->

<?php if (adminNomPageEnCours($_SERVER['PHP_SELF']) == 'porte-documents.admin.php'): ?>
<script type="text/javascript">
<!--
SET_DHTML("redimensionnable"+RESIZABLE);
//-->
</script>

<?php if ($colorationSyntaxique): ?>
<script type="text/javascript">
var editor = CodeMirror.fromTextArea('code', {
	parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
	stylesheet: ["inc/CodeMirror/css/xmlcolors.css", "inc/CodeMirror/css/jscolors.css", "inc/CodeMirror/css/csscolors.css", "inc/CodeMirror/contrib/php/css/phpcolors.css"],
	path: "inc/CodeMirror/js/",
	continuousScanning: 500,
	disableSpellcheck: false,
	indentUnit: 4,
	tabMode: "shift",
	height: "93%",
	iframeClass: "editeur",
});
</script>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
