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

// Si la navigation est faite avec des vignettes, ajouter ou non une petite flèche au centre des vignettes (comme une sorte de tatouage numérique)
$galerieNavigationVignettesTatouage = FALSE; // TRUE|FALSE

// Si la navigation est faite avec des vignettes, et si `$galerieNavigationVignettesTatouage = FALSE`, ajouter ou non une petite flèche à côté des vignettes
$galerieNavigationVignettesAccompagnees = TRUE; // TRUE|FALSE

// Choix de l'emplacement de la navigation
$galerieNavigationEmplacement = 'haut'; // haut|bas

// Hauteur des vignettes si génération automatique
$galerieHauteurVignette = 100;

// Si un lien est ajouté vers l'image au format original, est-ce qu'on force son téléchargement sans affichage dans le navigateur?
$galerieTelechargeOrig = FALSE; // TRUE|FALSE

// Pagination des vignettes: nombre de vignettes par page (0 pour désactiver la pagination)
$galerieVignettesParPage = 0;

// S'il y a pagination, affichage des liens au-dessus des vignettes
$galeriePaginationAuDessus = TRUE; // TRUE|FALSE

// S'il y a pagination, affichage des liens au-dessous des vignettes
$galeriePaginationAuDessous = FALSE; // TRUE|FALSE

// Aperçu en minivignettes du contenu de la galerie sur les pages individuelles de chaque oeuvre
$galerieMinivignettes = FALSE; //TRUE|FALSE

// S'il y a des minivignettes, choix de l'emplacement de la div
$galerieMinivignettesEmplacement = 'haut'; // haut|bas

// S'il y a des minivignettes, le nombre à afficher (0 pour un nombre illimité)
$galerieMinivignettesNombre = 0;

// Informations sur la galerie
$galerieInfoAjout = TRUE; // TRUE|FALSE

// S'il y a des informations sur la galerie, choix de l'emplacement de la div
$galerieInfoEmplacement = 'haut'; // haut|bas

// Ajout automatique d'une légende (contenu de l'attribut `alt` + taille du fichier) dans le cas où aucune légende n'a été précisée
$galerieLegendeAutomatique = FALSE; // TRUE|FALSE

// Emplacement de la légende et du lien vers l'image originale (s'il y a lieu)
$galerieLegendeEmplacement = 'bas'; // haut|bas

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
