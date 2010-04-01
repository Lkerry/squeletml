<?php
########################################################################
##
## Configuration générale.
##
########################################################################

/* ____________________ En-tête HTML. ____________________ */

// Choix du DTD (Définition de Type de Document).
/*
- Les choix possibles sont:
  - XHTML 1.1
  - XHTML 1.0 Strict
  - XHTML 1.0 Transitional
  - HTML 4.01 Strict
  - HTML 4.01 Transitional

- Voir la fonction `doctype()`.
*/
$doctype = 'XHTML 1.0 Strict';

// Complément de la balise `title` selon la langue.
/*
- Le complément de la balise `title` est ajouté à la suite du contenu principal de la balise `title`.
- Pour chaque langue, deux compléments sont précisables:
  - `accueil`, utilisé seulement sur la page d'accueil de la langue en question;
  - `interne`, utilisé pour toutes les autres pages dans cette langue.
- Voir la fonction `baliseTitleComplement()`.
*/
$tableauBaliseTitleComplement['fr']['accueil'] = " | Système de gestion de contenu léger et sans base de données";
$tableauBaliseTitleComplement['fr']['interne'] = " | Squeletml";
$tableauBaliseTitleComplement['en']['accueil'] = " | Lightweight content management system without database";
$tableauBaliseTitleComplement['en']['interne'] = " | Squeletml";

// Fichiers inclus dans des balises `link` et `script`.
/*
- Les types possibles sont: css, cssltIE7, cssIE7, csslteIE7, js, jsDirect, jsDirectltIE7, jsltIE7, favicon, po, rss.
- Syntaxe pour tous les types:
  $balisesLinkScript[] = "URL#TYPE#fichier à inclure#contenu de l'attribut `title`";
  Le contenu de l'attribut `title` est optionnel, et est utilisé seulement pour le type rss.
- Ajouter une étoile à la fin de l'URL pour inclure toutes les pages enfants.
- Dans le fichier de configuration personnalisé, ajouter tout simplement des éléments au tableau `$balisesLinkScript`, par exemple:
  $balisesLinkScript[] = "$urlRacine/*#css#$urlRacine/site/css/style-general.css";
  $balisesLinkScript[] = "$urlRacine/page.php#css#$urlRacine/site/css/style-page.css";
- Voir la fonction `linkScript()`.
*/
$balisesLinkScript[] = "$urlRacine/*#css#$urlRacine/css/squeletml.css";
$balisesLinkScript[] = "$urlRacine/*#css#$urlRacine/css/extensions-proprietaires.css";
$balisesLinkScript[] = "$urlRacine/*#csslteIE7#$urlRacine/css/ie6-7.css";
$balisesLinkScript[] = "$urlRacine/*#cssIE7#$urlRacine/css/ie7.css";
$balisesLinkScript[] = "$urlRacine/*#cssltIE7#$urlRacine/css/ie6.css";
$balisesLinkScript[] = "$urlRacine/*#js#$urlRacine/js/phpjs/php.min.js";
$balisesLinkScript[] = "$urlRacine/*#js#$urlRacine/js/squeletml.js";
$balisesLinkScript[] = "$urlRacine/*#favicon#$urlRacine/fichiers/favicon.png";

// Version par défaut des fichiers déclarés dans le tableau `$balisesLinkScript`.
/*
- La version est ajoutée à la suite du nom des fichiers en tant que variable GET.
- Laisser vide pour désactiver l'ajout de version.
- Exemple de sortie HTML lorsque `$versionParDefautLinkScript` vaut `1`:
  <script type="text/javascript" src="/js/squeletml.js?1"></script>
- Voir la fonction `linkScript()`.
*/
$versionParDefautLinkScript = "";

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
- Si la variable `$robots` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
- Voir la fonction `robots()`.
*/
$robotsParDefaut = 'index, follow, archive';

// Désactivation de l'indexation des pages de catégorie dans les moteurs de recherche.
/*
- L'intérêt de ne pas indexer les pages de catégorie dans les moteurs de recherche est d'éviter le contenu dupliqué.
*/
$desactiverIndexationPagesCategorie = FALSE; // TRUE|FALSE

// Encodage du site.
$charset = 'UTF-8';

// Langue par défaut.
/*
- Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
- Voir la fonction `langue()`.
*/
$langueParDefaut = 'fr';

/* ____________________ Contenu et ordre du flux HTML. ____________________ */

// Titre du site en en-tête.
/*
- Contenu (balises HTML permises) qui sera inséré comme titre de site dans un `h1` sur la page d'accueil, et dans un `p` sur toutes les autres pages.
- Astuce: si vous ne voulez pas bidouiller dans le style, remplacez la première image (dont l'`id` est `logo`) par une autre image de 75px × 75px, et remplacez le contenu du `span` (dont l'`id` est `logoSupplement`) par le titre de votre site.
*/
$titreSite['fr'] = "<img id=\"logo\" src=\"$urlRacine/fichiers/squeletml-logo.png\" alt=\"Squeletml\" /><span id=\"logoSupplement\"><img src=\"$urlRacine/fichiers/squeletml.png\" alt=\"Squeletml\" /></span>";
$titreSite['en'] = $titreSite['fr'];

