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
  - `524288` équivaut à 500 Kio;
  - `1048576` équivaut à 1 Mio;
  - `2097152` équivaut à 2 Mio;
  - `5242880` équivaut à 5 Mio;
  - `10485760` équivaut à 10 Mio.
*/
$adminTailleCache = '2097152';

/* _______________ En-tête HTML. _______________ */

// Choix du DTD (Définition de Type de Document).
/*
- Voir les explications pour la variable `$xhtmlStrict` dans le fichier de configuration du site.
*/
$adminXhtmlStrict = TRUE; // TRUE|FALSE

// Encodage de l'administration.
$adminCharset = 'UTF-8';

// Contenu par défaut de la métabalise `robots`.
/*
- Voir les explications pour la variable `$robotsParDefaut` dans le fichier de configuration du site.
*/
$adminRobots = 'noindex, nofollow, noarchive';

// Langue par défaut de l'administration.
/*
- Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` existe (par exemple déclarée dans une page) et n'est pas vide, c'est la valeur de cette dernière qui sera utilisée.
- Voir la fonction `langue()`.
*/
$adminLangueParDefaut = 'fr';

// Fichiers inclus dans des balises `link` et `script`.
/*
- Voir les explications pour la variable `$balisesLinkScript` dans le fichier de configuration du site.
*/
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#css#$urlRacineAdmin/css/admin.css";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#css#$urlRacineAdmin/css/extensions-proprietaires.css";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#cssIE7#$urlRacineAdmin/css/ie7.css";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#cssltIE7#$urlRacineAdmin/css/ie6.css";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#css#$urlRacine/css/extensions-proprietaires.css";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#js#$urlRacine/js/phpjs.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#js#$urlRacine/js/squeletml.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#js#$urlRacineAdmin/js/squeletml.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/inc/CodeMirror/js/codemirror.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#favicon#$urlRacine/fichiers/puce.png";
$adminBalisesLinkScript[] = "$urlRacineAdmin/*#jsDirect#ajouteEvenementLoad(function(){lienActif('menu');});";
$adminBalisesLinkScript[] = "$urlRacineAdmin/rss.admin.php#js#$urlRacine/js/jquery.min.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/rss.admin.php#js#$urlRacineAdmin/js/jquery-ui/ui.core.js";
$adminBalisesLinkScript[] = "$urlRacineAdmin/rss.admin.php#js#$urlRacineAdmin/js/jquery-ui/ui.sortable.js";
$jsDirect = <<<JS
	$(function()
	{
		$('ul.triable').sortable();
		$('ul.triable').disableSelection();
	});
JS;
$adminBalisesLinkScript[] = "$urlRacineAdmin/rss.admin.php#jsDirect#$jsDirect";

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

/* _______________ Liste des fichiers et dossiers. _______________ */

// Dossier racine contenant les fichiers (sans / à la fin).
/*
- Le chemin peut être absolu ou bien relatif à partir du dossier racine de l'administration (valeur de `$racineAdmin`).
- Exemple de dossier absolu:
  $adminDossierRacinePorteDocuments = '/var/www/squeletml/site';
- Exemple de dossier relatif:
  $adminDossierRacinePorteDocuments = '../site';
*/
$adminDossierRacinePorteDocuments = '..';

// Filtre des dossiers.
/*
- Il est possible d'appliquer un filtre à la liste de dossiers.
- Pour ne préciser que les dossiers à prendre en compte, mettre 'dossiersInclus'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersExclus'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $adminTypeFiltreDossiers = '';
*/
$adminTypeFiltreDossiers = 'dossiersExclus';

// Préciser les dossiers à prendre en compte dans le filtre.
/*
- Si la variable `$adminTypeFiltreDossiers` est vide, aucun filtre ne sera appliqué.
- Le chemin peut être absolu ou bien relatif à partir du dossier racine de l'administration (valeur de `$racineAdmin`).
- Lister les dossiers en les séparant par une barre verticale | (ne pas mettre d'espace).
- Exemple:
  $adminFiltreDossiers = '../rep|../rep2|../rep3/sous-rep4';
*/
$adminFiltreDossiers = '../.bzr';

/* _______________ Ajout de fichiers. _______________ */

// Taille maximale des fichiers ajoutés (en octets).
$adminTailleMaxFichiers = adminPhpIniOctets(ini_get('upload_max_filesize'));

// Filtre des noms de fichier.
/*
- Le filtre convertit automatiquement les caractères différents de `a-zA-Z0-9.-_+` en tiret, et les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e»).
*/
$adminFiltreNom = FALSE; // TRUE|FALSE

