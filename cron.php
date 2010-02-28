<?php
if (file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	super_set_time_limit($delaiExpirationScript);
	
	@file_put_contents("$racine/site/inc/cron.txt", time());
	
	$tableauUrl = array ();
	
	if ($dureeCache['fluxRss'])
	{
		if (cheminConfigCategories($racine))
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
						$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, $categories, array('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
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
						$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, $categories, array('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					}
				}
			}
			
			if (!empty($categories))
			{
				foreach ($categories as $categorie => $categorieInfos)
				{
					if (!empty($categorieInfos['urlCategorie']))
					{
						$nomFichierCache = filtreChaine($racine, "rss-categorie-$categorie.cache.xml");
						$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCategorie'], 'cache' => $nomFichierCache);
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
						$nomFichierCache = filtreChaine($racine, "rss-galerie-$idGalerie.cache.xml");
						$tableauUrl[] = array ('url' => $urlRacine . '/rss.php?type=galerie&amp;chemin=' . $urlGalerie, 'cache' => $nomFichierCache);
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
						
						$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-1.cache.html");
						$tableauUrl[] = array ('url' => $urlRacine . '/' . $urlGalerie, 'cache' => $nomFichierCache);
					
						if ($nombreDePages > 1)
						{
							for ($i = 2; $i <= $nombreDePages; $i++)
							{
								$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "page=$i");
								$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-$i.cache.html");
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
		
		if (!empty($categories))
		{
			foreach ($categories as $categorie => $categorieInfos)
			{
				if (!empty($categorieInfos['urlCategorie']))
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
					
					$nomFichierCache = filtreChaine($racine, "categorie-$categorie-page-1.cache.html");
					$tableauUrl[] = array ('url' => $urlRacine . '/' . $categorieInfos['urlCategorie'], 'cache' => $nomFichierCache);
					
					if ($nombreDePages > 1)
					{
						for ($i = 2; $i <= $nombreDePages; $i++)
						{
							$adresse = ajouteGet($urlRacine . '/' . $categorieInfos['urlCategorie'], "page=$i");
							$nomFichierCache = filtreChaine($racine, "categorie-$categorie-page-$i.cache.html");
							$tableauUrl[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
						}
					}
				}
			}
		}
	}
	
	foreach ($tableauUrl as $url)
	{
		@unlink($racine . '/site/cache/' . $url['cache']);
		@file_get_contents(superRawurlencode($url['url'], TRUE));
	}
}
?>
