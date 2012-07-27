<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML suivant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/(LANGUE/)page.dernier.inc.php`.

Étapes dans ce fichier:

1. Affectations, inclusions et traitement personnalisé optionnel.
2. Inclusion de code XHTML.
*/

########################################################################
##
## Affectations, inclusions et traitement personnalisé optionnel.
##
########################################################################

// Traitement personnalisé optionnel 1 de 2.

if (file_exists($racine . '/site/inc/dernier-pre.inc.php'))
{
	include $racine . '/site/inc/dernier-pre.inc.php';
}

// Affectations.

$cheminBasDePage = cheminXhtml($racine, array ($langue, $langueParDefaut), 'bas-de-page');

if (!empty($courrielContact) || ($partageCourrielActif && $partageCourrielInclureContact))
{
	$inclureContact = TRUE;
}
else
{
	$inclureContact = FALSE;
}

$premierOuDernier = 'dernier';

if (!empty($idCategorie) || !empty($nomCategorie))
{
	$afficherCategorie = TRUE;
}
else
{
	$afficherCategorie = FALSE;
}

if (!empty($tableauCorpsGalerie['corpsGalerie']))
{
	$galerie = $tableauCorpsGalerie['corpsGalerie'];
	$afficherGalerie = TRUE;
}
else
{
	$afficherGalerie = FALSE;
}

$linkScriptFin = linkScript($racine, $urlRacine, $fusionnerCssJs, '', $balisesLinkScriptFinales);

// Inclusions.

include $racine . '/inc/blocs.inc.php';
include $racine . '/inc/contact.inc.php';

// Traitement personnalisé optionnel 2 de 2.

if (file_exists($racine . '/site/inc/dernier.inc.php'))
{
	include $racine . '/site/inc/dernier.inc.php';
}

########################################################################
##
## Code XHTML 2 de 2.
##
########################################################################

include cheminXhtml($racine, array ($langue, $langueParDefaut), 'page.dernier');

if ($dureeCache && !$desactiverCache)
{
	$codePage = ob_get_contents();
	ob_end_clean();
	
	if ($tableDesMatieres)
	{
		$codePage = tableDesMatieres($codePage, 'div#milieuInterieurContenu', $tDmBaliseTable, $tDmBaliseTitre, $tDmNiveauDepart, $tDmNiveauArret);
	}
	
	creeDossierCache($racine);
	$enregistrerCache = TRUE;
	
	if (file_exists($cheminFichierCache))
	{
		$codePageCache = @file_get_contents($cheminFichierCache);
		
		if ($codePageCache !== FALSE && md5($codePageCache) == md5($codePage))
		{
			$enregistrerCache = FALSE;
		}
	}
	
	if ($enregistrerCache)
	{
		@file_put_contents($cheminFichierCache, $codePage);
	}
	
	$enTetesHttp .= 'header("Expires: ' . gmdate("D, d M Y H:i:s \G\M\T", time() + $dureeCache) . '");';
	$enTetesHttp .= 'header("Cache-Control: max-age=' . $dureeCache . '");';
	
	$dateFichierCache = @filemtime($cheminFichierCache);
	$tailleFichierCache = @filesize($cheminFichierCache);
	
	if ($dateFichierCache !== FALSE)
	{
		$enTetesHttp .= 'header("Last-Modified: ' . gmdate("D, d M Y H:i:s \G\M\T", $dateFichierCache) . '");';
	}
	
	if ($dateFichierCache !== FALSE && $tailleFichierCache !== FALSE)
	{
		$enTetesHttp .= 'header(\'ETag: "' . md5($dateFichierCache . $tailleFichierCache) . '"\');';
	}
	
	@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
	eval($enTetesHttp);
	
	echo $codePage;
}
?>
