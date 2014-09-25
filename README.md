## Aperçu

Le logiciel libre Squeletml est un système de gestion de contenu sans base de données, utilisant un squelette de site valide XHTML 1.0 Strict et géré par inclusion de fichiers PHP, c'est-à-dire un site dont le corps de chaque page se trouve dans un fichier unique et dont les principaux éléments de structuration comme l'en-tête, le bas de page, etc. sont partagés entre toutes les pages. Il a pour but d'optimiser la création et la maintenance de ce genre de site.

En effet, seulement deux fichiers doivent être inclus dans chaque page (un au début et un à la fin), peu importe le type de page (galerie, catégorie, formulaire de contact, etc.), et ces derniers fournissent une structure de site personnalisée et traduite dans la langue de la page (si disponible). Chaque page peut facilement avoir ses propres informations: langue, balise `title`, métabalises, feuilles de style, scripts Javascript, titre de premier niveau (`h1`), licence, table des matières, etc. L'en-tête personnalisée permet d'offrir de meilleurs repères aux internautes, d'éviter le contenu dupliqué dans les moteurs de recherche et d'avoir un site optimisé pour le référencement.

Aussi, un fichier de configuration permet de paramétrer, par simple affectation de variables, le flux HTML ainsi que plusieurs aspects visuels sans devoir bidouiller dans les feuilles de style CSS, la structure de page XHTML ou la programmation PHP. Bien sûr, il est également possible d'utiliser ses propres styles ou structure de page.

Enfin, Squeletml fournit plusieurs modules prêts à l'emploi: formulaires de contact, galeries photo, classement par catégories, flux RSS, commentaires avec notification, partage de la page par courriel ou par marque-pages et réseaux sociaux, cron, fichiers Sitemap, interface d'administration reproduisant les principales actions normalement effectuées par FTP, etc. Un site Squeletml peut être géré autant par l'interface graphique de l'administration que par un  simple éditeur de texte. Le tout peut facilement être traduit puisque Squeletml utilise PHP Gettext pour l'affichage de l'interface.

