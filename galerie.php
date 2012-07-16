<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include $cheminFichier;
}

if (!empty($_GET['id']))
{
	$getId = securiseTexte($_GET['id']);
	
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
			$idReel = idGalerie($racine, $galeries, $idGalerie);
			
			if (!empty($idReel))
			{
				$idGalerie = $idReel;
			}
		}
	}
}

if (!empty($_GET['langue']))
{
	$getLangue = securiseTexte($_GET['langue']);
	
	if (isset($accueil[$getLangue]))
	{
		$langue = $getLangue;
	}
}

if ($activerGalerieDemo && $getId != 'démo' && $idGalerie == 'démo' && isset($langue))
{
	$rssGalerie = FALSE;
	$robots = "noindex, follow, noarchive"; // Empêche la présence de la galerie démo dans les moteurs de recherche.
}
elseif (
	($getId != 'démo' && $idGalerie == 'démo') ||
	isset($_GET['langue']) ||
	empty($idGalerie) ||
	!isset($galeries[$idGalerie]) ||
	(!empty($galeries[$idGalerie]['url']) && strpos($galeries[$idGalerie]['url'], 'galerie.php?id=' . filtreChaine($racine, $idGalerie)) === FALSE) || // Empêcher la duplication de contenu dans les moteurs de recherche.
	($getId != filtreChaine($racine, $getId)) // Idem.
)
{
	$erreur404 = TRUE;
}

if (isset($galeries[$idGalerie]['langue']))
{
	$langue = $galeries[$idGalerie]['langue'];
}

$pageGlobaleGalerie = TRUE;
include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
