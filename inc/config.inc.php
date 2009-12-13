<?php
########################################################################
##
## Configuration générale.
##
########################################################################

/* _______________ En-tête HTML. _______________ */

// Choix du DTD (Définition de Type de Document).
/*
- Si `$xhtmlStrict` vaut TRUE, le doctype utilisé est XHTML 1.0 Strict, sinon c'est XHTML 1.0 Transitional. Si vous ne savez pas de quoi il s'agit, laissez TRUE. Le seul intérêt de choisir FALSE est dans le cas où on sait qu'on doit utiliser du code non valide en XHTML 1.0 Strict.
- Voir la fonction `doctype()`.
*/
$xhtmlStrict = TRUE; // TRUE|FALSE

// Complément de la balise `title` selon la langue.
/*
- Le contenu de la balise `title` est généré ainsi:
  Balise `title` unique à chaque page | Complément
- Voir la fonction `baliseTitleComplement()`.
*/
$baliseTitleComplement['fr'] = "Site Squeletml";
$baliseTitleComplement['en'] = "Squeletml website";

// Fichiers inclus dans des balises `link` et `script`.
/*
- Les types possibles sont: css, cssltIE7, cssIE7, csslteIE7, js, jsDirect, jsDirectltIE7, jsltIE7, favicon, po, rss.
- Syntaxe pour tous les types:
  $balisesLinkScript[] = "URL#TYPE#fichier à inclure#contenu de l'attribut `title`";
  Le contenu de l'attribut `title` est optionnel, et est utilisé seulement pour le type rss.
- Ajouter une étoile à la fin de l'URL pour inclure toutes les pages enfants.
- Dans le fichier de configuration personnalisé, ajouter tout simplement des éléments au tableau `$balisesLinkScript`.
- Exemples:
  $balisesLinkScript[] = "$urlRacine/page.php#css#$urlRacine/site/css/style-page.css";
  $balisesLinkScript[] = "$urlRacine/page.php*#css#$urlRacine/site/css/style-general.css";
- Voir la fonction `linkScript()`.
*/
$balisesLinkScript[] = "$urlRacine/*#css#$urlRacine/css/squeletml.css";
$balisesLinkScript[] = "$urlRacine/*#css#$urlRacine/css/extensions-proprietaires.css";
$balisesLinkScript[] = "$urlRacine/*#csslteIE7#$urlRacine/css/ie6-7.css";
$balisesLinkScript[] = "$urlRacine/*#cssIE7#$urlRacine/css/ie7.css";
$balisesLinkScript[] = "$urlRacine/*#cssltIE7#$urlRacine/css/ie6.css";
$balisesLinkScript[] = "$urlRacine/*#js#$urlRacine/js/phpjs.js";
$balisesLinkScript[] = "$urlRacine/*#js#$urlRacine/js/squeletml.js";
$balisesLinkScript[] = "$urlRacine/*#favicon#$urlRacine/fichiers/puce.png";

// Version des fichiers déclarés dans le tableau `$balisesLinkScript`.
/*
- La version est ajoutée à la suite du nom des fichiers en tant que variable GET.
- Pratique quand un fichier a été modifié et qu'on veut forcer son retéléchargement.
- Laisser vide pour désactiver l'ajout de version.
- Exemple de sortie HTML lorsque `$versionFichiersLinkScript` vaut `1`:
  <script type="text/javascript" src="/js/squeletml.js?1"></script>
- Voir la fonction `linkScript()`.
*/
$versionFichiersLinkScript = '';

// Inclusion des feuilles de style par défaut de Squeletml (dossier `css`).
/*
- Voir les fonctions `linkScript()` et `supprimeInclusionCssParDefaut()`.
*/
$inclureCssParDefaut = TRUE; // TRUE|FALSE

// Inclusion de la métabalise `keywords`.
/*
- A priorité sur la déclaration de mots-clés spécifiques à une page.
*/
$inclureMotsCles = FALSE; // TRUE|FALSE

