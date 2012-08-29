<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAaffecterAuDebut());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include $cheminFichier;
}

if (isset($_GET['langue']))
{
	$getLangue = $_GET['langue'];
	phpGettext('.', $getLangue); // Nécessaire à la traduction.
}
else
{
	$getLangue = '';
}

include_once $racine . '/inc/simplehtmldom/simple_html_dom.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/common.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/filter.inc.php';

$dureeCacheRss = 0;

if ($dureeCache)
{
	$dureeCacheRss = $dureeCache;
}
elseif ($dureeCachePartiel)
{
	$dureeCacheRss = $dureeCachePartiel;
}

if ($dureeCacheRss)
{
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url, FALSE);
	$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
}

$enTetesHttp = 'header("Content-Type: text/xml; charset=utf-8");';

if (!isset($estPageCron))
{
	$estPageCron = FALSE;
}

if ($inclureApercu && $utiliserApercuDansFluxRss)
{
	$fluxRssAvecApercu = TRUE;
}
else
{
	$fluxRssAvecApercu = FALSE;
}

if (isset($_GET['id']))
{
	$getId = $_GET['id'];
}
else
{
	$getId = '';
}

if (isset($_GET['type']))
{
	$getType = $_GET['type'];
}
else
{
	$getType = '';
}

$erreur404 = FALSE;

