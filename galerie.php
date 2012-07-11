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
	$idGalerie = $getId;
	
	$galeries = super_parse_ini_file(cheminConfigGaleries($racine), TRUE);
	
	if (!empty($galeries))
	{
		$idReel = idGalerie($racine, $categories, $idCategorie);
		
		if (!empty($idReel))
		{
			$idGalerie = $idReel;
		}
	}
}

if ($idGalerie == 'démo')
{
	$rssGalerie = FALSE;
	$robots = "noindex, follow, noarchive"; // Empêche la présence de la galerie démo dans les moteurs de recherche.
}
elseif (
	empty($idGalerie) ||
	!isset($galeries[$idGalerie]) ||
	(!empty($galeries[$idGalerie]['url']) && strpos($galeries[$idGalerie]['url'], 'galerie.php?id=' . filtreChaine($racine, $idGalerie)) === FALSE) || // Empêcher la duplication de contenu dans les moteurs de recherche.
	($getId != filtreChaine($racine, $getId)) // Idem.
)
{
	$erreur404 = TRUE;
}

$pageGlobaleGalerie = TRUE;
include $racine . '/inc/premier.inc.php';
include $racine . '/inc/dernier.inc.php';
?>
