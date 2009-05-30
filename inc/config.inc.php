<?php
########################################################################
##
## Configuration générale
##
########################################################################

// Complément de la balise title selon la langue
$baliseTitleComplement['fr'] = "Site de...";
$baliseTitleComplement['en'] = "Site of...";

// Fichiers inclus par la balise `link` et la balise `script` pour le javascript
/*
Syntaxe: $fichiersLinkScript[] = array ("URL" => "TYPE:fichier à inclure");
Les types possibles sont: css, cssltIE7, cssIE7, javascript, favicon.
Ajouter une étoile à la fin de l'URL pour inclure toutes les pages enfants. Exemples:
$fichiersLinkScript[] = array ("$urlRacine/page.php" => "css:$urlRacine/css/style.css");
$fichiersLinkScript[] = array ("$urlRacine/page.php*" => "css:$urlRacine/css/style.css");
*/
$fichiersLinkScript[] = array ("$urlRacine*" => "css:$urlRacine/css/squeletml.css");

// Version des fichiers précédemment déclarés
/* La version sera ajoutée à la suite du nom des fichiers en tant que variable GET.
Pratique quand un fichier a été modifié et qu'on veut forcer son retéléchargement. */
$versionFichiersLinkScript = 1;

// Inclusion de la feuille de style par défaut de Squeletml (`css/squeletml.css`)
$styleSqueletmlCss = TRUE; // TRUE|FALSE

// Langue par défaut si aucune autre précision n'est apportée. Si la variable $langue existe (par exemple déclarée dans une page), c'est la valeur de cette dernière qui sera utilisée. Voir la fonction langue().
$langue[0] = 'fr';

// Contenu par défaut de la métabalise robots
/* Liste de valeurs possibles: index, follow, archive, noindex, nofollow, noarchive, noodp, noydir */
$robots[0] = "index, follow, archive";

// Encodage
$charset = 'UTF-8';

// Titre du site en en-tête
/* Contenu (balises HTML permises) qui sera inséré comme titre de site dans un h1 s'il s'agit de la page d'accueil, ou dans un p pour toutes les autres pages. */
$titreSite['fr'] = "Titre du site";
$titreSite['en'] = "Website title";

// Message pour IE6
$messageIE6 = TRUE; // TRUE|FALSE

// Inclusion du sur-titre
$surTitre = FALSE; // TRUE|FALSE

// Inclusion du bas de page
$basDePage = TRUE; // TRUE|FALSE

// Position du menu
/* Le menu peut être inséré dans le flux HTML au-dessus du contenu ou bien en-dessous. */
$menuSousLeContenu = TRUE; // TRUE|FALSE

// Le cas échéant, position du menu des langues
/* Le menu des langues peut être inséré dans le flux HTML au-dessus du contenu ou bien en-dessous. */
$menuLanguesSousLeContenu = TRUE; // TRUE|FALSE

// Le cas échéant, l'ordre d'affichage des menus
/* Si le menu ainsi que le menu des langues sont situés dans la même région (par exemple, tous les deux au-dessus du contenu ou tous les deux en-dessous), il est possible de choisir l'ordre dans lequel générer le flux HTML pour ces deux menus. */
$menuSousLeMenuLangues = TRUE; // TRUE|FALSE

########################################################################
##
## Configuration du formulaire de contact
##
########################################################################

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

// Choix de la navigation dans la galerie
$galerieNavigation = 'fleches'; // fleches ou vignettes

// Choix de l'emplacement de la navigation
$galerieNavigationEmplacement = 'haut'; // haut ou bas

// Hauteur des vignettes si génération automatique
$galerieHauteurVignette = 100;

// Si un lien est ajouté vers l'image au format original, est-ce qu'on force son téléchargement sans affichage dans le navigateur?
$galerieTelechargeOrig = FALSE; // TRUE|FALSE

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
