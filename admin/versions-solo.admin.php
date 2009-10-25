<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Version de Squeletml et autres informations</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex, nofollow, noarchive" />
	</head>
	<body>
		<h1>Version de Squeletml et autres informations</h1>
		
		<?php
		$fic = fopen('../version.txt', 'r');
		$tag = fgets($fic, 20); // exemple: logiciel-1.4
		fclose($fic);
		$version = explode('-', $tag);
		$versionSqueletml = trim($version[1]);
		?>
		
		<ul>
			<li>Version de Squeletml: <?php echo $versionSqueletml; ?></li>
			<li>Version de PHP: <?php echo PHP_VERSION; ?></li>
			<li>Serveur: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
			<li>Système d'exploitation: <?php echo PHP_OS; ?></li>
		</ul>
		
		<p><a href="phpinfo.admin.php">Afficher plus d'information.</a></p>
	</body>
</html>
