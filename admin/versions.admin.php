<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Version de Squeletml et autres informations");
include $racineAdmin . '/inc/premier.inc.php';

$versionActuelleSqueletml = adminVersionSqueletml('actuelle', $racine . '/version.txt');
$derniereVersionSqueletml = adminVersionSqueletml('derniere', URL_DERNIERE_VERSION_SQUELETML);
?>

<h1><?php echo T_("Version de Squeletml et autres informations"); ?></h1>

<h2 id="apercu"><?php echo T_("Aperçu"); ?></h2>

<ul>
	<li>
		<?php printf(T_("Version de Squeletml: %1\$s"), $versionActuelleSqueletml); ?>
		
		<?php if ($derniereVersionSqueletml > $versionActuelleSqueletml): ?>
			<?php printf(T_("(<a href=\"%1\$s\">la version %2\$s est disponible</a>)"), URL_SQUELETML, $derniereVersionSqueletml); ?>
		<?php elseif ($derniereVersionSqueletml == $versionActuelleSqueletml): ?>
			<?php echo T_("(il s'agit de la dernière version publiée)"); ?>
		<?php else: ?>
			<?php printf(T_("(impossible de vérifier si une version plus récente a été publiée; <a href=\"%1\$s\">vérifier manuellement</a>)"), URL_SQUELETML); ?>
		<?php endif; ?>
	</li>
	<li><?php printf(T_("Version de PHP: %1\$s"), PHP_VERSION); ?></li>
	<li><?php printf(T_("Version d'Apache: %1\$s"), securiseTexte($_SERVER['SERVER_SOFTWARE'])); ?></li>
	<li><?php echo adminReecritureDurl(TRUE); ?></li>
	<li><?php printf(T_("Système d'exploitation: %1\$s"), PHP_OS); ?></li>
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
