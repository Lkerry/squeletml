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
$fichiersLinkScript[] = array ("$squeletmlAccueil/page.php" => "css:$squeletmlAccueil/css/style.css");
$fichiersLinkScript[] = array ("$squeletmlAccueil/page.php*" => "css:$squeletmlAccueil/css/style.css");
*/
$fichiersLinkScript[] = array ("$squeletmlAccueil*" => "css:$squeletmlAccueil/css/squeletml.css");

// Version des fichiers précédemment déclarés
/* La version sera ajoutée à la suite du nom des fichiers en tant que variable GET.
Pratique quand un fichier a été modifié et qu'on veut forcer son retéléchargement. */
$versionFichiersLinkScript = 1;

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

// Inclusion du bas de page
$basDePage = TRUE; // TRUE|FALSE

// Position du menu
/* Le menu peut être inséré dans le flux HTML au-dessus du contenu ou bien en-dessous. */
$menuSousLeContenu = TRUE; // TRUE|FALSE

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

// Courriel qui va recevoir les messages
$courrielContact = "courriel@site.ext";

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

########################################################################
##
## Ne pas modifier ce qui suit
##
########################################################################

// DOCUMENT_ROOT n'a pas toujours la bonne valeur selon les serveurs
$_SERVER['DOCUMENT_ROOT'] = $racine;

?>
