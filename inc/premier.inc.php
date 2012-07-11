<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML précédant le contenu ajouté directement dans une page du site. Le code XHTML n'est envoyé au navigateur qu'à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/(LANGUE/)page.premier.inc.php`.

Étapes dans ce fichier:

1. Première série d'inclusions.
2. Première série d'affectations.
3. Deuxième série d'inclusions.
4. Première série de traitement personnalisé optionnel.
5. Deuxième série d'affectations.
6. Troisième série d'inclusions.
7. Troisième série d'affectations.
8. Ajouts dans `$balisesLinkScript`.
9. Deuxième série de traitement personnalisé optionnel.
10. En-têtes HTTP.
11. Inclusion de code XHTML.
*/

########################################################################
##
## Affectations et inclusions.
##
########################################################################

// Inclusions 1 de 3.

include dirname(__FILE__) . '/../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

if (file_exists($racine . '/site/inc/devel.inc.php'))
{
	include_once $racine . '/site/inc/devel.inc.php';
}

include_once $racine . '/inc/fonctions.inc.php';

// Affectations 1 de 3.

eval(variablesAaffecterAuDebut());
$estPageCompte = $urlSansGet == "$urlRacine/compte.php" ? TRUE : FALSE;
$estPageDeconnexion = $urlSansGet == "$urlRacine/deconnexion.php" ? TRUE : FALSE;

if ($estPageCompte || $estPageDeconnexion)
{
	$langue = langue('navigateur', '');
}
elseif (!isset($langue))
{
	$langue = '';
}

if (!isset($idCategorie))
{
	$idCategorie = '';
}

// Inclusions 2 de 3.

foreach (inclureAuDebut($racine) as $fichier)
{
	include $fichier;
}

foreach (inclureUneFoisAuDebut($racine) as $fichier)
{
	include_once $fichier;
}

phpGettext($racine, LANGUE); // Nécessaire à la traduction.

// Traitement personnalisé optionnel 1 de 2.
if (file_exists($racine . '/site/inc/premier-pre.inc.php'))
{
	include $racine . '/site/inc/premier-pre.inc.php';
}

// Affectations 2 de 3.

extract(init('', 'baliseH1', 'boitesDeroulantes', 'classesBody', 'classesContenu', 'courrielContact', 'dateCreation', 'dateRevision', 'description', 'enTetesHttp', 'idGalerie', 'idGalerieDossier', 'motsCles', 'robots'), EXTR_SKIP);
extract(init(FALSE, 'envoyerAmisEstActif', 'envoyerAmisInclureContact', 'erreur404', 'estPageDerreur', 'pageGlobaleGalerie', 'titreGalerieGenere'), EXTR_SKIP);

if (!isset($apercu))
{
	$apercu = $apercuParDefaut;
}

if (!empty($apercu))
{
	$apercu = "<!-- APERÇU: $apercu -->";
}

if (!isset($auteur))
{
	$auteur = $auteurParDefaut;
}

if ($estPageCompte)
{
	$baliseH1 = T_("Demande de création d'un compte utilisateur");
}
elseif ($estPageDeconnexion)
{
	$baliseH1 = T_("Déconnexion de la section d'administration de Squeletml");
}

$estAccueil = estAccueil(ACCUEIL);
$baliseTitleComplement = baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut), $estAccueil);

if (!isset($boitesDeroulantesAlaMain))
{
	$boitesDeroulantesAlaMain = $boitesDeroulantesAlaMainParDefaut;
}

$cheminAncres = cheminXhtml($racine, array ($langue, $langueParDefaut), 'ancres');
$cheminSousTitre = cheminXhtml($racine, array ($langue, $langueParDefaut), 'sous-titre');
$cheminSurTitre = cheminXhtml($racine, array ($langue, $langueParDefaut), 'sur-titre');
$listeCategoriesPage = categories($racine, $urlRacine, $url, $langueParDefaut);
$classesBody = classesBody($racine, $url, $estAccueil, $idCategorie, $idGalerie, $courrielContact, $listeCategoriesPage, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $margesPage, $borduresPage, $ombrePage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $tableDesMatieresAvecFond, $tableDesMatieresArrondie, $galerieAccueilJavascriptCouleurNavigation, $classesBody);
$classesContenu = classesContenu($differencierLiensVisitesHorsContenu, $classesContenu);

if (!empty($classesContenu))
{
	$classesContenu = ' class="' . trim($classesContenu) . '"';
}

list ($contenuDoctype, $ouvertureBaliseHtml) = doctype($doctype, LANGUE);

if ($courrielContact == '@' && !empty($contactCourrielParDefaut))
{
	$courrielContact = $contactCourrielParDefaut;
}

if (!isset($envoyerAmis))
{
	$envoyerAmis = $activerEnvoyerAmisParDefaut;
}

if (!isset($infosPublication))
{
	$infosPublication = $afficherInfosPublicationParDefaut;
}

if (!isset($licence))
{
	$licence = $licenceParDefaut;
}

if (!isset($lienPage))
{
	$lienPage = $afficherLienPageParDefaut;
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

$siteEstEnMaintenance = siteEstEnMaintenance($racine . '/.htaccess');

if ($siteEstEnMaintenance)
{
	$noticeMaintenance = noticeMaintenance();
}

if (!isset($partage))
{
	$partage = $activerPartageParDefaut;
}

if ($partage)
{
	$boitesDeroulantesAlaMain .= TRUE;
}

$premierOuDernier = 'premier';
$robots = robots($robotsParDefaut, $robots);

if (!isset($rssCategorie))
{
	$rssCategorie = $activerFluxRssCategorieParDefaut;
}

if (isset($idCategorie) && ($idCategorie == 'site' || $idCategorie == 'galeries'))
{
	$rssCategorie = FALSE;
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

// Inclusions 3 de 3.

if (!empty($idCategorie))
{
	include $racine . '/inc/categorie.inc.php';
}

if (!empty($idGalerie))
{
	include $racine . '/inc/galerie.inc.php';
}

include $racine . '/inc/blocs.inc.php';

// Affectations 3 de 3.

if (!isset($baliseTitle))
{
	$baliseTitle = '';
}

$baliseTitle = baliseTitle($baliseTitle, $baliseH1);
$boitesDeroulantesTableau = boitesDeroulantes($boitesDeroulantesParDefaut, $boitesDeroulantes);

if ($estPageDerreur)
{
	$classesBody .= 'pageDerreur ';
}

if (!empty($classesBody))
{
	$classesBody = ' class="' . trim($classesBody) . '"';
}

if ($erreur404 || $estPageDerreur || $courrielContact == '@' || (!empty($courrielContact) && !isset($accueil[LANGUE]) && strpos($url, urlRacineLangueInactive($racine, $urlRacine, LANGUE)) === 0))
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

if (!empty($boitesDeroulantesTableau) || $boitesDeroulantesAlaMain)
{
	$balisesLinkScript[] = "$url#css#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = "$url#cssIE7#$urlRacine/css/boites-deroulantes-ie7.css";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.cookie.js";
	
	if (!empty($boitesDeroulantesTableau))
	{
		$jsDirect = '';
		
		foreach ($boitesDeroulantesTableau as $boiteDeroulante)
		{
			$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante', \"$aExecuterApresClicBd\");});\n";
		}
		
		$balisesLinkScript[] = "$url#jsDirect#$jsDirect";
	}
}

// Flux RSS.

$fluxRssGlobalSiteActif = FALSE;

if ($activerFluxRssGlobalSite)
{
	$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
	
	if (isset($pages[LANGUE]))
	{
		$fluxRssGlobalSiteActif = TRUE;
		$urlFlux = $urlRacine . '/rss.php?type=site&amp;langue=' . LANGUE;
		$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('Dernières publications');
	}
}

$fluxRssGlobalGaleriesActif = FALSE;

if ($galerieActiverFluxRssGlobal)
{
	$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
	
	if (isset($pages[LANGUE]))
	{
		$fluxRssGlobalGaleriesActif = TRUE;
		$urlFlux = $urlRacine . '/rss.php?type=galeries&amp;langue=' . LANGUE;
		$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('Derniers ajouts aux galeries');
	}
}

if (!empty($idGalerie) && $rssGalerie)
{
	$urlFlux = "$urlRacine/rss.php?type=galerie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlSansIndexSansGet);
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('Galerie %1$s'), $idGalerie);
}

if (!empty($idCategorie) && $rssCategorie)
{
	if (strpos($url, $urlRacine . '/categorie.php?id=') !== FALSE)
	{
		$urlFlux = $urlRacine . '/rss.php?type=categorie&amp;id=' . filtreChaine($racine, $idCategorie);
	}
	else
	{
		$urlFlux = "$urlRacine/rss.php?type=categorie&amp;chemin=" . str_replace($urlRacine . '/', '', $urlSansIndexSansGet);
	}
	
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('Catégorie %1$s'), $idCategorie);
}

// PIE (Progressive Internet Explorer).

$profondeur = profondeurPage($urlRacine, $url);
$cheminPie = '';

for ($i = 0; $i < $profondeur; $i++)
{
	$cheminPie .= '../';
}

$cheminPie .= 'inc/PIE/PIE.php';

$cssDirectlteIE8 = '';
$cssDirectlteIE8 .= "body.tableDesMatieresArrondie #tableDesMatieres, .blocArrondi, .blocAvecFond {\n";
$cssDirectlteIE8 .= "\tbehavior: url(\"$cheminPie\");\n";
$cssDirectlteIE8 .= "}\n";

if ($ombrePage)
{
	$cssDirectlteIE8 .= "body.ombrePage #page {\n";
	$cssDirectlteIE8 .= "\tbackground-color: white;\n";
	$cssDirectlteIE8 .= "\tbehavior: url(\"$cheminPie\");\n";
	$cssDirectlteIE8 .= "}\n";
}

if ($inclureBasDePage && !$basDePageInterieurPage)
{
	$cssDirectlteIE8 .= "#basDePageHorsPage {\n";
	$cssDirectlteIE8 .= "\tbackground-color: white;\n";
	$cssDirectlteIE8 .= "\tbehavior: url(\"$cheminPie\");\n";
	$cssDirectlteIE8 .= "}\n";
}

$balisesLinkScript[] = "$url#cssDirectlteIE8#$cssDirectlteIE8";

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
	$balisesLinkScript[] = "$url#csslteIE7#$urlRacine/css/table-des-matieres-ie6-7.css";
	
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery-tableofcontents/jquery.tableofcontents.js";
	$balisesLinkScript[] = "$url#jsDirect#tableDesMatieres('milieuInterieurContenu', '$tDmBaliseTable', '$tDmBaliseTitre', $tDmNiveauDepart, $tDmNiveauArret, '$langue', '$langueParDefaut');";
}

// Message pour IE6.

if ($afficherMessageIe6)
{
	$balisesLinkScript[] = "$url#cssltIE7#$urlRacine/css/boites-deroulantes.css";
	$balisesLinkScript[] = "$url#jsltIE7#$urlRacine/js/jquery/jquery.min.js";
	$balisesLinkScript[] = "$url#jsltIE7#$urlRacine/js/jquery/jquery.cookie.js";
	$balisesLinkScript[] = "$url#jsDirectltIE7#ajouteEvenementLoad(function(){boiteDeroulante('#messageIe6', '');});";
}

// Variable finale.

if (!$inclureCssParDefaut)
{
	supprimeInclusionCssParDefaut($balisesLinkScript);
}

$linkScript = linkScript($racine, $urlRacine, $fusionnerCssJs, '', $balisesLinkScript, $versionParDefautLinkScriptCss, $versionParDefautLinkScriptNonCss);

########################################################################
##
## Traitement personnalisé optionnel 2 de 2.
##
########################################################################

if (file_exists($racine . '/site/inc/premier.inc.php'))
{
	include $racine . '/site/inc/premier.inc.php';
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

if ($dureeCache)
{
	// On vérifie si la page existe en cache ou si le cache est expiré.
	
	$nomFichierCache = nomFichierCache($racine, $urlRacine, $url);
	
	if (file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache))
	{
		@readfile("$racine/site/cache/$nomFichierCache");
		
		exit(0);
	}
	else
	{
		ob_start();
	}
}

include cheminXhtml($racine, array ($langue, $langueParDefaut), 'page.premier');
?>