Le fonctionnement de Squeletml est indéniablement inspiré de systèmes de gestion de contenu comme [Drupal](http://www.drupalfr.org/).

## Fonctionnalités et caractéristiques

Voici un aperçu des fonctionnalités et des caractéristiques de Squeletml:

- Sans base de données.

- Requiert Apache et PHP 5, et compatible PHP 5.3.

- Structure valide XHTML 1.0 Strict (si nécessaire, il est possible de choisir la définition de type de document ou tout simplement d'utiliser sa propre structure de page).

- Possibilité d'utiliser la [syntaxe Markdown Extra](http://michelf.com/projets/php-markdown/extra/) pour le texte des pages et d'imbriquer du code PHP dans le Markdown.

- Fichier de configuration offrant une syntaxe simple pour:

  - insérer des fichiers CSS ou Javascipt dans l'en-tête de certaines pages en particulier ou pour toutes les pages du site;
  
  - fusionner les feuilles de style dans un seul fichier, tout comme les scripts Javascript, permettant ainsi de réduire le nombre de requêtes HTTP lors de la visite d'une page;
  
  - choisir diverses structures et styles possibles pour le site: nombre de colonnes et arrière-plan, position des blocs de contenu (comme les menus) dans le flux HTML (et par conséquent leur ordre dans les colonnes), présence ou non de certaines structures comme le bas de page, ajout de blocs de contenu personnalisés, blocs avec couleur de fond et coins arrondis, activation de boîtes déroulantes sur les contenus spécifiés (affichant ou masquant le corps du contenu par simple clic sur le titre, et enregistrant la préférence de l'internaute), etc.;
  
  - configurer finement les galeries et les formulaires de contact.

- Création aisée d'une page: seulement deux fichiers PHP à inclure pour chaque page, un au début et un à la fin, et toujours les deux mêmes fichiers, peu importe la langue ou le type de la page (texte, galerie, catégorie, formulaire de contact, etc.).

- Personnalisation de l'en-tête pour chaque page: balise `title`, métabalises, langue, etc.

- Gestion multilingue, et structure de site (menus, bas de page, etc.) selon la langue de la page.

- Possibilité de générer automatiquement une table des matières pour la page en cours.

- Construction automatique des pages de galerie et de catégorie et du menu des catégories.

- Ajout de formulaires de contact avec options d'antipourriel et de copie à l'expéditeur.

- Module de partage par courriel et par marque-pages et réseaux sociaux.

- Gestion des commentaires:

  - Formulaire d'ajout de commentaires activable ou désactivable page par page.

  - Options relatives à l'affichage des commentaires.

  - Notification par courriel des nouveaux commentaires avec lien de désabonnement.

  - Section d'administration pour la modération des commentaires.

- Syndication de contenu (flux RSS):

  - Par défaut, un flux RSS pour chaque catégorie et chaque galerie, et des flux RSS globaux contenant les dernières publications du site et les dernières images de toutes les galeries.
  
  - Un flux RSS global activable pour chaque langue du site.

- Galeries photo:

  - Nombre illimité de galeries.
  
  - Formats d'image PNG, JPG ou GIF.
  
  - Pagination configurable des vignettes constituant l'accueil de la galerie.
  
  - Choix de la navigation (et de son emplacement) entre chaque image d'une galerie, dont avec des flèches, des vignettes ou une fenêtre Javascript permettant de naviguer d'une image à une autre sans rechargement de page.
    
  - Information personnalisable pour chaque image (légende, attributs de la balise `img`, en-tête HTML, etc.), ou génération automatique, évitant ainsi le contenu dupliqué d'une manière ou d'une autre.
  
  - Syntaxe Markdown Extra en option pour les légendes.
  
  - Affichage par défaut des données Exif des images au format JPG.
  
  - Choix de l'emplacement de la légende et des données Exif (au-dessus ou au-dessous de l'image, ou dans une colonne du site).
  
  - Réordonnement d'une image dans une galerie sans modification de l'URL de cette image.
  
  - Génération automatique des vignettes ou utilisation de vignettes personnalisées.
  
  - Script offert pour redimensionner automatiquement les images originales et ainsi obtenir des copies de taille intermédiaire.
  
  - Ajout d'images par lot contenues dans une archive (`.tar`, `.tar.bz2`, `.tar.gz` ou `.zip`).
  
  - Reconnaissance automatique possible de la version d'une image selon le nom du fichier (par exemple un nom de fichier terminant par `-vignette.extension` pour une vignette).

- Choix possible d'une ou de plusieurs licences pour tout le site ou selon chaque page.

- Mise à jour rapide: la configuration personnalisée d'un site se trouve dans un dossier qui ne sera pas écrasé lors d'une mise à jour de Squeletml.

- Cron permettant d'effectuer automatiquement des tâches d'administration, comme la génération du cache et la construction de fichiers Sitemap.

- Section d'administration offrant des fonctionnalités utiles sans devoir passer par FTP:

  - Fichiers: parcours des dossiers du site, renommage, suppression, création, modification (coloration syntaxique du code en direct à l'aide de [CodeMirror](http://marijn.haverbeke.nl/codemirror/) durant la saisie), ajout, téléchargement...
  
  - Accès: ajout, modification et suppresion d'utilisateurs ayant le droit d'accéder à l'administration; mise en maintenance du site; suppression du cache; lancement du cron; sauvegarde du site.
  
  - Gestion des galeries: listage de toutes les galeries existantes, création et mise à jour du fichier de configuration pour chaque galerie, génération automatique d'images de taille intermédiaire à partir des images originales, ajout et suppression d'images, etc.
  
  - Gestion des catégories, des commentaires, des flux RSS globaux et des fichiers Sitemap.
  
  - Documentation: consultation de l'aide directement en local.

## Documentation

Voir le fichier `doc/documentation.md` dans l'archive du logiciel.

## Téléchargement

[Télécharger l'archive de la dernière version.](https://github.com/jpfleury/squeletml/archive/master.zip)

## Développement

Le logiciel Git est utilisé pour la gestion de versions. [Le dépôt peut être consulté en ligne ou récupéré en local.](https://github.com/jpfleury/squeletml)

## Licence

Auteur: Jean-Philippe Fleury (<http://www.jpfleury.net/contact.php>)  
Copyright © Jean-Philippe Fleury, 2008.

Ce programme est un logiciel libre; vous pouvez le redistribuer ou le
modifier suivant les termes de la GNU Affero General Public License telle que
publiée par la Free Software Foundation: soit la version 3 de cette
licence, soit (à votre gré) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il vous sera utile, mais SANS
AUCUNE GARANTIE: sans même la garantie implicite de COMMERCIALISABILITÉ
ni d'ADÉQUATION À UN OBJECTIF PARTICULIER. Consultez la Licence publique
générale GNU Affero pour plus de détails.

Vous devriez avoir reçu une copie de la Licence publique générale GNU Affero
avec ce programme; si ce n'est pas le cas, consultez
<http://www.gnu.org/licenses/>.

### Matériel tiers

#### Visuel

- **Logo de Squeletml**: le logo a été fait à partir de l'image `clipart/animals/lizard_guillaume_boitel_.svg` de l'[Open Clip Art Library](http://www.openclipart.org/) et de la police de caractères [*Architext*](http://www.dafont.com/FR/architext.font). Les deux sont dans le domaine public.

- **Bannières**: les bannières (80px × 15px) de Squeletml et des licences utilisent la police de caractères [Picopixel](http://www.dafont.com/FR/micropixel.font) de Sebastian Weber. Sur le site de Dafont, la licence est précisée ainsi: «Domaine public / GNU GPL». J'ai écrit à l'auteur pour obtenir des précisions, sans réponse pour l'instant. Les bannières des licences Copyleft utilisent également une image de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Image:Copyleft.svg?uselang=fr) dans le domaine public. La bannière du domaine public utilise également une image de [Wikimedia Commons](http://fr.wikipedia.org/wiki/Fichier:PD-icon.svg) dans le domaine public.

- **Images utilisées dans les galeries**:

  - les images de la galerie démo sont dans le domaine public et proviennent de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Accueil);
  
  - la petite icône proposant la navigation sans fenêtre Javascript provient du [module QuickJava pour Firefox](https://addons.mozilla.org/fr/firefox/addon/1237), sous licence Mozilla Public License version 1.1;
  
  - la petite icône offrant un lien vers le format original d'une image provient du [thème d'icônes Gnome](http://packages.ubuntu.com/jaunty/gnome-icon-theme), sous licence GPL.

- **Icône utilisée dans le lien de déconnexion**: cette icône provient du [thème d'icônes Gnome](http://packages.ubuntu.com/jaunty/gnome-icon-theme), sous licence GPL.

- **Icône utilisée pour les informations de publication**: cette icône est une version modifiée d'une image provenant de l'[Open Clip Art Library](http://www.openclipart.org/) et est dans le domaine public.

- **Icônes utilisées dans le porte-documents**: ces icônes proviennent du [thème d'icônes Gnome](http://packages.ubuntu.com/jaunty/gnome-icon-theme), sous licence GPL.

- **Icône utilisée dans le script de gestion des flux RSS globaux**: cette icône provient du [thème d'icônes Gnome](http://packages.ubuntu.com/jaunty/gnome-icon-theme), sous licence GPL.

- **Logo utilisé pour représenter Firefox dans le module d'affiche d'un message pour Internet Explorer 6**: ce logo est sous triple licence Mozilla Public License version 1.1/GPL version 2 ou toute version ultérieure/LGPL version 2.1 ou toute version ultérieure, et provient de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Image:Deer_Park_Globe.png?uselang=fr).

- **Icône des flux RSS**: cette icône est sous triple licence Mozilla Public License version 1.1/GPL version 2 ou toute version ultérieure/LGPL version 2.1 ou toute version ultérieure, et provient de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Image:Feed-icon.svg?uselang=fr).

- **Image de cône pour la page de maintenance**: cette image provient de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Image:Small-Traffic-Cone-Edited.png?uselang=fr) et est dans le domaine public.

- **Image en cercle dans la page d'erreur 401**: cette image provient de l'[Open Clip Art Library](http://www.openclipart.org/) et est dans le domaine public.

- **Image en triangle utilisée dans la page d'erreur 404 et comme vignette par défaut**: modification d'une image provenant de [Wikimedia Commons](http://commons.wikimedia.org/wiki/Image:Attention_Sign.svg?uselang=fr) et étant dans le domaine public.

#### Code

- [**CodeMirror**](http://codemirror.net/): sous licence BSD. J'ai créé un jeu de couleurs, nommé *gedit*, distribué sous licence AGPL version 3 ou toute version ultérieure.

- [**eZ Components**](http://ezcomponents.org/): sous la nouvelle licence BSD.

- [**\_filter\_htmlcorrector**](http://api.drupal.org/api/function/_filter_htmlcorrector/6): fonction de Drupal, sous licence GPL.

- [**HTML Purifier**](http://htmlpurifier.org/): sous licence LGPL version 2.1 ou toute version ultérieure.

- [**jQuery**](http://jquery.com/): sous double licence MIT et GPL.

- [**jQuery Cookie plugin**](http://plugins.jquery.com/project/cookie): sous double licence MIT et GPL.

- [**jQuery UI**](http://jqueryui.com/): sous double licence MIT et GPL.

- [**PHP gettext**](https://launchpad.net/php-gettext/): sous licence GPL version 2 ou toute version ultérieure.

- [**PHPJS**](http://phpjs.org/): sous double licence MIT et GPL.

- [**PHP Markdown Extra**](http://michelf.com/projets/php-markdown/extra/): sous licence de style BSD.

- [**PHP Simple HTML DOM Parser**](http://simplehtmldom.sourceforge.net/): sous licence MIT.

- [**PIE (Progressive Internet Explorer)**](http://css3pie.com/): sous double licence Apache version 2 ou GPL version 2.

- [**Slimbox 2**](http://www.digitalia.be/software/slimbox2): sous licence MIT.

- [**Table of Contents jQuery Plugin**](http://fuelyourcoding.com/table-of-contents-jquery-plugin/): le script original a été écrit par Doug Neiner et est sous licence MIT. J'ai modifié le script et ai incorporé une fraction de code provenant du [module Pathauto pour Drupal](http://drupal.org/project/pathauto), publié sous licence GPL (sans précision de la version). Je publie le script résultant sous licence GPL version 3 ou toute version ultérieure.

- [**Unsharp Mask for PHP**](http://vikjavev.no/computing/ump.php?id=306): permission écrite de l'auteur Torstein Hønsi d'ajouter «Unsharp Mask for PHP» à mon script sous AGPL.
