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
	if (file_exists($racine . '/' . $getChemin) && $fic = fopen($racine . '/' . $getChemin, 'r'))
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
	
			if ($rss && isset($idGalerie) && !empty($idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie . "/config.pc"))
			{
				// A: le flux est activé.
				
				// On vérifie si le flux existe en cache ou si le cache est expiré
				if ($dureeCache && file_exists("$racine/site/cache/rss-$idGalerie.xml") && !cacheExpire("$racine/site/cache/rss-$idGalerie.xml", $dureeCache))
				{
					readfile("$racine/site/cache/rss-$idGalerie.xml");
				}
				else
				{
					$urlGalerie = $urlRacine . '/' . $getChemin;
					
					$itemsFlux = rssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxGlobalUrlOeuvre);
					if (!empty($itemsFlux))
					{
						$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
					}
					$rssAafficher = rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), $urlGalerie, $itemsFlux, TRUE);
					
					if ($dureeCache)
					{
						creeDossierCache($racine);
						file_put_contents("$racine/site/cache/rss-$idGalerie.xml", $rssAafficher);
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
			$itemsFlux = array ();
			if (!empty($galeries))
			{
				foreach ($galeries as $codeLangueIdGalerie => $urlRelativeGalerie)
				{
					list ($codeLangue, $idGalerie) = explode(':', $codeLangueIdGalerie, 2);
					if ($codeLangue == $getLangue)
					{
						$itemsFlux = array_merge($itemsFlux, rssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
					}
				}
				
				if (!empty($itemsFlux))
				{
					$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
				}
			}
			
			$idGalerie = FALSE;
			$rssAafficher = rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), ACCUEIL, $itemsFlux, TRUE);
			
			if ($dureeCache)
			{
				creeDossierCache($racine);
				file_put_contents("$racine/site/cache/rss-global-galeries.xml", $rssAafficher);
				readfile("$racine/site/cache/rss-global-galeries.xml");
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
			$itemsFlux = array ();
			if (!empty($pages))
			{
				$i = 0;
				foreach ($pages as $page)
				{
					if ($i < $nbreItemsFlux)
					{
						if (strpos($page, ':') !== FALSE)
						{
							list ($codeLangue, $page) = explode(':', $page, 2);
						}
						$page = rtrim($page);
						if ($codeLangue == $getLangue)
						{
							$itemsFlux = array_merge($itemsFlux, rssPageTableauBrut("$racine/$page", $urlRacine . "/" . str_replace('%2F', '/', rawurlencode($page))));
						}
					}
					$i++;
				}
				
				// On vérifie si les galeries ont leur flux global, et si oui, on les inclut dans le flux global du site
				if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
				{
					$galeries = tableauAssociatif("$racine/site/inc/rss-global-galeries.pc");
					if (!empty($galeries))
					{
						foreach ($galeries as $codeLangueIdGalerie => $urlRelativeGalerie)
						{
							list ($codeLangue, $idGalerie) = explode(':', $codeLangueIdGalerie, 2);
							if ($codeLangue == $getLangue)
							{
								$itemsFlux = array_merge($itemsFlux, rssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie));
							}
						}
					}
				}
				
				if (!empty($itemsFlux))
				{
					$itemsFlux = rssTableauFinal($itemsFlux, $nbreItemsFlux);
				}
			}
			$idGalerie = FALSE;
			$rssAafficher = rss($idGalerie, baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue), ACCUEIL, $itemsFlux, FALSE);
			
			if ($dureeCache)
			{
				creeDossierCache($racine);
				file_put_contents("$racine/site/cache/rss-global-site.xml", $rssAafficher);
				readfile("$racine/site/cache/rss-global-site.xml");
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