// Filtre du type Mime.
$adminFiltreTypesMime = TRUE; // TRUE|FALSE

// Si `$adminFiltreTypesMime` vaut TRUE, types MIME permis pour les fichiers ajoutés.
/*
- Si `$adminFiltreTypesMime` vaut TRUE et que le tableau `$adminTypesMimePermis` est vide, l'ajout de fichiers par le porte-documents sera désactivé.
*/
$adminTypesMimePermis['gif'] = 'image/gif';
$adminTypesMimePermis['jpeg|jpg|jpe'] = 'image/jpeg';
$adminTypesMimePermis['png'] = 'image/png';
$adminTypesMimePermis['svg|svgz'] = 'image/svg+xml';
$adminTypesMimePermis['bmp'] = 'image/x-ms-bmp';
$adminTypesMimePermis['tiff|tif'] = 'image/tiff';
$adminTypesMimePermis['xcf'] = 'application/x-xcf';
$adminTypesMimePermis['psd'] = 'image/x-photoshop';

$adminTypesMimePermis['html|htm|shtml'] = 'text/html';
$adminTypesMimePermis['xhtml|xht'] = 'application/xhtml+xml';
$adminTypesMimePermis['xml|xsl'] = 'application/xml';
$adminTypesMimePermis['css'] = 'text/css';
$adminTypesMimePermis['asc|txt|text|pot|ini'] = 'text/plain';

$adminTypesMimePermis['odb'] = 'application/vnd.oasis.opendocument.database';
$adminTypesMimePermis['odp'] = 'application/vnd.oasis.opendocument.presentation';
$adminTypesMimePermis['ods'] = 'application/vnd.oasis.opendocument.spreadsheet';
$adminTypesMimePermis['odt'] = 'application/vnd.oasis.opendocument.text';
$adminTypesMimePermis['rtf'] = 'application/rtf';
$adminTypesMimePermis['mdb'] = 'application/msaccess';
$adminTypesMimePermis['ppt|pps'] = 'application/vnd.ms-powerpoint';
$adminTypesMimePermis['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
$adminTypesMimePermis['xls|xlb|xlt'] = 'application/vnd.ms-excel';
$adminTypesMimePermis['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
$adminTypesMimePermis['doc|dot'] = 'application/msword';
$adminTypesMimePermis['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
$adminTypesMimePermis['pdf'] = 'application/pdf';
$adminTypesMimePermis['ps|ai|eps'] = 'application/postscript';

$adminTypesMimePermis['tar'] = 'application/x-tar';
$adminTypesMimePermis['7z'] = 'application/x-7z-compressed';
$adminTypesMimePermis['gtar|tgz|taz'] = 'application/x-gtar';
$adminTypesMimePermis['zip'] = 'application/zip';
$adminTypesMimePermis['rar'] = 'application/rar';

$adminTypesMimePermis['ogg|ogx'] = 'application/ogg';
$adminTypesMimePermis['oga|spx'] = 'audio/ogg';
$adminTypesMimePermis['ogv'] = 'video/ogg';
$adminTypesMimePermis['avi'] = 'video/x-msvideo';
$adminTypesMimePermis['mpga|mpega|mp2|mp3|m4a'] = 'audio/mpeg';
$adminTypesMimePermis['mpeg|mpg|mpe'] = 'video/mpeg';
$adminTypesMimePermis['mp4'] = 'video/mp4';
$adminTypesMimePermis['ra|rm|ram'] = 'audio/x-pn-realaudio';
$adminTypesMimePermis['wma'] = 'audio/x-ms-wma';
$adminTypesMimePermis['wmv'] = 'video/x-ms-wmv';
$adminTypesMimePermis['qt|mov'] = 'video/quicktime';

/* _______________ Actions sur les fichiers. _______________ */

// Actions à activer dans le porte-documents.
/*
- Chaque élément peut valoir TRUE ou FALSE.
*/
$adminPorteDocumentsDroits = array (
	'ajouter' => TRUE,
	'copier' => TRUE,
	'creer' => TRUE,
	'deplacer' => TRUE,
	'editer' => TRUE,
	'permissions' => TRUE,
	'renommer' => TRUE,
	'supprimer' => TRUE,
	'telecharger' => TRUE,
);

// Si `$adminPorteDocumentsDroits['edition']` vaut TRUE, activer la coloration syntaxique en direct durant la saisie dans le `textarea`.
/*
- La coloration s'applique au PHP, HTML, CSS et Javascript (séparément ou entremêlés dans le même fichier).
*/
$adminColorationSyntaxique = TRUE; // TRUE|FALSE
?>
