<?php
if (!defined('LC_MESSAGES'))
{
	define('LC_MESSAGES', 5);
}

if (!defined('ACCUEIL'))
{
	define('ACCUEIL', 'return accueil($accueil, array ($langue, $langueParDefaut));');
}

if (!defined('LANGUE'))
{
	define('LANGUE', 'return langue($langue, $langueParDefaut);');
}

// Le code de `MD_PHP` est utilisé pour évaluer du code PHP imbriqué dans du Markdown (voir la section «Syntaxe Markdown avec imbrication de code PHP» dans la documentation). La raison pour laquelle une fonction n'a pas été utilisée est de pouvoir profiter de la même portée pour les variables que celle dans le corps d'une page de Squeletml sans devoir passer par des variables globales.
if (!defined('MD_PHP'))
{
	define('MD_PHP', <<<CODE
		if (!isset(\$chaineMdPhp) && isset(\$cheminMdPhp))
		{
			\$chaineMdPhp = @file_get_contents(\$cheminMdPhp);
		}
		
		if (isset(\$chaineMdPhp) && \$chaineMdPhp !== FALSE)
		{
			if (preg_match_all('/\[php\](.+?)\[\/php\]/s', \$chaineMdPhp, \$resultatMdPhp))
			{
				\$iMdPhp = 0;
				
				foreach (\$resultatMdPhp[1] as \$phpAevaluerMdPhp)
				{
					ob_start();
					eval(\$phpAevaluerMdPhp);
					\$phpEvalueMdPhp = ob_get_contents();
					ob_end_clean();
					
					\$chaineMdPhp = str_replace(\$resultatMdPhp[0][\$iMdPhp], \$phpEvalueMdPhp, \$chaineMdPhp);
					\$iMdPhp++;
				}
			}
			
			echo mdChaine(\$chaineMdPhp);
		}
CODE
	);
}

if (!defined('URL_SQUELETML'))
{
	define('URL_SQUELETML', 'https://github.com/jpfleury/squeletml');
}

if (!defined('URL_DERNIERE_VERSION_SQUELETML'))
{
	define('URL_DERNIERE_VERSION_SQUELETML', 'https://raw.githubusercontent.com/jpfleury/squeletml/master/doc/version.txt');
}

if (!defined('URL_TELECHARGEMENT_SQUELETML'))
{
	define('URL_TELECHARGEMENT_SQUELETML', 'https://github.com/jpfleury/squeletml');
}
?>
