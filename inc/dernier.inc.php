		<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
			<?php echo $tableauCorpsGalerie['corpsGalerie']; ?>
		<?php endif; ?>
		
		<?php if ((isset($courrielContact) && !empty($courrielContact)) ||
		($decouvrir && $decouvrirInclureContact)): ?>
			<?php include $racine . '/inc/contact.inc.php'; ?>
		<?php endif; ?>
		
		<?php if ($idGalerie): ?>
			</div><!-- /galerie -->
		<?php endif; ?>
	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

<div id="sousContenu">
	<?php
	$divSurSousContenu = 'sous';
	include $racine . '/inc/partage-premier-dernier.inc.php';
	?>
</div><!-- /sousContenu -->

<?php if ($basDePage): ?>
	<div class="sep"></div>
	<div id="basDePage">
		<?php include cheminFichierIncHtml($racine, 'bas-de-page', $langueParDefaut, $langue); ?>
	</div><!-- /basDePage -->
<?php endif; ?>

	</div><!-- /interieurPage -->
</div><!-- /page -->

<script type="text/javascript">
egaliseHauteur('interieurPage', 'surContenu', 'sousContenu');
</script>

</body>
</html>