// Ordre et région des blocs constituant les menus.
/*
Les divers blocs constituant les menus sont positionnables, au choix, dans les régions suivantes:

- `div` `enTete`;
- `div` `surContenu`;
- `div` `debutInterieurContenu`;
- `div` `finInterieurContenu`;
- `div` `sousContenu`;
- `div` `basDePage`.

Ce choix concerne l'ordre dans lequel les blocs apparaissent dans le flux HTML. Ensuite, selon le style CSS utilisé, les deux `div` `surContenu` et `sousContenu` formeront:

- une seule colonne à gauche;
- une seule colonne à droite;
- deux colonnes dont celle de gauche est remplie par les blocs de `surContenu` et celle de droite par les blocs de `sousContenu`;
- deux colonnes dont celle de gauche est remplie par les blocs de `sousContenu` et celle de droite par les blocs de `surContenu`;
- aucune colonne, les blocs étant positionnés au-dessus ou au-dessous du contenu selon la `div` dans laquelle ils ont été assignés.

Chaque bloc se voit assigner trois nombres (séparés par une espace), qui font référence respectivement à l'ordre du bloc lorsqu'il n'y a pas de colonne, lorsqu'il y a une seule colonne et lorsqu'il y en a deux. Selon la centaine à laquelle le nombre appartient, le bloc sera placé dans une région en particulier:

- `div` `enTete`: 100-199 (un nombre entre 100 et 199 signifie que le bloc en question sera placé dans la `div` `enTete`);
- `div` `surContenu`: 200-299;
- `div` `debutInterieurContenu`: 300-399;
- `div` `finInterieurContenu`: 400-499;
- `div` `sousContenu`: 500-599;
- `div` `basDePage`: 600-699.

À l'intérieur d'une même région, l'ordre d'insertion des blocs se fait en ordre croissant des nombres leur étant assignés.

Par exemple:

	$ordreBlocsDansFluxHtml['menu-langues'] = array (510, 510, 504);
	$ordreBlocsDansFluxHtml['flux-rss']     = array (502, 502, 506);

signifie que le menu des langues ainsi que les liens RSS seront insérés dans la `div` `sousContenu`, peu importe le nombre de colonnes, puisque les deux blocs ont un nombre compris entre 500 et 599 pour chaque possibilité en lien avec le nombre de colonnes, et qu'à l'intérieur de la `div`, les liens RSS seront insérés en premier lorsqu'il n'y a pas de colonne et lorsqu'il n'y en a qu'une seule puisqu'en ordre croissant, nous obtenons 502 (les liens RSS) et 510 (le menu des langues), mais s'il y a deux colonnes, les liens RSS seront insérés après le menu des langues, car nous obtenons 504 (le menu des langues) et 506 (les liens RSS).

Il est possible d'insérer un nombre illimité de blocs personnalisés. Il faut toutefois avoir en tête que chaque clé ajoutée dans le tableau `$ordreBlocsDansFluxHtml` doit représenter une partie du nom du fichier à insérer. Par exemple, un bloc personnalisé ayant une clé `heure` dans le tableau `$ordreBlocsDansFluxHtml` fera référence à un fichier `$racine/site/xhtml/(LANGUE/)heure.inc.php`.

Note: le tableau ci-dessous n'a pas de lien avec l'activation ou la désactivation d'une fonctionnalité, mais seulement avec l'odre dans lequel les blocs sont insérés dans le flux HTML dans le cas où la fonctionnalité est activée.

Voir la fonction `blocs()`.
*/
$ordreBlocsDansFluxHtml['balise-h1']             = array (300, 300, 300);
$ordreBlocsDansFluxHtml['infos-publication']     = array (400, 400, 400);
$ordreBlocsDansFluxHtml['licence']               = array (410, 410, 410);
$ordreBlocsDansFluxHtml['lien-page']             = array (420, 420, 420);
$ordreBlocsDansFluxHtml['menu-langues']          = array (500, 500, 200);
$ordreBlocsDansFluxHtml['menu']                  = array (200, 510, 510);
$ordreBlocsDansFluxHtml['menu-categories']       = array (520, 520, 520);
$ordreBlocsDansFluxHtml['legende-image-galerie'] = array (530, 530, 530);
$ordreBlocsDansFluxHtml['flux-rss']              = array (540, 540, 540);
$ordreBlocsDansFluxHtml['faire-decouvrir']       = array (550, 550, 550);
$ordreBlocsDansFluxHtml['marque-pages-sociaux']  = array (560, 560, 560);

// Conditions d'insertion des blocs.
/*
- Il est possible d'ajouter des conditions à l'insertion d'un bloc de contenu. Les conditions doivent être du code PHP valide. Elles seront exécutées par la fonction PHP `eval()`. Un bloc sera inclus seulement si le code PHP retourne TRUE. Exemple:
	
		$conditionsBlocs['faire-decouvrir'] = 'return strpos($url, "/dossier/") ? TRUE : FALSE;';
	
	Voici la même condition, écrite autrement:
	
		$conditionsBlocs['faire-decouvrir'] = 'if (strpos($url, "/dossier/")) {return TRUE;} else {return FALSE;}';
	
	Dans cet exemple, le bloc «Faire découvrir» sera inclus seulement si l'URL contient `/dossier/`.
	
- Si aucune condition n'est donnée pour un bloc, le retour est automatiquement évalué à TRUE.
*/
$conditionsBlocs = array ();

// Détection du type MIME.
/*
- La détection du type MIME se fait par un des outils suivants, selon leur disponibilité (en ordre de priorité):
  - `Fileinfo` de PHP;
  - commande `file` si la variable `$typeMimeFile` vaut TRUE;
  - tableau personnalisé de correspondance entre une extension et son type MIME si la variable `$typeMimeCorrespondance` n'est pas vide. Exemple:
    $typeMimeCorrespondance = array ('rmi' => 'audio/midi');
  - tableau par défaut de correspondance entre une extension et son type MIME de la fonction `file_get_mimetype()`.
*/
$typeMimeFile = FALSE; // TRUE|FALSE
$typeMimeCheminFile = '/usr/bin/file';
$typeMimeCorrespondance = array ();

