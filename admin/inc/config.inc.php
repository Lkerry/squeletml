<?php
########################################################################
##
## Configuration générale
##
########################################################################


########################################################################
##
## Porte-documents
##
########################################################################

// Taille maximale des fichiers téléchargés (en octets)
$tailleMaxFichiers = 5000000;

// Dossier racine contenant les fichiers (sans / au début ni à la fin)
$dossierRacine = '..';

// Filtre des extensions
$filtreExtensions = FALSE; // TRUE|FALSE

//Extensions permises pour les fichiers téléchargés (si $filtreExtensions vaut FALSE, aucun filtre ne sera appliqué)
$extensionsPermises = array (
	'jpg', 'jpeg', 'png', 'gif', 'svg', 'pdf', 'txt', 'sxw', 'odt', 'ods', 'rtf', 
	'doc', 'xls', 'tgz', 'tbz2', 'zip', '7z', 'bz2', 'gz', 'tar', 'ogg', 'mp3', 'rm', 'wma', 
	'mpg', 'mpeg', 'mp4', 'avi', 'mov', 'wmv'
	);

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

// Affiche les dimensions des images
$afficheDimensionsImages = TRUE; // TRUE|FALSE

// Valeur de l'attribut `action` des formulaires
$action = $_SERVER['PHP_SELF'];

// Symbole variable GET
/* Si la variable $action contient déjà une variable GET, mettre & sinon mettre ? */
$symboleUrl = '?';

// Niveau du titre principal en HTML du script (écrire seulement un chiffre de 1 à 4)
$niveauTitreScript = 1;

?>