<?php
########################################################################
##
## Configuration générale
##
########################################################################

##
## En-tête HTML
##

// Choix du DTD (Définition de Type de Document)
/* Si `$xhtmlStrict` vaut TRUE, le doctype utilisé est XHTML 1.0 Strict, sinon c'est XHTML 1.0 Transitional. Si vous ne savez pas de quoi il s'agit, laissez TRUE. Le seul intérêt de choisir FALSE serait dans le cas où vous savez que vous devez utiliser des balises non valides en XHTML 1.0 Strict. */
$xhtmlStrict = TRUE; // TRUE|FALSE

// Complément de la balise title selon la langue
$baliseTitleComplement['fr'] = "Site Squeletml";
$baliseTitleComplement['en'] = "Squeletml website";

// Fichiers inclus par la balise `link` et la balise `script` pour le javascript
/*
Syntaxe: $fichiersLinkScript[] = array ("URL" => "TYPE:fichier à inclure");
Les types possibles sont: css, cssltIE7, cssIE7, csslteIE7, javascript, favicon.
Ajouter une étoile à la fin de l'URL pour inclure toutes les pages enfants. Exemples:
$fichiersLinkScript[] = array ("$urlRacine/page.php" => "css:$urlRacine/css/style.css");
$fichiersLinkScript[] = array ("$urlRacine/page.php*" => "css:$urlRacine/css/style.css");
*/
$fichiersLinkScript = array ();
$fichiersLinkScript[] = array ("$urlRacine*" => "css:$urlRacine/css/squeletml.css");
$fichiersLinkScript[] = array ("$urlRacine*" => "csslteIE7:$urlRacine/css/ie7.css");
$fichiersLinkScript[] = array ("$urlRacine*" => "cssltIE7:$urlRacine/css/ie6.css");
$fichiersLinkScript[] = array ("$urlRacine*" => "javascript:$urlRacine/js/squeletml.js");
$fichiersLinkScript[] = array ("$urlRacine*" => "favicon:$urlRacine/fichiers/puce.png");

// Version des fichiers précédemment déclarés
/* La version sera ajoutée à la suite du nom des fichiers en tant que variable GET.
Pratique quand un fichier a été modifié et qu'on veut forcer son retéléchargement. */
$versionFichiersLinkScript = 1;

// Inclusion des feuilles de style par défaut de Squeletml (dans le dossier `css`)
$styleSqueletmlCss = TRUE; // TRUE|FALSE

// Activation ou non de la métabalise `keywords`
$motsClesActives = TRUE; // TRUE|FALSE

// Contenu par défaut de la métabalise `robots`
/* Liste de valeurs possibles: index, follow, archive, noindex, nofollow, noarchive, noodp, noydir */
$robots[0] = "index, follow, archive";

// Encodage
$charset = 'UTF-8';

// Langue par défaut si aucune autre précision n'est apportée. Si la variable $langue existe (par exemple déclarée dans une page), c'est la valeur de cette dernière qui sera utilisée. Voir la fonction langue().
$langue[0] = 'fr';

##
## Syndication de contenu (flux RSS)
##

// Syndication globale du site
/* La syndication globale du site est constituée des pages, mais également des galeries si `$galerieFluxGlobal` vaut TRUE. La syndication n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant une liste d'URL. Voir la documentation pour plus de détails. */
$siteFluxGlobal = FALSE; // TRUE|FALSE

// Nombre d'items par flux RSS
$nbreItemsFlux = 50;

// Temps en secondes avant que le cache des flux RSS n'expire
/*
Exemples:
- `0` équivaut à désactiver le cache;
- `1800` équivaut à 30 minutes;
- `3600` équivaut à 1 heure;
- `43200` équivaut à 12 heures;
- `86400` équivaut à 1 journée.
*/
$dureeCache = 0;

##
## Activations et inclusions
##

// Activer l'option «Faire découvrir à des ami-e-s»
$faireDecouvrir = TRUE; // TRUE|FALSE

