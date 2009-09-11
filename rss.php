<?php
include_once 'init.inc.php';
include_once 'inc/fonctions.inc.php';
include_once $racine . '/inc/config.inc.php';
include_once $racine . '/inc/constantes.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

$langueNavigateur = langue('navigateur');
$langue[1] = $langueNavigateur;
// Nécessaire à la traduction
phpGettext('.', $langueNavigateur);

if (isset($_GET['chemin']) && !empty($_GET['chemin']))
{
	if (file_exists($racine . '/' . $_GET['chemin']) && $fic = fopen($racine . '/' . $_GET['chemin'], 'r'))
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
		
		// Flux de la galerie
		if (isset($idGalerie))
		{
			if (!isset($rss))
			{
				$rss = $galerieFluxParDefaut;
			}
	
			if ($rss && isset($idGalerie) && !empty($idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && file_exists("$racine/site/inc/galerie-" . $idGalerie . ".pc"))
			{
				// A: le flux est activé.
				
				// On vérifie si le flux existe en cache ou si le cache est expiré
				if ($dureeCache && file_exists("$racine/site/cache/rss-$idGalerie.xml") && !cacheExpire("$racine/site/cache/rss-$idGalerie.xml", $dureeCache))
				{
					readfile("$racine/site/cache/rss-$idGalerie.xml");
				}
				else
				{
					$urlGalerie = $urlRacine . '/' . $_GET['chemin'];
				
					$itemsFlux = rssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxGlobalUrlOeuvre);
					$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
					creeDossierCache($racine);
					file_put_contents("$racine/site/cache/rss-$idGalerie.xml", rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langue), $urlGalerie, $itemsFlux, TRUE));
					readfile("$racine/site/cache/rss-$idGalerie.xml");
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

elseif (isset($_GET['global']) && $_GET['global'] == 'galeries')
{
	if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
	{
		// A: le flux global pour les galeries est activé.
		
		// On vérifie si le flux existe en cache ou si le cache est expiré
		if ($dureeCache && file_exists("$racine/site/cache/rss-global-galeries.xml") && !cacheExpire("$racine/site/cache/rss-global-galeries.xml", $dureeCache))
		{
			readfile("$racine/site/cache/rss-global-galeries.xml");
		}
		else
		{
			$galeries = tableauAssociatif("$racine/site/inc/rss-global-galeries.pc");
			if (!empty($galeries))
			{
				$itemsFlux = array ();
			
				foreach ($galeries as $idGalerie => $urlRelativeGalerie)
				{
					$itemsFlux = array_merge($itemsFlux, rssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
				}
			
				$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
				$idGalerie = FALSE;
				creeDossierCache($racine);
				file_put_contents("$racine/site/cache/rss-global-galeries.xml", rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langue), ACCUEIL, $itemsFlux, TRUE));
				readfile("$racine/site/cache/rss-global-galeries.xml");
			}
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}

elseif (isset($_GET['global']) && $_GET['global'] == 'site')
{
	if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
	{
		// A: le flux global du site est activé.
		
		// On vérifie si le flux existe en cache ou si le cache est expiré
		if ($dureeCache && file_exists("$racine/site/cache/rss-global-site.xml") && !cacheExpire("$racine/site/cache/rss-global-site.xml", $dureeCache))
		{
			readfile("$racine/site/cache/rss-global-site.xml");
		}
		else
		{
			$pages = file("$racine/site/inc/rss-global-site.pc");
			if (!empty($pages))
			{
				$itemsFlux = array ();
				$i = 0;
				foreach ($pages as $page)
				{
					if ($i < $nbreItemsFlux)
					{
						$page = rtrim($page);
						$itemsFlux = array_merge($itemsFlux, rssPageTableauBrut("$racine/$page", "$urlRacine/$page"));
					}
					$i++;
				}
				
				// On vérifie si les galeries ont leur flux global, et si oui, on les inclut dans le flux global du site
				if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
				{
					$galeries = tableauAssociatif("$racine/site/inc/rss-global-galeries.pc");
					if (!empty($galeries))
					{
						foreach ($galeries as $idGalerie => $urlRelativeGalerie)
						{
							$itemsFlux = array_merge($itemsFlux, rssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
						}
					}
				}
				
				$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
				$idGalerie = FALSE;
				creeDossierCache($racine);
				file_put_contents("$racine/site/cache/rss-global-site.xml", rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langue), ACCUEIL, $itemsFlux, FALSE));
				readfile("$racine/site/cache/rss-global-site.xml");
			}
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}

?>
