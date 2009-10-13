<?php
if(file_exists('init.inc.php') && file_exists('.acces'))
{
	include_once 'init.inc.php';
	
	if (file_exists($racine . '/site/inc/page.fr.index.inc.php'))
	{
		include $racine . '/site/inc/page.fr.index.inc.php';
	}
	else
	{
		include $racine . '/inc/page.fr.index.inc.php';
	}
}
else
{
	include_once 'inc/fonctions.inc.php';
	$langueNavigateur = langue($langueParDefaut, 'navigateur');
	// Nécessaire à la traduction
	phpGettext('.', $langueNavigateur);
	
	$nbreErreurs = 2;
	if(file_exists('init.inc.php') || file_exists('.acces'))
	{
		$nbreErreurs = 1;
	}
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langueNavigateur . '" lang="' . $langueNavigateur . '">' . "\n";
	echo '<head>' . "\n";
	echo '<title>' . T_("Squeletml: actions à accomplir") . '</title>' . "\n";
	echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />' . "\n";
	echo '<meta name="robots" content="noindex, nofollow, noarchive" />' . "\n";
	echo '<style type="text/css">' . "\n";
	echo '	.erreur {width: 50%; border: 2px solid red; padding: 10px;}' . "\n";
	echo '	.erreur h1 {margin-top: 0px;}' . "\n";
	echo '</style>' . "\n";
	echo '</head>' . "\n";
	echo '<body>' . "\n";
	
	echo '<div class="erreur">' . "\n";
	echo '<h1>Important</h1>' . "\n";
	
	echo '<p>' . T_ngettext('Voici une action importante à réaliser pour bien débuter l\'utilisation de Squeletml:', 'Voici deux actions importantes à réaliser pour bien débuter l\'utilisation de Squeletml:', $nbreErreurs) . '</p>';
	
	echo '<ul>' . "\n";
	if(!file_exists('init.inc.php'))
	{
		echo '<li>' . sprintf(T_("Le fichier %1\$s n'existe pas. Ce fichier est nécessaire au fonctionnement de Squeletml. Veuillez copier le fichier %2\$s (situé à la racine du site), le coller sous le nom %1\$s et renseigner les quelques variables y étant contenues."), '<code>init.inc.php</code>', '<code>init.inc.php.defaut</code>') . '</li>' . "\n";
	}
	if(!file_exists('.acces'))
	{
		if(!file_exists('init.inc.php'))
		{
			echo '<li>' . sprintf(T_("Après avoir créé et renseigné le fichier %1\$s, veuillez visiter <a href='%2\$s'>la page de gestion de l'accès au site et à l'administration</a> et ajouter un utilisateur pour protéger l'accès à la section d'administration de votre site."), '<code>init.inc.php</code>', 'admin/acces.admin.php') . '</li>' . "\n";
		}
		else
		{
			echo '<li>' . sprintf(T_("Veuillez visiter <a href='%1\$s'>la page de gestion de l'accès au site et à l'administration</a> et ajouter un utilisateur pour protéger l'accès à la section d'administration de votre site."), 'admin/acces.admin.php') . '</li>' . "\n";
		}
	}
	echo '</ul>' . "\n";
	
	echo '<p>' . T_ngettext("Après avoir accompli l'action demandée, visitez de nouveau la présente page.", "Après avoir accompli les actions demandées, visitez de nouveau la présente page.", $nbreErreurs) . '</p>' . "\n";
	echo '</div>' . "\n";
	
	echo '</body>' . "\n";
	echo '</html>';
}

?>
