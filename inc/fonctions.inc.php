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
	Les types possibles sont: css, cssltIE7, cssIE7, javascript, favicon
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
		// Suppression de la feuille de style par défaut
		unset($fichiers[0]);
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
function construitClass($estAccueil, $colonneAgauche, $idGalerie)
{
	$class = '';
	
	if ($estAccueil)
	{
		$class .= 'accueil ';
	}
	
	if ($colonneAgauche)
	{
		$class .= 'colonneAgauche ';
	}
	else
	{
		$class .= 'colonneAgaucheFalse ';
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
function vignetteTatouage($paragraphe, $sens, $racine, $racineImgSrc, $urlImgSrc)
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
Retourne un tableau de deux éléments: le premier contient le corps de la galerie prêt à être affiché; le deuxième contient les informations sur l'image en version grande, s'il y a lieu, sinon est vide.
*/
function coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement)
{
	if (preg_match('/(<div id="galerieGrandeTexte">.+<\/div><!-- \/galerieGrandeTexte -->)/', $corpsGalerie, $res))
	{
		if ($galerieLegendeEmplacement == 'sousLeContenu')
		{
			$corpsGalerie = preg_replace('/<div id="galerieGrandeTexte">.+<\/div><!-- \/galerieGrandeTexte -->/', '', $corpsGalerie);
			$tableauCorpsGalerie['texteGrande'] = '<div id="galerieGrandeTexteSousLeContenu"><h2>' . T_("Légende de l'oeuvre") . '</h2>' . $res[1] . '</div><!-- /galerieGrandeTexteSousLeContenu -->';
		}
		else
		{
			$tableauCorpsGalerie['texteGrande'] = '';
		}
		
		$tableauCorpsGalerie['corpsGalerie'] = $corpsGalerie;
	}
	
	else
	{
		$tableauCorpsGalerie['corpsGalerie'] = $corpsGalerie;
		$tableauCorpsGalerie['texteGrande'] = '';
	}
	
	// Dans tous les cas, on supprime la div `galerieGrandeTexte` si elle est vide
	if (preg_match('/(<div id="galerieGrandeTexte"><\/div><!-- \/galerieGrandeTexte -->)/', $tableauCorpsGalerie['corpsGalerie']))
	{
		$tableauCorpsGalerie['corpsGalerie'] = preg_replace('/<div id="galerieGrandeTexte"><\/div><!-- \/galerieGrandeTexte -->/', '', $tableauCorpsGalerie['corpsGalerie']);
	}
	
	return $tableauCorpsGalerie;
}

/**
Construit et retourne le code pour afficher une oeuvre dans la galerie.
*/
function afficheOeuvre($racine, $urlRacine, $racineImgSrc, $urlImgSrc, $galerie, $galerieNavigation, $estAccueil, $taille, $indice, $sens, $galerieHauteurVignette, $galerieTelechargeOrig, $vignetteAvecDimensions, $galerieLegendeAutomatique, $galerieLegendeEmplacement, $qualiteJpg, $ajoutExif, $infosExif, $galerieLegendeMarkdown)
{
	$infoGrandeNom = pathinfo($galerie[$indice]['grandeNom']);
	
	if ($taille == 'grande')
	{
		if (!empty($galerie[$indice]['grandeLargeur'])
			|| !empty($galerie[$indice]['grandeHauteur']))
		{
			if (!empty($galerie[$indice]['grandeLargeur']))
			{
				$width = 'width="' . $galerie[$indice]['grandeLargeur'] . '"';
			}
			
			if (!empty($galerie[$indice]['grandeHauteur']))
			{
				$height = 'height="' . $galerie[$indice]['grandeHauteur'] . '"';
			}
		}
		else
		{
			list($larg, $haut) = getimagesize($urlImgSrc . '/' . $galerie[$indice]['grandeNom']);
			{
				$width = 'width="' . $larg . '"';
				$height = 'height="' . $haut . '"';
			}
		}
		
		if (!empty($galerie[$indice]['grandeAlt']))
		{
			$alt = 'alt="' . $galerie[$indice]['grandeAlt'] . '"';
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
				$id = $galerie[$indice]['grandeNom'];
			}
			
			$alt = 'alt="' . T_("Oeuvre") . ' ' . $id . '"';
		}
		
		if (!empty($galerie[$indice]['grandeLegende']))
		{
			if ($galerieLegendeMarkdown)
			{
				$galerie[$indice]['grandeLegende'] = trim(Markdown($galerie[$indice]['grandeLegende']));
			}
			$legende = '<div id="galerieGrandeLegende">' . $galerie[$indice]['grandeLegende'] . '</div>';
		}
		elseif ($galerieLegendeAutomatique)
		{
			if ($contenuAlt = str_replace('alt="', '', $alt))
			{
				$contenuAlt = substr($contenuAlt, 0, -1);
			}
			$legende = '<div id="galerieGrandeLegende">' . $contenuAlt . ' (' . sprintf(T_('%1$s&nbsp;Kio'), octetsVersKio(filesize($racineImgSrc . '/' . $origNom))) . ')</div>';
		}
		else
		{
			$legende = '';
		}
		
		// Si le nom de l'image au format original a été renseigné, on utilise ce nom.
		if (!empty($galerie[$indice]['origNom']))
		{
			$origNom = $galerie[$indice]['origNom'];
		}
		
		// Si le nom de l'image au format original n'a pas été renseigné, on génère automatiquement un nom selon le nom de la version grande de l'image.
		else
		{
			$origNom = basename($galerie[$indice]['grandeNom'], '.' . $infoGrandeNom['extension']);
			$origNom .= '-orig.' . $infoGrandeNom['extension'];
		}
			
		// On vérifie maintenant si le fichier $origNom existe. S'il existe, on insère un lien vers l'image.
		if (file_exists($racineImgSrc . '/' . $origNom))
		{
			// On génère des infos utilisables plus loin
			$infoOrigNom = pathinfo($origNom);
			
			$lienOrigHref = '';
			if ($galerieTelechargeOrig)
			{
				$lienOrigTrad = sprintf(T_("Télécharger l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $origNom)) . '&nbsp;');
				$lienOrigHref .= $urlRacine . '/telecharger.php?url=';
			}
			else
			{
				$lienOrigTrad = sprintf(T_("Afficher l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $origNom)) . '&nbsp;');
			}
			$lienOrigHref .= $urlImgSrc . '/' . $origNom;
			
			$lienOrig = '<div id="galerieLienOrig"><a href="' . $lienOrigHref . '">' . $lienOrigTrad . '</a></div>';
		}
		else
		{
			$lienOrig = '';
		}
		
		// Exif
		if ($ajoutExif && typeImage($infoGrandeNom['extension']) == 'jpeg' && function_exists('exif_read_data'))
		{
			$tableauExif = exif_read_data($racineImgSrc . '/' . $galerie[$indice]['grandeNom'], 'IFD0', 0);
			
			// Si aucune données Exif n'a été récupérée, on essaie d'en récupérer dans l'image en version originale, si elle existe et si c'est du JPG
			if (!$tableauExif && !empty($lienOrig) && typeImage($infoOrigNom['extension']) == 'jpeg')
			{
				$tableauExif = exif_read_data($racineImgSrc . '/' . $origNom, 'IFD0', 0);
			}
			
			if ($tableauExif)
			{
				$exif = '';
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
				$exif = "<div id='galerieGrandeExif'><ul>" . $exif . "</ul></div><!-- /galerieGrandeExif -->";
			}
		}
		else
		{
			$exif = '';
		}
		
		if ($galerieLegendeEmplacement == 'haut' || $galerieLegendeEmplacement == 'sousLeContenu')
		{
			return '<div id="galerieGrandeTexte">' . $legende . $exif . $lienOrig . "</div><!-- /galerieGrandeTexte -->\n" . '<div id="galerieGrandeImg"><img src="' . $urlImgSrc . '/' . $galerie[$indice]['grandeNom'] . '"' . " $width $height $alt /></div><!-- /galerieGrandeImg -->\n";
		}
		elseif ($galerieLegendeEmplacement == 'bas')
		{
			return '<div id="galerieGrandeImg"><img src="' . $urlImgSrc . '/' . $galerie[$indice]['grandeNom'] . '"' . " $width $height $alt /></div><!-- /galerieGrandeImg -->\n" . '<div id="galerieGrandeTexte">' . $legende . $exif . $lienOrig . "</div><!-- /galerieGrandeTexte -->\n";
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
				// Si le nom de la vignette n'a pas été renseigné, on génère un nom automatique selon le nom de la version grande de l'image.
				$vignetteNom = basename($galerie[$indice]['grandeNom'], '.' . $infoGrandeNom['extension']);
				$vignetteNom .= '-vignette.' . $infoGrandeNom['extension'];
				
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
					$type = typeImage($infoGrandeNom['extension']);
					
					// Grande image
					switch ($type)
					{
						case 'gif':
							$imageGrande = imagecreatefromgif($racineImgSrc . '/' . $galerie[$indice]['grandeNom']);
							break;
						
						case 'jpeg':
							$imageGrande = imagecreatefromjpeg($racineImgSrc . '/' . $galerie[$indice]['grandeNom']);
							break;
						
						case 'png':
							$imageGrande = imagecreatefrompng($racineImgSrc . '/' . $galerie[$indice]['grandeNom']);
							imagealphablending($imageGrande, true);
							imagesavealpha($imageGrande, true);
							break;
					}
					
					// Dimensions de la grande image
					$imageGrandeLargeur = imagesx($imageGrande);
					$imageGrandeHauteur = imagesy($imageGrande);
					
					// On trouve les futures dimensions de la vignette
					$imageVignetteHauteur = $galerieHauteurVignette;
					$imageVignetteLargeur = ($imageVignetteHauteur / $imageGrandeHauteur) * $imageGrandeLargeur;
					
					// On crée une vignette vide
					$imageVignette = imagecreatetruecolor($imageVignetteLargeur, $imageVignetteHauteur);
					
					// On crée la vignette à partir de la grande image
					imagecopyresampled($imageVignette, $imageGrande, 0, 0, 0, 0, $imageVignetteLargeur, $imageVignetteHauteur, $imageGrandeLargeur, $imageGrandeHauteur);
					
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
			$id = $galerie[$indice]['grandeNom'];
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
		
		// On s'assure que la variable $class existe (pour éviter un avertissement).
		if (!isset($class))
		{
			$class = '';
		}
		return '<div class="galerieNavigation' . $classAccueil . $class . '"><a href="' . nomFichierGalerie() . '?oeuvre=' . $id . '"><img ' . "$src $width $height $alt /></a></div>";
	}
	
	else
	{
		return;
	}
}

/**
Construit et retourne un tableau PHP à partir d'un fichier texte dont la syntaxe est clé=valeur.
*/
function construitTableauGalerie($fichierTexte)
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
				list($cle, $valeur) = split('=', $ligne, 2);
				$galerieTemp[$cle] = $valeur;
			}
			elseif ($ligne == '__IMG__')
			{
				$galerie[] = $galerieTemp;
				unset($galerieTemp);
			}
		}
		
		fclose($fic);
	}
	
	return $galerie;
}

/**
Inclut tout ce qu'il faut pour utiliser php-gettext comme outil de traduction des pages.
*/
function phpGettext($racine, $langue)
{
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