// Contenu par défaut de la métabalise `robots`.
/*
- Liste de valeurs possibles: index, follow, archive, noindex, nofollow, noarchive, noodp, noydir.
- Si la variable `$robots` existe (par exemple déclarée dans une page) et n'est pas vide, c'est la valeur de cette dernière qui est utilisée.
- Voir la fonction `robots()`.
*/
$robotsParDefaut = 'index, follow, archive';

// Encodage du site.
$charset = 'UTF-8';

// Langue par défaut.
/*
- Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` existe (par exemple déclarée dans une page) et n'est pas vide, c'est la valeur de cette dernière qui est utilisée.
- Voir la fonction `langue()`.
*/
$langueParDefaut = 'fr';

/* _______________ Inclusions dans le corps. _______________ */

// Inclusion du sur-titre.
$inclureSurTitre = FALSE; // TRUE|FALSE

// Inclusion du bas de page.
$inclureBasDePage = TRUE; // TRUE|FALSE

// Activation de l'option «Faire découvrir à des ami-e-s».
$activerFaireDecouvrir = TRUE; // TRUE|FALSE

// Affichage du message pour Internet Explorer 6.
/*
- Voir la fonction `messageIe6()`.
*/
$afficherMessageIe6 = TRUE; // TRUE|FALSE

// Affichage par défaut de la table des matières.
/*
- État de la table des matières si aucune autre précision n'est apportée. Si la variable `$tableDesMatieres` existe (par exemple déclarée dans une page) et n'est pas vide, c'est la valeur de cette dernière qui est utilisée.
*/
$afficherTableDesMatieresParDefaut = FALSE; // TRUE|FALSE

// Activation de boîtes déroulantes par défaut.
/*
Une boîte déroulante permet d'afficher/de masquer un contenu par simple clic, et enregistre le choix d'affichage de l'internaute dans un témoin valide durant 30 jours. Ce contenu peut être situé n'importe où dans la page: menu, corps, bas de page, etc. Une boîte déroulante peut être activée seulement pour un contenu constitué d'un conteneur, d'un titre et d'un corps. La représentation générale est la suivante:

<balise id="conteneur">
<balise class="bDtitre">...</balise>
<balise class="bDcorps">...</balise>
</balise>

Voici un exemple concret:

1. avant application de la boîte déroulante:

<div id="fruits">
<h2 class="bDtitre">Fruits disponibles</h2>

<div class="bDcorps">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>

<p>Il n'y a plus de kiwi.</p>
</div>
</div>

2. après application de la boîte déroulante avec affichage par défaut du corps:

<div id="fruits">
<h2 class="bDtitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[-]&nbsp;</span><span>Fruits disponibles</span></a></h2>

<div class="bDcorps" class="afficher">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>

<p>Il n'y a plus de kiwi.</p>
</div>
</div>

3. après application de la boîte déroulante avec masquage par défaut du corps:

<div id="fruits">
<h2 class="bDtitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[+]&nbsp;</span><span>Fruits disponibles</span></a></h2>

<div class="bDcorps" class="masquer">
<ul>
	<li>Fraises</li>
	<li>Poires</li>
	<li>Pommes</li>
</ul>

<p>Il n'y a plus de kiwi.</p>
</div>
</div>

Nous constatons qu'un lien est ajouté au titre. Un clic sur le titre permet de changer l'état du corps (affiché ou masqué).

Dans l'exemple précédent, la boîte déroulante peut être activée de deux façons:

1) en ajoutant `fruits` dans la variable `$boitesDeroulantesParDefaut`.

  La syntaxe de chaque boîte est donc la suivante:

  'idConteneur'

  En ajoutant au moins une boîte dans cette variable, chaque page de Squeletml ajoutera les scripts nécessaires aux boîtes déroulantes. Pour ajouter plusieurs boîtes, le séparateur à utiliser est `|`. Voici un exemple d'ajout de plusieurs boîtes:

  $boitesDeroulantesParDefaut = 'idConteneur1 | idConteneur2 | idConteneur3';

2) en renseignant la variable `$boitesDeroulantes` dans une page spécifique avant l'inclusion du premier fichier PHP (la syntaxe est la même que pour la variable `$boitesDeroulantesParDefaut`). Voir la documentation pour plus de détails.

Comme le montre l'exemple général, le titre n'est pas à comprendre au sens sémantique. Ce n'est donc pas nécessaire de l'entourer de balises `h1`, `h2`, `h3`, `h4`, `h5` ou `h6`. Il s'agit simplement du texte qui servira à afficher ou masquer le corps.

Voir la fonction Javascript `boiteDeroulante()`.
*/
$boitesDeroulantesParDefaut = '';

