#!/usr/bin/php
<?php

// ChangeLog vers Markdown
if ($argv[1] == 'mdtxt')
{
	$fichierALire = $argv[2];
	$fic = fopen($fichierALire . '.mdtxt', 'w');
	$fichier = file_get_contents($fichierALire);
	
	$fichier = preg_replace('/^/m', "\t\t", $fichier);
	$fichier = preg_replace('/^\t\t=== ([^=]+) ===$/m', '- $1' . "\n", $fichier);
	$fichier = preg_replace('/^\t\t([0-9]{4}(-[0-9]{2}){2})  /m', "\t" . '- $1&nbsp;&nbsp;', $fichier);
	$fichier = preg_replace('/^\t\t\t\* (.+)$/m', "\t\t" . '- $1', $fichier);
	$fichier = preg_replace('/,\n\t\t- (?! )/m', ",  \n\t\t", $fichier);
	$fichier = preg_replace('/\.\n\t\t- (?! )/m', ".\n\n\t\t- ", $fichier);
	$fichier = preg_replace('/^\t$/m', '', $fichier);
	
	$fichier = preg_replace('/^(\t\t.+: \[[0-9]+\]) /m', '$1' . "\n\n\t\t\t", $fichier);
	
	// Optionnel. Supprime l'adresse courriel.
	$fichier = preg_replace('/^(\t- [0-9]{4}(-[0-9]{2}){2}[^<]+) <[^@]+@[^>]+>/m', '$1', $fichier);
	
	fwrite($fic, $fichier);
	fclose($fic);
}

// Message d'accueil
if ($argv[1] == 'message-accueil')
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
						$fichierLisezMoi[] = rtrim(Markdown($ligne));
					}
					$ligne = fgets($fic);
				} while (!preg_match('/^## /', $ligne));
				break;
			}
		}
		
		fclose($fic);
		
		$fichierLisezMoi[] = '<p>Apprenez-en plus sur les fonctionnalités de Squeletml, et commencez à personnaliser votre installation, <a href=\'admin/documentation.admin.php\'>en visitant la documentation</a>.</p>';
	}
	
	if ($fic = fopen('inc/message-accueil.inc.php', 'w'))
	{
		fputs($fic, "<?php\n");
		foreach ($fichierLisezMoi as $ligne)
		{
			fputs($fic, 'echo T_("' . $ligne . '");' . "\n\n");
		}
		fputs($fic, "?>");
		fclose($fic);
	}
}

?>
