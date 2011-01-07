<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

define('ACCUEIL', accueil($accueil, array ($langue, $langueParDefaut)));
define('LANGUE', langue($langue, $langueParDefaut));

// Le code de la constante `MDTXT_PHP` est utilisé pour évaluer du code PHP imbriqué dans du Markdown (voir la section «Syntaxe Markdown avec imbrication de code PHP» dans la documentation). La raison pour laquelle une fonction n'a pas été utilisée est de pouvoir profiter de la même portée pour les variables que celle dans le corps d'une page de Squeletml sans devoir passer par des variables globales.
define('MDTXT_PHP', <<<CODE
	if (!isset(\$chaineMdtxtPhp) && isset(\$cheminMdtxtPhp))
	{
		\$chaineMdtxtPhp = @file_get_contents(\$cheminMdtxtPhp);
	}
	
	if (isset(\$chaineMdtxtPhp) && \$chaineMdtxtPhp !== FALSE)
	{
		if (preg_match_all('/\[php\](.+?)\[\/php\]/s', \$chaineMdtxtPhp, \$resultatMdtxtPhp))
		{
			\$iMdtxtPhp = 0;
			
			foreach (\$resultatMdtxtPhp[1] as \$phpAevaluerMdtxtPhp)
			{
				ob_start();
				eval(\$phpAevaluerMdtxtPhp);
				\$phpEvalueMdtxtPhp = ob_get_contents();
				ob_end_clean();
				
				\$chaineMdtxtPhp = str_replace(\$resultatMdtxtPhp[0][\$iMdtxtPhp], \$phpEvalueMdtxtPhp, \$chaineMdtxtPhp);
				\$iMdtxtPhp++;
			}
		}
		
		echo mdtxtChaine(\$chaineMdtxtPhp);
	}
CODE
);

define('URL_SQUELETML', 'http://www.squeletml.net/');
define('URL_DERNIERE_VERSION_SQUELETML', 'http://www.squeletml.net/site/fichiers/version.txt');
define('URL_TELECHARGEMENT_SQUELETML', 'http://www.squeletml.net/telechargement.php');
?>