// Balises `link` et `script` finales, ajoutées juste avant `</body>`.
/*
- Voir les commentaires de la variable `$balisesLinkScript` dans ce même fichier de configuration pour les détails de la syntaxe.
- Voir la fonction `linkScript()`.
*/
$balisesLinkScriptFinales[] = "$urlRacine/*#jsDirect#egaliseHauteur('interieurPage', 'surContenu', 'sousContenu');";

/* _______________ Contenu et ordre du flux HTML. _______________ */

// Titre du site en en-tête.
/*
- Contenu (balises HTML permises) qui sera inséré comme titre de site dans un `h1` sur la page d'accueil, et dans un `p` sur toutes les autres pages.
- Astuce: si vous ne voulez pas bidouiller dans le style, remplacez la première image (dont l'`id` est `logo`) par une autre image de 75px × 75px, et remplacez le contenu du `span` (dont l'`id` est `logoSupplement`) par le titre de votre site.
*/
$titreSite['fr'] = "<img id=\"logo\" src=\"$urlRacine/fichiers/squeletml-logo.png\" alt=\"Squeletml\" /><span id=\"logoSupplement\"><img src=\"$urlRacine/fichiers/squeletml.png\" alt=\"Squeletml\" /></span>";
$titreSite['en'] = $titreSite['fr'];

// Ordre des blocs constituant les menus.
/*
Les divers blocs constituant les menus sont positionnables, au choix, dans les div `surContenu` ou `sousContenu`. Ce choix concerne l'ordre dans lequel les blocs apparaissent dans le flux HTML. Ensuite, selon le style CSS utilisé, les deux div `surContenu` et `sousContenu` formeront une seule colonne à droite, une seule colonne à gauche, deux colonnes dont celle de gauche est remplie par les blocs de `surContenu` et celle de droite par les blocs de `sousContenu`, ou deux colonnes dont celle de gauche est remplie par les blocs de `sousContenu` et celle de droite par les blocs de `surContenu`.

Chaque bloc se voit assigner trois nombres (séparés par une espace), qui font référence respectivement à l'ordre du bloc lorsqu'il n'y a pas de colonne, lorsqu'il y a une seule colonne et lorsqu'il y en a deux. Un nombre impair signifie que le bloc en question sera placé dans la div `surContenu`, alors qu'un nombre pair signifie que le bloc sera placé dans la div `sousContenu`. À l'intérieur de chaque div, l'ordre d'insertion des blocs se fait en ordre croissant des nombres leur étant liés.

Par exemple:

	'menu-langues' => array (10, 10, 4),
	'flux-rss' => array (2, 2, 6),

signifie que le menu des langues ainsi que les liens RSS seront insérés dans la div `sousContenu`, peu importe le nombre de colonnes, puisque les deux blocs ont un nombre pair pour chaque possibilité en lien avec le nombre de colonnes, et qu'à l'intérieur de la div, les liens RSS seront insérés en premier lorsqu'il n'y a pas de colonne et lorsqu'il n'y en a qu'une seule puisqu'en ordre croissant, nous obtenons 2 (les liens RSS) et 10 (le menu des langues), mais s'il y a deux colonnes, les liens RSS seront insérés après le menu des langues, car nous obtenons 4 (le menu des langues) et 6 (les liens RSS).

Il est possible d'insérer un nombre illimité de blocs personnalisés. Il faut toutefois avoir en tête que chaque clé du tableau `$ordreBlocsDansFluxHtml` ci-dessous représente une partie du nom du fichier à insérer. Par exemple, `faire-decouvrir` fait référence au fichier `LANGUE/html.faire-decouvrir.inc.php`, présent dans le dossier personnalisé `$racine/site/inc` ou le dossier par défaut `$racine/inc`. Ainsi, un bloc personnalisé ayant une clé `heure` dans le tableau `$ordreBlocsDansFluxHtml` fait référence à un fichier `$racine/site/inc/LANGUE/html.heure.inc.php`.

Note: le tableau ci-dessous n'a pas de lien avec l'activation ou la désactivation d'une fonctionnalité, mais seulement avec l'odre dans lequel les blocs sont insérés dans le flux HTML dans le cas où la fonctionnalité est activée.

Voir la fonction `blocs()`.
*/
$ordreBlocsDansFluxHtml = array (
	'menu-langues' => array (2, 2, 1),
	'menu' => array (1, 4, 4),
	'faire-decouvrir' => array (6, 6, 6),
	'legende-oeuvre-galerie' => array (8, 8, 8), // S'il y a lieu (voir `$galerieLegendeEmplacement`).
	'flux-rss' => array (10, 10, 10),
);

