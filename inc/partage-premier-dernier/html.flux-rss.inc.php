<div class="sep"></div>
<div id="fluxRss">
	<ul>
		<?php if ($idGalerie && $rss): ?>
			<li><?php echo lienRss($urlFlux, $idGalerie, TRUE); ?></li>
		<?php endif; ?>

		<?php if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc")): ?>
			<li><?php echo lienRss("$urlRacine/rss.php?global=galeries&langue=" . langue($langue), FALSE, TRUE); ?></li>
		<?php endif; ?>
	
		<?php if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc")): ?>
			<li><?php echo lienRss("$urlRacine/rss.php?global=site&langue=" . langue($langue), FALSE, FALSE); ?></li>
		<?php endif; ?>
	</ul>
</div><!-- /fluxRss -->
