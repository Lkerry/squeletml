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

include_once $racine . '/inc/simplehtmldom/simple_html_dom.php';
include_once $racine . '/inc/filter_htmlcorrector/common.inc.php';
include_once $racine . '/inc/filter_htmlcorrector/filter.inc.php';
include_once $racine . '/inc/node_teaser/node.inc.php';
include_once $racine . '/inc/node_teaser/unicode.inc.php';

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
	$getCheminOriginal = sansEchappement($_GET['chemin']);
	$getChemin = urlAvecIndex($urlRacine . '/' . $getCheminOriginal);
	$getChemin = str_replace($urlRacine . '/', '', $getChemin);
}
else
{
	$getCheminOriginal = '';
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

if ($dureeCache)
{
	$nomFichierCache = nomFichierCache($racine, $urlRacine, $url, FALSE);
}

$erreur404 = FALSE;

if ((!empty($getChemin) && !empty($getId)) || preg_match('|(?<=/)index\.php$|', $getCheminOriginal))
{
	$erreur404 = TRUE;
}
elseif ($getType == 'galerie' && !empty($getChemin) && empty($getLangue))
{
	if (file_exists($racine . '/' . $getChemin))
	{
		$tableauConfigGalerie = fluxRssConfigGalerie($racine . '/' . $getChemin);
		
		if ($tableauConfigGalerie[0] != NULL)
		{
			$rssGalerie = $tableauConfigGalerie[0];
		}
		
		if ($tableauConfigGalerie[1] != NULL)
		{
			$idGalerie = $tableauConfigGalerie[1];
		}
		
		if ($tableauConfigGalerie[2] != NULL)
		{
			$langue = $tableauConfigGalerie[2];
		}
		
		// Flux RSS de la galerie.
		if (!empty($idGalerie))
		{
			if (!isset($rssGalerie))
			{
				$rssGalerie = $galerieActiverFluxRssParDefaut;
			}
			
			$idGalerieDossier = idGalerieDossier($racine, $idGalerie);
			
			if ($rssGalerie && file_exists("$racine/site/fichiers/galeries/$idGalerieDossier") && cheminConfigGalerie($racine, $idGalerieDossier))
			{
				// A: le flux RSS est activé.
				
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
				
				if (!isset($langue))
				{
					$langue = $langueParDefaut;
				}
				
				phpGettext('.', $langue); // Nécessaire à la traduction.
				
				if ($dureeCache && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache))
				{
					@readfile("$racine/site/cache/$nomFichierCache");
				}
				else
				{
					$urlGalerie = $urlRacine . '/' . $getChemin;
					$itemsFluxRss = fluxRssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
					
					if (!empty($itemsFluxRss))
					{
						$itemsFluxRss = fluxRssTableauFinal($getType, $itemsFluxRss, $nombreItemsFluxRss);
					}
					
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlGalerie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut), FALSE), $idGalerie, '');
					
					if ($dureeCache)
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
elseif ($getType == 'categorie' && (!empty($getChemin) || !empty($getId)) && empty($getLangue))
{
	if (!empty($getChemin))
	{
		if (file_exists($racine . '/' . $getChemin))
		{
			$tableauConfigCategorie = fluxRssConfigCategorie($racine . '/' . $getChemin);
			
			if ($tableauConfigCategorie[0] != NULL)
			{
				$rssCategorie = $tableauConfigCategorie[0];
			}
			
			if ($tableauConfigCategorie[1] != NULL)
			{
				$idCategorie = $tableauConfigCategorie[1];
			}
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
			
			if (!empty($getId) && !empty($categories))
			{
				$idReel = idCategorie($racine, $categories, $getId);
				
				if (!empty($idReel))
				{
					$idCategorie = $idReel;
				}
			}
			
			if (
				isset($categories[$idCategorie]) &&
				(empty($getId) || ($getId == filtreChaine($racine, $getId))) && // Pour éviter la duplication de contenu dans les moteurs de recherche.
				((!empty($getId) && (empty($categories[$idCategorie]['urlCat']) || strpos($categories[$idCategorie]['urlCat'], 'categorie.php?id=') !== FALSE)) || (!empty($getChemin) && !empty($categories[$idCategorie]['urlCat']) && strpos($categories[$idCategorie]['urlCat'], 'categorie.php?id=') === FALSE))
			)
			{
				// A: le flux RSS est activé.
				
				// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
				
				$langue = langueCat($categories[$idCategorie], $langueParDefaut);
				
				phpGettext('.', $langue); // Nécessaire à la traduction.
				
				if ($dureeCache && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache))
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
					
					if (!empty($getChemin))
					{
						$urlCategorie = $urlRacine . '/' . $getChemin;
					}
					else
					{
						$urlCategorie = $urlRacine . '/' . urlCat($racine, $categories[$idCategorie], $idCategorie, $langueParDefaut);
					}
					
					$rssAafficher = fluxRss($getType, $itemsFluxRss, $url, $urlCategorie, baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut), FALSE), '', $idCategorie);
			
					if ($dureeCache)
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
elseif ($getType == 'galeries' && !empty($getLangue))
{
	$galeries = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
	
	if ($galerieActiverFluxRssGlobal && isset($galeries[$getLangue]))
	{
		// A: le flux RSS global pour les galeries est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCache && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
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
elseif ($getType == 'site' && !empty($getLangue))
{
	$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
	
	if ($activerFluxRssGlobalSite && isset($pages[$getLangue]))
	{
		// A: le flux RSS global du site est activé.
		
		// On vérifie si le flux RSS existe en cache ou si le cache est expiré.
		
		if ($dureeCache && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
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
