<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

include_once $racine . '/inc/constantes.inc.php';
include_once $racine . '/inc/simplehtmldom/simple_html_dom.php';
include_once $racine . '/inc/filter_htmlcorrector/common.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/filter.inc.php';

$langue = langue($langueParDefaut, 'navigateur');

// Nécessaire à la traduction.
phpGettext('.', $langue);

if (isset($_GET['chemin']))
{
	$getChemin = sansEchappement($_GET['chemin']);
}

if (isset($_GET['langue']))
{
	$getLangue = sansEchappement($_GET['langue']);
}

if (isset($getChemin) && !empty($getChemin))
{
	if (file_exists($racine . '/' . $getChemin) && $fic = @fopen($racine . '/' . $getChemin, 'r'))
	{
		while (!strstr($ligne, 'inc/premier.inc.php') && !feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			
			if (preg_match('/\$rssGalerie\s*=\s*((TRUE|true|FALSE|false))\s*;/', $ligne, $resultat))
			{
				if ($resultat[1] == "TRUE" || $resultat[1] == "true")
				{
					$rssGalerie = TRUE;
				}
				elseif ($resultat[1] == "FALSE" || $resultat[1] == "false")
				{
					$rssGalerie = FALSE;
				}
			}
	
			if (preg_match('/\$idGalerie\s*=\s*[\'"](.+)[\'"]\s*;/', $ligne, $resultat))
			{
				$idGalerie = $resultat[1];
			}
		}

		fclose($fic);
		
		// Flux RSS de la galerie.
		if (isset($idGalerie))
		{
			if (!isset($rssGalerie))
			{
				$rssGalerie = $galerieActiverFluxRssParDefaut;
			}
			
			if ($rssGalerie && isset($idGalerie) && !empty($idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && cheminConfigGalerie($racine, $idGalerie))
			{
				// A: le flux RSS est activé.
				
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
				if ($dureeCacheFluxRss && file_exists("$racine/site/cache/rss-$idGalerie.xml") && !cacheExpire("$racine/site/cache/rss-$idGalerie.xml", $dureeCacheFluxRss))
				{
					readfile("$racine/site/cache/rss-$idGalerie.xml");
				}
				else
				{
					$urlGalerie = $urlRacine . '/' . $getChemin;
					$itemsFluxRss = fluxRssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie);
					
					if (!empty($itemsFluxRss))
					{
						$itemsFluxRss = fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss);
					}
					
					$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), $urlGalerie, $itemsFluxRss, TRUE);
					
					if ($dureeCacheFluxRss)
					{
						creeDossierCache($racine);
						@file_put_contents("$racine/site/cache/rss-$idGalerie.xml", $rssAafficher);
						readfile("$racine/site/cache/rss-$idGalerie.xml");
					}
					else
					{
						echo $rssAafficher;
					}
				}
			}
			else
			{
				header('HTTP/1.1 404 Not found');
			}
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}
elseif (isset($_GET['global']) && $_GET['global'] == 'galeries' && isset($getLangue) && isset($accueil[$getLangue]))
{
	$cheminConfigFluxRssGlobalGaleries = cheminConfigFluxRssGlobal($racine, 'galeries');
	
	if ($galerieActiverFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		if ($dureeCacheFluxRss && file_exists("$racine/site/cache/rss-global-galeries-$getLangue.xml") && !cacheExpire("$racine/site/cache/rss-global-galeries-$getLangue.xml", $dureeCacheFluxRss))
		{
			readfile("$racine/site/cache/rss-global-galeries-$getLangue.xml");
		}
		else
		{
			$galeries = super_parse_ini_file($cheminConfigFluxRssGlobalGaleries, TRUE);
			$itemsFluxRss = array ();
			
			if ($galeries !== FALSE && !empty($galeries))
			{
				foreach ($galeries as $codeLangue => $langueInfos)
				{
					if ($codeLangue == $getLangue)
					{
						foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
						{
							$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
						}
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss);
				}
			}
			
			$idGalerie = '';
			$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), ACCUEIL, $itemsFluxRss, TRUE);
			
			if ($dureeCacheFluxRss)
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/rss-global-galeries-$getLangue.xml", $rssAafficher);
				readfile("$racine/site/cache/rss-global-galeries-$getLangue.xml");
			}
			else
			{
				echo $rssAafficher;
			}
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}
elseif (isset($_GET['global']) && $_GET['global'] == 'site' && isset($getLangue) && isset($accueil[$getLangue]))
{
	$cheminConfigFluxRssGlobalGaleries = cheminConfigFluxRssGlobal($racine, 'galeries');
	
	if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
	{
		// A: le flux RSS global du site est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		if ($dureeCacheFluxRss && file_exists("$racine/site/cache/rss-global-site-$getLangue.xml") && !cacheExpire("$racine/site/cache/rss-global-site-$getLangue.xml", $dureeCacheFluxRss))
		{
			readfile("$racine/site/cache/rss-global-site-$getLangue.xml");
		}
		else
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
			$itemsFluxRss = array ();
			
			if ($pages !== FALSE && !empty($pages))
			{
				$i = 0;
				
				foreach ($pages as $codeLangue => $langueInfos)
				{
					if ($codeLangue == $getLangue && $i < $nombreItemsFluxRss)
					{
						foreach ($langueInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", $urlRacine . "/" . str_replace('%2F', '/', rawurlencode($page)), $inclureApercu);
							
							if (!empty($fluxRssPageTableauBrut))
							{
								$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
							}
						}
					}
					
					$i++;
				}
				
				// On vérifie si les galeries ont leur flux RSS global, et si oui, on les inclut dans le flux RSS global du site.
				if ($galerieActiverFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
				{
					$galeries = super_parse_ini_file($cheminConfigFluxRssGlobalGaleries, TRUE);
					
					if ($galeries !== FALSE && !empty($galeries))
					{
						foreach ($galeries as $codeLangue => $langueInfos)
						{
							if ($codeLangue == $getLangue)
							{
								foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
								{
									$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
								}
							}
						}
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss);
				}
			}
			
			$idGalerie = '';
			$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), ACCUEIL, $itemsFluxRss, FALSE);
			
			if ($dureeCacheFluxRss)
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/rss-global-site-$getLangue.xml", $rssAafficher);
				readfile("$racine/site/cache/rss-global-site-$getLangue.xml");
			}
			else
			{
				echo $rssAafficher;
			}
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}
else
{
	header('HTTP/1.1 404 Not found');
}
?>
