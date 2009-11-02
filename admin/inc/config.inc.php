<?php
########################################################################
##
## Général
##
########################################################################

// Langue par défaut de l'administration
/* Langue par défaut si aucune autre précision n'est apportée. Si la variable `$langue` existe (par exemple déclarée dans une page), c'est la valeur de cette dernière qui sera utilisée. Voir la fonction `langue()`. */
$langueParDefaut = 'fr';

// URL relative de la page de maintenance à partir de `$urlRacine/`
$urlMaintenance = 'maintenance.php';

########################################################################
##
## Porte-documents
##
########################################################################

// Taille maximale des fichiers téléchargés (en octets)
$tailleMaxFichiers = adminPhpIniOctets(ini_get('upload_max_filesize'));

// Dossier racine contenant les fichiers (sans / au début ni à la fin)
$dossierRacine = '..';

// Filtre des extensions
$filtreExtensions = FALSE; // TRUE|FALSE

// Si $filtreExtensions vaut FALSE, extensions permises pour les fichiers téléchargés
$extensionsPermises = array ('jpg', 'jpeg', 'png', 'gif', 'svg', 'pdf', 'mdtxt', 'pc', 'txt', 'sxw', 'odt', 'ods', 'rtf', 'doc', 'xls', 'tgz', 'tbz2', 'zip', '7z', 'bz2', 'gz', 'tar', 'ogg', 'oga', 'ogv', 'mp3', 'rm', 'wma', 'mpg', 'mpeg', 'mp4', 'avi', 'mov', 'wmv');

// Filtre des dossiers
/* Il est possible d'appliquer un filtre à la liste de dossiers.
Pour ne préciser que les dossiers à lister, mettre 'dossiersPermis'
Pour ne préciser que les dossiers à exclure, mettre 'dossiersExclus'
Pour ne pas appliquer de filtre, laisser la variable vide, c'est-à-dire $typeFiltreDossiers = ''; */
$typeFiltreDossiers = 'dossiersExclus';

// Préciser les dossiers à prendre en considération dans le filtre
/* Si la variable $typeFiltreDossiers est vide, aucun filtre ne sera appliqué.
Lister les dossiers en les séparant par une barre verticale | (ne pas mettre d'espace).
Exemple: $filtreDossiers = 'rep|rep2|rep3/sous-rep4'; */
$filtreDossiers = '../.bzr|../.bzr/branch|../.bzr/branch-lock|../.bzr/checkout|../.bzr/checkout/lock|../.bzr/branch/lock|../.bzr/repository|../.bzr/repository/indices|../.bzr/repository/lock|../.bzr/repository/obsolete_packs|../.bzr/repository/packs|../.bzr/repository/upload';

// Filtre des noms de fichier
/* Le filtre affiche un message d'erreur si le nom du fichier contient des accents et convertit automatiquement les espaces par des barres de soulignement. */
$filtreNom = FALSE; // TRUE|FALSE

// Affichage des dimensions des images
$afficheDimensionsImages = TRUE; // TRUE|FALSE

// Valeur de l'attribut `action` des formulaires
$action = $_SERVER['SCRIPT_NAME'];

// Symbole variable GET
/* Si la variable `$action` contient déjà une variable GET, mettre &amp; sinon mettre ? */
$symboleUrl = '?';

// Coloration syntaxique en direct durant la saisie dans le textarea. La coloration s'applique au PHP, HTML, CSS et Javascript (séparément ou entremêlés dans le même fichier).
$colorationSyntaxique = TRUE; // TRUE|FALSE

?>
