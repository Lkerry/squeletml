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

// Le code de `MKD_PHP` est utilisé pour évaluer du code PHP imbriqué dans du Markdown (voir la section «Syntaxe Markdown avec imbrication de code PHP» dans la documentation). La raison pour laquelle une fonction n'a pas été utilisée est de pouvoir profiter de la même portée pour les variables que celle dans le corps d'une page de Squeletml sans devoir passer par des variables globales.
if (!defined('MKD_PHP'))
{
	define('MKD_PHP', <<<CODE
		if (!isset(\$chaineMkdPhp) && isset(\$cheminMkdPhp))
		{
			\$chaineMkdPhp = @file_get_contents(\$cheminMkdPhp);
		}
		
		if (isset(\$chaineMkdPhp) && \$chaineMkdPhp !== FALSE)
		{
			if (preg_match_all('/\[php\](.+?)\[\/php\]/s', \$chaineMkdPhp, \$resultatMkdPhp))
			{
				\$iMkdPhp = 0;
				
				foreach (\$resultatMkdPhp[1] as \$phpAevaluerMkdPhp)
				{
					ob_start();
					eval(\$phpAevaluerMkdPhp);
					\$phpEvalueMkdPhp = ob_get_contents();
					ob_end_clean();
					
					\$chaineMkdPhp = str_replace(\$resultatMkdPhp[0][\$iMkdPhp], \$phpEvalueMkdPhp, \$chaineMkdPhp);
					\$iMkdPhp++;
				}
			}
			
			echo mkdChaine(\$chaineMkdPhp);
		}
CODE
	);
}

if (!defined('URL_SQUELETML'))
{
	define('URL_SQUELETML', 'http://www.jpfleury.net/logiciels/squeletml.php');
}

if (!defined('URL_DERNIERE_VERSION_SQUELETML'))
{
	define('URL_DERNIERE_VERSION_SQUELETML', 'http://jpfleury.indefero.net/p/squeletml/source/file/master/doc/version.txt');
}

if (!defined('URL_TELECHARGEMENT_SQUELETML'))
{
	define('URL_TELECHARGEMENT_SQUELETML', 'http://www.jpfleury.net/logiciels/squeletml.php#Telechargement');
}
?>
