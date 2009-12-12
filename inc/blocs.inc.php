<?php
/*
Ce fichier construit le code des blocs. Après son inclusion, la variable `$blocs` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Vérification de l'état du module «Faire découvrir».
include $racine . '/inc/faire-decouvrir.inc.php';

$blocsAinserer = blocs($ordreBlocsDansFluxHtml, $nombreDeColonnes, $divSurSousContenu . 'Contenu');
$blocs = '';

if (!empty($blocsAinserer))
{
	foreach ($blocsAinserer as $blocAinserer)
	{
		switch ($blocAinserer)
		{
			case 'menu-langues':
				list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
				if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
				{
					$classeBlocArrondi = ' blocArrondi';
				}
				else
				{
					$classeBlocArrondi = '';
				}
				
				if (count($accueil) > 1)
				{
					$blocs .= '<div id="menuLangues" class="bloc' . $classeBlocArrondi . '">' . "\n";
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
				list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
				if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
				{
					$classeBlocArrondi = ' blocArrondi';
				}
				else
				{
					$classeBlocArrondi = '';
				}
				
				$blocs .= '<div id="menu" class="bloc' . $classeBlocArrondi . '">' . "\n";
				$blocs .= $codeInterieurBlocHaut;
				
				ob_start();
				include_once cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'menu');
				$blocMenu = ob_get_contents();
				ob_end_clean();
				
				$blocs .= lienActif($blocMenu, FALSE, 'li');
				$blocs .= $codeInterieurBlocBas;
				$blocs .= '</div><!-- /#menu -->' . "\n";
				break;
			
			case 'faire-decouvrir':
				list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
				if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
				{
					$classeBlocArrondi = ' blocArrondi';
				}
				else
				{
					$classeBlocArrondi = '';
				}
				
				if ($activerFaireDecouvrir && $decouvrir)
				{
					$blocs .= '<div id="faireDecouvrir" class="bloc' . $classeBlocArrondi . '">' . "\n";
					$blocs .= $codeInterieurBlocHaut;
					$blocs .= '<a href="' . urlPageAvecDecouvrir() . '">' . T_("Faire découvrir à des ami-e-s") . '</a>';
					$blocs .= $codeInterieurBlocBas;
					$blocs .= '</div><!-- /#faireDecouvrir -->' . "\n";
				}
				
				break;
				
			case 'legende-oeuvre-galerie':
				if (!empty($tableauCorpsGalerie['texteIntermediaire']) && $galerieLegendeEmplacement[$nombreDeColonnes] == $divSurSousContenu . 'Contenu')
				{
					$blocs .= $tableauCorpsGalerie['texteIntermediaire'];
				}
				
				break;
				
			case 'flux-rss':
				if (($idGalerie && $rss) || ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries')) || ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site')))
				{
					list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
					
					if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
					{
						$classeBlocArrondi = ' blocArrondi';
					}
					else
					{
						$classeBlocArrondi = '';
					}
					
					$blocs .= '<div id="fluxRss" class="bloc' . $classeBlocArrondi . '">' . "\n";
					$blocs .= $codeInterieurBlocHaut;
					$blocs .= "\t<ul>\n";
					
					if ($idGalerie && $rss)
					{
						$blocs .= "\t\t<li>" . lienFluxRss($urlFlux, $idGalerie, TRUE) . "</li>\n";
					}
					
					if ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries'))
					{
						$blocs .= "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=galeries&amp;langue=" . LANGUE, FALSE, TRUE) . "</li>\n";
					}
					
					if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
					{
						$blocs .= "\t\t<li>" . lienFluxRss("$urlRacine/rss.php?global=site&amp;langue=" . LANGUE, FALSE, FALSE) . "</li>\n";
					}
					
					$blocs .= "\t</ul>\n";
					$blocs .= $codeInterieurBlocBas;
					$blocs .= '</div><!-- /#fluxRss -->' . "\n";
				}
				
				break;
				
			default:
				if (cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), $blocAinserer, FALSE))
				{
					list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
					
					if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
					{
						$classeBlocArrondi = ' blocArrondi';
					}
					else
					{
						$classeBlocArrondi = '';
					}
					
					$blocs .= "<div class=\"bloc$classeBlocArrondi $blocAinserer\">\n";
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
