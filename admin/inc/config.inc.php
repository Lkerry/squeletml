<?php
########################################################################
##
## Configuration générale.
##
########################################################################

// URL relative de la page de maintenance à partir de `$urlRacine/`.
$adminUrlMaintenance = 'maintenance.php';

// Taille en octets du dossier de cache.
/*
- Exemples:
  - `102400` équivaut à 100 Kio;
  - `512000` équivaut à 500 Kio;
  - `1048576` équivaut à 1 Mio;
  - `2097152` équivaut à 2 Mio;
  - `5242880` équivaut à 5 Mio;
  - `10485760` équivaut à 10 Mio;
  - `26214400` équivaut à 25 Mio;
  - `52428800` équivaut à 50 Mio;
*/
$adminTailleCache = '5242880';

// Activation de boîtes déroulantes par défaut.
/*
- Voir les explications de la variable `$boitesDeroulantesParDefaut` dans le fichier de configuration du site.
*/
$adminBoitesDeroulantesParDefaut = '';

// Activation de boîtes déroulantes à la main par défaut.
/*
- Voir les explications de la variable `$boitesDeroulantesAlaMainParDefaut` dans le fichier de configuration du site.
*/
$adminBoitesDeroulantesAlaMainParDefaut = TRUE; // TRUE|FALSE

// Activation de l'infobulle.
/*
- L'infobulle apparaît lors du survol du curseur au-dessus de l'icône des propriétés d'un dossier ou d'un fichier, et contient plusieurs informations sur ce dernier.
- Note: lors du listage de dossiers contenant beaucoup de fichiers, cette option peut ralentir considérablement l'affichage de la page. C'est la raison pour laquelle il peut être intéressant de la désactiver.
*/
$adminActiverInfobulle['contenuDossier']   = FALSE; // TRUE|FALSE
$adminActiverInfobulle['listeDesDossiers'] = FALSE; // TRUE|FALSE
$adminActiverInfobulle['apercuGalerie']    = TRUE; // TRUE|FALSE

// Inclusion du bas de page.
$adminInclureBasDePage = TRUE; // TRUE|FALSE

/* ____________________ En-tête HTML. ____________________ */

// Choix du DTD (Définition de Type de Document).
/*
- Voir les explications de la variable `$doctype` dans le fichier de configuration du site.
*/
$adminDoctype = 'XHTML 1.0 Strict';

// Encodage de l'administration.
$adminCharset = 'UTF-8';

// Contenu par défaut de la métabalise `robots`.
/*
- Voir les explications de la variable `$robotsParDefaut` dans le fichier de configuration du site.
*/
$adminRobots = 'noindex, nofollow, noarchive';

// Langue par défaut de l'administration.
/*
- Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` est déclarée dans une page, c'est la valeur de cette dernière qui est utilisée.
- Voir la fonction `langue()`.
*/
$adminLangueParDefaut = 'fr';

// Fichiers inclus dans des balises `link` et `script`.
/*
- Voir les explications de la variable `$balisesLinkScript` dans le fichier de configuration du site.
*/
$adminBalisesLinkScript[0] = "$urlRacineAdmin/*#css#$urlRacineAdmin/css/admin.css";
$adminBalisesLinkScript[2] = "$urlRacineAdmin/*#csslteIE8#$urlRacineAdmin/css/ie6-7-8.css";
$adminBalisesLinkScript[3] = "$urlRacineAdmin/*#csslteIE7#$urlRacineAdmin/css/ie6-7.css";
$adminBalisesLinkScript[4] = "$urlRacineAdmin/*#cssltIE7#$urlRacineAdmin/css/ie6.css";
$adminBalisesLinkScript[5] = "$urlRacineAdmin/*#js#$urlRacine/js/phpjs/php.min.js";
$adminBalisesLinkScript[6] = "$urlRacineAdmin/*#js#$urlRacine/js/squeletml.js";
$adminBalisesLinkScript[7] = "$urlRacineAdmin/*#js#$urlRacineAdmin/js/squeletml.js";
$adminBalisesLinkScript[8] = "$urlRacineAdmin/*#favicon#$urlRacine/fichiers/favicon.png";

$jsDirect = <<<JS
	$(function()
	{
		$('ul.triable').sortable();
	});