// Inclusion des ancres.
$inclureAncres = TRUE; // TRUE|FALSE

// Inclusion du sur-titre.
$inclureSurTitre = FALSE; // TRUE|FALSE

// Inclusion du sous-titre.
$inclureSousTitre = TRUE; // TRUE|FALSE

// Inclusion du bas de page.
$inclureBasDePage = TRUE; // TRUE|FALSE

// Activation par défaut de l'option «Faire découvrir à des ami-e-s».
$activerFaireDecouvrirParDefaut = TRUE; // TRUE|FALSE

// Activation par défaut des marque-pages ou réseaux sociaux.
$activerMarquePagesSociauxParDefaut = TRUE; // TRUE|FALSE

// Affichage du message pour Internet Explorer 6.
/*
- Message invitant l'internaute à télécharger un navigateur moderne.
- Voir la fonction `messageIe6()`.
*/
$afficherMessageIe6 = TRUE; // TRUE|FALSE

// Auteur par défaut.
/*
- Auteur par défaut si aucune autre précision n'est apportée. Si la variable `$auteur` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
- L'auteur est inséré en tant que métabalise `author`. Cette information est également utilisée dans le bloc des informations de publication, lors du listage des articles faisant partie d'une catégorie ainsi que dans les flux RSS.
*/
$auteurParDefaut = "";

// Affichage par défaut des informations de publication.
/*
- Les informations de publication contiennent l'auteur, la date de création et la date de dernière révision.
- Voir dans la documentation les explications pour les variables `$auteur`, `$dateCreation`, `$dateRevision` et `$infosPublication`.
*/
$afficherInfosPublicationParDefaut = TRUE; // TRUE|FALSE

// Affichage par défaut d'une suggestion de code pour un lien vers la page.
/*
- Le code propose un lien prêt à être intégré dans une page HTML. Si la variable `$afficherLienPage` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
*/
$afficherLienPageParDefaut = TRUE; // TRUE|FALSE

// Licence par défaut pour tout le site.
/*
- Licence à déclarer pour chaque page du site. Si la variable `$licence` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
- Plusieurs licences peuvent être déclarées, chacune devant être séparée par une espace.
- Voir la fonction `licence()` pour connaître les choix possibles.
*/
$licenceParDefaut = "";

// Affichage par défaut de la table des matières.
/*
- État de la table des matières si aucune autre précision n'est apportée. Si la variable `$tableDesMatieres` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
*/
$afficherTableDesMatieresParDefaut = FALSE; // TRUE|FALSE

// Activation de boîtes déroulantes par défaut.
/*
Une boîte déroulante permet d'afficher/de masquer un contenu par simple clic, et enregistre, si possible, le choix d'affichage de l'internaute dans un témoin valide durant 365 jours. Ce contenu peut être situé n'importe où dans la page: menu, corps, bas de page, etc. Une boîte déroulante peut être activée seulement pour un contenu constitué d'un conteneur, d'un titre et d'un corps. La représentation générale est la suivante:

	<balise id="conteneur"> ou <balise class="conteneur">
		<balise class="bDtitre">...</balise>
		<balise class="bDcorps (afficher|masquer)">...</balise>
	</balise>

Le corps est affiché seulement s'il possède une classe `afficher`. La classe `masquer` n'est donc pas essentielle.

Voici un exemple concret:

1. avant application de la boîte déroulante:

	<div id="fruits">
		<h2 class="bDtitre">Fruits disponibles</h2>
		
		<div class="bDcorps afficher">
			<ul>
				<li>Fraises</li>
				<li>Poires</li>
				<li>Pommes</li>
			</ul>
			
			<p>Il n'y a plus de kiwi.</p>
		</div>
	</div>

2. après application de la boîte déroulante. Le corps est affiché:

	<div id="fruits">
		<h2 class="bDtitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[-]&nbsp;</span><span>Fruits disponibles</span></a></h2>
		
		<div class="bDcorps afficher">
			<ul>
				<li>Fraises</li>
				<li>Poires</li>
				<li>Pommes</li>
			</ul>
			
			<p>Il n'y a plus de kiwi.</p>
		</div>
	</div>

3. après application de la boîte déroulante si le corps n'avait pas possédé de classe `afficher` (le corps aurait donc été masqué):

	<div id="fruits">
		<h2 class="bDtitre"><a href="#" class="boiteDeroulanteLien"><span class="boiteDeroulanteSymbole">[+]&nbsp;</span><span>Fruits disponibles</span></a></h2>
		
		<div class="bDcorps masquer">
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

1. en ajoutant `#fruits` dans la variable `$boitesDeroulantesParDefaut`.

	La syntaxe de chaque boîte est donc:

		'#conteneur'

	pour un conteneur identifié à l'aide de son attribut `id`, ou bien:

		'.conteneur'

	pour un conteneur identifié à l'aide de son attribut `class`.

	En ajoutant au moins une boîte dans cette variable, chaque page de Squeletml ajoutera les scripts nécessaires aux boîtes déroulantes. Pour ajouter plusieurs boîtes, le séparateur à utiliser est une espace. Voici un exemple d'ajout de plusieurs boîtes:

		$boitesDeroulantesParDefaut = "#conteneur1 .conteneur2 .conteneur3";

2. en renseignant la variable `$boitesDeroulantes` dans une page spécifique avant l'inclusion du premier fichier PHP (la syntaxe est la même que pour la variable `$boitesDeroulantesParDefaut`). Voir la documentation pour plus de détails.

Comme le montre l'exemple général, le titre n'est pas à comprendre au sens sémantique. Ce n'est donc pas nécessaire de l'entourer de balises `h1`, `h2`, `h3`, `h4`, `h5` ou `h6`. Il s'agit simplement du texte qui servira à afficher ou masquer le corps.

Voir la fonction Javascript `boiteDeroulante()`.
*/
$boitesDeroulantesParDefaut = "";

