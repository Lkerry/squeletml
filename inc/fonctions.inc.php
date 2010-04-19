<?php
/*
Retourne TRUE si l'administration est protégée, sinon retourne FALSE.

À noter une restriction importante de cette fonction: une vérification est effectuée pour savoir si le fichier `.acces` contient au moins un utilisateur et si une directive `AuthUserFile` pointe vers ce fichier dans `.htaccess`, mais aucune vérification n'est effectuée pour savoir si la directive `AuthUserFile` est bien prise en compte par le serveur.
*/
function accesAdminEstProtege($racine)
{
	$accesAdminEstProtege = FALSE;
	
	if (file_exists($racine . '/.htaccess') && file_exists($racine . '/.acces'))
	{
		$htaccess = @file_get_contents($racine . '/.htaccess');
		$acces = @file_get_contents($racine . '/.acces');
		
		if ($htaccess !== FALSE && $acces !== FALSE && preg_match('/^\tAuthUserFile ' . preg_quote($racine, '/') . '\/\.acces\n/m', $htaccess) && preg_match('/^[^:]+:/m', $acces))
		{
			$accesAdminEstProtege = TRUE;
		}
	}
	
	return $accesAdminEstProtege;
}

/*
Ajoute dans le fichier `.htaccess` le code nécessaire à la protection de certains fichiers dont l'accès est réservé aux utilisateurs listés dans `.acces`. Si une erreur survient, retourne le résultat sous forme de message concaténable dans `$messagesScript`, sinon retourne une chaîne vide.
*/
function accesDansHtaccess($racine, $serveurFreeFr)
{
	$messagesScript = '';
	
	if (file_exists($racine . '/.acces') && strpos(file_get_contents($racine . '/.acces'), ':') !== FALSE)
	{
		$lienAccesDansHtaccess = FALSE;
	
		if ($fic = @fopen($racine . '/.htaccess', 'r'))
		{
			while (!feof($fic))
			{
				$ligne = rtrim(fgets($fic));
			
				if (preg_match('/^# Ajout automatique de Squeletml \(accès admin\). Ne pas modifier./', $ligne))
				{
					$lienAccesDansHtaccess = TRUE;
					break;
				}
			}
			
			fclose($fic);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), "<code>$racine/.htaccess</code>") . "</li>\n";
		}

		if (!$lienAccesDansHtaccess)
		{
			$htaccess = '';
			$htaccess .= "# Ajout automatique de Squeletml (accès admin). Ne pas modifier.\n";
			$htaccess .= "# Empêcher l'affichage direct de certains fichiers.\n";
		
			$htaccessFilesModele = "(ChangeLog|ChangeLog-version-actuelle|ChangeLog-version-actuelle-fichiers|\.acces|\.admin\.php|\.cache\.gif|\.cache\.html|\.cache\.jpeg|\.cache\.jpg|\.cache\.png|\.cache\.xml|\.defaut|\.ini|\.mdtxt|\.txt)$";
		
			if ($serveurFreeFr)
			{
				$htaccess .= "<Files ~ \"$htaccessFilesModele\">\n";
	
				preg_match('|/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+(.+)|', $racine . '/.acces', $cheminAcces);
	
				$htaccess .= "\tPerlSetVar AuthFile " . $cheminAcces[1] . "\n";
			}
			else
			{
				$htaccess .= "<FilesMatch \"$htaccessFilesModele\">\n";
				$htaccess .= "\tAuthUserFile $racine/.acces\n";
			}

			$htaccess .= "\tAuthType Basic\n";
			$htaccess .= "\tAuthName \"Administration de Squeletml\"\n";
			$htaccess .= "\tRequire valid-user\n";

			if ($serveurFreeFr)
			{
				$htaccess .= "</Files>\n";
			}
			else
			{
				$htaccess .= "</FilesMatch>\n";
			}
		
			$htaccessFilesModele = "deconnexion\.php$";
		
			if ($serveurFreeFr)
			{
				$htaccess .= "<Files ~ \"$htaccessFilesModele\">\n";
	
				preg_match('|/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+(.+)|', $racine . '/.deconnexion.acces', $cheminAcces);
	
				$htaccess .= "\tPerlSetVar AuthFile " . $cheminAcces[1] . "\n";
			}
			else
			{
				$htaccess .= "<FilesMatch \"$htaccessFilesModele\">\n";
				$htaccess .= "\tAuthUserFile $racine/.deconnexion.acces\n";
			}

			$htaccess .= "\tAuthType Basic\n";
			$htaccess .= "\tAuthName \"Administration de Squeletml\"\n";
			$htaccess .= "\tRequire valid-user\n";

			if ($serveurFreeFr)
			{
				$htaccess .= "</Files>\n";
			}
			else
			{
				$htaccess .= "</FilesMatch>\n";
			}
		
			$htaccess .= "# Fin de l'ajout automatique de Squeletml (accès admin).\n";

			if ($fic = @fopen($racine . '/.htaccess', 'a+'))
			{
				fputs($fic, $htaccess);
				fclose($fic);
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), "<code>$racine/.htaccess</code>") . "</li>\n";
			}
		}
	}
	
	return $messagesScript;
}

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
	else
	{
		$action .= '#messages';
	}
	
	return $action;
}

