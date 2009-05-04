	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

<?php if ($menuLanguesSousLeContenu): ?>
	<div id="menuLangues">
		<?php include fichierMenuLangues($racine, $langue); ?>
	</div><!-- /menuLangues -->
<?php endif; ?>

<?php if ($menuSousLeContenu): ?>
	<div id="menu">
		<?php include fichierMenu($racine, $langue); ?>
	</div><!-- /menu -->
<?php endif; ?>

<?php if ($basDePage): ?>
	<div id="basDePage">
		<?php include fichierBasDePage($racine, $langue); ?>
	</div><!-- /basDePage -->
<?php endif; ?>

</div><!-- /page -->

</body>
</html>