JS;

$adminBalisesLinkScript[9] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[10] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.core.min.js";
$adminBalisesLinkScript[11] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.widget.min.js";
$adminBalisesLinkScript[12] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.mouse.min.js";
$adminBalisesLinkScript[13] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.sortable.min.js";
$adminBalisesLinkScript[14] = "$urlRacineAdmin/categories.admin.php*#jsDirect#$jsDirect";

$adminBalisesLinkScript[15] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[16] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.core.min.js";
$adminBalisesLinkScript[17] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.widget.min.js";
$adminBalisesLinkScript[18] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.mouse.min.js";
$adminBalisesLinkScript[19] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.sortable.min.js";
$adminBalisesLinkScript[20] = "$urlRacineAdmin/galeries.admin.php*#jsDirect#$jsDirect";

$adminBalisesLinkScript[21] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[22] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.core.min.js";
$adminBalisesLinkScript[23] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.widget.min.js";
$adminBalisesLinkScript[24] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.mouse.min.js";
$adminBalisesLinkScript[25] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.sortable.min.js";
$adminBalisesLinkScript[26] = "$urlRacineAdmin/rss.admin.php*#jsDirect#$jsDirect";

$adminBalisesLinkScript[27] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[28] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.core.min.js";
$adminBalisesLinkScript[29] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.widget.min.js";
$adminBalisesLinkScript[30] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.mouse.min.js";
$adminBalisesLinkScript[31] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/jquery.ui.sortable.min.js";
$adminBalisesLinkScript[32] = "$urlRacineAdmin/sitemap.admin.php*#jsDirect#$jsDirect";

// Fusion des fichiers CSS et des scripts Javascript.
/*
- Voir les explications de la variable `$fusionnerCssJs` dans le fichier de configuration du site.
*/
$adminFusionnerCssJs = FALSE; // TRUE|FALSE

########################################################################
##
## Catégories.
##
########################################################################

// S'il y a lieu, inclure une page dans la catégorie parente et dans les catégories parentes indirectes.
/*
Explications: par exemple, une page est ajoutée à la catégorie «Miniatures». Cette catégorie a comme parent «Chiens», qui a elle-même comme parent la catégorie «Animaux». Si l'option d'ajout dans la catégorie parente est activée, la page sera ajoutée dans la catégorie «Miniatures» et dans la catégorie parente «Chiens». Aussi, si l'option d'ajout dans les catégories parentes indirectes est activée, la page sera également ajoutée à la catégorie «Animaux».
*/
$adminInclurePageDansCategorieParente = TRUE;
$adminInclurePageDansCategoriesParentesIndirectes = TRUE;

########################################################################
##
## Galeries.
##
########################################################################

// Commandes pour la rotation automatique et sans perte de qualité des images JPG.
/*
- Pour utiliser `exiftran`, mettre son chemin d'accès sur la machine.
- Pour utiliser `jpegtran`, mettre son chemin d'accès sur la machine et s'assurer que la fonction PHP `exif_read_data()` est utilisable.
- Voir les commentaires de la fonction `adminRotationJpegSansPerte()` pour plus de détails.
*/
$adminCheminExiftran = '/usr/bin/exiftran';
$adminCheminJpegtran = '/usr/bin/jpegtran';

// Taille par défaut lors d'un redimensionnement.
$adminTailleParDefautRedimensionnement['largeur'] = 500;
$adminTailleParDefautRedimensionnement['hauteur'] = 500;

########################################################################
##
## Porte-documents.
##
########################################################################

// Valeur de l'attribut `action` des formulaires.
$adminAction = $_SERVER['SCRIPT_NAME'];

// Symbole variable GET.
/*
- Si la variable `$adminAction` contient déjà une variable GET, mettre `&amp;`, sinon mettre `?`.
*/
$adminSymboleUrl = '?';

// Dossier racine contenant les fichiers (sans / à la fin).
/*
- Le chemin peut être absolu ou bien relatif à partir du dossier racine de l'administration (valeur de `$racineAdmin`).
- Exemple de dossier absolu:
  $adminDossierRacinePorteDocuments = '/var/www/squeletml/site';
- Exemple de dossier relatif:
  $adminDossierRacinePorteDocuments = '../site';
*/
$adminDossierRacinePorteDocuments = '..';

