<?php
if (file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	include_once $racine . '/inc/php-gettext/gettext.inc';
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	super_set_time_limit($delaiExpirationScript);
	
	@file_put_contents("$racine/site/inc/cron.txt", time());
	
	$tableauUrl = array ();
	$extraAsupprimer = array ();
	
	if ($dureeCache['fluxRss'])
	{
		if (cheminConfigCategories($racine))
		{
			$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
			
			if (!empty($categories))
			{
				foreach ($categories as $categorie => $categorieInfos)
				{
					foreach ($accueil as $langueCache => $infosLangueCache)
					{
						$nomFichierCache = filtreChaine($racine, "rss-categorie-$categorie-$langueCache.cache.xml");
						
						if (empty($categorieInfos['urlCategorie']) || $categorieInfos['urlCategorie'] == "categorie.php?id=$categorie")
						{
							$tableauUrl[] = array ('url' => "$urlRacine/rss.php?type=categorie&amp;id=$categorie&amp;langue=$langueCache", 'cache' => $nomFichierCache);
						}
						else
						{
							$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCategorie'] . "&amp;langue=$langueCache", 'cache' => $nomFichierCache);
						}
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
					$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=galeries&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
					
					foreach ($langueInfos as $idGalerie => $urlGalerie)
					{
						foreach ($accueil as $langueCache => $infosLangueCache)
						{
							$nomFichierCache = filtreChaine($racine, "rss-galerie-$idGalerie-$langueCache.cache.xml");
							$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=galerie&amp;chemin=' . $urlGalerie . '&amp;langue=' . $langueCache, 'cache' => $nomFichierCache);
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
					$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=site&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
				}
			}
		}
	}
	
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
							$nombreDoeuvres = count($tableauGalerie);
							$nombreDePages = ceil($nombreDoeuvres / $galerieVignettesParPage);
						}
						else
						{
							$nombreDePages = 1;
						}
						
						$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-1-$codeLangue.cache.html");
						$tableauUrl[] = array ('url' => $urlRacine . '/' . $urlGalerie, 'cache' => $nomFichierCache);
				
						if ($nombreDePages > 1)
						{
							for ($i = 2; $i <= $nombreDePages; $i++)
							{
								$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "page=$i");
								$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-$i-$codeLangue.cache.html");
								$tableauUrl[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
							}
						}
					}
				}
			}
		}
	}
	
	if ($dureeCache['categorie'] && cheminConfigCategories($racine))
	{
		$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
		if ($categories === FALSE)
		{
			$categories = array ();
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
						$categories = array_merge($categorie, $categories);
						break; // Si cette catégorie globale existe, l'important est d'obtenir sa présence dans le tableau des catégories, car les autres langues seront ajoutées (s'il y a lieu) plus bas.
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
						$categories = array_merge($categorie, $categories);
						break;
					}
				}
			}
		}
		
		if (!empty($categories))
		{
			foreach ($categories as $categorie => $categorieInfos)
			{
				if ($nombreArticlesParPageCategorie)
				{
					$nombreArticles = count($categorieInfos['pages']);
					$nombreDePages = ceil($nombreArticles / $nombreArticlesParPageCategorie);
				}
				else
				{
					$nombreDePages = 1;
				}
				
				if (empty($categorieInfos['urlCategorie']) || $categorieInfos['urlCategorie'] == "categorie.php?id=$categorie")
				{
					foreach ($accueil as $langueCache => $infosLangueCache)
					{
						$categorieInfos['urlCategorie'] = "categorie.php?id=$categorie&amp;langue=$langueCache";
						$nomFichierCache = filtreChaine($racine, "categorie-$categorie-page-1-$langueCache.cache.html");
				$tableauUrl[] = array ('url' => $urlRacine . '/' . $categorieInfos['urlCategorie'], 'cache' => $nomFichierCache);
						
						if ($nombreDePages > 1)
						{
							for ($i = 2; $i <= $nombreDePages; $i++)
							{
								$adresse = ajouteGet($urlRacine . '/' . $categorieInfos['urlCategorie'], "page=$i");
								$nomFichierCache = filtreChaine($racine, "categorie-$categorie-page-$i-$langueCache.cache.html");
								$tableauUrl[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
							}
						}
					}
				}
				else
				{
					foreach ($accueil as $langueCache => $infosLangueCache)
					{
						$extraAsupprimer[] = filtreChaine($racine, "categorie-$categorie-page-1-$langueCache.cache.html");
					}
					
					$tableauUrl[] = array ('url' => $urlRacine . '/' . $categorieInfos['urlCategorie'], 'cache' => '');
			
					if ($nombreDePages > 1)
					{
						for ($i = 2; $i <= $nombreDePages; $i++)
						{
							foreach ($accueil as $langueCache => $infosLangueCache)
							{
								$extraAsupprimer[] = filtreChaine($racine, "categorie-$categorie-page-$i-$langueCache.cache.html");
							}
							
							$adresse = ajouteGet($urlRacine . '/' . $categorieInfos['urlCategorie'], "page=$i");
							$tableauUrl[] = array ('url' => $adresse, 'cache' => '');
						}
					}
				}
			}
		}
	}
	
	foreach ($tableauUrl as $url)
	{
		if (!empty($url['cache']))
		{
			@unlink($racine . '/site/cache/' . $url['cache']);
		}
	}
	
	foreach ($extraAsupprimer as $aSupprimer)
	{
		@unlink($racine . '/site/cache/' . $aSupprimer);
	}
	
	foreach ($tableauUrl as $url)
	{
		if (empty($url['cache']) || !file_exists($racine . '/site/cache/' . $url['cache']))
		{
			@file_get_contents(superRawurlencode($url['url'], TRUE));
		}
	}
}
?>
