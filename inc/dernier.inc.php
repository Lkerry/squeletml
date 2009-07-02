		<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
			<?php $tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement); ?>
			<?php echo $tableauCorpsGalerie['corpsGalerie']; ?>
		<?php endif; ?>
		
		<?php
		$decouvrir = FALSE; // Initialisation
		$decouvrirInclureContact = FALSE; // Initialisation
		include $racine . '/inc/faire-decouvrir.inc.php';
		?>
		
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
	<?php if (!$menuSousLeMenuLangues): ?>
		<?php if ($menuSousLeContenu): ?>
			<div id="menu">
				<?php include fichierMenu($racine, $langue); ?>
			</div><!-- /menu -->
			<script type="text/javascript">setPage();</script>
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
			<script type="text/javascript">setPage();</script>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($faireDecouvrir && $decouvrir): ?>
		<div id="faireDecouvrir">
			<?php echo '<a href="' . urlPageAvecDecouvrir() . '">' . T_("Faire découvrir à des ami-e-s") . '</a>'; ?>
		</div><!-- /faireDecouvrir -->
	<?php endif; ?>

	<?php if (!empty($tableauCorpsGalerie['texteGrande'])): ?>
		<?php echo $tableauCorpsGalerie['texteGrande']; ?>
	<?php endif; ?>

	<?php if (($idGalerie && $rss) || $galerieFluxGlobal || $siteFluxGlobal): ?>
		<div class="sep"></div>
		<div id="iconeRss">
			<ul>
				<?php if ($idGalerie && $rss): ?>
					<li><?php echo lienRss($urlFlux, $idGalerie, TRUE); ?></li>
				<?php endif; ?>
		
				<?php if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.txt")): ?>
					<li><?php echo lienRss("$urlRacine/rss.php?global=galeries", FALSE, TRUE); ?></li>
				<?php endif; ?>
			
				<?php if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.txt")): ?>
					<li><?php echo lienRss("$urlRacine/rss.php?global=site", FALSE, FALSE); ?></li>
				<?php endif; ?>
			</ul>
		</div><!-- /iconeRss -->
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
