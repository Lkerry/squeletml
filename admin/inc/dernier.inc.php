				</div><!-- /interieurContenu -->
			</div><!-- /contenu -->
		</div><!-- /page -->

		<?php if (page() == 'porte-documents.admin.php'): ?>
			<?php if (!adminEstIE()): ?>
				<script type="text/javascript">SET_DHTML('redimensionnable' + RESIZABLE);</script>
			<?php endif; ?>

			<?php if ($adminColorationSyntaxique): ?>
				<?php if (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.css$|', $_GET['valeur'])): ?>
					<script type="text/javascript">
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
					</script>
				<?php elseif (isset($_GET['action']) && $_GET['action'] = 'editer' && isset($_GET['valeur']) && preg_match('|\.js$|', $_GET['valeur'])): ?>
					<script type="text/javascript">
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
					</script>
				<?php else: ?>
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
							iframeClass: "editeur"
						});
					</script>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	</body>
</html>
