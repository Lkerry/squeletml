<?php
/*
Ce fichier gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML précédant le contenu ajouté directement dans une page du site. Le code XHTML est envoyé au navigateur lors de la vérification du cache ou à la toute fin du fichier par le biais de l'inclusion du fichier `(site/)xhtml/(LANGUE/)page.premier.inc.php`.

Étapes dans ce fichier:

1. Première série d'inclusions.
2. Première série d'affectations.
3. Deuxième série d'inclusions.
4. Première série de traitement personnalisé optionnel.
5. Vérification du cache.
6. Deuxième série d'affectations.
7. Troisième série d'inclusions.
8. Troisième série d'affectations.
9. Ajouts dans `$balisesLinkScript`.
10. Deuxième série de traitement personnalisé optionnel.
11. En-têtes HTTP.
12. Inclusion de code XHTML.
*/

########################################################################
##
## Affectations et inclusions.
##
########################################################################

// Inclusions 1 de 3.

include dirname(__FILE__) . '/../init.inc.php';

if (file_exists($racine . '/site/inc/devel.inc.php'))
{
	include_once $racine . '/site/inc/devel.inc.php';
}

include_once $racine . '/inc/fonctions.inc.php';

// Affectations 1 de 3.

eval(variablesAaffecterAuDebut());
$estPageCompte = $urlSansGet == "$urlRacine/compte.php" ? TRUE : FALSE;
$estPageDeconnexion = $urlSansGet == "$urlRacine/deconnexion.php" ? TRUE : FALSE;

if (!isset($pageGlobaleGalerie))
{
	$pageGlobaleGalerie = FALSE;
}

if ($estPageCompte || $estPageDeconnexion)
{
	$langue = langue('navigateur', '');
}
elseif (!isset($langue))
{
	$langue = '';
}

if (!isset($desactiverCache))
{
	$desactiverCache = FALSE;
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

// Vérification du cache.
if ($dureeCache && !$desactiverCache)
{
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
	$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
	
	// S'il y a lieu, analyse d'une requête effectuée par le client.
	
	$code304 = FALSE;
	
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == @filemtime($cheminFichierCache))
	{
		$code304 = TRUE;
	}
	elseif (isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5(@filesize($cheminFichierCache) . @filemtime($cheminFichierCache)))
	{
		$code304 = TRUE;
	}
	
	if ($code304)
	{
		header('HTTP/1.1 304 Not Modified');
		
		exit(0);
	}
	
	// On vérifie si la page existe en cache ou si le cache est expiré.
	if (file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache))
	{
		if (file_exists($cheminFichierCacheEnTete))
		{
			$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
			
			if (!empty($contenuFichierCacheEnTete))
			{
				eval($contenuFichierCacheEnTete);
			}
		}
		
		@readfile($cheminFichierCache);
		
		exit(0);
	}
	else
	{
		ob_start();
	}
}

// Affectations 2 de 3.

extract(init('', 'baliseH1', 'boitesDeroulantes', 'classesBody', 'classesContenu', 'courrielContact', 'dateCreation', 'dateRevision', 'description', 'enTetesHttp', 'idGalerie', 'idGalerieDossier', 'motsCles', 'robots'), EXTR_SKIP);
extract(init(FALSE, 'partageCourrielActif', 'partageCourrielInclureContact', 'erreur404', 'estPageDerreur', 'titreGalerieGenere'), EXTR_SKIP);

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
$listeCategoriesPage = categories($racine, $urlRacine, $url);
$classesBody = classesBody($racine, $url, $estAccueil, $idCategorie, $idGalerie, $courrielContact, $listeCategoriesPage, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $margesPage, $borduresPage, $ombrePage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $tableDesMatieresAvecFond, $tableDesMatieresArrondie, $galerieAccueilJavascriptCouleurNavigation, $basDePageInterieurPage, $classesBody);
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

