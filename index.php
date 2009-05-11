<?php
if(file_exists('init.inc.php'))
{
	include_once 'init.inc.php';
}
else
{
	include_once 'inc/fonctions.inc.php';
	
	$langueNavigateur = langue('navigateur');
	
	// Nécessaire à la traduction
	phpGettext('.', $langueNavigateur);
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langueNavigateur . '" lang="' . $langueNavigateur . '">' . "\n";
	echo '<head>' . "\n";
	echo '<title>' . T_("Erreur: configuration non accessible") . '</title>' . "\n";
	echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />' . "\n";
	echo '<meta name="robots" content="noindex, nofollow, noarchive" />' . "\n";
	echo '<style type="text/css">' . "\n";
	echo '	.erreur {width: 50%; border: 2px solid red; padding: 10px;}' . "\n";
	echo '</style>' . "\n";
	echo '</head>' . "\n";
	echo '<body>' . "\n";
	
	echo '<p class="erreur">' . sprintf(T_("Erreur: le fichier %1\$s n'existe pas. Ce fichier est nécessaire au fonctionnement de Squeletml. Veuillez copier le fichier %2\$s (situé à la racine du site), le coller sous le nom %1\$s et renseigner les quelques variables y étant contenues."), '<code>init.inc.php</code>', '<code>init.inc.php.defaut</code>') . '</p>' . "\n";
	
	echo '</body>' . "\n";
	echo '</html>';
	
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
