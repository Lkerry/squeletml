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
}
else
{
	$getChemin = '';
}

if (isset($_GET['langue']))
{
	$getLangue = sansEchappement($_GET['langue']);
}
else
{
	$getLangue = '';
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

if ($getType == 'galerie' && !empty($getChemin))
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
				
				$nomFichierCache = 'rss-galerie-' . md5($idGalerie) . '.cache.xml';
				
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
					
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $urlGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), $idGalerie, '');
					
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
elseif ($getType == 'categorie' && !empty($getChemin))
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
		
		if (!isset($rssCategorie))
		{
			$rssCategorie = $activerFluxRssCategorieParDefaut;
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
		
				$nomFichierCache = 'rss-categorie-' . md5($idCategorie) . '.cache.xml';
		
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
							$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", $urlRacine . "/" . superRawurlencode($page), $fluxRssAvecApercu);
					
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
					
					$urlCategorie = $urlRacine . '/' . $getChemin;
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $urlCategorie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), '', $idCategorie);
			
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
elseif ($getType == 'galeries' && !empty($getLangue) && isset($accueil[$getLangue]))
{
	$cheminConfigFluxRssGlobalGaleries = cheminConfigFluxRssGlobal($racine, 'galeries');
	
	if ($galerieActiverFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		$nomFichierCache = 'rss-galeries-' . md5($getLangue) . '.cache.xml';
		
		if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$galeries = super_parse_ini_file($cheminConfigFluxRssGlobalGaleries, TRUE);
			$itemsFluxRss = array ();
			
			if (!empty($galeries))
			{
				foreach ($galeries as $codeLangue => $langueInfos)
				{
					if ($codeLangue == $getLangue)
					{
						foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
						{
							$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger));
						}
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
				}
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), '', '');
			
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
elseif ($getType == 'site' && !empty($getLangue) && isset($accueil[$getLangue]))
{
	$cheminConfigFluxRssGlobalGaleries = cheminConfigFluxRssGlobal($racine, 'galeries');
	
	if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
	{
		// A: le flux RSS global du site est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		$nomFichierCache = 'rss-site-' . md5($getLangue) . '.cache.xml';
		
		if ($dureeCache['fluxRss'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['fluxRss']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
			$itemsFluxRss = array ();
			
			if (!empty($pages))
			{
				$i = 0;
				
				foreach ($pages as $codeLangue => $langueInfos)
				{
					if ($codeLangue == $getLangue && $i < $nombreItemsFluxRss)
					{
						foreach ($langueInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", $urlRacine . "/" . superRawurlencode($page), $fluxRssAvecApercu);
							
							if (!empty($fluxRssPageTableauBrut))
							{
								$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
							}
						}
					}
					
					$i++;
				}
				
				// On vérifie si les galeries ont leur flux RSS global, et si oui, on les inclut dans le flux RSS global du site.
				
				$galeriesDansFluxRssGlobalSite = FALSE;
				
				if ($galerieActiverFluxRssGlobal && $cheminConfigFluxRssGlobalGaleries)
				{
					$galeries = super_parse_ini_file($cheminConfigFluxRssGlobalGaleries, TRUE);
					
					if (!empty($galeries))
					{
						foreach ($galeries as $codeLangue => $langueInfos)
						{
							if ($codeLangue == $getLangue)
							{
								$galeriesDansFluxRssGlobalSite = TRUE;
								
								foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
								{
									$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger));
								}
							}
						}
					}
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss, $galeriesDansFluxRssGlobalSite);
				}
			}
			
			$rssAafficher = fluxRss($getType, $itemsFluxRss, ACCUEIL, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut)), '', '');
			
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
