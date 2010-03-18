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
					$langueCat = langueCat($categorieInfos, $langueParDefaut);
					
					$nomFichierCache = filtreChaine($racine, "rss-categorie-$categorie-$langueCat.cache.xml");
					
					if (empty($categorieInfos['urlCat']) || strpos($categorieInfos['urlCat'], 'categorie.php?id=') !== FALSE)
					{
						$tableauUrl[] = array ('url' => "$urlRacine/rss.php?type=categorie&amp;id=$categorie", 'cache' => $nomFichierCache);
					}
					else
					{
						$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCat'], 'cache' => $nomFichierCache);
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
						$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=galerie&amp;chemin=' . $urlGalerie, 'cache' => '');
						
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
							$nombreDimages = count($tableauGalerie);
							$nombreDePages = ceil($nombreDimages / $galerieVignettesParPage);
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
						
						foreach ($tableauGalerie as $image)
						{
							$id = idImage($racine, $image);
							$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "image=$id");
							$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-image-$id-$codeLangue.cache.html");
							$tableauUrl[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
						}
					}
				}
			}
		}
	}
	
	if ($dureeCache['categorie'] && cheminConfigCategories($racine))
	{
		$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
		if (!empty($categories))
		{
			foreach ($categories as $categorie => $categorieInfos)
			{
				$tableauUrl = array_merge($tableauUrl, cronUrlCategorie($racine, $urlRacine, $categorieInfos, $categorie, $nombreArticlesParPageCategorie, $langueParDefaut));
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
						$tableauUrl = array_merge($tableauUrl, cronUrlCategorie($racine, $urlRacine, $categorie['galeries'], 'galeries', $nombreArticlesParPageCategorie, $langueParDefaut));
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
						$tableauUrl = array_merge($tableauUrl, cronUrlCategorie($racine, $urlRacine, $categorie['site'], 'site', $nombreArticlesParPageCategorie, $langueParDefaut));
					}
				}
			}
		}
	}
	
	foreach ($extraAsupprimer as $aSupprimer)
	{
		@unlink($racine . '/site/cache/' . $aSupprimer);
	}
	
	foreach ($tableauUrl as $url)
	{
		if (!empty($url['cache']))
		{
			@unlink($racine . '/site/cache/' . $url['cache']);
		}
		
		if (empty($url['cache']) || !file_exists($racine . '/site/cache/' . $url['cache']))
		{
			@file_get_contents(superRawurlencode($url['url'], TRUE));
		}
	}
}
?>
