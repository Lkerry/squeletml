<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML précédant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/page.premier.inc.php`.

Étapes dans ce fichier:

1. Première série d'inclusions.
2. Première série d'affectations.
3. Deuxième série d'inclusions.
4. Deuxième série d'affectations.
5. Troisième série d'inclusions.
6. Troisième série d'affectations.
7. Ajouts dans `$balisesLinkScript`.
8. Traitement personnalisé optionnel.
9. En-têtes HTTP.
10. Inclusion de code XHTML.
*/

########################################################################
##
## Affectations et inclusions.
##
########################################################################

// Inclusions 1 de 3.

include_once dirname(__FILE__) . '/../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

include_once $racine . '/inc/fonctions.inc.php';

// Affectations 1 de 3.

extract(init('', 'langue'), EXTR_SKIP);

// Inclusions 2 de 3.

foreach (aInclureDebut($racine) as $fichier)
{
	include_once $fichier;
}

// Affectations 2 de 3.

extract(init('', 'apercu', 'baliseTitle', 'boitesDeroulantes', 'classesBody', 'classesContenu', 'courrielContact', 'dateCreation', 'dateRevision', 'description', 'enTetesHttp', 'idCategorie', 'idGalerie', 'motsCles', 'robots'), EXTR_SKIP);
extract(init(FALSE, 'decouvrir', 'decouvrirInclureContact', 'erreur404', 'estPageDerreur'), EXTR_SKIP);

if (!empty($apercu))
{
	$apercu = "<!-- APERÇU: $apercu -->";
}

if (!isset($auteur))
{
	$auteur = $auteurParDefaut;
}

$baliseTitle = baliseTitle($baliseTitle, array ($langue, $langueParDefaut));
$baliseTitleComplement = baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut));
$cheminAncres = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'ancres');
$cheminFaireDecouvrir = $racine . '/inc/faire-decouvrir.inc.php';
$cheminSousTitre = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'sous-titre');
$cheminSurTitre = cheminXhtmlLangue($racine, array ($langue, $langueParDefaut), 'sur-titre');
$estAccueil = estAccueil(ACCUEIL);
$classesBody = classesBody($estAccueil, $idGalerie, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $borduresPage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $tableDesMatieresArrondie, $classesBody);

if (!empty($classesBody))
{
	$classesBody = ' class="' . $classesBody . '"';
}

$classesContenu = classesContenu($differencierLiensVisitesHorsContenu, $classesContenu);

if (!empty($classesContenu))
{
	$classesContenu = ' class="' . $classesContenu . '"';
}

if ($courrielContact == '@' && !empty($contactCourrielParDefaut))
{
	$courrielContact = $contactCourrielParDefaut;
}

$doctype = doctype($xhtmlStrict);

if (!isset($faireDecouvrir))
{
	$faireDecouvrir = $activerFaireDecouvrirParDefaut;
}

if (!isset($infosPublication))
{
	$infosPublication = $afficherInfosPublicationParDefaut;
}

if (!isset($licence))
{
	$licence = $licenceParDefaut;
}

if (!isset($marquePagesSociaux))
{
	$marquePagesSociaux = $activerMarquePagesSociauxParDefaut;
}

if ($marquePagesSociaux)
{
	$boitesDeroulantes .= ' #marquePagesSociaux';
}

if ($afficherMessageIe6)
{
	$messageIe6 = messageIe6($urlRacine);
}

if ($inclureMotsCles)
{
	$motsCles = motsCles($motsCles, $description);
}

$nomSite = nomSite($estAccueil, lienAccueil(ACCUEIL, $estAccueil, titreSite($titreSite, array ($langue, $langueParDefaut))));
$nomPage = nomPage();
$premierOuDernier = 'premier';
$robots = robots($robotsParDefaut, $robots);

if (!isset($rssCategorie))
{
	$rssCategorie = $activerFluxRssCategorieParDefaut;
}

if (!isset($rssGalerie))
{
	$rssGalerie = $galerieActiverFluxRssParDefaut;
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = $afficherTableDesMatieresParDefaut;
}

if (!empty($idCategorie))
{
	$tableDesMatieres = FALSE;
}

if ($tableDesMatieres)
{
	$boitesDeroulantes .= ' #tableDesMatieres';
	$locale = locale(LANGUE);
}

$url = url();
$urlAvecIndexSansGet = url(FALSE, TRUE, TRUE);
$urlFichiers = $urlRacine . '/site/fichiers';
$urlRacineAdmin = $urlRacine . '/' . $dossierAdmin;
$urlSansGet = url(FALSE);
$urlSite = $urlRacine . '/site';

// Inclusions 3 de 3.

if (!empty($idCategorie))
{
	include_once $racine . '/inc/categorie.inc.php';
}

if (!empty($idGalerie))
{
	include_once $racine . '/inc/galerie.inc.php';
}

include $racine . '/inc/blocs.inc.php';

// Affectations 3 de 3.

$boitesDeroulantesTableau = boitesDeroulantes($boitesDeroulantesParDefaut, $boitesDeroulantes);

if ($erreur404 || $estPageDerreur)
{
	$robots = 'noindex, follow, noarchive';
}

if ($erreur404)
{
	$enTetesHttp .= "header('HTTP/1.1 404 Not found');";
}

########################################################################
##
## Ajouts dans `$balisesLinkScript`.
##
########################################################################

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau))
{
	$balisesLinkScript[] = "$url#css#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.cookie.js";
	$jsDirect = '';
	
	foreach ($boitesDeroulantesTableau as $boiteDeroulante)
	{
		$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante');});\n";
	}
	
	$balisesLinkScript[] = "$url#jsDirect#$jsDirect";
}

// Flux RSS.

if (!empty($idGalerie) && $rssGalerie)
{
	$urlFlux = "$urlRacine/rss.php?type=galerie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlAvecIndexSansGet);
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('RSS de la galerie %1$s'), $idGalerie);
}

if (!empty($idCategorie) && $rssCategorie)
{
	$urlFlux = "$urlRacine/rss.php?type=categorie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlAvecIndexSansGet);
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('RSS de la catégorie %1$s'), $idCategorie);
}

if ($galerieActiverFluxRssGlobal && cheminConfigFluxRssGlobal($racine, 'galeries'))
{
	$urlFlux = $urlRacine . '/rss.php?type=galeries&amp;langue=' . LANGUE;
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('RSS de toutes les galeries');
}

if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobal($racine, 'site'))
{
	$urlFlux = $urlRacine . '/rss.php?type=site&amp;langue=' . LANGUE;
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('RSS global du site');
}

// Slimbox2.

if (($galerieAccueilJavascript || $galerieLienOriginalJavascript) && !empty($idGalerie))
{
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/slimbox2/js/slimbox2.js";
	$balisesLinkScript[] = "$url#css#$urlRacine/js/slimbox2/css/slimbox2.css";
}

// Table des matières.

if ($tableDesMatieres)
{
	$balisesLinkScript[] = "$url#css#$urlRacine/css/table-des-matieres.css";
	$balisesLinkScript[] = "$url#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	
	$balisesLinkScript[] = "$url#js#$urlRacine/js/Gettext/lib/Gettext.js";
	
	if (file_exists($racine . '/locale/' . $locale))
	{
		$balisesLinkScript[] = "$url#po#$urlRacine/locale/$locale/LC_MESSAGES/squeletml.po";
	}
	
	$balisesLinkScript[] = "$url#jsDirect#var gt = new Gettext({'domain': 'squeletml'});";
	
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery-tableofcontents/jquery.tableofcontents.js";
	$balisesLinkScript[] = "$url#jsDirect#tableDesMatieres('interieurContenu', 'ul', 'h2');";
}

// Message pour IE6.

if ($afficherMessageIe6)
{
	$balisesLinkScript[] = "$url#cssltIE7#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = "$url#jsltIE7#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#jsltIE7#$urlRacine/js/jquery/jquery.cookie.js";
	$balisesLinkScript[] = "$url#jsDirectltIE7#ajouteEvenementLoad(function(){boiteDeroulante('#messageIe6');});";
}

if ($estAccueil)
{
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$jsDirect = "ajouteEvenementLoad(function(){var oH2 = \$('body.accueil #interieurContenu').find('h2'); if (oH2.length > 0){\$(oH2[0]).addClass('accueilInterieurContenuPremierH2');}});\n";
	$balisesLinkScript[] = "$urlRacine/*#jsDirect#$jsDirect";
}

// Variable finale.

if (!$inclureCssParDefaut)
{
	supprimeInclusionCssParDefaut($balisesLinkScript);
}

$linkScript = linkScript($balisesLinkScript, $versionParDefautLinkScript);

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
## En-têtes HTTP.
##
########################################################################

if (!empty($enTetesHttp))
{
	eval($enTetesHttp);
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include_once cheminXhtml($racine, 'page.premier');
?>
