		<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
			<?php $tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement); ?>
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
	<?php if (!$menuSousMenuLangues && $menuSousContenu): ?>
		<?php include fichierPartagePremierDernier($racine, 'menu'); ?>
	<?php endif; ?>

	<?php if (count($accueil) > 1 && $menuLanguesSousContenu): ?>
		<?php include fichierPartagePremierDernier($racine, 'menu-langues'); ?>
	<?php endif; ?>

	<?php if ($menuSousMenuLangues && $menuSousContenu): ?>
		<?php include fichierPartagePremierDernier($racine, 'menu'); ?>
	<?php endif; ?>

	<?php if ($faireDecouvrir && $decouvrir && $faireDecouvrirSousContenu): ?>
		<?php include fichierPartagePremierDernier($racine, 'faire-decouvrir'); ?>
	<?php endif; ?>

	<?php if (!empty($tableauCorpsGalerie['texteIntermediaire'])): ?>
		<?php echo $tableauCorpsGalerie['texteIntermediaire']; ?>
	<?php endif; ?>

	<?php if ((($idGalerie && $rss) || ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc")) || ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))) && ($rssSousContenu)): ?>
		<?php include fichierPartagePremierDernier($racine, 'flux-rss'); ?>
	<?php endif; ?>
</div><!-- /sousContenu -->

<?php if ($basDePage): ?>
	<div class="sep"></div>
	<div id="basDePage">
		<?php include fichierBasDePage($racine, $langue); ?>
	</div><!-- /basDePage -->
<?php endif; ?>

	</div><!-- /interieurPage -->
</div><!-- /page -->

</body>
</html>
