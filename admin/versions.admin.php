<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Version de Squeletml et autres informations");
include 'inc/premier.inc.php';
?>

<h1><?php echo T_("Version de Squeletml et autres informations"); ?></h1>

<h2><?php echo T_("Aperçu"); ?></h2>

<ul>
	<li><?php printf(T_("Version de Squeletml: %1\$s"), adminVersionLogiciel($racine)); ?></li>
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
	<h2><?php echo T_("Fonctions internes de PHP"); ?></h2>
	
	<?php $fonctions = get_defined_functions(); ?>
	<?php $modules = get_loaded_extensions(); ?>
	<p><?php printf(T_("Il y a %1\$s fonctions internes réparties entre %2\$s modules. En voici la liste:"), count($fonctions['internal']), count($modules)); ?></p>
	
	<ul>
		<?php foreach($modules as $module): ?>
			<li><h3><?php echo $module; ?></h3>
			<ul>
				<?php $fonctionsModule = get_extension_funcs($module); ?>
				<?php foreach($fonctionsModule as $fonctionModule): ?>
					<li><?php echo $fonctionModule; ?></li>
				<?php endforeach; ?>
			</ul></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
