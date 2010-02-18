<?php
if (file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	
	super_set_time_limit(300);
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	@file_put_contents("$racine/site/inc/cron.txt", time());
	
	$tableauUrl = array ();
	
	if ($dureeCache['fluxRss'])
	{
		if (cheminConfigCategories($racine))
		{
			$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
			
			if (!empty($categories))
			{
				foreach ($categories as $categorie => $categorieInfos)
				{
					if (!empty($categorieInfos['urlCategorie']))
					{
						$tableauUrl[] = $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCategorie'];
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
					$tableauUrl[] = $urlRacine . '/rss.php?type=galeries&amp;langue=' . $codeLangue;
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
					$tableauUrl[] = $urlRacine . '/rss.php?type=site&amp;langue=' . $codeLangue;
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
					
						$tableauUrl[] = $urlRacine . '/' . $urlGalerie;
					
						if ($nombreDePages > 1)
						{
							for ($i = 2; $i <= $nombreDePages; $i++)
							{
								$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "page=$i");
								$tableauUrl[] = $adresse;
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
					
					$tableauUrl[] = $urlRacine . '/' . $categorieInfos['urlCategorie'];
					
					if ($nombreDePages > 1)
					{
						for ($i = 2; $i <= $nombreDePages; $i++)
						{
							$adresse = ajouteGet($urlRacine . '/' . $categorieInfos['urlCategorie'], "page=$i");
							$tableauUrl[] = $adresse;
						}
					}
				}
			}
		}
	}
	
	foreach ($tableauUrl as $url)
	{
		@file_get_contents(superRawurlencode($url));
	}
}
?>
