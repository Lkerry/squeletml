<?php
$blocsAinserer = blocs($ordreFluxHtml, $divSurSousContenu . 'Contenu');
if (!empty($blocsAinserer))
{
	foreach ($blocsAinserer as $blocAinserer)
	{
		switch ($blocAinserer)
		{
			case 'menu-langues':
				if (count($accueil) > 1)
				{
					echo '<div id="menuLangues" class="bloc">' . "\n";
						include cheminFichierIncHtml($racine, 'menu-langues', $langueParDefaut, $langue);
					echo '</div><!-- /menuLangues -->' . "\n";
				}
				break;
			
			case 'menu':
				echo '<div id="menu" class="bloc">' . "\n";
					include cheminFichierIncHtml($racine, 'menu', $langueParDefaut, $langue);
				echo '</div><!-- /menu -->' . "\n";
				echo '<script type="text/javascript">setPage();</script>' . "\n";
				break;
			
			case 'faire-decouvrir':
				if ($faireDecouvrir && $decouvrir)
				{
					echo '<div id="faireDecouvrir" class="bloc">' . "\n";
						echo '<a href="' . urlPageAvecDecouvrir() . '">' . T_("Faire découvrir à des ami-e-s") . '</a>';
					echo '</div><!-- /faireDecouvrir -->' . "\n";
				}
				break;
				
			case 'legende-oeuvre-galerie':
				if (!empty($tableauCorpsGalerie['texteIntermediaire']) && $galerieLegendeEmplacement == $divSurSousContenu . 'Contenu')
				{
					echo $tableauCorpsGalerie['texteIntermediaire'];
				}
				break;
				
			case 'flux-rss':
				if ((($idGalerie && $rss) || ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc")) || ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))) && (!$rssSousContenu))
				{
					echo '<div class="sep"></div>' . "\n";
					echo '<div id="fluxRss" classclass="bloc">' . "\n";
					echo "\t<ul>\n";
					if ($idGalerie && $rss)
					{
						echo "\t\t<li>" . lienRss($urlFlux, $idGalerie, TRUE) . "</li>\n";
					}
					if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
					{
						echo "\t\t<li>" . lienRss("$urlRacine/rss.php?global=galeries&langue=" . LANGUE, FALSE, TRUE) . "</li>\n";
					}
					if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
					{
						echo "\t\t<li>" . lienRss("$urlRacine/rss.php?global=site&langue=" . LANGUE, FALSE, FALSE) . "</li>\n";
					}
					echo "\t</ul>\n";
					echo '</div><!-- /fluxRss -->' . "\n";
				}
				break;
				
			default:
				if (cheminFichierIncHtml($racine, $blocAinserer, $langueParDefaut, $langue, FALSE))
				{
					echo "<div class=\"bloc blocPerso $blocAinserer\">\n";
						include cheminFichierIncHtml($racine, $blocAinserer, $langueParDefaut, $langue);
					echo "</div><!-- /class=$blocAinserer -->\n";
				}
				break;
		}
	}
}
?>
