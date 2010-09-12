<?php
if (isset($_GET['action']) && $_GET['action'] == 'phpinfo')
{
	phpinfo();
}
else
{
	$langue = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$langue = strtolower(substr(rtrim($langue[0]), 0, 2));
	
	switch ($langue)
	{
		default:
			$titreSite = "Version de Squeletml et autres informations";
			$explicationSite = "Cette page ne nécessite aucune dépendance par rapport à Squeletml. Elle permet donc de récolter facilement certaines informations lorsqu'un bogue empêche le bon fonctionnement du logiciel. <a href=\"versions.admin.php\">Consulter l'accueil de l'administration.</a>";
			$titreApercu = "Aperçu";
			$versionSqueletml = "Version de Squeletml:";
			$versionPhp = "Version de PHP:";
			$versionApache = "Version d'Apache:";
			$reecritureDurlO = "La réécriture d'URL est activée.";
			$reecritureDurlN = "La réécriture d'URL n'est pas activée.";
			$reecritureDurlI = "Impossible de savoir si la réécriture d'URL est activée.";
			$gdO = "La bibliothèque GD est installée.";
			$gdN = "La bibliothèque GD n'est pas installée.";
			$versionSysteme = "Système d'exploitation:";
			$afficherPhpinfo = "Afficher le <code>phpinfo()</code>";
			$afficherFonctions = "Afficher la liste des fonctions internes de PHP";
			$titreFonctions = "Fonctions internes de PHP";
			$nombreFonctions = "Nombre de fonctions internes:";
			$nombreModules = "Nombre de modules:";
			$langue = 'fr';
			break;
	}
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $langue; ?>" lang="<?php echo $langue; ?>">
	<head>
		<title><?php echo $titreSite; ?></title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex, nofollow, noarchive" />
	</head>
	<body>
		<h1><?php echo $titreSite; ?></h1>
		
		<p><?php echo $explicationSite; ?></p>
		
		<h2><?php echo $titreApercu; ?></h2>
		
		<?php
		$version = @file_get_contents('../doc/version.txt');
		
		if ($version !== FALSE)
		{
			$version = explode('-', $version);
			
			if (isset($version[1]))
			{
				$version = trim($version[1]);
			}
			else
			{
				$version = '';
			}
		}
		else
		{
			$version = '';
		}
		?>
		
		<ul>
			<li><?php echo $versionSqueletml; ?> <?php echo $version; ?></li>
			<li><?php echo $versionPhp; ?> <?php echo PHP_VERSION; ?></li>
			<li><?php echo $versionApache; ?> <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'], ENT_COMPAT, 'UTF-8'); ?></li>
			<li>
				<?php if (function_exists('apache_get_modules')): ?>
					<?php if (in_array("mod_rewrite", apache_get_modules())): ?>
						<?php echo $reecritureDurlO; ?>
					<?php else: ?>
						<?php echo $reecritureDurlN; ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo $reecritureDurlI; ?>
				<?php endif; ?>
			</li>
			<li>
				<?php if (function_exists('gd_info')): ?>
					<?php echo $gdO; ?>
				<?php else: ?>
					<?php echo $gdN; ?>
				<?php endif; ?>
			</li>
			<li><?php echo $versionSysteme; ?> <?php echo php_uname(); ?></li>
		</ul>
		
		<ul>
			<li><a href="?action=phpinfo"><?php echo $afficherPhpinfo; ?></a></li>
			<li><a href="?action=fonctions"><?php echo $afficherFonctions; ?></a></li>
		</ul>
		
		<?php if (isset($_GET['action']) && $_GET['action'] == 'fonctions'): ?>
			<h2><?php echo $titreFonctions; ?></h2>
			
			<?php $fonctions = get_defined_functions(); ?>
			<?php $modules = get_loaded_extensions(); ?>
			<?php natcasesort($modules); ?>
			
			<ul>
				<li><?php echo $nombreFonctions; ?> <?php echo count($fonctions['internal']); ?></li>
				<li><?php echo $nombreFonctions; ?> <?php echo count($modules); ?></li>
			</ul>
			
			<ul>
				<?php foreach($modules as $module): ?>
					<li><h3><?php echo $module; ?></h3>
					<?php $fonctionsModule = get_extension_funcs($module); ?>
					
					<?php if ($fonctionsModule): ?>
						<?php natcasesort($fonctionsModule); ?>
						<ul>
							<?php foreach($fonctionsModule as $fonctionModule): ?>
								<li><?php echo $fonctionModule; ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</body>
</html><?php
}
?>