// Détection du type MIME.
/*
- La détection du type MIME se fait selon la disponibilité des outils suivants, en ordre de priorité:
  - `Fileinfo` de PHP;
  - commande `file` si la variable `$typeMimeFile` vaut TRUE;
  - tableau personnalisé de correspondance entre une extension et son type MIME si la variable `$typeMimeCorrespondance` n'est pas vide. Exemple:
    array ('rmi' => 'audio/midi');
  - tableau par défaut de correspondance entre une extension et son type MIME de la fonction `file_get_mimetype()`.
*/
$typeMimeFile = FALSE; // TRUE|FALSE
$typeMimeCheminFile = '/usr/bin/file';
$typeMimeCorrespondance = array ();

/* _______________ Style CSS. _______________ */

// Note: les options suivantes n'ont aucune influence sur le flux HTML.

// Style des liens visités.
/*
- Les liens visités (`a:visited`) de tout le site ont par défaut un style différent des liens non visités. Mettre à FALSE pour différencier seulement les liens visités contenus dans le corps des pages (div `contenu`).
*/
$differencierLiensVisitesHorsContenu = TRUE; // TRUE|FALSE

// Détection des liens actifs dans les blocs.
/*
- Si la détection est activée pour un bloc, ajoute la classe `actif` à tous les liens (balises `a`) de ce bloc et pointant vers la page en cours ainsi qu'au `li` contenant ce lien. Avec la feuille de style par défaut, le résultat est un lien actif en gras et un `li` marqué d'une petite puce spéciale.
- Voir les explications de la variable `$ordreBlocsDansFluxHtml` dans ce fichier de configuration pour connaître la syntaxe des clés du tableau (par exemple `menu-langues`).
- Voir la fonction `lienActif()`.
*/
$liensActifsBlocs = array (
	'menu-langues' => TRUE,
	'menu' => TRUE,
	'faire-decouvrir' => NULL, // Ne s'applique pas.
	'legende-oeuvre-galerie' => FALSE, // S'il y a lieu (voir `$galerieLegendeEmplacement`).
	'flux-rss' => NULL, // Ne s'applique pas.
);