// Activation de boîtes déroulantes à la main par défaut.
/*
- Si vaut TRUE, insère les fichiers nécessaires à la gestion d'une boîte déroulante, mais l'appel à la fonction Javascript `boiteDeroulante()` est fait à la main par l'utilisateur.
- Voir les explications de la variable `$boitesDeroulantesAlaMain` dans la documentation pour plus de détails.
*/
$boitesDeroulantesAlaMainParDefaut = FALSE; // TRUE|FALSE

// Balises `link` et `script` finales, ajoutées juste avant `</body>`.
/*
- Voir les commentaires de la variable `$balisesLinkScript` dans ce même fichier de configuration pour les détails de la syntaxe.
- Voir la fonction `linkScript()`.
*/
$balisesLinkScriptFinales[] = "$urlRacine/*#jsDirect#ajouteEvenementLoad(function(){egaliseHauteur('interieurPage', 'surContenu', 'sousContenu');});";

// Inclusion de l'aperçu d'une page.
/*
- L'aperçu d'une page (s'il existe et n'est pas vide, et si `$inclureApercu` vaut TRUE) est inséré en tant que commentaire HTML au début de la `div` `interieurContenu` et est utilisé par certains scripts comme celui de construction des flux RSS.
- Le but de mettre `$inclureApercu` à FALSE est que les scripts qui utilisent normalement l'aperçu d'une page sauteront alors l'étape de sa recherche, ce qui sauvera du temps et des ressources.
- Voir les explications de la variable `$apercu` dans la documentation pour plus de détails.
*/
$inclureApercu = TRUE; // TRUE|FALSE

// Si `$inclureApercu` vaut TRUE, aperçu par défaut.
/*
- Voir les explications de la variable `$apercu` dans la documentation pour connaître les différentes valeurs possibles.
*/
$apercuParDefaut = "";

// S'il y a lieu, taille, en nombre de caractères, d'un aperçu généré automatiquement.
$tailleApercuAutomatique = 750;

// Expiration du cache.
/*
- Temps en secondes avant que le cache n'expire.
- Exemples:
  - `0` équivaut à désactiver le cache;
  - `1800` équivaut à 30 minutes;
  - `3600` équivaut à 1 heure;
  - `28800` équivaut à 8 heures;
  - `43200` équivaut à 12 heures;
  - `86400` équivaut à 1 jour;
  - `259200` équivaut à 3 jours;
  - `604800` équivaut à 7 jours.
*/
$dureeCache['fluxRss']               = 0;
$dureeCache['categorie']             = 0;
$dureeCache['galerie']               = 0;
$dureeCache['publications-recentes'] = 0; // Voir la fonction `publicationsRecentes()`.

// Génération automatisée du titre principal de la page d'accueil d'une catégorie.
/*
- Si vaut `TRUE`, un titre `h1` sera ajouté avant la liste des pages faisant partie de la catégorie affichée.
*/
$genererTitrePageCategories = TRUE; // TRUE|FALSE

// Si `$genererTitrePageCategories` vaut TRUE, nom de la catégorie précédé de l'expression «Articles dans la catégorie» traduit dans la langue de la page.
/*
- Par exemple, le choix est d'afficher «Chiens» ou «Articles dans la catégorie Chiens».
*/
$titrePageCategoriesAvecMotCategorie = TRUE; // TRUE|FALSE

// Génération automatisée du bloc de menu des catégories.
/*
- Le bloc de menu des catégories peut être réalisé à la main dans le fichier `menu-categories.inc.php` ou généré automatiquement.
- Voir la section «Bloc de menu des catégories» dans la documentation pour plus de détails.
*/
$genererMenuCategories = TRUE; // TRUE|FALSE

// Si `$genererMenuCategories` vaut TRUE, affichage du nombre d'articles dans chaque catégorie.
/*
- Exemple: `Animaux (23)`.
*/
$afficherNombreArticlesCategorie = TRUE; // TRUE|FALSE

// Activation s'il y a lieu des catégories spéciales.
/*
- Les catégories spéciales sont les dernières publications et les derniers ajouts aux galeries.
- Par défaut, l'URL est `$urlRacine/categorie.php?id=(site|galeries)`.
*/
$activerCategoriesGlobales = TRUE; // TRUE|FALSE

// Pagination par défaut de la liste des articles faisant partie d'une catégorie.
/*
- Nombre d'articles par page par défaut (0 pour désactiver la pagination).
*/
$nombreArticlesParPageCategorie = 10;

/* ____________________ Style CSS. ____________________ */

// Note: les options suivantes n'ont aucune influence sur le flux HTML. Il s'agit simplement d'un outil optionnel mais utile pour modifier le style du site sans devoir bidouiller dans les feuilles CSS. En aucun cas ces options sont obligatoires à la stylisation du site.

// Style des liens visités.
/*
- Les liens visités (`a:visited`) de tout le site (menus y compris) ont par défaut un style différent des liens non visités. Mettre à FALSE pour différencier seulement les liens visités contenus dans le corps des pages (`div` `contenu`).
*/
$differencierLiensVisitesHorsContenu = TRUE; // TRUE|FALSE

