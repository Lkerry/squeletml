<?php
/*
Ce fichier construit le code des blocs. Après son inclusion, le tableau `$blocs` est prêt à être utilisé. Aucun code XHTML n'est envoyé au navigateur.
*/

// Vérification de l'état du module de partage (par courriel).
include $racine . '/inc/partage-courriel.inc.php';

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
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="baliseH1" class="bloc ' . $classesBloc . '">' . "\n";
						
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
						$blocs[$region] .= '</div><!-- /#baliseH1 -->' . "\n";
					}
					
					break;
					
				case 'flux-rss':
					$fluxRssGlobalSiteActif = FALSE;
					
					if ($activerFluxRssGlobalSite)
					{
						$pagesFluxRssGlobalSite = super_parse_ini_file(cheminConfigFluxRssGlobalSite($racine), TRUE);
						
						if (!empty($pagesFluxRssGlobalSite[$codeLangue]))
						{
							$fluxRssGlobalSiteActif = TRUE;
						}
					}
					
					$fluxRssGlobalGaleriesActif = FALSE;
					
					if ($galerieActiverFluxRssGlobal)
					{
						foreach ($accueil as $codeLangue => $infosLangue)
						{
							$itemsFluxRssGaleriesLangue = fluxRssGaleriesTableauBrut($racine, $urlRacine, $codeLangue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
						
							if (!empty($itemsFluxRssGaleriesLangue))
							{
								$fluxRssGlobalGaleriesActif = TRUE;
								break;
							}
						}
					}
					
					if (($idCategorie && $rssCategorie) || ($idGalerie && $rssGalerie) || $fluxRssGlobalGaleriesActif || $fluxRssGlobalSiteActif)
					{
						$boiteDeroulanteAjoutee = FALSE;
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="fluxRss" class="bloc ' . $classesBloc . '">' . "\n";
						$blocs[$region] .= '<h2>' . T_("Flux RSS") . "</h2>\n";
						
						$blocFluxRssIndividuels = '';
						
						if (!empty($idGalerie) && $rssGalerie)
						{
							$blocFluxRssIndividuels .= '<li><a class="fluxRssLien" href="' . "$urlRacine/rss.php?type=galerie&amp;id=" . filtreChaine($idGalerie) . '&amp;langue=' . LANGUE . '">' . sprintf(T_("Galerie %1\$s"), '<em>' . securiseTexte($idGalerie) . '</em>') . "</a></li>\n";
						}
						
						if (!empty($idCategorie) && $rssCategorie)
						{
							$blocFluxRssIndividuels .= '<li><a class="fluxRssLien" href="' . "$urlRacine/rss.php?type=categorie&amp;id=" . filtreChaine($idCategorie) . '">' . sprintf(T_("Catégorie %1\$s"), '<em>' . securiseTexte($idCategorie) . '</em>') . "</a></li>\n";
						}
						
						if (!empty($blocFluxRssIndividuels))
						{
							$blocs[$region] .= "<ul>\n$blocFluxRssIndividuels</ul>\n";
						}
						
						if ($fluxRssGlobalSiteActif || $fluxRssGlobalGaleriesActif)
						{
							$tableauAccueilTrie = triTableauAccueil($accueil, LANGUE);
							
							foreach ($tableauAccueilTrie as $codeLangue => $urlAccueilLangue)
							{
								$blocLangue = '';
								
								if ($fluxRssGlobalSiteActif && isset($pagesFluxRssGlobalSite[$codeLangue]))
								{
									$blocLangue .= "<li><a class=\"fluxRssLien\" href=\"$urlRacine/rss.php?type=site&amp;langue=$codeLangue\">" . T_("Dernières publications") . "</a></li>\n";
								}
								
								if ($fluxRssGlobalGaleriesActif)
								{
									$itemsFluxRssLangue = fluxRssGaleriesTableauBrut($racine, $urlRacine, $codeLangue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
									
									if (!empty($itemsFluxRssLangue))
									{
										$blocLangue .= "<li><a class=\"fluxRssLien\" href=\"$urlRacine/rss.php?type=galeries&amp;langue=$codeLangue\">" . T_("Derniers ajouts aux galeries") . "</a></li>\n";
									}
								}
								
								if (!empty($blocLangue))
								{
									if ($codeLangue != LANGUE)
									{
										$boiteDeroulanteAjoutee = TRUE;
										$blocs[$region] .= "<div class=\"fluxRssLangueAutre\">\n<h3 class=\"bDtitre\">" . codeLangueVersNom($codeLangue) . "</h3>\n<ul class=\"bDcorps masquer\">\n$blocLangue</ul>\n</div><!-- /.menuFluxRssLangue -->\n";
									}
									else
									{
										$blocs[$region] .= "<ul>\n$blocLangue</ul>\n";
									}
								}
							}
						}
						
						$blocs[$region] .= '</div><!-- /#fluxRss -->' . "\n";
						
						if ($boiteDeroulanteAjoutee)
						{
							$blocs[$region] .= '<script type="text/javascript">' . "\n";
							$blocs[$region] .= "//<![CDATA[\n";
							$blocs[$region] .= "boiteDeroulante('.fluxRssLangueAutre', \"$aExecuterApresClicBd\");\n";
							$blocs[$region] .= "//]]>\n";
							$blocs[$region] .= "</script>\n";
						}
					}
					
					break;
					
				case 'infos-publication':
					if ($infosPublication && !$erreur404 && !$estPageDerreur && empty($courrielContact) && empty($idCategorie) && empty($idGalerie))
					{
						$listeCategoriesPage = categories($racine, $urlRacine, $url);
						$bloc = infosPublication($urlRacine, $auteur, $dateCreation, $dateRevision, $listeCategoriesPage);
					
						if (!empty($bloc))
						{
							$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
				
							$blocs[$region] .= '<div id="infosPublication" class="bloc ' . $classesBloc . '">' . "\n";
							$blocs[$region] .= $bloc;
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
							$bloc = lienActif($urlRacine, $bloc, TRUE, 'li');
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
							$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
					
							$blocs[$region] .= '<div id="licence" class="bloc ' . $classesBloc . '">' . "\n";
							$blocs[$region] .= T_("Sauf mention contraire:") . "\n";
							
							$blocs[$region] .= "<ul>\n";
							$blocs[$region] .= $bloc;
							$blocs[$region] .= "</ul>\n";
							$blocs[$region] .= '</div><!-- /#licence -->' . "\n";
						}
					}
					
					break;
					
				case 'lien-page':
					if ($lienPage && !$erreur404 && !$estPageDerreur && empty($courrielContact))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="lienPage" class="bloc ' . $classesBloc . '">' . "\n";
						$blocs[$region] .= '<h2 class="bDtitre">' . T_("Faire un lien vers cette page") . "</h2>\n";
						$blocs[$region] .= "<div class=\"bDcorps\">\n";
						$urlSansAction = variableGet(0, $url, 'action');
						$codeLienPage = '<pre><code>' . securiseTexte('<a href="' . $urlSansAction . '">' . $baliseTitle . $baliseTitleComplement . '</a>') . "</code></pre>\n";
						$liensImage = array ();
						
						if (isset($lienPageVignette) && preg_match('#(<img .+? />)#', $lienPageVignette, $resultat))
						{
							$liensImage['vignette']['description'] = '<p>' . T_("Lien avec l'image en vignette:") . "</p>\n";
							$liensImage['vignette']['balise'] = $resultat[1];
						}
						
						if (isset($lienPageIntermediaire) && preg_match('#(<img .+? />)#', $lienPageIntermediaire, $resultat))
						{
							$liensImage['intermediaire']['description'] = '<p>' . T_("Lien avec l'image en taille intermédiaire:") . "</p>\n";
							$liensImage['intermediaire']['balise'] = $resultat[1];
						}
						
						if (empty($liensImage))
						{
							$blocs[$region] .= '<p>' . T_("Ajoutez le code ci-dessous sur votre site:") . "</p>\n$codeLienPage";
						}
						else
						{
							$blocs[$region] .= '<p>' . T_("Lien textuel seulement:") . "</p>\n$codeLienPage";
							
							foreach ($liensImage as $lienImageType => $lienImageInfo)
							{
								$blocs[$region] .= $lienImageInfo['description'];
								$blocs[$region] .= '<pre><code>' . securiseTexte('<a href="' . $urlSansAction . '" title="' . $baliseTitle . $baliseTitleComplement . '">' . $lienImageInfo['balise'] . '</a>') . "</code></pre>\n";
							}
						}
						
						$blocs[$region] .= "</div>\n";
						$blocs[$region] .= '</div><!-- /#lienPage -->' . "\n";
						$blocs[$region] .= '<script type="text/javascript">' . "\n";
						$blocs[$region] .= "//<![CDATA[\n";
						$blocs[$region] .= "boiteDeroulante('#lienPage', \"$aExecuterApresClicBd\");\n";
						$blocs[$region] .= "//]]>\n";
						$blocs[$region] .= "</script>\n";
					}
					
					break;
					
				case 'menu':
					$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
				
					$blocs[$region] .= '<div id="menu" class="bloc ' . $classesBloc . '">' . "\n";
				
					ob_start();
					include cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu');
					$bloc = ob_get_contents();
					ob_end_clean();
				
					if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
					{
						$bloc = lienActif($urlRacine, $bloc, TRUE, 'li');
					}
				
					if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
					{
						$bloc = limiteProfondeurListe($bloc);
					}
				
					$blocs[$region] .= $bloc;
					$blocs[$region] .= '</div><!-- /#menu -->' . "\n";
					break;
					
				case 'menu-categories':
					$bloc = '';
					$boiteDeroulanteAjoutee = FALSE;
					
					if ($genererMenuCategories)
					{
						$cheminConfigCategories = cheminConfigCategories($racine);
						
						if ($cheminConfigCategories && ($categories = super_parse_ini_file($cheminConfigCategories, TRUE)) !== FALSE)
						{
							$tableauAccueilTrie = triTableauAccueil($accueil, LANGUE);
							
							foreach ($tableauAccueilTrie as $codeLangue => $urlAccueilLangue)
							{
								$blocLangue = menuCategoriesAutomatise($racine, $urlRacine, $codeLangue, $categories, $afficherNombreArticlesCategorie, $activerCategoriesGlobales, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
								
								if (!empty($blocLangue))
								{
									if ($codeLangue != LANGUE)
									{
										$boiteDeroulanteAjoutee = TRUE;
										$bloc .= "<div class=\"menuCategoriesLangueAutre\">\n<h3 class=\"bDtitre\">" . codeLangueVersNom($codeLangue) . "</h3>\n<ul class=\"bDcorps masquer\">\n$blocLangue</ul>\n</div><!-- /.menuCategoriesLangue -->\n";
									}
									else
									{
										$bloc .= "<ul>\n$blocLangue</ul>\n";
									}
								}
							}
							
							if (!empty($bloc))
							{
								$bloc = '<h2>' . T_("Catégories") . "</h2>\n$bloc";
							}
						}
					}
					else
					{
						$cheminMenuCategories = cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu-categories');
						
						if (!empty($cheminMenuCategories))
						{
							ob_start();
							include $cheminMenuCategories;
							$bloc = ob_get_contents();
							ob_end_clean();
						}
					}
					
					if (!empty($bloc))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="menuCategories" class="bloc ' . $classesBloc . '">' . "\n";
						
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = categoriesActives($bloc, $listeCategoriesPage, $idCategorie);
							$bloc = lienActif($urlRacine, $bloc, TRUE, 'li');
						}
						
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= '</div><!-- /#menuCategories -->' . "\n";
						
						if ($boiteDeroulanteAjoutee)
						{
							$blocs[$region] .= '<script type="text/javascript">' . "\n";
							$blocs[$region] .= "//<![CDATA[\n";
							$blocs[$region] .= "boiteDeroulante('.menuCategoriesLangueAutre', \"$aExecuterApresClicBd\");\n";
							$blocs[$region] .= "//]]>\n";
							$blocs[$region] .= "</script>\n";
						}
					}
						
					break;
					
				case 'menu-galeries':
					$bloc = '';
					
					if ($genererMenuGaleries)
					{
						$listeGaleries = listeGaleries($racine, '', TRUE);
						uksort($listeGaleries, 'strnatcasecmp');
						
						if ($activerGalerieDemo)
						{
							$listeGaleries = array_merge(array ('démo' => array ('dossier' => 'demo', 'url' => 'galerie.php?id=demo&amp;langue={LANGUE}')), $listeGaleries);
						}
						
						foreach ($listeGaleries as $listeIdGalerie => $listeInfosGalerie)
						{
							$bloc .= '<li><a href="' . urlGalerie(1, $racine, $urlRacine, $listeInfosGalerie['url'], LANGUE) . '">' . securiseTexte($listeIdGalerie) . "</a></li>\n";
						}
						
						if (!empty($bloc))
						{
							$bloc = '<h2>' . T_("Galeries") . "</h2>\n<ul>\n$bloc</ul>\n";
						}
					}
					else
					{
						$cheminMenuGaleries = cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu-galeries');
						
						if (!empty($cheminMenuGaleries))
						{
							ob_start();
							include $cheminMenuGaleries;
							$bloc = ob_get_contents();
							ob_end_clean();
						}
					}
					
					if (!empty($bloc))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="menuGaleries" class="bloc ' . $classesBloc . '">' . "\n";
						
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = lienActif($urlRacine, $bloc, TRUE, 'li');
						}
						
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= '</div><!-- /#menuGaleries -->' . "\n";
					}
						
					break;
					
				case 'menu-langues':
					$bloc = '';
					
					if (count($accueil) > 1)
					{
						if ($genererMenuLangues)
						{
							$tableauAccueilTrie = triTableauAccueil($accueil, LANGUE);
							
							foreach ($tableauAccueilTrie as $codeLangue => $urlAccueilLangue)
							{
								$bloc .= '<li><a href="' . $urlAccueilLangue . '/">' . codeLangueVersNom($codeLangue, FALSE) . "</a></li>\n";
							}
							
							if (!empty($bloc))
							{
								$bloc = '<h2>' . T_("Langues") . "</h2>\n<ul>\n$bloc</ul>\n";
							}
						}
						else
						{
							$cheminMenuLangues = cheminXhtml($racine, array ($langue, $langueParDefaut), 'menu-langues');
							
							if (!empty($cheminMenuLangues))
							{
								ob_start();
								include $cheminMenuLangues;
								$bloc = ob_get_contents();
								ob_end_clean();
							}
						}
					}
					
					if (!empty($bloc))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						$blocs[$region] .= '<div id="menuLangues" class="bloc ' . $classesBloc . '">' . "\n";
						
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = langueActive($bloc, LANGUE, $accueil);
						}
					
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= '</div><!-- /#menuLangues -->' . "\n";
					}
					
					break;
					
				case 'partage':
					if (
						(
							($partageCourriel && $partageCourrielActif) ||
							($partageReseaux && empty($courrielContact))
						) &&
						!$erreur404 &&
						!$estPageDerreur
					)
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						$bloc = '<div id="partage" class="bloc ' . $classesBloc . '">' . "\n";
						$bloc .= '<h2 class="bDtitre">' . T_("Partager") . "</h2>\n";
						
						$bloc .= "<ul class=\"bDcorps\">\n";
						
						if ($partageCourriel && $partageCourrielActif)
						{
							$blocPartageCourriel = '<li id="partageCourriel"><a href="' . variableGet(2, $url, 'action', 'partageCourriel') . '#titrePartageCourriel">' . T_("Par courriel") . "</a></li>\n";
							
							if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
							{
								$blocPartageCourriel = lienActif($urlRacine, $blocPartageCourriel, TRUE, 'li');
							}
							
							$bloc .= $blocPartageCourriel;
						}
						
						if ($partageReseaux && empty($courrielContact))
						{
							$listePartage = partageReseaux($url, $baliseTitle . $baliseTitleComplement);
							
							foreach ($listePartage as $service)
							{
								$bloc .= '<li id="' . $service['id'] . '"><a href="' . $service['lien'] . '" rel="nofollow">' . $service['nom'] . "</a></li>\n";
							}
						}
						
						$bloc .= "</ul>\n";
						$bloc .= '</div><!-- /#partage -->' . "\n";
						$bloc .= '<script type="text/javascript">' . "\n";
						$bloc .= "//<![CDATA[\n";
						$bloc .= "boiteDeroulante('#partage', \"$aExecuterApresClicBd\");\n";
						$bloc .= "//]]>\n";
						$bloc .= "</script>\n";
						$blocs[$region] .= $bloc;
					}
					
					break;
					
				case 'piwik':
					$cheminPiwik = cheminXhtml($racine, array ($langue, $langueParDefaut), 'piwik');
					
					if (!empty($cheminPiwik))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="piwik" class="bloc ' . $classesBloc . '">' . "\n";
						
						ob_start();
						include $cheminPiwik;
						$bloc = ob_get_contents();
						ob_end_clean();
						
						$blocs[$region] .= $bloc;
						$blocs[$region] .= '</div><!-- /#piwik -->' . "\n";
					}
					
					break;
					
				case 'recherche-google':
					if ($activerRechercheGoogle)
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
						
						$blocs[$region] .= '<div id="rechercheGoogle" class="bloc ' . $classesBloc . '">' . "\n";
						$blocs[$region] .= '<h2>' . T_("Rechercher dans le site") . "</h2>\n";
						$blocs[$region] .= '<form method="get" action="http://www.google.' . $rechercheGoogleExtension . '/search">' . "\n";
						$blocs[$region] .= "<div>\n";
						$blocs[$region] .= '<input id="inputMotsCles" type="text" name="as_q" maxlength="255" />' . "\n";
						$blocs[$region] .= '<input type="submit" value="' . T_("Rechercher") . '" />' . "\n";
						$blocs[$region] .= '<input type="hidden" name="as_sitesearch" value="' . securiseTexte($_SERVER['SERVER_NAME']) . '" />' . "\n";
						$blocs[$region] .= "</div>\n";
						$blocs[$region] .= "</form>\n";
						$blocs[$region] .= '</div><!-- /#rechercheGoogle -->' . "\n";
					}
					
					break;
					
				// Blocs personnalisés.
				default:
					if (cheminXhtml($racine, array ($langue, $langueParDefaut), $blocAinserer, FALSE))
					{
						$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $blocAinserer, $nombreDeColonnes);
					
						$blocs[$region] .= "<div class=\"bloc $classesBloc $blocAinserer\">\n";
					
						ob_start();
						include cheminXhtml($racine, array ($langue, $langueParDefaut), $blocAinserer);
						$bloc = ob_get_contents();
						ob_end_clean();
					
						if (isset($liensActifsBlocs[$blocAinserer]) && $liensActifsBlocs[$blocAinserer])
						{
							$bloc = lienActif($urlRacine, $bloc, TRUE, 'li');
						}
					
						if (isset($limiterProfondeurListesBlocs[$blocAinserer]) && $limiterProfondeurListesBlocs[$blocAinserer])
						{
							$bloc = limiteProfondeurListe($bloc);
						}
					
						$blocs[$region] .= $bloc;
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
	include $racine . '/site/inc/blocs.inc.php';
}
?>