// Limite de la profondeur d'une liste dans un bloc.
/*
Pour chaque bloc, préciser par TRUE ou FALSE si la profondeur d'une liste y étant présente doit être limitée. Aucun texte n'est supprimé, mais une classe `masquer` est ajoutée aux sous-listes inactives, détectées et marquées par la fonction `lienActif()`, ce qui signifie qu'un bloc pour lequel la détection des liens actifs n'aura pas été activée dans la variable `$liensActifsBlocs` (dans ce fichier de configuration) ne pourra pas avoir de limite de profondeur.

Par exemple, si la page en cours est `page2.1.php`, une liste comme celle-ci:

	Lien page1
		Lien page1.1
			Lien page1.1.1
				Lien page1.1.1.1
				Lien page1.1.1.2
			Lien page1.1.2
				Lien page1.1.2.1
				Lien page1.1.2.2
		Lien page1.2
			Lien page1.2.1
				Lien page1.2.1.1
				Lien page1.2.1.2
			Lien page1.2.2
				Lien page1.2.2.1
				Lien page1.2.2.2
	Lien page2
		Lien page2.1
			Lien page2.1.1
				Lien page2.1.1.1
				Lien page2.1.1.2
			Lien page2.1.2
				Lien page2.1.2.1
				Lien page2.1.2.2
		Lien page2.2
			Lien page2.2.1
				Lien page2.2.1.1
				Lien page2.2.1.2
			Lien page2.2.2
				Lien page2.2.2.1
				Lien page2.2.2.2

sera visible ainsi:

	Lien page1
	Lien page2
		Lien page2.1
			Lien page2.1.1
			Lien page2.1.2
		Lien page2.2

Voir les explications de la variable `$ordreBlocsDansFluxHtml` dans ce fichier de configuration pour connaître la syntaxe des clés du tableau (par exemple `menu-langues`).

Voir les fonctions `limiteProfondeurListe()` et `lienActif()`.
*/
$limiterProfondeurListesBlocs = array (
	'menu-langues' => FALSE,
	'menu' => TRUE,
	'faire-decouvrir' => NULL, // Ne s'applique pas.
	'legende-oeuvre-galerie' => FALSE, // S'il y a lieu (voir `$galerieLegendeEmplacement`).
	'flux-rss' => NULL, // Ne s'applique pas.
);

// Nombre de colonnes.
/*
- Si vaut 2, ajoute à la balise `body` les classes `deuxColonnes`, `colonneAgauche` et `colonneAdroite`, sinon si vaut 1, ajoute la classe `uneColonne`, sinon si vaut 0, ajoute la classe `aucuneColonne`.

*/
$nombreDeColonnes = 1; // 0|1|2

// Emplacement du sous-contenu lorsqu'il y a deux colonnes.
/*
- Si `$nombreDeColonnes` vaut 2 et si `$deuxColonnesSousContenuAgauche` vaut TRUE, ajoute la classe `deuxColonnesSousContenuAgauche` au `body`, sinon si `$nombreDeColonnes` vaut 2 et que `$deuxColonnesSousContenuAgauche` vaut FALSE, ajoute la classe `deuxColonnesSousContenuAdroite` au `body`.
- Le sur-contenu va être affiché par défaut dans la colonne opposée.
*/
$deuxColonnesSousContenuAgauche = TRUE; // TRUE|FALSE

// S'il y a lieu, emplacement de la colonne unique.
/*
- Si `$nombreDeColonnes` vaut 1 et que `$uneColonneAgauche` vaut TRUE, les classes `colonneAgauche` et `uneColonneAgauche` sont ajoutées au `body`, sinon si `$nombreDeColonnes` vaut 1 et que `$uneColonneAgauche` vaut FALSE, les classes `colonneAdroite` et `uneColonneAdroite` sont ajoutées au `body`.
*/
$uneColonneAgauche = TRUE; // TRUE|FALSE

// S'il y a lieu, arrière-plan d'une colonne.
$arrierePlanColonne = 'rayuresEtBordure'; // aucun|bordure|rayures|rayuresEtBordure|fondUni

// Div `page` avec bordures.
$borduresPage = array(
	'gauche' => TRUE, // TRUE|FALSE
	'droite' => TRUE, // TRUE|FALSE
);

// S'il y a au moins une colonne, étendre l'en-tête sur toute la largeur du site.
/*
- Par défaut, l'en-tête ne s'étend que sur la largeur du contenu, excluant la largeur de la ou des colonnes. Mettre à TRUE pour l'étendre sur toute la page.
*/ 
$enTetePleineLargeur = FALSE; // TRUE|FALSE

// Blocs de menu avec coins arrondis par défaut.
$blocsArrondisParDefaut = FALSE; // TRUE|FALSE

