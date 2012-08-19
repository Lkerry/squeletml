<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include $cheminFichier;
}

if (!empty($_GET['id']))
{
	$getId = $_GET['id'];
	
	if ($getId == 'demo')
	{
		$idGalerie = 'démo';
	}
	else
	{
		$idGalerie = $getId;
		$galeries = super_parse_ini_file(cheminConfigGaleries($racine), TRUE);
		
		if (!empty($galeries))
		{
			$idReel = idGalerie($galeries, $idGalerie);
			
			if (!empty($idReel))
			{
				$idGalerie = $idReel;
			}
		}
	}
}

if (!empty($_GET['langue']) && isset($accueil[$_GET['langue']]))
{
	$langue = $_GET['langue'];
}

if ($activerGalerieDemo && $getId != 'démo' && $idGalerie == 'démo' && isset($langue))
{
	$rssGalerie = FALSE;
	$robots = "noindex, follow, noarchive"; // Empêche la présence de la galerie démo dans les moteurs de recherche.
}
elseif (
	($getId != 'démo' && $idGalerie == 'démo') ||
	!isset($langue) ||
	empty($idGalerie) ||
	!isset($galeries[$idGalerie]) ||
	(!empty($galeries[$idGalerie]['url']) && (strpos($galeries[$idGalerie]['url'], 'galerie.php?') !== 0 || !preg_match('/(\?|&|&amp;)id=' . preg_quote(filtreChaine($idGalerie), '/') . '/', $galeries[$idGalerie]['url']))) || // Empêcher la duplication de contenu dans les moteurs de recherche.
	($getId != filtreChaine($getId)) // Idem.
)
{
	$erreur404 = TRUE;
}

$pageGlobaleGalerie = TRUE;
include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
