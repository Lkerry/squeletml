<?php
echo '<h2 class="accueilPremierH2">' . T_("Bienvenue sur votre site Squeletml") . "</h2>\n";

echo T_("<p>Le logiciel libre Squeletml est un système de gestion de contenu sans base de données, utilisant un squelette de site valide XHTML 1.0 Strict et géré par inclusion de fichiers PHP, c'est-à-dire un site dont le corps de chaque page se trouve dans un fichier unique et dont les principaux éléments de structuration comme l'en-tête, le bas de page, etc. sont partagés entre toutes les pages. Il a pour but d'optimiser la création et la maintenance de ce genre de site.</p>") . "\n";

echo T_("<p>En effet, seulement deux fichiers doivent être inclus dans chaque page (un au début et un à la fin), peu imorte le type de page (galerie, catégorie, formulaire de contact, etc.), et ces derniers fournissent une structure de site personnalisée et traduite dans la langue de la page (si disponible). Chaque page peut facilement avoir ses propres informations: langue, balise <code>title</code>, métabalises, feuilles de style, scripts Javascript, titre de premier niveau (<code>h1</code>), licence, table des matières, etc. L'en-tête personnalisée permet d'offrir de meilleurs repères aux internautes, d'éviter le contenu dupliqué dans les moteurs de recherche et d'avoir un site optimisé pour le référencement.</p>") . "\n";

echo T_("<p>Aussi, un fichier de configuration permet de paramétrer, par simple affectation de variables, le flux HTML ainsi que plusieurs aspects visuels sans devoir bidouiller dans les feuilles de style CSS, la structure de page XHTML ou la programmation PHP. Bien sûr, il est également possible d'utiliser ses propres styles ou structure de page.</p>") . "\n";

echo T_("<p>Enfin, Squeletml fournit plusieurs modules prêts à l'emploi: formulaires de contact, galeries photo, classement par catégories, flux RSS, fonction «Faire découvrir à des ami-e-s» et liens vers des services de marque-pages et de réseaux sociaux, cron, fichiers Sitemap, interface d'administration reproduisant les principales actions normalement effectuées par FTP, etc. Un site Squeletml peut être géré autant par l'interface graphique de l'administration que par un  simple éditeur de texte. Le tout peut facilement être traduit puisque Squeletml utilise PHP Gettext (et dans une moindre mesure JavaScript Gettext) pour l'affichage de l'interface.</p>") . "\n";

echo T_("<p>Le fonctionnement de Squeletml est indéniablement inspiré de systèmes de gestion de contenu comme <a href=\"http://www.drupalfr.org/\">Drupal</a>.</p>") . "\n";

echo '<p>' . sprintf(T_("Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href=\"%1\$s\">en visitant la documentation</a>."), "$urlRacine/$dossierAdmin/documentation.admin.php") . "</p>\n";
?>