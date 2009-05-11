<?php
if(file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
}
else
{
	include_once 'inc/fonctions.inc.php';
	// Nécessaire à la traduction
	phpGettext('.', langue('navigateur'));
	
	echo '<p style="width: 50%; border: 2px solid red; padding: 10px;">' . sprintf(T_("Erreur: le fichier %1\$s n'existe pas. Ce fichier est nécessaire au fonctionnement de Squeletml. Veuillez copier le fichier %2\$s (situé à la racine du site), le coller sous le nom %1\$s et renseigner les quelques variables y étant contenues."), '<code>init.inc.php</code>', '<code>init.inc.php.defaut</code>') . '</p>';
	
	exit(1);
}

if (file_exists($racine . '/site/inc/page.fr.index.inc.php'))
{
	include $racine . '/site/inc/page.fr.index.inc.php';
}
else
{
	include $racine . '/inc/page.fr.index.inc.php';
}

?>