// Activer le message pour Internet Explorer 6
$messageIE6 = TRUE; // TRUE|FALSE

// Inclusion du sur-titre
$surTitre = FALSE; // TRUE|FALSE

// Inclusion du bas de page
$basDePage = TRUE; // TRUE|FALSE

##
## Contenu et ordre du flux HTML
##

/* Note: `sousContenu` fait référence à l'insertion dans le flux HTML au-dessous du contenu (dans la div `sousContenu`), en opposition à l'insertion au-dessus du contenu (dans la div `surContenu`). */

// Titre du site en en-tête
/* Contenu (balises HTML permises) qui sera inséré comme titre de site dans un `h1` s'il s'agit de la page d'accueil, ou dans un `p` pour toutes les autres pages. Astuce: si vous ne voulez pas trop bidouiller dans le style, remplacez la première image (dont l'`id` est `logo`) par une autre image de 75px × 75px, et remplacez le contenu du `span` (dont l'`id` est `logoSupplement`) par le titre de votre site. */
$titreSite['fr'] = "<img id=\"logo\" src=\"$urlRacine/fichiers/squeletml-logo.png\" alt=\"Squeletml\" /><span id=\"logoSupplement\"><img src=\"$urlRacine/fichiers/squeletml.png\" alt=\"Squeletml\" /></span>";
$titreSite['en'] = $titreSite['fr'];

// Position du menu principal
$menuSousContenu = TRUE; // TRUE|FALSE

// Le cas échéant, position du menu des langues
$menuLanguesSousContenu = TRUE; // TRUE|FALSE

// Le cas échéant, l'ordre d'affichage des menus
/* Si le menu principal ainsi que le menu des langues sont situés dans la même région (tous les deux en-dessous du contenu ou tous les deux au-dessus), il est possible de choisir l'ordre dans lequel générer le flux HTML pour ces deux menus. */
$menuSousMenuLangues = TRUE; // TRUE|FALSE

// Le cas échéant, position du lien «Faire découvrir»
$faireDecouvrirSousContenu = TRUE; // TRUE|FALSE

// Le cas échéant, position des liens RSS
$rssSousContenu = TRUE; // TRUE|FALSE

// Le cas échéant, position du supplément du sur-contenu
/* S'il y a lieu,  positionner le supplément du sur-contenu à la fin de la div `surContenu`, sinon le positionner au début. */
$surContenuSupplementFin = TRUE; // TRUE|FALSE

// Le cas échéant, position du supplément du sous-contenu
/* S'il y a lieu,  positionner le supplément du sous-contenu à la fin de la div `sousContenu`, sinon le positionner au début. */
$sousContenuSupplementFin = TRUE; // TRUE|FALSE

##
## Style CSS
##

/* Note: les options suivantes n'ont aucune influence sur le flux HTML. */

// Style des liens visités
/* Les liens visités (`a:visited`) de tout le site ont par défaut un style différent des liens non visités. Mettre à TRUE pour différencier seulement les liens visités contenus dans le corps des pages (div `contenu`). */
$stylerLiensVisitesSeulementDansContenu = FALSE; // TRUE|FALSE

// Choisir le nombre de colonnes
/* Si vaut TRUE, ajoute les classes `deuxColonnes`, `colonneAgauche` et `colonneAdroite` au `body`, sinon ajoute la classe `uneColonne` au `body`. À noter que Squeletml ne se sert pas par défaut de la deuxième colonne. */
$deuxColonnes = FALSE; // TRUE|FALSE

// Si `$deuxColonnes` vaut TRUE et que `$deuxColonnesSousContenuAgauche` vaut TRUE, ajoute la classe `deuxColonnesSousContenuAgauche` au `body`, sinon si `$deuxColonnes` vaut TRUE et que `$deuxColonnesSousContenuAgauche` vaut FALSE, ajoute la classe `deuxColonnesSousContenuAdroite` au `body`. Le sur-contenu va être affiché par défaut dans la colonne opposée.
$deuxColonnesSousContenuAgauche = TRUE; // TRUE|FALSE