/*
Il est possible de modifier la configuration par défaut des blocs arrondis pour un bloc en particulier selon le nombre de colonnes. Par exemple, la ligne suivante:

	'menu' => array (TRUE, FALSE, FALSE),

précise que le bloc de menu principal devra avoir des coins arrondis lorsqu'il n'y a pas de colonne, mais ne devra pas en avoir lorsqu'il y en a une ou deux. Nous pouvons donc dégager la syntaxe générale suivante:

	'bloc' => array (valeur quand aucune colonne, valeur quand 1 colonne, valeur quand 2 colonnes),
*/
$blocsArrondisSpecifiques = array (
	'menu' => array (TRUE, FALSE, FALSE),
);

/* _______________ Syndication de contenu (flux RSS). _______________ */

// Syndication globale du site.
/*
- La syndication globale du site est constituée des pages, mais également des galeries si `$galerieActiverFluxRssGlobal` vaut TRUE. La syndication n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant une liste d'URL.
- Voir la documentation pour plus de détails.
*/
$activerFluxRssGlobalSite = FALSE; // TRUE|FALSE

// Nombre d'items par flux RSS.
$nombreItemsFluxRss = 50;

// Expiration du cache des flux.
/*
- Temps en secondes avant que le cache des flux n'expire.
- Exemples:
  - `0` équivaut à désactiver le cache;
  - `1800` équivaut à 30 minutes;
  - `3600` équivaut à 1 heure;
  - `28800` équivaut à 8 heures;
  - `43200` équivaut à 12 heures;
  - `86400` équivaut à 1 journée;
  - `604800` équivaut à 7 jours.
*/
$dureeCacheFluxRss = 0;

########################################################################
##
## Configuration du formulaire de contact.
##
########################################################################

/* _______________ Général. _______________ */

// Contact par défaut.
/*
- Pour utiliser le formulaire de contact livré par défaut sans devoir créer une page de contact personnalisée simplement pour y renseigner la variable `$courrielContact`, saisir ci-dessous l'adresse courriel à utiliser, sinon laisser vide.
*/
$contactCourrielParDefaut = '';

// Vérification de la forme du courriel.
$contactVerifierCourriel = TRUE; // TRUE|FALSE

// Ajout optionnel d'un identifiant dans l'objet.
$contactCourrielIdentifiantObjet = '[Contact] ';

// Ajout dans le formulaire d'une option d'envoi d'une copie à l'expéditeur.
$contactCopieCourriel = FALSE; // TRUE|FALSE

/* _______________ Antipourriel. _______________ */

// Ajout d'un champ de calcul mathématique.
$contactActiverCaptchaCalcul = TRUE; // TRUE|FALSE
$contactCaptchaCalculMin = 2;
$contactCaptchaCalculMax = 10;

// Si `$contactActiverCaptchaCalcul` vaut TRUE, inversion des termes de l'addition et du résultat.
/*
- Par défaut, le calcul se présente ainsi:
  c = ? + ?
  Bien sûr, il peut y avoir plusieurs réponses possibles.
- Mettre à FALSE pour anuler l'inversion et obtenir ceci:
  a + b = ?
*/
$contactCaptchaCalculInverse = TRUE; // TRUE|FALSE;

// Limitation du nombre de liens dans le corps d'un message.
$contactActiverCaptchaLiens = FALSE; // TRUE|FALSE
$contactCaptchaLiensNombre = 5; // Nombre de liens max dans un message

########################################################################
##
## Configuration de la galerie.
##
########################################################################

/* _______________ Général. _______________ */

// Qualité des images JPG générées par le script.
$galerieQualiteJpg = 90; // 0-100

// Dimensions d'une vignette si génération automatique.
/*
- La valeur `0` assignée à une dimension signifie que cette dernière sera calculée automatiquement à partir de l'autre dimension donnée ainsi que des dimensions de l'image source. Si les deux dimensions sont données, la plus grande taille possible contenable dans ces dimensions sera utilisée, sans toutefois dépasser la taille originale.
- Les proportions de l'image sont conservées.
- Au moins une dimension doit être donnée.
*/
$galerieDimensionsVignette = array (
	'largeur' => 100,
	'hauteur' => 100,
);

