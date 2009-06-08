		<?php if (isset($courrielContact) && !empty($courrielContact)): ?>
			<?php include $racine . '/inc/contact.inc.php'; ?>
		<?php endif; ?>
		
		<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
			<?php $tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement); ?>
			<?php echo $tableauCorpsGalerie['corpsGalerie']; ?>
		<?php endif; ?>
		
		<?php if ($idGalerie): ?>
			</div><!-- /galerie -->
		<?php endif; ?>
		
	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

<?php if (!$menuSousLeMenuLangues): ?>
	<?php if ($menuSousLeContenu): ?>
		<div id="menu">
			<?php include fichierMenu($racine, $langue); ?>
		</div><!-- /menu -->
	<?php endif; ?>
<?php endif; ?>

<?php if ($menuLanguesSousLeContenu && count($accueil) > 1): ?>
	<div id="menuLangues">
		<?php include fichierMenuLangues($racine, $langue); ?>
	</div><!-- /menuLangues -->
<?php endif; ?>

<?php if ($menuSousLeMenuLangues): ?>
	<?php if ($menuSousLeContenu): ?>
		<div id="menu">
			<?php include fichierMenu($racine, $langue); ?>
		</div><!-- /menu -->
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($tableauCorpsGalerie['texteGrande'])): ?>
	<?php echo $tableauCorpsGalerie['texteGrande']; ?>
<?php endif; ?>

<?php if ($basDePage): ?>
	<div id="basDePage">
		<?php include fichierBasDePage($racine, $langue); ?>
	</div><!-- /basDePage -->
<?php endif; ?>

</div><!-- /page -->

</body>
</html>
