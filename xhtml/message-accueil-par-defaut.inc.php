<?php
echo T_("<h2>Bienvenue sur votre site Squeletml</h2>");

echo T_("<h2>Qu'est-ce que Squeletml?</h2>");

echo T_("<p>Le logiciel libre Squeletml est un squelette de site valide XHTML 1.0 Strict géré par inclusion de fichiers PHP et sans base de données, c'est-à-dire un site dont le corps de chaque page se trouve dans un fichier unique et dont les principaux éléments de structuration comme l'en-tête, le bas de page, etc. sont partagés entre toutes les pages. Il a pour but de réduire l'effort nécessaire pour la création et la maintenance de ce genre de site.</p>");

echo T_("<p>En effet, seulement deux fichiers doivent être inclus dans chaque page (un au début et un à la fin), et ces derniers fournissent une structure de site personnalisée et traduite dans la langue de la page (si disponible). Chaque page peut facilement avoir ses propres informations: langue, balise <code>title</code>, métabalises, feuilles de style, scripts Javascript, titre de premier niveau (<code>h1</code>), table des matières, etc. L'en-tête personnalisée permet d'offrir de meilleurs repères aux internautes, d'éviter le contenu dupliqué dans les moteurs de recherche et d'avoir un site optimisé pour le référencement.</p>");

echo T_("<p>Aussi, un fichier de configuration permet de paramétrer, par simple affectation de variables, le flux HTML ainsi que plusieurs aspets visuels sans devoir bidouiller dans les feuilles de style CSS ou la structure de page. Bien sûr, il est également possible d'utiliser ses propres styles ou structure de page.</p>");

echo T_("<p>Enfin, Squeletml fournit quelques modules prêts à l'emploi, comme des formulaires de contact, des galeries photo, des flux RSS, une fonction «Faire découvrir à des ami-e-s», une interface d'administration reproduisant les principales actions normalement effectuées par FTP, etc. Le tout peut facilement être traduit puisque Squeletml utilise PHP Gettext (et dans une moindre mesure JavaScript Gettext) pour l'affichage de l'interface.</p>");

printf(T_("<p>Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href='%1\$s'>en visitant la documentation</a>.</p>"), "$dossierAdmin/documentation.admin.php");?>