// S'il y a lieu, emplacement de la colonne unique
/* Si `$deuxColonnes` vaut FALSE et que `$uneColonneAgauche` vaut TRUE, les classes `colonneAgauche` et `uneColonneAgauche` sont ajoutées au `body`, sinon si `$deuxColonnes` vaut FALSE et que `$uneColonneAgauche` vaut FALSE, les classes `colonneAdroite` et `uneColonneAdroite` sont ajoutées au `body`. */
$uneColonneAgauche = TRUE; // TRUE|FALSE

########################################################################
##
## Configuration du formulaire de contact
##
########################################################################

// Pour utiliser le formulaire de contact livré par défaut sans devoir créer une page de contact personnalisée simplement pour y renseigner la variable `$courrielContact`, saisir ci-dessous l'adresse courriel à utiliser, sinon laisser vide.
$courrielContactParDefaut = "";

$captchaCalcul = TRUE; // TRUE|FALSE
$captchaCalculMin = 0;
$captchaCalculMax = 10;

$captchaLiens = FALSE; // TRUE|FALSE
$captchaLiensNbre = 5; // Nombre de liens max dans un message

// Vérification de la forme du courriel
$verifCourriel = TRUE; // TRUE|FALSE

// Ajout optionnel d'un identifiant dans l'objet; ex.: "[Contact] "
$courrielObjetId = "[Contact] ";

// Offrir la possibilité dans le formulaire d'envoyer une copie à l'expéditeur
$copieCourriel = FALSE; // TRUE|FALSE

########################################################################
##
## Configuration de la galerie
##
########################################################################

##
## Général
##

// Qualité des images JPG générées par le script
$qualiteJpg = 90; // 0-100

// Hauteur des vignettes si génération automatique
$galerieHauteurVignette = 100;

##
## Syndication de contenu (flux RSS)
##

// Syndication individuelle par défaut des galeries (il est possible de configurer la syndication pour chaque galerie, et ainsi donner une valeur différente de celle par défaut)
$galerieFluxParDefaut = TRUE; // TRUE|FALSE

// Syndication globale pour toutes les galeries
/* La syndication globale des galeries n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant la liste des galeries et de leur URL. Voir la documentation pour plus de détails. */
$galerieFluxGlobal = FALSE; // TRUE|FALSE

##
## Accueil des galeries
##

// Pagination des vignettes de la page d'accueil: nombre de vignettes par page (0 pour désactiver la pagination)
$galerieVignettesParPage = 0;

// S'il y a pagination, affichage des liens au-dessus des vignettes
$galeriePaginationAuDessus = TRUE; // TRUE|FALSE

// S'il y a pagination, affichage des liens au-dessous des vignettes
$galeriePaginationAuDessous = FALSE; // TRUE|FALSE

// Informations sur la galerie
$galerieInfoAjout = TRUE; // TRUE|FALSE

// S'il y a des informations sur la galerie, choix de l'emplacement de la div
$galerieInfoEmplacement = 'haut'; // haut|bas

// Fenêtre Javascript sur l'accueil de la galerie pour consulter les images
/* Utiliser Slimbox 2 pour passer d'une image à une autre sur la page d'accueil de la galerie au lieu de naviguer d'une image à une autre en rechargeant toute la page. */
$galerieAccueilJavascript = FALSE; // TRUE|FALSE

##
## Page individuelle d'une oeuvre
##

// Choix de la navigation d'une oeuvre à une autre
$galerieNavigation = 'fleches'; // fleches ou vignettes

// Si la navigation est faite avec des vignettes, ajouter ou non une petite flèche au centre des vignettes (comme une sorte de tatouage numérique)
$galerieNavigationVignettesTatouage = FALSE; // TRUE|FALSE

