<?php
/**
Retourne un tableau contenant les fichiers à inclure.
*/
function init($racine, $langue, $idGalerie)
{
	$fichiers = array ();
	
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	
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
	
	if ($idGalerie)
	{
		$fichiers[] = $racine . '/inc/galerie.inc.php'; // Important d'insérer avant premier.inc.php, pour permettre la modification des balises de l'en-tête
	}
	
	return $fichiers;
}

/**
Retourne le bon DTD (Définition de Type de Document).
*/
function doctype($xhtmlStrict)
{
	if ($xhtmlStrict)
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	}
	else
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	}
}

/**
Accepte en paramètre un fichier dont le contenu est rédigé en Markdown, et retourne le contenu de ce fichier converti en HTML.
*/
function mdtxt($fichier)
{
	return Markdown(file_get_contents($fichier));
}

/**
Accepte en paramètre une chaîne rédigée en Markdown, et retourne cette chaîne convertie en HTML.
*/
function mdtxtChaine($chaine)
{
	return Markdown($chaine);
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
	Les types possibles sont: css, cssltIE7, cssIE7, csslteIE7, javascript, favicon
@param version un identifiant (optionnel) des versions des fichiers inclus, comme une date, un nombre...
@return les balises `link` correctement remplies
*/
function construitLinkScript($fichiers, $version = '', $styleSqueletmlCss)
{
	$balisesLinkScript = '';
	if (!empty($version))
	{
		$version = '?' . $version;
	}
	
	if (!$styleSqueletmlCss)
	{
		// Suppression des feuilles de style par défaut
		unset($fichiers[0], $fichiers[1], $fichiers[2]);
	}
	
	if (!empty($fichiers))
	{
		$favicon = '';
		foreach ($fichiers as $indice)
		{
			foreach ($indice as $page => $fichier)
			{
				// Récupérer le type
				preg_match('/^([^:]+):(.+)/', $fichier, $res);
				$type = $res[1];
				$fichier = $res[2];
		
				// Si l'adresse se termine par *, accepter toutes les pages enfants possibles de cette page parent en plus de la page parent elle-même.
				if (preg_match('/\*$/i', $page))
				{
					$modele = substr($page, 0, -1);
					$modele .= ".*";
				}
				else
				{
					$modele = $page;
				}

				if (preg_match("|$modele|i", 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']))
				{
					switch ($type)
					{
						case 'favicon':
							// On ne conserve qu'une déclaration de favicon.
							$favicon = '<link rel="shortcut icon" type="images/x-icon" href="' . $fichier . $version . '" />' . "\n";
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
							
						case 'csslteIE7':
							$balisesLinkScript .= '<!--[if lte IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
							break;
				
						case 'javascript':
							$balisesLinkScript .= '<script type="text/javascript" src="' . $fichier . $version . '"></script>' . "\n";
							break;
					}
				}
			}
		}
		
		$balisesLinkScript .= $favicon;
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
function construitClass($estAccueil, $menuSousLeContenu, $menuLanguesSousLeContenu, $menuSousLeMenuLangues, $colonneAgauche, $deuxColonnes, $idGalerie)
{
	$class = '';
	
	if ($estAccueil)
	{
		$class .= 'accueil ';
	}
	
	if ($menuSousLeContenu)
	{
		$class .= 'menuSousLeContenu ';
	}
	
	if ($menuLanguesSousLeContenu)
	{
		$class .= 'menuLanguesSousLeContenu ';
	}
	
	if ($menuSousLeMenuLangues)
	{
		$class .= 'menuSousLeMenuLangues ';
	}
	
	if ($colonneAgauche)
	{
		$class .= 'colonneAgauche ';
	}
	else
	{
		$class .= 'colonneAgaucheFalse ';
	}
	
	if ($deuxColonnes)
	{
		$class .= 'deuxColonnes ';
		if ($colonneAgauche)
		{
			$class .= 'deuxColonnesGauche ';
		}
		else
		{
			$class .= 'deuxColonnesGaucheFalse ';
		}
	}
	
	if ($idGalerie)
	{
		$class .= 'galerie ';
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
Inclut le sur-titre personnalisé s'il existe dans `site/inc/`, sinon inclut le sur-titre par défaut.
*/
function fichierSurTitre($racine, $tableauLangue)
{
	if (file_exists($racine . '/site/inc/html.' . langue($tableauLangue) . '.sur-titre.inc.php'))
	{
		return $racine . '/site/inc/html.' . langue($tableauLangue) . '.sur-titre.inc.php';
	}
	elseif (file_exists($racine . '/inc/html.' . langue($tableauLangue) . '.sur-titre.inc.php'))
	{
		return $racine . '/inc/html.' . langue($tableauLangue) . '.sur-titre.inc.php';
	}
	else
	{
		return $racine . '/inc/html.' . langueParDefaut($tableauLangue) . '.sur-titre.inc.php';
	}
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
Renvoie le type d'image entre gif, jpg et png.
*/
function typeImage($extension)
{
	if (strtolower($extension) == 'gif')
	{
		$type = 'gif';
	}
	elseif (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg')
	{
		$type = 'jpeg';
	}
	elseif (strtolower($extension) == 'png')
	{
		$type = 'png';
	}
	
	return $type;
}

/**
Modifie la source de la vignette pour la remplacer par une vignette tatouée d'une flèche de navigation.
*/
function vignetteTatouage($paragraphe, $sens, $racine, $racineImgSrc, $urlImgSrc, $qualiteJpg)
{
	preg_match('/src="([^"]+)"/', $paragraphe, $res);
	$srcContenu = $res[1];
	$nomImgSrcContenu = basename($srcContenu);
	$infoImgSrcContenu = pathinfo($nomImgSrcContenu);
	$vignetteNom = basename($nomImgSrcContenu, '.' . $infoImgSrcContenu['extension']);
	$vignetteNom .= '-' . $sens . '.' . $infoImgSrcContenu['extension'];
	
	if (file_exists($racineImgSrc . '/tatouage/' . $vignetteNom))
	{
		$srcContenu = $urlImgSrc . '/tatouage/' . $vignetteNom;
	}
	else
	{
		if (!file_exists($racineImgSrc . '/tatouage'))
		{
			mkdir($racineImgSrc . '/tatouage');
		}
	
		copy($racineImgSrc . '/' . $nomImgSrcContenu, $racineImgSrc . '/tatouage/' . $vignetteNom);
		
		if (file_exists($racine . '/site/fichiers/' . $sens . '-tatouage.png'))
		{
			$imgSrc = imagecreatefrompng($racine . '/site/fichiers/' . $sens . '-tatouage.png');
		}
		else
		{
			$imgSrc = imagecreatefrompng($racine . '/fichiers/' . $sens . '-tatouage.png');
		}
	
		$infoVignette = pathinfo($racineImgSrc . '/tatouage/' . $vignetteNom);
		$type = typeImage($infoVignette['extension']);
		switch ($type)
		{
			case 'gif':
				$imgDest = imagecreatefromgif($racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
	
			case 'jpeg':
				$imgDest = imagecreatefromjpeg($racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
		
			case 'png':
				$imgDest = imagecreatefrompng($racineImgSrc . '/tatouage/' . $vignetteNom);
				imagealphablending($imgDest, true);
				imagesavealpha($imgDest, true);
				break;
		}
	
		$largSrc = imagesx($imgSrc);
		$hautSrc = imagesy($imgSrc);
		$largDest = imagesx($imgDest);
		$hautDest = imagesy($imgDest);
	
		imagecopy($imgDest, $imgSrc, ($largDest / 2) - ($largSrc / 2), ($hautDest / 2) - ($hautSrc / 2), 0, 0, $largSrc, $hautSrc);
	
		switch ($type)
		{
			case 'gif':
				imagegif($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
	
			case 'jpeg':
				imagejpeg($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom, $qualiteJpg);
				break;
		
			case 'png':
				imagepng($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom, 9);
				break;
		}
	}
	
	// On retourne le paragraphe avec l'attribut `src` modifié
	return preg_replace('/src="[^"]+"/', 'src="' . $urlImgSrc . '/tatouage/' . $vignetteNom . '"', $paragraphe);
}

/**
Ajoute une deuxième image (flèche) à la navigation par vignettes.
*/
function vignetteAccompagnee($paragraphe, $sens, $racine, $urlRacine)
{
	if (file_exists($racine . '/site/fichiers/' . $sens . '-accompagnee.png'))
	{
		$cheminImage = $racine . '/site/fichiers/' . $sens . '-accompagnee.png';
		$urlImage = $urlRacine . '/site/fichiers/' . $sens . '-accompagnee.png';
	}
	else
	{
		$cheminImage = $racine . '/fichiers/' . $sens . '-accompagnee.png';
		$urlImage = $urlRacine . '/fichiers/' . $sens . '-accompagnee.png';
	}
	
	list($larg, $haut) = getimagesize($cheminImage);
	{
		$width = 'width="' . $larg . '"';
		$height = 'height="' . $haut . '"';
	}
	preg_match('/alt="([^"]+)"/', $paragraphe, $res);
	$altContenu = $res[1];
	$alt = 'alt="' . $altContenu . '"';
	
	$img = '<div id="galerieAccompagnementVignette' . ucfirst($sens) . '"><img src="' . $urlImage . '" ' . "$alt $width $height" . ' /></div>';
	
	// On retourne le paragraphe avec l'image de flèche en plus
	if ($sens == 'precedent')
	{
		return preg_replace('/(<img [^>]+>)/', '\1' . $img, $paragraphe);
	}
	elseif ($sens == 'suivant')
	{
		return preg_replace('/(<img [^>]+>)/', '\1' . $img, $paragraphe);
	}
}

/**
Génère l'attribut `style` pour les div vide simulant la présence d'une flèche ou d'une vignette de navigation dans la galerie.
*/
function styleDivVideNavigation($oeuvre)
{
	$width = '';
	$height = '';
	
	if (!empty($oeuvre))
	{
		preg_match('/width="(\d+)"/', $oeuvre, $resWidth);
		preg_match('/height="(\d+)"/', $oeuvre, $resHeight);
		if (!empty($resWidth[1]))
		{
			$width = $resWidth[1];
		}
		if (!empty($resHeight[1]))
		{
			$height = $resHeight[1];
		}
	}
	
	if (empty($width) && empty($height))
	{
		$style = '';
	}
	else
	{
		$style = ' style="width: ' . $width . 'px; height: ' . $height . 'px;"';
	}
	
	return $style;
}

/**
Retourne un tableau de deux éléments: le premier contient le corps de la galerie prêt à être affiché; le deuxième contient les informations sur l'image en version intermediaire, s'il y a lieu, sinon est vide.
*/
function coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement)
{
	if (preg_match('/(<div id="galerieIntermediaireTexte">.+<\/div><!-- \/galerieIntermediaireTexte -->)/', $corpsGalerie, $res))
	{
		if ($galerieLegendeEmplacement == 'sousLeContenu')
		{
			$corpsGalerie = preg_replace('/<div id="galerieIntermediaireTexte">.+<\/div><!-- \/galerieIntermediaireTexte -->/', '', $corpsGalerie);
			$tableauCorpsGalerie['texteIntermediaire'] = '<div id="galerieIntermediaireTexteSousLeContenu"><h2>' . T_("Légende de l'oeuvre") . '</h2>' . $res[1] . '</div><!-- /galerieIntermediaireTexteSousLeContenu -->';
		}
		else
		{
			$tableauCorpsGalerie['texteIntermediaire'] = '';
		}
		
		$tableauCorpsGalerie['corpsGalerie'] = $corpsGalerie;
	}
	
	else
	{
		$tableauCorpsGalerie['corpsGalerie'] = $corpsGalerie;
		$tableauCorpsGalerie['texteIntermediaire'] = '';
	}
	
	// Dans tous les cas, on supprime la div `galerieIntermediaireTexte` si elle est vide
	if (preg_match('/(<div id="galerieIntermediaireTexte"><\/div><!-- \/galerieIntermediaireTexte -->)/', $tableauCorpsGalerie['corpsGalerie']))
	{
		$tableauCorpsGalerie['corpsGalerie'] = preg_replace('/<div id="galerieIntermediaireTexte"><\/div><!-- \/galerieIntermediaireTexte -->/', '', $tableauCorpsGalerie['corpsGalerie']);
	}
	
	return $tableauCorpsGalerie;
}

/**
Retourne la légende d'une oeuvre dans le bon format.
*/
function intermediaireLegende($legende, $galerieLegendeMarkdown)
{
	if ($galerieLegendeMarkdown)
	{
		return trim(Markdown($legende));
	}
	else
	{
		return $legende;
	}
}

/**
Construit et retourne le code pour afficher une oeuvre dans la galerie.
*/
function afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, $estAccueil, $taille, $indice, $sens, $galerieHauteurVignette, $galerieTelechargeOriginal, $vignetteAvecDimensions, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown, $galerieAccueilJavascript, $galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieIconeOriginal)
{
	$infoIntermediaireNom = pathinfo($galerie[$indice]['intermediaireNom']);
	
	if ($taille == 'intermediaire')
	{
		if (!empty($galerie[$indice]['intermediaireLargeur'])
			|| !empty($galerie[$indice]['intermediaireHauteur']))
		{
			if (!empty($galerie[$indice]['intermediaireLargeur']))
			{
				$width = 'width="' . $galerie[$indice]['intermediaireLargeur'] . '"';
			}
			
			if (!empty($galerie[$indice]['intermediaireHauteur']))
			{
				$height = 'height="' . $galerie[$indice]['intermediaireHauteur'] . '"';
			}
		}
		else
		{
			list($larg, $haut) = getimagesize($urlImgSrc . '/' . $galerie[$indice]['intermediaireNom']);
			{
				$width = 'width="' . $larg . '"';
				$height = 'height="' . $haut . '"';
			}
		}
		
		if (!empty($galerie[$indice]['intermediaireAlt']))
		{
			$alt = 'alt="' . $galerie[$indice]['intermediaireAlt'] . '"';
		}
		else
		{
			// On récupère l'id de l'image
			if (!empty($galerie[$indice]['id']))
			{
				$id = $galerie[$indice]['id'];
			}
			else
			{
				$id = $galerie[$indice]['intermediaireNom'];
			}
			
			$alt = 'alt="' . T_("Oeuvre") . ' ' . $id . '"';
		}
		
		if (!empty($galerie[$indice]['intermediaireLegende']))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . intermediaireLegende($galerie[$indice]['intermediaireLegende'], $galerieLegendeMarkdown) . '</div>';
		}
		elseif ($galerieLegendeAutomatique)
		{
			if ($contenuAlt = str_replace('alt="', '', $alt))
			{
				$contenuAlt = substr($contenuAlt, 0, -1);
			}
			$legende = '<div id="galerieIntermediaireLegende">' . $contenuAlt . ' (' . sprintf(T_('%1$s&nbsp;Kio'), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom))) . ')</div>';
		}
		else
		{
			$legende = '';
		}
		
		// Si le nom de l'image au format original a été renseigné, on utilise ce nom.
		if (!empty($galerie[$indice]['originalNom']))
		{
			$originalNom = $galerie[$indice]['originalNom'];
		}
		
		// Si le nom de l'image au format original n'a pas été renseigné, on génère automatiquement un nom selon le nom de la version intermediaire de l'image.
		else
		{
			$originalNom = basename($galerie[$indice]['intermediaireNom'], '.' . $infoIntermediaireNom['extension']);
			$originalNom .= '-original.' . $infoIntermediaireNom['extension'];
		}
			
		// On vérifie maintenant si le fichier `$originalNom` existe. S'il existe, on insère un lien vers l'image.
		if (file_exists($racineImgSrc . '/' . $originalNom))
		{
			// On génère des infos utilisables plus loin
			$infoOriginalNom = pathinfo($originalNom);
			
			$lienOriginalHref = '';
			if ($galerieTelechargeOriginal && !$galerieLienOriginalJavascript && ($galerieLienOriginalEmplacement == 'legende' || $galerieLienOriginalEmplacement == 'imageLegende'))
			{
				$lienOriginalTrad = sprintf(T_("Télécharger l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)) . '&nbsp;');
				$lienOriginalHref .= $urlRacine . '/telecharger.php?fichier=';
			}
			else
			{
				$lienOriginalTrad = sprintf(T_("Afficher l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)) . '&nbsp;');
			}
			
			$lienOriginalHref .= preg_replace("|^$urlRacine/|", '', $urlImgSrc . '/' . $originalNom);
			
			if ($galerieLienOriginalJavascript && !preg_match('|\.svg$|i', $originalNom))
			{
				$relOriginal = ' rel="lightbox"';
			}
			else
			{
				$relOriginal = '';
			}
			
			$lienOriginal = '<div id="galerieLienOriginal"><a href="' . $lienOriginalHref . '"' . $relOriginal . '>' . $lienOriginalTrad . '</a></div>';
		}
		else
		{
			$lienOriginal = '';
		}
		
		// Exif
		$exif = '';
		if ($ajoutExif && typeImage($infoIntermediaireNom['extension']) == 'jpeg' && function_exists('exif_read_data'))
		{
			$tableauExif = exif_read_data($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom'], 'IFD0', 0);
			
			// Si aucune données Exif n'a été récupérée, on essaie d'en récupérer dans l'image en version originale, si elle existe et si c'est du JPG
			if (!$tableauExif && !empty($lienOriginal) && typeImage($infoOriginalNom['extension']) == 'jpeg')
			{
				$tableauExif = exif_read_data($racineImgSrc . '/' . $originalNom, 'IFD0', 0);
			}
			
			if ($tableauExif)
			{
				foreach ($infosExif as $cle => $valeur)
				{
					if ($valeur && isset($tableauExif[$cle]) && !empty($tableauExif[$cle]))
					{
						switch ($cle)
						{
							case 'Model':
								$exifTrad = T_("Modèle d'appareil photo");
								break;
							
							case 'DateTime':
								$exifTrad = T_("Date et heure");
								if (preg_match('/^\d{4}:\d{2}:\d{2} /', $tableauExif[$cle]))
								{
									$tableauExif[$cle] = preg_replace('/(\d{4}):(\d{2}):(\d{2}) /', '$1-$2-$3 ', $tableauExif[$cle]);
								}
								break;
							
							case 'ExposureTime':
								$exifTrad = T_("Durée d'exposition");
								break;
							
							case 'ISOSpeedRatings':
								$exifTrad = T_("Sensibilité ISO");
								break;
							
							case 'FNumber':
								$exifTrad = T_("Ouverture");
								break;
							
							case 'FocalLength':
								$exifTrad = T_("Distance focale");
								break;
							
							case 'Make':
								$exifTrad = T_("Fabriquant");
								break;
							
							default:
								$exifTrad = $cle;
								break;
						}
						$exif .= "<li><em>$exifTrad:</em> " . $tableauExif[$cle] . "</li>";
					}
				}
			}
			
			if (!empty($exif))
			{
				$exif = "<div id='galerieIntermediaireExif'><ul>" . $exif . "</ul></div><!-- /galerieIntermediaireExif -->";
			}
		}
		
		if (isset($lienOriginalHref) && !empty($lienOriginalHref) && ($galerieLienOriginalEmplacement == 'image' || $galerieLienOriginalEmplacement == 'imageLegende'))
		{
			if ($galerieLienOriginalJavascript && !preg_match('|\.svg$|i', $originalNom))
			{
				$relOriginal = ' rel="lightbox"';
			}
			else
			{
				$relOriginal = '';
			}
			$lienOriginalAvant = '<a href="' . $lienOriginalHref . '"' . $relOriginal . '>';
			$lienOriginalApres = '</a>';
		}
		else
		{
			$lienOriginalAvant = '';
			$lienOriginalApres = '';
		}
		
		if ($galerieIconeOriginal && isset($lienOriginalHref) && !empty($lienOriginalHref))
		{
			if (file_exists($racine . '/site/fichiers/agrandir.png'))
			{
				$galerieIconeOriginalSrc = $urlRacine . '/site/fichiers/agrandir.png';
			}
			else
			{
				$galerieIconeOriginalSrc = $urlRacine . '/fichiers/agrandir.png';
			}
			
			$imgLienOriginal = '<div id="galerieIconeOriginal">' . $lienOriginalAvant . '<img src="' . $galerieIconeOriginalSrc . '" alt="' . str_replace('&nbsp;', ' ', $lienOriginalTrad) . '" width="22" height="22" />' . $lienOriginalApres . '</div><!-- /galerieIconeOriginal -->' . "\n";
		}
		else
		{
			$imgLienOriginal = '';
		}
		
		if ($galerieLegendeEmplacement == 'haut' || $galerieLegendeEmplacement == 'sousLeContenu')
		{
			return '<div id="galerieIntermediaireTexte">' . $legende . $exif . $lienOriginal . "</div><!-- /galerieIntermediaireTexte -->\n" . '<div id="galerieIntermediaireImg">' . $lienOriginalAvant . '<img src="' . $urlImgSrc . '/' . $galerie[$indice]['intermediaireNom'] . '"' . " $width $height $alt />" . $lienOriginalApres . "</div><!-- /galerieIntermediaireImg -->\n" . $imgLienOriginal;
		}
		elseif ($galerieLegendeEmplacement == 'bas')
		{
			return '<div id="galerieIntermediaireImg">' . $lienOriginalAvant . '<img src="' . $urlImgSrc . '/' . $galerie[$indice]['intermediaireNom'] . '"' . " $width $height $alt />" . $lienOriginalApres . "</div><!-- /galerieIntermediaireImg -->\n" . $imgLienOriginal . '<div id="galerieIntermediaireTexte">' . $legende . $exif . $lienOriginal . "</div><!-- /galerieIntermediaireTexte -->\n";
		}
	}

	elseif ($taille == 'vignette')
	{
		if ($galerieNavigation == 'fleches' && $sens != 'aucun')
		{
			$class = ' galerieFleche';
			$width = 'width="80"';
			$height = 'height="80"';
			if (file_exists($racine . '/site/fichiers/' . $sens . '.png'))
			{
				$src = 'src="' . $urlRacine . '/site/fichiers/' . $sens . '.png"';
			}
			else
			{
				$src = 'src="' . $urlRacine . '/fichiers/' . $sens . '.png"';
			}
		}

		elseif (($galerieNavigation == 'fleches' && $sens == 'aucun')
			|| $galerieNavigation == 'vignettes')
		{
			// Si le nom de la vignette a été renseigné, on prend pour acquis que le fichier existe avec ce nom. On assigne donc une valeur à l'attribut `src`.
			if (!empty($galerie[$indice]['vignetteNom']))
			{
				$src = 'src="' . $urlImgSrc . '/' . $galerie[$indice]['vignetteNom'] . '"';
			}
			else
			{
				// Si le nom de la vignette n'a pas été renseigné, on génère un nom automatique selon le nom de la version intermediaire de l'image.
				$vignetteNom = basename($galerie[$indice]['intermediaireNom'], '.' . $infoIntermediaireNom['extension']);
				$vignetteNom .= '-vignette.' . $infoIntermediaireNom['extension'];
				
				// On vérifie si un fichier existe avec ce nom.
				// Si oui, on assigne une valeur à l'attribut `src`. 
				if (file_exists($racineImgSrc . '/' . $vignetteNom))
				{
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
				// Sinon, on génère une vignette avec gd
				else
				{
					// On trouve le type de l'image dans le but d'utiliser la bonne fonction php
					$type = typeImage($infoIntermediaireNom['extension']);
					
					// Image intermediaire
					switch ($type)
					{
						case 'gif':
							$imageIntermediaire = imagecreatefromgif($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom']);
							break;
						
						case 'jpeg':
							$imageIntermediaire = imagecreatefromjpeg($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom']);
							break;
						
						case 'png':
							$imageIntermediaire = imagecreatefrompng($racineImgSrc . '/' . $galerie[$indice]['intermediaireNom']);
							imagealphablending($imageIntermediaire, true);
							imagesavealpha($imageIntermediaire, true);
							break;
					}
					
					// Dimensions de l'image intermediaire
					$imageIntermediaireLargeur = imagesx($imageIntermediaire);
					$imageIntermediaireHauteur = imagesy($imageIntermediaire);
					
					// On trouve les futures dimensions de la vignette
					$imageVignetteHauteur = $galerieHauteurVignette;
					$imageVignetteLargeur = ($imageVignetteHauteur / $imageIntermediaireHauteur) * $imageIntermediaireLargeur;
					
					// On crée une vignette vide
					$imageVignette = imagecreatetruecolor($imageVignetteLargeur, $imageVignetteHauteur);
					
					// On crée la vignette à partir de l'image intermediaire
					imagecopyresampled($imageVignette, $imageIntermediaire, 0, 0, 0, 0, $imageVignetteLargeur, $imageVignetteHauteur, $imageIntermediaireLargeur, $imageIntermediaireHauteur);
					
					// On enregistre la vignette
					switch ($type)
					{
						case 'gif':
							imagegif($imageVignette, $racineImgSrc . '/' . $vignetteNom);
							break;
						
						case 'jpeg':
							imagejpeg($imageVignette, $racineImgSrc . '/' . $vignetteNom, $qualiteJpg);
							break;
						
						case 'png':
							imagepng($imageVignette, $racineImgSrc . '/' . $vignetteNom, 9);
							break;
					}
					
					// On assigne l'attribut `src`
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
			}
			
			if ($vignetteAvecDimensions)
			{
				if (!empty($galerie[$indice]['vignetteLargeur'])
					|| !empty($galerie[$indice]['vignetteHauteur']))
				{
					if (!empty($galerie[$indice]['vignetteLargeur']))
					{
						$width = 'width="' . $galerie[$indice]['vignetteLargeur'] . '"';
					}
				
					if (!empty($galerie[$indice]['vignetteHauteur']))
					{
						$height = 'height="' . $galerie[$indice]['vignetteHauteur'] . '"';
					}
				}
				else
				{
					list($larg, $haut) = getimagesize($urlImgSrc . '/' . $vignetteNom);
					{
						$width = 'width="' . $larg . '"';
						$height = 'height="' . $haut . '"';
					}
				}
			}
			else
			{
				$width = '';
				$height = '';
			}
		}
		
		// On récupère l'id de l'image
		if (!empty($galerie[$indice]['id']))
		{
			$id = $galerie[$indice]['id'];
		}
		else
		{
			$id = $galerie[$indice]['intermediaireNom'];
		}
		
		if (!empty($galerie[$indice]['vignetteAlt']))
		{
			$alt = 'alt="' . $galerie[$indice]['vignetteAlt'] . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Oeuvre %1\$s"), $id) . '"';
		}
		
		if ($estAccueil)
		{
			$classAccueil = 'Accueil ';
		}
		else
		{
			$classAccueil = ' ';
		}
		
		if ($estAccueil && $galerieAccueilJavascript)
		{
			if (!empty($galerie[$indice]['intermediaireLegende']))
			{
				$title = ' title="' . preg_replace(array ('/</', '/>/', '/"/'), array ('&lt;', '&gt;', "'"), $galerie[$indice]['intermediaireLegende']) . '"';
			}
			else
			{
				$title = '';
			}
			
			$aHref = '<a href="' . $urlImgSrc . '/' . $galerie[$indice]['intermediaireNom'] . '" rel="lightbox-galerie"' . $title . '>';
		}
		else
		{
			
			$aHref = '<a href="' . nomFichierGalerie() . '?oeuvre=' . $id . '">';
		}
		
		// On s'assure que la variable $class existe (pour éviter un avertissement).
		if (!isset($class))
		{
			$class = '';
		}
		return '<div class="galerieNavigation' . $classAccueil . $class . '">' . $aHref . '<img ' . "$src $width $height $alt /></a></div>";
	}
	
	else
	{
		return;
	}
}

/**
Construit et retourne un tableau associatif à partir d'un fichier texte dont chaque ligne a la forme `clé=valeur`.
*/
function tableauAssociatif($fichierTexte)
{
	$tableau = array ();
	
	$fic = @fopen($fichierTexte, 'r');
	if ($fic)
	{
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			if (strstr($ligne, '='))
			{
				list($cle, $valeur) = explode('=', $ligne, 2);
				$tableau[$cle] = $valeur;
			}
		}
		
		fclose($fic);
	}
	
	return $tableau;
}

/**
Construit et retourne un tableau PHP à partir d'un fichier texte listant des images, dont chacun de leurs champs se trouve seul sur une ligne avec la syntaxe `clé=valeur`. Chaque image est séparée par une ligne ne contenant que `#IMG`. Si `$exclure` vaut TRUE, ne tient pas compte des images ayant un champ `exclure=oui`.
*/
function construitTableauGalerie($fichierTexte, $exclure = FALSE)
{
	$galerie = array ();
	
	$fic = @fopen($fichierTexte, 'r');
	if ($fic)
	{
		$galerieTemp = array ();
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			if (strstr($ligne, '='))
			{
				list($cle, $valeur) = explode('=', $ligne, 2);
				$galerieTemp[$cle] = $valeur;
			}
			elseif ($ligne == '#IMG')
			{
				if (!$exclure || !(isset($galerieTemp['exclure']) && $galerieTemp['exclure'] == 'oui'))
				{
					$galerie[] = $galerieTemp;
				}
				
				unset($galerieTemp);
			}
		}
		
		fclose($fic);
	}
	
	return $galerie;
}

/**
Ajoute `-vignette` au nom d'un fichier, par exemple `fichier.extension` devient `fichier-vignette.extension`.
*/
function nomSuffixeVignette($fichier)
{
	$info = pathinfo($fichier);
	$fichierVignette = basename($fichier, '.' . $info['extension']);
	$fichierVignette .= '-vignette.' . $info['extension'];
	
	return $fichierVignette;
}

/**
Retourne le contenu de l'attribut `action` du formulaire de contact.
*/
function actionFormContact($decouvrir)
{
	$action = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
	if ($decouvrir)
	{
		$action .= '#formulaireFaireDecouvrir';
	}
	
	return $action;
}

/**
Retourne le texte supplémentaire d'une page pour le message envoyé par le module «Faire découvrir».
*/
function decouvrirSupplementPage($description, $baliseTitle)
{
	$messageDecouvrirSupplement = '';
	
	if (!empty($description))
	{
		$messageDecouvrirSupplement .= $description;
	}
	elseif (!empty($baliseTitle))
	{
		$messageDecouvrirSupplement .= $baliseTitle;
	}
	
	if (!empty($messageDecouvrirSupplement))
	{
		$messageDecouvrirSupplement = "<p style='font-style: italic;'>$messageDecouvrirSupplement</p>";
	}
	
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . T_("Consultez cette page!") . '</a> ' . T_("En espérant qu'elle vous intéresse!") . '</p>';
	
	return $messageDecouvrirSupplement;
}

/**
Retourne le texte supplémentaire d'une oeuvre pour le message envoyé par le module «Faire découvrir».
*/
function decouvrirSupplementOeuvre($urlRacine, $idGalerie, $oeuvre, $galerieLegendeMarkdown)
{
	$messageDecouvrirSupplement = '';
	
	if (isset($oeuvre['vignetteNom']) && !empty($oeuvre['vignetteNom']))
	{
		$vignetteNom = $oeuvre['vignetteNom'];
	}
	else
	{
		$vignetteNom = nomSuffixeVignette($oeuvre['intermediaireNom']);
	}
	
	if (isset($oeuvre['vignetteAlt']) && !empty($oeuvre['vignetteAlt']))
	{
		$vignetteAlt = $oeuvre['vignetteAlt'];
	}
	elseif (isset($oeuvre['intermediaireAlt']) && !empty($oeuvre['intermediaireAlt']))
	{
		$vignetteAlt = $oeuvre['intermediaireAlt'];
	}
	else
	{
		$vignetteAlt = '';
	}
	
	$messageDecouvrirSupplement .= "<p style='text-align: center;'><img src='$urlRacine/site/fichiers/galeries/$idGalerie/" . $vignetteNom . "' alt='$vignetteAlt' /></p>";
	
	if (isset($oeuvre['intermediaireLegende']) && !empty($oeuvre['intermediaireLegende']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($oeuvre['intermediaireLegende'], $galerieLegendeMarkdown);
	}
	elseif (isset($oeuvre['intermediaireAlt']) && !empty($oeuvre['intermediaireAlt']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($oeuvre['intermediaireAlt'], $galerieLegendeMarkdown);
	}
	elseif (isset($oeuvre['vignetteAlt']) && !empty($oeuvre['vignetteAlt']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($oeuvre['vignetteAlt'], $galerieLegendeMarkdown);
	}
	elseif (isset($oeuvre['pageIntermediaireDescription']) && !empty($oeuvre['pageIntermediaireDescription']))
	{
		$messageDecouvrirSupplement .= $oeuvre['pageIntermediaireDescription'];
	}
	elseif (isset($oeuvre['pageIntermediaireBaliseTitle']) && !empty($oeuvre['pageIntermediaireBaliseTitle']))
	{
		$messageDecouvrirSupplement .= $oeuvre['pageIntermediaireBaliseTitle'];
	}
	
	$messageDecouvrirSupplement = "<div style='font-style: italic; text-align: center;'>$messageDecouvrirSupplement</div>";
	
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . T_("Voyez l'oeuvre en plus grande taille!") . '</a> ' . T_("En espérant qu'elle vous intéresse!") . '</p>';
	
	return $messageDecouvrirSupplement;
}

/**
Si le paramètre optionnel vaut TRUE, retourne un tableau contenant l'URL de la page en cours sans la variable GET `action=faireDecouvrir` (si elle existe) ainsi qu'un boléen informant de la présence ou non d'autres variables GET (peu importe lesquelles) après suppression de `action=faireDecouvrir`; sinon retourne une chaîne de caractères équivalant au premier élément du tableau retourné si le paramètre optionnel vaut TRUE.
*/
function urlPageSansDecouvrir($retourneTableau = FALSE)
{
	$urlPageSansDecouvrir = array ();
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
	if (strstr($url, '?action=faireDecouvrir&'))
	{
		$urlPageSansDecouvrir[0] = str_replace('?action=faireDecouvrir&', '?', $url);
	}
	elseif (preg_match('/\?action=faireDecouvrir$/', $url))
	{
		$urlPageSansDecouvrir[0] = str_replace('?action=faireDecouvrir', '', $url);
	}
	elseif (strstr($url, '&action=faireDecouvrir'))
	{
		$urlPageSansDecouvrir[0] = str_replace('&action=faireDecouvrir', '', $url);
	}
	else
	{
		$urlPageSansDecouvrir[0] = $url;
	}
	
	if ($retourneTableau)
	{
		if (strstr($url, '?'))
		{
			$urlPageSansDecouvrir[1] = TRUE;
		}
		else
		{
			$urlPageSansDecouvrir[1] = FALSE;
		}
		
		return $urlPageSansDecouvrir;
	}
	else
	{
		return $urlPageSansDecouvrir[0];
	}
}

/**
Retourne l'URL de la page en cours avec la variable GET `action=faireDecouvrir`.
*/
function urlPageAvecDecouvrir()
{
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
	if (preg_match('/(\?|&)action=faireDecouvrir/', $url))
	{
		return $url . '#formulaireFaireDecouvrir';
	}
	elseif (strstr($url, '?'))
	{
		return "$url&action=faireDecouvrir#formulaireFaireDecouvrir";
	}
	else
	{
		return "$url?action=faireDecouvrir#formulaireFaireDecouvrir";
	}
}

/**
Retourne l'id d'une oeuvre d'une galerie.
*/
function idOeuvre($oeuvre)
{
	return !empty($oeuvre['id']) ? $oeuvre['id'] : $oeuvre['intermediaireNom'];
}

/**
Retourne un tableau listant les oeuvres d'une galerie, chaque oeuvre constituant elle-même un tableau contenant les informations nécessaires à la création d'un fichier RSS.
*/
function rssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie)
{
	$galerie = construitTableauGalerie("$racine/site/inc/galerie-$idGalerie.pc", TRUE);
	$itemsFlux = array ();
	
	foreach ($galerie as $oeuvre)
	{
		$id = idOeuvre($oeuvre);
		$title = sprintf(T_("Oeuvre %1\$s"), $id);
		$cheminOeuvre = "$racine/site/fichiers/galeries/$idGalerie/" . $oeuvre['intermediaireNom'];
		$urlOeuvre = "$urlRacine/site/fichiers/galeries/$idGalerie/" . $oeuvre['intermediaireNom'];
		$urlGalerieOeuvre = "$urlGalerie?oeuvre=$id";
		
		if (!empty($oeuvre['pageIntermediaireDescription']))
		{
			$description = $oeuvre['pageIntermediaireDescription'];
		}
		elseif (!empty($oeuvre['intermediaireLegende']))
		{
			$description = $oeuvre['intermediaireLegende'];
		}
		else
		{
			$description = $title;
		}
		
		if (!empty($oeuvre['intermediaireLargeur']))
		{
			$width = $oeuvre['intermediaireLargeur'];
		}
		else
		{
			list($width, $height) = getimagesize($cheminOeuvre);
		}
		
		if (!empty($oeuvre['intermediaireHauteur']))
		{
			$height = $oeuvre['intermediaireHauteur'];
		}
		
		if (!empty($oeuvre['intermediaireAlt']))
		{
			$alt = $oeuvre['intermediaireAlt'];
		}
		else
		{
			$alt = $title;
		}
		
		if (!empty($oeuvre['originalNom']))
		{
			$urlOriginal = "$urlRacine/site/fichiers/galeries/$idGalerie/" . $oeuvre['originalNom'];
		}
		else
		{
			$infoOriginal = pathinfo($oeuvre['intermediaireNom']);
			$nomOriginal = basename($oeuvre['intermediaireNom'], '.' . $infoOriginal['extension']);
			$nomOriginal .= '-original.' . $infoOriginal['extension'];
			if (file_exists("$racine/site/fichiers/galeries/$idGalerie/$nomOriginal"))
			{
				$urlOriginal = "$urlRacine/site/fichiers/galeries/$idGalerie/$nomOriginal";
			}
			else
			{
				$urlOriginal = '';
			}
		}
		
		if (!empty($urlOriginal))
		{
			$msgOriginal = "\n<p><a href='$urlOriginal'>" . T_("Lien vers l'oeuvre au format original.") . "</a></p>";
		}
		else
		{
			$msgOriginal = '';
		}
		
		$description = securiseTexte("<div>$description</div>\n<p><img src='$urlOeuvre' width='$width' height='$height' alt='$alt' /></p>$msgOriginal");
		$pubDate = fileatime($cheminOeuvre);
		
		$itemsFlux[] = array (
			"title" => $title,
			"link" => $urlGalerieOeuvre,
			"guid" => $urlGalerieOeuvre,
			"description" => $description,
			"pubDate" => $pubDate,
		);
	}
	
	return $itemsFlux;
}

/**
Retourne un tableau d'un élément représentant une page du site, cet élément étant lui-même un tableau contenant les informations nécessaires à la création d'un fichier RSS.
*/
function rssPageTableauBrut($cheminPage, $urlPage)
{
	$itemFlux = array ();
	
	preg_match('|<title>(.+)</title>.+<div id="interieurContenu">(.+)</div><!-- /interieurContenu -->|s', file_get_contents($urlPage), $res);
	$pubDate = fileatime($cheminPage);
	
	$title = securiseTexte($res[1]);
	if (empty($title))
	{
		$title = $urlPage;
	}
	
	$itemFlux[] = array (
		"title" => $title,
		"link" => $urlPage,
		"guid" => $urlPage,
		"description" => securiseTexte($res[2]),
		"pubDate" => $pubDate,
	);
	
	return $itemFlux;
}

/**
Retourne le tableau `$itemsFlux` trié selon la date de dernière modification et contenant au maximum le nombre d'items précisé dans la configuration.
*/
function rssTableauFinal($itemsFlux, $nbreItemsFlux)
{
	foreach ($itemsFlux as $cle => $valeur)
	{
		$itemsFluxTitle[$cle] = $valeur['title'];
		$itemsFluxLink[$cle] = $valeur['link'];
		$itemsFluxGuid[$cle] = $valeur['guid'];
		$itemsFluxDescription[$cle] = $valeur['description'];
		$itemsFluxPubDate[$cle] = $valeur['pubDate'];
	}
	
	array_multisort($itemsFluxPubDate, SORT_DESC, $itemsFlux);
	
	$itemsFlux = array_slice($itemsFlux, 0, $nbreItemsFlux);
	
	return $itemsFlux;
}

/**
Retourne le contenu d'un fichier RSS.
*/
function rss($idGalerie, $baliseTitleComplement, $url, $itemsFlux, $estGalerie)
{
	$contenuRss = '';
	$contenuRss .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$contenuRss .= '<rss version="2.0">' . "\n";
	$contenuRss .= "\t<channel>\n";
	
	if ($idGalerie)
	{
		// Page individuelle d'une galerie
		$contenuRss .= "\t\t<title>" . sprintf(T_("Galerie %1\$s | %2\$s"), $idGalerie, $baliseTitleComplement) . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Derniers ajouts à la galerie %1\$s"), $idGalerie) . "</description>\n\n";
	}
	elseif ($estGalerie)
	{
		// Toutes les galeries
		$contenuRss .= "\t\t<title>" . T_("Galeries") . ' | ' . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . T_("Derniers ajouts aux galeries") . ' | ' . $baliseTitleComplement . "</description>\n\n";
	}
	else
	{
		// Tout le site
		$contenuRss .= "\t\t<title>" . T_("Dernières publications") . ' | ' . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . T_("Dernières publications") . ' | ' . $baliseTitleComplement . "</description>\n\n";
	}
	
	foreach ($itemsFlux as $itemFlux)
	{
		$contenuRss .= "\t\t<item>\n";
		$contenuRss .= "\t\t\t<title>" . $itemFlux['title'] . "</title>\n";
		$contenuRss .= "\t\t\t<link>" . $itemFlux['link'] . "</link>\n";
		$contenuRss .= "\t\t\t" . '<guid isPermaLink="true">' . $itemFlux['guid'] . "</guid>\n";
		$contenuRss .= "\t\t\t<description>" . $itemFlux['description'] . "</description>\n";
		if ($itemFlux['pubDate'])
		{
			$contenuRss .= "\t\t\t<pubDate>" . date('r', $itemFlux['pubDate']) . "</pubDate>\n";
		}
		$contenuRss .= "\t\t</item>\n\n";
	}
	
	$contenuRss .= "\t</channel>\n";
	$contenuRss .= '</rss>';
	
	return $contenuRss;
}

/**
Retourne le code pour un lien vers le fichier du flux RSS en question.
*/
function lienRss($urlFlux, $idGalerie, $estGalerie)
{
	if ($idGalerie)
	{
		$description = sprintf(T_("RSS de la galerie %1\$s"), $idGalerie);
	}
	elseif ($estGalerie)
	{
		$description = T_("RSS de toutes les galeries");
	}
	else
	{
		$description = T_("RSS global du site");
	}
	
	return "<a href=\"$urlFlux\">$description</a>";
}

/**
Vérifie si le cache d'un fichier expire.
*/
function cacheExpire($fichier, $dureeCache)
{
	if (time() - fileatime($fichier) > $dureeCache)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/**
Vérifie si le dossier de cache existe. S'il n'existe pas, le dossier est créé, sinon rien n'est fait.
*/
function creeDossierCache($racine)
{
	if (!file_exists("$racine/site/cache"))
	{
		mkdir("$racine/site/cache", 0755, TRUE);
	}
	
	return;
}

/**
Inclut tout ce qu'il faut pour utiliser php-gettext comme outil de traduction des pages.
*/
function phpGettext($racine, $langue)
{
	if (!defined('LC_MESSAGES'))
	{
		define('LC_MESSAGES', 5);
	}
	require_once $racine . '/inc/php-gettext/gettext.inc';
	$locale = $langue;
	// Palliatif à un bogue sur les serveurs de Koumbit. Aucune idée du problème. On dirait que 9 fois sur 10, php-gettext passe le relais au gettext par défaut de PHP, et que si la locale est seulement 'en', elle n'existe pas sur le serveur d'hébergement, donc la traduction ne fonctionne pas.
	if ($locale == 'en')
	{
		$locale = 'en_US';
	}
	T_setlocale(LC_MESSAGES, $locale);
	$domain = 'squeletml';
	T_bindtextdomain($domain, $racine . '/locale');
	T_bind_textdomain_codeset($domain, 'UTF-8');
	T_textdomain($domain);
	return;
}

/**
Retourne la langue de la page courante.
*/
function langue($langue)
{
	if ($langue == 'navigateur')
	{
		$langue = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$langue = strtolower(substr(chop($langue[0]), 0, 2));
		
		return $langue;
	}
	
	return isset($langue[1]) ? $langue[1] : $langue[0];
}

/**
Conversion des octets en Kio.
*/
function octetsVersKio($octets)
{
	return number_format($octets / 1024, 1, ',', '');
}

/**
Conversion des octets en Mio.
*/
function octetsVersMio($octets)
{
	return number_format($octets / 1048576, 1, ',', '');
}

/**
Fonction `in_array()` multidimensionel.
*/
function in_array_multi($recherche, $tableau)
{
	foreach ($tableau as $pos => $val)
	{
		if (is_array($val))
		{
			if (in_array_multi($recherche, $val))
			{
				return 1;
			}
		}
		else
		{
			if ($val == $recherche)
			{
				return 1;
			}
		}
	}
}

?>
