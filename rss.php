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
	
			if ($rss && isset($idGalerie) && !empty($idGalerie) && file_exists("$racine/site/fichiers/galeries/" . $idGalerie) && file_exists("$racine/site/inc/galerie-" . $idGalerie . ".txt"))
			{
				$urlGalerie = $urlRacine . '/' . $_GET['chemin'];
				
				$itemsFlux = rssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxGlobalUrlOeuvre);
				$itemsFlux = rssGalerieTableauFinal($itemsFlux, $nbreItemsFlux);
				echo rssGalerie($idGalerie, baliseTitleComplement($baliseTitleComplement, $langue), $urlGalerie, $itemsFlux);
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
	if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.txt"))
	{
		$galeries = tableauAssociatif("$racine/site/inc/rss-global-galeries.txt");
		if (!empty($galeries))
		{
			$itemsFlux = array ();
			
			foreach ($galeries as $idGalerie => $urlGalerie)
			{
				$itemsFlux = array_merge($itemsFlux, rssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie));
			}
			
			$itemsFlux = rssGalerieTableauFinal($itemsFlux, $nbreItemsFlux);
			$idGalerie = FALSE;
			echo rssGalerie($idGalerie, baliseTitleComplement($baliseTitleComplement, $langue), ACCUEIL, $itemsFlux);
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}

elseif (isset($_GET['global']) && $_GET['global'] == 'site')
{
	//
}

?>
