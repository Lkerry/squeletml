#!/usr/bin/php
<?php
error_reporting(E_ALL);

########################################################################
##
## Ajout des fichiers de configuration à la documentation convertie en HTML.
##
########################################################################

if ($argv[1] == 'annexesDoc')
{
	include 'init.inc.php';
	include $racine . '/inc/fonctions.inc.php';
	include $racine . '/inc/php-markdown/markdown.php';
	
	$cheminFichierSauvegarde = $argv[2] . '/documentation-avec-config.html';
	eval(variablesAvantConfig());
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	phpGettext('.', 'fr'); // Nécessaire à la traduction.
	
	$contenuDocAvecConfig = mdtxt($racine . '/doc/documentation.mdtxt');
	$contenuDocAvecConfig .= annexesDocumentation($racineAdmin);
	
	file_put_contents($cheminFichierSauvegarde, $contenuDocAvecConfig);
}
########################################################################
##
## ChangeLog vers HTML.
##
########################################################################
elseif ($argv[1] == 'changelogHtml')
{
	include 'inc/php-markdown/markdown.php';
	
	$cheminFichierSauvegarde = $argv[2] . '/ChangeLog.html';
	
	// ChangeLog vers Markdown.
	$fichier = file_get_contents('doc/ChangeLog');
	$fichier = preg_replace('/^/m', "\t\t", $fichier);
	$fichier = preg_replace('/^\t\t=== ([^=]+) ===$/m', '- $1' . "\n", $fichier);
	$fichier = preg_replace('/^\t\t([0-9]{4}(-[0-9]{2}){2})  /m', "\t" . '- $1&nbsp;&nbsp;', $fichier);
	$fichier = preg_replace('/^\t\t\t\* (.+)$/em', "\"\t\t- \" . str_replace('_', '\_', '\$1')", $fichier);
	$fichier = preg_replace('/,\n\t\t- (?! )/m', ",  \n\t\t", $fichier);
	$fichier = preg_replace('/\.\n\t\t- (?! )/m', ".\n\n\t\t- ", $fichier);
	$fichier = preg_replace('/^\t$/m', '', $fichier);
	$fichier = preg_replace('/^(\t\t.+: \[[0-9]+\]) /m', '$1' . "\n\n\t\t\t", $fichier);
	$fichier = preg_replace('/^(\t- [0-9]{4}(-[0-9]{2}){2}[^<]+) <[^@]+@[^>]+>/m', '$1', $fichier); // Supprime l'adresse courriel (optionnel).
	
	// Markdown vers HTML.
	//$fichier = Markdown($fichier);
	
	$fic = fopen($cheminFichierSauvegarde, 'w');
	fwrite($fic, $fichier);
	fclose($fic);
}
########################################################################
##
## Modèles de fichiers de configuration (du site et de l'administration) personnalisés.
##
########################################################################
elseif ($argv[1] == 'config')
{
	$cheminDossierSauvegarde = $argv[2];
	
	$config = file_get_contents('inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($cheminDossierSauvegarde . '/modeles/site/inc/config.inc.php.modele', $ajout);
	
	$config = file_get_contents('admin/inc/config.inc.php');
	preg_match_all('~(^#{72}.*?^#{72}|^/\* _{20} .*? _{20} \*/)~ms', $config, $resultat);
	$ajout = "<?php\n" . implode("\n\n", $resultat[1]) . "\n\n?>";
	file_put_contents($cheminDossierSauvegarde . '/modeles/site/admin/inc/config.inc.php.modele', $ajout);
}
########################################################################
##
## Modèle de feuille de style CSS personnalisée.
##
########################################################################
elseif ($argv[1] == 'css')
{
	$cheminFichierSauvegarde = $argv[2] . '/modeles/site/css/style.css.modele';
	
	$css = file_get_contents('css/squeletml.css');
	preg_match_all('|^(/\*.*?\*/)|ms', $css, $resultat);
	$ajout = implode("\n\n", $resultat[1]) . "\n\n";
	file_put_contents($cheminFichierSauvegarde, $ajout);
}
########################################################################
##
## Message d'accueil.
##
########################################################################
elseif ($argv[1] == 'messageAccueil')
{
	include 'inc/php-markdown/markdown.php';
	
	if ($fic = fopen('doc/LISEZ-MOI.mdtxt', 'r'))
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
	
	if ($fic = fopen('xhtml/message-accueil-par-defaut.inc.php', 'w'))
	{
		fputs($fic, '<?php' . "\n");
		fputs($fic, 'echo \'<h2 class="accueilPremierH2">\' . T_("Bienvenue sur votre site Squeletml") . "</h2>\n";' . "\n\n");
		
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