// Taille forcée pour une vignette.
/*
- En résumé: permet d'avoir des vignettes de même hauteur ou de même largeur, ou les deux.
- En détails: si la taille calculée pour la génération d'une vignette est plus petite que la taille voulue pour une vignette, ajoute des bordures blanches (ou transparentes pour les PNG) pour compléter l'espace manquant.
  - Par exemple, disons que nous avons une petite image source de 24 px × 24 px, et que la taille voulue pour une vignette est de 100 px × 100 px. Si `$galerieForcerDimensionsVignette` vaut FALSE, la vignette créée aura la même taille que l'image source (c'est-à-dire 24 px × 24 px), mais si `$galerieForcerDimensionsVignette` vaut TRUE, alors la vignette fera 100 px × 100 px, mais il y aura des marges blanches ou transparentes de 38 px autour du corps de l'image (qui se trouve donc à être centrée).
  - Bien sûr, on ne peut forcer une dimension (largeur ou hauteur) que si la dimension voulue a été précisée dans `$galerieDimensionsVignette`.
*/
$galerieForcerDimensionsVignette = TRUE; // TRUE|FALSE

/* _______________ Accueil d'une galerie. _______________ */

// Pagination des vignettes de la page d'accueil.
/*
- Nombre de vignettes par page (0 pour désactiver la pagination).
*/
$galerieVignettesParPage = 0;

// S'il y a pagination, affichage des liens au-dessus ou au-dessous des vignettes, ou les deux.
$galeriePagination = array (
	'au-dessus' => TRUE, // TRUE|FALSE
	'au-dessous' => FALSE, // TRUE|FALSE
);

// Affichage d'informations au sujet de la galerie.
$galerieInfoAjout = TRUE; // TRUE|FALSE

// S'il y a des informations au sujet de la galerie, choix de leur emplacement.
$galerieInfoEmplacement = 'haut'; // haut|bas

// Fenêtre Javascript sur l'accueil de la galerie pour consulter les images.
/*
- Utiliser Slimbox 2 pour passer d'une image à une autre sur la page d'accueil de la galerie au lieu de naviguer d'une image à une autre en rechargeant toute la page.
*/
$galerieAccueilJavascript = FALSE; // TRUE|FALSE

/* _______________ Page individuelle d'une oeuvre. _______________ */

// Choix de la navigation entre les oeuvres.
$galerieNavigation = 'fleches'; // fleches|vignettes

// Si la navigation est faite avec des vignettes, ajout d'une petite flèche au centre des vignettes.
/*
- Il s'agit d'une superposition (une sorte de tatouage) d'une image au centre de chaque vignette. Par défaut il s'agit d'une petite flèche gauche pour la vignette de l'image précédente et d'une petite flèche droite pour la vignette de l'image suivante. Il est possible d'utiliser ses propres images. Le résultat est un seul fichier image.
- Voir la documentation pour plus de détails.
*/
$galerieNavigationTatouerVignettes = FALSE; // TRUE|FALSE

// Si la navigation est faite avec des vignettes, et si `$galerieNavigationTatouerVignettes` vaut FALSE, ajout d'une petite flèche à côté des vignettes.
/*
- Au lieu de tatouer les vignettes, ajouter une image à leur côté. Il est possible d'utiliser ses propres images d'accompagnement.
- Voir la documentation pour plus de détails.
*/
$galerieNavigationAccompagnerVignettes = TRUE; // TRUE|FALSE

// Choix de l'emplacement de la navigation.
$galerieNavigationEmplacement = 'haut'; // haut|bas

// Aperçu grâce à des minivignettes du contenu de la galerie sur les pages individuelles de chaque oeuvre.
/*
- Il s'agit d'un résumé visuel de la galerie. Chaque oeuvre est représentée par une toute petite vignette cliquable.
*/
$galerieAfficherMinivignettes = TRUE; //TRUE|FALSE

