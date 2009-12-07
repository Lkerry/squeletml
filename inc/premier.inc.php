<?php
/*
Ce fichier gère l'inclusion des fichiers et l'initialisation des variables nécessaires à la construction de la structure XHTML précédant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/page.premier.inc.php`.

Étapes dans ce fichier:

1. Première série d'initialisations.
2. Première série d'inclusions.
3. Deuxième série d'initialisations.
4. Deuxième série d'inclusions.
5. Ajouts dans `$balisesLinkScript`.
6. Traitement personnalisé optionnel.
7. Inclusion de code XHTML.
*/

########################################################################
##
## Initialisations et inclusions.
##
########################################################################

// Initialisations 1 de 2.

extract(init(FALSE, 'idGalerie', 'langue'), EXTR_SKIP);

// Inclusions 1 de 2.

include_once dirname(__FILE__) . '/../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

include_once $racine . '/inc/fonctions.inc.php';

foreach (aInclureDebut($racine, $idGalerie) as $fichier)
{
	include_once $fichier;
}

// Initialisations 2 de 2.

extract(init('', 'baliseTitle', 'boitesDeroulantes', 'description', 'motsCles', 'robots'), EXTR_SKIP);
extract(init(FALSE, 'decouvrir', 'decouvrirInclureContact', 'estPageDerreur', 'rss'), EXTR_SKIP);

$baliseTitle = baliseTitle($baliseTitle, $baliseTitleComplement, array ($langue, $langueParDefaut));

$boitesDeroulantesTableau = boitesDeroulantes($boitesDeroulantesParDefaut, $boitesDeroulantes);

$cheminAncres = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'ancres');

$cheminFaireDecouvrir = $racine . '/inc/faire-decouvrir.inc.php';

$cheminSousTitre = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'sous-titre');

$cheminSurTitre = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'sur-titre');

$classesBody = classesBody(estAccueil(ACCUEIL), $idGalerie, $deuxColonnes, $deuxColonnesSousContenuAgauche, $uneColonneAgauche, $differencierLiensVisitesHorsContenu, $arrierePlanColonne, $borduresPage, $blocsArrondis);

if (!empty($classesBody))
{
	$classesBody = ' class="' . $classesBody . '"';
}

if (!$differencierLiensVisitesHorsContenu)
{
	$classesContenu = ' class="liensVisitesDifferencies"';
}
else
{
	$classesContenu = '';
}

if (isset($courrielContact) && $courrielContact == '@' && !empty($contactCourrielParDefaut))
{
	$courrielContact = $contactCourrielParDefaut;
}

$divSurSousContenu = 'sur';

$doctype = doctype($xhtmlStrict);

if (!galerieExiste($racine, $idGalerie))
{
	$idGalerie = FALSE;
}

if ($afficherMessageIe6)
{
	$messageIe6 = messageIe6($urlRacine);
}

if ($inclureMotsCles)
{
	$motsCles = motsCles($motsCles, $description);
}

$nomSite = nomSite(estAccueil(ACCUEIL), lienAccueil(ACCUEIL, estAccueil(ACCUEIL), titreSite($titreSite, array ($langue, $langueParDefaut))));

$robots = robots($robotsParDefaut, $robots);

if ($idGalerie && !isset($rss))
{
	$rss = $galerieActiverFluxRssParDefaut;
}

if (isset($corpsGalerie) && !empty($corpsGalerie))
{
	$tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $blocsArrondis);
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = $afficherTableDesMatieresParDefaut;
}

if ($tableDesMatieres)
{
	$boitesDeroulantes .= '|tableDesMatieres';
	$locale = locale(LANGUE);
}

// Inclusions 2 de 2.

include $racine . '/inc/blocs.inc.php';

########################################################################
##
## Ajouts dans `$balisesLinkScript`.
##
########################################################################

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau))
{
	$balisesLinkScript[] = url(FALSE) . "#css#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.min.js";
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.cookie.js";
	$jsDirect = '';
	
	foreach ($boitesDeroulantesTableau as $boiteDeroulante)
	{
		$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante');});\n";
	}
	
	$balisesLinkScript[] = url(FALSE) . "#jsDirect#$jsDirect";
}

// Flux RSS.

if ($idGalerie && $rss)
{
	$urlFlux = "$urlRacine/rss.php?chemin=" . str_replace($urlRacine . '/', '', url(FALSE));
	$balisesLinkScript[] = url(FALSE) . "#rss#$urlFlux#" . sprintf(T_('RSS de la galerie %1$s'), $idGalerie);
}

if ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries'))
{
	$urlFlux = $urlRacine . '/rss.php?global=galeries&amp;langue=' . LANGUE;
	$balisesLinkScript[] = url(FALSE) . "#rss#$urlFlux#" . T_('RSS de toutes les galeries');
}

if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
{
	$urlFlux = $urlRacine . '/rss.php?global=pages&amp;langue=' . LANGUE;
	$balisesLinkScript[] = url(FALSE) . "#rss#$urlFlux#" . T_('RSS global du site');
}

// Slimbox2.

if (($galerieAccueilJavascript || $galerieLienOriginalJavascript) && $idGalerie)
{
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.min.js";
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/slimbox2/js/slimbox2.js";
	$balisesLinkScript[] = url(FALSE) . "#css#$urlRacine/js/slimbox2/css/slimbox2.css";
}

// Table des matières.

if ($tableDesMatieres)
{
	$balisesLinkScript[] = url(FALSE) . "#css#$urlRacine/css/table-des-matieres.css";
	$balisesLinkScript[] = url(FALSE) . "#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/Gettext/lib/Gettext.js";
	
	if (file_exists($racine . '/locale/' . $locale))
	{
		$balisesLinkScript[] = url(FALSE) . "#po#$urlRacine/locale/$locale/LC_MESSAGES/squeletml.po";
	}
	
	$balisesLinkScript[] = url(FALSE) . "#jsDirect#var gt = new Gettext({'domain': 'squeletml'});";
	
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery.min.js";
	$balisesLinkScript[] = url(FALSE) . "#js#$urlRacine/js/jquery-tableofcontents/jquery.tableofcontents.js";
	$balisesLinkScript[] = url(FALSE) . "#jsDirect#tableDesMatieres('interieurContenu', 'ul');";
}

// Message pour IE6.

if ($afficherMessageIe6)
{
	$balisesLinkScript[] = url(FALSE) . "#cssltIE7#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = url(FALSE) . "#jsltIE7#$urlRacine/js/jquery.min.js";
	$balisesLinkScript[] = url(FALSE) . "#jsltIE7#$urlRacine/js/jquery.cookie.js";
	$balisesLinkScript[] = url(FALSE) . "#jsDirectltIE7#ajouteEvenementLoad(function(){boiteDeroulante('messageIe6');});";
}

// Variable finale.

if (!$inclureCssParDefaut)
{
	supprimeInclusionCssParDefaut($balisesLinkScript);
}

$linkScript = linkScript($balisesLinkScript, $versionFichiersLinkScript);

########################################################################
##
## Traitement personnalisé optionnel.
##
########################################################################

if (file_exists($racine . '/site/inc/premier.inc.php'))
{
	include_once $racine . '/site/inc/premier.inc.php';
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include_once cheminXhtml($racine, 'page.premier');
?>
