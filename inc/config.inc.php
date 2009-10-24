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
$fichiersLinkScript[] = array ("$urlRacine*" => "javascript:$urlRacine/js/phpjs.js");
$fichiersLinkScript[] = array ("$urlRacine*" => "javascript:$urlRacine/js/squeletml.js");
$fichiersLinkScript[] = array ("$urlRacine*" => "favicon:$urlRacine/fichiers/puce.png");

// Version des fichiers précédemment déclarés
/* La version sera ajoutée à la suite du nom des fichiers en tant que variable GET.
Pratique quand un fichier a été modifié et qu'on veut forcer son retéléchargement. */
$versionFichiersLinkScript = 1;

// Inclusion des feuilles de style par défaut de Squeletml (dans le dossier `css`)
$styleSqueletmlCss = TRUE; // TRUE|FALSE

// Activation ou non de la métabalise `keywords`
$motsClesActives = FALSE; // TRUE|FALSE

// Contenu par défaut de la métabalise `robots`
/* Liste de valeurs possibles: index, follow, archive, noindex, nofollow, noarchive, noodp, noydir */
$robotsParDefaut = "index, follow, archive";

// Encodage
$charset = 'UTF-8';

// Langue par défaut si aucune autre précision n'est apportée. Si la variable $langue existe (par exemple déclarée dans une page), c'est la valeur de cette dernière qui sera utilisée. Voir la fonction langue().
$langueParDefaut = 'fr';

##
## Syndication de contenu (flux RSS)
##

// Syndication globale du site
/* La syndication globale du site est constituée des pages, mais également des galeries si `$galerieFluxRssGlobal` vaut TRUE. La syndication n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant une liste d'URL. Voir la documentation pour plus de détails. */
$siteFluxRssGlobal = FALSE; // TRUE|FALSE

// Nombre d'items par flux RSS
$nombreItemsFluxRss = 50;

// Temps en secondes avant que le cache des flux RSS n'expire
/*
Exemples:
- `0` équivaut à désactiver le cache;
- `1800` équivaut à 30 minutes;
- `3600` équivaut à 1 heure;
- `43200` équivaut à 12 heures;
- `86400` équivaut à 1 journée.
*/
$dureeCacheFluxRss = 0;

##
## Inclusions et activations
##

// Inclusion du sur-titre
$surTitre = FALSE; // TRUE|FALSE

// Inclusion du bas de page
$basDePage = TRUE; // TRUE|FALSE

// Activer l'option «Faire découvrir à des ami-e-s»
$faireDecouvrir = TRUE; // TRUE|FALSE

// Activer le message pour Internet Explorer 6
$messageIE6 = TRUE; // TRUE|FALSE

// Activer des boîtes déroulantes
/* Une boîte déroulante permet d'afficher/de masquer un contenu par simple clic, et enregistre le choix d'affichage de l'internaute dans un témoin valide 30 jours. Ce contenu peut être situé n'importe où dans la page: menu, corps, bas de page... Une boîte déroulante peut être activée pour un contenu constitué d'un conteneur, d'un titre et d'un corps. La représentation générale est comme suit:

<balise id="conteneur">
<balise id="titre">...</balise>
<balise id="corps">...</balise>
</balise>

Voici un exemple concret:

1. avant application de la boîte déroulante:

<div id="fruits">
<h2 id="fruitsTitre">Fruits disponibles</h2>

<div id="fruitsCorps">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>
</div>
</div>

2. après application de la boîte déroulante avec affichage par défaut du corps:

<div id="fruits">
<h2 id="fruitsTitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[-]&nbsp;</span><span>Fruits disponibles</span></a></h2>

<div id="fruitsCorps" class="afficher">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>
</div>
</div>

3. après application de la boîte déroulante avec masquage par défaut du corps:

<div id="fruits">
<h2 id="fruitsTitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[+]&nbsp;</span><span>Fruits disponibles</span></a></h2>

<div id="fruitsCorps" class="masquer">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>
</div>
</div>

Nous constatons qu'un lien est ajouté au titre. Un clic sur le titre permet de changer l'état du corps (affiché ou masqué).

Pour l'exemple précédent, la boîte déroulante peut être activée en ajoutant la ligne suivante dans le tableau `$boitesDeroulantes`:

'fruits fruitsTitre fruitsCorps',

La syntaxe de chaque élément de tableau est donc la suivante:

'idConteneur idTitre idCorps',

Comme le montre l'exemple général, le titre n'est pas à comprendre au sens sémantique. Ce n'est donc pas nécessaire de l'entourer de balises `h1`, `h2`, `h3`, `h4`, `h5` ou `h6`. Il s'agit simplement du texte qui servira à afficher ou masquer le corps.
*/
$boitesDeroulantes = array (
	
	);