// Détection des liens actifs dans les blocs.
/*
- Si la détection est activée pour un bloc, ajoute la classe `actif` à tous les liens (balises `a`) de ce bloc et pointant vers la page en cours ainsi qu'au `li` contenant ce lien. Avec la feuille de style par défaut, le résultat est un lien actif en gras et un `li` marqué d'une petite puce spéciale.
- Voir les explications de la variable `$ordreBlocsDansFluxHtml` dans ce fichier de configuration pour connaître la syntaxe des clés du tableau (par exemple `menu-langues`).
- Chaque élément prend comme valeur TRUE, FALSE ou NULL. NULL signifie que cette option ne s'applique pas au bloc en question.
- Voir la fonction `lienActif()`.
*/
$liensActifsBlocs['balise-h1']             = NULL;
$liensActifsBlocs['faire-decouvrir']       = NULL;
$liensActifsBlocs['flux-rss']              = NULL;
$liensActifsBlocs['infos-publication']     = NULL;
$liensActifsBlocs['legende-image-galerie'] = FALSE; // S'il y a lieu (voir `$galerieLegendeEmplacement`).
$liensActifsBlocs['licence']               = NULL;
$liensActifsBlocs['lien-page']             = NULL;
$liensActifsBlocs['marque-pages-sociaux']  = NULL;
$liensActifsBlocs['menu']                  = TRUE;
$liensActifsBlocs['menu-categories']       = TRUE; // S'il y a lieu (voir la section «Catégories» de la documentation).
$liensActifsBlocs['menu-langues']          = TRUE;

// Limite de la profondeur d'une liste dans un bloc.
/*
Pour chaque bloc, préciser par TRUE ou FALSE (ou NULL si cette option ne s'applique pas au bloc en question) si la profondeur d'une liste y étant présente doit être limitée. Aucun texte n'est supprimé, mais une classe `masquer` est ajoutée aux sous-listes inactives identifiées auparavant par la fonction `lienActif()`, ce qui signifie qu'un bloc pour lequel la détection des liens actifs n'aura pas été activée dans la variable `$liensActifsBlocs` (dans ce fichier de configuration) ne pourra pas avoir de limite de profondeur.

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
$limiterProfondeurListesBlocs['balise-h1']             = NULL;
$limiterProfondeurListesBlocs['faire-decouvrir']       = NULL;
$limiterProfondeurListesBlocs['flux-rss']              = NULL;
$limiterProfondeurListesBlocs['infos-publication']     = NULL;
$limiterProfondeurListesBlocs['legende-image-galerie'] = FALSE;
$limiterProfondeurListesBlocs['licence']               = NULL;
$limiterProfondeurListesBlocs['lien-page']             = NULL;
$limiterProfondeurListesBlocs['marque-pages-sociaux']  = NULL;
$limiterProfondeurListesBlocs['menu']                  = TRUE;
$limiterProfondeurListesBlocs['menu-categories']       = TRUE; // S'il y a lieu (voir la section «Catégories» de la documentation).
$limiterProfondeurListesBlocs['menu-langues']          = FALSE;

// Nombre de colonnes.
/*
- Si vaut 2, ajoute à la balise `body` les classes `deuxColonnes`, `colonneAgauche` et `colonneAdroite`, sinon si vaut 1, ajoute la classe `uneColonne`, sinon si vaut 0, ajoute la classe `aucuneColonne`.
*/
$nombreDeColonnes = 1; // 0|1|2

// S'il y a lieu, emplacement de la colonne unique.
/*
- Si `$nombreDeColonnes` vaut 1 et que `$uneColonneAgauche` vaut TRUE, les classes `colonneAgauche` et `uneColonneAgauche` sont ajoutées à `body`, sinon si `$nombreDeColonnes` vaut 1 et que `$uneColonneAgauche` vaut FALSE, les classes `colonneAdroite` et `uneColonneAdroite` sont ajoutées à `body`.
*/
$uneColonneAgauche = TRUE; // TRUE|FALSE

// Emplacement du sous-contenu lorsqu'il y a deux colonnes.
/*
- Si `$nombreDeColonnes` vaut 2 et si `$deuxColonnesSousContenuAgauche` vaut TRUE, ajoute la classe `deuxColonnesSousContenuAgauche` à `body`, sinon si `$nombreDeColonnes` vaut 2 et que `$deuxColonnesSousContenuAgauche` vaut FALSE, ajoute la classe `deuxColonnesSousContenuAdroite` à `body`.
- Le sur-contenu va être affiché par défaut dans la colonne opposée.
*/
$deuxColonnesSousContenuAgauche = TRUE; // TRUE|FALSE

// S'il y a lieu, arrière-plan d'une colonne.
$arrierePlanColonne = 'rayuresEtBordure'; // aucun|bordure|rayures|rayuresEtBordure|fondUni

// Div `page` avec bordures.
/*
- Les valeurs possibles sont TRUE ou FALSE.
*/
$borduresPage['droite'] = TRUE;
$borduresPage['bas']    = TRUE;
$borduresPage['gauche'] = TRUE;

// S'il y a au moins une colonne, étendre l'en-tête sur toute la largeur du site.
/*
- Par défaut, l'en-tête ne s'étend que sur la largeur du contenu, excluant la largeur de la ou des colonnes. Mettre à TRUE pour l'étendre sur toute la page.
*/ 
$enTetePleineLargeur = FALSE; // TRUE|FALSE

// Table des matières avec coins arrondis.
$tableDesMatieresArrondie = TRUE; // TRUE|FALSE

// Blocs de contenu avec coins arrondis par défaut.
$blocsArrondisParDefaut = FALSE; // TRUE|FALSE

