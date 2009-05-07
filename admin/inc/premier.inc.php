<?php
// Début des insertions
include_once 'inc/fonctions.inc.php';
foreach (init() as $fichier)
{
	include_once $fichier;
}
// Fin des insertions
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php echo $baliseTitle; ?> | Administration de Squeletml</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow, noarchive" />
<link rel="stylesheet" type="text/css" href="css/admin.css" media="screen" />
</head>
<body>
<div id="page">

<div id="entete">
	<p>Options d'administration:</p>
	<ul>
		<li><a href="index.php">Accueil de l'admin</a> | </li>
		<li><a href="droits.php">Droits d'accès</a> | </li>
		<li><a href="galeries.php">Galeries</a> | </li>
		<li><a href="porte-documents.php">Porte-documents</a> | </li>
		<li><a href="documentation.php">Documentation</a> | </li>
		<li><a href="../index.php">Retour au site</a></li>
	</ul>
</div><!-- /entete -->

<div id="ancres">
	<ul>
		<li><a href="#menu">Aller au menu</a></li>
		<li><a href="#contenu">Aller au contenu</a></li>
	</ul>
</div><!-- /ancres -->

<div id="contenu">
	<div id="interieurContenu">
