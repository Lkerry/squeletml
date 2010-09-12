<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Version de Squeletml et autres informations");
include $racineAdmin . '/inc/premier.inc.php';

$contenuLiVersion = '';
$versionActuelleSqueletml = @file_get_contents($racine . '/doc/version.txt');
$versionActuelleSqueletml = trim($versionActuelleSqueletml);

if (!empty($versionActuelleSqueletml))
{
	$contenuLiVersion .= sprintf(T_("Version de Squeletml: %1\$s"), $versionActuelleSqueletml) . "\n";
	$tableauVersionActuelleSqueletml = explode('.', $versionActuelleSqueletml);
	
	if (!isset($tableauVersionActuelleSqueletml[2]))
	{
		$tableauVersionActuelleSqueletml[2] = 0;
	}

	$derniereVersionSqueletml = contenuUrl(URL_DERNIERE_VERSION_SQUELETML);
	$derniereVersionSqueletml = trim($derniereVersionSqueletml);

	if (!empty($derniereVersionSqueletml))
	{
		$tableauDerniereVersionSqueletml = explode('.', $derniereVersionSqueletml);
		
		if (!isset($tableauDerniereVersionSqueletml[2]))
		{
			$tableauDerniereVersionSqueletml[2] = 0;
		}
		
		if (($tableauDerniereVersionSqueletml[0] > $tableauVersionActuelleSqueletml[0]) || ($tableauDerniereVersionSqueletml[1] > $tableauVersionActuelleSqueletml[1]) || ($tableauDerniereVersionSqueletml[2] > $tableauVersionActuelleSqueletml[2]))
		{
			$contenuLiVersion .= sprintf(T_("(<a href=\"%1\$s\">la version %2\$s est disponible</a>)"), URL_TELECHARGEMENT_SQUELETML, $derniereVersionSqueletml);
		}
		elseif ($derniereVersionSqueletml == $versionActuelleSqueletml)
		{
			$contenuLiVersion .= T_("(il s'agit de la dernière version publiée)");
		}
		else
		{
			$contenuLiVersion .= sprintf(T_("(impossible de vérifier si une version plus récente a été publiée; <a href=\"%1\$s\">vérifier manuellement</a>)"), URL_SQUELETML);
		}
	}
	else
	{
		$contenuLiVersion .= sprintf(T_("(impossible de vérifier si une version plus récente a été publiée; <a href=\"%1\$s\">vérifier manuellement</a>)"), URL_SQUELETML);
	}
}
else
{
	$contenuLiVersion .= sprintf(T_("Version de Squeletml: %1\$s"), T_("impossible de déterminer la version"));
}

if (gdEstInstallee())
{
	$contenuLiGd = T_("La bibliothèque GD est installée.");
}
else
{
	$contenuLiGd = T_("La bibliothèque GD n'est pas installée.");
}
?>

<h1><?php echo T_("Version de Squeletml et autres informations"); ?></h1>

<h2 id="apercu"><?php echo T_("Aperçu"); ?></h2>

<ul>
	<li><?php echo $contenuLiVersion; ?></li>
	<li><?php printf(T_("Version de PHP: %1\$s"), PHP_VERSION); ?></li>
	<li><?php printf(T_("Version d'Apache: %1\$s"), securiseTexte($_SERVER['SERVER_SOFTWARE'])); ?></li>
	<li><?php echo adminReecritureDurl(TRUE); ?></li>
	<li><?php echo $contenuLiGd; ?></li>
	<li><?php printf(T_("Système d'exploitation: %1\$s"), php_uname()); ?></li>
</ul>

<ul>
			<li><a href="versions-solo.admin.php?action=phpinfo"><?php echo T_("Afficher le <code>phpinfo()</code>"); ?></a></li>
			<li><a href="versions.admin.php?action=fonctions"><?php echo T_("Afficher la liste des fonctions internes de PHP"); ?></a></li>
</ul>

<?php if (isset($_GET['action']) && $_GET['action'] == 'fonctions'): ?>
	<h2 id="fonctions"><?php echo T_("Fonctions internes de PHP"); ?></h2>
	
	<?php $fonctions = get_defined_functions(); ?>
	<?php $modules = get_loaded_extensions(); ?>
	<?php natcasesort($modules); ?>
	
	<p><?php printf(T_("Il y a %1\$s fonctions internes réparties entre %2\$s modules. En voici la liste:"), count($fonctions['internal']), count($modules)); ?></p>
	
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

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