// Blocs de contenu spécifiques avec coins arrondis.
/*
Il est possible de modifier la configuration par défaut des blocs arrondis pour un bloc en particulier selon le nombre de colonnes. Par exemple, la ligne suivante:

	$blocsArrondisSpecifiques['menu'] = array (TRUE, FALSE, FALSE);

précise que le bloc de menu principal devra avoir des coins arrondis lorsqu'il n'y a pas de colonne, mais ne devra pas en avoir lorsqu'il y en a une ou deux. Nous pouvons donc dégager la syntaxe générale suivante:

	$blocsArrondisSpecifiques['bloc'] = array (valeur quand aucune colonne, valeur quand 1 colonne, valeur quand 2 colonnes);
*/
$blocsArrondisSpecifiques['balise-h1'] = array (FALSE, FALSE, FALSE);
$blocsArrondisSpecifiques['menu']      = array (TRUE, FALSE, FALSE);
$blocsArrondisSpecifiques['licence']   = array (TRUE, TRUE, TRUE);

/* ____________________ Syndication de contenu (flux RSS). ____________________ */

// Syndication globale du site (dernières publications).
/*
- La syndication n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant une liste d'URL.
- Voir la documentation pour plus de détails.
*/
$activerFluxRssGlobalSite = TRUE; // TRUE|FALSE

// Syndication individuelle par défaut des catégories.
/*
- Note: il est possible de configurer la syndication pour chaque catégorie, et ainsi donner une valeur différente de celle par défaut. En effet, si la variable `$rssCategorie` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
*/
$activerFluxRssCategorieParDefaut = TRUE; // TRUE|FALSE

// Nombre maximal d'items par flux RSS.
$nombreItemsFluxRss = 25;

// Si `$inclureApercu` vaut TRUE, utiliser les aperçus dans les flux RSS.
$utiliserApercuDansFluxRss = FALSE; // TRUE|FALSE

########################################################################
##
## Configuration du formulaire de contact.
##
########################################################################

/* ____________________ Général. ____________________ */

// Contact par défaut.
/*
- Pour utiliser le formulaire de contact livré par défaut sans devoir créer une page de contact personnalisée simplement pour y renseigner la variable `$courrielContact`, saisir ci-dessous l'adresse courriel à utiliser, sinon laisser vide.
*/
$contactCourrielParDefaut = "";

// Vérification de la forme du courriel.
$contactVerifierCourriel = TRUE; // TRUE|FALSE

// Ajout optionnel d'un identifiant dans l'objet.
$contactCourrielIdentifiantObjet = '[Contact] ';

// Ajout dans le formulaire d'une option d'envoi d'une copie à l'expéditeur.
$contactCopieCourriel = FALSE; // TRUE|FALSE

// Champs obligatoires.
/*
- Chaque élément prend comme valeur TRUE ou FALSE.
*/
$contactChampsObligatoires['nom']     = TRUE;
$contactChampsObligatoires['message'] = TRUE;

/* ____________________ Antipourriel. ____________________ */

// Ajout d'un champ de calcul mathématique.
$contactActiverCaptchaCalcul = TRUE; // TRUE|FALSE
$contactCaptchaCalculMin = 2;
$contactCaptchaCalculMax = 10;

// Si `$contactActiverCaptchaCalcul` vaut TRUE, inversion des termes de l'addition et du résultat.
/*
- Par défaut (TRUE), le calcul se présente ainsi:
  c = ? + ?
  Bien sûr, il peut y avoir plusieurs réponses possibles.
- Mettre à FALSE pour annuler l'inversion et obtenir ceci:
  a + b = ?
*/
$contactCaptchaCalculInverse = TRUE; // TRUE|FALSE;

// Limitation du nombre de liens dans le corps d'un message.
$contactActiverCaptchaLiens = FALSE; // TRUE|FALSE
$contactCaptchaLiensNombre = 5; // Nombre maximal de liens dans un message

########################################################################
##
## Configuration de la galerie.
##
########################################################################

/* ____________________ Général. ____________________ */

// Génération automatisée du titre principal des pages d'une galerie.
/*
- Si vaut `TRUE`, un titre `h1` sera ajouté au début de chaque page d'une galerie (accueil d'une galerie et pages individuelles d'une image).
*/
$galerieGenererTitrePages = TRUE; // TRUE|FALSE

// Si `$galerieGenererTitrePages` vaut TRUE, séparation du titre de l'image et du nom de la galerie.
/*
- Par défaut, le titre généré comprend dans la balise `h1` le titre de l'image et le nom de la galerie. Mettre à TRUE pour que le titre de l'image soit dans un `h1` et le nom de la galerie dans un `p`.
*/
$galerieSeparerTitreImageEtNomGalerie = FALSE; // TRUE|FALSE

// Si `$galerieGenererTitrePages` vaut TRUE, nom de la galerie précédé du mot «Galerie» traduit dans la langue de la page.
/*
- Par exemple, le choix est d'afficher «Chiens» ou «Galerie Chiens».
- `accueil` correspond à l'accueil d'une galerie, alors que `page-image` correspond à la page individuelle d'une image.
- Chaque élément peut valoir TRUE ou FALSE.
*/
$galerieTitreAvecMotGalerie['accueil']    = TRUE;
$galerieTitreAvecMotGalerie['page-image'] = TRUE;

// Qualité des images JPG générées par le script.
$galerieQualiteJpg = 90; // 0-100

// Couleur allouée pour les images JPG et GIF générées par le script.
/*
- Cette couleur est visible lorsque des bordures sont ajoutées aux vignettes générées automatiquement (`$galerieForcerDimensionsVignette` doit valoir TRUE). Par défaut, c'est du blanc.
- Les images PNG ont des bordures transparentes.
*/
$galerieCouleurAlloueeImage = array (
	'rouge' => '255',
	'vert'  => '255',
	'bleu'  => '255',
);

