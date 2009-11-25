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
$adminLangueParDefaut = 'fr';

// URL relative de la page de maintenance à partir de `$urlRacine/`
$adminUrlMaintenance = 'maintenance.php';

// Taille du dossier de cache en octets
$adminTailleCache = '2097152';

########################################################################
##
## Porte-documents
##
########################################################################

// Valeur de l'attribut `action` des formulaires
$adminAction = $_SERVER['SCRIPT_NAME'];

// Symbole variable GET
/*
- Si la variable `$adminAction` contient déjà une variable GET, mettre `&amp;` sinon mettre `?`.
*/
$adminSymboleUrl = '?';

/* _______________ Liste des fichiers et dossiers _______________ */

// Dossier racine contenant les fichiers (sans / au début ni à la fin)
$adminDossierRacine = '..';

// Filtre des dossiers
/*
- Il est possible d'appliquer un filtre à la liste de dossiers.
- Pour ne préciser que les dossiers à prendre en compte, mettre 'dossiersPermis'
- Pour ne préciser que les dossiers à exclure, mettre 'dossiersExclus'
- Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire:
  $adminTypeFiltreDossiers = '';
*/
$adminTypeFiltreDossiers = 'dossiersExclus';

// Préciser les dossiers à prendre en compte dans le filtre
/*
- Si la variable `$adminTypeFiltreDossiers` est vide, aucun filtre ne sera appliqué.
- Lister les dossiers en les séparant par une barre verticale | (ne pas mettre d'espace).
- Exemple:
  $adminFiltreDossiers = 'rep|rep2|rep3/sous-rep4';
*/
$adminFiltreDossiers = '../.bzr';

/* _______________ Ajout de fichiers _______________ */

// Taille maximale des fichiers téléchargés (en octets)
$adminTailleMaxFichiers = adminPhpIniOctets(ini_get('upload_max_filesize'));

// Filtre des noms de fichier
/*
- Le filtre convertit automatiquement les caractères différents de `a-zA-Z0-9.-_+` en tiret, et les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e»).
*/
$adminFiltreNom = FALSE; // TRUE|FALSE

// Filtre du type Mime
$adminFiltreTypesMime = FALSE; // TRUE|FALSE

// Détection du type MIME
/*
- La détection du type MIME se fait selon la disponibilité des outils suivants, en ordre de priorité:
  - `Fileinfo` de PHP;
  - commande `file` si la variable `$adminTypeMimeFile` vaut TRUE;
  - tableau personnalisé de correspondance entre une extension et son type MIME si la variable `$adminTypeMimeCorrespondance` n'est pas vide. Exemple: `array ('rmi' => 'audio/midi');`.
  - tableau par défaut de correspondance entre une extension et son type MIME de la fonction `file_get_mimetype()`.
*/
$adminTypeMimeFile = FALSE; // TRUE|FALSE
$adminTypeMimeCheminFile = '/usr/bin/file';
$adminTypeMimeCorrespondance = array ();

// Si `$adminFiltreTypesMime` vaut TRUE, types MIME permis pour les fichiers téléchargés
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

/* _______________ Actions sur les fichiers _______________ */

// Actions à activer dans le porte-documents
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

// Si `$adminPorteDocumentsDroits['edition']` vaut TRUE, activer la coloration syntaxique en direct durant la saisie dans le `textarea`
/*
- La coloration s'applique au PHP, HTML, CSS et Javascript (séparément ou entremêlés dans le même fichier).
*/
$adminColorationSyntaxique = TRUE; // TRUE|FALSE

?>