##
## Contenu et ordre du flux HTML
##

// Titre du site en en-tête
/* Contenu (balises HTML permises) qui sera inséré comme titre de site dans un `h1` s'il s'agit de la page d'accueil, ou dans un `p` pour toutes les autres pages. Astuce: si vous ne voulez pas trop bidouiller dans le style, remplacez la première image (dont l'`id` est `logo`) par une autre image de 75px × 75px, et remplacez le contenu du `span` (dont l'`id` est `logoSupplement`) par le titre de votre site. */
$titreSite['fr'] = "<img id=\"logo\" src=\"$urlRacine/fichiers/squeletml-logo.png\" alt=\"Squeletml\" /><span id=\"logoSupplement\"><img src=\"$urlRacine/fichiers/squeletml.png\" alt=\"Squeletml\" /></span>";
$titreSite['en'] = $titreSite['fr'];

/*
Les divers blocs constituant les menus sont positionnables, au choix, dans les div `surContenu` ou `sousContenu`. Ce choix concerne l'ordre dans lequel les blocs apparaissent dans le flux HTML. Ensuite, selon le style CSS utilisé, les deux div `surContenu` ou `sousContenu` formeront une seule colonne à droite, une seule colonne à gauche, deux colonnes dont celle de gauche est remplie par les blocs de `surContenu` et celle de droite par les blocs de `sousContenu`, ou deux colonnes dont celle de gauche est remplie par les blocs de `sousContenu` et celle de droite par les blocs de `surContenu`.

Chaque bloc se voit assigné un nombre. Un nombre impair signifie que le bloc en question sera placé dans la div `surContenu`, alors qu'un nombre pair signifie que le bloc sera placé dans la div `sousContenu`. À l'intérieur de chaque div, l'ordre d'insertion des blocs se fait en ordre croissant des nombres leur étant liés.

Par exemple:

	'menu-langues' => 10,
	'flux-rss' => 2,

signifie que le menu des langues ainsi que les liens RSS seront insérés dans la div `sousContenu` puisque les deux blocs ont un nombre pair, et qu'à l'intérieur de la div, les liens RSS seront insérés en premier puisqu'en ordre croissant, cela donne 2 (les liens RSS) et 10 (menu des langues).

Il est possible d'insérer un nombre illimité de blocs personnalisés. Il faut toutefois avoir en tête que chaque clé du tableau `$ordreFluxHtml` ci-dessous représente une partie du nom du fichier à insérer. Par exemple, `faire-decouvrir` fait référence au fichier `html.LANGUE.faire-decouvrir.inc.php`, présent dans le dossier personnalisé `$racine/site/inc` ou le dossier par défaut `$racine/inc`. Ainsi, un bloc personnalisé ayant une clé `heure` dans le tableau `$ordreFluxHtml` fait référence à un fichier `$racine/site/inc/html.LANGUE.heure.inc.php`.

Note: le tableau ci-dessous n'a pas de lien avec l'activation ou la désactivation d'une fonctionnalité, mais seulement avec l'odre dans lequel les blocs sont insérés dans le flux HTML dans le cas où la fonctionnalité est activée.
*/
$ordreFluxHtml = array (
	'menu-langues' => 2,
	'menu' => 4,
	'faire-decouvrir' => 6,
	'legende-oeuvre-galerie' => 8, // S'il y a lieu (voir `$galerieLegendeEmplacement`)
	'flux-rss' => 10,
	);

##
## Style CSS
##

/* Note: les options suivantes n'ont aucune influence sur le flux HTML. */

// Style des liens visités
/* Les liens visités (`a:visited`) de tout le site ont par défaut un style différent des liens non visités. Mettre à TRUE pour différencier seulement les liens visités contenus dans le corps des pages (div `contenu`). */
$differencierLiensVisitesSeulementDansContenu = FALSE; // TRUE|FALSE

