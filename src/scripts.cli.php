#!/usr/bin/php
<?php
error_reporting(E_ALL);

include dirname(__FILE__) . '/../init.inc.php';

########################################################################
##
## Modèles.
##
########################################################################
if ($argv[1] == 'modeles')
{
	$config = file_get_contents($racine . '/inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($racine . '/modeles/site/inc/config.inc.php.modele', $ajout);
	
	$config = file_get_contents($racine . '/admin/inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($racine . '/modeles/site/admin/inc/config.inc.php.modele', $ajout);
	
	$css = file_get_contents($racine . '/css/squeletml.css');
	preg_match_all('|^(/\*.*?\*/)|ms', $css, $resultat);
	$ajout = implode("\n\n", $resultat[1]) . "\n\n";
	file_put_contents($racine . '/modeles/site/css/style.css.modele', $ajout);
	
	copy("$racine/Makefile", "$racine/modeles/Makefile.modele");
	
	copy("$racine/.gitignore", "$racine/modeles/.gitignore.modele");
}
########################################################################
##
## Message d'accueil.
##
########################################################################
elseif ($argv[1] == 'messageAccueil')
{
	include $racine . '/inc/php-markdown/markdown.inc.php';
	
	if ($fic = fopen($racine . '/README.md', 'r'))
	{
		$fichierLisezMoi = array ();
		
		while (!feof($fic))
		{
			$ligne = fgets($fic);
			
			if (strpos($ligne, '## ') === 0)
			{
				$ligne = fgets($fic);
				
				do
				{
					if ($ligne != "\n")
					{
						$fichierLisezMoi[] = rtrim(Markdown($ligne));
					}
					
					$ligne = fgets($fic);
				} while (strpos($ligne, '## ') !== 0);
				
				break;
			}
		}
		
		fclose($fic);
	}
	
	if ($fic = fopen($racine . '/xhtml/message-accueil-par-defaut.inc.php', 'w'))
	{
		fputs($fic, '<?php' . "\n");
		fputs($fic, 'echo \'<h2>\' . T_("Bienvenue sur votre site Squeletml") . "</h2>\n";' . "\n\n");
		
		foreach ($fichierLisezMoi as $ligne)
		{
			fputs($fic, 'echo T_("' . str_replace('"', '\"', $ligne) . '") . "\n";' . "\n\n");
		}
		
		fputs($fic, 'echo \'<p>\' . sprintf(T_("Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href=\"%1\$s\">en visitant la documentation</a>."), "$urlRacine/$dossierAdmin/documentation.admin.php") . "</p>\n";' . "\n");
		
		fputs($fic, '?>');
		fclose($fic);
	}
}
?>