if (!isset($partageCourriel))
{
	$partageCourriel = $activerPartageCourrielParDefaut;
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

if (!isset($partageReseaux))
{
	$partageReseaux = $activerPartageReseauxParDefaut;
}

if ($partageCourriel || $partageReseaux)
{
	$boitesDeroulantesAlaMain = TRUE;
}

$premierOuDernier = 'premier';
$robots = robots($robotsParDefaut, $robots);

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
else
{
	$enTetesHttp .= 'header("Content-Type: text/html; charset=' . $charset . '");';
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

if ($activerFluxRssGlobalSite)
{
	if (!isset($pagesFluxRssGlobalSite))
	{
		$pagesFluxRssGlobalSite = super_parse_ini_file(cheminConfigFluxRssGlobalSite($racine), TRUE);
	}
	
	if (!empty($pagesFluxRssGlobalSite[LANGUE]))
	{
		$urlFlux = $urlRacine . '/rss.php?type=site&amp;langue=' . LANGUE;
		$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('Dernières publications');
	}
}

if ($galerieActiverFluxRssGlobal)
{
	$itemsFluxRssGaleriesLangue = fluxRssGaleriesTableauBrut($racine, $urlRacine, LANGUE, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
	
	if (!empty($itemsFluxRssGaleriesLangue))
	{
		$urlFlux = $urlRacine . '/rss.php?type=galeries&amp;langue=' . LANGUE;
		$balisesLinkScript[] = "$url#rss#$urlFlux#" . T_('Derniers ajouts aux galeries');
	}
}

if (!empty($idGalerie) && $rssGalerie)
{
	$urlFlux = "$urlRacine/rss.php?type=galerie&amp;id=" . filtreChaine($racine, $idGalerie);
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('Galerie %1$s'), $idGalerie);
}

if (!empty($idCategorie) && $rssCategorie)
{
	$urlFlux = "$urlRacine/rss.php?type=categorie&amp;id=" . filtreChaine($racine, $idCategorie);
	$balisesLinkScript[] = "$url#rss#$urlFlux#" . sprintf(T_('Catégorie %1$s'), $idCategorie);
}

// Versions en d'autres langues.
if ($estAccueil && count($accueil) > 1)
{
	foreach ($accueil as $codeLangue => $urlAccueilLangue)
	{
		$balisesLinkScript[] = "$url#hreflang#$urlAccueilLangue/#$codeLangue";
	}
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
$cssDirectlteIE8 .= 'body.tableDesMatieresArrondie #tableDesMatieres, pre, table, .blocArrondi, .blocAvecFond';

if ($idGalerie)
{
	$cssDirectlteIE8 .= ', div.galerieNavigationAccueil img, div#galerieIntermediaireImg img, div.galerieIntermediaireImgApercu img, div#galerieIntermediaireTexte';
}

if ($inclureBasDePage && !$basDePageInterieurPage)
{
	$cssDirectlteIE8 .= ', #basDePageHorsPage';
}

if ($ombrePage)
{
	$cssDirectlteIE8 .= ', body.ombrePage #page';
}

$cssDirectlteIE8 .= " {\n\tbehavior: url(\"$cheminPie\");\n";
$cssDirectlteIE8 .= "}\n";
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
	$balisesLinkScript[] = "$url#cssIE8#$urlRacine/css/table-des-matieres-ie8.css";
	
	$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	
	if (!$dureeCache || $desactiverCache)
	{
		$balisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery-tableofcontents/jquery.tableofcontents.js";
		$balisesLinkScript[] = "$url#jsDirect#tableDesMatieres('milieuInterieurContenu', '$tDmBaliseTable', '$tDmBaliseTitre', $tDmNiveauDepart, $tDmNiveauArret, '$langue', '$langueParDefaut');";
	}
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

$linkScript = linkScript($racine, $urlRacine, $fusionnerCssJs, '', $balisesLinkScript, $versionParDefautLinkScript);

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

if (!$dureeCache || $desactiverCache)
{
	eval($enTetesHttp);
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include cheminXhtml($racine, array ($langue, $langueParDefaut), 'page.premier');
?>
