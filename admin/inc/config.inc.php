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
$adminBalisesLinkScript[2] = "$urlRacineAdmin/*#csslteIE7#$urlRacineAdmin/css/ie6-7.css";
$adminBalisesLinkScript[3] = "$urlRacineAdmin/*#cssltIE7#$urlRacineAdmin/css/ie6.css";
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
$adminBalisesLinkScript[10] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.core.js";
$adminBalisesLinkScript[11] = "$urlRacineAdmin/categories.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.sortable.js";
$adminBalisesLinkScript[12] = "$urlRacineAdmin/categories.admin.php*#jsDirect#$jsDirect";
$adminBalisesLinkScript[13] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[14] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.core.js";
$adminBalisesLinkScript[15] = "$urlRacineAdmin/galeries.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.sortable.js";
$adminBalisesLinkScript[16] = "$urlRacineAdmin/galeries.admin.php*#jsDirect#$jsDirect";
$adminBalisesLinkScript[17] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[18] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.core.js";
$adminBalisesLinkScript[19] = "$urlRacineAdmin/rss.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.sortable.js";
$adminBalisesLinkScript[20] = "$urlRacineAdmin/rss.admin.php*#jsDirect#$jsDirect";
$adminBalisesLinkScript[21] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacine/js/jquery/jquery.min.js";
$adminBalisesLinkScript[22] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.core.js";
$adminBalisesLinkScript[23] = "$urlRacineAdmin/sitemap.admin.php*#js#$urlRacineAdmin/js/jquery-ui/ui.sortable.js";
$adminBalisesLinkScript[24] = "$urlRacineAdmin/sitemap.admin.php*#jsDirect#$jsDirect";

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
$adminFiltreAccesDossiers = '../.bzr';

/* ____________________ Liste des dossiers. ____________________ */

// Affichage des sous-dossiers dans la liste des dossiers.
$adminAfficherSousDossiersDansListe = TRUE; // TRUE|FALSE

// Filtre d'affichage de la liste des dossiers.
/*
- Il est possible d'appliquer un filtre d'affichage de la liste des dossiers. Un dossier dont l'affichage est désactivé est quand même accessible par le porte-documents et téléchargeable; il n'est simplement pas listé par défaut dans la liste des dossiers, ce qui allège l'utilisation de cet outil.
- Pour ne préciser que les dossiers à prendre en compte, mettre 'dossiersAffiches'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersNonAffiches'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $adminTypeFiltreAffichageDansListe = '';
*/
$adminTypeFiltreAffichageDansListe = 'dossiersNonAffiches';

// Dossiers à prendre en compte dans le filtre d'affichage de la liste des dossiers.
/*
- Si la variable `$adminTypeFiltreAffichageDansListe` est vide, aucun filtre ne sera appliqué.
- Voir les explication de la variable `$adminFiltreAccesDossiers` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminFiltreAffichageDansListe = '../admin/inc/pclzip|../admin/inc/tar|../admin/inc/UnsharpMask|../admin/inc/untar|../admin/js/bueditor|../admin/js/CodeMirror|../admin/js/jquery-ui|../admin/js/wz_dragdrop|../fichiers/coins|../fichiers/galeries|../inc/filter_htmlcorrector|../inc/htmlpurifier|../inc/mimedetect|../inc/node_teaser|../inc/pathauto|../inc/php-gettext|../inc/php-markdown|../inc/rolling-curl|../inc/simplehtmldom|../js/Gettext|../js/jquery|../js/phpjs|../js/slimbox2|../locale/en_US|../locale/fr_CA|../modeles/site|../piwik|../scripts|../src';

/* ____________________ Sous-dossiers dans le contenu d'un dossier. ____________________ */

// Lors de l'affichage du contenu d'un dossier, affichage du contenu des sous-dossiers.
$adminAfficherSousDossiersDansContenu = TRUE; // TRUE|FALSE

