<?php
/*
Ce fichier construit le code des blocs. Après son inclusion, le tableau `$blocs` est prêt à être utilisé. Aucun code XHTML n'est envoyé au navigateur.
*/

// Vérification de l'état du module «Envoyer à des amis».
include $racine . '/inc/envoyer-amis.inc.php';

$blocsAinsererTemp = blocs($ordreBlocsDansFluxHtml, $nombreDeColonnes, $premierOuDernier);
$blocs = array (
	100 => '',
	200 => '',
	300 => '',
	400 => '',
	500 => '',
	600 => '',
);

if (!empty($blocsAinsererTemp))
{
	$blocsAinserer = array ();
	
	foreach ($blocsAinsererTemp as $region => $blocsParRegion)
	{
		$blocsAinserer[$region] = array ();
		
		foreach ($blocsParRegion as $blocAinserer)
		{
			if (!isset($conditionsBlocs[$blocAinserer]) || eval($conditionsBlocs[$blocAinserer]))
			{
				$blocsAinserer[$region][] = $blocAinserer;
			}
		}
	}
	
	foreach ($blocsAinserer as $region => $blocsParRegion)
	{
		foreach ($blocsParRegion as $blocAinserer)
		{
			switch ($blocAinserer)
			{
				case 'balise-h1':
					if (!empty($baliseH1))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="baliseH1" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						
						if ($titreGalerieGenere)
						{
							$h1 = '<h1 id="galerieTitre">' . $baliseH1 . "</h1>\n";
							
							if (!empty($sousTitreGalerie))
							{
								$h1 .= '<p id="galerieSousTitre">' . $sousTitreGalerie . "</p>\n";
							}
						}
						else
						{
							$h1 = "<h1>$baliseH1</h1>\n";
						}
						
						$blocs[$region] .= $h1;
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#baliseH1 -->' . "\n";
					}
					
					break;
					
				case 'envoyer-amis':
					if ($envoyerAmis && $envoyerAmisEstActif)
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="envoyerAmis" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						$blocs[$region] .= '<a href="' . urlPageAvecEnvoyerAmis() . '">' . T_("Envoyer à des amis") . '</a>';
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#envoyerAmis -->' . "\n";
					}
					
					break;
					
				case 'flux-rss':
					if (($idCategorie && $rssCategorie) || ($idGalerie && $rssGalerie) || $fluxRssGlobalGaleriesActif || $fluxRssGlobalSiteActif)
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
					
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="fluxRss" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						$blocs[$region] .= '<h2>' . T_("Flux RSS") . "</h2>\n";
						
						$blocs[$region] .= "<ul>\n";
						
						if ($fluxRssGlobalSiteActif)
						{
							$blocs[$region] .= '<li><a href="' . "$urlRacine/rss.php?type=site&amp;langue=" . LANGUE . '">' . T_("Dernières publications") . "</a></li>\n";
						}
						
						if ($fluxRssGlobalGaleriesActif)
						{
							$blocs[$region] .= '<li><a href="' . "$urlRacine/rss.php?type=galeries&amp;langue=" . LANGUE . '">' . T_("Derniers ajouts aux galeries") . "</a></li>\n";
						}
						
						if (!empty($idGalerie) && $rssGalerie)
						{
							$blocs[$region] .= '<li><a href="' . "$urlRacine/rss.php?type=galerie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlSansIndexSansGet) . '">' . sprintf(T_("Galerie %1\$s"), "<em>$idGalerie</em>") . "</a></li>\n";
						}
						
						if (!empty($idCategorie) && $rssCategorie)
						{
							if (strpos($url, $urlRacine . '/categorie.php?id=') !== FALSE)
							{
								$blocs[$region] .= '<li><a href="' . $urlRacine . '/rss.php?type=categorie&amp;id=' . filtreChaine($racine, $idCategorie) . '">' . sprintf(T_("Catégorie %1\$s"), "<em>$idCategorie</em>") . "</a></li>\n";
							}
							else
							{
								$blocs[$region] .= '<li><a href="' . "$urlRacine/rss.php?type=categorie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlSansIndexSansGet) . '">' . sprintf(T_("Catégorie %1\$s"), "<em>$idCategorie</em>") . "</a></li>\n";
							}
						}
						
						$blocs[$region] .= "</ul>\n";
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#fluxRss -->' . "\n";
					}
					
					break;
					
				case 'infos-publication':
					if ($infosPublication && !$erreur404 && !$estPageDerreur && empty($courrielContact) && empty($idCategorie) && empty($idGalerie))
					{
						$listeCategoriesPage = categories($racine, $urlRacine, $url, $langueParDefaut);
						$bloc = infosPublication($urlRacine, $auteur, $dateCreation, $dateRevision, $listeCategoriesPage);
					
						if (!empty($bloc))
						{
							list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
							if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
							{
								$classeBlocArrondi = ' blocArrondi';
							}
							else
							{
								$classeBlocArrondi = '';
							}
				
							$blocs[$region] .= '<div id="infosPublication" class="bloc' . $classeBlocArrondi . '">' . "\n";
							$blocs[$region] .= $codeInterieurBlocHaut;
							$blocs[$region] .= $bloc;
							$blocs[$region] .= $codeInterieurBlocBas;
							$blocs[$region] .= '</div><!-- /#infosPublication -->' . "\n";
						}
					}
						
					break;
					
				case 'legende-image-galerie':
					if (!empty($tableauCorpsGalerie['texteIntermediaire']) && $galerieLegendeEmplacement[$nombreDeColonnes] == 'bloc')
					{
						$bloc = $tableauCorpsGalerie['texteIntermediaire'];
					
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = lienActif($bloc, FALSE, 'li');
						}
					
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
					
						$blocs[$region] .= $bloc;
					}
					
					break;
					
				case 'licence':
					if (!empty($licence) && !$erreur404 && !$estPageDerreur && empty($courrielContact))
					{
						$bloc = '';
						$licenceTableau = explode(' ', $licence);
					
						foreach ($licenceTableau as $choixLicence)
						{
							$codeLicence = licence($urlRacine, $choixLicence);
							
							if (!empty($codeLicence))
							{
								$bloc .= "<li>$codeLicence</li>\n";
							}
						}
					
						if (!empty($bloc))
						{
							list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
					
							if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
							{
								$classeBlocArrondi = ' blocArrondi';
							}
							else
							{
								$classeBlocArrondi = '';
							}
					
							$blocs[$region] .= '<div id="licence" class="bloc' . $classeBlocArrondi . '">' . "\n";
							$blocs[$region] .= $codeInterieurBlocHaut;
							$blocs[$region] .= T_("Sauf avis contraire:") . "\n";
							
							$blocs[$region] .= "<ul>\n";
							$blocs[$region] .= $bloc;
							$blocs[$region] .= "</ul>\n";
							$blocs[$region] .= $codeInterieurBlocBas;
							$blocs[$region] .= '</div><!-- /#licence -->' . "\n";
						}
					}
					
					break;
					
				case 'lien-page':
					if ($lienPage && !$erreur404 && !$estPageDerreur && empty($courrielContact))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="lienPage" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						$blocs[$region] .= '<h2>' . T_("Faire un lien vers cette page") . "</h2>\n";
						$blocs[$region] .= '<p>' . T_("Ajoutez le code ci-dessous sur votre site:") . "</p>\n";
						$codeLienPage = '<a href="' . urlPageSansEnvoyerAmis() . '">' . $baliseTitle . $baliseTitleComplement . '</a>';
						$blocs[$region] .= '<pre><code>' . securiseTexte($codeLienPage) . "</code></pre>\n";
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#lienPage -->' . "\n";
					}
					
					break;
					
				case 'menu':
					list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
					if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
					{
						$classeBlocArrondi = ' blocArrondi';
					}
					else
					{
						$classeBlocArrondi = '';
					}
				
					$blocs[$region] .= '<div id="menu" class="bloc' . $classeBlocArrondi . '">' . "\n";
					$blocs[$region] .= $codeInterieurBlocHaut;
				
					ob_start();
					include_once cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu');
					$bloc = ob_get_contents();
					ob_end_clean();
				
					if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
					{
						$bloc = lienActif($bloc, FALSE, 'li');
					}
				
					if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
					{
						$bloc = limiteProfondeurListe($bloc);
					}
				
					$blocs[$region] .= $bloc;
					$blocs[$region] .= $codeInterieurBlocBas;
					$blocs[$region] .= '</div><!-- /#menu -->' . "\n";
					break;
					
				case 'menu-categories':
					$bloc = '';
					$cheminMenuCategories = cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu-categories');
					$cheminConfigCategories = cheminConfigCategories($racine);
					
					if (!empty($cheminMenuCategories))
					{
						ob_start();
						include_once $cheminMenuCategories;
						$bloc = ob_get_contents();
						ob_end_clean();
					}
					elseif ($genererMenuCategories && $cheminConfigCategories && ($categories = super_parse_ini_file($cheminConfigCategories, TRUE)) !== FALSE)
					{
						$bloc = menuCategoriesAutomatise($racine, $urlRacine, $langueParDefaut, LANGUE, $categories, $afficherNombreArticlesCategorie, $activerCategoriesGlobales, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
						
						if (!empty($bloc))
						{
							$bloc = '<h2>' . T_("Catégories") . "</h2>\n<ul>\n$bloc</ul>\n";
						}
					}
					
					if (!empty($bloc))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="menuCategories" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = categoriesActives($bloc, $listeCategoriesPage, $idCategorie);
						}
						
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#menuCategories -->' . "\n";
					}
						
					break;
					
				case 'menu-langues':
					if (count($accueil) > 1)
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="menuLangues" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
					
						ob_start();
						include_once cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu-langues');
						$bloc = ob_get_contents();
						ob_end_clean();
					
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = langueActive($bloc, LANGUE, $accueil);
						}
					
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
					
						$blocs[$region] .= $bloc;
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#menuLangues -->' . "\n";
					}
						
					break;
					
				case 'partage':
					$listePartage = partage($url, $baliseTitle . $baliseTitleComplement);
					
					if ($partage && !empty($listePartage) && !$erreur404 && !$estPageDerreur && empty($courrielContact))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="partage" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						$blocs[$region] .= '<h2 class="bDtitre">' . T_("Partager") . "</h2>\n";
						
						$blocs[$region] .= "<ul class=\"bDcorps\">\n";
						
						foreach ($listePartage as $service)
						{
							$blocs[$region] .= '<li><a href="' . $service['lien'] . '" rel="nofollow">' . $service['nom'] . "</a></li>\n";
						}
						
						$blocs[$region] .= "</ul>\n";
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#partage -->' . "\n";
						$blocs[$region] .= '<script type="text/javascript">' . "\n";
						$blocs[$region] .= "//<![CDATA[\n";
						$blocs[$region] .= "boiteDeroulante('#partage', \"$aExecuterApresClicBd\");\n";
						$blocs[$region] .= "//]]>\n";
						$blocs[$region] .= "</script>\n";
					}
					
					break;
					
				case 'piwik':
					$cheminPiwik = cheminXhtml($racine, array ($langue, $langueParDefaut), 'piwik');
					
					if (!empty($cheminPiwik))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="piwik" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						
						ob_start();
						include_once $cheminPiwik;
						$bloc = ob_get_contents();
						ob_end_clean();
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#piwik -->' . "\n";
					}
					
					break;
					
				case 'recherche-google':
					if ($activerRechercheGoogle)
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
				
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
						
						$blocs[$region] .= '<div id="rechercheGoogle" class="bloc' . $classeBlocArrondi . '">' . "\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
						$blocs[$region] .= '<h2>' . T_("Rechercher dans le site") . "</h2>\n";
						$blocs[$region] .= '<form method="get" action="http://www.google.' . $rechercheGoogleExtension . '/search">' . "\n";
						$blocs[$region] .= "<div>\n";
						$blocs[$region] .= '<input id="inputMotsCles" type="text" name="as_q" maxlength="255" />' . "\n";
						$blocs[$region] .= '<input type="submit" value="' . T_("Rechercher") . '" />' . "\n";
						$blocs[$region] .= '<input type="hidden" name="as_sitesearch" value="' . securiseTexte($_SERVER['SERVER_NAME']) . '" />' . "\n";
						$blocs[$region] .= "</div>\n";
						$blocs[$region] .= "</form>\n";
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= '</div><!-- /#rechercheGoogle -->' . "\n";
					}
					
					break;
					
				// Blocs personnalisés.
				default:
					if (cheminXhtml($racine, array ($langue, $langueParDefaut), $blocAinserer, FALSE))
					{
						list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes);
					
						if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $blocAinserer, $nombreDeColonnes))
						{
							$classeBlocArrondi = ' blocArrondi';
						}
						else
						{
							$classeBlocArrondi = '';
						}
					
						$blocs[$region] .= "<div class=\"bloc$classeBlocArrondi $blocAinserer\">\n";
						$blocs[$region] .= $codeInterieurBlocHaut;
					
						ob_start();
						include_once cheminXhtml($racine, array ($langue, $langueParDefaut), $blocAinserer);
						$bloc = ob_get_contents();
						ob_end_clean();
					
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = lienActif($bloc, FALSE, 'li');
						}
					
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
					
						$blocs[$region] .= $bloc;
						$blocs[$region] .= $codeInterieurBlocBas;
						$blocs[$region] .= "</div><!-- /.$blocAinserer -->\n";
					}
					
					break;
			}
		}
	}
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/blocs.inc.php'))
{
	include_once $racine . '/site/inc/blocs.inc.php';
}
?>
