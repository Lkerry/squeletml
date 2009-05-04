<?php
/* Inclut tout ce qu'il faut pour utiliser php-gettext comme outil de traduction des modules */
function phpGettext($racine, $langue)
{
	require_once $racine . '/inc/php-gettext/gettext.inc';
	$locale = $langue;
	T_setlocale(LC_MESSAGES, $locale);
	$domain = 'squeletml';
	T_bindtextdomain($domain, $racine . '/locale');
	T_bind_textdomain_codeset($domain, 'UTF-8');
	T_textdomain($domain);
	return;
}

/**
Retourne un tableau contenant les fichiers à inclure.
*/
function init($racine, $langue)
{
	$fichiers = array ();
	
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	
	$fichiers[] = $racine . '/inc/config.inc.php';

	$fichiers[] = $racine . '/inc/constantes.inc.php';
	
	if (file_exists($racine . '/site/inc/config.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/config.inc.php';
	}
	
	if (file_exists($racine . '/site/inc/fonctions.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/fonctions.inc.php';
	}
	
	if (file_exists($racine . '/site/inc/constantes.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/constantes.inc.php';
	}
	
	return $fichiers;
}

/**
Retourne le lien vers l'accueil de la langue de la page.
*/
function accueil($tableauAccueil, $tableauLangue)
{
	if (array_key_exists(langue($tableauLangue), $tableauAccueil))
	{
		return $tableauAccueil[langue($tableauLangue)];
	}
	else
	{
		return $tableauAccueil[langueParDefaut($tableauLangue)];
	}
}

/**
Retourne la langue de la page courante.
*/
function langue($langue)
{
	return isset($langue[1]) ? $langue[1] : $langue[0];
}

/**
Retourne la langue par défaut du site
*/
function langueParDefaut($langue)
{
	return $langue[0];
}

/**
Si $motsCles est vide, génère à partir d'une chaîne fournie une liste de mots-clés utilisables par la métabalise keywords, et retourne cette liste, sinon retourne tout simplement $motsCles.
*/
function construitMotsCles($motsCles, $chaine)
{
	if (empty($motsCles))
	{
		$chaine = trim($chaine);

		// Suppression des caractères inutiles
		$chaine = str_replace(
				array (
					'(',
					')',
					'!',
					'?',
					'+',
					'...',
					'"',
					'«',
					'»',
					'[',
					']',
					':',
					'&quot;',
					','
				),
				array ('', '', '', '', '', '', '', '', '', '', '', ''), $chaine);

		// Remplacement des séparateurs «utiles» par des espaces
		$chaine = str_replace(array ('/', '.', '-'), array (' ', ' ', ' '), $chaine);

		// Compression des espaces en trop éventuelles générées par l'étape précédente
		$chaine = str_replace(array ('  '), array (' '), $chaine);

		// Remplacement des espaces par des virgules
		$chaine = str_replace(' ', ', ', $chaine);
		
		// Suppression des mots de trois lettres ou moins
		$chaine = preg_replace('/(^| )[^, ]{1,3},/', '', $chaine);
		
		// Suppression du potentiel ', ' final avant le mélange des mots
		if (preg_match('/, $/', $chaine))
		{
			$chaine = trim(substr($chaine, 0, -2));
		}
		
		// Mélanger l'ordre des mots
		$tableauChaine = explode(', ', $chaine);
		shuffle($tableauChaine);
		$chaine = '';
		foreach ($tableauChaine as $mot)
		{
			$chaine .= $mot . ', ';
		}
		
		// Resuppression du ', ' final
		$chaine = trim(substr($chaine, 0, -2));
		
		// Tout en minuscule
		$chaine = strtolower($chaine);
		
		return $chaine;
	}
	else
	{
		return $motsCles;
	}
}

/**
Construit les balises d'inclusion `link` et les balises `script` pour le javascript.
@param fichiers un tableau dont la syntaxe est:
	$fichiers[] = array ("URL" => "TYPE:fichier à inclure");
	ajouter une étoile à la fin de l'URL pour inclure toutes les pages enfants
	Les types possibles sont: css, cssltIE7, cssIE7, javascript, favicon
@param version un identifiant (optionnel) des versions des fichiers inclus, comme une date, un nombre...
@return les balises `link` correctement remplies
*/
function construitLinkScript($fichiers, $version = '')
{
	$balisesLinkScript = '';
	if (!empty($version))
	{
		$version = '?' . $version;
	}
	
	if (!empty($fichiers))
	{
		foreach ($fichiers as $indice)
		{
			foreach ($indice as $page => $fichier)
			{
				// Récupérer le type
				preg_match('/^([^:]+):(.+)/', $fichier, $res);
				$type = $res[1];
				$fichier = $res[2];
		
				// Si l'adresse se termine par *, accepter toutes les pages enfants possibles de cette page parent en plus de la page parent elle-même.
				if (eregi("\*$", $page))
				{
					$modele = substr($page, 0, -1);
					$modele .= ".*";
				}
				else
				{
					$modele = $page;
				}

				if (eregi($modele, 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']))
				{
					switch ($type)
					{
						case 'favicon':
							$balisesLinkScript .= '<link rel="shortcut icon" type="images/x-icon" href="' . $fichier . $version . '" />' . "\n";
							break;
				
						case 'css':
							$balisesLinkScript .= '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n";
							break;
				
						case 'cssltIE7':
							$balisesLinkScript .= '<!--[if lt IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
							break;
					
						case 'cssIE7':
							$balisesLinkScript .= '<!--[if IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
							break;
				
						case 'javascript':
							$balisesLinkScript .= '<script type="text/javascript" src="' . $fichier . $version . '"></script>' . "\n";
							break;
					}
				}
			}
		}
	}
	
	return $balisesLinkScript;
}

/**
Construit un lien vers l'accueil si la page n'est pas l'accueil, sinon n'ajoute aucune balise et retourne la chaîne telle quelle.
*/
function construitLienVersAccueil($accueil, $estAccueil, $contenu)
{
	if (!$estAccueil)
	{
		$aOuvrant = '<a href="' . $accueil . '/">';
		$aFermant = '</a>';
	}
	else
	{
		$aOuvrant = '';
		$aFermant = '';
	}
	
	return $aOuvrant . $contenu . $aFermant;
}

/**
Construit la phrase de description du site dans le haut des pages.

Sur la page d'accueil, ce sera le titre principal h1; sur les autres pages, ce sera un paragraphe p.

@param estAccueil informe si on se trouve sur l'accueil du site ou non
@param contenu le contenu à insérer dans les balises qui seront construites
@return le contenu entouré de balises
*/
function construitNomSite($estAccueil, $contenu)
{
	if (!$estAccueil)
	{
		$baliseOuvrante = '<p>';
		$baliseFermante = '</p>';
	}
	else
	{
		$baliseOuvrante = '<h1>';
		$baliseFermante = '</h1>';
	}
	
	return $baliseOuvrante . $contenu . $baliseFermante . "\n";
}

/**
Renvoie TRUE si la page est l'accueil, sinon renvoie FALSE.
*/
function estAccueil($accueil)
{
	if ('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $accueil . '/'
		|| 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $accueil . '/index.php'
		|| 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $accueil . '/index.html'
		|| 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $accueil . '/index.htm')
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/**
Traite une chaîne pour l'afficher sécuritairement à l'écran.
*/
function securiseTexte($texte)
{
	return stripslashes(htmlspecialchars($texte));
}

/**
Renvoie une lettre au hasard (a-zA-Z). Optionnellement, préciser des lettres à ne pas renvoyer.
*/
function lettreAuHasard($lettresExclues = '')
{
	$lettres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	do
	{
		$lettre = $lettres[rand(0, 51)];
	} while (substr_count($lettresExclues, $lettre));
	
	return $lettre;
}

/**
Renvoie une liste de classes.
*/
function construitClass($estAccueil)
{
	$class = '';
	
	if ($estAccueil)
	{
		$class .= 'accueil ';
	}
	
	return trim($class);
}

/**
Retourne le contenu de la métabalise robots.
*/
function robots($robots)
{
	return isset($robots[1]) ? $robots[1] : $robots[0];
}

/**
Construit le message affiché à IE6. Les 4 premiers paramètres sont relatifs à l'image de Firefox qui va être affichée dans le message. le dernier précise la langue du message.
*/
function messageIE6($src, $alt, $width, $height)
{
	$message = '<!--[if lt IE 7]>' . "\n";
	$message .= '<div id="messageIE6">' . "\n";
	$message .= '<p><strong>' . T_("Savez-vous que le navigateur Internet&nbsp;Explorer&nbsp;6 (avec lequel vous visitez sur ce site actuellement) est obsolète?") . '</strong></p>' . "\n";
	$message .= "\n";
	$message .= '<p>' . T_("Pour naviguer de la manière la plus satisfaisante et sécuritaire, nous recommandons d'utiliser <strong>Firefox</strong>, un navigateur libre, performant, sécuritaire et respectueux des standards sur lesquels le web est basé. Firefox est tout à fait gratuit. Si vous utilisez un ordinateur au travail, vous pouvez faire la suggestion à votre service informatique.") . '</p>' . "\n";
	$message .= "\n";
	$message .= "<p><strong><a href=\"http://www.mozilla-europe.org/fr/\"><img src=\"$src\" alt=\"$alt\" width=\"$width\" height=\"$height\" /></a> <a href=\"http://www.mozilla-europe.org/fr/\"><span>" . T_("Télécharger Firefox") . '</span></a></strong></p>' . "\n";
	$message .= '</div>';
	$message .= '<![endif]-->';
	
	return $message;
}

/**
Retourne le complément de la balise `title`.
*/
function baliseTitleComplement($tableauBaliseTitleComplement, $tableauLangue)
{
	if (array_key_exists(langue($tableauLangue), $tableauBaliseTitleComplement))
	{
		return $tableauBaliseTitleComplement[langue($tableauLangue)];
	}
	else
	{
		return $tableauBaliseTitleComplement[langueParDefaut($tableauLangue)];
	}
}

/**
Retourne le titre du site.
*/
function titreSite($tableauTitreSite, $tableauLangue)
{
	if (array_key_exists(langue($tableauLangue), $tableauTitreSite))
	{
		return $tableauTitreSite[langue($tableauLangue)];
	}
	else
	{
		return $tableauTitreSite[langueParDefaut($tableauLangue)];
	}
}

/**
Retourne le fichier de menu des langues.
*/
function fichierMenuLangues($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.menu-langues.inc.php'))
	{
		$menuLangues = $racine . '/site/inc/html.' . langue($tableauLangue) . '.menu-langues.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.menu-langues.inc.php'))
	{
		$menuLangues = $racine . '/inc/html.' . langue($tableauLangue) . '.menu-langues.inc.php';
	}
	else
	{
		$menuLangues = $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.menu-langues.inc.php';
	}
	
	return $menuLangues;
}

/**
Retourne le fichier de menu.
*/
function fichierMenu($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.menu.inc.php'))
	{
		$menu = $racine . '/site/inc/html.' . langue($tableauLangue) . '.menu.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.menu.inc.php'))
	{
		$menu = $racine . '/inc/html.' . langue($tableauLangue) . '.menu.inc.php';
	}
	else
	{
		$menu = $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.menu.inc.php';
	}
	
	return $menu;
}

/**
Inclut le sous-titre personnalisé s'il existe dans `site/inc/`, sinon inclut le sous-titre par défaut.
*/
function fichierSousTitre($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.sous-titre.inc.php'))
	{
		return $racine . '/site/inc/html.' . langue($tableauLangue) . '.sous-titre.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.sous-titre.inc.php'))
	{
		return $racine . '/inc/html.' . langue($tableauLangue) . '.sous-titre.inc.php';
	}
	else
	{
		return $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.sous-titre.inc.php';
	}
}

/**
Inclut le fichier d'ancres personnalisé s'il existe dans `site/inc/`, sinon inclut les ancres par défaut.
*/
function fichierAncres($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.ancres.inc.php'))
	{
		return $racine . '/site/inc/html.' . langue($tableauLangue) . '.ancres.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.ancres.inc.php'))
	{
		return $racine . '/inc/html.' . langue($tableauLangue) . '.ancres.inc.php';
	}
	else
	{
		return $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.ancres.inc.php';
	}
}

/**
Inclut le bas de page personnalisé s'il existe dans `site/inc/`, sinon inclut le base de page par défaut.
*/
function fichierBasDePage($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.bas-de-page.inc.php'))
	{
		return $racine . '/site/inc/html.' . langue($tableauLangue) . '.bas-de-page.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.bas-de-page.inc.php'))
	{
		return $racine . '/inc/html.' . langue($tableauLangue) . '.bas-de-page.inc.php';
	}
	else
	{
		return $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.bas-de-page.inc.php';
	}
}

/**
Retourne le nom du fichier affichant la galerie.
*/
function nomFichierGalerie()
{
	return $_SERVER['SCRIPT_NAME'];
}

/**
Construit et retourne le code pour afficher une oeuvre dans la galerie.
*/
function afficheOeuvre($squeletmlAccueil, $racineImgSrc, $galerie, $galerieNavigation, $taille, $indice, $sens)
{
	if ($taille == 'grande')
	{
		if (!empty($galerie[$indice]['grandeLargeur']))
		{
			$width = 'width="' . $galerie[$indice]['grandeLargeur'] . '"';
		}
		
		if (!empty($galerie[$indice]['grandeHauteur']))
		{
			$height = 'height="' . $galerie[$indice]['grandeHauteur'] . '"';
		}
		
		if (!empty($galerie[$indice]['grandeAlt']))
		{
			$alt = 'alt="' . $galerie[$indice]['grandeAlt'] . '"';
		}
		else
		{
			$alt = 'alt="Oeuvre ' . $galerie[$indice]['id'] . '"';
		}
		
		if (!empty($galerie[$indice]['grandeCommentaire']))
		{
			$commentaire = '<span id="galerieGrandeCommentaire">' . $galerie[$indice]['grandeCommentaire'] . '</span>';
		}

		return '<img src="' . $racineImgSrc . '/' . $galerie[$indice]['grandeNom'] . '"' . " $width $height $alt />" . $commentaire;
	}

	elseif ($taille == 'vignette')
	{
		if ($galerieNavigation == 'fleches' && $sens != 'aucun')
		{
			$class = ' galerieFleche';
			$width = 'width="80"';
			$height = 'height="80"';
			if ($sens == 'precedent')
			{
				$fleche = 'gauche';
			}
			elseif ($sens == 'suivant')
			{
				$fleche = 'droite';
			}
			$src = 'src="' . $squeletmlAccueil . '/fichiers/png/fleche-' . $fleche . '.png"';
		}

		elseif (($galerieNavigation == 'fleches' && $sens == 'aucun')
			|| $galerieNavigation == 'vignettes')
		{
			if (!empty($galerie[$indice]['vignetteLargeur']))
			{
				$width = 'width="' . $galerie[$indice]['vignetteLargeur'] . '"';
			}
			if (!empty($galerie[$indice]['vignetteHauteur']))
			{
				$height = 'height="' . $galerie[$indice]['vignetteHauteur'] . '"';
			}
			
			if (!empty($galerie[$indice]['vignetteNom']))
			{
				$src = 'src="' . $racineImgSrc . '/' . $galerie[$indice]['vignetteNom'] . '"';
			}
			else
			{
				$infoGrandeNom = pathinfo($galerie[$indice]['grandeNom']);
				$vignetteNom = basename($galerie[$indice]['grandeNom'], '.' . $infoGrandeNom['extension']);
				$vignetteNom .= '-vignette.' . $infoGrandeNom['extension'];
				$src = 'src="' . $racineImgSrc . '/' . $vignetteNom . '"';
			}
		}

		if (!empty($galerie[$indice]['vignetteAlt']))
		{
			$alt = 'alt="' . $galerie[$indice]['vignetteAlt'] . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Oeuvre %1\$s"), $galerie[$indice]['id']) . '"';
		}

		return '<a href="' . nomFichierGalerie() . '?oeuvre=' . $galerie[$indice]['id'] . '"><img class="galerieNavigation' . $class . '" ' . "$src $width $height $alt /></a>";
	}
	
	else
	{
		return;
	}
}

?>
