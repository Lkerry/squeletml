<?php
include_once 'init.inc.php';
include_once 'inc/fonctions.inc.php';
include_once $racine . '/inc/config.inc.php';
include_once $racine . '/inc/constantes.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

$langueNavigateur = langue($langueParDefaut, 'navigateur');
$langue = $langueNavigateur;
// Nécessaire à la traduction
phpGettext('.', $langueNavigateur);

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
			if (preg_match('/\$rss\s*=\s*((TRUE|true|FALSE|false))\s*;/', $ligne, $res))
			{
				if ($res[1] == "TRUE" || $res[1] == "true")
				{
					$rss = TRUE;
				}
				elseif ($res[1] == "FALSE" || $res[1] == "false")
				{
					$rss = FALSE;
				}
			}
	
			if (preg_match('/\$idGalerie\s*=\s*[\'"](.+)[\'"]\s*;/', $ligne, $res))
			{
				$idGalerie = $res[1];
			}
		}

		fclose($fic);
		
		// Flux RSS de la galerie
		if (isset($idGalerie))
		{
			if (!isset($rss))
			{
				$rss = $galerieFluxRssParDefaut;
			}
			
			if ($rss && isset($idGalerie) && !empty($idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && adminCheminConfigGalerie($racine, $idGalerie))
			{
				// A: le flux RSS est activé.
				
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré
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
					$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), $urlGalerie, $itemsFluxRss, TRUE);
					
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
	$cheminConfigFluxRssGlobalGaleries = adminCheminConfigFluxRssGlobalGaleries($racine);
	
	if ($galerieFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré
		if ($dureeCacheFluxRss && file_exists("$racine/site/cache/rss-global-galeries-$getLangue.xml") && !cacheExpire("$racine/site/cache/rss-global-galeries-$getLangue.xml", $dureeCacheFluxRss))
		{
			readfile("$racine/site/cache/rss-global-galeries-$getLangue.xml");
		}
		else
		{
			$galeries = tableauAssociatif($cheminConfigFluxRssGlobalGaleries);
			$itemsFluxRss = array ();
			if (!empty($galeries))
			{
				foreach ($galeries as $codeLangueIdGalerie => $urlRelativeGalerie)
				{
					list ($codeLangue, $idGalerie) = explode(':', $codeLangueIdGalerie, 2);
					if ($codeLangue == $getLangue)
					{
						$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss);
				}
			}
			
			$idGalerie = FALSE;
			$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), ACCUEIL, $itemsFluxRss, TRUE);
			
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
	$cheminConfigFluxRssGlobalGaleries = adminCheminConfigFluxRssGlobalGaleries($racine);
	
	if ($siteFluxRssGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
	{
		// A: le flux RSS global du site est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré
		if ($dureeCacheFluxRss && file_exists("$racine/site/cache/rss-global-site-$getLangue.xml") && !cacheExpire("$racine/site/cache/rss-global-site-$getLangue.xml", $dureeCacheFluxRss))
		{
			readfile("$racine/site/cache/rss-global-site-$getLangue.xml");
		}
		else
		{
			$pages = file("$racine/site/inc/rss-global-site.pc");
			$itemsFluxRss = array ();
			if (!empty($pages))
			{
				$i = 0;
				foreach ($pages as $page)
				{
					if ($i < $nombreItemsFluxRss)
					{
						if (strpos($page, ':') !== FALSE)
						{
							list ($codeLangue, $page) = explode(':', $page, 2);
						}
						$page = rtrim($page);
						if ($codeLangue == $getLangue)
						{
							$itemsFluxRss = array_merge($itemsFluxRss, fluxRssPageTableauBrut("$racine/$page", $urlRacine . "/" . str_replace('%2F', '/', rawurlencode($page))));
						}
					}
					$i++;
				}
				
				// On vérifie si les galeries ont leur flux RSS global, et si oui, on les inclut dans le flux RSS global du site
				if ($galerieFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
				{
					$galeries = tableauAssociatif($cheminConfigFluxRssGlobalGaleries);
					if (!empty($galeries))
					{
						foreach ($galeries as $codeLangueIdGalerie => $urlRelativeGalerie)
						{
							list ($codeLangue, $idGalerie) = explode(':', $codeLangueIdGalerie, 2);
							if ($codeLangue == $getLangue)
							{
								$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
							}
						}
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss);
				}
			}
			$idGalerie = FALSE;
			$rssAafficher = fluxRss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), ACCUEIL, $itemsFluxRss, FALSE);
			
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