// Si `$adminAfficherSousDossiersDansContenu` vaut TRUE, filtre d'affichage du contenu des dossiers.
/*
- Il est possible d'appliquer un filtre d'affichage du contenu des dossiers. Un dossier dont l'affichage de son contenu est désactivé est quand même accessible par le porte-documents et téléchargeable; son contenu n'est simplement pas listé par défaut dans le porte-documents.
- Voir les explication de la variable `$adminTypeFiltreAffichageDansListe` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminTypeFiltreAffichageDansContenu = 'dossiersNonAffiches';

// Dossiers à prendre en compte dans le filtre d'affichage du contenu des dossiers.
/*
- Si la variable `$adminTypeFiltreAffichageDansContenu` est vide, aucun filtre ne sera appliqué.
- Voir les explication de la variable `$adminFiltreAccesDossiers` dans le présent fichier de configuration pour la syntaxe à utiliser.
*/
$adminFiltreAffichageDansContenu = '../admin|../css|../doc|../fichiers|../inc|../js|../locale|../modeles|../piwik|../scripts|../site/admin/cache|../site/cache|../site/fichiers/galeries|../src|../xhtml';

/* ____________________ Ajout de fichiers. ____________________ */

// Filtre du type Mime.
$adminFiltreTypesMime = TRUE; // TRUE|FALSE

// Si `$adminFiltreTypesMime` vaut TRUE, types MIME permis pour les fichiers ajoutés.
/*
- Si `$adminFiltreTypesMime` vaut TRUE et que le tableau `$adminTypesMimePermis` est vide, l'ajout de fichiers par le porte-documents sera désactivé.
*/
$adminTypesMimePermis['gif']          = 'image/gif';
$adminTypesMimePermis['jpeg|jpg|jpe'] = 'image/jpeg';
$adminTypesMimePermis['png']          = 'image/png';
$adminTypesMimePermis['svg|svgz']     = 'image/svg+xml';
$adminTypesMimePermis['bmp']          = 'image/x-ms-bmp';
$adminTypesMimePermis['tiff|tif']     = 'image/tiff';
$adminTypesMimePermis['xcf']          = 'application/x-xcf';
$adminTypesMimePermis['psd']          = 'image/x-photoshop';

$adminTypesMimePermis['html|htm|shtml']       = 'text/html';
$adminTypesMimePermis['xhtml|xht']            = 'application/xhtml+xml';
$adminTypesMimePermis['xml|xsl']              = 'application/xml';
$adminTypesMimePermis['css']                  = 'text/css';
$adminTypesMimePermis['asc|txt|text|pot|ini'] = 'text/plain';

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

$adminTypesMimePermis['tar']          = 'application/x-tar';
$adminTypesMimePermis['7z']           = 'application/x-7z-compressed';
$adminTypesMimePermis['gtar|tgz|taz'] = 'application/x-gtar';
$adminTypesMimePermis['zip']          = 'application/zip';
$adminTypesMimePermis['rar']          = 'application/rar';

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

/* ____________________ Actions sur les fichiers. ____________________ */

// Actions à activer dans le porte-documents.
/*
- Chaque élément peut valoir TRUE ou FALSE.
*/
$adminPorteDocumentsDroits['ajouter']              = TRUE;
$adminPorteDocumentsDroits['copier']               = TRUE;
$adminPorteDocumentsDroits['creer']                = TRUE;
$adminPorteDocumentsDroits['deplacer']             = TRUE;
$adminPorteDocumentsDroits['editer']               = TRUE;
$adminPorteDocumentsDroits['modifier-permissions'] = TRUE;
$adminPorteDocumentsDroits['renommer']             = TRUE;
$adminPorteDocumentsDroits['supprimer']            = TRUE;
$adminPorteDocumentsDroits['telecharger']          = TRUE;

// Si `$adminPorteDocumentsDroits['edition']` vaut TRUE, activer une aide lors de l'édition.
/*
- Il y a deux possibilités:
  - activer l'ajout d'une barre de raccourcis de balises HTML à l'aide de [BUEditor](http://ufku.com/drupal/bueditor), qui permet également de visualiser un aperçu du code HTML.
  - activer la coloration en direct du **code** à l'aide de [CodeMirror](http://marijn.haverbeke.nl/codemirror/) durant la saisie dans le `textarea`. La coloration s'applique alors au code PHP, HTML, CSS et Javascript (séparément ou entremêlés dans le même fichier);
- Pour désactiver l'aide, laisser vide, c'est-à-dire:
  $adminAideEdition = '';
*/
$adminAideEdition = 'CodeMirror'; // BUEditor|CodeMirror
?>
