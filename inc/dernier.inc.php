	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

<?php if ($menuSousLeContenu): ?>
	<?php afficheMenu($racine, $accueil); ?>
<?php endif; ?>

<?php if ($basDePage): ?>
	<div id="basDePage">
		<?php include $racine . '/inc/html.bas-de-page.inc.php'; ?>
	</div><!-- /basDePage -->
<?php endif; ?>

</div><!-- /page -->

</body>
</html>
