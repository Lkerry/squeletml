<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML suivant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier lors de la vérification du cache global ou lors de l'inclusion du fichier `(site/)xhtml/(LANGUE/)page.dernier.inc.php`.

Étapes dans ce fichier:

1. Affectations, inclusions et traitement personnalisé optionnel.
2. Inclusion de code XHTML et vérification du cache global.
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

// Affectations 1 de 2.

$cheminBasDePage = cheminXhtml($racine, array ($langue, $langueParDefaut), 'bas-de-page');

if (!empty($courrielContact) || ($partageCourrielActif && $partageCourrielInclureContact))
{
	$inclureContact = TRUE;
}
else
{
	$inclureContact = FALSE;
}

if ($ajoutCommentaires && isset($_GET['action']) && $_GET['action'] == 'commentaire' && !$erreur404 && !$estPageDerreur && !$estAccueil && empty($courrielContact) && empty($idCategorie))
{
	$inclureFormulaireCommentaire = TRUE;
}
else
{
	$inclureFormulaireCommentaire = FALSE;
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
	$galerie = '<div id="galerie">' . "\n" . $tableauCorpsGalerie['corpsGalerie'] . "</div><!-- /#galerie -->\n";
	$afficherGalerie = TRUE;
}
else
{
	$afficherGalerie = FALSE;
}

$linkScriptFin = linkScript($racine, $urlRacine, $fusionnerCssJs, '', $balisesLinkScriptFinales);

// Inclusions.

if ($inclureFormulaireCommentaire)
{
	include $racine . '/inc/commentaire.inc.php';
}

include $racine . '/inc/blocs.inc.php';

if ($inclureContact)
{
	include $racine . '/inc/contact.inc.php';
}

// Affectations 2 de 2.

$inclureFinInterieurContenu = FALSE;

if (!empty($blocs[400]) || $inclureContact || $inclureFormulaireCommentaire)
{
	$inclureFinInterieurContenu = TRUE;
}

// Traitement personnalisé optionnel 2 de 2.
if (file_exists($racine . '/site/inc/dernier.inc.php'))
{
	include $racine . '/site/inc/dernier.inc.php';
}

########################################################################
##
## Code XHTML 2 de 2 et vérification du cache global.
##
########################################################################

include cheminXhtml($racine, array ($langue, $langueParDefaut), 'page.dernier');

// Vérification du cache global.
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
	
	$enTetesHttp .= enTetesCache($cheminFichierCache, $dureeCache);
	@file_put_contents($cheminFichierCacheEnTete, $enTetesHttp);
	
	if (!$estPageCron && !$estVisiteSimulee)
	{
		eval($enTetesHttp);
	}
	
	echo $codePage;
}
?>