// Dimensions d'une vignette si génération automatique.
/*
- La valeur `0` assignée à une dimension signifie que cette dernière sera calculée automatiquement à partir de l'autre dimension donnée ainsi que des dimensions de l'image source. Si les deux dimensions sont données, la plus grande taille possible contenable dans ces dimensions sera utilisée, sans toutefois dépasser la taille originale.
- Les proportions de l'image sont conservées.
- Au moins une dimension doit être donnée.
*/
$galerieDimensionsVignette['largeur'] = 100;
$galerieDimensionsVignette['hauteur'] = 100;

// Taille forcée pour une vignette.
/*
- En résumé: permet d'avoir des vignettes de même hauteur ou de même largeur, ou les deux.
- En détails: si la taille calculée pour la génération d'une vignette est plus petite que la taille voulue pour une vignette, ajoute des bordures de couleur `$galerieCouleurAlloueeImage` (ou transparentes pour les PNG) pour compléter l'espace manquant.
  - Par exemple, disons que nous avons une petite image source de 24 px × 24 px, et que la taille voulue pour une vignette est de 100 px × 100 px. Si `$galerieForcerDimensionsVignette` vaut FALSE, la vignette créée aura la même taille que l'image source (c'est-à-dire 24 px × 24 px), mais si `$galerieForcerDimensionsVignette` vaut TRUE, alors la vignette fera 100 px × 100 px, mais il y aura des marges blanches ou transparentes de 38 px autour du corps de l'image (qui se trouve donc à être centrée).
  - Bien sûr, on ne peut forcer une dimension (largeur ou hauteur) que si la dimension voulue a été précisée dans `$galerieDimensionsVignette`.
*/
$galerieForcerDimensionsVignette = TRUE; // TRUE|FALSE

/* ____________________ Accueil d'une galerie. ____________________ */

// Pagination des vignettes de la page d'accueil.
/*
- Nombre de vignettes par page (0 pour désactiver la pagination).
*/
$galerieVignettesParPage = 0;

// S'il y a pagination, affichage des liens au-dessus ou au-dessous des vignettes, ou les deux.
/*
- Chaque élément prend comme valeur TRUE ou FALSE.
*/
$galeriePagination['au-dessus']  = TRUE;
$galeriePagination['au-dessous'] = FALSE;

// Affichage d'informations au sujet de la galerie.
$galerieInfoAjout = TRUE; // TRUE|FALSE

// S'il y a des informations au sujet de la galerie, choix de leur emplacement.
$galerieInfoEmplacement = 'haut'; // haut|bas

// Fenêtre Javascript sur l'accueil de la galerie pour consulter les images.
/*
- Utiliser Slimbox 2 pour passer d'une image à une autre sur la page d'accueil de la galerie au lieu de naviguer d'une image à une autre en rechargeant toute la page.
*/
$galerieAccueilJavascript = FALSE; // TRUE|FALSE

// Si `$galerieAccueilJavascript` vaut TRUE, ajout d'un lien de navigation sans Javascript.
$galerieAccueilLienSansJavascript = TRUE; // TRUE|FALSE

// Si `$galerieAccueilLienSansJavascript` vaut TRUE, choix de l'emplacement du lien.
/*
- Les choix possibles sont: haut, bas, info.
- Les emplacements `haut` et `bas` font référence au bloc de vignettes, alors que `info` fait référence aux informations au sujet de la galerie affichées lorsque `$galerieInfoAjout` vaut TRUE.
*/
$galerieAccueilLienSansJavascriptEmplacement = 'info';

/* ____________________ Page individuelle d'une image. ____________________ */

// Choix de la navigation entre les images.
$galerieNavigation = 'fleches'; // fleches|vignettes

// Si la navigation est faite avec des vignettes, ajout d'une petite flèche au centre des vignettes.
/*
- Il s'agit d'une superposition (une sorte de tatouage) d'une image au centre de chaque vignette. Par défaut il s'agit d'une petite flèche gauche pour la vignette de l'image précédente et d'une petite flèche droite pour la vignette de l'image suivante. Il est possible d'utiliser ses propres images. Le résultat est un seul fichier image.
- Voir la documentation pour plus de détails.
*/
$galerieNavigationTatouerVignettes = TRUE; // TRUE|FALSE

// Si la navigation est faite avec des vignettes, et si `$galerieNavigationTatouerVignettes` vaut FALSE, ajout d'une petite flèche à côté des vignettes.
/*
- Au lieu de tatouer les vignettes, ajouter une image à leur côté. Il est possible d'utiliser ses propres images d'accompagnement.
- Voir la documentation pour plus de détails.
*/
$galerieNavigationAccompagnerVignettes = TRUE; // TRUE|FALSE

// Choix de l'emplacement de la navigation.
$galerieNavigationEmplacement = 'haut'; // haut|bas

// Aperçu grâce à des minivignettes du contenu de la galerie sur les pages individuelles de chaque image.
/*
- Il s'agit d'un résumé visuel de la galerie. Chaque image est représentée par une toute petite vignette cliquable.
*/
$galerieAfficherMinivignettes = TRUE; //TRUE|FALSE

// S'il y a des minivignettes, choix de leur emplacement.
$galerieMinivignettesEmplacement = 'haut'; // haut|bas

// S'il y a des minivignettes, le nombre à afficher.
/*
- 0 pour un nombre illimité.
*/
$galerieMinivignettesNombre = 0;

