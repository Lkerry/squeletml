<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Fichiers Sitemap");
$boitesDeroulantes = '#configActuelleAdminSitemapSite #optionsAjoutAdminSitemap';
$boitesDeroulantes .= ' .aideAdminSitemap .contenuFichierPourSauvegarde .sitemapBalisesOptionnelles .sitemapImage';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des fichiers Sitemap"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_POST['sitemap']))
	{
		if ($_POST['sitemap'] == 'ajoutAutomatique')
		{
			$messagesScript = '';
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Ajout automatique des pages du site dans le fichier Sitemap") . "</h3>\n";
			
			$listeUrl = adminListeUrl($racine, $urlRacine, $accueil, $activerCategoriesGlobales, $nombreArticlesParPageCategorie, $nombreItemsFluxRss, $activerFluxRssGlobalSite, $galerieActiverFluxRssGlobal, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieVignettesParPage, $activerGalerieDemo);
			$contenuSitemap = adminGenereContenuSitemap($listeUrl);
			$messagesScript .= adminEnregistreSitemap($racine, $contenuSitemap);
			echo adminMessagesScript($messagesScript);
			echo "</div><!-- /.sousBoite -->\n";
		}
		elseif ($_POST['sitemap'] == 'robots')
		{
			$messagesScript = '';
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Vérification de la déclaration du fichier Sitemap dans le fichier <code>robots.txt</code>") . "</h3>\n" ;
			
			$messagesScript .= adminDeclareSitemapDansRobots($racine, $urlRacine);
			echo adminMessagesScript($messagesScript);
			echo "</div><!-- /.sousBoite -->\n";
		}
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite">
	<h2 id="config"><?php echo T_("Configuration actuelle"); ?></h2>
	
	<ul>
		<?php if ($ajouterPagesParCronDansSitemap): ?>
			<li><?php echo T_("L'ajout de pages par le cron dans le fichier Sitemap est activé") . ' (<code>$ajouterPagesParCronDansSitemap = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("L'ajout de pages par le cron dans le fichier Sitemap n'est pas activé") . ' (<code>$ajouterPagesParCronDansSitemap = FALSE;</code>).'; ?></li>
		<?php endif; ?>
	</ul>
	
	<p><a href="porte-documents.admin.php?action=editer&amp;valeur=<?php echo encodeTexteGet('../site/' . $dossierAdmin . '/inc/config.inc.php'); ?>#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
</div><!-- /.boite -->

<div class="boite">
	<h2 id="actions"><?php echo T_("Fichiers Sitemap"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<ul>
					<li><input id="inputSitemapAjoutAutomatique" type="radio" name="sitemap" value="ajoutAutomatique" checked="checked" /> <label for="inputSitemapAjoutAutomatique"><?php echo T_("Ajouter automatiquement les pages du site dans le fichier Sitemap"); ?></label></li>
					
					<li><input id="inputSitemapRobots" type="radio" name="sitemap" value="robots" /> <label for="inputSitemapRobots"><?php printf(T_("Vérifier la déclaration du fichier Sitemap dans le fichier %1\$s"), '<code>robots.txt</code>'); ?></label></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="action" value="<?php echo T_('Choisir'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
