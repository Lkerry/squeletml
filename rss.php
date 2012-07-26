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
	$getLangue = sansEchappement($_GET['langue']);
	phpGettext('.', $getLangue); // Nécessaire à la traduction.
}
else
{
	$getLangue = '';
}

include_once $racine . '/inc/simplehtmldom/simple_html_dom.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/common.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/filter.inc.php';
include_once $racine . '/inc/node_teaser/node.inc.php';
include_once $racine . '/inc/node_teaser/unicode.inc.php';

if ($dureeCache)
{
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url, FALSE);
}

$enTetesHttp = 'header("Content-Type: text/xml; charset=' . $charset . '");';

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
	$getId = sansEchappement($_GET['id']);
}
else
{
	$getId = '';
}

if (isset($_GET['type']))
{
	$getType = sansEchappement($_GET['type']);
}
else
{
	$getType = '';
}

$erreur404 = FALSE;

if ($getType == 'galerie' && !empty($getId) && !empty($getLangue))
{
	$galeries = galeries($racine);
	
	foreach ($galeries as $idGalerie => $infosGalerie)
	{
		if ($getId == filtreChaine($racine, $idGalerie))
		{
			$id = $idGalerie;
			
			if ($infosGalerie['rss'] == 1)
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
			
			eval($enTetesHttp);
			
			// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
			if ($dureeCache && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache))
			{
				@readfile($cheminFichierCache);
			}
			else
			{
				$itemsFluxRss = fluxRssGalerieTableauBrut($racine, $urlRacine, $langueParDefaut, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
				}
				
				$urlGalerie = $urlRacine . '/' . $infosGalerie['url'];
				$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut), FALSE), $idGalerie, '');
				
				if ($dureeCache)
				{
					creeDossierCache($racine);
					@file_put_contents($cheminFichierCache, $rssAafficher);
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
			if ($getId == filtreChaine($racine, $idCategorie))
			{
				$id = $idCategorie;
				
				if ($infosCategorie['rss'] == 1)
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
		
		eval($enTetesHttp);
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		phpGettext('.', $infosCategorie['langue']); // Nécessaire à la traduction.
		
		if ($dureeCache && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache))
		{
			@readfile($cheminFichierCache);
		}
		else
		{
			$itemsFluxRss = array ();
			$i = 0;
			
			foreach ($infosCategorie['pages'] as $page)
			{
				if ($i < $nombreItemsFluxRss)
				{
					$page = rtrim($page);
					$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", "$urlRacine/$page", $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCache);
					
					if (!empty($fluxRssPageTableauBrut))
					{
						$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
					}
				}
				
				$i++;
			}
			
			if (!empty($itemsFluxRss))
			{
				$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $infosCategorie['url'], baliseTitleComplement($tableauBaliseTitleComplement, array ($infosCategorie['langue'], $langueParDefaut), FALSE), '', $idCategorie);
	
			if ($dureeCache)
			{
				creeDossierCache($racine);
				@file_put_contents($cheminFichierCache, $rssAafficher);
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
	$galeries = galeries($racine);
	$listeGaleriesRss = array ();
	
	foreach ($galeries as $idGalerie => $infosGalerie)
	{
		if ($infosGalerie['rss'] == 1)
		{
			$listeGaleriesRss[$idGalerie] = $infosGalerie;
		}
	}
	
	if ($galerieActiverFluxRssGlobal && !empty($listeGaleriesRss))
	{
		// A: le flux RSS global pour les galeries est activé.
		
		eval($enTetesHttp);
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCache && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache))
		{
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
			
			if ($dureeCache)
			{
				creeDossierCache($racine);
				@file_put_contents($cheminFichierCache, $rssAafficher);
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
		
		eval($enTetesHttp);
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCache && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache))
		{
			@readfile($cheminFichierCache);
		}
		else
		{
			$langue = $getLangue;
			include_once $racine . '/inc/constantes.inc.php';
			$itemsFluxRss = array ();
			
			if (isset($pages[$getLangue]['pages']))
			{
				$i = 0;
				
				foreach ($pages[$getLangue]['pages'] as $page)
				{
					if ($i < $nombreItemsFluxRss)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", $urlRacine . '/' . $page, $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCache);
						
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
			
			if ($dureeCache)
			{
				creeDossierCache($racine);
				@file_put_contents($cheminFichierCache, $rssAafficher);
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