// Choisir le nombre de colonnes
/* Si vaut TRUE, ajoute les classes `deuxColonnes`, `colonneAgauche` et `colonneAdroite` au `body`, sinon ajoute la classe `uneColonne` au `body`. À noter que Squeletml ne se sert pas par défaut de la deuxième colonne. */
$deuxColonnes = FALSE; // TRUE|FALSE

// Si `$deuxColonnes` vaut TRUE et que `$deuxColonnesSousContenuAgauche` vaut TRUE, ajoute la classe `deuxColonnesSousContenuAgauche` au `body`, sinon si `$deuxColonnes` vaut TRUE et que `$deuxColonnesSousContenuAgauche` vaut FALSE, ajoute la classe `deuxColonnesSousContenuAdroite` au `body`. Le sur-contenu va être affiché par défaut dans la colonne opposée.
$deuxColonnesSousContenuAgauche = TRUE; // TRUE|FALSE

// S'il y a lieu, emplacement de la colonne unique
/* Si `$deuxColonnes` vaut FALSE et que `$uneColonneAgauche` vaut TRUE, les classes `colonneAgauche` et `uneColonneAgauche` sont ajoutées au `body`, sinon si `$deuxColonnes` vaut FALSE et que `$uneColonneAgauche` vaut FALSE, les classes `colonneAdroite` et `uneColonneAdroite` sont ajoutées au `body`. */
$uneColonneAgauche = TRUE; // TRUE|FALSE

// Arrière-plan d'une colonne
$arrierePlanColonne = "rayuresEtBordure"; // aucun|bordure|rayures|rayuresEtBordure|fondUni

// Div `page` avec bordures
$borduresPage = array(
	'gauche' => TRUE, // TRUE|FALSE
	'droite' => TRUE, // TRUE|FALSE
	);

// Blocs de menu avec coins arrondis
$coinsArrondisBloc = FALSE; // TRUE|FALSE

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

// Dimensions des vignettes si génération automatique
/* La valeur `0` assignée à une dimension signifie que cette dernière sera calculée automatiquement à partir de l'autre dimension donnée ainsi que des dimensions de l'image source. Si les deux dimensions sont données, la plus grande taille possible contenable dans ces dimensions sera utilisée, sans toutefois dépasser la taille originale. Les proportions de l'image sont conservées. Au moins une dimension doit être donnée. */
$galerieDimensionsVignette = array (
	'largeur' => 100,
	'hauteur' => 100,
	);

// Forcer la taille des vignettes
/* En résumé: permet d'avoir des vignettes de même hauteur ou de même largeur, ou les deux. En détails: si la taille calculée pour la génération d'une vignette est plus petite que la taille voulue pour une vignette, ajoute des bordures blanches (ou transparentes pour les PNG) pour compléter l'espace manquant. Par exemple, disons que nous avons une petite image source de 24 px × 24 px, et que la taille voulue pour une vignette est de 100 px × 100 px. Si `$galerieForcerDimensionsVignette` vaut FALSE, la vignette créée aura la même taille que l'image source (c'est-à-dire 24 px × 24 px), mais si `$galerieForcerDimensionsVignette` vaut TRUE, alors la vignette fera 100 px × 100 px, mais il y aura des marges blanches ou transparentes de 38 px autour du corps de l'image (qui se trouve donc à être centrée). Bien sûr, on ne peut forcer une dimension (largeur ou hauteur) que si la dimension voulue a été précisée dans `$galerieDimensionsVignette`. */
$galerieForcerDimensionsVignette = TRUE; // TRUE|FALSE

##
## Syndication de contenu (flux RSS)
##

// Syndication individuelle par défaut des galeries (il est possible de configurer la syndication pour chaque galerie, et ainsi donner une valeur différente de celle par défaut)
$galerieFluxRssParDefaut = TRUE; // TRUE|FALSE

// Syndication globale pour toutes les galeries
/* La syndication globale des galeries n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant la liste des galeries et de leur URL. Voir la documentation pour plus de détails. */
$galerieFluxRssGlobal = FALSE; // TRUE|FALSE

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