// Ajout d'une ancre de navigation.
/*
- L'ancre est ajoutée dans le lien d'une vignette ou d'une flèche de navigation, et permet de positionner la page à un endroit précis lors de la navigation entre les images d'une galerie.
- Les choix possibles sont:
  - `galerie`: `div` générale de la galerie;
  - `titre`: titre de premier niveau de la page, si ce dernier est généré automatiquement (voir la variable `$galerieGenererTitrePages` dans ce présent fichier de configuration);
  - `sousTitre`: titre de deuxième niveau de la page, si ce dernier est généré automatiquement (voir les variables `$galerieGenererTitrePages` et `$galerieSeparerTitreImageEtNomGalerie` dans ce présent fichier de configuration);
  - `info`: le paragraphe d'information au sujet de la galerie;
  - `minivignettes`;
  - `divImage`: la `div` comprenant l'image en version intermédiaire;
  - `image`: l'image en version intermédiaire.
*/
$galerieAncreDeNavigation = 'titre';

// Ajout automatique d'une légende dans le cas où aucune légende n'a été précisée.
$galerieLegendeAutomatique = TRUE; // TRUE|FALSE

// Utilisation de la syntaxe Markdown dans la légende.
/*
- Active la syntaxe Markdown pour le texte de la légende (valeur du paramètre `intermediaireLegende`).
*/
$galerieLegendeMarkdown = FALSE; // TRUE|FALSE

// Affichage de données Exif pour les fichiers JPG.
/*
- La version de PHP utilisée doit être compilée avec l'option `--enable-exif`. Voir <http://us3.php.net/manual/fr/exif.requirements.php> pour plus de détails. Si ce n'est pas le cas, les données Exif ne seront tout simplement pas affichées.
*/
$galerieExifAjout = TRUE; // TRUE|FALSE

// S'il y a lieu, choix des informations Exif à afficher.
/*
- Chaque élément prend comme valeur TRUE ou FALSE.
*/
$galerieExifDonnees['DateTime']        = TRUE;
$galerieExifDonnees['ExposureTime']    = TRUE;
$galerieExifDonnees['FNumber']         = TRUE;
$galerieExifDonnees['FocalLength']     = TRUE;
$galerieExifDonnees['ISOSpeedRatings'] = TRUE;
$galerieExifDonnees['Make']            = TRUE;
$galerieExifDonnees['Model']           = TRUE;

// Si le format original d'une image existe, emplacement du lien vers le fichier.
/*
- Si l'emplacement `icone` vaut TRUE, une petite icône est ajoutée sous l'image pour signifier que le format original existe.
- Les valeurs possibles pour chaque emplacement sont TRUE ou FALSE.
*/
$galerieLienOriginalEmplacement['image']   = TRUE;
$galerieLienOriginalEmplacement['legende'] = TRUE;
$galerieLienOriginalEmplacement['icone']   = TRUE;

// Si le format original d'une image existe, est-ce que le lien vers le fichier est pris en charge par une fenêtre Javascript (ne fonctionne pas pour le SVG)?
/*
- Cette option n'est pas conseillée pour de grandes images. Voir <http://code.google.com/p/slimbox/wiki/FAQ#Can_Slimbox_automatically_resize_my_images_when_they_are_too_lar> pour plus de détails.
*/
$galerieLienOriginalJavascript = FALSE; // TRUE|FALSE

// Si le format original d'une image existe et que le lien n'est pas pris en charge par une fenêtre Javascript, est-ce que le lien vers le fichier force le téléchargement sans affichage dans le navigateur?
$galerieLienOriginalTelecharger = FALSE; // TRUE|FALSE

// S'il y a lieu, emplacement de la légende.
/*
- La légende comprend les informations Exif et le lien vers l'image originale.
- Les choix possibles sont: haut, bas, bloc.
- Les emplacements `haut` et `bas` font référence à l'image en version intermediaire, alors que `bloc` transforme la légende en bloc positionnable comme n'importe quel autre bloc de contenu à l'aide de la variable `$ordreBlocsDansFluxHtml`.
- Les trois emplacements à préciser sont respectivement lorsqu'il n'y a pas de colonne, lorsqu'il y a une seule colonne et lorsqu'il y en a deux.
*/
$galerieLegendeEmplacement = array ('bas', 'bas', 'bas');

/* ____________________ Syndication de contenu (flux RSS). ____________________ */

// Syndication individuelle par défaut des galeries.
/*
- Note: il est possible de configurer la syndication pour chaque galerie, et ainsi donner une valeur différente de celle par défaut. En effet, si la variable `$rssGalerie` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
*/
$galerieActiverFluxRssParDefaut = TRUE; // TRUE|FALSE

// Syndication globale des galeries (derniers ajouts aux galeries).
/*
- La syndication globale des galeries n'est pas complètement automatique. En effet, il faut maintenir un fichier contenant la liste des galeries et de leur URL.
- Voir la documentation pour plus de détails.
*/
$galerieActiverFluxRssGlobal = TRUE; // TRUE|FALSE

// Auteur par défaut à afficher dans la syndication.
/*
- Si `$galerieFluxRssAuteurEstAuteurParDefaut` vaut TRUE, l'auteur affiché dans les flux RSS pour une image donnée est la valeur de `$auteurParDefaut` (voir ce présent fichier de configuration). Dans tous les cas, si le champ `auteurAjout` est précisé pour l'image donnée dans le fichier de configuration de la galerie en question, c'est la valeur de ce dernier qui est utilisée.
*/
$galerieFluxRssAuteurEstAuteurParDefaut = TRUE; // TRUE|FALSE
?>