/* ____________________ Accès aux dossiers. ____________________ */

// Filtre d'accès aux dossiers.
/*
- Il est possible d'appliquer un filtre d'accès aux dossiers du site. Un dossier inaccessible n'apparaît pas dans la liste des dossiers ni dans le contenu d'un dossier, et ne peut être géré par le porte-documents ni téléchargé par l'administration.
- Pour ne préciser que les dossiers à prendre en compte, mettre 'dossiersInclus'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersExclus'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $adminTypeFiltreAccesDossiers = '';
*/
$adminTypeFiltreAccesDossiers = 'dossiersExclus';

// Dossiers à prendre en compte dans le filtre d'accès aux dossiers.
/*
- Si la variable `$adminTypeFiltreAccesDossiers` est vide, aucun filtre ne sera appliqué.
- Le chemin peut être absolu ou bien relatif à partir du dossier racine de l'administration (valeur de `$racineAdmin`).
- Lister les dossiers en les séparant par une barre verticale | (ne pas mettre d'espace).
- Exemple:
  $adminFiltreAccesDossiers = '../rep|../rep2|../rep3/sous-rep4';
*/
$adminFiltreAccesDossiers = '../.git';

/* ____________________ Liste des dossiers. ____________________ */

// Affichage des sous-dossiers dans la liste des dossiers.
$adminAfficherSousDossiersDansListe = FALSE; // TRUE|FALSE

// Filtre d'affichage de la liste des dossiers.
/*
- Il est possible d'appliquer un filtre d'affichage de la liste des dossiers. Un dossier dont l'affichage est désactivé est quand même accessible par le porte-documents et téléchargeable; il n'est simplement pas listé par défaut dans la liste des dossiers, ce qui allège l'utilisation de cet outil.
- Pour ne préciser que les dossiers à prendre en compte, mettre 'dossiersAffiches'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersNonAffiches'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $adminTypeFiltreAffichageDansListe = '';
*/
$adminTypeFiltreAffichageDansListe = '';

// Dossiers à prendre en compte dans le filtre d'affichage de la liste des dossiers.
/*
- Si la variable `$adminTypeFiltreAffichageDansListe` est vide, aucun filtre ne sera appliqué.
- Voir les explication de la variable `$adminFiltreAccesDossiers` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminFiltreAffichageDansListe = '';

/* ____________________ Sous-dossiers dans le contenu d'un dossier. ____________________ */

// Lors de l'affichage du contenu d'un dossier, affichage du contenu des sous-dossiers.
$adminAfficherSousDossiersDansContenu = FALSE; // TRUE|FALSE

// Si `$adminAfficherSousDossiersDansContenu` vaut TRUE, filtre d'affichage du contenu des dossiers.
/*
- Il est possible d'appliquer un filtre d'affichage du contenu des dossiers. Un dossier dont l'affichage de son contenu est désactivé est quand même accessible par le porte-documents et téléchargeable; son contenu n'est simplement pas listé par défaut dans le porte-documents.
- Voir les explication de la variable `$adminTypeFiltreAffichageDansListe` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminTypeFiltreAffichageDansContenu = '';

// Dossiers à prendre en compte dans le filtre d'affichage du contenu des dossiers.
/*
- Si la variable `$adminTypeFiltreAffichageDansContenu` est vide, aucun filtre ne sera appliqué.
- Voir les explication de la variable `$adminFiltreAccesDossiers` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminFiltreAffichageDansContenu = '';

/* ____________________ Ajout, création ou édition de fichiers. ____________________ */

// Filtre du type Mime.
$adminFiltreTypesMime = TRUE; // TRUE|FALSE

// Si `$adminFiltreTypesMime` vaut TRUE, types MIME permis pour les fichiers ajoutés.
/*
- Si `$adminFiltreTypesMime` vaut TRUE et que le tableau `$adminTypesMimePermis` est vide, l'ajout de fichiers par le porte-documents sera désactivé.
*/
$adminTypesMimePermis['vide'] = 'application/x-empty';

$adminTypesMimePermis['dossier'] = 'directory';