// S'il y a des minivignettes, choix de leur emplacement.
$galerieMinivignettesEmplacement = 'haut'; // haut|bas

// S'il y a des minivignettes, le nombre à afficher.
/*
- 0 pour un nombre illimité.
*/
$galerieMinivignettesNombre = 0;

// Ajout automatique d'une légende dans le cas où aucune légende n'a été précisée.
/*
- La légende générée automatiquement correspond au contenu de l'attribut `alt` et à la taille du fichier.
*/
$galerieLegendeAutomatique = FALSE; // TRUE|FALSE

// Utilisation de la syntaxe Markdown dans la légende.
/*
- Active la syntaxe Markdown pour le texte de la légende (valeur du paramètre `intermediaireLegende`).
*/
$galerieLegendeMarkdown = FALSE; // TRUE|FALSE

// Affichage d'informations Exif pour les fichiers JPG.
/*
- La version de PHP utilisée doit être compilée avec l'option `--enable-exif`. Voir <http://us3.php.net/manual/fr/exif.requirements.php> pour plus de détails. Si ce n'est pas le cas, les informations Exif ne seront tout simplement pas affichées.
*/
$galerieExifAjout = TRUE; // TRUE|FALSE

// S'il y a lieu, choix des informations Exif à afficher.
/*
- Chaque élément prend comme valeur TRUE ou FALSE.
*/
$galerieExifInfos = array (
	'DateTime' => TRUE,
	'ExposureTime' => TRUE,
	'FNumber' => TRUE,
	'FocalLength' => TRUE,
	'ISOSpeedRatings' => TRUE,
	'Make' => TRUE,
	'Model' => TRUE,
);

// Si le format original d'une image existe, est-ce que le lien vers le fichier est fait sur l'image ou dans la légende, ou les deux?
$galerieLienOriginalEmplacement = 'imageLegende'; // image|legende|imageLegende

// Si le format original d'une image existe, est-ce qu'on ajoute une petite icône sous l'image pour le signifier?
$galerieLienOriginalIcone = TRUE; // TRUE|FALSE

// Si le format original d'une image existe, est-ce que le lien vers le fichier est pris en charge par une fenêtre Javascript (ne fonctionne pas pour le SVG)?
$galerieLienOriginalJavascript = FALSE; // TRUE|FALSE

// Si le format original d'une image existe et que le lien n'est pas pris en charge par une fenêtre Javascript, est-ce que le lien vers le fichier force le téléchargement sans affichage dans le navigateur?
$galerieLienOriginalTelecharger = FALSE; // TRUE|FALSE

// S'il y a lieu, emplacement de la légende, des informations Exif et du lien vers l'image originale.
/*
- Les choix possibles sont: haut, bas, sousContenu, surContenu.
- Les emplacements `haut` et `bas` font référence à l'image en version intermediaire, alors que `sousContenu` et `surContenu` font référence à la page. Par exemple, l'option `sousContenu` place avec la configuration et le style par défaut de Squeletml les informations de l'image dans la colonne de gauche.
- Les trois emplacements à préciser sont respectivement lorsqu'il n'y a pas de colonne, lorsqu'il y a une seule colonne et lorsqu'il y en a deux.
*/
$galerieLegendeEmplacement = array ('bas', 'sousContenu', 'sousContenu');

/* _______________ Syndication de contenu (flux RSS). _______________ */

// Syndication individuelle par défaut des galeries.
/*
- Note: il est possible de configurer la syndication pour chaque galerie, et ainsi donner une valeur différente de celle par défaut. En effet, si la variable `$rss` existe (par exemple déclarée dans une page) et n'est pas vide, c'est la valeur de cette dernière qui est utilisée.
*/
$galerieActiverFluxRssParDefaut = TRUE; // TRUE|FALSE

// Syndication globale pour toutes les galeries.
/*
- La syndication globale des galeries n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant la liste des galeries et de leur URL.
- Voir la documentation pour plus de détails.
*/
$galerieActiverFluxRssGlobal = FALSE; // TRUE|FALSE
?>
