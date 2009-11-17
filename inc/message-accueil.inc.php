<?php
echo T_("<h2>Bienvenue sur votre site Squeletml</h2>");

echo T_("<h2>Qu'est-ce que Squeletml?</h2>");

echo T_("<p>Le logiciel libre Squeletml est un squelette de site valide XHTML 1.0 Strict géré par inclusion de fichiers PHP et sans base de données, c'est-à-dire un site dont le corps de chaque page se trouve dans un fichier unique et dont les principaux éléments de structuration comme l'en-tête, le bas de page, etc. sont partagés entre toutes les pages. Il a pour but de réduire l'effort nécessaire pour la création et la maintenance de ce genre de site.</p>");

echo T_("<p>En effet, seulement deux fichiers doivent être inclus dans chaque page (un au début et un à la fin), et ces derniers fournissent une structure de site personnalisée et traduite dans la langue de la page (si disponible). Chaque page peut facilement avoir ses propres informations: balise <code>title</code>, métabalises, titre de premier niveau (<code>h1</code>), langue, table des matières, etc. L'en-tête personnalisée permet d'offrir de meilleurs repères aux internautes, d'éviter le contenu dupliqué dans les moteurs de recherche et d'avoir un site optimisé pour le référencement.</p>");

echo T_("<p>Aussi, un fichier de configuration permet de choisir, par simple renseignement de variables, le nombre et l'emplacement des colonnes, la position des menus dans le flux HTML (et par conséquent leur ordre dans les colonnes), la présence ou non de certaines structures comme le bas de page, le style par défaut (blocs de menu avec coins arrondis, arrière-plan des colonnes...), l'activation de boîtes déroulantes sur les contenus spécifiés (affichant ou masquant le corps du contenu par simple clic sur le titre, et enregistrant la préférence de l'internaute), etc.</p>");

echo T_("<p>Enfin, Squeletml fournit quelques modules prêts à l'emploi, comme des formulaires de contact, des galeries photo, des flux RSS, une fonction «Faire découvrir à des ami-e-s», une interface d'administration reproduisant sensiblement les principales actions normalement effectuées par ftp, etc. Le tout peut facilement être traduit puisque Squeletml utilise PHP Gettext (et dans une moindre mesure JavaScript Gettext) pour l'affichage de l'interface.</p>");

echo T_("<p>Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href='admin/documentation.admin.php'>en visitant la documentation</a>.</p>");

?>