<?php
echo T_("<h2>Bienvenue sur votre site Squeletml</h2>");

echo T_("<p>Le logiciel libre Squeletml est un squelette de site XHTML 1.0 Strict géré par inclusion de fichiers PHP et sans base de données, c'est-à-dire un site dont le corps de chaque page se trouve dans un fichier unique et dont les principaux éléments de structuration comme l'en-tête, le bas de page, etc. sont partagés entre toutes les pages. Il a pour but de réduire l'effort nécessaire pour la création et la maintenance de ce genre de site.</p>");

echo T_("<p>En effet, seulement deux fichiers doivent être inclus dans chaque page (un au début et un à la fin de chaque page), et ces derniers fournissent une structure de site optimisée pour le référencement et traduite dans la langue de la page (si la langue est disponible bien entendu): chaque page peut avoir sa propre balise <code>title</code>, ses propres métabalises, son titre de premier niveau (<code>h1</code>), sa propre langue, etc. L'en-tête personnalisée permet d'éviter le contenu dupliqué dans les moteurs de recherche et d'avoir un site optimisé pour le référencement.</p>");

echo T_("<p>Squeletml fournit également quelques modules prêts à l'emploi, comme des formulaires de contact, des galeries photo, une interface d'administration reproduisant sensiblement les principales actions normalement effectuées par ftp, etc. Le tout peut facilement être traduit puisque Squeletml utilise PHP Gettext pour l'affichage de l'interface.</p>");

echo T_("<p>Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href='admin/documentation.admin.php'>en visitant la documentation</a>.</p>");

?>