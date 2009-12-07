<?php
/**
Ce fichier construit le code des blocs. Après son inclusion, la variable `$blocs` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Vérification de l'état du module «Faire découvrir».
include $racine . '/inc/faire-decouvrir.inc.php';

list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondis);
$blocsAinserer = blocs($ordreBlocsDansFluxHtml, $divSurSousContenu . 'Contenu');

$blocs = '';

if (!empty($blocsAinserer))
{
	foreach ($blocsAinserer as $blocAinserer)
	{
		switch ($blocAinserer)
		{
			case 'menu-langues':
				if (count($accueil) > 1)
				{
					$blocs .= '<div id="menuLangues" class="bloc">' . "\n";
					$blocs .= $codeInterieurBlocHaut;
					
					ob_start();
					include_once cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'menu-langues');
					$blocs .= ob_get_contents();
					ob_end_clean();
					
					$blocs .= $codeInterieurBlocBas;
					$blocs .= '</div><!-- /#menuLangues -->' . "\n";
				}
				
				break;
			
			case 'menu':
				$blocs .= '<div id="menu" class="bloc">' . "\n";
				$blocs .= $codeInterieurBlocHaut;
				
				ob_start();
				include_once cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'menu');
				$blocs .= ob_get_contents();
				ob_end_clean();
				
				$blocs .= $codeInterieurBlocBas;
				$blocs .= '</div><!-- /#menu -->' . "\n";
				$blocs .= '<script type="text/javascript">lienActif("menu");</script>' . "\n";
				break;
			
			case 'faire-decouvrir':
				if ($activerFaireDecouvrir && $decouvrir)
				{
					$blocs .= '<div id="faireDecouvrir" class="bloc">' . "\n";
					$blocs .= $codeInterieurBlocHaut;
					$blocs .= '<a href="' . urlPageAvecDecouvrir() . '">' . T_("Faire découvrir à des ami-e-s") . '</a>';
					$blocs .= $codeInterieurBlocBas;
					$blocs .= '</div><!-- /#faireDecouvrir -->' . "\n";
				}
				
				break;
				
			case 'legende-oeuvre-galerie':
				if (!empty($tableauCorpsGalerie['texteIntermediaire']) && $galerieLegendeEmplacement == $divSurSousContenu . 'Contenu')
				{
					$blocs .= $tableauCorpsGalerie['texteIntermediaire'];
				}
				
				break;
				
			case 'flux-rss':
				if (($idGalerie && $rss) || ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries')) || ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site')))
				{
					$blocs .= '<div id="fluxRss" class="bloc">' . "\n";
					$blocs .= $codeInterieurBlocHaut;
					$blocs .= "\t<ul>\n";
					
					if ($idGalerie && $rss)
					{
						$blocs .= "\t\t<li>" . lienFluxRss($urlFlux, $idGalerie, TRUE) . "</li>\n";
					}
					
					if ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries'))
					{
						$blocs .= "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=galeries&langue=" . LANGUE, FALSE, TRUE) . "</li>\n";
					}
					
					if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
					{
						$blocs .= "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=site&langue=" . LANGUE, FALSE, FALSE) . "</li>\n";
					}
					
					$blocs .= "\t</ul>\n";
					$blocs .= $codeInterieurBlocBas;
					$blocs .= '</div><!-- /#fluxRss -->' . "\n";
				}
				
				break;
				
			default:
				if (cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), $blocAinserer, FALSE))
				{
					$blocs .= "<div class=\"bloc $blocAinserer\">\n";
					$blocs .= $codeInterieurBlocHaut;
					
					ob_start();
					include_once cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), $blocAinserer);
					$blocs .= ob_get_contents();
					ob_end_clean();
					
					$blocs .= $codeInterieurBlocBas;
					$blocs .= "</div><!-- /.$blocAinserer -->\n";
				}
				
				break;
		}
	}
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/blocs.inc.php'))
{
	include_once $racine . '/site/inc/blocs.inc.php';
}
?>
