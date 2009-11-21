<?php
list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($coinsArrondisBloc);
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
					echo $codeInterieurBlocHaut;
					include cheminFichierIncHtml($racine, 'menu-langues', $langueParDefaut, $langue);
					echo $codeInterieurBlocBas;
					echo '</div><!-- /menuLangues -->' . "\n";
				}
				break;
			
			case 'menu':
				echo '<div id="menu" class="bloc">' . "\n";
				echo $codeInterieurBlocHaut;
				include cheminFichierIncHtml($racine, 'menu', $langueParDefaut, $langue);
				echo $codeInterieurBlocBas;
				echo '</div><!-- /menu -->' . "\n";
				echo '<script type="text/javascript">lienActif("menu");</script>' . "\n";
				break;
			
			case 'faire-decouvrir':
				if ($faireDecouvrir && $decouvrir)
				{
					echo '<div id="faireDecouvrir" class="bloc">' . "\n";
					echo $codeInterieurBlocHaut;
					echo '<a href="' . urlPageAvecDecouvrir() . '">' . T_("Faire découvrir à des ami-e-s") . '</a>';
					echo $codeInterieurBlocBas;
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
				if (($idGalerie && $rss) || ($galerieFluxRssGlobal && adminCheminConfigFluxRssGlobalGaleries($racine)) || ($siteFluxRssGlobal && file_exists("$racine/site/inc/rss-global-site.pc")))
				{
					echo '<div class="sep"></div>' . "\n";
					echo '<div id="fluxRss" class="bloc">' . "\n";
					echo $codeInterieurBlocHaut;
					echo "\t<ul>\n";
					if ($idGalerie && $rss)
					{
						echo "\t\t<li>" . lienFluxRss($urlFlux, $idGalerie, TRUE) . "</li>\n";
					}
					if ($galerieFluxRssGlobal && adminCheminConfigFluxRssGlobalGaleries($racine))
					{
						echo "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=galeries&langue=" . LANGUE, FALSE, TRUE) . "</li>\n";
					}
					if ($siteFluxRssGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
					{
						echo "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=site&langue=" . LANGUE, FALSE, FALSE) . "</li>\n";
					}
					echo "\t</ul>\n";
					echo $codeInterieurBlocBas;
					echo '</div><!-- /fluxRss -->' . "\n";
				}
				break;
				
			default:
				if (cheminFichierIncHtml($racine, $blocAinserer, $langueParDefaut, $langue, FALSE))
				{
					echo "<div class=\"bloc $blocAinserer\">\n";
					echo $codeInterieurBlocHaut;
					include cheminFichierIncHtml($racine, $blocAinserer, $langueParDefaut, $langue);
					echo $codeInterieurBlocBas;
					echo "</div><!-- /class=$blocAinserer -->\n";
				}
				break;
		}
	}
}
?>
