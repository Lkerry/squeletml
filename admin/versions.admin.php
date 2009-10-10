<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Version de Squeletml et autres informations");
include 'inc/premier.inc.php';
?>

<h1><?php echo T_("Environnement utilisé"); ?></h1>

<ul>
	<li><?php printf(T_('Version de Squeletml: %1$s'), adminVersionLogiciel($racine)); ?></li>
	<li><?php printf(T_('Version de PHP: %1$s'), PHP_VERSION); ?></li>
	<li><?php printf(T_('Serveur: %1$s'), $_SERVER['SERVER_SOFTWARE']); ?></li>
	<li><?php printf(T_('Système d\'exploitation: %1$s'), PHP_OS); ?></li>
</ul>

<p><a href="phpinfo.admin.php"><?php echo T_("Afficher plus d'information."); ?></a></p>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
