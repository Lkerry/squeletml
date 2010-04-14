<?php
if (file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	include_once $racineAdmin . '/inc/fonctions.inc.php';
	include_once $racine . '/inc/php-gettext/gettext.inc';
	
	eval(variablesAvantConfig());
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	phpGettext('.', $langueParDefaut); // Nécessaire à la traduction.
	
	$date = time();
	@file_put_contents("$racine/site/inc/cron.txt", $date);
	
	$rapport = '';
	$rapport .= '# ' . sprintf(T_("Rapport d'exécution du cron (%1\$s)"), $date) . "\n\n";
	
	########################################################################
	##
	## Cache.
	##
	########################################################################

	$rapport .= '## ' . T_("Cache") . "\n\n";
	$cUrlEstDisponible = FALSE;
	
	if (function_exists('curl_init'))
	{
		$cUrlEstDisponible = TRUE;
		require $racine . '/inc/rolling-curl/RollingCurl.php';
		$ch = new RollingCurl('cUrlCronRapport');
	}
	
	$tableauUrlCache = array ();
	$extraAsupprimer = array ();
	
	// Flux RSS.
	if ($dureeCache['fluxRss'])
	{
		if (cheminConfigCategories($racine))
		{
			$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
			
			if (!empty($categories))
			{
				foreach ($categories as $categorie => $categorieInfos)
				{
					$langueCat = langueCat($categorieInfos, $langueParDefaut);
					
					$nomFichierCache = filtreChaine($racine, "rss-categorie-$categorie-$langueCat.cache.xml");
					
					if (empty($categorieInfos['urlCat']) || strpos($categorieInfos['urlCat'], 'categorie.php?id=') !== FALSE)
					{
						$tableauUrlCache[] = array ('url' => "$urlRacine/rss.php?type=categorie&amp;id=$categorie", 'cache' => $nomFichierCache);
					}
					else
					{
						$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCat'], 'cache' => $nomFichierCache);
					}
				}
			}
		}
		
		if (cheminConfigFluxRssGlobal($racine, 'galeries'))
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
			
			if (!empty($pages))
			{
				foreach ($pages as $codeLangue => $langueInfos)
				{
					$nomFichierCache = filtreChaine($racine, "rss-galeries-$codeLangue.cache.xml");
					$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=galeries&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
					
					foreach ($langueInfos as $idGalerie => $urlGalerie)
					{
						$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=galerie&amp;chemin=' . $urlGalerie, 'cache' => '');
						
						foreach ($accueil as $langueCache => $infosLangueCache)
						{
							$extraAsupprimer[] = filtreChaine($racine, "rss-galerie-$idGalerie-$langueCache.cache.xml");
						}
					}
				}
			}
		}
		
		if (cheminConfigFluxRssGlobal($racine, 'site'))
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
			
			if (!empty($pages))
			{
				foreach ($pages as $codeLangue => $langueInfos)
				{
					$nomFichierCache = filtreChaine($racine, "rss-site-$codeLangue.cache.xml");
					$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=site&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
				}
			}
		}
	}
	
	// Galeries.
	if ($dureeCache['galerie'] && cheminConfigFluxRssGlobal($racine, 'galeries'))
	{
		$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
		
		if (!empty($pages))
		{
			foreach ($pages as $codeLangue => $langueInfos)
			{
				foreach ($langueInfos as $idGalerie => $urlGalerie)
				{
					if (cheminConfigGalerie($racine, $idGalerie))
					{
						$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
						
						if ($galerieVignettesParPage)
						{
							$nombreDimages = count($tableauGalerie);
							$nombreDePages = ceil($nombreDimages / $galerieVignettesParPage);
						}
						else
						{
							$nombreDePages = 1;
						}
						
						$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-1-$codeLangue.cache.html");
						$tableauUrlCache[] = array ('url' => $urlRacine . '/' . $urlGalerie, 'cache' => $nomFichierCache);
				
						if ($nombreDePages > 1)
						{
							for ($i = 2; $i <= $nombreDePages; $i++)
							{
								$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "page=$i");
								$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-$i-$codeLangue.cache.html");
								$tableauUrlCache[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
							}
						}
						
						foreach ($tableauGalerie as $image)
						{
							$id = idImage($racine, $image);
							$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "image=$id");
							$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-image-$id-$codeLangue.cache.html");
							$tableauUrlCache[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
						}
					}
				}
			}
		}
	}
	
	// Catégories.
	if ($dureeCache['categorie'] && cheminConfigCategories($racine))
	{
		$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
		if (!empty($categories))
		{
			foreach ($categories as $categorie => $categorieInfos)
			{
				$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorieInfos, $categorie, $nombreArticlesParPageCategorie, $langueParDefaut));
			}
		}
		
		if (cheminConfigFluxRssGlobal($racine, 'galeries'))
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
		
			if (!empty($pages))
			{
				foreach ($pages as $codeLangue => $langueInfos)
				{
					$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					
					if (!empty($categorie))
					{
						$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorie['galeries'], 'galeries', $nombreArticlesParPageCategorie, $langueParDefaut));
					}
				}
			}
		}
		
		if (cheminConfigFluxRssGlobal($racine, 'site'))
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
		
			if (!empty($pages))
			{
				foreach ($pages as $codeLangue => $langueInfos)
				{
					$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					
					if (!empty($categorie))
					{
						$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorie['site'], 'site', $nombreArticlesParPageCategorie, $langueParDefaut));
					}
				}
			}
		}
	}
	
	// Mise en action.
	
	foreach ($extraAsupprimer as $aSupprimer)
	{
		if (@unlink($racine . '/site/cache/' . $aSupprimer))
		{
			$rapport .= '- 1: ';
		}
		else
		{
			$rapport .= '- 0: ';
		}
		
		$rapport .= 'unlink("' . $racine . '/site/cache/' . $aSupprimer . '");' . "\n\n";
	}
	
	foreach ($tableauUrlCache as $url)
	{
		if (!empty($url['cache']))
		{
			if (@unlink($racine . '/site/cache/' . $url['cache']))
			{
				$rapport .= '- 1: ';
			}
			else
			{
				$rapport .= '- 0: ';
			}
			
			$rapport .= 'unlink("' . $racine . '/site/cache/' . $url['cache'] . '");' . "\n\n";
		}
		
		if (empty($url['cache']) || !file_exists($racine . '/site/cache/' . $url['cache']))
		{
			$urlEncodee = superRawurlencode($url['url'], TRUE);
			
			if ($cUrlEstDisponible)
			{
				$requete = new Request(superRawurlencode($url['url'], TRUE));
				$ch->add($requete);
			}
			else
			{
				if (@file_get_contents($urlEncodee) !== FALSE)
				{
					$rapport .= '- 1: ';
				}
				else
				{
					$rapport .= '- 0: ';
				}
				
				$rapport .= 'file_get_contents("' . $urlEncodee . '");' . "\n\n";
			}
		}
	}
	
	if ($cUrlEstDisponible)
	{
		ob_start();
		$ch->execute();
		$rapport .= ob_get_contents();
		ob_end_clean();
	}
	
	########################################################################
	##
	## Fichier Sitemap des galeries.
	##
	########################################################################

	$rapport .= '## ' . T_("Fichier Sitemap des galeries") . "\n\n";
	$rapport .= adminGenereSitemapGaleries($racine, $urlRacine, $galerieVignettesParPage, $adminPorteDocumentsDroits) . "\n\n";
	
	########################################################################
	##
	## Envoi du rapport.
	##
	########################################################################
	
	if ($rapportCron && (!empty($courrielAdmin) || !empty($contactCourrielParDefaut)))
	{
		$infosCourriel = array ();

		if (!empty($courrielExpediteurRapports))
		{
			$infosCourriel['From'] = $courrielExpediteurRapports;
		}
		
		$infosCourriel['destinataire'] = !empty($courrielAdmin) ? $courrielAdmin : $contactCourrielParDefaut;
		$infosCourriel['objet'] = "Cron ($date)" . baliseTitleComplement($tableauBaliseTitleComplement, array ($langueParDefaut), FALSE);
		$infosCourriel['message'] = $rapport;
		courriel($infosCourriel);
	}
}
?>