/*
Ajoute au tableau des catégories la catégorie spéciale `site` (si elle n'existe pas déjà) contenant les dernières publications du site (pages déclarées dans le fichier de configuration du flux RSS des dernières publications), et retourne le tableau résultant.
*/
function ajouteCategoriesSpeciales($racine, $urlRacine, $langue, $categories, $categoriesSpecialesAajouter, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger)
{
	if (in_array('galeries', $categoriesSpecialesAajouter) && !isset($categories['galeries']))
	{
		$itemsFluxRss = fluxRssGaleriesTableauBrut($racine, $urlRacine, $langue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
		
		if (!empty($itemsFluxRss))
		{
			$itemsFluxRss = fluxRssTableauFinal('galeries', $itemsFluxRss, $nombreItemsFluxRss);
			$categories = array ('galeries' => array ()) + $categories;
			$categories['galeries']['langueCat'] = $langue;
			$categories['galeries']['urlCat'] = "categorie.php?id=galeries&amp;langue=$langue";
			$categories['galeries']['pages'] = array ();
			
			foreach ($itemsFluxRss as $item => $infosItem)
			{
				$categories['galeries']['pages'][] = str_replace($urlRacine . '/', '', rawurldecode($infosItem['link']));
			}
		}
	}
	
	if (in_array('site', $categoriesSpecialesAajouter) && !isset($categories['site']))
	{
		$cheminFichier = cheminConfigFluxRssGlobal($racine, 'site');
	
		if ($cheminFichier)
		{
			$pages = super_parse_ini_file($cheminFichier, TRUE);
		}
	
		if (isset($pages[$langue]))
		{
			$categories = array ('site' => array ()) + $categories;
			$categories['site']['langueCat'] = $langue;
			$categories['site']['urlCat'] = "categorie.php?id=site&amp;langue=$langue";
			$categories['site']['pages'] = array ();
		
			foreach ($pages[$langue]['pages'] as $page)
			{
				$categories['site']['pages'][] = $page;
			}
		}
	}
	
	return $categories;
}

/*
Ajoute la variable GET à l'adresse fournie, et retourne le résultat. `$get` doit être sous la forme `cle=valeur` ou `cle`.
*/
function ajouteGet($adresse, $get)
{
	if (!empty($get))
	{
		if (strpos($adresse, '?') !== FALSE)
		{
			$adresse .= '&amp;' . $get;
		}
		else
		{
			$adresse .= '?' . $get;
		}
	}
	
	return $adresse;
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
	
	$texte .= '<pre class="fichierDeConfiguration">' . coloreFichierPhp($racine . '/inc/config.inc.php', TRUE, TRUE) . "</pre>\n\n";
	
	$texte .= '### ' . T_("Contenu du fichier de configuration de l'administration de Squeletml") . "\n\n";
	
	$texte .= '<pre class="fichierDeConfiguration">' . coloreFichierPhp($racineAdmin . '/inc/config.inc.php', TRUE, TRUE) . "</pre>\n\n";
	
	return $texte;
}

/*
Retourne le code HTML de l'aperçu d'une page apparaissant dans une page de catégorie.
*/
function apercuDansCategorie($racine, $urlRacine, $infosPage, $adresse, $baliseTitleComplement, $langueParDefaut)
{
	$apercu = '';
	$apercu .= "<div class=\"apercu\">\n";
	
	if (!empty($baliseTitleComplement))
	{
		$infosPage['titre'] = preg_replace('/' . preg_quote($baliseTitleComplement, '/') . '$/', '', $infosPage['titre']);
	}

	$apercu .= "<h2 class=\"titreApercu\"><a href=\"$adresse\">{$infosPage['titre']}</a></h2>\n";
	$listeCategoriesAdresse = categories($racine, $urlRacine, $adresse, $langueParDefaut);
	$infosPublication = infosPublication($urlRacine, $infosPage['auteur'], $infosPage['dateCreation'], $infosPage['dateRevision'], $listeCategoriesAdresse);

	if (!empty($infosPublication))
	{
		$apercu .= "<div class=\"infosPublicationApercu\">\n";
		$apercu .= $infosPublication;
		$apercu .= "</div><!-- /.infosPublicationApercu -->\n";
	}

	$apercu .= "<div class=\"descriptionApercu\">\n";

	if (!empty($infosPage['apercu']))
	{
		$apercu .= $infosPage['apercu'] . "\n";
	}
	else
	{
		$apercu .= $infosPage['contenu'] . "\n";
	}

	$apercu .= "</div><!-- /.descriptionApercu -->\n";

	if (!empty($infosPage['apercu']))
	{
		$apercu .= "<div class=\"lienApercu\">\n";
		$apercu .= sprintf(T_("Lire la suite de %1\$s"), "<em><a href=\"$adresse\">" . $infosPage['titre'] . '</a></em>') . "\n";
		$apercu .= "</div><!-- /.lienApercu -->\n";
	}

	$apercu .= "</div><!-- /.apercu -->\n";

	return $apercu;
}

/*
Retourne le contenu de la balise `title`.
*/
function baliseTitle($baliseTitle, $baliseH1)
{
	if (empty($baliseTitle))
	{
		if (!empty($baliseH1))
		{
			return $baliseH1;
		}
		else
		{
			return urlPageSansDecouvrir();
		}
	}
	else
	{
		return $baliseTitle;
	}
}

/*
Retourne le complément de la balise `title`. Si aucun complément, n'a été trouvé, retourne une chaîne vide.
*/
function baliseTitleComplement($tableauBaliseTitleComplement, $langues, $estAccueil)
{
	if ($estAccueil)
	{
		$premierElementAtester = 'accueil';
		$deuxiemeElementAtester = 'interne';
	}
	else
	{
		$premierElementAtester = 'interne';
		$deuxiemeElementAtester = 'accueil';
	}
	
	foreach ($langues as $langue)
	{
		if (isset($tableauBaliseTitleComplement[$langue][$premierElementAtester]))
		{
			return $tableauBaliseTitleComplement[$langue][$premierElementAtester];
		}
		elseif (isset($tableauBaliseTitleComplement[$langue][$deuxiemeElementAtester]))
		{
			return $tableauBaliseTitleComplement[$langue][$deuxiemeElementAtester];
		}
	}
	
	return '';
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
Prend en argument un contenu qui sera retourné balisé pour apparaître dans une boîte avec des coins arrondis.
*/
function boiteArrondie($contenu)
{
	$code = '';
	list ($codeInterieurHaut, $codeInterieurBas) = codeInterieurBloc(FALSE, array (), '', 0, TRUE);
	$code .= '<div class="blocArrondi">' . "\n";
	$code .= $codeInterieurHaut;
	$code .= "$contenu\n";
	$code .= $codeInterieurBas;
	$code .= '</div><!-- /.blocArrondi -->' . "\n";
	
	return $code;
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
	if (time() - filemtime($fichier) > $dureeCache)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le code à insérer dans un formulaire pour afficher un antipourriel basé sur un calcul mathématique.
*/
function captchaCalcul($calculMin = 2, $calculMax = 10, $calculInverse = TRUE)
{
	$calculUn = mt_rand($calculMin, $calculMax);
	$calculDeux = mt_rand($calculMin, $calculMax);
	$captchaCalcul = '';
	$inputHidden = '';
	$inputHidden .= '<input type="hidden" name="u" value="' . $calculUn . '" />' . "\n";
	
	// Ajout de quelques `input` bidons pour augmenter les chances de duper les robots pourrielleurs.
	$nbreInput = mt_rand(5, 10);
	$lettresExcluses = 'drsu';
	
	for ($i = 0; $i < $nbreInput; $i++)
	{
		$lettreAuHasard = lettreAuHasard($lettresExcluses);
		$lettresExcluses .= $lettreAuHasard;
		$inputHidden .= '<input type="hidden" name="' . $lettreAuHasard . '" value="' . mt_rand($calculMin, $calculMax) . '" />' . "\n";
	}
	
	$inputHidden .= '<input type="hidden" name="d" value="' . $calculDeux . '" />' . "\n";
	
	$captchaCalcul .= '<p><label>' . T_("Antipourriel:") . "</label><br />\n";
	
	if ($calculInverse)
	{
		$captchaCalcul .= sprintf(T_("Veuillez indiquer deux nombres qui, une fois additionnés, donnent %1\$s (plusieurs réponses possibles):"), $calculUn);
		$captchaCalcul .= sprintf(T_("%1\$s et %2\$s"), "<input name=\"r\" type=\"text\" size=\"4\" />", "<input name=\"s\" type=\"text\" size=\"4\" />");
	}
	else
	{
		$captchaCalcul .= sprintf(T_("Veuillez compléter: %1\$s ajouté à %2\$s vaut %3\$s"), $calculUn, $calculDeux, "<input name=\"r\" type=\"text\" size=\"4\" />");
	}

	$captchaCalcul .= "</p>\n";

	$captchaCalcul .= $inputHidden;

	return $captchaCalcul;
}

/*
Retourne un tableau des catégories auxquelles appartient l'URL fournie. La structure est:

	$listeCategories['idCategorie'] = 'urlCat';

Fournir une URL traitée par `superRawurlencode()`.
*/
function categories($racine, $urlRacine, $url, $langueParDefaut)
{
	$listeCategories = array ();
	$cheminFichier = cheminConfigCategories($racine);
	
	if ($cheminFichier && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE && !empty($categories))
	{
		foreach ($categories as $categorie => $categorieInfos)
		{
			foreach ($categorieInfos['pages'] as $page)
			{
				$urlPage = $urlRacine . '/' . rtrim($page);
				
				if (superRawurlencode($urlPage) == $url)
				{
					$listeCategories[$categorie] = urlCat($categorieInfos, $categorie, $langueParDefaut);
				}
			}
		}
	}
	
	ksort($listeCategories);
	
	return $listeCategories;
}

/*
S'il y a lieu, ajoute la classe `actif` au lien de chaque catégorie à laquelle la page fait partie ainsi qu'au `li` contenant le lien. Retourne le code résultant.
*/
function categoriesActives($codeMenuCategories, $listeCategoriesPage, $idCategorie)
{
	if (!empty($listeCategoriesPage) || !empty($idCategorie))
	{
		$dom = str_get_html($codeMenuCategories);
	
		foreach ($dom->find('a') as $a)
		{
			$actif = FALSE;
			
			if (!empty($listeCategoriesPage))
			{
				foreach ($listeCategoriesPage as $categorie => $urlCategorie)
				{
					if ($a->href == $urlCategorie)
					{
						$actif = TRUE;
						break;
					}
				}
			}
			
			if (!$actif && !empty($idCategorie) && $a->innertext == $idCategorie)
			{
				$actif = TRUE;
			}
			
			if ($actif)
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
		
		$codeMenuCategoriesFiltre = $dom->save();
		$dom->clear();
		unset($dom);
		
		return $codeMenuCategoriesFiltre;
	}
	else
	{
		return $codeMenuCategories;
	}
}

/*
Retourne un tableau contenant la liste des catégories enfants d'une catégorie donnée. Les catégories enfants doivent avoir la même langue que la catégorie parente.
*/
function categoriesEnfants($categories, $categorie, $langueParDefaut)
{
	$categoriesEnfants = array ();
	
	foreach ($categories as $cat => $catInfos)
	{
		if (isset($catInfos['catParente']) && $catInfos['catParente'] == $categorie)
		{
			if (langueCat($catInfos, $langueParDefaut) == langueCat($categories[$categorie], $langueParDefaut))
			{
				$categoriesEnfants[] = $cat;
			}
		}
	}
	
	return $categoriesEnfants;
}

/*
Retourne un tableau contenant la liste des catégories parentes indirectes d'une catégorie donnée. Par exemple, si la catégorie donnée est «Miniatures», que cette dernière a comme catégorie parente «Chiens», et que la catégorie «Chiens» est une catégorie enfant de «Animaux», la fonction va retourner `array ('Chiens', 'Animaux')`.

Note: chaque catégorie parente doit avoir la même langue que la catégorie donnée.
*/
function categoriesParentesIndirectes($categories, $categorie, $langueParDefaut)
{
	$categoriesParentesIndirectes = array ();
	
	if (isset($categories[$categorie]['catParente']))
	{
		$idCatParente = $categories[$categorie]['catParente'];
	}
	else
	{
		$idCatParente = '';
	}
	
	if (!empty($idCatParente) && langueCat($categories[$idCatParente], $langueParDefaut) == langueCat($categories[$categorie], $langueParDefaut))
	{
		$categoriesParentesIndirectes[] = $idCatParente;
		$categoriesParentesIndirectes = array_merge($categoriesParentesIndirectes, categoriesParentesIndirectes($categories, $idCatParente, $langueParDefaut));
	}
	
	return array_unique($categoriesParentesIndirectes);
}

/*
Retourne la chaîne fournie en paramètre filtrée convenablement pour un nom de classe CSS.
*/
function chaineVersClasseCss($racine, $chaine)
{
	$classe = filtreChaine($racine, rawurldecode($chaine));
	$classe = str_replace(array ('.', '+'), '-', $classe);
	$classe = filtreChaine($racine, $classe);
	$classe = preg_replace('/(^[-0-9_]+)|([-_]+$)/', '', $classe);
	
	return $classe;
}

/*
Retourne le chapeau balisé. Optionnellement, le chapeau peut être rédigé à l'aide de la syntaxe Markdown. Pour ce faire, s'assurer que le deuxième paramètre vaille TRUE.
*/
function chapeau($contenuChapeau, $chapeauEnMarkdown = FALSE)
{
	$chapeau = '';
	$chapeau .= "<div class=\"chapeau\">\n";
	$chapeau .= '<p class="legende"><span>' . T_("Résumé") . "</span></p>\n";
	
	$chapeau .= "<div class=\"contenuChapeau\">\n";
	
	if ($chapeauEnMarkdown)
	{
		$contenuChapeau = mdtxtChaine($contenuChapeau);
	}
	
	$chapeau .= $contenuChapeau;
	$chapeau .= "</div><!-- /.contenuChapeau -->\n";
	$chapeau .= "</div><!-- /.chapeau -->\n";
	
	return $chapeau;
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
Retourne le chemin vers le fichier de configuration du flux RSS global des galeries ou du site, selon le nom passé en paramètre. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigFluxRssGlobal($racine, $nom, $retourneCheminParDefaut = FALSE)
{
	if (file_exists("$racine/site/inc/rss-$nom.ini.txt"))
	{
		return "$racine/site/inc/rss-$nom.ini.txt";
	}
	elseif (file_exists("$racine/site/inc/rss-$nom.ini"))
	{
		return "$racine/site/inc/rss-$nom.ini";
	}
	elseif ($retourneCheminParDefaut)
	{
		return "$racine/site/inc/rss-$nom.ini.txt";
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
Si `$retourneChemin` vaut TRUE, retourne le chemin vers le fichier `(site/)xhtml/(LANGUE/)$nom.inc.php` demandé. Si aucun fichier n'a été trouvé, retourne une chaîne vide. Si `$retourneChemin` vaut FALSE, retourne TRUE si un fichier a été trouvé, sinon retourne FALSE.
*/
function cheminXhtml($racine, $langues, $nom, $retourneChemin = TRUE)
{
	foreach ($langues as $langue)
	{
		if (file_exists("$racine/site/xhtml/$langue/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/site/xhtml/$langue/$nom.inc.php" : TRUE;
		}
		elseif (file_exists("$racine/site/xhtml/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/site/xhtml/$nom.inc.php" : TRUE;
		}
	}
	
	foreach ($langues as $langue)
	{
		if (file_exists("$racine/xhtml/$langue/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/xhtml/$langue/$nom.inc.php" : TRUE;
		}
		elseif (file_exists("$racine/xhtml/$nom.inc.php"))
		{
			return $retourneChemin ? "$racine/xhtml/$nom.inc.php" : TRUE;
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
Retourne le mot de passe chiffré.
*/
function chiffreMotDePasse($motDePasse)
{
	return '{SHA}' . base64_encode(sha1($motDePasse, TRUE));
}

/*
Retourne une liste de classes pour `body`.
*/
function classesBody($racine, $url, $estAccueil, $idCategorie, $idGalerie, $courrielContact, $listeCategoriesPage, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $borduresPage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $tableDesMatieresArrondie, $galerieAccueilJavascriptCouleurNavigation, $classesSupplementaires)
{
	$classesBody = '';
	$arrierePlanColonne = 'Avec' . ucfirst($arrierePlanColonne);
	
	if ($estAccueil)
	{
		$classesBody .= 'accueil ';
	}
	
	if (!empty($idCategorie))
	{
		$classesBody .= 'categorie ';
	}
	
	if (!empty($idGalerie))
	{
		$classesBody .= 'galerie ';
		
		if ($nombreDeColonnes == 0)
		{
			$classesBody .= 'galerieAucuneColonne ';
		}
		
		if (preg_match('/(\?|&(amp;)?)image\=(.+?)(&(amp;)?|$)/', $url, $resultat))
		{
			$classesBody .= 'galeriePageImage ';
			$classesBody .= chaineVersClasseCss($racine, 'image-' . $resultat[3]) . ' ';
		}
		else
		{
			$classesBody .= 'galerieAccueil ';
			
			if (!empty($galerieAccueilJavascriptCouleurNavigation) && $galerieAccueilJavascriptCouleurNavigation != 'blanc')
			{
				$classesBody .= "slimbox2$galerieAccueilJavascriptCouleurNavigation ";
			}
		}
	}
	
	if (!empty($courrielContact))
	{
		$classesBody .= 'contact ';
	}
	
	if (empty($idCategorie) && empty($idGalerie) && empty($courrielContact))
	{
		$classesBody .= 'pageStandard ';
	}
	
	if (!empty($listeCategoriesPage))
	{
		$classesBody .= 'article ';
		
		foreach ($listeCategoriesPage as $categoriePage => $urlCategoriePage)
		{
			$classesBody .= chaineVersClasseCss($racine, "article-$categoriePage") . ' ';
		}
	}
	
	if (empty($idCategorie) && empty($idGalerie) && empty($courrielContact) && empty($listeCategoriesPage))
	{
		$classesBody .= 'pageStandardSansCategorie ';
	}
	
	if ($nombreDeColonnes == 2)
	{
		$classesBody .= 'deuxColonnes colonneAgauche colonneAdroite ';
		
		if ($arrierePlanColonne != 'Aucun')
		{
			$classesBody .= "deuxColonnes$arrierePlanColonne ";
		}
		
		if ($deuxColonnesSousContenuAgauche)
		{
			$classesBody .= "deuxColonnesSousContenuAgauche ";
		}
		else
		{
			$classesBody .= "deuxColonnesSousContenuAdroite ";
		}
	}
	elseif ($nombreDeColonnes == 1)
	{
		$classesBody .= 'uneColonne ';
		
		if ($uneColonneAgauche)
		{
			$classesBody .= "colonneAgauche uneColonneAgauche ";
			
			if ($arrierePlanColonne != 'Aucun')
			{
				$classesBody .= "colonneAgauche$arrierePlanColonne ";
			}
		}
		else
		{
			$classesBody .= "colonneAdroite uneColonneAdroite ";
			
			if ($arrierePlanColonne != 'Aucun')
			{
				$classesBody .= "colonneAdroite$arrierePlanColonne ";
			}
		}
	}
	elseif ($nombreDeColonnes == 0)
	{
		$classesBody .= "aucuneColonne ";
	}
	
	if ($borduresPage['droite'])
	{
		$classesBody .= 'bordureDroitePage ';
	}
	
	if ($borduresPage['bas'])
	{
		$classesBody .= 'bordureBasPage ';
	}
	
	if ($borduresPage['gauche'])
	{
		$classesBody .= 'bordureGauchePage ';
	}
	
	if ($enTetePleineLargeur && ($nombreDeColonnes == 1 || $nombreDeColonnes == 2))
	{
		$classesBody .= 'enTetePleineLargeur ';
	}
	
	if ($differencierLiensVisitesHorsContenu)
	{
		$classesBody .= 'liensVisitesDifferencies ';
	}
	
	if ($tableDesMatieresArrondie)
	{
		$classesBody .= 'tableDesMatieresArrondie ';
	}
	
	$urlAvecGetSansServeurAvecIndex = url(TRUE, FALSE, TRUE);
	$classesBody .= chaineVersClasseCss($racine, $urlAvecGetSansServeurAvecIndex) . ' ';
	
	if (dirname($urlAvecGetSansServeurAvecIndex) != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss($racine, dirname($urlAvecGetSansServeurAvecIndex)) . ' ';
	}
	
	if (superBasename($urlAvecGetSansServeurAvecIndex) != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss($racine, superBasename($urlAvecGetSansServeurAvecIndex)) . ' ';
	}
	
	$urlSansGetSansServeurAvecIndex = url(FALSE, FALSE, TRUE);
	
	if ($urlSansGetSansServeurAvecIndex != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss($racine, $urlSansGetSansServeurAvecIndex) . ' ';
		$classesBody .= chaineVersClasseCss($racine, superBasename($urlSansGetSansServeurAvecIndex)) . ' ';
	}
	
	if (!empty($classesSupplementaires))
	{
		$classesBody .= trim($classesSupplementaires) . ' ';
	}
	
	return $classesBody;
}

/*
Retourne une liste de classes pour la div `contenu`.
*/
function classesContenu($differencierLiensVisitesHorsContenu, $classesSupplementaires)
{
	$classesContenu = '';
	
	if (!$differencierLiensVisitesHorsContenu)
	{
		$classesContenu .= 'liensVisitesDifferencies ';
	}
	
	if (!empty($classesSupplementaires))
	{
		$classesContenu .= trim($classesSupplementaires) . ' ';
	}
	
	return $classesContenu;
}

/*
Retourne un tableau dont le premier élément contient le code débutant l'intérieur d'un bloc (donc ce qui suit l'ouverture d'une `div` de classe `bloc`); et le deuxième élément, le code terminant l'intérieur d'un bloc (donc ce qui précède la fermeture d'une `div` de classe `bloc`). Si `$forcerBlocArrondi` vaut TRUE, le code retourné sera nécessairement celui pour un bloc arrondi.
*/
function codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes, $forcerBlocArrondi = FALSE)
{
	$codeInterieurBloc = array ();
	$codeInterieurBloc[0] = "\n\t";
	$codeInterieurBloc[1] = "\n\t" . '</div><!-- /.contenuBloc -->';
	
	if ($forcerBlocArrondi || estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes))
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
Retourne le contenu accessible à l'URL fournie en paramètre. Si l'URL n'est pas accessible, retourne FALSE.

Fournir une URL traitée par `superRawurlencode()`.
*/
function contenuUrl($url)
{
	if (function_exists('curl_init'))
	{
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		@curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		$contenu = @curl_exec($ch);
		@curl_close($ch);
	}
	else
	{
		$contenu = @file_get_contents($url);
	}
	
	return $contenu;
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
			
			list ($codeInterieurBlocHaut, $codeInterieurBlocBas) = codeInterieurBloc($blocsArrondisParDefaut, $blocsArrondisSpecifiques, 'legende-image-galerie', $nombreDeColonnes);
			
			if (estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, 'legende-image-galerie', $nombreDeColonnes))
			{
				$classeBlocArrondi = ' blocArrondi';
			}
			else
			{
				$classeBlocArrondi = '';
			}
			
			$tableauCorpsGalerie['texteIntermediaire'] = '<div id="galerieIntermediaireTexteHorsContenu" class="bloc' . $classeBlocArrondi . '">' . $codeInterieurBlocHaut . '<h2>' . T_("Légende de l'image") . "</h2>\n" . $resultat[1] . $codeInterieurBlocBas . '</div><!-- /#galerieIntermediaireTexteHorsContenu -->' . "\n";
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
Envoie un courriel. Retourne TRUE si le courriel a été envoyé, sinon retourne FALSE.

Le tableau en paramètre peut contenir les informations suivantes:

  - `$infos['From']` (optionnel);
  - `$infos['ReplyTo']` (optionnel);
  - `$infos['Bcc']` (optionnel);
  - `$infos['format']` (optionnel): le format texte (`plain`) est celui par défaut. Peut valoir également `html`;
  - `$infos['destinataire']` (obligatoire);
  - `$infos['objet']` (obligatoire);
  - `$infos['message']` (obligatoire);
*/
function courriel($infos)
{
	if (!isset($infos['destinataire']) || !isset($infos['objet']) || !isset($infos['message']))
	{
		return FALSE;
	}
	else
	{
		$enTete = '';
	
		if (!empty($infos['From']))
		{
			$enTete .= "From: {$infos['From']}\n";
		}
	
	
		if (!empty($infos['ReplyTo']))
		{
			$enTete .= "Reply-to: {$infos['ReplyTo']}\n";
		}
	

		if (!empty($infos['Bcc']))
		{
			$enTete .= "Bcc: {$infos['Bcc']}\n";
		}
	
		$enTete .= "MIME-Version: 1.0\n";
	
		if (isset($infos['format']) && $infos['format'] == 'html')
		{
			$format = 'html';
		}
		else
		{
			$format = 'plain';
		}
	
		$enTete .= "Content-Type: text/$format; charset=\"utf-8\"\n";
		$enTete .= "X-Mailer: Squeletml\n";
	
		return @mail($infos['destinataire'], $infos['objet'], $infos['message'], $enTete);
	}
}

/*
Retourne TRUE si l'adresse courriel a une forme valide, sinon retourne FALSE.
*/
function courrielValide($courriel)
{
	$motifCourriel = "/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i";
	
	return preg_match($motifCourriel, $courriel);
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
Retourne un tableau d'URL à visiter par le cron pour une catégorie donnée.
*/
function cronUrlCategorie($racine, $urlRacine, $categorie, $idCategorie, $nombreArticlesParPageCategorie, $langueParDefaut)
{
	$tableauUrl = array ();
	
	if ($nombreArticlesParPageCategorie)
	{
		$nombreArticles = count($categorie['pages']);
		$nombreDePages = ceil($nombreArticles / $nombreArticlesParPageCategorie);
	}
	else
	{
		$nombreDePages = 1;
	}
	
	$categorie['langueCat'] = langueCat($categorie, $langueParDefaut);
	$categorie['urlCat'] = urlCat($categorie, $idCategorie, $langueParDefaut);
	$nomFichierCache = filtreChaine($racine, "categorie-$idCategorie-page-1-" . $categorie['langueCat'] . '.cache.html');
$tableauUrl[] = array ('url' => $urlRacine . '/' . $categorie['urlCat'], 'cache' => $nomFichierCache);
	
	if ($nombreDePages > 1)
	{
		for ($i = 2; $i <= $nombreDePages; $i++)
		{
			$adresse = ajouteGet($urlRacine . '/' . $categorie['urlCat'], "page=$i");
			$nomFichierCache = filtreChaine($racine, "categorie-$idCategorie-page-$i-" . $categorie['langueCat'] . '.cache.html');
			$tableauUrl[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
		}
	}
	
	return $tableauUrl;
}

/*
S'il ne vaut pas FALSE, le contenu est balisé et ensuite affiché. Retourne une chaîne vide.
*/
function cUrlCategorie($contenu, $infos)
{
	if ($contenu !== FALSE)
	{
		echo "<!-- `cUrlCategorie()`: {$infos['url']} -->$contenu<!-- /`cUrlCategorie()`: {$infos['url']} -->";
	}
	
	return '';
}

/*
Affiche une ligne de rapport cron pour une requête effectuée avec `RollingCurl`. Retourne une chaîne vide.
*/
function cUrlCronRapport($contenu, $infos)
{
	$rapport = '';
	
	if (preg_match('/^[23]/', $infos['http_code']))
	{
		$rapport .= '<li>1: ';
	}
	else
	{
		$rapport .= '<li class="erreur">0: ';
	}
	
	$rapport .= 'RollingCurl: <code>' . $infos['url'] . "</code></li>\n";
	echo $rapport;
	
	return '';
}

/*
Le paramètre `$date` doit être une date sous une des formes suivantes:

  - `année` (exemple: `2010`);
  - `année-mois` (exemple: `2010-01`);
  - `année-mois-jour` (exemple: `2010-01-19`);
  - `année-mois-jour heure` (exemple: `2010-01-19 20`);
  - `année-mois-jour heure:minutes` (exemple: `2010-01-19 20:04`);
  - `année-mois-jour heure:minutes:secondes` (exemple: `2010-01-19 20:04:02`).

L'année peut être sur deux ou quatre chiffres. Les autres informations peuvent avoir un zéro initial (par exemple `01` ou `1`). Lors du calcul du timestamp, une information manquante sera extraite de la date modèle `1970-1-1 0:0:0`.
*/
function dateVersTimestamp($date)
{
	$mktime = array (
		'heure' => 0,
		'minutes' => 0,
		'secondes' => 0,
		'mois' => 1,
		'jour' => 1,
		'annee' => 1970,
	);
	
	if (!empty($date))
	{
		$mktime['annee'] = $date;
		
		if (strpos($mktime['annee'], '-') !== FALSE)
		{
			$jour = explode('-', $mktime['annee']);
			$mktime['annee'] = $jour[0];
			$mktime['mois'] = $jour[1];
		
			if (isset($jour[2]))
			{
				$mktime['jour'] = $jour[2];
			
				if (strpos($mktime['jour'], ' ') !== FALSE)
				{
					$heure = explode(' ', $mktime['jour']);
					$mktime['jour'] = $heure[0];
					$mktime['heure'] = $heure[1];
		
					if (strpos($mktime['heure'], ':') !== FALSE)
					{
						$heure = explode(':', $mktime['heure']);
						$mktime['heure'] = $heure[0];
						$mktime['minutes'] = $heure[1];
			
						if (isset($heure[2]))
						{
							$mktime['secondes'] = $heure[2];
						}
					}
				}
			}
		}
	}
	
	return mktime($mktime['heure'], $mktime['minutes'], $mktime['secondes'], $mktime['mois'], $mktime['jour'], $mktime['annee']);
}

/*
Retourne le texte supplémentaire d'une image pour le message envoyé par le module «Faire découvrir».
*/
function decouvrirSupplementImage($urlRacine, $idGalerie, $image, $galerieLegendeMarkdown)
{
	$messageDecouvrirSupplement = '';
	$titreImage = titreImage($image);
	
	if (!empty($image['vignetteNom']))
	{
		$vignetteNom = $image['vignetteNom'];
	}
	else
	{
		$vignetteNom = nomSuffixe($image['intermediaireNom'], '-vignette');
	}
	
	if (!empty($image['vignetteAlt']))
	{
		$vignetteAlt = $image['vignetteAlt'];
	}
	elseif (!empty($image['intermediaireAlt']))
	{
		$vignetteAlt = $image['intermediaireAlt'];
	}
	else
	{
		$vignetteAlt = sprintf(T_("Image %1\$s"), $titreImage);
	}
	
	$messageDecouvrirSupplement .= "<p style=\"text-align: center;\"><img src=\"$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . '/' . rawurlencode($vignetteNom) . "\" alt=\"$vignetteAlt\" /></p>\n";
	
	if (!empty($image['titre']))
	{
		$messageDecouvrirSupplement .= '<p>' . $image['titre'] . "</p>\n";
	}
	
	if (!empty($image['intermediaireLegende']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($image['intermediaireLegende'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['intermediaireAlt']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($image['intermediaireAlt'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['vignetteAlt']))
	{
		$messageDecouvrirSupplement .= intermediaireLegende($image['vignetteAlt'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['pageIntermediaireDescription']))
	{
		$messageDecouvrirSupplement .= '<p>' . $image['pageIntermediaireDescription'] . "</p>\n";
	}
	elseif (!empty($image['pageIntermediaireBaliseTitle']))
	{
		$messageDecouvrirSupplement .= '<p>' . $image['pageIntermediaireBaliseTitle'] . "</p>\n";
	}
	
	$messageDecouvrirSupplement = "<div style=\"font-style: italic;\">$messageDecouvrirSupplement</div>\n";
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . sprintf(T_("Voyez l'image %1\$s en plus grande taille!"), "<em>$titreImage</em>") . '</a> ' . T_("En espérant qu'elle vous intéresse!") . "</p>\n";
	
	return $messageDecouvrirSupplement;
}

/*
Retourne le texte supplémentaire d'une page pour le message envoyé par le module «Faire découvrir».
*/
function decouvrirSupplementPage($description, $baliseTitle)
{
	$messageDecouvrirSupplement = '';
	
	if (!empty($baliseTitle))
	{
		$messageDecouvrirSupplement .= '<p>' . $baliseTitle . "</p>\n";
	}
	
	if (!empty($description))
	{
		$messageDecouvrirSupplement .= '<p>' . $description . "</p>\n";
	}
	
	if (!empty($messageDecouvrirSupplement))
	{
		$messageDecouvrirSupplement = "<div style=\"font-style: italic;\">$messageDecouvrirSupplement</div>\n";
	}
	
	$messageDecouvrirSupplement .= '<p><a href="' . urlPageSansDecouvrir() . '">' . urlPageSansDecouvrir() . "</a></p>\n";
	
	$messageDecouvrirSupplement .= '<p> ' . T_("En espérant que cette page vous intéresse!") . "</p>\n";
	
	return $messageDecouvrirSupplement;
}

/*
Retourne un tableau dont le premier élément contient le contenu du DTD (Définition de Type de Document); et le second élément, le contenu de la balise `html`.
*/
function doctype($doctype, $langue)
{
	switch ($doctype)
	{
		case 'XHTML 1.1':
			return array ('<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n", '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langue . '">' . "\n");
			break;
			
		case 'XHTML 1.0 Strict':
			return array ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n", '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langue . '" lang="' . $langue . '">' . "\n");
			break;
			
		case 'XHTML 1.0 Transitional':
			return array ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n", '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langue . '" lang="' . $langue . '">' . "\n");
			break;
			
		case 'HTML 4.01 Strict':
			return array ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n", '<html lang="' . $langue . '">' . "\n");
			break;
			
		case 'HTML 4.01 Transitional':
			return array ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n", '<html lang="' . $langue . '">' . "\n");
			break;
			
		default:
			return array ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n", '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $langue . '" lang="' . $langue . '">' . "\n");
			break;
	}
}

/*
Retourne TRUE si la page est l'accueil, sinon retourne FALSE.
*/
function estAccueil($accueil)
{
	$url = url();
	$listeIndex = array ('index.html', 'index.cgi', 'index.pl', 'index.php', 'index.xhtml', 'index.htm'); // Valeur par défaut de `DirectoryIndex` sous Apache 2.
	
	if ($url == $accueil . '/')
	{
		return TRUE;
	}
	else
	{
		foreach ($listeIndex as $index)
		{
			if ($url == $accueil . '/' . $index)
			{
				return TRUE;
			}
		}
	}
	
	return FALSE;
}

/*
Returne TRUE si le bloc a des coins arrondis, sinon retourne FALSE.
*/
function estBlocArrondi($blocsArrondisParDefaut, $blocsArrondisSpecifiques, $bloc, $nombreDeColonnes)
{
	if ((isset($blocsArrondisSpecifiques[$bloc][$nombreDeColonnes]) && $blocsArrondisSpecifiques[$bloc][$nombreDeColonnes]) || (!isset($blocsArrondisSpecifiques[$bloc][$nombreDeColonnes]) && $blocsArrondisParDefaut))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne TRUE si la catégorie est une catégorie spéciale, sinon retourne FALSE.
*/
function estCatSpeciale($categorie)
{
	if ($categorie == 'galeries' || $categorie == 'site')
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Si `$retourneNomSansExtension` vaut FALSE, retourne l'extension d'un fichier (sans le point). Si aucune extension n'a été trouvée, retourne une chaîne vide. Sinon si `$retourneNomSansExtension` vaut TRUE, retourne le nom du fichier sans l'extension (ni le point).
*/
function extension($nomFichier, $retourneNomSansExtension = FALSE)
{
	$tableauNomFichier = explode('.', $nomFichier);
	$extension = array_pop($tableauNomFichier);
	
	if ($retourneNomSansExtension)
	{
		return $extension != $nomFichier ? superBasename($nomFichier, '.' . $extension) : $nomFichier;
	}
	else
	{
		return $extension != $nomFichier ? $extension : '';
	}
}

/*
Retourne un tableau contenant les fichiers à inclure au début du script.
*/
function fichiersAinclureAuDebut($racine, $idCategorie)
{
	$fichiers = array ();
	$fichiers[] = $racine . '/inc/mimedetect/file.inc.php';
	$fichiers[] = $racine . '/inc/mimedetect/mimedetect.inc.php';
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	$fichiers[] = $racine . '/inc/simplehtmldom/simple_html_dom.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/common.inc.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/filter.inc.php';
	
	if (!empty($idCategorie))
	{
		$fichiers[] = $racine . '/inc/rolling-curl/RollingCurl.php';
	}
	
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
Filtre une chaîne de caractères pour ne conserver que des caractères non accentués et certains autres caractères. Retourne la chaîne filtrée.
*/
function filtreChaine($racine, $chaine, $casse = '')
{
	$transliteration = parse_ini_file($racine . '/inc/pathauto/i18n-ascii.txt');
	
	$chaine = strtr($chaine, $transliteration);
	$chaine = preg_replace('/[^-A-Za-z0-9._\+]/', '-', $chaine);
	$chaine = preg_replace('/-+/', '-', $chaine);
	$chaine = str_replace('-.', '.', $chaine);
	$chaine = str_replace('.-', '-', $chaine);
	$chaine = preg_replace('/\.+/', '.', $chaine);
	
	if ($casse == 'min')
	{
		$chaine = strtolower($chaine);
	}
	elseif ($casse == 'maj')
	{
		$chaine = strtoupper($chaine);
	}
	elseif ($casse == 'premiereMaj')
	{
		$chaine = ucfirst($chaine);
	}
	elseif ($casse == 'premiereChaqueMotMaj')
	{
		$chaine = ucwords($chaine);
	}
	
	return $chaine;
}

/*
Retourne le code HTML filtré.
*/
function filtreHtml($racine, $doctype, $charset, $balisesPermises, $codeHtml)
{
	require_once $racine . '/inc/htmlpurifier/library/HTMLPurifier.auto.php';
	$configHtmlPurifier = HTMLPurifier_Config::createDefault();
	$configHtmlPurifier->set('Cache.SerializerPath', $racine . '/site/cache');
	
	if (!empty($doctype))
	{
		$configHtmlPurifier->set('HTML.Doctype', $doctype);
	}
	
	if (!empty($charset))
	{
		$configHtmlPurifier->set('Core.Encoding', $charset);
	}
	
	if ($balisesPermises != 'defaut')
	{
		$configHtmlPurifier->set('HTML.Allowed', $balisesPermises);
	}
	
	$htmlPurifier = new HTMLPurifier($configHtmlPurifier);
	
	return $htmlPurifier->purify($codeHtml);
}

/*
Retourne le contenu d'un fichier RSS.

Fournir les URL traitées par `superRawurlencode()`.
*/
function fluxRss($type, $itemsFluxRss, $urlRss, $url, $baliseTitleComplement, $idGalerie, $idCategorie)
{
	$contenuRss = '';
	$contenuRss .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$contenuRss .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
	$contenuRss .= "\t<channel>\n";
	$contenuRss .= "\t\t" . '<atom:link href="' . $urlRss . '" rel="self" type="application/rss+xml" />' . "\n";
	
	// Page individuelle d'une galerie.
	if ($type == 'galerie')
	{
		$contenuRss .= "\t\t<title>" . sprintf(T_("Galerie %1\$s"), $idGalerie . $baliseTitleComplement) . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Derniers ajouts à la galerie «%1\$s»"), $idGalerie) . "</description>\n\n";
	}
	// Catégorie.
	elseif ($type == 'categorie')
	{
		$contenuRss .= "\t\t<title>" . sprintf(T_("Dernières publications dans la catégorie «%1\$s»"), $idCategorie) . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Dernières publications dans la catégorie «%1\$s»"), $idCategorie) . $baliseTitleComplement . "</description>\n\n";
	}
	// Toutes les galeries.
	elseif ($type == 'galeries')
	{
		
		$contenuRss .= "\t\t<title>" . T_("Galeries") . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
		$contenuRss .= "\t\t<description>" . T_("Derniers ajouts aux galeries") . $baliseTitleComplement . "</description>\n\n";
	}
	// Tout le site.
	elseif ($type == 'site')
	{
		$contenuRss .= "\t\t<title>" . T_("Dernières publications") . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
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
			
			if (!empty($itemFlux['dccreator']))
			{
				$contenuRss .= "\t\t\t<dc:creator>" . $itemFlux['dccreator'] . "</dc:creator>\n";
			}
			
			$contenuRss .= "\t\t\t<pubDate>" . date('r', dateVersTimestamp($itemFlux['pubDate'])) . "</pubDate>\n";
			$contenuRss .= "\t\t</item>\n\n";
		}
	}
	
	$contenuRss .= "\t</channel>\n";
	$contenuRss .= '</rss>';
	
	return $contenuRss;
}

/*
Retourne un tableau listant les images d'une galerie, chaque image constituant elle-même un tableau des informations nécessaires à la création d'un fichier RSS.
*/
function fluxRssGalerieTableauBrut($racine, $urlRacine, $urlGalerie, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown)
{
	$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
	$itemsFluxRss = array ();
	
	if ($tableauGalerie !== FALSE)
	{
		foreach ($tableauGalerie as $image)
		{
			$id = idImage($racine, $image);
			$titreImage = titreImage($image);
			$title = sprintf(T_("%1\$s – Galerie %2\$s"), $titreImage, $idGalerie);
			$cheminImage = "$racine/site/fichiers/galeries/$idGalerie/" . $image['intermediaireNom'];
			$urlImage = "$urlRacine/site/fichiers/galeries/" . rawurlencode($idGalerie) . '/' . rawurlencode($image['intermediaireNom']);
			$urlGalerieImage = superRawurlencode("$urlGalerie?image=$id");
			
			if (!empty($image['intermediaireLargeur']))
			{
				$width = $image['intermediaireLargeur'];
			}
			else
			{
				list ($width, $height) = getimagesize($cheminImage);
			}
		
			if (!empty($image['intermediaireHauteur']))
			{
				$height = $image['intermediaireHauteur'];
			}
		
			if (!empty($image['intermediaireAlt']))
			{
				$alt = $image['intermediaireAlt'];
			}
			else
			{
				$alt = sprintf(T_("Image %1\$s"), $titreImage);
			}
			
			$urlOriginal = '';
			
			if (!empty($image['originalNom']))
			{
				$nomOriginal = $image['originalNom'];
				
			}
			else
			{
				$nomOriginal = nomSuffixe($image['intermediaireNom'], '-original');
			}
			
			$cheminOriginal = "$racine/site/fichiers/galeries/$idGalerie/$nomOriginal";
			
			if (file_exists($cheminOriginal))
			{
				$urlOriginal = "site/fichiers/galeries/" . rawurlencode($idGalerie) . '/' . rawurlencode($nomOriginal);
				
				if ($galerieLienOriginalTelecharger)
				{
					$urlOriginal = "$urlRacine/telecharger.php?fichier=$urlOriginal";
					$msgOriginal = "<li><a href=\"$urlOriginal\">" . sprintf(T_("Télécharger l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), "<em>$titreImage</em>", '<em>' . extension($nomOriginal) . '</em>', octetsVersKio(filesize($cheminOriginal))) . "</a></li>\n";
				}
				else
				{
					$urlOriginal = "$urlRacine/$urlOriginal";
					$msgOriginal = "<li><a href=\"$urlOriginal\">" . sprintf(T_("Voir l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), "<em>$titreImage</em>", '<em>' . extension($nomOriginal) . '</em>', octetsVersKio(filesize($cheminOriginal))) . "</a></li>\n";
				}
			}
			else
			{
				$msgOriginal = '';
			}
			
			if (!empty($image['auteurAjout']))
			{
				$dccreator = $image['auteurAjout'];
			}
			elseif ($galerieFluxRssAuteurEstAuteurParDefaut)
			{
				$dccreator = $auteurParDefaut;
			}
			else
			{
				$dccreator = '';
			}
		
			if (!empty($image['dateAjout']))
			{
				$pubDate = $image['dateAjout'];
			}
			else
			{
				$pubDate = date('Y-m-d H:i', filemtime($cheminImage));
			}
			
			$description = '';
			$description .= "<p><img src=\"$urlImage\" width=\"$width\" height=\"$height\" alt=\"$alt\" /></p>\n";
			
			if (!empty($image['intermediaireLegende']))
			{
				$description .= '<div>' . intermediaireLegende($image['intermediaireLegende'], $galerieLegendeMarkdown) . "</div>\n";
			}
			
			$msgPagePresentation = "<li><a href=\"$urlGalerieImage\">" . sprintf(T_("Consulter la page de présentation de l'image %1\$s dans la galerie %2\$s."), "<em>$titreImage</em>", "<em>$idGalerie</em>") . "</a></li>\n";
			$description .= "<ul>\n" . $msgPagePresentation . $msgOriginal . "</ul>\n";
			$description = securiseTexte($description);
			
			$itemsFluxRss[] = array (
				"title" => $title,
				"link" => $urlGalerieImage,
				"guid" => $urlGalerieImage,
				"description" => $description,
				"dccreator" => $dccreator,
				"pubDate" => $pubDate,
			);
		}
	}
	
	return $itemsFluxRss;
}

/*
Retourne un tableau listant les images de toutes les galeries déclarées dans le fichier de configuration du flux RSS des derniers ajouts aux galeries.

Voir la fonction `fluxRssGalerieTableauBrut()` pour plus de détails.
*/
function fluxRssGaleriesTableauBrut($racine, $urlRacine, $langue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown)
{
	$itemsFluxRss = array ();
	$galeries = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
	
	if (isset($galeries[$langue]))
	{
		foreach ($galeries[$langue] as $idGalerie => $urlRelativeGalerie)
		{
			$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, "$urlRacine/$urlRelativeGalerie", $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown));
		}
	}
	
	return $itemsFluxRss;
}

/*
Retourne un tableau d'un élément représentant une page du site, cet élément étant lui-même un tableau contenant les informations nécessaires à la création d'un fichier RSS. Si une erreur survient, retourne un tableau vide.

Ne pas fournir une URL traitée par `superRawurlencode()`.
*/
function fluxRssPageTableauBrut($cheminPage, $urlPage, $fluxRssAvecApercu, $tailleApercuAutomatique)
{
	$itemFlux = array ();
	$infosPage = infosPage($urlPage, $fluxRssAvecApercu, $tailleApercuAutomatique);
	$urlPage = superRawurlencode($urlPage);
	
	if (!empty($infosPage))
	{
		if (!empty($infosPage['dateCreation']))
		{
			$pubDate = securiseTexte($infosPage['dateCreation']);
		}
		elseif (!empty($infosPage['dateRevision']))
		{
			$pubDate = securiseTexte($infosPage['dateRevision']);
		}
		else
		{
			$pubDate = date('Y-m-d H:i', filemtime($cheminPage));
		}
		
		if (!empty($infosPage['apercu']))
		{
			$description = $infosPage['apercu'] . "<p><a href=\"$urlPage\">" . sprintf(T_("Lire la suite de %1\$s."), '<em>' . $infosPage['titre'] . '</em>') . "</a></p>\n";
		}
		else
		{
			$description = $infosPage['contenu'];
		}
		
		$itemFlux[] = array (
			"title" => securiseTexte($infosPage['titre']),
			"link" => $urlPage,
			"guid" => $urlPage,
			"description" => securiseTexte($description),
			"dccreator" => securiseTexte($infosPage['auteur']),
			"pubDate" => $pubDate,
		);
	}
	
	return $itemFlux;
}

/*
Retourne le tableau `$itemsFluxRss` contenant au maximum le nombre d'items précisé dans la configuration. L'ordre des items dépend du type.
*/
function fluxRssTableauFinal($type, $itemsFluxRss, $nombreItemsFluxRss)
{
	if ($type == 'galerie' || $type == 'galeries')
	{
		foreach ($itemsFluxRss as $cle => $valeur)
		{
			$itemsFluxRssPubDate[$cle] = $valeur['pubDate'];
		}
		
		array_multisort($itemsFluxRssPubDate, SORT_DESC, $itemsFluxRss);
	}
	
	$itemsFluxRss = array_slice($itemsFluxRss, 0, $nombreItemsFluxRss);
	
	return $itemsFluxRss;
}

/*
Retourne le code HTML d'une catégorie à inclure dans le menu des catégories automatisé.
*/
function htmlCategorie($urlRacine, $categories, $categorie, $langueParDefaut, $afficherNombreArticlesCategorie)
{
	$nomCategorie = $categorie;
	
	if ($nomCategorie == 'site')
	{
		$nomCategorie = T_("Dernières publications");
	}
	elseif ($nomCategorie == 'galeries')
	{
		$nomCategorie = T_("Derniers ajouts aux galeries");
	}
	
	$htmlCategorie = '';
	$htmlCategorie .= '<li>';
	
	$categories[$categorie]['urlCat'] = urlCat($categories[$categorie], $categorie, $langueParDefaut);
	
	$htmlCategorie .= '<a href="' . $urlRacine . '/' . superRawurlencode($categories[$categorie]['urlCat']) . '">' . $nomCategorie . '</a>';
	
	if ($afficherNombreArticlesCategorie)
	{
		$htmlCategorie .= sprintf(T_(" (%1\$s)"), count($categories[$categorie]['pages']));
	}
	
	$categoriesEnfants = categoriesEnfants($categories, $categorie, $langueParDefaut);
	
	if (!empty($categoriesEnfants))
	{
		$htmlCategorie .= "<ul>\n";
		
		foreach ($categoriesEnfants as $enfant)
		{
			$htmlCategorie .= htmlCategorie($urlRacine, $categories, $enfant, $langueParDefaut, $afficherNombreArticlesCategorie);
		}
		
		$htmlCategorie .= "</ul>\n";
	}
	
	$htmlCategorie .= "</li>\n";
	
	return $htmlCategorie;
}

/*
Retourne l'`id` d'une image d'une galerie.
*/
function idImage($racine, $image)
{
	if (!empty($image['id']))
	{
		return $image['id'];
	}
	elseif (!empty($image['titre']))
	{
		return filtreChaine($racine, $image['titre']);
	}
	else
	{
		return filtreChaine($racine, $image['intermediaireNom']);
	}
}

/*
Retourne un tableau d'informations au sujet du contenu accessible à l'URL `$urlPage`, ou directement au sujet du contenu fourni si `$html` n'est pas vide. Le tableau contient les informations suivantes:

  - `$infosPage['titre']`: titre de la page. Prend comme valeur la première information trouvée parmi les suivantes:
    - contenu de la premère balise `h1`;
    - contenu de la balise `title`;
    - URL de la page.

  - `$infosPage['contenu']`: vaut tout le contenu de la `div` `milieuInterieurContenu`, la première balise `h1` en moins;

  - `$infosPage['apercu']`: est vide par défaut. Selon les valeurs de `$inclureApercu` et `$apercu` pour la page demandée, la description peut correspondre à un extrait de la page ou au contenu de la métabalise `description`;

  - `$infosPage['auteur']`: vaut le contenu de la métabalise `author`, si elle existe;

  - `$infosPage['dateCreation']`: vaut le contenu de la métabalise `date-creation-yyyymmdd`, si elle existe;

  - `$infosPage['dateRevision']`: vaut le contenu de la métabalise `date-revision-yyyymmdd`, si elle existe.

Si `$html` est vide et que l'URL fournie n'est pas accessible, retourne un tableau vide.

Ne pas fournir une URL traitée par `superRawurlencode()`.
*/
function infosPage($urlPage, $inclureApercu, $tailleApercuAutomatique, $html = '')
{
	$infosPage = array ();
	
	if (empty($html))
	{
		$html = contenuUrl(superRawurlencode($urlPage, TRUE));
	}
	
	if ($html !== FALSE)
	{
		$dom = str_get_html($html);
	
		// Titre.
	
		if ($titre = $dom->find('h1'))
		{
			$infosPage['titre'] = $titre[0]->plaintext;
		}
		else
		{
			$infosPage['titre'] = '';
		}
	
		if (empty($infosPage['titre']) && $titre = $dom->find('title'))
		{
			$infosPage['titre'] = $titre[0]->innertext;
		}
	
		unset($titre);
	
		if (empty($infosPage['titre']))
		{
			$infosPage['titre'] = superRawurlencode($urlPage);
		}
		
		// Contenu.
		
		if ($contenu = $dom->find('div#galerieIntermediaireImg img'))
		{
			$infosPage['contenu'] = '<div class="galerieIntermediaireImgApercu"><a href="' . superRawurlencode($urlPage) . '">' . $contenu[0]->outertext . "</a></div>\n";
			
			if ($contenu = $dom->find('div#galerieIntermediaireLegende'))
			{
				$infosPage['contenu'] .= $contenu[0]->innertext;
			}
		}
		elseif ($contenu = $dom->find('div#milieuInterieurContenu'))
		{
			if ($h1 = $contenu[0]->find('h1'))
			{
				$h1[0]->outertext = '';
			}
			
			$infosPage['contenu'] = $contenu[0]->innertext;
			unset($contenu);
		}
		else
		{
			$infosPage['contenu'] = '';
		}
		
		// Aperçu.
		
		$commentairesHtmlSupprimes = FALSE;
		$infosPage['apercu'] = '';
	
		if ($inclureApercu && preg_match('|<!-- APERÇU: (.+?) -->|s', $infosPage['contenu'], $resultatApercu))
		{
			if ($resultatApercu[1] == 'interne')
			{
				if (preg_match('#^(.+?)<!-- ?/aper(ç|c)u ?-->#s', $infosPage['contenu'], $resultatInterne))
				{
					$infosPage['apercu'] = corrigeHtml(supprimeCommentairesHtml($resultatInterne[1]) . ' […]');
					$commentairesHtmlSupprimes = TRUE;
				}
			}
			elseif ($resultatApercu[1] == 'description' && $description = $dom->find('meta[name=description]'))
			{
				$infosPage['apercu'] = $description[0]->content;
				unset($description);
			}
			elseif ($resultatApercu[1] == 'automatique')
			{
				// Merci à <http://mydrupalblog.lhmdesign.com/drupal-php-how-auto-truncate-content-end-word>.
				
				$mots = explode(' ', supprimeCommentairesHtml($infosPage['contenu']));
				$commentairesHtmlSupprimes = TRUE;
				$apercu = '';
				
				foreach ($mots as $mot)
				{
					if (strlen($apercu) < $tailleApercuAutomatique)
					{
						$apercu .= ' ' . $mot;
					}
					else
					{
						$apercu .= ' […]';
						break;
					}
				}
				
				unset($mots);
				$infosPage['apercu'] = corrigeHtml($apercu);
				unset($apercu);
			}
			else
			{
				$infosPage['apercu'] = $resultatApercu[1];
			}
		}
	
		if (!$commentairesHtmlSupprimes)
		{
			$infosPage['apercu'] = supprimeCommentairesHtml($infosPage['apercu']);
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
Retourne les informations sur l'auteur, les dates de création et de révision ainsi que la ou les catégories. Si aucune information n'est présente, retourne une chaine vide.
*/
function infosPublication($urlRacine, $auteur, $dateCreation, $dateRevision, $categories)
{
	$infosPublication = '';
	
	if (!empty($auteur))
	{
		if (!empty($dateCreation))
		{
			$infosPublication .= sprintf(T_("Publié par %1\$s le %2\$s."), $auteur, $dateCreation) . "\n";
		}
		else
		{
			$infosPublication .= sprintf(T_("Publié par %1\$s."), $auteur) . "\n";
		}
	}
	elseif (!empty($dateCreation))
	{
		$infosPublication .= sprintf(T_("Publié le %1\$s."), $dateCreation) . "\n";
	}
	
	if (!empty($dateRevision))
	{
		$infosPublication .= sprintf(T_("Dernière révision le %1\$s."), $dateRevision) . "\n";
	}
	
	if (!empty($categories))
	{
		$listeCategories = '';
		
		foreach ($categories as $categorie => $urlCat)
		{
			$listeCategories .= '<a href="' . $urlRacine . '/' . superRawurlencode($urlCat) . '">' . $categorie . '</a>, ';
		}
		
		$listeCategories = substr($listeCategories, 0, -2); // Suppression du `, ` final.
		$infosPublication .= sprintf(T_ngettext("Catégorie: %1\$s.", "Catégories: %1\$s.", count($categories)), $listeCategories) . "\n";
	}
	
	return $infosPublication;
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
Retourne la légende d'une image dans le bon format.
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
Retourne l'IP de l'internaute si elle a été trouvée, sinon retourne une chaîne vide.
*/
function ipInternaute()
{
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip = securiseTexte($_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	elseif (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = securiseTexte($_SERVER['HTTP_CLIENT_IP']);
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
		$ip = securiseTexte($_SERVER['REMOTE_ADDR']);
	}
	else
	{
		$ip = '';
	}
	
	return $ip;
}

/*
Retourne la langue de la page courante.
*/
function langue($langue, $langueParDefaut)
{
	if ($langue == 'navigateur')
	{
		$langue = explode(',', securiseTexte($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		$langue = strtolower(substr(rtrim($langue[0]), 0, 2));
		
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
		$dom = str_get_html($codeMenuLangues);
	
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
		
		$codeMenuLanguesFiltre = $dom->save();
		$dom->clear();
		unset($dom);
		
		return $codeMenuLanguesFiltre;
	}
	else
	{
		return $codeMenuLangues;
	}
}

/*
Retourne la langue d'une catégorie.
*/
function langueCat($categorie, $langueParDefaut)
{
	return !empty($categorie['langueCat']) ? $categorie['langueCat'] : $langueParDefaut;
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

  - `verbatim` pour la licence GNU Verbatim Copying License. Voir <http://www.gnu.org/licenses/licenses.fr.html#VerbatimCopying>.

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

		case 'verbatim':
			$licence = T_("La reproduction exacte et la distribution intégrale de cet article est permise sur n'importe quel support d'archivage, pourvu que cette notice soit préservée.");
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
		$aHref = $a->href;
		
		if (!$inclureGet)
		{
			$aHref = preg_replace('/\?.*/', '', $aHref);
		}
		
		if ($aHref == $url)
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
function linkScript($balisesBrutes, $versionParDefautLinkScriptCss = '', $versionParDefautLinkScriptNonCss = '')
{
	$balisesBrutesAinclure = linkScriptAinclure($balisesBrutes);
	$balisesFormatees = '';
	$favicon = '';
	
	foreach ($balisesBrutesAinclure as $fichierBrut)
	{
		// On récupère les infos.
		list ($type, $fichier) = explode('#', $fichierBrut, 2);
		
		if ($type == 'rss' && strpos($fichier, '#') !== FALSE)
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
				$favicon = '<link rel="shortcut icon" type="images/x-icon" href="' . ajouteGet($fichier, $versionParDefautLinkScriptNonCss) . '" />' . "\n";
				break;
	
			case 'css':
				$balisesFormatees .= '<link rel="stylesheet" type="text/css" href="' . ajouteGet($fichier, $versionParDefautLinkScriptCss) . '" media="screen" />' . "\n";
				break;
	
			case 'cssltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . ajouteGet($fichier, $versionParDefautLinkScriptCss) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
		
			case 'cssIE7':
				$balisesFormatees .= '<!--[if IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . ajouteGet($fichier, $versionParDefautLinkScriptCss) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'csslteIE7':
				$balisesFormatees .= '<!--[if lte IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . ajouteGet($fichier, $versionParDefautLinkScriptCss) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
	
			case 'js':
				$balisesFormatees .= '<script type="text/javascript" src="' . ajouteGet($fichier, $versionParDefautLinkScriptNonCss) . '"></script>' . "\n";
				break;
				
			case 'jsDirect':
				$balisesFormatees .= "<script type=\"text/javascript\">\n//<![CDATA[\n
$fichier\n//]]>\n</script>\n";
				break;
				
			case 'jsDirectltIE7':
				$balisesFormatees .= "<!--[if lt IE 7]>\n<script type=\"text/javascript\">\n//<![CDATA[\n$fichier\n//]]>\n</script>\n<![endif]-->\n";
				break;
				
			case 'jsltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<script type="text/javascript" src="' . ajouteGet($fichier, $versionParDefautLinkScriptNonCss) . '"></script>' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'rss':
				if (!empty($title))
				{
					$title = ' title="' . $title . '"';
				}
				
				$balisesFormatees .= '<link rel="alternate" type="application/rss+xml" href="' . ajouteGet($fichier, $versionParDefautLinkScriptNonCss) . '"' . $title . ' />' . "\n";
				break;
				
			case 'po':
				$balisesFormatees .= '<link type="application/x-po" rel="gettext" href="' . ajouteGet($fichier, $versionParDefautLinkScriptNonCss) . '" />' . "\n";
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
		
		if ($type == 'rss' && strpos($fichier, '#') !== FALSE)
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
				
				if ($typeAinclure == 'rss' && strpos($fichierAinclure, '#') !== FALSE)
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
	
	// Palliatif à un bogue sur les serveurs de Koumbit. Aucune idée du problème. On dirait que 9 fois sur 10, php-gettext passe le relais au gettext par défaut de PHP, et que si la locale est seulement 'en' par exemple, elle n'existe pas sur le serveur d'hébergement, donc la traduction ne fonctionne pas.
	if ($locale == 'en')
	{
		$locale = 'en_US';
	}
	elseif ($locale == 'fr')
	{
		$locale = 'fr_CA';
	}
	
	return $locale;
}

/*
Retourne un tableau de liens de marque-pages ou réseaux sociaux pour la page en cours. Les liens ont été en partie récupérés dans le module Service links pour Drupal, sous licence GPL. Voir <http://drupal.org/project/service_links>.
*/
function marquePagesSociaux($url, $titre)
{
	$url = urlencode($url);
	$titre = urlencode($titre);
	
	if ($titre == $url)
	{
		$titre = '';
	}
	
	$liens = array();
	
	$liens['Bebo'] = array(
		'nom' => 'Bebo',
		'lien' => "http://www.bebo.com/share.php?Url=$url&amp;Title=$titre",
	);
	
	$liens['BlogMemes'] = array(
		'nom' => 'BlogMemes',
		'lien' => "http://blogmemes.net/fr/post.php?url=$url&amp;title=$titre",
	);
	
	$liens['Delicious'] = array(
		'nom' => 'Delicious',
		'lien' => "http://delicious.com/post?url=$url&amp;title=$titre",
	);
	
	$liens['Digg'] = array(
		'nom' => 'Digg',
		'lien' => "http://digg.com/submit?phase=2&amp;url=$url&amp;title=$titre",
	);
	
	$liens['Facebook'] = array(
		'nom' => 'Facebook',
		'lien' => "http://www.facebook.com/sharer.php?u=$url&amp;t=$titre",
	);
	
	$liens['Furl'] = array(
		'nom' => 'Furl',
		'lien' => "http://www.furl.net/storeIt.jsp?u=$url&amp;t=$titre",
	);
	
	$liens['Fuzz'] = array(
		'nom' => 'Fuzz',
		'lien' => "http://www.fuzz.fr/?nws_article?link=$url&amp;title=$titre",
	);
	
	$liens['Gnolia'] = array(
		'nom' => 'Gnolia',
		'lien' => "http://gnolia.com/bookmarklet/add?url=$url&amp;title=$titre",
	);
	
	$liens['GoogleBookmarks'] = array(
		'nom' => 'Google Bookmarks',
		'lien' => "http://www.google.com/bookmarks/mark?op=add&amp;bkmk=$url&amp;title=$titre",
	);
	
	$liens['Identica'] = array(
		'nom' => 'Identi.ca',
		'lien' => "http://identi.ca/index.php?action=newnotice&amp;status_textarea=$titre $url",
	);
	
	$liens['Linkedin'] = array(
		'nom' => 'LinkedIn',
		'lien' => "http://www.linkedin.com/shareArticle?mini=true&amp;url=$url&amp;title=$titre",
	);
	
	$liens['MisterWong'] = array(
		'nom' => 'Mister Wong',
		'lien' => "http://www.mister-wong.com/addurl/?bm_url=$url&amp;bm_description=$titre",
	);
	
	$liens['Mixx'] = array(
		'nom' => 'Mixx',
		'lien' => "http://www.mixx.com/submit?page_url=$url",
	);
	
	$liens['MySpace'] = array(
		'nom' => 'MySpace',
		'lien' => "http://www.myspace.com/index.cfm?fuseaction=postto&amp;t=$titre&amp;u=$url",
	);
	
	$liens['Newsvine'] = array(
		'nom' => 'Newsvine',
		'lien' => "http://www.newsvine.com/_tools/seed&amp;save?u=$url&amp;h=$titre",
	);
	
	$liens['Propeller'] = array(
		'nom' => 'Propeller',
		'lien' => "http://www.propeller.com/submit/?U=$url&amp;T=$titre",
	);
	
	$liens['Reddit'] = array(
		'nom' => 'Reddit',
		'lien' => "http://reddit.com/submit?url=$url&amp;title=$titre",
	);
	
	$liens['Scoopeo'] = array(
		'nom' => 'Scoopeo',
		'lien' => "http://www.scoopeo.com/scoop/new?newurl=$url&amp;title=$titre",
	);
	
	$liens['SlashDot'] = array(
		'nom' => 'SlashDot',
		'lien' => "http://slashdot.org/bookmark.pl?url=$url&amp;title=$titre",
	);
	
	$liens['StumbleUpon'] = array (
		'nom' => 'StumbleUpon',
		'lien' => "http://www.stumbleupon.com/submit?url=$url&amp;title=$titre",
	);
	
	$liens['Tapemoi'] = array(
		'nom' => 'Tapemoi',
		'lien' => "http://www.tapemoi.com/submit.php?lien=$url",
	);
	
	$liens['Technorati'] = array(
		'nom' => 'Technorati',
		'lien' => "http://technorati.com/search/$url",
	);
	
	$liens['Twitter'] = array(
		'nom' => 'Twitter',
		'lien' => "http://twitter.com/home/?status=$url+--+$titre",
	);
	
	$liens['Wikio'] = array(
		'nom' => 'Wikio',
		'lien' => "http://www.wikio.fr/vote?url=$url",
	);
	
	$liens['YahooBookmarks'] = array(
		'nom' => 'Yahoo! Bookmarks',
		'lien' => "http://bookmarks.yahoo.com/myresults/bookmarklet?u=$url&amp;t=$titre",
	);
	
	$liens['YahooBuzz'] = array(
		'nom' => 'Yahoo! Buzz',
		'lien' => "http://buzz.yahoo.com/buzz?targetUrl=$url&amp;headline=$titre",
	);
	
	return $liens;
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
Retourne le menu des catégories, qui doit être entouré par la balise `ul` (seuls les `li` sont retournés).
*/
function menuCategoriesAutomatise($racine, $urlRacine, $langueParDefaut, $langue, $categories, $afficherNombreArticlesCategorie, $activerCategoriesGlobales, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger)
{
	$menuCategoriesAutomatise = '';
	ksort($categories);
	
	if ($activerCategoriesGlobales)
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $langue, $categories, array('site', 'galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
	
	foreach ($categories as $categorie => $categorieInfos)
	{
		if (empty($categorieInfos['catParente']) && langueCat($categorieInfos, $langueParDefaut) == $langue)
		{
			$menuCategoriesAutomatise .= htmlCategorie($urlRacine, $categories, $categorie, $langueParDefaut, $afficherNombreArticlesCategorie);
		}
	}
	
	return $menuCategoriesAutomatise;
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
	$message .= '<div class="bDcorps afficher"><p>' . T_("<strong>Pour naviguer de la manière la plus satisfaisante et sécuritaire, nous recommandons d'utiliser Firefox</strong>, un navigateur libre, performant, sécuritaire et respectueux des standards sur lesquels le web est basé. Firefox est tout à fait gratuit. Si vous utilisez un ordinateur au travail, vous pouvez faire la suggestion à votre service informatique.") . "</p>\n";
	$message .= "\n";
	$message .= "<p><strong><a href=\"http://www.firefox.com/\"><img src=\"$urlRacine/fichiers/Deer_Park_Globe.png\" alt=\"\" width=\"52\" height=\"52\" /></a> <a href=\"http://www.mozilla-europe.org/fr/\"><span>" . T_("Télécharger Firefox") . "</span></a></strong></p></div>\n";
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
Retourne le code de la petite notice affichée dans le haut des pages pour informer que le site est en maintenance. Seules les personnes ayant accès au site hors ligne peuvent voir cette notice.
*/
function noticeMaintenance()
{
	$notice = '';
	$notice .= '<div id="noticeMaintenance">' . "\n";
	$notice .= '<p>' . T_("Le site est présentement hors ligne pour maintenance. Certaines pages peuvent ne pas s'afficher correctement.") . "</p>\n";
	$notice .= "</div><!-- /#noticeMaintenance -->\n";
	
	return $notice;
}

/*
Génère une image de dimensions données à partir d'une image source. Si les dimensions voulues de la nouvelle image sont au moins aussi grandes que celles de l'image source, il y a seulement copie et non génération, à moins que `$galerieForcerDimensionsVignette` vaille TRUE. Dans ce cas, il y a ajout de bordures blanches (ou transparentes pour les PNG) pour compléter l'espace manquant. Retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function nouvelleImage($cheminImageSource, $cheminNouvelleImage, $typeMime,$nouvelleImageDimensionsVoulues, $galerieForcerDimensionsVignette, $galerieQualiteJpg, $galerieCouleurAlloueeImage, $nettete)
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
			$couleur = imagecolorallocate($nouvelleImage, $galerieCouleurAlloueeImage['rouge'], $galerieCouleurAlloueeImage['vert'], $galerieCouleurAlloueeImage['bleu']);
			imagefill($nouvelleImage, 0, 0, $couleur);
		}
		
		if ($typeMime == 'image/png')
		{
			$transparence = imagecolorallocatealpha($nouvelleImage, 200, 200, 200, 127);
			imagefill($nouvelleImage, 0, 0, $transparence);
		}
		
		// On crée la nouvelle image à partir de l'image source.
		imagecopyresampled($nouvelleImage, $imageSource, $demiSupplementLargeur, $demiSupplementHauteur, 0, 0, $nouvelleImageLargeur, $nouvelleImageHauteur, $imageSourceLargeur, $imageSourceHauteur);
		
		// Netteté demandée.
		if ($nettete['nettete'])
		{
			$nouvelleImage = UnsharpMask($nouvelleImage, $nettete['gain'], $nettete['rayon'], $nettete['seuil']);
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
Construit et retourne le code pour afficher une image dans la galerie. Si la taille de l'image n'est pas valide, retourne une chaîne vide.
*/
function image(
	// Infos sur le site.
	$racine, $urlRacine, $racineImgSrc, $urlImgSrc, $estAccueil, $nombreDeColonnes,
	
	// Infos sur l'image à générer.
	$infosImage, $typeMime, $taille, $sens, $galerieQualiteJpg, $galerieCouleurAlloueeImage,
	
	// Exif.
	$galerieExifAjout, $galerieExifDonnees,
	
	// Légende.
	$galerieLegendeAutomatique, $galerieLegendeEmplacement, $galerieLegendeMarkdown,
	
	// Lien vers l'image originale.
	$galerieLienOriginalEmplacement, $galerieLienOriginalJavascript, $galerieLienOriginalTelecharger,
	
	// Navigation.
	$galerieAccueilJavascript, $galerieNavigation, $galerieAncreDeNavigation,
	
	// Vignettes.
	$galerieDimensionsVignette, $galerieForcerDimensionsVignette, $vignetteAvecDimensions, $minivignetteImageEnCours
)
{
	####################################################################
	#
	# Taille intermédiaire.
	#
	####################################################################
	
	if ($taille == 'intermediaire')
	{
		$titreImage = titreImage($infosImage);
		
		if (!empty($infosImage['intermediaireLargeur']) || !empty($infosImage['intermediaireHauteur']))
		{
			if (!empty($infosImage['intermediaireLargeur']))
			{
				$width = 'width="' . $infosImage['intermediaireLargeur'] . '"';
			}
			
			if (!empty($infosImage['intermediaireHauteur']))
			{
				$height = 'height="' . $infosImage['intermediaireHauteur'] . '"';
			}
		}
		else
		{
			list ($larg, $haut) = getimagesize($racineImgSrc . '/' . $infosImage['intermediaireNom']);
			{
				$width = 'width="' . $larg . '"';
				$height = 'height="' . $haut . '"';
			}
		}
		
		if (!empty($infosImage['intermediaireAlt']))
		{
			$alt = 'alt="' . $infosImage['intermediaireAlt'] . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Image %1\$s"), $titreImage) . '"';
		}
		
		if (!empty($infosImage['intermediaireAttributTitle']))
		{
			$attributTitle = 'title="' . $infosImage['intermediaireAttributTitle'] . '"';
		}
		else
		{
			$attributTitle = '';
		}
		
		// Si le nom de l'image au format original a été renseigné, on utilise ce nom.
		if (!empty($infosImage['originalNom']))
		{
			$originalNom = $infosImage['originalNom'];
		}
		// Sinon on génère automatiquement un nom selon le nom de la version intermediaire de l'image.
		else
		{
			$originalNom = nomSuffixe($infosImage['intermediaireNom'], '-original');
		}
		
		// On vérifie maintenant si le fichier `$originalNom` existe. S'il existe, on récupère certaines informations.
		
		$divLienOriginalIcone = '';
		$divLienOriginalLegende = '';
		$aLienOriginalDebut = '';
		$aLienOriginalFin = '';
		$aLienOriginalImgIntermediaireDebut = '';
		$aLienOriginalImgIntermediaireFin = '';
		$relLienOriginal = '';
		
		if ($galerieLienOriginalJavascript && $typeMime != 'image/svg+xml')
		{
			$relLienOriginal = ' rel="lightbox"';
		}
		
		$originalExiste = FALSE;
		
		if (file_exists($racineImgSrc . '/' . $originalNom))
		{
			$originalExiste = TRUE;
		}
		
		if ($originalExiste)
		{
			$originalExtension = extension($originalNom);
			
			if ($galerieLienOriginalTelecharger && !$galerieLienOriginalJavascript)
			{
				$urlLienOriginal = $urlRacine . '/telecharger.php?fichier=' . preg_replace("|^$urlRacine/|", '', $urlImgSrc . '/' . $originalNom);
				$texteLienOriginal = sprintf(T_("Télécharger l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), "<em>$titreImage</em>", "<em>$originalExtension</em>", octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)));
				$texteAltLienOriginal = sprintf(T_("Télécharger l'image %1\$s au format original"), $titreImage);
			}
			else
			{
				$urlLienOriginal = $urlImgSrc . '/' . $originalNom;
				$texteLienOriginal = sprintf(T_("Voir l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), "<em>$titreImage</em>", "<em>$originalExtension</em>", octetsVersKio(filesize($racineImgSrc . '/' . $originalNom)));
				$texteAltLienOriginal = sprintf(T_("Voir l'image %1\$s au format original"), $titreImage);
			}
			
			$aLienOriginalDebut = '<a href="' . $urlLienOriginal . '"' . $relLienOriginal . '>';
			$aLienOriginalFin = '</a>';
			
			if ($galerieLienOriginalEmplacement['legende'])
			{
				$divLienOriginalLegende = '<div id="galerieLienOriginalLegende"><a href="' . $urlLienOriginal . '"' . $relLienOriginal . '>' . $texteLienOriginal . "</a></div><!-- /#galerieLienOriginalLegende -->\n";
			}
			
			if ($galerieLienOriginalEmplacement['image'])
			{
				$aLienOriginalImgIntermediaireDebut = $aLienOriginalDebut;
				$aLienOriginalImgIntermediaireFin = $aLienOriginalFin;
			}
			
			if ($galerieLienOriginalEmplacement['icone'])
			{
				if (file_exists($racine . '/site/fichiers/agrandir.png'))
				{
					$iconeLienOriginalSrc = $urlRacine . '/site/fichiers/agrandir.png';
				}
				else
				{
					$iconeLienOriginalSrc = $urlRacine . '/fichiers/agrandir.png';
				}
			
				$divLienOriginalIcone = '<div id="galerieLienOriginalIcone">' . $aLienOriginalDebut . '<img src="' . $iconeLienOriginalSrc . '" alt="' . $texteAltLienOriginal . '" width="22" height="22" />' . $aLienOriginalFin . '</div><!-- /#galerieLienOriginalIcone -->' . "\n";
			}
		}
		
		// Légende.
		if (!empty($infosImage['intermediaireLegende']))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . intermediaireLegende($infosImage['intermediaireLegende'], $galerieLegendeMarkdown) . "</div>\n";
		}
		elseif ($galerieLegendeAutomatique && (!$originalExiste || ($originalExiste && !$galerieLienOriginalEmplacement['legende'])))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . sprintf(T_("Image %1\$s (extension: %2\$s; taille: %3\$s Kio)."), "<em>$titreImage</em>", '<em>' . extension($infosImage['intermediaireNom']) . '</em>', octetsVersKio(filesize($racineImgSrc . '/' . $infosImage['intermediaireNom']))) . "</div>\n";
		}
		else
		{
			$legende = '';
		}
		
		// Exif.
		
		$exif = '';
		
		if ($galerieExifAjout && $typeMime == 'image/jpeg' && function_exists('exif_read_data'))
		{
			$tableauExif = @exif_read_data($racineImgSrc . '/' . $infosImage['intermediaireNom'], 'IFD0', 0);
			
			// Si aucune donnée Exif n'a été récupérée, on essaie d'en récupérer dans l'image en version originale, si elle existe et si son format est JPG.
			if (!$tableauExif && $originalExiste && $typeMime == 'image/jpeg')
			{
				$tableauExif = @exif_read_data($racineImgSrc . '/' . $originalNom, 'IFD0', 0);
			}
			
			if ($tableauExif)
			{
				foreach ($galerieExifDonnees as $cle => $valeur)
				{
					if ($valeur && !empty($tableauExif[$cle]))
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
				$exif = "<div id=\"galerieIntermediaireExif\">\n<ul>\n" . $exif . "</ul>\n</div><!-- /#galerieIntermediaireExif -->\n";
			}
		}
		
		// Code de retour.
		if ($galerieLegendeEmplacement[$nombreDeColonnes] == 'haut' || $galerieLegendeEmplacement[$nombreDeColonnes] == 'bloc')
		{
			return '<div id="galerieIntermediaireTexte">' . $legende . $exif . $divLienOriginalLegende . "</div><!-- /#galerieIntermediaireTexte -->\n" . '<div id="galerieIntermediaireImg">' . $aLienOriginalImgIntermediaireDebut . '<img src="' . $urlImgSrc . '/' . $infosImage['intermediaireNom'] . '"' . " $width $height $alt $attributTitle />" . $aLienOriginalImgIntermediaireFin . "</div><!-- /#galerieIntermediaireImg -->\n" . $divLienOriginalIcone;
		}
		elseif ($galerieLegendeEmplacement[$nombreDeColonnes] == 'bas')
		{
			return '<div id="galerieIntermediaireImg">' . $aLienOriginalImgIntermediaireDebut . '<img src="' . $urlImgSrc . '/' . $infosImage['intermediaireNom'] . '"' . " $width $height $alt $attributTitle />" . $aLienOriginalImgIntermediaireFin . "</div><!-- /#galerieIntermediaireImg -->\n" . $divLienOriginalIcone . '<div id="galerieIntermediaireTexte">' . $legende . $exif . $divLienOriginalLegende . "</div><!-- /#galerieIntermediaireTexte -->\n";
		}
		else
		{
			return '';
		}
	}
	####################################################################
	#
	# Taille vignette.
	#
	####################################################################
	elseif ($taille == 'vignette')
	{
		$class = '';
		
		if ($galerieNavigation == 'fleches' && ($sens == 'precedent' || $sens == 'suivant'))
		{
			$class .= ' galerieFleche';
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
			if (!empty($infosImage['vignetteNom']))
			{
				$src = 'src="' . $urlImgSrc . '/' . $infosImage['vignetteNom'] . '"';
			}
			// Sinon on génère un nom automatique selon le nom de la version intermediaire de l'image.
			else
			{
				$vignetteNom = nomSuffixe($infosImage['intermediaireNom'], '-vignette');
				
				// On vérifie si un fichier existe avec ce nom.
				// Si oui, on assigne une valeur à l'attribut `src`.
				if (file_exists($racineImgSrc . '/' . $vignetteNom))
				{
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
				// Sinon on génère une vignette.
				else
				{
					nouvelleImage($racineImgSrc . '/' . $infosImage['intermediaireNom'], $racineImgSrc . '/' . $vignetteNom, $typeMime, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, $galerieQualiteJpg, $galerieCouleurAlloueeImage, array ('nettete' => FALSE));
					
					// On assigne l'attribut `src`.
					$src = 'src="' . $urlImgSrc . '/' . $vignetteNom . '"';
				}
			}
			
			if ($vignetteAvecDimensions)
			{
				if (!empty($infosImage['vignetteLargeur']) || !empty($infosImage['vignetteHauteur']))
				{
					if (!empty($infosImage['vignetteLargeur']))
					{
						$width = 'width="' . $infosImage['vignetteLargeur'] . '"';
					}
				
					if (!empty($infosImage['vignetteHauteur']))
					{
						$height = 'height="' . $infosImage['vignetteHauteur'] . '"';
					}
				}
				else
				{
					list ($larg, $haut) = getimagesize($racineImgSrc . '/' . $vignetteNom);
					$width = 'width="' . $larg . '"';
					$height = 'height="' . $haut . '"';
				}
			}
			else
			{
				$width = '';
				$height = '';
			}
		}
		
		if (!empty($infosImage['vignetteAlt']))
		{
			$alt = 'alt="' . $infosImage['vignetteAlt'] . '"';
		}
		else
		{
			$titreImage = titreImage($infosImage);
			$alt = 'alt="' . sprintf(T_("Image %1\$s"), $titreImage) . '"';
		}
		
		if (!empty($infosImage['vignetteAttributTitle']))
		{
			$attributTitle = 'title="' . $infosImage['vignetteAttributTitle'] . '"';
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
			if (!empty($infosImage['intermediaireLegende']))
			{
				$title = ' title="' . preg_replace(array ('/</', '/>/', '/"/'), array ('&lt;', '&gt;', "'"), $infosImage['intermediaireLegende']) . '"';
			}
			else
			{
				$title = '';
			}
			
			$aHref = '<a href="' . $urlImgSrc . '/' . $infosImage['intermediaireNom'] . '" rel="lightbox-galerie"' . $title . '>';
		}
		else
		{
			switch ($galerieAncreDeNavigation)
			{
				case 'galerie':
					$ancre = '#galerie';
					break;
					
				case 'titre':
					$ancre = '#galerieTitre';
					break;
					
				case 'sousTitre':
					$ancre = '#galerieSousTitre';
					break;
					
				case 'info':
					$ancre = '#galerieInfo';
					break;
					
				case 'minivignettes':
					$ancre = '#galerieMinivignettes';
					break;
					
				case 'divImage':
					$ancre = '#galerieIntermediaire';
					break;
					
				case 'image':
					$ancre = '#galerieIntermediaireImg';
					break;
					
				default:
					$ancre = '';
					break;
			}
			
			$id = idImage($racine, $infosImage);
			$aHref = '<a href="' . url(FALSE, FALSE) . '?image=' . $id . $ancre . '" title="' . $titreImage . '">';
		}
		
		if ($minivignetteImageEnCours)
		{
			$class .= ' minivignetteImageEnCours';
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
  - `$pagination['nombreDePages']`: nombre de pages de la pagination;
  - `$pagination['indicePremierElement']`: indice du premier élément de la page en cours;
  - `$pagination['indiceDernierElement']`: indice du dernier élément de la page en cours;
  - `$pagination['baliseTitle']`: contenu de la balise `title` modifié pour prendre en compte la pagination;
  - `$pagination['description']`: contenu de la métabalise `description` modifié pour prendre en compte la pagination;
  - `$pagination['estPageDerreur']`: informe si la page demandée par la variable GET `page` existe. Vaut TRUE si la page n'existe pas.
*/
function pagination($racine, $urlRacine, $type, $nombreElements, $elementsParPage, $urlSansGet, $baliseTitle, $description)
{
	$pagination = array ();
	$pagination['pagination'] = '';
	$pagination['nombreDePages'] = ceil($nombreElements / $elementsParPage);
	$pagination['indicePremierElement'] = 0;
	$pagination['indiceDernierElement'] = 0;
	$pagination['baliseTitle'] = $baliseTitle;
	$pagination['description'] = $description;
	$pagination['estPageDerreur'] = FALSE;

	if (isset($_GET['page']))
	{
		$page = intval($_GET['page']);
		
		if ($page > $pagination['nombreDePages'])
		{
			$pagination['estPageDerreur'] = TRUE;
			$page = $pagination['nombreDePages'];
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
		$pagination['baliseTitle'] = sprintf(T_("%1\$s – Page %2\$s"), $pagination['baliseTitle'], $page);
		$pagination['description'] = sprintf(T_("%1\$s – Page %2\$s"), $pagination['description'], $page);
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
				$lien .= $cle . '=' . $valeur . '&amp;';
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
		// Sinon on n'ajoute pas de variable GET `page` et on supprime le dernier `&amp;` ou `?`.
		else
		{
			$lienPrecedent = preg_replace('/(\?|&amp;)$/', '', $lien);
		}
		
		$pagination['pagination'] .= '<a href="' . $lienPrecedent . '">';

		if ($type == 'image')
		{
			if (file_exists($racine . '/site/fichiers/precedent.png'))
			{
				$srcPrecedent = $urlRacine . '/site/fichiers/precedent.png';
			}
			else
			{
				$srcPrecedent = $urlRacine . '/fichiers/precedent.png';
			}
			
			$pagination['pagination'] .= '<img class="paginationPrecedent" src="' . $srcPrecedent . '" alt="' . T_("Page précédente") . '" width="80" height="80" />';
		}
		elseif ($type == 'texte')
		{
			$pagination['pagination'] .= T_("Page précédente");
		}
		
		$pagination['pagination'] .= '</a>';
	}
	
	if ($page < $pagination['nombreDePages'])
	{
		$numeroPageSuivant = $page + 1;
		$lienSuivant = $lien . 'page=' . $numeroPageSuivant;
		
		if (isset($lienPrecedent))
		{
			$pagination['pagination'] .= '<span class="separateurPaginationType' . ucfirst($type) . '"> | </span>';
		}

		$pagination['pagination'] .= '<a href="' . $lienSuivant . '">';
		
		if ($type == 'image')
		{
			if (file_exists($racine . '/site/fichiers/suivant.png'))
			{
				$srcSuivant = $urlRacine . '/site/fichiers/suivant.png';
			}
			else
			{
				$srcSuivant = $urlRacine . '/fichiers/suivant.png';
			}
			
			$pagination['pagination'] .= '<img class="paginationSuivant" src="' . $srcSuivant . '" alt="' . T_("Page suivante") . '" width="80" height="80" />';
		}
		elseif ($type == 'texte')
		{
			$pagination['pagination'] .= T_("Page suivante");
		}
		
		$pagination['pagination'] .= '</a>';
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
Retourne une liste des publications récentes pour le type de publication donné.

Les types possibles sont:

- `categorie`: derniers ajouts à une catégorie en particulier;
- `galerie`: derniers ajouts à une galerie en particulier;
- `galeries`: derniers ajouts aux galeries;
- `site`: dernières publications sur le site.

Le paramètre `$id` n'est utile que pour les types `categorie` (fournir la valeur de `$idCategorie`) et `galerie` (fournir la valeur de `$idGalerie`). Dans les autres cas, ce paramètre peut être vide.

Le paramètre `$nombreVoulu` correspond au nombre de publications dans la liste retournée.

Le paramètre `$ajouterLien` peut valoir TRUE ou FALSE. S'il vaut TRUE, un lien est ajouté vers la liste complète des publications pour le type donné (par exemple vers la liste de toutes les pages appartenant à une catégorie).

À noter que le code retourné peut ne pas avoir été généré, mais lu dans le cache, si `$dureeCache['publications-recentes']` du fichier de configuration du site vaut plus de 0.
*/
function publicationsRecentes($racine, $urlRacine, $langueParDefaut, $langue, $type, $id, $nombreVoulu, $ajouterLien, $dureeCache, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger)
{
	$html = '';
	
	if ($type == 'categorie')
	{
		// On vérifie si la liste existe en cache ou si le cache est expiré.
		
		if ($ajouterLien)
		{
			$lienCache = 'avec-lien';
		}
		else
		{
			$lienCache = 'sans-lien';
		}
		
		$nomFichierCache = filtreChaine($racine, "publications-recentes-categorie-$id-$nombreVoulu-$lienCache-$langue.cache.html");
		
		if ($dureeCache['publications-recentes'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['publications-recentes']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$itemsFluxRss = array ();
			$lienDesactive = FALSE;
			$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
			
			if (!empty($categories) && isset($categories[$id]))
			{
				$nombreReel = count($categories[$id]);
				
				if ($nombreVoulu >= $nombreReel)
				{
					$lienDesactive = TRUE;
				}
				
				if ($nombreVoulu > $nombreReel)
				{
					$nombreVoulu = $nombreReel;
				}
				
				$i = 0;
				
				foreach ($categories[$id]['pages'] as $page)
				{
					if ($i < $nombreVoulu)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", "$urlRacine/$page", FALSE, 600);
						
						if (!empty($fluxRssPageTableauBrut))
						{
							$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
						}
					}
					
					$i++;
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal('categorie', $itemsFluxRss, $nombreVoulu);
					
					foreach ($itemsFluxRss as $cle => $valeur)
					{
						$html .= '<li><a href="' . $valeur['link'] . '">' . $valeur['title'] . "</a></li>\n";
					}
					
					if (!empty($html))
					{
						if ($ajouterLien && !$lienDesactive)
						{
							$categories[$id]['urlCat'] = urlCat($categories[$id], $id, $langueParDefaut);
							$lien = $urlRacine . '/' . $categories[$id]['urlCat'];
							$codeLien = '<p class="publicationsRecentesLien"><a href="' . $lien . '">' . T_("Voir plus de titres") . "</a></p>\n";
						}
						else
						{
							$codeLien = '';
						}
						
						$html = "<div class=\"publicationsRecentes publicationsRecentesCategorie\">\n<ul>\n$html</ul>\n$codeLien</div>\n";
					}
				}
			}
			
			if ($dureeCache['publications-recentes'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $html);
			}
		}
	}
	elseif ($type == 'galerie')
	{
		// On vérifie si la liste existe en cache ou si le cache est expiré.
		
		if ($ajouterLien)
		{
			$lienCache = 'avec-lien';
		}
		else
		{
			$lienCache = 'sans-lien';
		}
		
		$nomFichierCache = filtreChaine($racine, "publications-recentes-galerie-$id-$nombreVoulu-$lienCache-$langue.cache.html");
		
		if ($dureeCache['publications-recentes'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['publications-recentes']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$lienDesactive = FALSE;
			$urlGalerie = '';
			$cheminConfigFluxRssGlobalGaleries = cheminConfigFluxRssGlobal($racine, 'galeries');
			
			if ($cheminConfigFluxRssGlobalGaleries)
			{
				$galeries = super_parse_ini_file($cheminConfigFluxRssGlobalGaleries, TRUE);
				
				if (!empty($galeries) && isset($galeries[$langue][$id]))
				{
					$urlGalerie = $urlRacine . '/' . $galeries[$langue][$id];
				}
			}
			
			if (!empty($urlGalerie))
			{
				$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $id), TRUE);
				
				if ($tableauGalerie !== FALSE)
				{
					$vignettes = array ();
					
					foreach ($tableauGalerie as $image)
					{
						$titreImage = titreImage($image);
						$title = $titreImage;
						$alt = $titreImage;
						
						if (!empty($image['dateAjout']))
						{
							$date = $image['dateAjout'];
						}
						else
						{
							$date = date('Y-m-d H:i', filemtime("$racine/site/fichiers/galeries/$idGalerie/" . $image['intermediaireNom']));
						}
						
						if (isset($image['vignetteNom']))
						{
							$vignetteNom = $image['vignetteNom'];
						}
						else
						{
							$vignetteNom = nomSuffixe($image['intermediaireNom'], '-vignette');
						}
					
						if (!empty($image['vignetteLargeur']) || !empty($image['vignetteHauteur']))
						{
							if (!empty($image['vignetteLargeur']))
							{
								$width = $image['vignetteLargeur'];
							}
				
							if (!empty($image['vignetteHauteur']))
							{
								$height = $image['vignetteHauteur'];
							}
						}
						else
						{
							list ($width, $height) = getimagesize($racine . '/site/fichiers/galeries/' . $id . '/' . $vignetteNom);
						}
						
						$vignettes[] = array (
							'code' => '<li><a href="' . superRawurlencode($urlGalerie . '?image=' . idImage($racine, $image)) . '" title="' . $title . '">' . '<img src="' . $urlRacine . '/site/fichiers/galeries/' . rawurlencode($id) . '/' . $vignetteNom . '" alt="' . $alt . '" width="' . $width . '" height="' . $height . '" />' . "</a></li>\n",
							'date' => $date,
						);
					}
					
					foreach ($vignettes as $cle => $valeur)
					{
						$vignettesDate[$cle] = $valeur['date'];
					}
					
					array_multisort($vignettesDate, SORT_DESC, $vignettes);
					
					$nombreReel = count($vignettes);
					
					if ($nombreVoulu >= $nombreReel)
					{
						$lienDesactive = TRUE;
					}
					
					if ($nombreVoulu > $nombreReel)
					{
						$nombreVoulu = $nombreReel;
					}
					
					for ($i = 0; $i < $nombreVoulu; $i++)
					{
						$html .= $vignettes[$i]['code'];
					}
					
					if (!empty($html))
					{
						if ($ajouterLien && !$lienDesactive)
						{
							$codeLien = '<p class="publicationsRecentesLien"><a href="' . $urlGalerie . '">' . T_("Voir plus d'images") . "</a></p>\n";
						}
						else
						{
							$codeLien = '';
						}
						
						$html = "<div class=\"publicationsRecentes publicationsRecentesGalerie\">\n<ul>\n$html</ul>\n$codeLien</div>\n";
					}
				}
			}
			
			if ($dureeCache['publications-recentes'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $html);
			}
		}
	}
	elseif ($type == 'galeries')
	{
		// On vérifie si la liste existe en cache ou si le cache est expiré.
		
		if ($ajouterLien)
		{
			$lienCache = 'avec-lien';
		}
		else
		{
			$lienCache = 'sans-lien';
		}
		
		$nomFichierCache = filtreChaine($racine, "publications-recentes-galeries-$nombreVoulu-$lienCache-$langue.cache.html");
		
		if ($dureeCache['publications-recentes'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['publications-recentes']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$lienDesactive = FALSE;
			$itemsFluxRss = fluxRssGaleriesTableauBrut($racine, $urlRacine, $langue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
			
			if (!empty($itemsFluxRss))
			{
				$nombreReel = count($itemsFluxRss);
				
				if ($nombreVoulu >= $nombreReel)
				{
					$lienDesactive = TRUE;
				}
				
				if ($nombreVoulu > $nombreReel)
				{
					$nombreVoulu = $nombreReel;
				}
				
				$itemsFluxRss = fluxRssTableauFinal('galeries', $itemsFluxRss, $nombreVoulu);
			}
			
			if (!empty($itemsFluxRss))
			{
				for ($i = 0; $i < $nombreVoulu; $i++)
				{
					preg_match('/<img src="([^"]+)"/', htmlspecialchars_decode($itemsFluxRss[$i]['description']), $resultat);
					$intermediaireSrc = rawurldecode($resultat[1]);
					$intermediaireNom = superBasename($intermediaireSrc);
					$idGalerie = superBasename(str_replace("/$intermediaireNom", '', $intermediaireSrc));
				
					if (!empty($idGalerie) && cheminConfigGalerie($racine, $idGalerie))
					{
						$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
					
						if (isset($tableauGalerie[$intermediaireNom]['vignetteNom']))
						{
							$vignetteNom = $tableauGalerie[$intermediaireNom]['vignetteNom'];
						}
						else
						{
							$vignetteNom = nomSuffixe($intermediaireNom, '-vignette');
						}
					
						if (!empty($tableauGalerie[$intermediaireNom]['vignetteLargeur']) || !empty($tableauGalerie[$intermediaireNom]['vignetteHauteur']))
						{
							if (!empty($tableauGalerie[$intermediaireNom]['vignetteLargeur']))
							{
								$width = $tableauGalerie[$intermediaireNom]['vignetteLargeur'];
							}
				
							if (!empty($tableauGalerie[$intermediaireNom]['vignetteHauteur']))
							{
								$height = $tableauGalerie[$intermediaireNom]['vignetteHauteur'];
							}
						}
						else
						{
							list ($width, $height) = getimagesize($racine . '/site/fichiers/galeries/' . $idGalerie . '/' . $vignetteNom);
						}
					
						$vignetteImg = '<img src="' . $urlRacine . '/site/fichiers/galeries/' . rawurlencode($idGalerie) . '/' . $vignetteNom . '" alt="' . $itemsFluxRss[$i]['title'] . '" width="' . $width . '" height="' . $height . '" />';
					}
				
					$html .= '<li><a href="' . $itemsFluxRss[$i]['link'] . '" title="' . $itemsFluxRss[$i]['title'] . '">' . "$vignetteImg</a></li>\n";
				}
			
				if (!empty($html))
				{
					if ($ajouterLien && !$lienDesactive)
					{
						$cheminFichier = cheminConfigCategories($racine);
					
						if ($cheminFichier)
						{
							$categories = super_parse_ini_file($cheminFichier, TRUE);
						}
						else
						{
							$categories = array ();
						}
					
						$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $langue, $categories, array ('galeries'), $nombreVoulu, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
						$lien = $urlRacine . '/' . $categories['galeries']['urlCat'];
						$codeLien = '<p class="publicationsRecentesLien"><a href="' . $lien . '">' . T_("Voir plus d'images") . "</a></p>\n";
					}
					else
					{
						$codeLien = '';
					}
					
					$html = "<div class=\"publicationsRecentes publicationsRecentesGaleries\">\n<ul>\n$html</ul>\n$codeLien</div>\n";
				}
			}
			
			if ($dureeCache['publications-recentes'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $html);
			}
		}
	}
	elseif ($type == 'site')
	{
		// On vérifie si la liste existe en cache ou si le cache est expiré.
		
		if ($ajouterLien)
		{
			$lienCache = 'avec-lien';
		}
		else
		{
			$lienCache = 'sans-lien';
		}
		
		$nomFichierCache = filtreChaine($racine, "publications-recentes-site-$nombreVoulu-$lienCache-$langue.cache.html");
		
		if ($dureeCache['publications-recentes'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['publications-recentes']))
		{
			@readfile("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			$lienDesactive = FALSE;
			$itemsFluxRss = array ();
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
			
			if (!empty($pages) && isset($pages[$langue]['pages']))
			{
				$nombreReel = count($pages[$langue]['pages']);
				
				if ($nombreVoulu >= $nombreReel)
				{
					$lienDesactive = TRUE;
				}
				
				if ($nombreVoulu > $nombreReel)
				{
					$nombreVoulu = $nombreReel;
				}
				
				$i = 0;
				
				foreach ($pages[$langue]['pages'] as $page)
				{
					if ($i < $nombreVoulu)
					{
						$page = rtrim($page);
						$fluxRssPageTableauBrut = fluxRssPageTableauBrut("$racine/$page", $urlRacine . '/' . $page, FALSE, 600);
					
						if (!empty($fluxRssPageTableauBrut))
						{
							$itemsFluxRss = array_merge($itemsFluxRss, $fluxRssPageTableauBrut);
						}
					}
					
					$i++;
				}
				
				if (!empty($itemsFluxRss))
				{
					$itemsFluxRss = fluxRssTableauFinal('site', $itemsFluxRss, $nombreVoulu);
					
					foreach ($itemsFluxRss as $cle => $valeur)
					{
						$html .= '<li><a href="' . $valeur['link'] . '">' . $valeur['title'] . "</a></li>\n";
					}
					
					if (!empty($html))
					{
						if ($ajouterLien && !$lienDesactive)
						{
							$cheminFichier = cheminConfigCategories($racine);
							
							if ($cheminFichier)
							{
								$categories = super_parse_ini_file($cheminFichier, TRUE);
							}
							else
							{
								$categories = array ();
							}
							
							$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $langue, $categories, array ('site'), $nombreVoulu, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
							$lien = $urlRacine . '/' . $categories['site']['urlCat'];
							$codeLien = '<p class="publicationsRecentesLien"><a href="' . $lien . '">' . T_("Voir plus de titres") . "</a></p>\n";
						}
						else
						{
							$codeLien = '';
						}
						
						$html = "<div class=\"publicationsRecentes publicationsRecentesSite\">\n<ul>\n$html</ul>\n$codeLien</div>\n";
					}
				}
			}
			
			if ($dureeCache['publications-recentes'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $html);
			}
		}
	}
	
	return $html;
}

/*
Retourne le contenu de la métabalise `robots`.
*/
function robots($robotsParDefaut, $robots)
{
	return !empty($robots) ? $robots : $robotsParDefaut;
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
		return array_map('securiseTexte', $texte);
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
Retourne TRUE si le site est en maintenance, sinon retourne FALSE.
*/
function siteEstEnMaintenance($cheminHtaccess)
{
	if ($fic = @fopen($cheminHtaccess, 'r'))
	{
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			
			if (preg_match('/^# Ajout automatique de Squeletml \(maintenance\). Ne pas modifier./', $ligne))
			{
				return TRUE;
			}
		}
		
		fclose($fic);
	}
	
	return FALSE;
}

/*
Simule la fonction `basename()` sans dépendre de la locale. Merci à <http://drupal.org/node/278425>.
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
Retourne l'URL traitée par `rawurlencode`, mais avec quelques substitutions.
*/
function superRawurlencode($url, $decoderEsperluette = FALSE)
{
	$url = preg_replace('/&(?!amp;)/', '&amp;', $url);
	$url = rawurlencode($url);
	
	if ($decoderEsperluette)
	{
		// Entre autres pour `curl_init()`, `file_get_contents()`, `fopen()`, `get_headers()` et `readfile()`.
		$url = str_replace('%26amp%3B', '&', $url);
	}
	else
	{
		$url = str_replace('%26amp%3B', '&amp;', $url);
	}
	
	$url = str_replace('%3A', ':', $url);
	$url = str_replace('%2F', '/', $url);
	$url = str_replace('%3F', '?', $url);
	$url = str_replace('%3D', '=', $url);
	
	return $url;
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

	$tableauGalerie = array (
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
		$tableauGalerie = array ();
		
		foreach ($galerieIni as $image => $infos)
		{
			if (!$exclure || !(isset($infos['exclure']) && $infos['exclure'] == 'oui'))
			{
				$infos['intermediaireNom'] = $image;
				$tableauGalerie[] = $infos;
			}
		}
		
		return $tableauGalerie;
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le titre d'une image.
*/
function titreImage($image)
{
	if (!empty($image['titre']))
	{
		return $image['titre'];
	}
	elseif (!empty($image['id']))
	{
		return $image['id'];
	}
	else
	{
		return $image['intermediaireNom'];
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
Retourne l'URL de la page courante. Un premier paramètre optionnel, s'il vaut FALSE, permet de ne pas retourner les variables GET. Un deuxième paramètre optionnel, s'il vaut FALSE, permet de retourner seulement l'URL demandée sans la partie serveur. Un troisième paramètre optionnel, s'il vaut TRUE, active la recherche d'un fichier d'index (par exemple `index.php`) pour l'ajouter, s'il y a lieu, à l'URL.

Note: si l'URL contient une ancre, cette dernière sera perdue, car le serveur n'en a pas connaissance. Par exemple, si l'URL fournie est `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3#ancre`, la fonction va retourner `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3` si `$retourneVariablesGet` et `$retourneServeur` vallent TRUE.

Fonction inspirée de <http://api.drupal.org/api/function/drupal_detect_baseurl>.
*/
function url($retourneVariablesGet = TRUE, $retourneServeur = TRUE, $rechercherIndex = FALSE)
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
	
	if ($rechercherIndex)
	{
		preg_match('/(\?.*)/', $uri, $resultat);
		
		if (!empty($resultat[1]))
		{
			$variablesGet = $resultat[1];
		}
		else
		{
			$variablesGet = '';
		}
		
		$uriSansGet = preg_replace('/\?.*/', '', $uri);
		$urlSansGet = $protocole . $serveur . $port . $uriSansGet;
		$urlSansGetAvecIndex = urlAvecIndex($urlSansGet);
		
		if ($urlSansGetAvecIndex != $urlSansGet)
		{
			$fichierIndex = superBasename($urlSansGetAvecIndex);
			$uriSansGet .= $fichierIndex;
		}
		
		$uri = $uriSansGet . $variablesGet;
	}
	
	if (!$retourneVariablesGet)
	{
		$uri = preg_replace('/\?.*/', '', $uri);
	}
	
	if ($retourneServeur)
	{
		$url = $protocole . $serveur . $port . $uri;
	}
	else
	{
		$url = $uri;
	}
	
	return $url;
}

/*
Recherche un index (par exemple `index.php`) pour l'URL fournie. Si un index a été trouvé, retourne l'URL avec l'index, sinon retourne l'URL de départ.
*/
function urlAvecIndex($url)
{
	if (preg_match('|/$|', $url))
	{
#		$fichiersIndex = array ('index.html', 'index.cgi', 'index.pl', 'index.php', 'index.xhtml', 'index.htm'); // Valeur par défaut de `DirectoryIndex` sous Apache 2.
#		
#		foreach ($fichiersIndex as $fichierIndex)
#		{
#			if (urlExiste($url . $fichierIndex))
#			{
#				$url .= $fichierIndex;
#				break;
#			}
#		}
		
		// À cause d'une lenteur sur les serveurs de Koumbit, je laisse tomber le test ci-dessus et ajoute directement `index.php`.
		$url .= 'index.php';
	}
	
	return $url;
}

/*
Retourne l'URL relative d'une catégorie.
*/
function urlCat($categorie, $idCategorie, $langueParDefaut)
{
	$langue = langueCat($categorie, $langueParDefaut);
	
	if (!empty($categorie['urlCat']))
	{
		if (strpos($categorie['urlCat'], 'categorie.php?id=') !== FALSE && !preg_match('/\blangue=/', $categorie['urlCat']) && estCatSpeciale($idCategorie) && !empty($langue))
		{
			$categorie['urlCat'] .= "&amp;langue=$langue";
		}
	}
	else
	{
		$categorie['urlCat'] = "categorie.php?id=$idCategorie";
		
		if (estCatSpeciale($idCategorie) && !empty($langue))
		{
			$categorie['urlCat'] .= "&amp;langue=$langue";
		}
	}
	
	return $categorie['urlCat'];
}

/*
Retourne TRUE si l'URL existe, sinon retourne FALSE.
*/
function urlExiste($url)
{
	$url = superRawurlencode($url, TRUE);
	$enTetes = '';
	
	if (function_exists('curl_init'))
	{
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$enTetes = @curl_exec($ch);
		@curl_close($ch);
	}
	else
	{
		@file_get_contents($url, 0, NULL, 0, 1);
		
		if (isset($http_response_header[0]))
		{
			$enTetes = $http_response_header[0];
		}
	}
	
	return preg_match('~^HTTP/\d+\.\d+\s+[23]~', $enTetes);
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
Retourne l'URL parente de la page courante. En d'autres mots, supprime la page courante de l'URL et retourne le résultat. Exemples:

- si l'URL est `http://www.NomDeDomaine.ext/dossier/fichier.php?a=2&b=3#ancre`, la fonction retourne `http://www.NomDeDomaine.ext/dossier`;
- si l'URL est `http://www.NomDeDomaine.ext/dossier/index.php`, la fonction retourne `http://www.NomDeDomaine.ext/dossier`;
- si l'URL est `http://www.NomDeDomaine.ext/dossier/`, la fonction retourne `http://www.NomDeDomaine.ext/dossier` (fichier index implicite);
- si l'URL est `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3#ancre`, la fonction retourne `http://www.NomDeDomaine.ext`;
- si l'URL est `http://www.NomDeDomaine.ext/index.php`, la fonction retourne `http://www.NomDeDomaine.ext`;
- si l'URL est `http://www.NomDeDomaine.ext/`, la fonction retourne `http://www.NomDeDomaine.ext` (fichier index implicite).
*/
function urlParente()
{
	$urlSansGet = url(FALSE);
	
	if (preg_match('|/$|', $urlSansGet))
	{
		$urlParente = substr($urlSansGet, 0, -1);
	}
	else
	{
		$urlParente = dirname($urlSansGet);
	}
	
	return $urlParente;
}

/*
Retourne sous forme de chaîne le code PHP nécessaire aux premières affectations du script. La chaîne retournée doit ensuite être exécutée par la fonction PHP `eval()`.
*/
function variablesAaffecterAuDebut()
{
	$variables = '$nomPage = nomPage();
	$url = url();
	$urlSansGet = url(FALSE);
	$urlAvecIndexSansGet = url(FALSE, TRUE, TRUE);';
	$variables .= variablesAvantConfig();
	
	return $variables;
}

/*
Retourne sous forme de chaîne le code PHP nécessaire aux affectations devant être effectuées avant l'inclusion des fichiers de configuration. La chaîne retournée doit ensuite être exécutée par la fonction PHP `eval()`.
*/
function variablesAvantConfig()
{
	$variables = '$urlFichiers = $urlRacine . \'/site/fichiers\';
	$urlRacineAdmin = $urlRacine . \'/\' . $dossierAdmin;
	$urlSite = $urlRacine . \'/site\';';
	
	return $variables;
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
	preg_match('/(alt="[^"]+")/', $paragraphe, $resultat);
	$alt = $resultat[1];
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
