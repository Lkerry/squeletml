#!/usr/bin/php
<?php
error_reporting(E_ALL);

########################################################################
##
## ChangeLog vers Markdown.
##
########################################################################

if ($argv[1] == 'mdtxt')
{
	$cheminFichier = $argv[2];
	$fic = fopen($cheminFichier . '.mdtxt', 'w');
	
	$fichier = file_get_contents($cheminFichier);
	$fichier = preg_replace('/^/m', "\t\t", $fichier);
	$fichier = preg_replace('/^\t\t=== ([^=]+) ===$/m', '- $1' . "\n", $fichier);
	$fichier = preg_replace('/^\t\t([0-9]{4}(-[0-9]{2}){2})  /m', "\t" . '- $1&nbsp;&nbsp;', $fichier);
	$fichier = preg_replace('/^\t\t\t\* (.+)$/m', "\t\t" . '- $1', $fichier);
	$fichier = preg_replace('/,\n\t\t- (?! )/m', ",  \n\t\t", $fichier);
	$fichier = preg_replace('/\.\n\t\t- (?! )/m', ".\n\n\t\t- ", $fichier);
	$fichier = preg_replace('/^\t$/m', '', $fichier);
	$fichier = preg_replace('/^(\t\t.+: \[[0-9]+\]) /m', '$1' . "\n\n\t\t\t", $fichier);
	// Supprime l'adresse courriel (optionnel).
	$fichier = preg_replace('/^(\t- [0-9]{4}(-[0-9]{2}){2}[^<]+) <[^@]+@[^>]+>/m', '$1', $fichier);
	
	fwrite($fic, $fichier);
	fclose($fic);
}
########################################################################
##
## Message d'accueil.
##
########################################################################
elseif ($argv[1] == 'message-accueil')
{
	include 'inc/php-markdown/markdown.php';
	
	if ($fic = fopen('LISEZ-MOI.mdtxt', 'r'))
	{
		$fichierLisezMoi = array ();
		$fichierLisezMoi[] = '<h2>Bienvenue sur votre site Squeletml</h2>';
		
		while (!feof($fic))
		{
			$ligne = fgets($fic);
			
			if ($ligne == "## Qu'est-ce que Squeletml?\n")
			{
				do
				{
					if ($ligne != "\n")
					{
						if (preg_match('|<img |', $ligne))
						{
							$ligne = preg_replace('|<img [^>]+>|', '', $ligne);
						}
						
						$fichierLisezMoi[] = rtrim(Markdown($ligne));
					}
					
					$ligne = fgets($fic);
				} while (!preg_match('/^## /', $ligne));
				
				break;
			}
		}
		
		fclose($fic);
	}
	
	if ($fic = fopen('xhtml/message-accueil-par-defaut.inc.php', 'w'))
	{
		fputs($fic, "<?php\n");
		
		foreach ($fichierLisezMoi as $ligne)
		{
			fputs($fic, 'echo T_("' . $ligne . '");' . "\n\n");
		}
		
		fputs($fic, 'printf(T_("<p>Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href=\'%1\$s\'>en visitant la documentation</a>.</p>"), "$urlRacine/$dossierAdmin/documentation.admin.php");');
		
		fputs($fic, "?>");
		fclose($fic);
	}
}
########################################################################
##
## Ajout du contenu du fichier de configuration à la documentation.
##
########################################################################
elseif ($argv[1] == 'annexes-doc')
{
	$cheminDocumentation = $argv[2];
	
	include 'init.inc.php';
	include $racine . '/inc/fonctions.inc.php';
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	phpGettext('.', 'fr'); // Nécessaire à la traduction.
	
	$ajout = annexesDocumentation($racineAdmin);
	
	file_put_contents($cheminDocumentation, $ajout, FILE_APPEND);
}
########################################################################
##
## Modèles de fichiers de configuration (du site et de l'administration) personnalisés.
##
########################################################################
elseif ($argv[1] == 'config')
{
	$cheminTag = $argv[2];
	
	$config = file_get_contents('inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($cheminTag . '/site/inc/config.inc.php', $ajout);
	
	$config = file_get_contents('admin/inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($cheminTag . '/site/admin/inc/config.inc.php', $ajout);
}
########################################################################
##
## Modèle de feuille de style CSS personnalisée.
##
########################################################################
elseif ($argv[1] == 'css')
{
	$cheminTag = $argv[2];
	
	$css = file_get_contents('css/squeletml.css');
	preg_match_all('|^(/\*.*?\*/)|ms', $css, $resultat);
	$ajout = implode("\n\n", $resultat[1]) . "\n\n";
	file_put_contents($cheminTag . '/site/css/style.css', $ajout);
}
?>
