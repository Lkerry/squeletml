<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

if (isset($_GET['langue']))
{
	$getLangue = sansEchappement($_GET['langue']);
}
else
{
	$getLangue = '';
}

// Nécessaire à la traduction.
phpGettext('.', $getLangue);

include_once $racine . '/inc/constantes.inc.php';
include_once $racine . '/inc/simplehtmldom/simple_html_dom.php';
include_once $racine . '/inc/filter_htmlcorrector/common.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/filter.inc.php';

if ($inclureApercu && $utiliserApercuDansFluxRss)
{
	$fluxRssAvecApercu = TRUE;
}
else
{
	$fluxRssAvecApercu = FALSE;
}

if (isset($_GET['chemin']))
{
	$getChemin = sansEchappement($_GET['chemin']);
	$getChemin = urlAvecIndex($urlRacine . '/' . $getChemin);
	$getChemin = str_replace($urlRacine . '/', '', $getChemin);
}
else
{
	$getChemin = '';
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

$url = url();

$erreur404 = FALSE;

if (empty($getLangue) || !isset($accueil[$getLangue]))
{
	$erreur404 = TRUE;
}
elseif ($getType == 'galerie' && !empty($getChemin))
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
		if (!empty($idGalerie))
		{
			if (!isset($rssGalerie))
			{
				$rssGalerie = $galerieActiverFluxRssParDefaut;
			}
			
			if ($rssGalerie && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && cheminConfigGalerie($racine, $idGalerie))
			{
				// A: le flux RSS est activé.
				
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
				
				$nomFichierCache = filtreChaine($racine, "rss-galerie-$idGalerie-$getLangue.cache.xml");
				
				if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
				{
					@readfile("$racine/site/cache/$nomFichierCache");
				}
				else
				{
					$urlGalerie = $urlRacine . '/' . $getChemin;
					$itemsFluxRss = fluxRssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					
					if (!empty($itemsFluxRss))
					{
						$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
					}
					
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut)), $idGalerie, '');
					
					if ($dureeCache['fluxRss'])
					{
						creeDossierCache($racine);
						@file_put_contents("$racine/site/cache/$nomFichierCache", $rssAafficher);
						@readfile("$racine/site/cache/$nomFichierCache");
					}
					else
					{
						echo $rssAafficher;
					}
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
	else
	{
		$erreur404 = TRUE;
	}
}
elseif ($getType == 'categorie' && (!empty($getChemin) || !empty($getId)))
{
	if (!empty($getChemin))
	{
		if (file_exists($racine . '/' . $getChemin) && $fic = @fopen($racine . '/' . $getChemin, 'r'))
		{
			while (!strstr($ligne, 'inc/premier.inc.php') && !feof($fic))
			{
				$ligne = rtrim(fgets($fic));
				
				if (preg_match('/\$rssCategorie\s*=\s*((TRUE|true|FALSE|false))\s*;/', $ligne, $resultat))
				{
					if ($resultat[1] == "TRUE" || $resultat[1] == "true")
					{
						$rssCategorie = TRUE;
					}
					elseif ($resultat[1] == "FALSE" || $resultat[1] == "false")
					{
						$rssCategorie = FALSE;
					}
				}
				
				if (preg_match('/\$idCategorie\s*=\s*[\'"](.+)[\'"]\s*;/', $ligne, $resultat))
				{
					$idCategorie = $resultat[1];
				}
			}
			
			fclose($fic);
		}
		else
		{
			$erreur404 = TRUE;
		}
	}
	
	if (!$erreur404)
	{
		if (!isset($rssCategorie))
		{
			$rssCategorie = $activerFluxRssCategorieParDefaut;
		}
		
		if (!empty($getId))
		{
			$idCategorie = $getId;
		}
		
		// Flux RSS de la catégorie.
		if ($rssCategorie && !empty($idCategorie))
		{
			$cheminConfigCategories = cheminConfigCategories($racine);
			
			if ($cheminConfigCategories)
			{
				$categories = super_parse_ini_file($cheminConfigCategories, TRUE);
			}
	
			if (isset($categories[$idCategorie]))
			{
				// A: le flux RSS est activé.
		
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
				$nomFichierCache = filtreChaine($racine, "rss-categorie-$idCategorie-$getLangue.cache.xml");
		
				if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
				{
					@readfile("$racine/site/cache/$nomFichierCache");
				}
				else
				{
					$itemsFluxRss = array ();
					$i = 0;
			
					foreach ($categories[$idCategorie]['pages'] as $page)
					{
						if ($i < $nombreItemsFluxRss)
						{
							$page = rtrim($page);
							$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", "$urlRacine/$page", $fluxRssAvecApercu);
					
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
					
					if (!empty($getChemin))
					{
						$urlCategorie = $urlRacine . '/' . $getChemin;
					}
					else
					{
						$urlCategorie = $urlRacine . '/categorie.php?id=' . $idCategorie;
					}
					
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlCategorie, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut)), '', $idCategorie);
			
					if ($dureeCache['fluxRss'])
					{
						creeDossierCache($racine);
						@file_put_contents("$racine/site/cache/$nomFichierCache", $rssAafficher);
						@readfile("$racine/site/cache/$nomFichierCache");
					}
					else
					{
						echo $rssAafficher;
					}
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
}
elseif ($getType == 'galeries')
{
	$galeries = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
	
	if ($galerieActiverFluxRssGlobal && isset($galeries[$getLangue]))
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		$nomFichierCache = filtreChaine($racine, "rss-galeries-$getLangue.cache.xml");
		
		if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$itemsFluxRss = fluxRssGaleriesTableauBrut($racine, $urlRacine, $getLangue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
			
			if (!empty($itemsFluxRss))
			{
				$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut)), '', '');
			
			if ($dureeCache['fluxRss'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $rssAafficher);
				@readfile("$racine/site/cache/$nomFichierCache");
			}
			else
			{
				echo $rssAafficher;
			}
		}
	}
	else
	{
		$erreur404 = TRUE;
	}
}
elseif ($getType == 'site')
{
	$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
	
	if ($activerFluxRssGlobalSite && isset($pages[$getLangue]))
	{
		// A: le flux RSS global du site est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		$nomFichierCache = filtreChaine($racine, "rss-site-$getLangue.cache.xml");
		
		if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$itemsFluxRss = array ();
			
			if (isset($pages[$getLangue]['pages']))
			{
				$i = 0;
				
				foreach ($pages[$getLangue]['pages'] as $page)
				{
					if ($i < $nombreItemsFluxRss)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", $urlRacine . '/' . $page, $fluxRssAvecApercu);
					
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
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($getLangue, $langueParDefaut)), '', '');
			
			if ($dureeCache['fluxRss'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $rssAafficher);
				@readfile("$racine/site/cache/$nomFichierCache");
			}
			else
			{
				echo $rssAafficher;
			}
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
