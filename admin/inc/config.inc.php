<?php
########################################################################
##
## Général
##
########################################################################

// Langue par défaut de l'administration
/*
- Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` existe (par exemple déclarée dans une page), c'est la valeur de cette dernière qui sera utilisée.
- Voir la fonction `langue()`.
*/
$langueParDefaut = 'fr';

// URL relative de la page de maintenance à partir de `$urlRacine/`
$urlMaintenance = 'maintenance.php';

########################################################################
##
## Porte-documents
##
########################################################################

// Valeur de l'attribut `action` des formulaires
$action = $_SERVER['SCRIPT_NAME'];

// Symbole variable GET
/*
- Si la variable `$action` contient déjà une variable GET, mettre `&amp;` sinon mettre `?`.
*/
$symboleUrl = '?';

/* _______________ Liste des fichiers et dossiers _______________ */

// Dossier racine contenant les fichiers (sans / au début ni à la fin)
$dossierRacine = '..';

// Filtre des dossiers
/*
- Il est possible d'appliquer un filtre à la liste de dossiers.
- Pour ne préciser que les dossiers à lister, mettre 'dossiersPermis'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersExclus'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $typeFiltreDossiers = '';
*/
$typeFiltreDossiers = 'dossiersExclus';

// Préciser les dossiers à prendre en considération dans le filtre
/*
- Si la variable `$typeFiltreDossiers` est vide, aucun filtre ne sera appliqué.
- Lister les dossiers en les séparant par une barre verticale | (ne pas mettre d'espace).
- Exemple:
  $filtreDossiers = 'rep|rep2|rep3/sous-rep4';
*/
$filtreDossiers = '../.bzr|../.bzr/branch|../.bzr/branch-lock|../.bzr/checkout|../.bzr/checkout/lock|../.bzr/branch/lock|../.bzr/repository|../.bzr/repository/indices|../.bzr/repository/lock|../.bzr/repository/obsolete_packs|../.bzr/repository/packs|../.bzr/repository/upload';

/* _______________ Ajout de fichiers _______________ */

// Taille maximale des fichiers téléchargés (en octets)
$tailleMaxFichiers = adminPhpIniOctets(ini_get('upload_max_filesize'));

// Filtre des noms de fichier
/*
- Le filtre convertit automatiquement les caractères différents de `a-zA-Z0-9.-_+` en tiret, et les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e»).
*/
$filtreNom = FALSE; // TRUE|FALSE

// Filtre du type Mime
$filtreTypesMime = FALSE; // TRUE|FALSE

// Détection du type MIME
/*
- La détection du type MIME se fait selon la disponibilité des outils suivants, en ordre de priorité:
  - `Fileinfo` de PHP;
  - commande `file` si la variable `$typeMimeFile` vaut TRUE;
  - tableau personnalisé de correspondance entre une extension et son type MIME si la variable `$typeMimeCorrespondance` n'est pas vide. Exemple: `array ('rmi' => 'audio/midi');`.
  - tableau par défaut de correspondance entre une extension et son type MIME de la fonction `file_get_mimetype()`.
*/
$typeMimeFile = FALSE; // TRUE|FALSE
$typeMimeCheminFile = '/usr/bin/file';
$typeMimeCorrespondance = array ();

// Si `$filtreTypesMime` vaut TRUE, types MIME permis pour les fichiers téléchargés
/*
- Si `$filtreTypesMime` vaut TRUE et que le tableau `$typesMimePermis` est vide, l'ajout de fichiers par le porte-documents sera désactivé.
*/
$typesMimePermis['gif'] = 'image/gif';
$typesMimePermis['jpeg|jpg|jpe'] = 'image/jpeg';
$typesMimePermis['png'] = 'image/png';
$typesMimePermis['svg|svgz'] = 'image/svg+xml';
$typesMimePermis['bmp'] = 'image/x-ms-bmp';
$typesMimePermis['tiff|tif'] = 'image/tiff';
$typesMimePermis['xcf'] = 'application/x-xcf';
$typesMimePermis['psd'] = 'image/x-photoshop';

$typesMimePermis['html|htm|shtml'] = 'text/html';
$typesMimePermis['xhtml|xht'] = 'application/xhtml+xml';
$typesMimePermis['xml|xsl'] = 'application/xml';
$typesMimePermis['css'] = 'text/css';
$typesMimePermis['asc|txt|text|pot'] = 'text/plain';

$typesMimePermis['odb'] = 'application/vnd.oasis.opendocument.database';
$typesMimePermis['odp'] = 'application/vnd.oasis.opendocument.presentation';
$typesMimePermis['ods'] = 'application/vnd.oasis.opendocument.spreadsheet';
$typesMimePermis['odt'] = 'application/vnd.oasis.opendocument.text';
$typesMimePermis['rtf'] = 'application/rtf';
$typesMimePermis['mdb'] = 'application/msaccess';
$typesMimePermis['ppt|pps'] = 'application/vnd.ms-powerpoint';
$typesMimePermis['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
$typesMimePermis['xls|xlb|xlt'] = 'application/vnd.ms-excel';
$typesMimePermis['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
$typesMimePermis['doc|dot'] = 'application/msword';
$typesMimePermis['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
$typesMimePermis['pdf'] = 'application/pdf';
$typesMimePermis['ps|ai|eps'] = 'application/postscript';

$typesMimePermis['tar'] = 'application/x-tar';
$typesMimePermis['7z'] = 'application/x-7z-compressed';
$typesMimePermis['gtar|tgz|taz'] = 'application/x-gtar';
$typesMimePermis['zip'] = 'application/zip';
$typesMimePermis['rar'] = 'application/rar';

$typesMimePermis['ogg|ogx'] = 'application/ogg';
$typesMimePermis['oga|spx'] = 'audio/ogg';
$typesMimePermis['ogv'] = 'video/ogg';
$typesMimePermis['avi'] = 'video/x-msvideo';
$typesMimePermis['mpga|mpega|mp2|mp3|m4a'] = 'audio/mpeg';
$typesMimePermis['mpeg|mpg|mpe'] = 'video/mpeg';
$typesMimePermis['mp4'] = 'video/mp4';
$typesMimePermis['ra|rm|ram'] = 'audio/x-pn-realaudio';
$typesMimePermis['wma'] = 'audio/x-ms-wma';
$typesMimePermis['wmv'] = 'video/x-ms-wmv';
$typesMimePermis['qt|mov'] = 'video/quicktime';

/* _______________ Actions sur les fichiers _______________ */

// Actions à activer dans le porte-documents
/*
- Chaque élément peut valoir TRUE ou FALSE.
*/
$porteDocumentsDroits = array (
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

// Si `$porteDocumentsDroits['edition']` vaut TRUE, activer la coloration syntaxique en direct durant la saisie dans le `textarea`
/*
- La coloration s'applique au PHP, HTML, CSS et Javascript (séparément ou entremêlés dans le même fichier).
*/
$colorationSyntaxique = TRUE; // TRUE|FALSE

?>
