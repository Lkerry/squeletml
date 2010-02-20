<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML suivant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/(LANGUE/)page.dernier.inc.php`.

Étapes dans ce fichier:

1. Affectations.
2. Inclusions.
3. Inclusion de code XHTML.
*/

########################################################################
##
## Affectations et inclusions.
##
########################################################################

// Affectations.

$cheminBasDePage = cheminXhtml($racine, array ($langue, $langueParDefaut), 'bas-de-page');

if (!empty($courrielContact) || ($decouvrir && $decouvrirInclureContact))
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

$linkScriptFin = linkScript($balisesLinkScriptFinales);

// Inclusions.

include $racine . '/inc/blocs.inc.php';
include_once $racine . '/inc/contact.inc.php';

// Traitement personnalisé optionnel.

if (file_exists($racine . '/site/inc/dernier.inc.php'))
{
	include_once $racine . '/site/inc/dernier.inc.php';
}

########################################################################
##
## Code XHTML 2 de 2.
##
########################################################################

include_once cheminXhtml($racine, array ($langue, $langueParDefaut), 'page.dernier');
?>
