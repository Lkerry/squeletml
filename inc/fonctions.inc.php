<?php
/*
Retourne le lien vers l'accueil de la langue de la page. Si le lien n'a pas été trouvé, retourne une chaîne vide.
*/
function accueil($accueil, $langues)
{
	foreach ($langues as $langue)
	{
		if (!empty($langue) && array_key_exists($langue, $accueil))
		{
			return $accueil[$langue];
		}
	}
	
	return '';
}

/*
Retourne le contenu de l'attribut `action` du formulaire de contact.
*/
function actionFormContact($decouvrir)
{
	$action = url();
	
	if ($decouvrir)
	{
		$action .= '#formulaireFaireDecouvrir';
	}
	
	return $action;
}

/*
Retourne un tableau contenant les fichiers à inclure au début du script.
*/
function aInclureDebut($racine)
{
	$fichiers = array ();
	$fichiers[] = $racine . '/inc/mimedetect/file.inc.php';
	$fichiers[] = $racine . '/inc/mimedetect/mimedetect.inc.php';
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	$fichiers[] = $racine . '/inc/simplehtmldom/simple_html_dom.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/common.inc.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/filter.inc.php';
	
	if (file_exists($racine . '/site/inc/fonctions.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/fonctions.inc.php';
	}
	
	foreach (cheminsInc($racine, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	foreach (cheminsInc($racine, 'constantes') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/*
Retourne les annexes de la documentation.
*/
function annexesDocumentation($racineAdmin)
{
	$racine = dirname($racineAdmin);
	$texte = '';
	$texte .= "\n\n## " . T_("Annexes") . "\n\n";
	
	$texte .= '### ' . T_("Contenu du fichier de configuration de Squeletml") . "\n\n";
	
	$texte .= T_("Voici le contenu du fichier de configuration, largement commenté, et constituant ainsi un bon complément à la documentation, pour ne pas dire une seconde documentation en parallèle.") . "\n\n";
	
	$texte .= '<pre id="fichierDeConfiguration">' . coloreFichierPhp($racine . '/inc/config.inc.php', TRUE, TRUE) . "</pre>\n\n";
	
	$texte .= '### ' . T_("Contenu du fichier de configuration de l'administration de Squeletml") . "\n\n";
	
	$texte .= '<pre id="fichierDeConfiguration">' . coloreFichierPhp($racineAdmin . '/inc/config.inc.php', TRUE, TRUE) . "</pre>\n\n";
	
	return $texte;
}

/*
Retourne le contenu de la balise `title`.
*/
function baliseTitle($baliseTitle, $langues)
{
	return empty($baliseTitle) ? url() : $baliseTitle;
}

/*
Retourne le complément de la balise `title`. Si aucun complément, n'a été trouvé, retourne une chaîne vide.
*/
function baliseTitleComplement($tableauBaliseTitleComplement, $langues)
{
	foreach ($langues as $langue)
	{
		if (array_key_exists($langue, $tableauBaliseTitleComplement))
		{
			return $tableauBaliseTitleComplement[$langue];
		}
	}
	
	return '';
}

/*
Returne TRUE si le bloc a des coins arrondis, sinon retourne FALSE.
*/
function blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes)
{
	if ((isset($blocsArrondisSpecifiques[$bloc][$nombreDeColonnes]) && $blocsArrondisSpecifiques[$bloc][$nombreDeColonnes]) || $blocsArrondisParDefaut)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne un tableau de régions dont chacune est un tableau contenant la liste des blocs à insérer. Si `$premierOuDernier` vaut `premier`, seules les régions situées avant le contenu de l'utilisateur (régions 100, 200 et 300) seront prises en considération, sinon si `$premierOuDernier` vaut `dernier`, seules les régions situées après le contenu de l'utilisateur (régions 400, 500 et 600) seront analysées. L'ordre des blocs dans une région correspond à l'ordre (du premier au dernier) dans lequel ces derniers doivent y apparaître.
*/
function blocs($ordreBlocsDansFluxHtml, $nombreDeColonnes, $premierOuDernier)
{
	$ordreBlocsDansFluxHtmlSelonColonnes = array ();
	$ordreBlocsDansFluxHtmlFiltre = array ();
	
	foreach ($ordreBlocsDansFluxHtml as $bloc => $nombres)
	{
		$ordreBlocsDansFluxHtmlSelonColonnes[$bloc] = $nombres[$nombreDeColonnes];
	}
	
	asort($ordreBlocsDansFluxHtmlSelonColonnes);
	
	foreach ($ordreBlocsDansFluxHtmlSelonColonnes as $bloc => $nombre)
	{
		$region = floor($nombre / 100) * 100; // Ex.: 580 devient 500.
		
		if (($premierOuDernier == 'premier' && $region >= 100 && $region <= 300) || ($premierOuDernier == 'dernier' && $region >= 400 && $region <= 600))
		{
			$ordreBlocsDansFluxHtmlFiltre[$region][] = $bloc;
		}
	}
	
	asort($ordreBlocsDansFluxHtmlFiltre);
	
	return $ordreBlocsDansFluxHtmlFiltre;
}

/*
Retourne un tableau dont chaque élément contient le code d'activation d'une boîte déroulante.
*/
function boitesDeroulantes($boitesDeroulantesParDefaut, $boitesDeroulantes)
{
	$boites = '';
	
	if (!empty($boitesDeroulantesParDefaut))
	{
		$boites .= $boitesDeroulantesParDefaut . ' ';
	}
	
	if (!empty($boitesDeroulantes))
	{
		$boites .= $boitesDeroulantes;
	}
	
	if (!empty($boites))
	{
		$boitesDeroulantesTableau = explode(' ', $boites);
		$boitesDeroulantesTableau = array_map('trim', $boitesDeroulantesTableau);
		$elementsVides = array_keys($boitesDeroulantesTableau, '');
		
		foreach ($elementsVides as $i)
		{
			unset($boitesDeroulantesTableau[$i]);
		}
	}
	else
	{
		$boitesDeroulantesTableau = array();
	}
	
	return $boitesDeroulantesTableau;
}

/*
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

/*
Retourne le chemin vers le fichier de configuration des catégories. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigCategories($racine, $retourneCheminParDefaut = FALSE)
{
	if (file_exists("$racine/site/inc/categories.ini.txt"))
	{
		return "$racine/site/inc/categories.ini.txt";
	}
	elseif (file_exists("$racine/site/inc/categories.ini"))
	{
		return "$racine/site/inc/categories.ini";
	}
	elseif ($retourneCheminParDefaut)
	{
		return "$racine/site/inc/categories.ini.txt";
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin vers le fichier de configuration du flux RSS global des galeries ou des pages autres que les galeries, selon le nom passé en paramètre. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigFluxRssGlobal($racine, $nom, $retourneCheminParDefaut = FALSE)
{
	if (file_exists("$racine/site/inc/rss-global-$nom.ini.txt"))
	{
		return "$racine/site/inc/rss-global-$nom.ini.txt";
	}
	elseif (file_exists("$racine/site/inc/rss-global-$nom.ini"))
	{
		return "$racine/site/inc/rss-global-$nom.ini";
	}
	elseif ($retourneCheminParDefaut)
	{
		return "$racine/site/inc/rss-global-$nom.ini.txt";
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin vers le fichier de configuration d'une galerie. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigGalerie($racine, $idGalerie, $retourneCheminParDefaut = FALSE)
{
	if (!empty($idGalerie) && file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt';
	}
	elseif (!empty($idGalerie) && file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini';
	}
	elseif ($retourneCheminParDefaut)
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt';
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin vers le fichier `(site/)xhtml/$nom.inc.php` demandé. Si aucun fichier n'a été trouvé, retourne une chaîne vide.
*/
function cheminXhtml($racine, $nom)
{
	if (file_exists("$racine/site/xhtml/$nom.inc.php"))
	{
		return "$racine/site/xhtml/$nom.inc.php";
	}
	elseif (file_exists("$racine/xhtml/$nom.inc.php"))
	{
		return "$racine/xhtml/$nom.inc.php";
	}
	
	return '';
}

/*
Si `$retourneChemin` vaut TRUE, retourne le chemin vers le fichier `(site/)xhtml/$langue/$nom.inc.php` demandé. Si aucun fichier n'a été trouvé, retourne une chaîne vide. Si `$retourneChemin` vaut FALSE, retourne TRUE si un fichier a été trouvé, sinon retourne FALSE.
*/
function cheminXhtmlLangue($racine, $langues, $nom, $retourneChemin = TRUE)
{
	foreach ($langues as $langue)
	{
		if (file_exists("$racine/site/xhtml/$langue/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/site/xhtml/$langue/$nom.inc.php" : TRUE;
		}
		elseif (file_exists("$racine/xhtml/$langue/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/xhtml/$langue/$nom.inc.php" : TRUE;
		}
	}
	
	return $retourneChemin ? '' : FALSE;
}

/*
Retourne un tableau dont chaque élément contient un chemin vers le fichier `(site/)inc/$nom.inc.php` demandé.
*/
function cheminsInc($racine, $nom)
{
	$fichiers = array ();
	$fichiers[] = "$racine/inc/$nom.inc.php";
	
	if (file_exists("$racine/site/inc/$nom.inc.php"))
	{
		$fichiers[] = "$racine/site/inc/$nom.inc.php";
	}
	
	return $fichiers;
}

/*
Retourne une liste de classes pour `body`.
*/
function classesBody($estAccueil, $idGalerie, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $borduresPage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $classesBody)
{
	$class = '';
	$arrierePlanColonne = 'Avec' . ucfirst($arrierePlanColonne);
	
	if ($estAccueil)
	{
		$class .= 'accueil ';
	}
	
	if (!empty($idGalerie))
	{
		$class .= 'galerie ';
		
		if ($nombreDeColonnes == 0)
		{
			$class .= 'galerieAucuneColonne ';
		}
	}
	
	if ($nombreDeColonnes == 2)
	{
		$class .= 'deuxColonnes colonneAgauche colonneAdroite ';
		
		if ($arrierePlanColonne != 'Aucun')
		{
			$class .= "deuxColonnes$arrierePlanColonne ";
		}
		
		if ($deuxColonnesSousContenuAgauche)
		{
			$class .= "deuxColonnesSousContenuAgauche ";
		}
		else
		{
			$class .= "deuxColonnesSousContenuAdroite ";
		}
	}
	elseif ($nombreDeColonnes == 1)
	{
		$class .= 'uneColonne ';
		
		if ($uneColonneAgauche)
		{
			$class .= "colonneAgauche uneColonneAgauche ";
			
			if ($arrierePlanColonne != 'Aucun')
			{
				$class .= "colonneAgauche$arrierePlanColonne ";
			}
		}
		else
		{
			$class .= "colonneAdroite uneColonneAdroite ";
			
			if ($arrierePlanColonne != 'Aucun')
			{
				$class .= "colonneAdroite$arrierePlanColonne ";
			}
		}
	}
	elseif ($nombreDeColonnes == 0)
	{
		$class .= "aucuneColonne ";
	}
	
	if ($borduresPage['gauche'])
	{
		$class .= 'bordureGauchePage ';
	}
	
	if ($borduresPage['droite'])
	{
		$class .= 'bordureDroitePage ';
	}
	
	if ($enTetePleineLargeur && ($nombreDeColonnes == 1 || $nombreDeColonnes == 2))
	{
		$class .= 'enTetePleineLargeur ';
	}
	
	if ($differencierLiensVisitesHorsContenu)
	{
		$class .= 'liensVisitesDifferencies ';
	}
	
	if (!empty($classesBody))
	{
		$class .= trim($classesBody);
	}
	
	return trim($class);
}

/*
Retourne une liste de classes pour la div `contenu`.
*/
function classesContenu($differencierLiensVisitesHorsContenu, $classesContenu)
{
	$class = '';
	
	if (!$differencierLiensVisitesHorsContenu)
	{
		$class = 'liensVisitesDifferencies ';
	}
	
	if (!empty($classesContenu))
	{
		$class .= trim($classesContenu);
	}
	
	return trim($class);
}

/*
Retourne un tableau dont le premier élément contient le code débutant l'intérieur d'un bloc (donc ce qui suit l'ouverture d'une div de classe `bloc`); et le deuxième élément, le code terminant l'intérieur d'un bloc (donc ce qui précède la fermeture d'une div de classe `bloc`).
*/
function codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes)
{
	$codeInterieurBloc = array ();
	$codeInterieurBloc[0] = "\n\t";
	$codeInterieurBloc[1] = "\n\t" . '</div><!-- /.contenuBloc -->';
	
	if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes))
	{
		$codeInterieurBloc[0] .= '<div class="haut-droit"></div><div class="haut-gauche"></div>';
		$codeInterieurBloc[1] .= '<div class="bas-droit"></div><div class="bas-gauche"></div>';
	}
	
	$codeInterieurBloc[0] .= '<div class="contenuBloc">' . "\n";
	$codeInterieurBloc[1] .= "\n";
	
	return $codeInterieurBloc;
}

/*
Personnalise la coloration syntaxique par défaut de la fonction `highlight_string()`.

La coloration est modifiée dans le but d'améliorer le contraste des commentaires. En effet, par défaut, la couleur utilisée pour les commentaires n'offre pas un contraste suffisant sous fond blanc (voir <http://www.britoweb.net/outils/contraste-couleurs.php>).

Les autres couleurs sont également modifiées et inspirées de la coloration par défaut pour le PHP de l'éditeur de texte gedit (<http://projects.gnome.org/gedit/>).

Aussi, les espaces insécables sont remplacées par des espaces normales.

Tout comme `highlight_string()`, un paramètre optionnel, s'il est défini à TRUE, permet de retourner le code au lieu de l'afficher directement.

Un paramètre optionnel supplémentaire permet d'afficher les commentaires en noir. Par défaut est défini à FALSE.
*/
function coloreCodePhp($code, $retourneCode = FALSE, $commentairesEnNoir = FALSE)
{
	$codeColore = highlight_string($code, TRUE);
	$codeColore = str_replace('&nbsp;', ' ', $codeColore);
	$codeColore = str_replace('<br />', "\n", $codeColore);
	
	// Commentaires vers bleu primaire ou vers noir.
	if ($commentairesEnNoir)
	{
		$couleurCommentaires = '000000';
	}
	else
	{
		$couleurCommentaires = '0000FF';
	}
	
	$codeColore = str_replace('color: #FF8000', 'color: #' . $couleurCommentaires, $codeColore);
	
	// Variables vers à peu près bleu sarcelle.
	$codeColore = str_replace('color: #0000BB', 'color: #008A8C', $codeColore);
	
	// Chaînes vers magenta secondaire.
	$codeColore = str_replace('color: #DD0000', 'color: #FF00FF', $codeColore);
	
	// Symboles vers à peu près fraise écrasée.
	$codeColore = str_replace('color: #007700', 'color: #A52A2A', $codeColore);
	
	if ($retourneCode)
	{
		return $codeColore;
	}
	else
	{
		echo $codeColore;
		
		return TRUE;
	}
}

/*
Personnalise la coloration syntaxique par défaut de la fonction `highlight_file()`. Voir la fonction `coloreCodePhp()`.
*/
function coloreFichierPhp($fichier, $retourneCode = FALSE, $commentairesEnNoir = FALSE)
{
	$code = file_get_contents($fichier);
	$codeColore = coloreCodePhp($code, TRUE, $commentairesEnNoir);
	
	if ($retourneCode)
	{
		return $codeColore;
	}
	else
	{
		echo $codeColore;
		
		return TRUE;
	}
}

/*
S'assure que les balises du code HTML fourni en paramètre sont toutes bien fermées et imbriquées. Retourne le code analysé (et modifié s'il y avait lieu). Il s'agit d'un alias de la fonction `_filter_htmlcorrector()`.
*/
function corrigeHtml($html)
{
	return _filter_htmlcorrector($html);
}

/*
Retourne un tableau de deux éléments: le premier contient le corps de la galerie prêt à être affiché; le deuxième contient les informations sur l'image en version intermediaire s'il y a lieu, sinon est vide.
*/
function coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $nombreDeColonnes, $blocsArrondisParDefaut, $blocsArrondisSpecifiques, $nombreDeColonnes)
{
	if (preg_match('/(<div id="galerieIntermediaireTexte">.+<\/div><!-- \/#galerieIntermediaireTexte -->)/s', $corpsGalerie, $resultat))
	{
		if ($galerieLegendeEmplacement[$nombreDeColonnes] == 'bloc')
		{
			$corpsGalerie = preg_replace('/<div id="galerieIntermediaireTexte">.+<\/div><!-- \/#galerieIntermediaireTexte -->/s', '', $corpsGalerie);
			
			list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, 'legende-oeuvre-galerie', $nombreDeColonnes);
			
			if (blocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, 'legende-oeuvre-galerie', $nombreDeColonnes))
			{
				$classeBlocArrondi = ' blocArrondi';
			}
			else
			{
				$classeBlocArrondi = '';
			}
			
			$tableauCorpsGalerie['texteIntermediaire'] = '<div id="galerieIntermediaireTexteHorsContenu" class="bloc' . $classeBlocArrondi . '">' . $codeInterieurBlocHaut . '<h2>' . T_("Légende de l'oeuvre") . "</h2>\n" . $resultat[1] . $codeInterieurBlocBas . '</div><!-- /#galerieIntermediaireTexteHorsContenu -->' . "\n";
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
	
	// Dans tous les cas, on supprime la div `galerieIntermediaireTexte` si elle est vide.
	if (preg_match('/(<div id="galerieIntermediaireTexte"><\/div><!-- \/#galerieIntermediaireTexte -->)/', $tableauCorpsGalerie['corpsGalerie']))
	{
		$tableauCorpsGalerie['corpsGalerie'] = preg_replace('/<div id="galerieIntermediaireTexte"><\/div><!-- \/#galerieIntermediaireTexte -->/', '', $tableauCorpsGalerie['corpsGalerie']);
	}
	
	return $tableauCorpsGalerie;
}

/*
Vérifie si le dossier de cache existe. S'il n'existe pas, le dossier est créé, sinon rien n'est fait. Retourne TRUE si le dossier existe ou si la création a été effectuée avec succès, sinon retourne FALSE.
*/
function creeDossierCache($racine)
{
	if (!file_exists("$racine/site/cache"))
	{
		if (@mkdir("$racine/site/cache", 0755, TRUE))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	else
	{
		return TRUE;
	}
}

/*
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
		$vignetteNom = nomSuffixe($oeuvre['intermediaireNom'], '-vignette');
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
	
	$messageDecouvrirSupplement .= "<p style='text-align: center;'><img src='$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . "/" . rawurlencode($vignetteNom) . "' alt='$vignetteAlt' /></p>\n";
	
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
	
	$messageDecouvrirSupplement = "<div style='font-style: italic; text-align: center;'>$messageDecouvrirSupplement</div>\n";
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . T_("Voyez l'oeuvre en plus grande taille!") . '</a> ' . T_("En espérant qu'elle vous intéresse!") . "</p>\n";
	
	return $messageDecouvrirSupplement;
}

/*
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
		$messageDecouvrirSupplement = "<p style='font-style: italic;'>$messageDecouvrirSupplement</p>\n";
	}
	
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . T_("Consultez cette page!") . '</a> ' . T_("En espérant qu'elle vous intéresse!") . "</p>\n";
	
	return $messageDecouvrirSupplement;
}

/*
Retourne le DTD (Définition de Type de Document) de la page.
*/
function doctype($xhtmlStrict)
{
	if ($xhtmlStrict)
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	}
	else
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	}
}

/*
Retourne TRUE si la page est l'accueil, sinon retourne FALSE.
*/
function estAccueil($accueil)
{
	$url = url();
	
	if ($url == $accueil . '/' || $url == $accueil . '/index.php' || $url == $accueil . '/index.html' || $url == $accueil . '/index.htm')
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le contenu d'un fichier RSS.
*/
function fluxRss($idGalerie, $baliseTitleComplement, $url, $itemsFluxRss, $estGalerie)
{
	$contenuRss = '';
	$contenuRss .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$contenuRss .= '<rss version="2.0">' . "\n";
	$contenuRss .= "\t<channel>\n";
	
	// Page individuelle d'une galerie.
	if (!empty($idGalerie))
	{
		$contenuRss .= "\t\t<title>" . sprintf(T_("Galerie %1\$s"), $idGalerie . $baliseTitleComplement) . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Derniers ajouts à la galerie %1\$s"), $idGalerie) . "</description>\n\n";
	}
	// Toutes les galeries.
	elseif ($estGalerie)
	{
		
		$contenuRss .= "\t\t<title>" . T_("Galeries") . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . T_("Derniers ajouts aux galeries") . $baliseTitleComplement . "</description>\n\n";
	}
	// Tout le site.
	else
	{
		$contenuRss .= "\t\t<title>" . T_("Dernières publications") . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>$url</link>\n";
		$contenuRss .= "\t\t<description>" . T_("Dernières publications") . $baliseTitleComplement . "</description>\n\n";
	}
	
	if (!empty($itemsFluxRss))
	{
		foreach ($itemsFluxRss as $itemFlux)
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
	}
	
	$contenuRss .= "\t</channel>\n";
	$contenuRss .= '</rss>';
	
	return $contenuRss;
}

/*
Retourne un tableau listant les oeuvres d'une galerie, chaque oeuvre constituant elle-même un tableau des informations nécessaires à la création d'un fichier RSS.
*/
function fluxRssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie)
{
	$galerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
	$itemsFluxRss = array ();
	
	foreach ($galerie as $oeuvre)
	{
		$id = idOeuvre($oeuvre);
		$title = sprintf(T_("Oeuvre %1\$s"), $id);
		$cheminOeuvre = "$racine/site/fichiers/galeries/$idGalerie/" . $oeuvre['intermediaireNom'];
		$urlOeuvre = "$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . "/" . rawurlencode($oeuvre['intermediaireNom']);
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
			list ($width, $height) = getimagesize($cheminOeuvre);
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
			$urlOriginal = "$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . "/" . rawurlencode($oeuvre['originalNom']);
		}
		else
		{
			$nomOriginal = nomSuffixe($oeuvre['intermediaireNom'], '-original');
			
			if (file_exists("$racine/site/fichiers/galeries/$idGalerie/$nomOriginal"))
			{
				$urlOriginal = "$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . "/" . rawurlencode($nomOriginal);
			}
			else
			{
				$urlOriginal = '';
			}
		}
		
		if (!empty($urlOriginal))
		{
			$msgOriginal = "\n<p><a href='$urlOriginal'>" . T_("Lien vers l'oeuvre au format original.") . "</a></p>\n";
		}
		else
		{
			$msgOriginal = '';
		}
		
		$description = securiseTexte("<div>$description</div>\n<p><img src='$urlOeuvre' width='$width' height='$height' alt='$alt' /></p>$msgOriginal");
		$pubDate = fileatime($cheminOeuvre);
		$itemsFluxRss[] = array (
			"title" => $title,
			"link" => $urlGalerieOeuvre,
			"guid" => $urlGalerieOeuvre,
			"description" => $description,
			"pubDate" => $pubDate,
		);
	}
	
	return $itemsFluxRss;
}

/*
Retourne un tableau d'un élément représentant une page du site, cet élément étant lui-même un tableau contenant les informations nécessaires à la création d'un fichier RSS. Si une erreur survient, retourne un tableau vide.
*/
function fluxRssPageTableauBrut($cheminPage, $urlPage, $inclureApercu)
{
	$itemFlux = array ();
	$infosPage = securiseTexte(infosPage($urlPage, $inclureApercu));
	
	if (!empty($infosPage))
	{
		if (!empty($infosPage['dateCreation']))
		{
			$date = $infosPage['dateCreation'];
		}
		elseif (!empty($infosPage['dateRevision']))
		{
			$date = $infosPage['dateRevision'];
		}
		else
		{
			$date = fileatime($cheminPage);
		}
	
		if (!$infosPage['descriptionComplete'])
		{
			$infosPage['description'] .= securiseTexte("<p><a href=\"$urlPage\">" . sprintf(T_("Lire la suite de %1\$s."), '<em>' . $infosPage['titre'] . '</em>') . "</a></p>\n");
		}
	
		$itemFlux[] = array (
			"title" => $infosPage['titre'],
			"link" => $urlPage,
			"guid" => $urlPage,
			"description" => $infosPage['description'],
			"pubDate" => $date,
		);
	}
	
	return $itemFlux;
}

/*
Retourne le tableau `$itemsFluxRss` trié selon la date de dernière modification et contenant au maximum le nombre d'items précisé dans la configuration.
*/
function fluxRssTableauFinal($itemsFluxRss, $nombreItemsFluxRss)
{
	foreach ($itemsFluxRss as $cle => $valeur)
	{
		$itemsFluxRssTitle[$cle] = $valeur['title'];
		$itemsFluxRssLink[$cle] = $valeur['link'];
		$itemsFluxRssGuid[$cle] = $valeur['guid'];
		$itemsFluxRssDescription[$cle] = $valeur['description'];
		$itemsFluxRssPubDate[$cle] = $valeur['pubDate'];
	}
	
	array_multisort($itemsFluxRssPubDate, SORT_DESC, $itemsFluxRss);
	$itemsFluxRss = array_slice($itemsFluxRss, 0, $nombreItemsFluxRss);
	
	return $itemsFluxRss;
}

/*
Retourne l'`id` d'une oeuvre d'une galerie.
*/
function idOeuvre($oeuvre)
{
	return !empty($oeuvre['id']) ? $oeuvre['id'] : $oeuvre['intermediaireNom'];
}

/*
Retourne un tableau d'informations au sujet de la page demandée. Le tableau contient les informations suivantes:

  - `$infosPage['titre']`: titre de la page. Prend comme valeur la première information trouvée parmi les suivantes:
    - contenu de la premère balise `h1`;
    - contenu de la balise `title`;
    - URL de la page.
  - `$infosPage['description']`: description de la page. Par défaut, vaut tout le contenu de la `div` `interieurContenu`, la première balise `h1` en moins. Selon les valeurs de `$inclureApercu` et `$apercu` pour la page demandée, la description peut correspondre à un extrait de la page ou au contenu de la métabalise `description`;
  - `$infosPage['descriptionComplete']`: vaut TRUE si la description n'est pas seulement un aperçu et correspond au texte en entier (`div` `interieurContenu`), sinon vaut FALSE;
  - `$infosPage['auteur']`: vaut le contenu de la métabalise `author`, si elle existe;
  - `$infosPage['dateCreation']`: vaut le contenu de la métabalise `date-creation-yyyymmdd`, si elle existe;
  - `$infosPage['dateRevision']`: vaut le contenu de la métabalise `date-revision-yyyymmdd`, si elle existe.

Si la page demandée n'est pas accessible, retourne un tableau vide.
*/
function infosPage($urlPage, $inclureApercu)
{
	$infosPage = array ();
	$html = @file_get_contents($urlPage);
	
	if ($html !== FALSE)
	{
		$dom = str_get_html($html);
	
		// Titre.
	
		if ($titre = $dom->find('h1'))
		{
			$infosPage['titre'] = $titre[0]->innertext;
		}
		else
		{
			$infosPage['titre'] = '';
		}
	
		if (empty($infosPage['titre']) && $dom->find('title'))
		{
			$infosPage['titre'] = $titre[0]->innertext;
		}
	
		unset($titre);
	
		if (empty($infosPage['titre']))
		{
			$infosPage['titre'] = $urlPage;
		}
	
		// Description.
	
		if ($description = $dom->find('div#interieurContenu'))
		{
			if ($h1 = $description[0]->find('h1'))
			{
				$h1[0]->outertext = '';
			}
		
			$infosPage['description'] = $description[0]->innertext;
			unset($description);
		}
		else
		{
			$infosPage['description'] = '';
		}
	
		$apercuInterne = FALSE;
		$infosPage['descriptionComplete'] = TRUE;
	
		if ($inclureApercu && preg_match('|<!-- APERÇU: (.+?) -->|s', $infosPage['description'], $resultatApercu))
		{
			if ($resultatApercu[1] == 'interne')
			{
				if (preg_match('|^(.+?)<!-- ?/aperçu ?-->|s', $infosPage['description'], $resultatInterne))
				{
					$apercuInterne = TRUE;
					$infosPage['descriptionComplete'] = FALSE;
					$infosPage['description'] = corrigeHtml(supprimeCommentairesHtml($resultatInterne[1]));
				}
			}
			elseif ($resultatApercu[1] == 'description' && $description = $dom->find('meta[name=description]'))
			{
				$infosPage['descriptionComplete'] = FALSE;
				$infosPage['description'] = $description[0]->content;
				unset($description);
			}
			else
			{
				$infosPage['descriptionComplete'] = FALSE;
				$infosPage['description'] = $resultatApercu[1];
			}
		}
	
		if (!$apercuInterne)
		{
			$infosPage['description'] = supprimeCommentairesHtml($infosPage['description']);
		}
	
		// Auteur.
		if ($auteur = $dom->find('meta[name=author]'))
		{
			$infosPage['auteur'] = $auteur[0]->content;
			unset($auteur);
		}
		else
		{
			$infosPage['auteur'] = '';
		}
	
		// Dates.
	
		if ($dateCreation = $dom->find('meta[name=date-creation-yyyymmdd]'))
		{
			$infosPage['dateCreation'] = $dateCreation[0]->content;
			unset($dateCreation);
		}
		else
		{
			$infosPage['dateCreation'] = '';
		}
	
		if ($dateRevision = $dom->find('meta[name=date-revision-yyyymmdd]'))
		{
			$infosPage['dateRevision'] = $dateRevision[0]->content;
			unset($dateRevision);
		}
		else
		{
			$infosPage['dateRevision'] = '';
		}
	
		$dom->clear();
		unset($dom);
	}
	
	return $infosPage;
}

/*
Retourne un tableau associatif dont les valeurs sont le premier paramètre de la fonction, et les clés sont tous les paramètres à partir du deuxième.

Cette fonction est intéressante couplée à la fonction `extract()` pour initialiser une liste de variables sans écraser celles qui sont déjà initalisées. Par exemple:

	init(FALSE, 'var1', 'var2', 'var3');

va retourner le tableau suivant:

	array ('var1' => FALSE, 'var2' => FALSE, 'var3' => FALSE)

En récupérant ce tableau comme premier paramètre de la fonction `extract()`, et en précisant grâce au deuxième paramètre de ne pas écraser les collisions, nous obtenons une initialisation des variables qui n'existent pas encore. Par exemple:

	extract(init(FALSE, 'var1', 'var2', 'var3'), EXTR_SKIP);

est équivalent à ceci:

	if (!array_key_exists('var1', get_defined_vars()))
	{
		$var1 = FALSE;
	}
	
	if (!array_key_exists('var2', get_defined_vars()))
	{
		$var2 = FALSE;
	}
	
	if (!array_key_exists('var3', get_defined_vars()))
	{
		$var3 = FALSE;
	}

Note: dans l'exemple ci-dessus, la fonction `array_key_exists()` est utilisée, car `isset()` retourne FALSE dans le cas où la variable existe mais est définie à NULL. La fonction `is_null()` ne permet pas non plus de différencier une variable définie à NULL d'une variable non existante, car TRUE est quand même retourné quand la variable n'existe pas.
*/
function init($valeurDinitialisation)
{
	$arguments = func_get_args();
	$nombreArguments = func_num_args();
	$tableau = array ();
	
	for ($i = 1; $i < $nombreArguments; $i++)
	{
		$tableau[$arguments[$i]] = $valeurDinitialisation;
	}
	
	return $tableau;
}

/*
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

/*
Retourne la langue de la page courante.
*/
function langue($langueParDefaut, $langue)
{
	if ($langue == 'navigateur')
	{
		$langue = explode(',', securiseTexte($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		$langue = strtolower(substr(chop($langue[0]), 0, 2));
		
		return $langue;
	}
	
	return !empty($langue) ? $langue : $langueParDefaut;
}

/*
S'il y a lieu, ajoute la classe `actif` au lien vers l'accueil dans la langue en cours ainsi qu'au `li` contenant le lien. Retourne le code résultant.
*/
function langueActive($codeMenuLangues, $langue, $accueil)
{
	if (array_key_exists($langue, $accueil))
	{
		$url = $accueil[$langue] . '/';
		$html = str_get_html($codeMenuLangues);
	
		foreach ($html->find('a') as $a)
		{
			if ($a->href == $url)
			{
				$class = 'actif';
			
				if (!empty($a->class))
				{
					$class .= ' ' . $a->class;
				}
			
				$a->class = $class;
			
				$aParent = $a->parent();
		
				while ($aParent->tag != 'li' && $aParent->tag != 'root' && $aParent->tag != NULL)
				{
					$aParent = $aParent->parent();
				}
		
				if ($aParent->tag == 'li')
				{
					$class = 'actif';
			
					if (!empty($aParent->class))
					{
						$class .= ' ' . $aParent->class;
					}
			
					$aParent->class = $class;
				}
			}
		}
	
		return $html;
	}
	else
	{
		return $codeMenuLangues;
	}
}

/*
Retourne une lettre (a-zA-Z) au hasard. Optionnellement, il est possible de préciser des lettres à exclure.
*/
function lettreAuHasard($lettresExclues = '')
{
	$lettres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	do
	{
		$lettre = $lettres[mt_rand(0, 51)];
	} while (substr_count($lettresExclues, $lettre));
	
	return $lettre;
}

/*
Retourne le code HTML informant de la licence choisie. Le paramètre `$choixLicence` prend une des valeurs suivantes:

  - `art-libre` pour la licence Art Libre;

  - `cc-by` pour un contrat Creative Commons Paternité 3.0 Générique;

  - `cc-by-sa` pour un contrat Creative Commons Paternité – Partage des conditions initiales à l'identique 3.0 Générique;

  - `cc-by-nd` pour un contrat Creative Commons Paternité – Pas de modification 3.0 Générique;

  - `cc-by-nc` pour un contrat Creative Commons Paternité – Pas d'utilisation commerciale 3.0 Générique;

  - `cc-by-nc-sa` pour un contrat Creative Commons Paternité – Pas d'utilisation commerciale – Partage des conditions initiales à l'identique 3.0 Générique;

  - `cc-by-nc-nd` pour un contrat Creative Commons Paternité – Pas d'utilisation commerciale – Pas de modification 3.0 Générique;

  - `dp` pour le domaine public;

  - `gplv2` pour la licence publique générale de GNU, version 2;

  - `gplv2+` pour la licence publique générale de GNU, version 2 ou toute version ultérieure;

  - `gplv3` pour la licence publique générale de GNU, version 3;

  - `gplv3+` pour la licence publique générale de GNU, version 3 ou toute version ultérieure;

  - `agplv3` pour la licence publique générale GNU Affero, version 3;

  - `agplv3+` pour la licence publique générale GNU Affero, version 3 ou toute version ultérieure;

  - `lgplv2.1` pour la licence publique générale amoindrie de GNU, version 2.1;

  - `lgplv2.1+` pour la licence publique générale amoindrie de GNU, version 2.1 ou toute version ultérieure;

  - `lgplv3` pour la licence publique générale amoindrie de GNU, version 3;

  - `lgplv3+` pour la licence publique générale amoindrie de GNU, version 3 ou toute version ultérieure;

  - `bsd` pour la licence BSD modifiée;

  - `mit` pour la licence MIT.

Si le choix n'est pas valide, une chaîne vide est retournée.
*/
function licence($urlRacine, $choixLicence)
{
	switch ($choixLicence)
	{
		case 'art-libre':
			$licence = sprintf(T_("<a href=\"http://artlibre.org/licence/lal\"><img %1\$s alt=\"Licence Art Libre\" /></a> Mis à disposition sous la <a href=\"http://artlibre.org/licence/lal\">licence Art Libre</a>."), "src=\"$urlRacine/fichiers/licence-art-libre-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by-sa':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-sa/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité – Partage des conditions initiales à l'identique 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by-sa/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-sa/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by-nd':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nd/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité – Pas de modification 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by-nd/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nd/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by-nc':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité – Pas d'utilisation commerciale 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by-nc-sa':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc-sa/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité – Pas d'utilisation commerciale – Partage des conditions initiales à l'identique 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by-nc-sa/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'cc-by-nc-nd':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc-nd/3.0/deed.fr\"><img %1\$s alt=\"Contrat Creative Commons Paternité – Pas d'utilisation commerciale – Pas de modification 3.0 Générique\" /></a> Mis à disposition sous un <a href=\"http://creativecommons.org/licenses/by-nc-nd/3.0/deed.fr\">contrat Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc-nd/3.0/80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'dp':
			$licence = sprintf(T_("<a href=\"http://creativecommons.org/licenses/publicdomain/deed.fr\"><img %1\$s alt=\"Domaine public\" /></a> Mis à disposition dans le <a href=\"http://creativecommons.org/licenses/publicdomain/deed.fr\">domaine public</a>."), "src=\"$urlRacine/fichiers/domaine-public-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'gplv2':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl-2.0.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 2\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl-2.0.html\">licence publique générale de GNU, version 2</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'gplv2+':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 2 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl.html\">licence publique générale de GNU, version 2 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'gplv3':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl-3.0.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl-3.0.html\">licence publique générale de GNU, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'gplv3+':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl.html\">licence publique générale de GNU, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'agplv3':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/agpl-3.0.html\"><img %1\$s alt=\"Licence publique générale GNU Affero, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/agpl-3.0.html\">licence publique générale GNU Affero, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-agpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'agplv3+':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/agpl.html\"><img %1\$s alt=\"Licence publique générale GNU Affero, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/agpl.html\">licence publique générale GNU Affero, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-agpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'lgplv2.1':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl-2.1.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 2.1\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl-2.1.html\">licence publique générale amoindrie de GNU, version 2.1</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'lgplv2.1+':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 2.1 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl.html\">licence publique générale amoindrie de GNU, version 2.1 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'lgplv3':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl-3.0.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl-3.0.html\">licence publique générale amoindrie de GNU, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'lgplv3+':
			$licence = sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl.html\">licence publique générale amoindrie de GNU, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'bsd':
			$licence = sprintf(T_("<a href=\"http://fr.wikipedia.org/wiki/Licence_BSD#Texte_de_la_licence\"><img %1\$s alt=\"Licence BSD modifiée\" /></a> Mis à disposition sous la <a href=\"http://fr.wikipedia.org/wiki/Licence_BSD#Texte_de_la_licence\">licence BSD modifiée</a>."), "src=\"$urlRacine/fichiers/licence-bsd-modifiee-80x15.png\" width=\"80\" height=\"15\"");
			break;

		case 'mit':
			$licence = sprintf(T_("<a href=\"http://www.opensource.org/licenses/mit-license.php\"><img %1\$s alt=\"Licence MIT\" /></a> Mis à disposition sous la <a href=\"http://www.opensource.org/licenses/mit-license.php\">licence MIT</a>."), "src=\"$urlRacine/fichiers/licence-mit-80x15.png\" width=\"80\" height=\"15\"");
			break;

		default:
			$licence = '';
			break;
	}
	
	return $licence;
}

/*
Ajoute la classe `actif` à tous les liens (balises `a`) du code passé en paramètre et pointant vers la page en cours ainsi qu'à un parent (s'il existe) spécifié avec le paramètre optionnel `$parent`, qui doit être le nom d'une balise (par exemple `li`). Si `$inclureGet` vaut FALSE, les variables GET ne sont pas prises en considération dans la comparaison des adresses. Retourne le code résultant.
*/
function lienActif($html, $inclureGet = TRUE, $parent = '')
{
	$url = url($inclureGet);
	$dom = str_get_html($html);
	
	foreach ($dom->find('a') as $a)
	{
		if ($a->href == $url)
		{
			$class = 'actif';
			
			if (!empty($a->class))
			{
				$class .= ' ' . $a->class;
			}
			
			$a->class = $class;
			
			if (!empty($parent))
			{
				$aParent = $a->parent();
			
				while ($aParent->tag != $parent && $aParent->tag != 'root' && $aParent->tag != NULL)
				{
					$aParent = $aParent->parent();
				}
			
				if ($aParent->tag == $parent)
				{
					$class = 'actif';
				
					if (!empty($aParent->class))
					{
						$class .= ' ' . $aParent->class;
					}
				
					$aParent->class = $class;
				}
			}
		}
	}
	
	$htmlFiltre = $dom->save();
	$dom->clear();
	unset($dom);
	
	return $htmlFiltre;
}

/*
Retourne le lien vers le fichier du flux RSS demandé.
*/
function lienFluxRss($urlFluxRss, $idGalerie, $estGalerie)
{
	if (!empty($idGalerie))
	{
		$description = sprintf(T_("RSS de la galerie %1\$s"), "<em>$idGalerie</em>");
	}
	elseif ($estGalerie)
	{
		$description = T_("RSS de toutes les galeries");
	}
	else
	{
		$description = T_("RSS global du site");
	}
	
	return "<a href=\"$urlFluxRss\">$description</a>";
}

/*
Si la page en cours n'est pas l'accueil, construit un lien vers l'accueil et rend cliquable la chaîne passée en paramètre, sinon n'ajoute aucune balise et retourne la chaîne telle quelle.
*/
function lienAccueil($accueil, $estAccueil, $contenu)
{
	$aOuvrant = '';
	$aFermant = '';
	
	if (!$estAccueil)
	{
		$aOuvrant = '<a href="' . $accueil . '/">';
		$aFermant = '</a>';
	}
	
	return $aOuvrant . $contenu . $aFermant;
}

/*
Limite la profondeur affichée d'une liste en masquant les sous-listes inactives. Retourne le code résultant.

Plus précisément, aucun texte n'est supprimé, mais une classe `masquer` est ajoutée aux balises `ul` appropriées. Cette classe correspond à un sélecteur dans la feuille de style par défaut.

Par exemple, disons que la page en cours est `page3.1.php`. La liste suivante:

	<ul>
		<li><a href="page1.php">Lien 1</a></li>
		<li><a href="page2.php">Lien 2</a></li>
		<li class="parent"><a href="page3.php">Lien 3</a>
		<ul>
			<li class="parent actif"><a href="page3.1.php" class="actif">Lien 3.1</a>
			<ul>
				<li class="parent"><a href="page3.1.1.php">Lien 3.1.1</a>
				<ul>
					<li><a href="page3.1.1.1.php">Lien 3.1.1.1</a></li>
					<li><a href="page3.1.1.2.php">Lien 3.1.1.2</a></li>
					<li><a href="page3.1.1.3.php">Lien 3.1.1.3</a></li>
				</ul></li>
				<li><a href="page3.1.2.php">Lien 3.1.2</a></li>
			</ul></li>
			<li class="parent"><a href="page3.2.php">Lien 3.2</a>
			<ul>
				<li><a href="page3.2.1.php">Lien 3.2.1</a></li>
				<li><a href="page3.2.2.php">Lien 3.2.2</a></li>
			</ul></li>
			<li><a href="page3.3.php">Lien 3.3</a></li>
		</ul></li>
		<li><a href="page4.php">Lien 4</a></li>
		<li><a href="page5.php">Lien 5</a></li>
	</ul>

sera retournée ainsi:

	<ul>
		<li><a href="page1.php">Lien 1</a></li>
		<li><a href="page2.php">Lien 2</a></li>
		<li class="parent"><a href="page3.php">Lien 3</a>
		<ul>
			<li class="parent actif"><a href="page3.1.php" class="actif">Lien 3.1</a>
			<ul>
				<li class="parent"><a href="page3.1.1.php">Lien 3.1.1</a>
				<ul class="masquer">
					<li><a href="page3.1.1.1.php">Lien 3.1.1.1</a></li>
					<li><a href="page3.1.1.2.php">Lien 3.1.1.2</a></li>
					<li><a href="page3.1.1.3.php">Lien 3.1.1.3</a></li>
				</ul></li>
				<li><a href="page3.1.2.php">Lien 3.1.2</a></li>
			</ul></li>
			<li class="parent"><a href="page3.2.php">Lien 3.2</a>
			<ul class="masquer">
				<li><a href="page3.2.1.php">Lien 3.2.1</a></li>
				<li><a href="page3.2.2.php">Lien 3.2.2</a></li>
			</ul></li>
			<li><a href="page3.3.php">Lien 3.3</a></li>
		</ul></li>
		<li><a href="page4.php">Lien 4</a></li>
		<li><a href="page5.php">Lien 5</a></li>
	</ul>

ce qui signifie que la liste apparaîtra ainsi avec le style par défaut:

	* Lien 1
	* Lien 2
	* Lien 3
		o Lien 3.1
			+ Lien 3.1.1
			+ Lien 3.1.2
		o Lien 3.2
		o Lien 3.3
	* Lien 4
	* Lien 5

Le code passé en paramètre doit avoir été traité par la fonction `lienActif()` ou avoir subi une modification équivalente pour que la détection des sous-listes inactives réussisse.
*/
function limiteProfondeurListe($html)
{
	$dom = str_get_html($html);
	
	foreach ($dom->find('li') as $li)
	{
		$liAvecUl = FALSE;
		
		foreach ($li->find('ul') as $ul)
		{
			$liAvecUl = TRUE;
			$ulParent = $ul->parent();
			
			while ($ulParent->tag != 'li' && $ulParent->tag != 'root' && $ulParent->tag != NULL)
			{
				$ulParent = $ulParent->parent();
			}
		
			if ($ulParent->tag == 'li' && preg_match('|\bactif\b|', $ulParent->class))
			{
				$ulParentActif = TRUE;
			}
			else
			{
				$ulParentActif = FALSE;
			}
			
			if (!count($ul->find('li.actif')) && !$ulParentActif)
			{
				$class = 'masquer';
				
				if (!empty($ul->class))
				{
					if (preg_match('|\bmasquer\b|', $ul->class))
					{
						$class = '';
					}
					else
					{
						$class .= ' ';
					}
					
					$class .= $ul->class;
				}
				
				$ul->class = $class;
			}
		}
		
		if ($liAvecUl)
		{
			$class = 'parent';
			
			if (!empty($li->class))
			{
				$class .= ' ' . $li->class;
			}
			
			$li->class = $class;
		}
	}
	
	$htmlFiltre = $dom->save();
	$dom->clear();
	unset($dom);
	
	return $htmlFiltre;
}

/*
Construit des balises `link` et `script`. Voir le fichier de configuration `inc/config.inc.php` pour les détails au sujet de la syntaxe utilisée.
*/
function linkScript($balisesBrutes, $version = '')
{
	$balisesBrutesAinclure = linkScriptAinclure($balisesBrutes);
	$balisesFormatees = '';
	$favicon = '';
	
	if (!empty($version))
	{
		$version = '?' . $version;
	}
	
	foreach ($balisesBrutesAinclure as $fichierBrut)
	{
		// On récupère les infos.
		list ($type, $fichier) = explode('#', $fichierBrut, 2);
		
		if ($type == 'rss' && strpos($fichier, '#'))
		{
			list ($fichier, $title) = explode('#', $fichier, 2);
		}
		else
		{
			$title = '';
		}
		
		switch ($type)
		{
			case 'favicon':
				// On ne conserve qu'une déclaration de favicon.
				$favicon = '<link rel="shortcut icon" type="images/x-icon" href="' . $fichier . $version . '" />' . "\n";
				break;
	
			case 'css':
				$balisesFormatees .= '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n";
				break;
	
			case 'cssltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
		
			case 'cssIE7':
				$balisesFormatees .= '<!--[if IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'csslteIE7':
				$balisesFormatees .= '<!--[if lte IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . $fichier . $version . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
	
			case 'js':
				$balisesFormatees .= '<script type="text/javascript" src="' . $fichier . $version . '"></script>' . "\n";
				break;
				
			case 'jsDirect':
				$balisesFormatees .= "<script type=\"text/javascript\">\n//<![CDATA[\n
$fichier\n//]]>\n</script>\n";
				break;
				
			case 'jsDirectltIE7':
				$balisesFormatees .= "<!--[if lt IE 7]>\n<script type=\"text/javascript\">\n//<![CDATA[\n$fichier\n//]]>\n</script>\n<![endif]-->\n";
				break;
				
			case 'jsltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<script type="text/javascript" src="' . $fichier . $version . '"></script>' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'rss':
				if (!empty($title))
				{
					$title = ' title="' . $title . '"';
				}
				
				$balisesFormatees .= '<link rel="alternate" type="application/rss+xml" href="' . $fichier . $version . '"' . $title . ' />' . "\n";
				break;
				
			case 'po':
				$balisesFormatees .= '<link type="application/x-po" rel="gettext" href="' . $fichier . $version . '" />' . "\n";
				break;
		}
	}
	
	$balisesFormatees .= $favicon;
	
	return $balisesFormatees;
}

/*
Retourne les déclarations d'inclusion des balises `link` et `script` sans les doublons ni les balises à inclure dans des pages autres que celle en cours. L'analyse prend en compte les URL incluses dans d'autres URL. Par exemple, si le tableau contient ceci:

	"a/*#js#fichier.js"
	"a/b/c#js#fichier.js"

la deuxième ligne sera considérée comme étant un doublon de la première puisque `a/b/c` constitue une page enfant de `a/`.

Aussi, la partie «URL» des déclarations n'est pas retournée. Par exemple, ce qui suit:

	"a/*#js#fichier.js"

serait retourné ainsi:

	"js#fichier.js"
*/
function linkScriptAinclure($balisesBrutes)
{
	$balisesBrutesAinclure = array ();
	
	foreach ($balisesBrutes as $baliseBrute)
	{
		// On récupère les infos.
		list ($url, $type, $fichier) = explode('#', $baliseBrute, 3);
		$url = preg_quote($url);
		
		if ($type == 'rss' && strpos($fichier, '#'))
		{
			list ($fichier, $title) = explode('#', $fichier, 2);
		}
		else
		{
			$title = '';
		}
		
		// Si l'adresse se termine par *, accepter toutes les pages enfants possibles de cette page parent en plus de la page parent elle-même.
		if (preg_match('/\*$/', $url))
		{
			$motif = substr($url, 0, -2); // -2 à cause de `preg_quote()` retournant `\*`.
			$motif .= '.*';
		}
		else
		{
			$motif = $url;
		}
		
		// On vérifie si la balise brute est à inclure pour la page en cours.
		if (preg_match("#^$motif$#", url()))
		{
			$doublon = FALSE;
			$i = 0;
			
			// On vérifie si la balise constitue un doublon.
			foreach ($balisesBrutesAinclure as $baliseBruteAinclure)
			{
				// On récupère les infos de la balise de comparaison.
				list ($urlAinclure, $typeAinclure, $fichierAinclure) = explode('#', $baliseBruteAinclure, 3);
				
				if ($typeAinclure == 'rss' && strpos($fichierAinclure, '#'))
				{
					list ($fichierAinclure) = explode('#', $fichierAinclure, 2);
				}
				
				if ($fichier == $fichierAinclure)
				{
					$doublon = TRUE;
					
					if (preg_match('/IE7$/', $typeAinclure))
					{
						$balisesBrutesAinclure[$i] = $baliseBrute;
					}
					
					break;
				}
				
				$i++;
			}
			
			if (!$doublon)
			{
				$balisesBrutesAinclure[] = $baliseBrute;
			}
		}
	}
	
	for ($i = 0; $i < count($balisesBrutesAinclure); $i++)
	{
		list ($url, $type, $fichier) = explode('#', $balisesBrutesAinclure[$i], 3);
		$balisesBrutesAinclure[$i] = "$type#$fichier";
	}
	
	return $balisesBrutesAinclure;
}

/*
Retourne la locale de la page courante pour utilisation avec gettext.
*/
function locale($langue)
{
	$locale = $langue;
	
	// Palliatif à un bogue sur les serveurs de Koumbit. Aucune idée du problème. On dirait que 9 fois sur 10, php-gettext passe le relais au gettext par défaut de PHP, et que si la locale est seulement 'en', elle n'existe pas sur le serveur d'hébergement, donc la traduction ne fonctionne pas.
	if ($locale == 'en')
	{
		$locale = 'en_US';
	}
	
	return $locale;
}

/*
Accepte en paramètre un fichier dont le contenu est rédigé en Markdown, et retourne le contenu de ce fichier converti en HTML.
*/
function mdtxt($fichier)
{
	return Markdown(file_get_contents($fichier));
}

/*
Accepte en paramètre une chaîne rédigée en Markdown, et retourne cette chaîne convertie en HTML.
*/
function mdtxtChaine($chaine)
{
	return Markdown($chaine);
}

/*
Construit le message affiché à Internet Explorer 6.
*/
function messageIe6($urlRacine)
{
	$message = '';
	$message .= "<!--[if lt IE 7]>\n";
	$message .= '<div id="messageIe6">' . "\n";
	$message .= '<p class="bDtitre">' . T_("Savez-vous que le navigateur Internet&nbsp;Explorer&nbsp;6 (avec lequel vous visitez sur ce site actuellement) est obsolète?") . "</p>\n";
	$message .= "\n";
	$message .= '<div class="bDcorps"><p>' . T_("Pour naviguer de la manière la plus satisfaisante et sécuritaire, nous recommandons d'utiliser <strong>Firefox</strong>, un navigateur libre, performant, sécuritaire et respectueux des standards sur lesquels le web est basé. Firefox est tout à fait gratuit. Si vous utilisez un ordinateur au travail, vous pouvez faire la suggestion à votre service informatique.") . "</p>\n";
	$message .= "\n";
	$message .= "<p><strong><a href=\"http://www.firefox.com/\"><img src=\"$urlRacine/fichiers/firefox-52x52.png\" alt=\"\" width=\"52\" height=\"52\" /></a> <a href=\"http://www.mozilla-europe.org/fr/\"><span>" . T_("Télécharger Firefox") . "</span></a></strong></p></div>\n";
	$message .= "</div>\n";
	$message .= "<![endif]-->\n";
	
	return $message;
}

/*
Si `$motsCles` est vide, génère à partir d'une chaîne fournie en paramètre une liste de mots-clés utilisables par la métabalise `keywords`, et retourne cette liste, sinon retourne tout simplement `$motsCles`. Si `$melanger` vaut TRUE, change aléatoirement l'ordre des mots avant le retour.
*/
function motsCles($motsCles, $chaine, $melanger = FALSE)
{
	if (empty($motsCles))
	{
		$chaine = trim($chaine);
		
		// Suppression des caractères inutiles.
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

		// Remplacement des séparateurs «utiles» par des espaces.
		$chaine = str_replace(array ('/', '.', '-', '\'', '’'), array (' ', ' ', ' ', ' ', ' '), $chaine);

		// Compression des espaces en trop éventuelles générées par l'étape précédente.
		$chaine = str_replace(array ('  '), array (' '), $chaine);

		// Remplacement des espaces par des virgules.
		$chaine = str_replace(' ', ', ', $chaine);
		
		// Suppression des mots de trois lettres ou moins.
		$chaine = preg_replace('/(^| )[^, ]{1,3},/', '', $chaine);
		
		// Suppression des mots scindés lors du remplacement de l'apostrophe.
		$chaine = preg_replace('/(^| )(aujourd|presqu|entr|prud|homie|homies|homal|homale|homales|homaux),/i', '', $chaine);
		
		// Suppression du potentiel ', ' final avant le mélange des mots.
		if (preg_match('/, $/', $chaine))
		{
			$chaine = trim(substr($chaine, 0, -2));
		}
		
		$tableauChaine = explode(', ', $chaine);
		
		// Mélange de l'ordre des mots.
		if ($melanger)
		{
			shuffle($tableauChaine);
		}
		
		$chaine = '';
		
		foreach ($tableauChaine as $mot)
		{
			$chaine .= $mot . ', ';
		}
		
		// Resuppression du ', ' final.
		$chaine = trim(substr($chaine, 0, -2));
		
		// Tout en minuscule.
		$chaine = strtolower($chaine);
		
		return $chaine;
	}
	else
	{
		return $motsCles;
	}
}

/*
Retourne le nom de la page en cours. Par exemple, si l'URL en cours est `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3#ancre`, la fonciton va retourner `fichier.php`.
*/
function nomPage()
{
	return superBasename(url(FALSE, FALSE));
}

/*
Retourne la phrase de description du site dans le haut des pages. Sur la page d'accueil, ce sera le titre principal `h1`; sur les autres pages, ce sera un paragraphe `p`.
*/
function nomSite($estAccueil, $contenu)
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

/*
Ajoute `$suffixe` au nom d'un fichier, juste avant l'extension. Par exemple `nom.extension` devient `nom$suffixe.extension`.
*/
function nomSuffixe($nomFichier, $suffixe)
{
	$infoFichier = pathinfo($nomFichier);
	$nomFichierAvecSuffixe = superBasename($nomFichier, '.' . $infoFichier['extension']);
	$nomFichierAvecSuffixe .= $suffixe . '.' . $infoFichier['extension'];
	
	return $nomFichierAvecSuffixe;
}

/*
Génère une image de dimensions données à partir d'une image source. Si les dimensions voulues de la nouvelle image sont au moins aussi grandes que celles de l'image source, il y a seulement copie et non génération, à moins que `$galerieForcerDimensionsVignette` vaille TRUE. Dans ce cas, il y a ajout de bordures blanches (ou transparentes pour les PNG) pour compléter l'espace manquant. Retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function nouvelleImage($cheminImageSource, $cheminNouvelleImage, $typeMime,$nouvelleImageDimensionsVoulues, $galerieForcerDimensionsVignette, $galerieQualiteJpg, $nettete)
{
	$erreur = FALSE;
	$messagesScript = '';
	$nomNouvelleImage = superBasename($cheminNouvelleImage);
	$nomImageSource = superBasename($cheminImageSource);
	
	// On vérifie le type MIME de l'image dans le but d'utiliser la bonne fonction PHP.
	switch ($typeMime)
	{
		case 'image/gif':
			$imageSource = imagecreatefromgif($cheminImageSource);
			break;
		
		case 'image/jpeg':
			$imageSource = imagecreatefromjpeg($cheminImageSource);
			break;
		
		case 'image/png':
			$imageSource = imagecreatefrompng($cheminImageSource);
			break;
	}
	
	// Calcul des dimensions de l'image source.
	$imageSourceHauteur = imagesy($imageSource);
	$imageSourceLargeur = imagesx($imageSource);
	
	// On trouve les futures dimensions de la nouvelle image.
	if ($nouvelleImageDimensionsVoulues['hauteur'])
	{
		$nouvelleImageHauteur = $nouvelleImageDimensionsVoulues['hauteur'];
		
		if ($nouvelleImageHauteur > $imageSourceHauteur)
		{
			$nouvelleImageHauteur = $imageSourceHauteur;
		}
		
		$nouvelleImageLargeur = ($nouvelleImageHauteur / $imageSourceHauteur) * $imageSourceLargeur;
		
		if ($nouvelleImageDimensionsVoulues['largeur'] && ($nouvelleImageLargeur > $nouvelleImageDimensionsVoulues['largeur']))
		{
			$nouvelleImageLargeur = $nouvelleImageDimensionsVoulues['largeur'];
			$nouvelleImageHauteur = ($nouvelleImageLargeur / $imageSourceLargeur) * $imageSourceHauteur;
		}
	}
	else
	{
		$nouvelleImageLargeur = $nouvelleImageDimensionsVoulues['largeur'];
		
		if ($nouvelleImageLargeur > $imageSourceLargeur)
		{
			$nouvelleImageLargeur = $imageSourceLargeur;
		}
		
		$nouvelleImageHauteur = ($nouvelleImageLargeur / $imageSourceLargeur) * $imageSourceHauteur;
		
		if ($nouvelleImageDimensionsVoulues['hauteur'] && ($nouvelleImageHauteur > $nouvelleImageDimensionsVoulues['hauteur']))
		{
			$nouvelleImageHauteur = $nouvelleImageDimensionsVoulues['hauteur'];
			$nouvelleImageLargeur = ($nouvelleImageHauteur / $imageSourceHauteur) * $imageSourceLargeur;
		}
	}
	
	$demiSupplementHauteur = 0;
	$demiSupplementLargeur = 0;
	
	if ($galerieForcerDimensionsVignette)
	{
		if ($nouvelleImageDimensionsVoulues['hauteur'] && ($nouvelleImageHauteur < $nouvelleImageDimensionsVoulues['hauteur']))
		{
			$demiSupplementHauteur = ($nouvelleImageDimensionsVoulues['hauteur'] - $nouvelleImageHauteur) / 2;
		}
		
		if ($nouvelleImageDimensionsVoulues['largeur'] && ($nouvelleImageLargeur < $nouvelleImageDimensionsVoulues['largeur']))
		{
			$demiSupplementLargeur = ($nouvelleImageDimensionsVoulues['largeur'] - $nouvelleImageLargeur) / 2;
		}
	}
	
	// Si la nouvelle image est théoriquement au moins aussi grande que l'image source, on ne fait qu'une copie de fichier.
	if ($nouvelleImageHauteur > $imageSourceHauteur || $nouvelleImageLargeur > $imageSourceLargeur)
	{
		if (@copy($cheminImageSource, $cheminNouvelleImage))
		{
			$messagesScript = sprintf(T_("Copie de <code>%1\$s</code> sous le nom <code>%2\$s</code> effectuée."), $nomImageSource, $nomNouvelleImage) . "\n";
		}
		else
		{
			$messagesScript = sprintf(T_("Copie de <code>%1\$s</code> sous le nom <code>%2\$s</code> impossible."), $nomImageSource, $nomNouvelleImage) . "\n";
			$erreur = TRUE;
		}
	}
	// Sinon on génère une nouvelle image avec gd.
	else
	{
		// On crée une nouvelle image vide.
		$nouvelleImage = imagecreatetruecolor($nouvelleImageLargeur + 2 * $demiSupplementLargeur, $nouvelleImageHauteur + 2 * $demiSupplementHauteur);
		
		if ($typeMime == 'image/png')
		{
			imagealphablending($nouvelleImage, false);
			imagesavealpha($nouvelleImage, true);
		}
		
		if ($typeMime == 'image/gif' || ($galerieForcerDimensionsVignette && $typeMime == 'image/jpeg'))
		{
			$blanc = imagecolorallocate($nouvelleImage, 255, 255, 255);
			imagefill($nouvelleImage, 0, 0, $blanc);
		}
		
		if ($typeMime == 'image/png')
		{
			$transparentColor = imagecolorallocatealpha($nouvelleImage, 200, 200, 200, 127);
			imagefill($nouvelleImage, 0, 0, $transparentColor);
		}
		
		// On crée la nouvelle image à partir de l'image source.
		imagecopyresampled($nouvelleImage, $imageSource, $demiSupplementLargeur, $demiSupplementHauteur, 0, 0, $nouvelleImageLargeur, $nouvelleImageHauteur, $imageSourceLargeur, $imageSourceHauteur);
		
		// Netteté demandée.
		if ($nettete)
		{
			$nouvelleImage = UnsharpMask($nouvelleImage, '100', '1', '3');
		}
		
		// On enregistre la nouvelle image.
		switch ($typeMime)
		{
			case 'image/gif':
				if (imagegif($nouvelleImage, $cheminNouvelleImage))
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), $nomNouvelleImage, $nomImageSource) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), $nomNouvelleImage, $nomImageSource) . "\n";
					$erreur = TRUE;
				}
				
				break;
		
			case 'image/jpeg':
				if (imagejpeg($nouvelleImage, $cheminNouvelleImage, $galerieQualiteJpg))
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), $nomNouvelleImage, $nomImageSource) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), $nomNouvelleImage, $nomImageSource) . "\n";
					$erreur = TRUE;
				}
				
				break;
		
			case 'image/png':
				if (imagepng($nouvelleImage, $cheminNouvelleImage, 9))
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), $nomNouvelleImage, $nomImageSource) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), $nomNouvelleImage, $nomImageSource) . "\n";
					$erreur = TRUE;
				}
				
				break;
		}
	}
	
	if ($erreur)
	{
		$messagesScript = '<li class="erreur">' . $messagesScript . "</li>\n";
	}
	else
	{
		$messagesScript = '<li>' . $messagesScript . "</li>\n";
	}
	
	return $messagesScript;
}

/*
Conversion des octets en Kio.
*/
function octetsVersKio($octets)
{
	return number_format($octets / 1024, 1, ',', '');
}

/*
Conversion des octets en Mio.
*/
function octetsVersMio($octets)
{
	return number_format($octets / 1048576, 1, ',', '');
}

/*
Construit et retourne le code pour afficher une oeuvre dans la galerie. Si la taille de l'image n'est pas valide, retourne une chaîne vide.
*/
function oeuvre(
	// Infos sur le site.
	$racine, $urlRacine, $racineImgSrc, $urlImgSrc, $estAccueil, $nombreDeColonnes,
	
	// Infos sur l'image à générer.
	$infosOeuvre, $typeMime, $taille, $sens, $galerieQualiteJpg,
	
	// Exif.
	$galerieExifAjout, $galerieExifInfos,
	
	// Légende.
	$galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown,
	
	// Lien vers l'oeuvre originale.
	$galerieLienOriginalEmplacement, $galerieLienOriginalIcone, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger,
	
	// Navigation.
	$galerieAccueilJavascript, $galerieNavigation,
	
	// Vignettes.
	$galerieDimensionsVignette, $galerieForcerDimensionsVignette, $vignetteAvecDimensions, $minivignetteOeuvreEnCours
)
{
	####################################################################
	#
	# Taille intermédiaire.
	#
	####################################################################
	
	if ($taille == 'intermediaire')
	{
		if (!empty($infosOeuvre['intermediaireLargeur']) || !empty($infosOeuvre['intermediaireHauteur']))
		{
			if (!empty($infosOeuvre['intermediaireLargeur']))
			{
				$width = 'width="' . $infosOeuvre['intermediaireLargeur'] . '"';
			}
			
			if (!empty($infosOeuvre['intermediaireHauteur']))
			{
				$height = 'height="' . $infosOeuvre['intermediaireHauteur'] . '"';
			}
		}
		else
		{
			list ($larg, $haut) = getimagesize($racineImgSrc . '/' . $infosOeuvre['intermediaireNom']);
			{
				$width = 'width="' . $larg . '"';
				$height = 'height="' . $haut . '"';
			}
		}
		
		if (!empty($infosOeuvre['intermediaireAlt']))
		{
			$alt = 'alt="' . $infosOeuvre['intermediaireAlt'] . '"';
		}
		else
		{
			$id = idOeuvre($infosOeuvre);
			$alt = 'alt="' . sprintf(T_("Oeuvre %1\$s"), $id) . '"';
		}
		
		if (!empty($infosOeuvre['intermediaireAttributTitle']))
		{
			$attributTitle = 'title="' . $infosOeuvre['intermediaireAttributTitle'] . '"';
		}
		else
		{
			$attributTitle = '';
		}
		
		if (!empty($infosOeuvre['intermediaireLegende']))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . intermediaireLegende($infosOeuvre['intermediaireLegende'], $galerieLegendeMarkdown) . "</div>\n";
		}
		elseif ($galerieLegendeAutomatique)
		{
			if ($contenuAlt = str_replace('alt="', '', $alt))
			{
				$contenuAlt = substr($contenuAlt, 0, -1);
			}
			
			$legende = "<div id=\"galerieIntermediaireLegende\">$contenuAlt (" . sprintf(T_("%1\$s&nbsp;Kio"), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom))) . ")</div>\n";
		}
		else
		{
			$legende = '';
		}
		
		// Si le nom de l'image au format original a été renseigné, on utilise ce nom.
		if (!empty($infosOeuvre['originalNom']))
		{
			$originalNom = $infosOeuvre['originalNom'];
		}
		// Sinon on génère automatiquement un nom selon le nom de la version intermediaire de l'image.
		else
		{
			$originalNom = nomSuffixe($infosOeuvre['intermediaireNom'], '-original');
		}
		
		// On vérifie maintenant si le fichier `$originalNom` existe. S'il existe, on insère un lien vers l'image.
		if (file_exists($racineImgSrc . '/' . $originalNom))
		{
			$lienOriginalHref = '';
			
			if ($galerieLienOriginalTelecharger && !$galerieLienOriginalJavascript && ($galerieLienOriginalEmplacement == 'legende' || $galerieLienOriginalEmplacement == 'imageLegende'))
			{
				$lienOriginalTrad = sprintf(T_("Télécharger l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)) . '&nbsp;');
				$lienOriginalHref .= $urlRacine . '/telecharger.php?fichier=';
			}
			else
			{
				$lienOriginalTrad = sprintf(T_("Afficher l'image au format original (%1\$s" . "Kio)"), octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)) . '&nbsp;');
			}
			
			$lienOriginalHref .= preg_replace("|^$urlRacine/|", '', $urlImgSrc . '/' . $originalNom);
			
			if ($galerieLienOriginalJavascript && $typeMime != 'image/svg+xml')
			{
				$relOriginal = ' rel="lightbox"';
			}
			else
			{
				$relOriginal = '';
			}
			
			$lienOriginal = '<div id="galerieLienOriginal"><a href="' . $lienOriginalHref . '"' . $relOriginal . '>' . $lienOriginalTrad . "</a></div>\n";
		}
		else
		{
			$lienOriginal = '';
		}
		
		// Exif.
		$exif = '';
		
		if ($galerieExifAjout && $typeMime == 'image/jpeg' && function_exists('exif_read_data'))
		{
			$tableauExif = exif_read_data($racineImgSrc . '/' . $infosOeuvre['intermediaireNom'], 'IFD0', 0);
			
			// Si aucune données Exif n'a été récupérée, on essaie d'en récupérer dans l'image en version originale, si elle existe et si son format est JPG.
			if (!$tableauExif && !empty($lienOriginal) && $typeMime == 'image/jpeg')
			{
				$tableauExif = exif_read_data($racineImgSrc . '/' . $originalNom, 'IFD0', 0);
			}
			
			if ($tableauExif)
			{
				foreach ($galerieExifInfos as $cle => $valeur)
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
						
						$exif .= "<li><em>$exifTrad:</em> " . $tableauExif[$cle] . "</li>\n";
					}
				}
			}
			
			if (!empty($exif))
			{
				$exif = "<div id='galerieIntermediaireExif'>\n<ul>\n" . $exif . "</ul>\n</div><!-- /#galerieIntermediaireExif -->\n";
			}
		}
		
		if (isset($lienOriginalHref) && !empty($lienOriginalHref) && ($galerieLienOriginalEmplacement == 'image' || $galerieLienOriginalEmplacement == 'imageLegende'))
		{
			if ($galerieLienOriginalJavascript && $typeMime != 'image/svg+xml')
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
		
		if ($galerieLienOriginalIcone && isset($lienOriginalHref) && !empty($lienOriginalHref))
		{
			if (file_exists($racine . '/site/fichiers/agrandir.png'))
			{
				$galerieLienOriginalIconeSrc = $urlRacine . '/site/fichiers/agrandir.png';
			}
			else
			{
				$galerieLienOriginalIconeSrc = $urlRacine . '/fichiers/agrandir.png';
			}
			
			$imgLienOriginal = '<div id="galerieIconeOriginal">' . $lienOriginalAvant . '<img src="' . $galerieLienOriginalIconeSrc . '" alt="' . str_replace('&nbsp;', ' ', $lienOriginalTrad) . '" width="22" height="22" />' . $lienOriginalApres . '</div><!-- /#galerieIconeOriginal -->' . "\n";
		}
		else
		{
			$imgLienOriginal = '';
		}
		
		if ($galerieLegendeEmplacement[$nombreDeColonnes] == 'haut' || $galerieLegendeEmplacement[$nombreDeColonnes] == 'bloc')
		{
			return '<div id="galerieIntermediaireTexte">' . $legende . $exif . $lienOriginal . "</div><!-- /#galerieIntermediaireTexte -->\n" . '<div id="galerieIntermediaireImg">' . $lienOriginalAvant . '<img src="' . $urlImgSrc . '/' . $infosOeuvre['intermediaireNom'] . '"' . " $width $height $alt $attributTitle />" . $lienOriginalApres . "</div><!-- /#galerieIntermediaireImg -->\n" . $imgLienOriginal;
		}
		elseif ($galerieLegendeEmplacement[$nombreDeColonnes] == 'bas')
		{
			return '<div id="galerieIntermediaireImg">' . $lienOriginalAvant . '<img src="' . $urlImgSrc . '/' . $infosOeuvre['intermediaireNom'] . '"' . " $width $height $alt $attributTitle />" . $lienOriginalApres . "</div><!-- /#galerieIntermediaireImg -->\n" . $imgLienOriginal . '<div id="galerieIntermediaireTexte">' . $legende . $exif . $lienOriginal . "</div><!-- /#galerieIntermediaireTexte -->\n";
		}
	}
	####################################################################
	#
	# Taille vignette.
	#
	####################################################################
	elseif ($taille == 'vignette')
	{
		if ($galerieNavigation == 'fleches' && ($sens == 'precedent' || $sens == 'suivant'))
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
		elseif (($galerieNavigation == 'fleches' && empty($sens)) || $galerieNavigation == 'vignettes')
		{
			// Si le nom de la vignette a été renseigné, on prend pour acquis que le fichier existe avec ce nom. On assigne donc une valeur à l'attribut `src`.
			if (!empty($infosOeuvre['vignetteNom']))
			{
				$src = 'src="' . $urlImgSrc . '/' . $infosOeuvre['vignetteNom'] . '"';
			}
			// Sinon on génère un nom automatique selon le nom de la version intermediaire de l'image.
			else
			{
				$vignetteNom = nomSuffixe($infosOeuvre['intermediaireNom'], '-vignette');
				
				// On vérifie si un fichier existe avec ce nom.
				// Si oui, on assigne une valeur à l'attribut `src`.
				if (file_exists($racineImgSrc . '/' . $vignetteNom))
				{
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
				// Sinon on génère une vignette.
				else
				{
					nouvelleImage($racineImgSrc . '/' . $infosOeuvre['intermediaireNom'], $racineImgSrc . '/' . $vignetteNom, $typeMime, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, $galerieQualiteJpg, FALSE);
					
					// On assigne l'attribut `src`.
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
			}
			
			if ($vignetteAvecDimensions)
			{
				if (!empty($infosOeuvre['vignetteLargeur']) || !empty($infosOeuvre['vignetteHauteur']))
				{
					if (!empty($infosOeuvre['vignetteLargeur']))
					{
						$width = 'width="' . $infosOeuvre['vignetteLargeur'] . '"';
					}
				
					if (!empty($infosOeuvre['vignetteHauteur']))
					{
						$height = 'height="' . $infosOeuvre['vignetteHauteur'] . '"';
					}
				}
				else
				{
					list ($larg, $haut) = getimagesize($racineImgSrc . '/' . $vignetteNom);
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
		
		$id = idOeuvre($infosOeuvre);
		
		if (!empty($infosOeuvre['vignetteAlt']))
		{
			$alt = 'alt="' . $infosOeuvre['vignetteAlt'] . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Oeuvre %1\$s"), $id) . '"';
		}
		
		if (!empty($infosOeuvre['vignetteAttributTitle']))
		{
			$attributTitle = 'title="' . $infosOeuvre['vignetteAttributTitle'] . '"';
		}
		else
		{
			$attributTitle = '';
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
			if (!empty($infosOeuvre['intermediaireLegende']))
			{
				$title = ' title="' . preg_replace(array ('/</', '/>/', '/"/'), array ('&lt;', '&gt;', "'"), $infosOeuvre['intermediaireLegende']) . '"';
			}
			else
			{
				$title = '';
			}
			
			$aHref = '<a href="' . $urlImgSrc . '/' . $infosOeuvre['intermediaireNom'] . '" rel="lightbox-galerie"' . $title . '>';
		}
		else
		{
			
			$aHref = '<a href="' . url(FALSE, FALSE) . '?oeuvre=' . $id . '">';
		}
		
		// On s'assure que la variable `$class` existe (pour éviter un avertissement).
		if (!isset($class))
		{
			$class = '';
		}
		
		if ($minivignetteOeuvreEnCours)
		{
			$class .= ' minivignetteOeuvreEnCours';
		}
		
		return '<div class="galerieNavigation' . $classAccueil . $class . '">' . $aHref . '<img ' . "$src $width $height $alt $attributTitle /></a></div>\n";
	}
	else
	{
		return '';
	}
}

/*
Construit le code HTML pour afficher une pagination, et retourne un tableau contenant les informations suivantes:

  - `$pagination['pagination']`: code HTML de la pagination;
  - `$pagination['indicePremierElement']`: indice du premier élément de la page en cours;
  - `$pagination['indiceDernierElement']`: indice du dernier élément de la page en cours;
  - `$pagination['baliseTitle']`: contenu de la balise `title` modifié pour prendre en compte la pagination;
  - `$pagination['description']`: contenu de la métabalise `description` modifié pour prendre en compte la pagination;
  - `$pagination['estPageDerreur']`: informe si la page demandée par la variable GET `page` existe. Vaut TRUE si la page n'existe pas.
*/
function pagination($nombreElements, $elementsParPage, $urlSansGet, $baliseTitle, $description)
{
	$pagination = array ();
	$pagination['pagination'] = '';
	$pagination['indicePremierElement'] = 0;
	$pagination['indiceDernierElement'] = 0;
	$pagination['baliseTitle'] = $baliseTitle;
	$pagination['description'] = $description;
	$pagination['estPageDerreur'] = FALSE;
	$nombreDePages = ceil($nombreElements / $elementsParPage);

	if (isset($_GET['page']))
	{
		$page = intval($_GET['page']);
		
		if ($page > $nombreDePages)
		{
			$pagination['estPageDerreur'] = TRUE;
			$page = $nombreDePages;
		}
		elseif ($page < 1)
		{
			$pagination['estPageDerreur'] = TRUE;
			$page = 1;
		}
	}
	else
	{
		$page = 1;
	}
	
	// Ajustement des métabalises.
	if (isset($page) && $page != 1)
	{
		$pagination['baliseTitle'] .= " - Page $page";
		$pagination['description'] .= " - Page $page";
	}
	
	$pagination['indicePremierElement'] = ($page - 1) * $elementsParPage;
	$pagination['indiceDernierElement'] = $pagination['indicePremierElement'] + $elementsParPage - 1;
	
	// Construction de la pagination.
	
	$pagination['pagination'] .= '<div class="pagination">' . "\n";

	// `$lien` va être utilisée pour construire l'URL de la page précédente ou suivante.
	$lien = $urlSansGet . '?';

	// On récupère les variables GET pour les ajouter au lien, sauf `page`.
	if (!empty($_GET))
	{
		foreach ($_GET as $cle => $valeur)
		{
			if ($cle != 'page')
			{
				$lien .= $cle . '=' . $valeur . '&';
			}
		}
	}

	if ($page > 1)
	{
		$numeroPagePrecedent = $page - 1;
		
		// Si la page précédente n'est pas la première, on y fait tout simplement un lien.
		if ($numeroPagePrecedent != 1)
		{
			$lienPrecedent = $lien . 'page=' . $numeroPagePrecedent;
		}
		// Sinon on n'ajoute pas de variable GET `page` et on supprime le dernier caractère (`&` ou `?`).
		else
		{
			$lienPrecedent = substr($lien, 0, -1);
		}
		
		$pagination['pagination'] .= '<a href="' . rawurlencode($lienPrecedent) . '">' . T_("Page précédente") . '</a>';
	}
	
	if ($page < $nombreDePages)
	{
		$numeroPageSuivant = $page + 1;
		$lienSuivant = $lien . 'page=' . $numeroPageSuivant;
		
		if (isset($lienPrecedent))
		{
			$pagination['pagination'] .= ' | ';
		}
		
		$pagination['pagination'] .= '<a href="' . rawurlencode($lienSuivant) . '">' . T_("Page suivante") . '</a>';
	}

	$pagination['pagination'] .= '</div><!-- /.pagination -->' . "\n";
	
	return $pagination;
}

/*
Inclut tout ce qu'il faut pour utiliser php-gettext comme outil de traduction des pages. Retourne TRUE.
*/
function phpGettext($racine, $langue)
{
	if (!defined('LC_MESSAGES'))
	{
		define('LC_MESSAGES', 5);
	}
	
	include_once $racine . '/inc/php-gettext/gettext.inc';
	
	$locale = locale($langue);
	T_setlocale(LC_MESSAGES, $locale);
	$domain = 'squeletml';
	T_bindtextdomain($domain, $racine . '/locale');
	T_bind_textdomain_codeset($domain, 'UTF-8');
	T_textdomain($domain);
	
	return TRUE;
}

/*
Retourne le contenu de la métabalise `robots`.
*/
function robots($robotsParDefaut, $robots)
{
	return $robots ? $robots : $robotsParDefaut;
}

/*
Retourne une chaîne débarrassée de ses barres obliques inverses.
*/
function sansEchappement($chaine)
{
	return stripslashes($chaine);
}

/*
Si la valeur passée en paramètre est une chaîne de caractères, retourne la chaîne traitée pour un affichage sécuritaire à l'écran, sinon si la valeur passée en paramètre est un tableau, retourne un tableau dont chaque élément a été sécurisé, sinon si la valeur passée en paramètre n'est ni une chaîne ni un tableau, retourne une chaîne vide.
*/
function securiseTexte($texte)
{
	if (is_array($texte))
	{
		$texteSecurise = array ();
		
		foreach ($texte as $valeur)
		{
			$texteSecurise[] = securiseTexte($valeur);
		}
		
		return $texteSecurise;
	}
	elseif (is_string($texte))
	{
		return sansEchappement(htmlspecialchars($texte, ENT_COMPAT, 'UTF-8'));
	}
	else
	{
		return '';
	}
}

/*
Génère l'attribut `style` pour les div vide simulant la présence d'une flèche ou d'une vignette de navigation dans la galerie.
*/
function styleDivVideNavigation($oeuvre)
{
	$width = '';
	$height = '';
	
	if (!empty($oeuvre))
	{
		preg_match('/width="(\d+)"/', $oeuvre, $resultatWidth);
		preg_match('/height="(\d+)"/', $oeuvre, $resultatHeight);
		
		if (!empty($resultatWidth[1]))
		{
			$width = $resultatWidth[1];
		}
		
		if (!empty($resultatHeight[1]))
		{
			$height = $resultatHeight[1];
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

/*
Simule la fonction `superBasename()` sans dépendre de la locale. Merci à <http://drupal.org/node/278425>.
*/
function superBasename($chemin, $suffixe = '')
{
	$chemin = preg_replace('|^.+[\\/]|', '', $chemin);
	
	if ($suffixe)
	{
		$chemin = preg_replace('|' . preg_quote($suffixe) . '$|', '', $chemin);
	}
	
	return $chemin;
}

/*
Simule en partie la fonction `parse_ini_file()`, en essayant de contourner certaines limitations concernant les caractères créant des erreurs dans les valeurs non délimitées par des guillemets. Par exemple, peut traiter sans erreur le paramètre suivant:

	lien=<a href="page.php">lien</a>
*/
function super_parse_ini_file($cheminFichier, $creerSections = FALSE)
{
	$tableau = array ();
	
	if ($fic = @fopen($cheminFichier, 'r'))
	{
		$ajouterDansSection = FALSE;
		
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			
			if (preg_match('/^\s*\[([^\]]+)\]\s*$/', $ligne, $resultat) && $creerSections)
			{
				$cle = $resultat[1];
				$tableau[$cle] = array ();
				$ajouterDansSection = $cle;
			}
			elseif (preg_match('/^([^=]+)=(.*)$/', $ligne, $resultat))
			{
				$parametre = trim($resultat[1]);
				$valeur = trim($resultat[2]);
				
				if (!empty($parametre))
				{
					if (preg_match('/\[\]$/', $parametre))
					{
						$parametre = substr($parametre, 0, -2);
						$parametreTableau = TRUE;
					}
					else
					{
						$parametreTableau = FALSE;
					}
					
					if ($ajouterDansSection !== FALSE)
					{
						if ($parametreTableau)
						{
							$tableau[$ajouterDansSection][$parametre][] = $valeur;
						}
						else
						{
							$tableau[$ajouterDansSection][$parametre] = $valeur;
						}
					}
					else
					{
						if ($parametreTableau)
						{
							$tableau[$parametre][] = $valeur;
						}
						else
						{
							$tableau[$parametre] = $valeur;
						}
					}
				}
			}
		}
		
		fclose($fic);
	}
	else
	{
		return FALSE;
	}
	
	return $tableau;
}

/*
Retourne le code HTML sans les commentaires.
*/
function supprimeCommentairesHtml($html)
{
	$dom = str_get_dom($html);
	
	foreach ($dom->find('comment') as $commentaire)
	{
		$commentaire->outertext = '';
	}
	
	$htmlFiltre = $dom->save();
	$dom->clear();
	unset($dom);
	
	return $htmlFiltre;
}

/*
Supprime l'inclusion des feuilles de style par défaut de Squeletml.
*/
function supprimeInclusionCssParDefaut(&$fichiers)
{
	for ($i = 0; $i < 5; $i++)
	{
		unset($fichiers[$i]);
	}
	
	return;
}

/*
Transforme un fichier de configuration `.ini` d'une galerie en tableau PHP. Chaque section du fichier `.ini` devient un tableau dans le tableau principal. Le titre d'une section est transformé en paramètre `intermediaireNom`. Si `$exclure` vaut TRUE, ne tient pas compte des sections ayant un paramètre `exclure=oui`. Par exemple, le fichier `.ini` suivant:

	[image1.png]
	id=1
	vignetteNom=image1Mini.png

devient:

	$galerie = array (
		array (
		'intermediaireNom' => 'image1.png',
		'id' => 1,
		'vignetteNom' => 'image1Mini.png',
		),
	);

Retourne le tableau final si le fichier de configuration existe et est accessible en lecture, sinon retourne FALSE.
*/
function tableauGalerie($cheminConfigGalerie, $exclure = FALSE)
{
	if ($cheminConfigGalerie && ($galerieIni = super_parse_ini_file($cheminConfigGalerie, TRUE)) !== FALSE)
	{
		$galerie = array ();
		
		foreach ($galerieIni as $oeuvre => $infos)
		{
			if (!$exclure || !(isset($infos['exclure']) && $infos['exclure'] == 'oui'))
			{
				$infos['intermediaireNom'] = $oeuvre;
				$galerie[] = $infos;
			}
		}
		
		return $galerie;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le titre du site. Si le titre n'a pas été trouvé, retourne une chaîne vide.
*/
function titreSite($tableauTitreSite, $langues)
{
	foreach ($langues as $langue)
	{
		if (array_key_exists($langue, $tableauTitreSite))
		{
			return $tableauTitreSite[$langue];
		}
	}
	
	return '';
}

/*
Retourne le type MIME du fichier. Il s'agit d'un alias de la fonction `mimedetect_mime()`.
*/
function typeMime($cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	return mimedetect_mime($cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
}

/*
Retourne l'URL de la page courante. Un premier paramètre optionnel, s'il vaut FALSE, permet de ne pas retourner les variables GET. Un deuxième paramètre optionnel, s'il vaut FALSE, permet de retourner seulement l'URL demandée sans la partie serveur.

Note: si l'URL contient une ancre, cette dernière sera perdue, car le serveur n'en a pas connaissance. Par exemple, si l'URL fournie est `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3#ancre`, la fonciton va retourner `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3` si `$retourneVariablesGet` et `$retourneServeur` vallent TRUE.

Fonction inspirée de <http://api.drupal.org/api/function/drupal_detect_baseurl>.
*/
function url($retourneVariablesGet = TRUE, $retourneServeur = TRUE)
{
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
	{
		$protocole = 'https://';
	}
	else
	{
		$protocole = 'http://';
	}
	
	$serveur = securiseTexte($_SERVER['SERVER_NAME']);
	
	if ($_SERVER['SERVER_PORT'] == 80)
	{
		$port = '';
	}
	else
	{
		$port = ':' . securiseTexte($_SERVER['SERVER_PORT']);
	}
	
	$uri = securiseTexte($_SERVER['REQUEST_URI']);
	
	if (!$retourneVariablesGet)
	{
		$uri = preg_replace("/\?.*/", '', $uri);
	}
	
	if ($retourneServeur)
	{
		$url = "$protocole$serveur$port$uri";
	}
	else
	{
		$url = "$uri";
	}
	
	return $url;
}

/*
Retourne l'URL de la page en cours avec la variable GET `action=faireDecouvrir`.
*/
function urlPageAvecDecouvrir()
{
	$url = url();
	
	if (preg_match('/(\?|&amp;)action=faireDecouvrir/', $url))
	{
		return $url . '#formulaireFaireDecouvrir';
	}
	elseif (strstr($url, '?'))
	{
		return "$url&amp;action=faireDecouvrir#formulaireFaireDecouvrir";
	}
	else
	{
		return "$url?action=faireDecouvrir#formulaireFaireDecouvrir";
	}
}

/*
Si le paramètre optionnel vaut TRUE, retourne un tableau contenant l'URL de la page en cours sans la variable GET `action=faireDecouvrir` (si elle existe) ainsi qu'un boléen informant de la présence ou non d'autres variables GET (peu importe lesquelles) après suppression de `action=faireDecouvrir`; sinon retourne une chaîne de caractères équivalant au premier élément du tableau retourné si le paramètre optionnel vaut TRUE.
*/
function urlPageSansDecouvrir($retourneTableau = FALSE)
{
	$urlPageSansDecouvrir = array ();
	$url = url();
	
	if (strstr($url, '?action=faireDecouvrir&amp;'))
	{
		$urlPageSansDecouvrir[0] = str_replace('?action=faireDecouvrir&amp;', '?', $url);
	}
	elseif (preg_match('/\?action=faireDecouvrir$/', $url))
	{
		$urlPageSansDecouvrir[0] = str_replace('?action=faireDecouvrir', '', $url);
	}
	elseif (strstr($url, '&amp;action=faireDecouvrir'))
	{
		$urlPageSansDecouvrir[0] = str_replace('&amp;action=faireDecouvrir', '', $url);
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

/*
Ajoute une deuxième image (une flèche par défaut) à la navigation par vignettes.
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
	
	list ($larg, $haut) = getimagesize($cheminImage);
	$width = 'width="' . $larg . '"';
	$height = 'height="' . $haut . '"';
	preg_match('/alt="([^"]+)"/', $paragraphe, $resultat);
	$altContenu = $resultat[1];
	$alt = 'alt="' . $altContenu . '"';
	$img = "<div id=\"galerieAccompagnementVignette" . ucfirst($sens) . "\"><img src=\"$urlImage\" $alt $width $height /></div>\n";
	
	// On retourne le paragraphe avec l'image en plus.
	if ($sens == 'precedent')
	{
		return preg_replace('/(<img [^>]+>)/', '\1' . $img, $paragraphe);
	}
	elseif ($sens == 'suivant')
	{
		return preg_replace('/(<img [^>]+>)/', '\1' . $img, $paragraphe);
	}
}

/*
Modifie la source de la vignette pour la remplacer par une vignette tatouée d'une autre image (une flèche de navigation par défaut).
*/
function vignetteTatouee($paragraphe, $sens, $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	preg_match('/src="([^"]+)"/', $paragraphe, $resultat);
	$srcContenu = $resultat[1];
	$nomImgSrcContenu = superBasename($srcContenu);
	$vignetteNom = nomSuffixe($nomImgSrcContenu, '-' . $sens);
	
	if (file_exists($racineImgSrc . '/tatouage/' . $vignetteNom))
	{
		$srcContenu = $urlImgSrc . '/tatouage/' . $vignetteNom;
	}
	else
	{
		if (!file_exists($racineImgSrc . '/tatouage'))
		{
			@mkdir($racineImgSrc . '/tatouage');
		}
	
		@copy($racineImgSrc . '/' . $nomImgSrcContenu, $racineImgSrc . '/tatouage/' . $vignetteNom);
		
		if (file_exists($racine . '/site/fichiers/' . $sens . '-tatouage.png'))
		{
			$imgSrc = imagecreatefrompng($racine . '/site/fichiers/' . $sens . '-tatouage.png');
		}
		else
		{
			$imgSrc = imagecreatefrompng($racine . '/fichiers/' . $sens . '-tatouage.png');
		}
		
		$typeMime = typeMime($racineImgSrc . '/tatouage/' . $vignetteNom, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
		switch ($typeMime)
		{
			case 'image/gif':
				$imgDest = imagecreatefromgif($racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
	
			case 'image/jpeg':
				$imgDest = imagecreatefromjpeg($racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
		
			case 'image/png':
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
	
		switch ($typeMime)
		{
			case 'image/gif':
				imagegif($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom);
				break;
	
			case 'image/jpeg':
				imagejpeg($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom, $galerieQualiteJpg);
				break;
		
			case 'image/png':
				imagepng($imgDest, $racineImgSrc . '/tatouage/' . $vignetteNom, 9);
				break;
		}
	}
	
	// On retourne le paragraphe avec l'attribut `src` modifié.
	return preg_replace('/src="[^"]+"/', 'src="' . $urlImgSrc . '/tatouage/' . $vignetteNom . '"', $paragraphe);
}
?>