// Si la navigation est faite avec des vignettes, et si `$galerieNavigationVignettesTatouage = FALSE`, ajouter ou non une petite flèche à côté des vignettes
$galerieNavigationVignettesAccompagnees = TRUE; // TRUE|FALSE

// Choix de l'emplacement de la navigation
$galerieNavigationEmplacement = 'haut'; // haut|bas

// Aperçu en minivignettes du contenu de la galerie sur les pages individuelles de chaque oeuvre
$galerieMinivignettes = TRUE; //TRUE|FALSE

// S'il y a des minivignettes, choix de l'emplacement de la div
$galerieMinivignettesEmplacement = 'haut'; // haut|bas

// S'il y a des minivignettes, le nombre à afficher (0 pour un nombre illimité)
$galerieMinivignettesNombre = 0;

// Ajout automatique d'une légende (contenu de l'attribut `alt` + taille du fichier) dans le cas où aucune légende n'a été précisée
$galerieLegendeAutomatique = FALSE; // TRUE|FALSE

// Utilisation de la syntaxe Markdown dans la légende
/* Active la syntaxe Markdown pour le texte de la légende (contenu du champ `intermediaireLegende`) */
$galerieLegendeMarkdown = FALSE; // TRUE|FALSE

// Affichage d'informations Exif pour les fichiers JPG
/* La version de PHP utilisée doit être compilée avec l'option `--enable-exif`. Voir http://us3.php.net/manual/fr/exif.requirements.php pour plus de détails. Si ce n'est pas le cas, les informations Exif ne seront tout simplement pas affichées. */
$ajoutExif = TRUE; // TRUE|FALSE

// Si on affiche des informations Exif, choisir lesquelles (TRUE|FALSE)
$infosExif = array (
	"DateTime" => TRUE,
	"ExposureTime" => TRUE,
	"FNumber" => TRUE,
	"FocalLength" => TRUE,
	"ISOSpeedRatings" => TRUE,
	"Make" => TRUE,
	"Model" => TRUE,
	);

// Si le format original d'une image existe, est-ce que le lien vers le fichier est fait sur l'image ou dans la légende, ou les deux?
$galerieLienOriginalEmplacement = 'imageLegende'; // image|legende|imageLegende

// Si le format original d'une image existe, est-ce qu'on ajoute une petite icône sous l'image pour le signifier?
$galerieIconeOriginal = TRUE; // TRUE|FALSE

// Si le format original d'une image existe, est-ce que le lien vers le fichier est pris en charge par une fenêtre Javascript (ne fonctionne pas pour le SVG)?
$galerieLienOriginalJavascript = FALSE; // TRUE|FALSE

// Si le format original d'une image existe et que le lien n'est pas pris en charge par une fenêtre Javascript, est-ce que le lien vers le fichier force le téléchargement sans affichage dans le navigateur?
$galerieTelechargeOriginal = FALSE; // TRUE|FALSE

// Emplacement de la légende, des informations Exif et du lien vers l'image originale (s'il y a lieu)
/* Les emplacements `haut` et `bas` font référence à l'image en version intermediaire, alors que `sousContenu` et `surContenu` font référence à la page. Par exemple, l'option `sousContenu` place avec la configuration et le style par défaut de Squeletml les informations de l'image dans la colonne de gauche. */
$galerieLegendeEmplacement = 'sousContenu'; // haut|bas|sousContenu|surContenu

########################################################################
##
## NE PAS MODIFIER CE QUI SUIT (à moins de savoir ce que vous faites)
##
########################################################################

// DOCUMENT_ROOT n'a pas toujours la bonne valeur selon les serveurs.
// On écrase donc sa valeur par défaut
$_SERVER['DOCUMENT_ROOT'] = $racine;

// Diverses variables utiles pour les liens dans les pages
$urlSite = $urlRacine . '/site';
$urlFichiers = $urlRacine . '/site/fichiers';

?>