if ($getType == 'galerie' && !empty($getId) && !empty($getLangue))
{
	$listeGaleries = listeGaleries($racine);
	
	foreach ($listeGaleries as $idGalerie => $infosGalerie)
	{
		if ($getId == filtreChaine($idGalerie))
		{
			$id = $idGalerie;
			
			if (!empty($infosGalerie['rss']) && $infosGalerie['rss'] == 1)
			{
				$rss = TRUE;
			}
			else
			{
				$rss = FALSE;
			}
			
			break;
		}
	}
	
	if (!empty($id) && $rss)
	{
		$idGalerieDossier = idGalerieDossier($racine, $idGalerie);
		
		if (file_exists("$racine/site/fichiers/galeries/$idGalerieDossier") && cheminConfigGalerie($racine, $idGalerieDossier))
		{
			// A: le flux RSS est activé.
			
			// S'il y a lieu, analyse d'une requête effectuée par le client.
			if (code304($cheminFichierCache))
			{
				header('HTTP/1.1 304 Not Modified');
				
				exit(0);
			}
			
			// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
			if ($dureeCacheRss && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCacheRss) && !$estPageCron)
			{
				if (file_exists($cheminFichierCacheEnTete))
				{
					$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
					
					if (!empty($contenuFichierCacheEnTete))
					{
						eval($contenuFichierCacheEnTete);
					}
				}
				
				@readfile($cheminFichierCache);
			}
			else
			{
				$itemsFluxRss = fluxRssGalerieTableauBrut($racine, $urlRacine, $langueParDefaut, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
				}
				
				if (!empty($infosGalerie['url']))
				{
					$urlGalerie = urlGalerie(1, '', $urlRacine, $infosGalerie['url'], $getLangue);
				}
				else
				{
					$urlGalerie = urlGalerie(0, $racine, $urlRacine, $idGalerie, $getLangue);
				}
				
				$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut), FALSE), $idGalerie, '');
				
				if ($dureeCacheRss)
				{
					creeDossierCache($racine);
					$enregistrerCache = TRUE;
					
					if (file_exists($cheminFichierCache))
					{
						$codePageCache = @file_get_contents($cheminFichierCache);
						
						if ($codePageCache !== FALSE && md5($codePageCache) == md5($rssAafficher))
						{
							$enregistrerCache = FALSE;
						}
					}
					
					if ($enregistrerCache)
					{
						@file_put_contents($cheminFichierCache, $rssAafficher);
					}
					
					$enTetesHttp .= enTetesCache($cheminFichierCache, $dureeCacheRss);
					@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
				}
				
				if (!$estPageCron)
				{
					eval($enTetesHttp);
				}
				
				echo $rssAafficher;
			}
		}
		else
		{
			$erreur404 = TRUE;
		}
	}
	else
	{
		$erreur404 = TRUE;
	}
}
elseif ($getType == 'categorie' && !empty($getId) && empty($getLangue))
{
	$cheminConfigCategories = cheminConfigCategories($racine);
	
	if ($cheminConfigCategories)
	{
		$categories = super_parse_ini_file($cheminConfigCategories, TRUE);
		
		foreach ($categories as $idCategorie => $infosCategorie)
		{
			if ($getId == filtreChaine($idCategorie))
			{
				$id = $idCategorie;
				
				if (!empty($infosCategorie['rss']) && $infosCategorie['rss'] == 1)
				{
					$rss = TRUE;
				}
				else
				{
					$rss = FALSE;
				}
				
				break;
			}
		}
	}
	
	if (!empty($id) && $rss)
	{
		// A: le flux RSS est activé.
		
		// S'il y a lieu, analyse d'une requête effectuée par le client.
		if (code304($cheminFichierCache))
		{
			header('HTTP/1.1 304 Not Modified');
			
			exit(0);
		}
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if (!empty($infosCategorie['langue']))
		{
			$infosCategorieLangue = $infosCategorie['langue'];
		}
		else
		{
			$infosCategorieLangue = $langueParDefaut;
		}
		
		phpGettext('.', $infosCategorieLangue); // Nécessaire à la traduction.
		
		if ($dureeCacheRss && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCacheRss) && !$estPageCron)
		{
			if (file_exists($cheminFichierCacheEnTete))
			{
				$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
				
				if (!empty($contenuFichierCacheEnTete))
				{
					eval($contenuFichierCacheEnTete);
				}
			}
			
			@readfile($cheminFichierCache);
		}
		else
		{
			$itemsFluxRss = array ();
			$i = 0;
			
			if (!empty($infosCategorie['pages']))
			{
				foreach ($infosCategorie['pages'] as $page)
				{
					if ($i < $nombreItemsFluxRss)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", "$urlRacine/$page", $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCacheRss, $estPageCron);
					
						if (!empty($fluxRssPageTableauBrut))
						{
							$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
						}
					}
				
					$i++;
				}
			}
			
			if (!empty($itemsFluxRss))
			{
				$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
			}
			
			if (!empty($infosCategorie['url']))
			{
				$urlCategorie = $infosCategorie['url'];
			}
			else
			{
				$urlCategorie = urlCat($infosCategorie, $id);
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlCategorie, baliseTitleComplement($tableauBaliseTitleComplement, array ($infosCategorie['langue'], $langueParDefaut), FALSE), '', $idCategorie);
	
			if ($dureeCacheRss)
			{
				creeDossierCache($racine);
				$enregistrerCache = TRUE;
				
				if (file_exists($cheminFichierCache))
				{
					$codePageCache = @file_get_contents($cheminFichierCache);
					
					if ($codePageCache !== FALSE && md5($codePageCache) == md5($rssAafficher))
					{
						$enregistrerCache = FALSE;
					}
				}
				
				if ($enregistrerCache)
				{
					@file_put_contents($cheminFichierCache, $rssAafficher);
				}
				
				$enTetesHttp .= enTetesCache($cheminFichierCache, $dureeCacheRss);
				@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
			}
			
			if (!$estPageCron)
			{
				eval($enTetesHttp);
			}
			
			echo $rssAafficher;
		}
	}
	else
	{
		$erreur404 = TRUE;
	}
}
elseif ($getType == 'galeries' && !empty($getLangue))
{
	$listeGaleries = listeGaleries($racine);
	$listeGaleriesRss = array ();
	
	foreach ($listeGaleries as $idGalerie => $infosGalerie)
	{
		if (!empty($infosGalerie['rss']) && $infosGalerie['rss'] == 1)
		{
			$listeGaleriesRss[$idGalerie] = $infosGalerie;
		}
	}
	
	if ($galerieActiverFluxRssGlobal && !empty($listeGaleriesRss))
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// S'il y a lieu, analyse d'une requête effectuée par le client.
		if (code304($cheminFichierCache))
		{
			header('HTTP/1.1 304 Not Modified');
			
			exit(0);
		}
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCacheRss && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCacheRss) && !$estPageCron)
		{
			if (file_exists($cheminFichierCacheEnTete))
			{
				$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
				
				if (!empty($contenuFichierCacheEnTete))
				{
					eval($contenuFichierCacheEnTete);
				}
			}
			
			@readfile($cheminFichierCache);
		}
		else
		{
			$langue = $getLangue;
			include_once $racine . '/inc/constantes.inc.php';
			$itemsFluxRss = fluxRssGaleriesTableauBrut($racine, $urlRacine, $getLangue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
			
			if (!empty($itemsFluxRss))
			{
				$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut), FALSE), '', '');
			
			if ($dureeCacheRss)
			{
				creeDossierCache($racine);
				$enregistrerCache = TRUE;
				
				if (file_exists($cheminFichierCache))
				{
					$codePageCache = @file_get_contents($cheminFichierCache);
					
					if ($codePageCache !== FALSE && md5($codePageCache) == md5($rssAafficher))
					{
						$enregistrerCache = FALSE;
					}
				}
				
				if ($enregistrerCache)
				{
					@file_put_contents($cheminFichierCache, $rssAafficher);
				}
				
				$enTetesHttp .= enTetesCache($cheminFichierCache, $dureeCacheRss);
				@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
			}
			
			if (!$estPageCron)
			{
				eval($enTetesHttp);
			}
			
			echo $rssAafficher;
		}
	}
	else
	{
		$erreur404 = TRUE;
	}
}
elseif ($getType == 'site' && !empty($getLangue))
{
	$pages = super_parse_ini_file(cheminConfigFluxRssGlobalSite($racine), TRUE);
	
	if ($activerFluxRssGlobalSite && isset($pages[$getLangue]))
	{
		// A: le flux RSS global du site est activé.
		
		// S'il y a lieu, analyse d'une requête effectuée par le client.
		if (code304($cheminFichierCache))
		{
			header('HTTP/1.1 304 Not Modified');
			
			exit(0);
		}
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCacheRss && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCacheRss) && !$estPageCron)
		{
			if (file_exists($cheminFichierCacheEnTete))
			{
				$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
				
				if (!empty($contenuFichierCacheEnTete))
				{
					eval($contenuFichierCacheEnTete);
				}
			}
			
			@readfile($cheminFichierCache);
		}
		else
		{
			$langue = $getLangue;
			include_once $racine . '/inc/constantes.inc.php';
			$itemsFluxRss = array ();
			
			if (!empty($pages[$getLangue]['pages']))
			{
				$i = 0;
				
				foreach ($pages[$getLangue]['pages'] as $page)
				{
					if ($i < $nombreItemsFluxRss)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", $urlRacine . '/' . $page, $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCacheRss, $estPageCron);
						
						if (!empty($fluxRssPageTableauBrut))
						{
							$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
						}
					}
					
					$i++;
				}
			}
			
			if (!empty($itemsFluxRss))
			{
				$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut), FALSE), '', '');
			
			if ($dureeCacheRss)
			{
				creeDossierCache($racine);
				$enregistrerCache = TRUE;
				
				if (file_exists($cheminFichierCache))
				{
					$codePageCache = @file_get_contents($cheminFichierCache);
					
					if ($codePageCache !== FALSE && md5($codePageCache) == md5($rssAafficher))
					{
						$enregistrerCache = FALSE;
					}
				}
				
				if ($enregistrerCache)
				{
					@file_put_contents($cheminFichierCache, $rssAafficher);
				}
				
				$enTetesHttp .= enTetesCache($cheminFichierCache, $dureeCacheRss);
				@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
			}
			
			if (!$estPageCron)
			{
				eval($enTetesHttp);
			}
			
			echo $rssAafficher;
		}
	}
	else
	{
		$erreur404 = TRUE;
	}
}
else
{
	$erreur404 = TRUE;
}

if ($erreur404)
{
	header('HTTP/1.1 404 Not found');
}
?>