$adminTypesMimePermis['gif']          = 'image/gif';
$adminTypesMimePermis['jpeg|jpg|jpe'] = 'image/jpeg';
$adminTypesMimePermis['png']          = 'image/png';
$adminTypesMimePermis['svg|svgz']     = 'image/svg+xml';
$adminTypesMimePermis['bmp']          = 'image/x-ms-bmp';
$adminTypesMimePermis['tiff|tif']     = 'image/tiff';
$adminTypesMimePermis['xcf']          = 'application/x-xcf';
$adminTypesMimePermis['psd']          = 'image/x-photoshop';

$adminTypesMimePermis['html|htm|shtml']          = 'text/html';
$adminTypesMimePermis['xhtml|xht']               = 'application/xhtml+xml';
$adminTypesMimePermis['xml|xsl']                 = 'application/xml';
$adminTypesMimePermis['css']                     = 'text/css';
$adminTypesMimePermis['asc|txt|text|po|pot|ini'] = 'text/plain';
$adminTypesMimePermis['markdown|md|mkd']         = 'text/x-markdown';

$adminTypesMimePermis['odb']         = 'application/vnd.oasis.opendocument.database';
$adminTypesMimePermis['odp']         = 'application/vnd.oasis.opendocument.presentation';
$adminTypesMimePermis['ods']         = 'application/vnd.oasis.opendocument.spreadsheet';
$adminTypesMimePermis['odt']         = 'application/vnd.oasis.opendocument.text';
$adminTypesMimePermis['rtf']         = 'application/rtf';
$adminTypesMimePermis['mdb']         = 'application/msaccess';
$adminTypesMimePermis['ppt|pps']     = 'application/vnd.ms-powerpoint';
$adminTypesMimePermis['pptx']        = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
$adminTypesMimePermis['xls|xlb|xlt'] = 'application/vnd.ms-excel';
$adminTypesMimePermis['xlsx']        = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
$adminTypesMimePermis['doc|dot']     = 'application/msword';
$adminTypesMimePermis['docx']        = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
$adminTypesMimePermis['pdf']         = 'application/pdf';
$adminTypesMimePermis['ps|ai|eps']   = 'application/postscript';

$adminTypesMimePermis['tar']      = 'application/x-tar';
$adminTypesMimePermis['7z']       = 'application/x-7z-compressed';
$adminTypesMimePermis['bz2|tbz2'] = 'application/x-bzip2';
$adminTypesMimePermis['gz|tgz']   = 'application/x-gzip';
$adminTypesMimePermis['zip']      = 'application/zip';
$adminTypesMimePermis['rar']      = 'application/rar';

$adminTypesMimePermis['ogg|ogx']                = 'application/ogg';
$adminTypesMimePermis['oga|spx']                = 'audio/ogg';
$adminTypesMimePermis['ogv']                    = 'video/ogg';
$adminTypesMimePermis['avi']                    = 'video/x-msvideo';
$adminTypesMimePermis['mpga|mpega|mp2|mp3|m4a'] = 'audio/mpeg';
$adminTypesMimePermis['mpeg|mpg|mpe']           = 'video/mpeg';
$adminTypesMimePermis['mp4']                    = 'video/mp4';
$adminTypesMimePermis['ra|rm|ram']              = 'audio/x-pn-realaudio';
$adminTypesMimePermis['wma']                    = 'audio/x-ms-wma';
$adminTypesMimePermis['wmv']                    = 'video/x-ms-wmv';
$adminTypesMimePermis['qt|mov']                 = 'video/quicktime';

// Coloration syntaxique lors de l'édition.
/*
Activer la coloration en direct du **code** à l'aide de [CodeMirror](http://codemirror.net/) durant la saisie dans le `textarea`. La coloration s'applique alors au code PHP, HTML, CSS, Javascript, XML, Markdown et INI.
*/
$adminColorationSyntaxique = TRUE; // TRUE|FALSE

// Corps du modèle de page Web créé dans le porte-documents.
$adminCorpsModelePageWeb = '<?php
$chaine = <<<TEXTE
Du texte écrit en *Markdown*.

Autre **paragraphe**.
TEXTE;

echo mkdChaine($chaine);
?>

<p>Lorem ipsum dolor sit amet.</p>';
?>
