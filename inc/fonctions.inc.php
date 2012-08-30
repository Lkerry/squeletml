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
		
		if ($htaccess !== FALSE && $acces !== FALSE && preg_match('#^\tAuthUserFile ' . preg_quote($racine, '#') . '/\.acces\n#m', $htaccess) && preg_match('/^[^:]+:/m', $acces))
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
	
	if (file_exists($racine . '/.acces') && strpos(@file_get_contents($racine . '/.acces'), ':') !== FALSE)
	{
		$lienAccesDansHtaccess = FALSE;
	
		if ($fic = @fopen($racine . '/.htaccess', 'r'))
		{
			while (!feof($fic))
			{
				$ligne = rtrim(fgets($fic));
			
				if (strpos($ligne, '# Ajout automatique de Squeletml (accès admin). Ne pas modifier.') === 0)
				{
					$lienAccesDansHtaccess = TRUE;
					break;
				}
			}
			
			fclose($fic);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
		}

		if (!$lienAccesDansHtaccess)
		{
			$htaccess = '';
			$htaccess .= "# Ajout automatique de Squeletml (accès admin). Ne pas modifier.\n";
			$htaccess .= "# Empêcher l'affichage direct de certains fichiers.\n";
		
			$htaccessFilesModele = "\.((admin|cli|inc|lib)\.php|cache\.(gif|html|jpe?g|png|xml)|acces|info|ini|mkd|mo|modele|pot?|sauv|src\.svg|te?xt)$";
		
			if ($serveurFreeFr)
			{
				$htaccess .= "<Files ~ \"$htaccessFilesModele\">\n";
	
				preg_match('#/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+(.+)#', $racine . '/.acces', $cheminAcces);
	
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
	
				preg_match('#/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+(.+)#', $racine . '/.deconnexion.acces', $cheminAcces);
	
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
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
function actionFormContact($partageCourrielActif)
{
	$action = url();
	
	if ($partageCourrielActif)
	{
		$action .= '#titrePartageCourriel';
	}
	else
	{
		$action .= '#messages';
	}
	
	return $action;
}

/*
Ajoute au tableau des catégories les catégories spéciales demandées (si elles n'existent pas déjà), et retourne le tableau résultant.
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
			$categories['galeries']['langue'] = $langue;
			$categories['galeries']['url'] = "categorie.php?id=galeries&amp;langue=$langue";
			$categories['galeries']['pages'] = array ();
			
			foreach ($itemsFluxRss as $item => $infosItem)
			{
				$categories['galeries']['pages'][] = supprimeUrlRacine($urlRacine, $infosItem['link']);
			}
		}
	}
	
	if (in_array('site', $categoriesSpecialesAajouter) && !isset($categories['site']))
	{
		$cheminFichier = cheminConfigFluxRssGlobalSite($racine);
	
		if ($cheminFichier)
		{
			$pages = super_parse_ini_file($cheminFichier, TRUE);
		}
	
		if (isset($pages[$langue]))
		{
			$categories = array ('site' => array ()) + $categories;
			$categories['site']['langue'] = $langue;
			$categories['site']['url'] = "categorie.php?id=site&amp;langue=$langue";
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
Retourne l'ancre de navigation d'une galerie.

Voir les explications de la variable `$galerieAncreDeNavigation` dans le ficher de configuration du site.
*/
function ancreDeNavigationGalerie($nomAncre)
{
	switch ($nomAncre)
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
	
	return $ancre;
}

/*
Retourne le code HTML de l'aperçu d'une page apparaissant dans une page de catégorie.
*/
function apercuDansCategorie($racine, $urlRacine, $infosPage, $adresse, $baliseTitleComplement)
{
	$apercu = '';
	$apercu .= "<div class=\"apercu\">\n";
	
	if (!empty($baliseTitleComplement))
	{
		$infosPage['titre'] = preg_replace('/' . preg_quote($baliseTitleComplement, '/') . '$/', '', $infosPage['titre']);
	}

	$apercu .= "<h2 class=\"titreApercu\"><a href=\"$adresse\">{$infosPage['titre']}</a></h2>\n";
	$listeCategoriesAdresse = listeCategoriesPage($racine, $urlRacine, $adresse);
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
		$texteApercu = $infosPage['apercu'];
	}
	else
	{
		$texteApercu = $infosPage['contenu'];
	}
	
	// Incrémentation des niveaux de titre 2 à 5 (par exemple, `<h2 class="classe">Sous-titre</h2>` devient `<h3 class="classe">Sous-titre</h3>`). Le but est d'éviter que des sous-titres affichés dans l'aperçu aient le même niveau (2) que le titre de l'aperçu lui-même. Cela découle du fait que le titre d'une page est habituellement de niveau 1 alors que dans l'aperçu, le même titre est de niveau 2.
	$texteApercu = preg_replace('#<h([2-5])(.*?>.*?</h)\1\b#e', '"<h" . ("$1" + 1) . "$2" . ("$1" + 1)', $texteApercu);
	
	$apercu .= $texteApercu . "\n";
	$apercu .= "</div><!-- /.descriptionApercu -->\n";

	if (!empty($infosPage['apercu']))
	{
		$apercu .= "<div class=\"lienApercu\">\n";
		$apercu .= '<p>' . sprintf(T_("Lire la suite de %1\$s"), "<em><a href=\"$adresse\">" . $infosPage['titre'] . '</a></em></p>') . "\n";
		$apercu .= "</div><!-- /.lienApercu -->\n";
	}

	$apercu .= "</div><!-- /.apercu -->\n";

	return $apercu;
}

/*
Coupe en segments la partie de l'URL à la suite du serveur, et retourne le segment (argument) demandé, si ce dernier existe, sinon retourne le tableau de segments.

Par exemple, pour l'URL `http://www.NomDeDomaine.ext/dossier1/dossier2/fichier.php?a=2&b=3#ancre`, `arg()` va retourner `array (0 => 'dossier1', 1 => 'dossier2', 3 => 'fichier.php')`.

Inspirée de <http://api.drupal.org/api/function/arg/6>.
*/
function arg($index = NULL)
{
	$urlArg = url(FALSE, FALSE);
	$urlArg = preg_replace('#^/#', '', $urlArg);
	$args = explode('/', $urlArg);
	
	if (isset($index) && isset($args[$index]))
	{
		return $args[$index];
	}
	else
	{
		return $args;
	}
}

/*
Retourne l'attribut `lang` à utiliser selon le DTD.
*/
function attributLang($codeLangue, $doctype)
{
	$attribut = '';
	
	if ($doctype == 'XHTML 1.1' || $doctype == 'XHTML 1.0 Strict' || $doctype == 'XHTML 1.0 Transitional')
	{
		$attribut = 'xml:lang="' . $codeLangue . '"';
	}
	
	if ($doctype != 'XHTML 1.1')
	{
		if (!empty($attribut))
		{
			$attribut .= ' ';
		}
		
		$attribut .= 'lang="' . $codeLangue . '"';
	}
	
	return $attribut;
}

/*
Retourne le code à utiliser pour afficher l'auteur d'un commentaire.
*/
function auteurAfficheCommentaire($nom, $site, $attributNofollowLiensCommentaires)
{
	if (!empty($nom))
	{
		$auteur = $nom;
	}
	elseif (!empty($site))
	{
		$auteur = $site;
	}
	else
	{
		$auteur = T_("Anonyme");
	}
	
	if (!empty($site))
	{
		$auteurAffiche = '<a href="' . $site . '"';
		
		if ($attributNofollowLiensCommentaires)
		{
			$auteurAffiche .= ' rel="nofollow"';
		}
		
		$auteurAffiche .= '>' . $auteur . '</a>';
	}
	else
	{
		$auteurAffiche = $auteur;
	}
	
	return $auteurAffiche;
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
			$baliseTitle = $baliseH1;
		}
		else
		{
			$baliseTitle = variableGet(0, url(), 'action');
		}
		
		$baliseTitle = securiseTexte(supprimeBalisesHtml($baliseTitle));
	}
	
	return $baliseTitle;
}

/*
Retourne le complément de la balise `title`. Si aucun complément, n'a été trouvé, retourne une chaîne vide.
*/
function baliseTitleComplement($tableauBaliseTitleComplement, $langues, $estAccueil)
{
	$baliseTitleComplement = '';
	
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
			$baliseTitleComplement = $tableauBaliseTitleComplement[$langue][$premierElementAtester];
			break;
		}
		elseif (isset($tableauBaliseTitleComplement[$langue][$deuxiemeElementAtester]))
		{
			$baliseTitleComplement = $tableauBaliseTitleComplement[$langue][$deuxiemeElementAtester];
			break;
		}
	}
	
	return $baliseTitleComplement;
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
		$boitesDeroulantesTableau = array ();
	}
	
	return $boitesDeroulantesTableau;
}

/*
Vérifie si le cache d'un fichier expire.
*/
function cacheExpire($cheminFichierCache, $dureeCache)
{
	$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
	$dateExpiration = 0;
	
	if (file_exists($cheminFichierCache))
	{
		if (file_exists($cheminFichierCacheEnTete))
		{
			$contenuFichierCacheEnTete = @file_get_contents($cheminFichierCacheEnTete);
			
			if ($contenuFichierCacheEnTete !== FALSE && preg_match('/header\("Expires: ([^"]+)/', $contenuFichierCacheEnTete, $resultat))
			{
				$dateExpiration = @strtotime($resultat[1]);
			}
		}
		else
		{
			$dateExpiration = @filemtime($cheminFichierCache) + $dureeCache;
		}
	}
	
	if (time() < $dateExpiration)
	{
		return FALSE;
	}
	
	return TRUE;
}

/*
Retourne le code à insérer dans un formulaire pour afficher un antipourriel basé sur un calcul mathématique.
*/
function captchaCalcul($calculMin = 2, $calculMax = 10, $calculInverse = TRUE, $afficherAsterisque = FALSE)
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
	
	if ($afficherAsterisque)
	{
		$captchaCalcul .= '<p><label>' . T_("Antipourriel<code>*</code>:") . "</label><br />\n";
	}
	else
	{
		$captchaCalcul .= '<p><label>' . T_("Antipourriel:") . "</label><br />\n";
	}
	
	if ($calculInverse)
	{
		$captchaCalcul .= sprintf(T_("Veuillez indiquer deux nombres qui, une fois additionnés, donnent %1\$s (plus d'une réponse correcte):"), $calculUn);
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
Retourne `TRUE` si le résultat du calcul du captcha est correct, sinon retourne `FALSE`.
*/
function captchaCalculValide($commentairesCaptchaCalculInverse)
{
	if (!isset($_POST['r']) || !is_numeric($_POST['r']))
	{
		return FALSE;
	}
	
	if (!isset($_POST['u']) || !is_numeric($_POST['u']))
	{
		return FALSE;
	}
	
	if ($commentairesCaptchaCalculInverse)
	{
		if (!isset($_POST['s']) || !is_numeric($_POST['s']))
		{
			return FALSE;
		}
		
		$resultat = $_POST['u'];
		$sommeUnDeux = $_POST['r'] + $_POST['s'];
	}
	else
	{
		if (!isset($_POST['d']) || !is_numeric($_POST['d']))
		{
			return FALSE;
		}
		
		$resultat = $_POST['r'];
		$sommeUnDeux = $_POST['u'] + $_POST['d'];
	}
	
	return $sommeUnDeux == $resultat;
}

/*
S'il y a lieu, ajoute la classe `actif` au lien de chaque catégorie à laquelle la page fait partie ainsi qu'au `li` contenant le lien. Retourne le code résultant.
*/
function categoriesActives($codeMenuCategories, $listeCategoriesPage, $idCategorie)
{
	if (!empty($listeCategoriesPage) || !empty($idCategorie))
	{
		$dom = str_get_html($codeMenuCategories);
		
		if (method_exists($dom, 'find'))
		{
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
		
			$codeMenuCategories = $dom->save();
			$dom->clear();
		}
		
		unset($dom);
	}
	
	return $codeMenuCategories;
}

/*
Retourne un tableau contenant la liste des catégories enfants d'une catégorie donnée. Les catégories enfants doivent avoir la même langue que la catégorie parente.
*/
function categoriesEnfants($categories, $categorie)
{
	$categoriesEnfants = array ();
	
	foreach ($categories as $cat => $catInfos)
	{
		if (!empty($catInfos['parent']) && $catInfos['parent'] == $categorie && isset($catInfos['langue']) && isset($categories[$categorie]['langue']) && $catInfos['langue'] == $categories[$categorie]['langue'])
		{
			$categoriesEnfants[] = $cat;
		}
	}
	
	return $categoriesEnfants;
}

/*
Retourne un tableau contenant la liste des catégories parentes indirectes d'une catégorie donnée. Par exemple, si la catégorie donnée est «Miniatures», que cette dernière a comme catégorie parente «Chiens», et que la catégorie «Chiens» est une catégorie enfant de «Animaux», la fonction va retourner `array ('Chiens', 'Animaux')`.

Note: chaque catégorie parente doit avoir la même langue que la catégorie donnée.
*/
function categoriesParentesIndirectes($categories, $categorie)
{
	$categoriesParentesIndirectes = array ();
	
	if (!empty($categories[$categorie]['parent']))
	{
		$idCatParente = $categories[$categorie]['parent'];
	}
	else
	{
		$idCatParente = '';
	}
	
	if (!empty($idCatParente) && isset($categories[$idCatParente]['langue']) && isset($categories[$categorie]['langue']) && $categories[$idCatParente]['langue'] == $categories[$categorie]['langue'])
	{
		$categoriesParentesIndirectes[] = $idCatParente;
		$categoriesParentesIndirectes = array_merge($categoriesParentesIndirectes, categoriesParentesIndirectes($categories, $idCatParente));
	}
	
	return array_unique($categoriesParentesIndirectes);
}

/*
Retourne une chaine aléatoire formée de caractères alphanumériques.
*/
function chaineAleatoire($longueur)
{
	$caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$chaineAleatoire = '';
	$compteur = 0;
	
	while ($compteur < $longueur)
	{
		$caractere = $caracteres[mt_rand(0, 61)];
		
		// Éviter de former un attribut `id` invalide (c'est-à-dire commençant par un chiffre).
		if ($compteur == 0 && ctype_digit($caractere))
		{
			continue;
		}
		
		$chaineAleatoire .= $caractere;
		$compteur++;
	}
	
	return $chaineAleatoire;
}

/*
Retourne la chaîne fournie en paramètre filtrée convenablement pour un nom de classe CSS.
*/
function chaineVersClasseCss($chaine)
{
	$classe = rawurldecode($chaine);
	$classe = str_replace('&amp;', '&', $classe);
	$classe = filtreChaine($classe);
	$classe = str_replace(array ('.', '+'), '-', $classe);
	$classe = filtreChaine($classe);
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
		$contenuChapeau = mkdChaine($contenuChapeau);
	}
	
	$chapeau .= $contenuChapeau;
	$chapeau .= "</div><!-- /.contenuChapeau -->\n";
	$chapeau .= "</div><!-- /.chapeau -->\n";
	
	return $chapeau;
}

/*
Retourne le chemin vers un fichier de configuration des abonnements aux commentaires d'une URL. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigAbonnementsCommentaires($cheminConfigCommentaires)
{
	$nomFichierConfig = superBasename($cheminConfigCommentaires);
	$dossierFichierConfig = dirname($cheminConfigCommentaires);
	
	return "$dossierFichierConfig/abonnements-$nomFichierConfig";
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
Retourne le chemin vers le fichier de configuration des commentaires de l'URL donnée. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigCommentaires($racine, $urlRacine, $url, $retourneCheminParDefaut = FALSE)
{
	$urlPourCheminConfigCommentaires = supprimeUrlRacine($urlRacine, $url);
	
	if (strpos($urlPourCheminConfigCommentaires, 'galerie.php?') === 0)
	{
		$urlPourCheminConfigCommentaires = variableGet(0, $urlPourCheminConfigCommentaires, 'langue');
	}
	
	$urlPourCheminConfigCommentaires = variableGet(0, $urlPourCheminConfigCommentaires, 'action');
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlPourCheminConfigCommentaires);
	$nomFichierCache = superBasename($cheminFichierCache);
	$nomConfigCommentaires = preg_replace('/\.cache\.(html|xml)$/', '.ini.txt', $nomFichierCache);
	$cheminConfigCommentaires = "$racine/site/inc/commentaires/$nomConfigCommentaires";
	
	if (file_exists($cheminConfigCommentaires) || $retourneCheminParDefaut)
	{
		return $cheminConfigCommentaires;
	}
	
	return FALSE;
}

/*
Retourne le chemin vers le fichier de configuration du flux RSS global du site. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigFluxRssGlobalSite($racine, $retourneCheminParDefaut = FALSE)
{
	if (file_exists("$racine/site/inc/rss-site.ini.txt"))
	{
		return "$racine/site/inc/rss-site.ini.txt";
	}
	elseif (file_exists("$racine/site/inc/rss-site.ini"))
	{
		return "$racine/site/inc/rss-site.ini";
	}
	elseif ($retourneCheminParDefaut)
	{
		return "$racine/site/inc/rss-site.ini.txt";
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin vers le fichier de configuration d'une galerie. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigGalerie($racine, $idGalerieDossier, $retourneCheminParDefaut = FALSE)
{
	if ($idGalerieDossier == 'demo')
	{
		return $racine . '/fichiers/galeries/' . $idGalerieDossier . '/config.ini.txt';
	}
	elseif (!empty($idGalerieDossier) && file_exists($racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/config.ini.txt'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/config.ini.txt';
	}
	elseif (!empty($idGalerieDossier) && file_exists($racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/config.ini'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/config.ini';
	}
	elseif ($retourneCheminParDefaut)
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/config.ini.txt';
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin vers le fichier de configuration des galeries. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function cheminConfigGaleries($racine, $retourneCheminParDefaut = FALSE)
{
	if (file_exists("$racine/site/inc/galeries.ini.txt"))
	{
		return "$racine/site/inc/galeries.ini.txt";
	}
	elseif (file_exists("$racine/site/inc/galeries.ini"))
	{
		return "$racine/site/inc/galeries.ini";
	}
	elseif ($retourneCheminParDefaut)
	{
		return "$racine/site/inc/galeries.ini.txt";
	}
	else
	{
		return FALSE;
	}
}

/*
Retourne le chemin d'un fichier cache global.
*/
function cheminFichierCache($racine, $urlRacine, $nomBrut, $html = TRUE)
{
	$nomFichierCache = supprimeUrlRacine($urlRacine, $nomBrut);
	
	if (empty($nomFichierCache))
	{
		$nomFichierCache = 'index';
	}
	
	$nomFichierCache = encodeTexte($nomFichierCache, TRUE);
	$nomFichierCache .= '.cache';
	
	if ($html)
	{
		$nomFichierCache .= '.html';
	}
	else
	{
		$nomFichierCache .= '.xml';
	}
	
	return "$racine/site/cache/$nomFichierCache";
}

/*
Retourne le chemin d'un fichier cache global contenant les informations d'en-tête HTTP.
*/
function cheminFichierCacheEnTete($cheminFichierCache)
{
	$nomFichierCache = superBasename($cheminFichierCache) . '.txt';
	$dossierFichierCache = dirname($cheminFichierCache);
	
	return "$dossierFichierCache/en-tete-$nomFichierCache";
}

/*
Retourne le chemin d'un fichier cache partiel.
*/
function cheminFichierCachePartiel($racine, $urlRacine, $nomBrut)
{
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $nomBrut);
	$nomFichierCache = superBasename($cheminFichierCache);
	$dossierFichierCache = dirname($cheminFichierCache);
	
	return "$dossierFichierCache/partiel-$nomFichierCache";
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
Retourne les classes du bloc (`blocAvecFond` ou `blocArrondi` ou les deux). Si aucune classe ne s'applique, retourne une chaîne vide.
*/
function classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $bloc, $nombreDeColonnes)
{
	$classes = '';
	
	if ((isset($blocsAvecFondSpecifiques[$bloc][$nombreDeColonnes]) && $blocsAvecFondSpecifiques[$bloc][$nombreDeColonnes]) || (!isset($blocsAvecFondSpecifiques[$bloc][$nombreDeColonnes]) && $blocsAvecFondParDefaut))
	{
		$classes .= 'blocAvecFond ';
	}
	
	if ($blocsArrondis)
	{
		$classes .= 'blocArrondi';
	}
	
	return rtrim($classes);
}

/*
Retourne une liste de classes pour `body`.
*/
function classesBody($url, $estAccueil, $idCategorie, $idGalerie, $courrielContact, $listeCategoriesPage, $nombreDeColonnes, $uneColonneAgauche, $deuxColonnesSousContenuAgauche, $arrierePlanColonne, $margesPage, $borduresPage, $ombrePage, $enTetePleineLargeur, $differencierLiensVisitesHorsContenu, $tableDesMatieresAvecFond, $tableDesMatieresArrondie, $galerieAccueilJavascriptCouleurNavigation, $basDePageInterieurPage, $classesSupplementaires)
{
	$classesBody = '';
	$arrierePlanColonne = 'Avec' . ucfirst($arrierePlanColonne);
	
	if ($arrierePlanColonne == 'AvecAucun')
	{
		$arrierePlanColonne = 'SansArrierePlan';
	}
	
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
			$classesBody .= chaineVersClasseCss('image-' . $resultat[3]) . ' ';
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
			$classesBody .= chaineVersClasseCss("article-$categoriePage") . ' ';
		}
	}
	
	if (empty($idCategorie) && empty($idGalerie) && empty($courrielContact) && empty($listeCategoriesPage))
	{
		$classesBody .= 'pageStandardSansCategorie ';
	}
	
	if ($nombreDeColonnes == 2)
	{
		$classesBody .= 'deuxColonnes colonneAgauche colonneAdroite ';
		$classesBody .= "deuxColonnes$arrierePlanColonne ";
		
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
			$classesBody .= "colonneAgauche$arrierePlanColonne ";
		}
		else
		{
			$classesBody .= "colonneAdroite uneColonneAdroite ";
			$classesBody .= "colonneAdroite$arrierePlanColonne ";
		}
	}
	elseif ($nombreDeColonnes == 0)
	{
		$classesBody .= "aucuneColonne ";
	}
	
	if ($margesPage['haut'])
	{
		$classesBody .= 'margeHautPage ';
	}
	
	if ($margesPage['bas'])
	{
		$classesBody .= 'margeBasPage ';
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
	
	if ($borduresPage['haut'])
	{
		$classesBody .= 'bordureHautPage ';
	}
	
	if ($ombrePage)
	{
		$classesBody .= 'ombrePage ';
	}
	
	if ($enTetePleineLargeur && ($nombreDeColonnes == 1 || $nombreDeColonnes == 2))
	{
		$classesBody .= 'enTetePleineLargeur ';
	}
	
	if ($differencierLiensVisitesHorsContenu)
	{
		$classesBody .= 'liensVisitesDifferencies ';
	}
	
	if ($tableDesMatieresAvecFond)
	{
		$classesBody .= 'tableDesMatieresAvecFond ';
	}
	
	if ($tableDesMatieresArrondie)
	{
		$classesBody .= 'tableDesMatieresArrondie ';
	}
	
	if ($basDePageInterieurPage)
	{
		$classesBody .= 'basDePageInterieur ';
	}
	else
	{
		$classesBody .= 'basDePageExterieur ';
	}
	
	$urlAvecGetSansServeurAvecIndex = url(TRUE, FALSE, TRUE);
	$classesBody .= chaineVersClasseCss($urlAvecGetSansServeurAvecIndex) . ' ';
	
	if (dirname($urlAvecGetSansServeurAvecIndex) != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss(dirname($urlAvecGetSansServeurAvecIndex)) . ' ';
	}
	
	if (superBasename($urlAvecGetSansServeurAvecIndex) != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss(superBasename($urlAvecGetSansServeurAvecIndex)) . ' ';
	}
	
	$urlSansGetSansServeurAvecIndex = url(FALSE, FALSE, TRUE);
	
	if ($urlSansGetSansServeurAvecIndex != $urlAvecGetSansServeurAvecIndex)
	{
		$classesBody .= chaineVersClasseCss($urlSansGetSansServeurAvecIndex) . ' ';
		$classesBody .= chaineVersClasseCss(superBasename($urlSansGetSansServeurAvecIndex)) . ' ';
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
Retourne TRUE si le code 304 peut être envoyé, sinon retourne FALSE.
*/
function code304($cheminFichierCache)
{
	$code304 = FALSE;
	
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == @filemtime($cheminFichierCache))
	{
		$code304 = TRUE;
	}
	elseif (isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5(@filemtime($cheminFichierCache) . '-' . @filesize($cheminFichierCache)))
	{
		$code304 = TRUE;
	}
	
	return $code304;
}

/*
Retourne le nom complet d'une langue (ex.: «Français») à partir du code de langue (ex.: «fr»). Si aucun nom n'a été trouvé, retourne le code de langue.
*/
function codeLangueVersNom($codeLangue, $doctype, $traduireNom = TRUE)
{
	switch ($codeLangue)
	{
		case 'en':
			if ($traduireNom)
			{
				$nom = T_("Anglais");
			}
			else
			{
				$nom = '<span ' . attributLang($codeLangue, $doctype) . '>English</span>';
			}
			
			break;
			
		case 'fr':
			if ($traduireNom)
			{
				$nom = T_("Français");
			}
			else
			{
				$nom = '<span ' . attributLang($codeLangue, $doctype) . '>Français</span>';
			}
			
			break;
			
		default:
			$nom = "<code class=\"codeLangue\">$codeLangue</code>";
			break;
	}
	
	return $nom;
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
	$codeColore = str_replace('<code><span style="color: #000000">' . "\n", '<code><span style="color: #000000">', $codeColore);
	$codeColore = str_replace("</span>\n</code>", '</span></code>', $codeColore);
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
	$code = @file_get_contents($fichier);
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
function coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $nombreDeColonnes, $blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $nombreDeColonnes)
{
	if (preg_match('|(<div id="galerieIntermediaireTexte">.+</div><!-- /#galerieIntermediaireTexte -->)|s', $corpsGalerie, $resultat))
	{
		if ($galerieLegendeEmplacement[$nombreDeColonnes] == 'bloc')
		{
			$corpsGalerie = preg_replace('|<div id="galerieIntermediaireTexte">.+</div><!-- /#galerieIntermediaireTexte -->|s', '', $corpsGalerie);
			
			$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, 'legende-image-galerie', $nombreDeColonnes);
			
			$tableauCorpsGalerie['texteIntermediaire'] = '<div id="galerieIntermediaireTexteHorsContenu" class="bloc ' . $classesBloc . '">' . "\n<h2>" . T_("Légende de l'image") . "</h2>\n" . $resultat[1] . '</div><!-- /#galerieIntermediaireTexteHorsContenu -->' . "\n";
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
	if (preg_match('|(<div id="galerieIntermediaireTexte"></div><!-- /#galerieIntermediaireTexte -->)|', $tableauCorpsGalerie['corpsGalerie']))
	{
		$tableauCorpsGalerie['corpsGalerie'] = preg_replace('|<div id="galerieIntermediaireTexte"></div><!-- /#galerieIntermediaireTexte -->|', '', $tableauCorpsGalerie['corpsGalerie']);
	}
	
	return $tableauCorpsGalerie;
}

/*
Envoie un courriel. Retourne TRUE si le courriel a été envoyé, sinon retourne FALSE.

Le tableau en paramètre peut contenir les informations suivantes:

  - `$infos['From']` (obligatoire; s'il y a lieu, encoder les noms);
  - `$infos['ReplyTo']` (optionnel; s'il y a lieu, encoder les noms);
  - `$infos['Bcc']` (optionnel; s'il y a lieu, encoder les noms);
  - `$infos['format']` (optionnel): le format texte (`plain`) est celui par défaut. Peut valoir également `html`;
  - `$infos['destinataire']` (obligatoire; s'il y a lieu, encoder les noms);
  - `$infos['objet']` (obligatoire; ne pas encoder);
  - `$infos['message']` (obligatoire; si le format est HTML, fournir seulement le corps à l'intérieur de `body`);
*/
function courriel($infos)
{
	if (!isset($infos['From']) || !isset($infos['destinataire']) || !isset($infos['objet']) || !isset($infos['message']))
	{
		return FALSE;
	}
	else
	{
		$infos['objet'] = encodeInfoEnTeteCourriel($infos['objet']);
		$infos['message'] = str_replace(array ("\r\n", "\n\r", "\r"), "\n", $infos['message']);
		$enTete = '';
		
		if (!empty($infos['From']))
		{
			$enTete .= "From: {$infos['From']}\r\n";
		}
		
		if (!empty($infos['ReplyTo']))
		{
			$enTete .= "Reply-to: {$infos['ReplyTo']}\r\n";
		}
		
		if (!empty($infos['Bcc']))
		{
			$enTete .= "Bcc: {$infos['Bcc']}\r\n";
		}
		
		$enTete .= "MIME-Version: 1.0\r\n";
		
		if (isset($infos['format']) && $infos['format'] == 'html')
		{
			$format = 'html';
			$infos['message'] = "<html>\n<head>\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n</head>\n<body>\n" . $infos['message'] . "</body>\n</html>";
		}
		else
		{
			$format = 'plain';
		}
		
		$enTete .= "Content-Type: text/$format; charset=\"utf-8\"\r\n";
		$enTete .= "X-Mailer: Squeletml\r\n";
		
		return @mail($infos['destinataire'], $infos['objet'], $infos['message'], $enTete);
	}
}

/*
Retourne TRUE si l'adresse courriel a une forme valide, sinon retourne FALSE.
*/
function courrielValide($courriel)
{
	return preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i", $courriel);
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
function cronUrlCategorie($racine, $urlRacine, $categorie, $idCategorie, $nombreArticlesParPageCategorie)
{
	$tableauUrl = array ();
	
	if ($nombreArticlesParPageCategorie && isset($categorie['pages']))
	{
		$nombreArticles = count($categorie['pages']);
		$nombreDePages = ceil($nombreArticles / $nombreArticlesParPageCategorie);
	}
	else
	{
		$nombreDePages = 1;
	}
	
	$categorie['url'] = urlCat($categorie, $idCategorie);
	$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $categorie['url']);
	$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
$tableauUrl[] = array ('url' => $urlRacine . '/' . $categorie['url'], 'cache' => $cheminFichierCache, 'cacheEnTete' => $cheminFichierCacheEnTete);
	
	if ($nombreDePages > 1)
	{
		for ($i = 2; $i <= $nombreDePages; $i++)
		{
			$adresse = variableGet(2, $urlRacine . '/' . $categorie['url'], 'page', $i);
			$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $adresse);
			$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
			$tableauUrl[] = array ('url' => $adresse, 'cache' => $cheminFichierCache, 'cacheEnTete' => $cheminFichierCacheEnTete);
		}
	}
	
	return $tableauUrl;
}

/*
Retourne un tableau d'URL à visiter par le cron pour une galerie donnée.
*/
function cronUrlGalerie($racine, $urlRacine, $galerieVignettesParPage, $infosGalerie)
{
	$tableauUrl = array ();
	
	if (!empty($infosGalerie['dossier']) && !empty($infosGalerie['url']) && cheminConfigGalerie($racine, $infosGalerie['dossier']))
	{
		$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $infosGalerie['dossier']), TRUE);
		
		if ($galerieVignettesParPage)
		{
			$nombreDimages = count($tableauGalerie);
			$nombreDePages = ceil($nombreDimages / $galerieVignettesParPage);
		}
		else
		{
			$nombreDePages = 1;
		}
		
		$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $infosGalerie['url']);
		$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
		$tableauUrl[] = array ('url' => $urlRacine . '/' . $infosGalerie['url'], 'cache' => $cheminFichierCache, 'cacheEnTete' => $cheminFichierCacheEnTete);
		
		if ($nombreDePages > 1)
		{
			for ($i = 2; $i <= $nombreDePages; $i++)
			{
				$adresse = variableGet(2, $urlRacine . '/' . $infosGalerie['url'], 'page', $i);
				$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $adresse);
				$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
				$tableauUrl[] = array ('url' => $adresse, 'cache' => $cheminFichierCache, 'cacheEnTete' => $cheminFichierCacheEnTete);
			}
		}
		
		foreach ($tableauGalerie as $image)
		{
			$adresse = variableGet(2, $urlRacine . '/' . $infosGalerie['url'], 'image', idImage($image));
			$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $adresse);
			$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
			$tableauUrl[] = array ('url' => $adresse, 'cache' => $cheminFichierCache, 'cacheEnTete' => $cheminFichierCacheEnTete);
		}
	}
	
	return $tableauUrl;
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
Décode le texte fourni.

Fonction inverse de `encodeTexte()`.
*/
function decodeTexte($texte)
{
	if (is_array($texte))
	{
		return array_map('decodeTexte', $texte);
	}
	elseif (is_int($texte) || is_string($texte))
	{
		return rawurldecode($texte);
	}
	
	return '';
}

/*
Fonction inverse de `encodeTexteGet()`.
*/
function decodeTexteGet($texte)
{
	return base64_decode(strtr($texte, '-_,', '+/='));
}

/*
Convertit la description en tableau d'une galerie au format texte affichable dans une page HTML, et retourne le résultat.
*/
function descriptionGalerieTableauVersTexte($tableauDescriptionGalerie)
{
	$descriptionGalerie = '';
	
	foreach ($tableauDescriptionGalerie as $ligneDescriptionGalerie)
	{
		$descriptionGalerie .= "$ligneDescriptionGalerie\n";
	}
	
	return "<div class=\"descriptionGalerie\">\n$descriptionGalerie</div><!-- /.descriptionGalerie -->\n";
}

/*
Fonction opposée à `securiseTexte()`. Si la valeur passée en paramètre est une chaîne de caractères, retourne la chaîne traitée pour que les entités HTML spéciales soient converties en caractères, sinon si la valeur passée en paramètre est un tableau, retourne un tableau dont chaque élément a été désécurisé, sinon si la valeur passée en paramètre n'est ni une chaîne ni un tableau, retourne une chaîne vide.
*/
function desecuriseTexte($texte)
{
	if (is_array($texte))
	{
		return array_map('desecuriseTexte', $texte);
	}
	elseif (is_int($texte) || is_string($texte))
	{
		return htmlspecialchars_decode($texte, ENT_COMPAT);
	}
	
	return '';
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
			
		case 'HTML 5':
			return array ("<!DOCTYPE html>\n", "<html lang=\"$langue\">\n");
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
S'il y a lieu, encode l'information d'en-tête de courriel. Retourne le résultat.
*/
function encodeInfoEnTeteCourriel($info)
{
	if (!estAsciiImprimable($info))
	{
		$info = '=?UTF-8?B?' . base64_encode($info) . '?=';
	}
	
	return $info;
}

/*
Encode le texte fourni.

La fonction inverse est `decodeTexte()`.
*/
function encodeTexte($texte, $encoderBarreOblique = FALSE)
{
	if (is_array($texte))
	{
		return array_map('encodeTexte', $texte);
	}
	elseif (is_int($texte) || is_string($texte))
	{
		$texte = rawurlencode($texte);
		
		if (!$encoderBarreOblique)
		{
			$texte = str_replace('%2F', '/', $texte);
		}
		
		return $texte;
	}
	
	return '';
}

/*
Encode le texte fourni pour pouvoir l'utiliser sans problème comme valeur d'un paramètre `GET` dans une URL. Par exemple, un nom de fichier `a%3Fz.txt` serait encodé `a%253Fz.txt` par la fonction `encodeTexte()`. Passé par paramètre `GET` dans une URL, la valeur récupérée serait `a%3Fz.txt` puisque la chaîne `%25` aurait été interprétée durant le processus. Après utilisation de la fonction `decodeTexte()`, le résultat obtenu serait `a?z.txt`, donc différent de la valeur de départ. Ce problème n'existe pas avec la fonction `encodeTexteGet()`.

La fonction inverse est `decodeTexteGet()`.
*/
function encodeTexteGet($texte)
{
	return strtr(base64_encode($texte), '+/=', '-_,');
}

/*
Retourne les en-têtes relatives au cache.
*/
function enTetesCache($cheminFichierCache, $dureeCache)
{
	$enTetesCache = 'header("Expires: ' . gmdate("D, d M Y H:i:s \G\M\T", time() + $dureeCache) . '");';
	$enTetesCache .= 'header("Cache-Control: max-age=' . $dureeCache . '");';
	
	$dateFichierCache = @filemtime($cheminFichierCache);
	$tailleFichierCache = @filesize($cheminFichierCache);
	
	if ($dateFichierCache !== FALSE)
	{
		$enTetesCache .= 'header("Last-Modified: ' . gmdate("D, d M Y H:i:s \G\M\T", $dateFichierCache) . '");';
	}
	
	if ($dateFichierCache !== FALSE && $tailleFichierCache !== FALSE)
	{
		$enTetesCache .= 'header(\'ETag: "' . md5("$dateFichierCache-$tailleFichierCache") . '"\');';
	}
	
	return $enTetesCache;
}

/*
Retourne TRUE si la page est l'accueil, sinon retourne FALSE.
*/
function estAccueil($accueil)
{
	$url = url();
	
	if ($url == $accueil . '/')
	{
		return TRUE;
	}
	else
	{
		$listeIndex = listeFichiersIndex();
		
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
Retourne `TRUE` si la chaîne ne contient que des caractères ASCII imprimables, sinon retourne `FALSE`.
*/
function estAsciiImprimable($chaine)
{
	return preg_match('/^[\x20-\x7e]*$/D', $chaine);
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
Filtre une chaîne de caractères pour ne conserver que des caractères non accentués et certains autres caractères. Retourne la chaîne filtrée.
*/
function filtreChaine($chaine, $casse = '', $filtrerBarreOblique = TRUE)
{
	// Le contenu du tableau `$transliteration` provient du fichier `i18n-ascii.txt` du module Pathauto pour Drupal, sous licence GPL. Voir <http://drupal.org/project/pathauto>.
	$transliteration = array (
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'A',
		'Æ' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Ç' => 'C', 'Ć' => 'C',
		'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D', 'È' => 'E',
		'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ě' => 'E',
		'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
		'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'İ' => 'I', 'Ĳ' => 'IJ',
		'Ĵ' => 'J', 'Ķ' => 'K', 'Ľ' => 'K', 'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K',
		'Ł' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N',
		'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe', 'Ø' => 'O',
		'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O', 'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R',
		'Ŗ' => 'R', 'Ś' => 'S', 'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Š' => 'S',
		'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T', 'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U',
		'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U',
		'Ũ' => 'U', 'Ų' => 'U', 'Ŵ' => 'W', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ý' => 'Y',
		'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z', 'à' => 'a', 'á' => 'a', 'â' => 'a',
		'ã' => 'a', 'ä' => 'ae', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a', 'å' => 'a',
		'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
		'ď' => 'd', 'đ' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
		'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ƒ' => 'f',
		'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h', 'ħ' => 'h',
		'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'ĩ' => 'i',
		'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j', 'ķ' => 'k',
		'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l', 'ŀ' => 'l',
		'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n', 'ŋ' => 'n',
		'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ø' => 'o',
		'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe', 'ŕ' => 'r', 'ř' => 'r',
		'ŗ' => 'r', 'ś' => 's', 'š' => 's', 'ş' => 's', 'ť' => 't', 'ţ' => 't',
		'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue', 'ū' => 'u', 'ů' => 'u',
		'ű' => 'u', 'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ÿ' => 'y',
		'ý' => 'y', 'ŷ' => 'y', 'ż' => 'z', 'ź' => 'z', 'ž' => 'z', 'ß' => 'ss',
		'ſ' => 'ss', 'Α' => 'A', 'Ά' => 'A', 'Ἀ' => 'A', 'Ἁ' => 'A', 'Ἂ' => 'A',
		'Ἃ' => 'A', 'Ἄ' => 'A', 'Ἅ' => 'A', 'Ἆ' => 'A', 'Ἇ' => 'A', 'ᾈ' => 'A',
		'ᾉ' => 'A', 'ᾊ' => 'A', 'ᾋ' => 'A', 'ᾌ' => 'A', 'ᾍ' => 'A', 'ᾎ' => 'A',
		'ᾏ' => 'A', 'Ᾰ' => 'A', 'Ᾱ' => 'A', 'Ὰ' => 'A', 'Ά' => 'A', 'ᾼ' => 'A',
		'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Έ' => 'E', 'Ἐ' => 'E',
		'Ἑ' => 'E', 'Ἒ' => 'E', 'Ἓ' => 'E', 'Ἔ' => 'E', 'Ἕ' => 'E', 'Έ' => 'E',
		'Ὲ' => 'E', 'Ζ' => 'Z', 'Η' => 'I', 'Ή' => 'I', 'Ἠ' => 'I', 'Ἡ' => 'I',
		'Ἢ' => 'I', 'Ἣ' => 'I', 'Ἤ' => 'I', 'Ἥ' => 'I', 'Ἦ' => 'I', 'Ἧ' => 'I',
		'ᾘ' => 'I', 'ᾙ' => 'I', 'ᾚ' => 'I', 'ᾛ' => 'I', 'ᾜ' => 'I', 'ᾝ' => 'I',
		'ᾞ' => 'I', 'ᾟ' => 'I', 'Ὴ' => 'I', 'Ή' => 'I', 'ῌ' => 'I', 'Θ' => 'TH',
		'Ι' => 'I', 'Ί' => 'I', 'Ϊ' => 'I', 'Ἰ' => 'I', 'Ἱ' => 'I', 'Ἲ' => 'I',
		'Ἳ' => 'I', 'Ἴ' => 'I', 'Ἵ' => 'I', 'Ἶ' => 'I', 'Ἷ' => 'I', 'Ῐ' => 'I',
		'Ῑ' => 'I', 'Ὶ' => 'I', 'Ί' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M',
		'Ν' => 'N', 'Ξ' => 'KS', 'Ο' => 'O', 'Ό' => 'O', 'Ὀ' => 'O', 'Ὁ' => 'O',
		'Ὂ' => 'O', 'Ὃ' => 'O', 'Ὄ' => 'O', 'Ὅ' => 'O', 'Ὸ' => 'O', 'Ό' => 'O',
		'Π' => 'P', 'Ρ' => 'R', 'Ῥ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y',
		'Ύ' => 'Y', 'Ϋ' => 'Y', 'Ὑ' => 'Y', 'Ὓ' => 'Y', 'Ὕ' => 'Y', 'Ὗ' => 'Y',
		'Ῠ' => 'Y', 'Ῡ' => 'Y', 'Ὺ' => 'Y', 'Ύ' => 'Y', 'Φ' => 'F', 'Χ' => 'X',
		'Ψ' => 'PS', 'Ω' => 'O', 'Ώ' => 'O', 'Ὠ' => 'O', 'Ὡ' => 'O', 'Ὢ' => 'O',
		'Ὣ' => 'O', 'Ὤ' => 'O', 'Ὥ' => 'O', 'Ὦ' => 'O', 'Ὧ' => 'O', 'ᾨ' => 'O',
		'ᾩ' => 'O', 'ᾪ' => 'O', 'ᾫ' => 'O', 'ᾬ' => 'O', 'ᾭ' => 'O', 'ᾮ' => 'O',
		'ᾯ' => 'O', 'Ὼ' => 'O', 'Ώ' => 'O', 'ῼ' => 'O', 'α' => 'a', 'ά' => 'a',
		'ἀ' => 'a', 'ἁ' => 'a', 'ἂ' => 'a', 'ἃ' => 'a', 'ἄ' => 'a', 'ἅ' => 'a',
		'ἆ' => 'a', 'ἇ' => 'a', 'ᾀ' => 'a', 'ᾁ' => 'a', 'ᾂ' => 'a', 'ᾃ' => 'a',
		'ᾄ' => 'a', 'ᾅ' => 'a', 'ᾆ' => 'a', 'ᾇ' => 'a', 'ὰ' => 'a', 'ά' => 'a',
		'ᾰ' => 'a', 'ᾱ' => 'a', 'ᾲ' => 'a', 'ᾳ' => 'a', 'ᾴ' => 'a', 'ᾶ' => 'a',
		'ᾷ' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'έ' => 'e',
		'ἐ' => 'e', 'ἑ' => 'e', 'ἒ' => 'e', 'ἓ' => 'e', 'ἔ' => 'e', 'ἕ' => 'e',
		'ὲ' => 'e', 'έ' => 'e', 'ζ' => 'z', 'η' => 'i', 'ή' => 'i', 'ἠ' => 'i',
		'ἡ' => 'i', 'ἢ' => 'i', 'ἣ' => 'i', 'ἤ' => 'i', 'ἥ' => 'i', 'ἦ' => 'i',
		'ἧ' => 'i', 'ᾐ' => 'i', 'ᾑ' => 'i', 'ᾒ' => 'i', 'ᾓ' => 'i', 'ᾔ' => 'i',
		'ᾕ' => 'i', 'ᾖ' => 'i', 'ᾗ' => 'i', 'ὴ' => 'i', 'ή' => 'i', 'ῂ' => 'i',
		'ῃ' => 'i', 'ῄ' => 'i', 'ῆ' => 'i', 'ῇ' => 'i', 'θ' => 'th', 'ι' => 'i',
		'ί' => 'i', 'ϊ' => 'i', 'ΐ' => 'i', 'ἰ' => 'i', 'ἱ' => 'i', 'ἲ' => 'i',
		'ἳ' => 'i', 'ἴ' => 'i', 'ἵ' => 'i', 'ἶ' => 'i', 'ἷ' => 'i', 'ὶ' => 'i',
		'ί' => 'i', 'ῐ' => 'i', 'ῑ' => 'i', 'ῒ' => 'i', 'ΐ' => 'i', 'ῖ' => 'i',
		'ῗ' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'ks',
		'ο' => 'o', 'ό' => 'o', 'ὀ' => 'o', 'ὁ' => 'o', 'ὂ' => 'o', 'ὃ' => 'o',
		'ὄ' => 'o', 'ὅ' => 'o', 'ὸ' => 'o', 'ό' => 'o', 'π' => 'p', 'ρ' => 'r',
		'ῤ' => 'r', 'ῥ' => 'r', 'σ' => 's', 'ς' => 's', 'τ' => 't', 'υ' => 'y',
		'ύ' => 'y', 'ϋ' => 'y', 'ΰ' => 'y', 'ὐ' => 'y', 'ὑ' => 'y', 'ὒ' => 'y',
		'ὓ' => 'y', 'ὔ' => 'y', 'ὕ' => 'y', 'ὖ' => 'y', 'ὗ' => 'y', 'ὺ' => 'y',
		'ύ' => 'y', 'ῠ' => 'y', 'ῡ' => 'y', 'ῢ' => 'y', 'ΰ' => 'y', 'ῦ' => 'y',
		'ῧ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'o', 'ώ' => 'o',
		'ὠ' => 'o', 'ὡ' => 'o', 'ὢ' => 'o', 'ὣ' => 'o', 'ὤ' => 'o', 'ὥ' => 'o',
		'ὦ' => 'o', 'ὧ' => 'o', 'ᾠ' => 'o', 'ᾡ' => 'o', 'ᾢ' => 'o', 'ᾣ' => 'o',
		'ᾤ' => 'o', 'ᾥ' => 'o', 'ᾦ' => 'o', 'ᾧ' => 'o', 'ὼ' => 'o', 'ώ' => 'o',
		'ῲ' => 'o', 'ῳ' => 'o', 'ῴ' => 'o', 'ῶ' => 'o', 'ῷ' => 'o', '¨' => '',
		'΅' => '', '᾿' => '', '῾' => '', '῍' => '', '῝' => '', '῎' => '',
		'῞' => '', '῏' => '', '῟' => '', '῀' => '', '῁' => '', '΄' => '',
		'΅' => '', '`' => '', '῭' => '', 'ͺ' => '', '᾽' => '', 'А' => 'A',
		'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E',
		'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'I', 'К' => 'K', 'Л' => 'L',
		'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S',
		'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'TS', 'Ч' => 'CH',
		'Ш' => 'SH', 'Щ' => 'SHCH', 'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
		'а' => 'A', 'б' => 'B', 'в' => 'V', 'г' => 'G', 'д' => 'D', 'е' => 'E',
		'ё' => 'E', 'ж' => 'ZH', 'з' => 'Z', 'и' => 'I', 'й' => 'I', 'к' => 'K',
		'л' => 'L', 'м' => 'M', 'н' => 'N', 'о' => 'O', 'п' => 'P', 'р' => 'R',
		'с' => 'S', 'т' => 'T', 'у' => 'U', 'ф' => 'F', 'х' => 'KH', 'ц' => 'TS',
		'ч' => 'CH', 'ш' => 'SH', 'щ' => 'SHCH', 'ы' => 'Y', 'э' => 'E', 'ю' => 'YU',
		'я' => 'YA', 'Ъ' => '', 'ъ' => '', 'Ь' => '', 'ь' => '', 'ð' => 'd',
		'Ð' => 'D', 'þ' => 'th', 'Þ' => 'TH',
	);
	
	$chaine = strtr($chaine, $transliteration);
	
	if ($filtrerBarreOblique)
	{
		$chaine = preg_replace('/[^-A-Za-z0-9._\+]/', '-', $chaine);
	}
	else
	{
		$chaine = preg_replace('#[^-A-Za-z0-9._\+/]#', '-', $chaine);
	}
	
	$chaine = preg_replace('/-+/', '-', $chaine);
	$chaine = str_replace('-.', '.', $chaine);
	$chaine = str_replace('.-', '-', $chaine);
	$chaine = preg_replace('/-$/', '', $chaine);
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
Retourne le contenu d'un fichier RSS.
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
		$contenuRss .= "\t\t<title>" . sprintf(T_("Galerie %1\$s"), securiseTexte($idGalerie) . $baliseTitleComplement) . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Derniers ajouts à la galerie «%1\$s»"), securiseTexte($idGalerie)) . "</description>\n\n";
	}
	// Catégorie.
	elseif ($type == 'categorie')
	{
		$contenuRss .= "\t\t<title>" . sprintf(T_("Dernières publications dans la catégorie «%1\$s»"), securiseTexte($idCategorie)) . $baliseTitleComplement . "</title>\n";
		$contenuRss .= "\t\t<link>" . $url . "</link>\n";
		$contenuRss .= "\t\t<description>" . sprintf(T_("Dernières publications dans la catégorie «%1\$s»"), securiseTexte($idCategorie)) . $baliseTitleComplement . "</description>\n\n";
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
function fluxRssGalerieTableauBrut($racine, $urlRacine, $langue, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown)
{
	$idGalerieDossier = idGalerieDossier($racine, $idGalerie);
	$urlGalerie = urlGalerie(0, $racine, $urlRacine, $idGalerie, $langue);
	$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerieDossier), TRUE);
	$itemsFluxRss = array ();
	
	if ($tableauGalerie !== FALSE)
	{
		foreach ($tableauGalerie as $image)
		{
			$titreImage = titreImage($image);
			$title = sprintf(T_("%1\$s – Galerie %2\$s"), securiseTexte($titreImage), securiseTexte($idGalerie));
			$cheminImage = "$racine/site/fichiers/galeries/$idGalerieDossier/" . $image['intermediaireNom'];
			$urlImage = "$urlRacine/site/fichiers/galeries/" . encodeTexte($idGalerieDossier . '/' . $image['intermediaireNom']);
			$urlGalerieImage = variableGet(2, $urlGalerie, 'image', idImage($image));
			
			if (!empty($image['intermediaireLargeur']))
			{
				$width = securiseTexte($image['intermediaireLargeur']);
			}
			else
			{
				list ($width, $height) = @getimagesize($cheminImage);
			}
		
			if (!empty($image['intermediaireHauteur']))
			{
				$height = securiseTexte($image['intermediaireHauteur']);
			}
		
			if (!empty($image['intermediaireAlt']))
			{
				$alt = securiseTexte($image['intermediaireAlt']);
			}
			else
			{
				$alt = sprintf(T_("Image %1\$s"), securiseTexte($titreImage));
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
			
			$cheminOriginal = "$racine/site/fichiers/galeries/$idGalerieDossier/$nomOriginal";
			
			if (file_exists($cheminOriginal))
			{
				$urlOriginal = "site/fichiers/galeries/$idGalerieDossier/$nomOriginal";
				
				if ($galerieLienOriginalTelecharger)
				{
					$urlOriginal = "$urlRacine/telecharger.php?fichier=" . encodeTexteGet($urlOriginal);
					$msgOriginal = "<li><a href=\"$urlOriginal\">" . sprintf(T_("Télécharger l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . extension($nomOriginal) . '</em>', octetsVersKio(@filesize($cheminOriginal))) . "</a></li>\n";
				}
				else
				{
					$urlOriginal = $urlRacine . '/' . encodeTexte($urlOriginal);
					$msgOriginal = "<li><a href=\"$urlOriginal\">" . sprintf(T_("Voir l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . extension($nomOriginal) . '</em>', octetsVersKio(@filesize($cheminOriginal))) . "</a></li>\n";
				}
			}
			else
			{
				$msgOriginal = '';
			}
			
			if (!empty($image['auteurAjout']))
			{
				$dccreator = securiseTexte($image['auteurAjout']);
			}
			elseif ($galerieFluxRssAuteurEstAuteurParDefaut)
			{
				$dccreator = securiseTexte($auteurParDefaut);
			}
			else
			{
				$dccreator = '';
			}
		
			if (!empty($image['dateAjout']))
			{
				$pubDate = securiseTexte($image['dateAjout']);
			}
			else
			{
				$pubDate = date('Y-m-d H:i', @filemtime($cheminImage));
			}
			
			$description = '';
			$description .= "<p><img src=\"$urlImage\" width=\"$width\" height=\"$height\" alt=\"$alt\" /></p>\n";
			
			if (!empty($image['intermediaireLegende']))
			{
				$description .= '<div>' . intermediaireLegende($image['intermediaireLegende'], $galerieLegendeMarkdown) . "</div>\n";
			}
			
			$msgPagePresentation = "<li><a href=\"$urlGalerieImage\">" . sprintf(T_("Consulter la page de présentation de l'image %1\$s dans la galerie %2\$s."), '<em>' . securiseTexte($titreImage) . '</em>', "<em>$idGalerie</em>") . "</a></li>\n";
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
Retourne un tableau listant les images de toutes les galeries dont le flux RSS est activé.

Voir la fonction `fluxRssGalerieTableauBrut()` pour plus de détails.
*/
function fluxRssGaleriesTableauBrut($racine, $urlRacine, $langue, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown)
{
	$itemsFluxRss = array ();
	$listeGaleriesRss = fluxRssGlobalGaleries($racine);
	
	if (!empty($listeGaleriesRss))
	{
		foreach ($listeGaleriesRss as $idGalerie)
		{
			$itemsFluxRss = array_merge($itemsFluxRss, fluxRssGalerieTableauBrut($racine, $urlRacine, $langue, $idGalerie, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown));
		}
	}
	
	return $itemsFluxRss;
}

/*
Retourne la liste des galeries dont le flux RSS est activé. Si aucune galerie n'a été trouvée, retourne un tableau vide.
*/
function fluxRssGlobalGaleries($racine)
{
	$listeGaleriesRss = array ();
	$galeries = super_parse_ini_file(cheminConfigGaleries($racine), TRUE);
	
	if (!empty($galeries))
	{
		foreach ($galeries as $idGalerie => $infosGalerie)
		{
			if (!empty($infosGalerie['rss']) && $infosGalerie['rss'] == 1)
			{
				$listeGaleriesRss[] = $idGalerie;
			}
		}
	}
	
	return $listeGaleriesRss;
}

/*
Retourne un tableau d'un élément représentant une page du site, cet élément étant lui-même un tableau contenant les informations nécessaires à la création d'un fichier RSS. Si une erreur survient, retourne un tableau vide.
*/
function fluxRssPageTableauBrut($racine, $urlRacine, $cheminPage, $urlPage, $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCache, $estPageCron)
{
	$itemFlux = array ();
	$infosPage = infosPage($racine, $urlRacine, $urlPage, $fluxRssAvecApercu, $tailleApercuAutomatique, $dureeCache, TRUE, $estPageCron);
	
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
			$pubDate = date('Y-m-d H:i', @filemtime($cheminPage));
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
Retourne `TRUE` si le formulaire précisé a déjà été envoyé, sinon retourne `FALSE`.
*/
function formulaireDejaEnvoye($racine, $idFormulaire)
{
	$fichierConfig = "$racine/site/cache/formulaires-envoyes.ini.txt";
	$formulairesEnvoyes = super_parse_ini_file($fichierConfig, TRUE);
	
	if (is_array($formulairesEnvoyes) && isset($formulairesEnvoyes['formulaires']['id']) && is_array($formulairesEnvoyes['formulaires']['id']) && in_array($idFormulaire, $formulairesEnvoyes['formulaires']['id']))
	{
		return TRUE;
	}
	
	return FALSE;
}

/*
Fusionne plusieurs fichiers CSS ou Javascript en un seul, et crée le fichier résultant dans le dossier de cache.

Retourne un tableau de balises brutes à inclure, utilisable par la fonction `linkScript()`.
*/
function fusionneCssJs($racine, $urlRacine, $dossierAdmin, $type, $extensionNomCache, $listeFichiers, $balisesBrutesTypeAinclure, $balisesBrutesFusionneesAinclure)
{
	if (!empty($listeFichiers))
	{
		$nomCache = $type . '-' . crc32(implode("\n", $listeFichiers)) . '.cache.' . $extensionNomCache;
		
		if (!empty($dossierAdmin))
		{
			$cheminCache = "$racine/site/$dossierAdmin/cache/$nomCache";
			$urlCache = "$urlRacine/site/$dossierAdmin/cache/$nomCache";
		}
		else
		{
			$cheminCache = "$racine/site/cache/$nomCache";
			$urlCache = "$urlRacine/site/cache/$nomCache";
		}
		
		if (!file_exists($cheminCache))
		{
			$contenuCache = '';
			
			foreach ($listeFichiers as $fichier)
			{
				if (strpos($fichier, $urlRacine) === 0)
				{
					$contenuFichier = @file_get_contents(preg_replace('/^' . preg_quote($urlRacine, '/') . '/', $racine, $fichier));
					
					// Ajustement des chemins relatifs dans les feuilles de style.
					if (strpos($type, 'css') === 0 && (strpos($fichier, "$urlRacine/css/") === 0 || (!empty($dossierAdmin) && strpos($fichier, "$urlRacine/$dossierAdmin/css/") === 0)))
					{
						$contenuFichier = preg_replace('#(\.\./)+#', '$1../', $contenuFichier);
					}
				}
				else
				{
					$contenuFichier = @file_get_contents($fichier);
				}
				
				if ($contenuFichier !== FALSE)
				{
					$enTete = '/* Fichier `' . superBasename($fichier) . "`. */\n\n";
					$contenuCache .= $enTete . $contenuFichier . "\n";
				}
			}
			
			if (!empty($contenuCache))
			{
				@file_put_contents($cheminCache, $contenuCache);
			}
		}
		
		if (file_exists($cheminCache))
		{
			array_unshift($balisesBrutesFusionneesAinclure, "$type#$urlCache");
		}
		else
		{
			$balisesBrutesFusionneesAinclure = array_merge($balisesBrutesTypeAinclure, $balisesBrutesFusionneesAinclure);
		}
	}
	
	return $balisesBrutesFusionneesAinclure;
}

/*
Génère le code HTML pour afficher toutes les galeries listées dans le tableau `$listeGaleries`.
*/
function galeries($racine, $urlRacine, $langue, $listeGaleries, $galerieAncreDeNavigation, $navigationJavascript = TRUE, $niveauTitre = 2)
{
	$contenu = "<div class=\"galeries\">\n";
	
	foreach ($listeGaleries as $idGalerie)
	{
		$idGalerieDossier = idGalerieDossier($racine, $idGalerie);
		$cheminConfigGalerie = cheminConfigGalerie($racine, $idGalerieDossier);
		$tableauGalerie = tableauGalerie($cheminConfigGalerie, TRUE);
		
		if (!empty($tableauGalerie))
		{
			$contenu .= "<div class=\"galerieDansListe\">\n";
			$contenu .= "<h$niveauTitre>" . securiseTexte($idGalerie) . "</h$niveauTitre>\n";
			$contenu .= "<ul class=\"galerieListeImages\">\n";
			$relLien = 'lightbox-galerie-' . chaineVersClasseCss($idGalerie);
			
			foreach($tableauGalerie as $image)
			{
				if (!empty($image['vignetteNom']))
				{
					$vignetteNom = $image['vignetteNom'];
				}
				else
				{
					$vignetteNom = nomSuffixe($image['intermediaireNom'], '-vignette');
				}
				
				$cheminVignette = $racine;
				$urlVignette = $urlRacine;
				
				if ($idGalerieDossier != 'demo')
				{
					$cheminVignette .= '/site';
					$urlVignette .= '/site';
				}
				
				$cheminVignette .= '/fichiers/galeries/' . $idGalerieDossier . '/' . $vignetteNom;
				$urlVignette .= '/fichiers/galeries/' . encodeTexte($idGalerieDossier);
				$urlSourceImage = $urlVignette;
				$urlVignette .= '/' . encodeTexte($vignetteNom);
				list ($largeurImage, $hauteurImage) = @getimagesize($cheminVignette);
				$titreImage = titreImage($image);
				
				if (!empty($image['vignetteAlt']))
				{
					$altImage = securiseTexte($image['vignetteAlt']);
				}
				else
				{
					$altImage = sprintf(T_("Image %1\$s"), securiseTexte($titreImage));
				}
				
				$ancre = ancreDeNavigationGalerie($galerieAncreDeNavigation);
				$urlGalerie = urlGalerie(0, $racine, $urlRacine, $idGalerie, $langue);
				$urlPageIndividuelleImage = variableGet(1, $urlGalerie, 'image', idImage($image)) . $ancre;
				
				if ($navigationJavascript)
				{
					$titleLien = '<a href="' . $urlPageIndividuelleImage . '">' . T_("Partager cette image ou voir plus d'information.") . '</a>';
					
					if (!empty($image['intermediaireLegende']))
					{
						$titleLien = $image['intermediaireLegende'] . '<br />' . $titleLien;
					}
					
					$titleLien = str_replace(array ('<', '>', '"'), array ('&lt;', '&gt;', "'"), $titleLien);
					$lienImage = '<a rel="' . $relLien . '" href="' . $urlSourceImage . '/' . encodeTexte($image['intermediaireNom']) . '" title="' . $titleLien . '">';
				}
				else
				{
					$lienImage = '<a rel="' . $relLien . '" href="' . $urlPageIndividuelleImage . '" title="' . securiseTexte($titreImage) . '">';
				}
				
				$contenu .= "<li><div class=\"galerieNavigationAccueil\">$lienImage<img src=\"$urlVignette\" width=\"$largeurImage\" height=\"$hauteurImage\" alt=\"$altImage\" /></a></div></li>\n";
			}
			
			$contenu .= "</ul><!-- /.galerieListeImages -->\n";
			$contenu .= "</div><!-- /.galerieDansListe -->\n";
			$contenu .= "<div class=\"sep\"></div>\n";
		}
	}
	
	$contenu .= "</div><!-- /.galeries -->\n";
	
	return $contenu;
}

/*
Retourne TRUE si la bibliothèque GD est installée, sinon retourne FALSE.
*/
function gdEstInstallee()
{
	if (function_exists('gd_info'))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Génère et retourne le code pour le bloc `lien-page`.
*/
function genereCodeLienPage($fusionnerBlocsPartageLienPage, $lienPage, $erreur404, $estPageDerreur, $courrielContact, $url, $blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, $nombreDeColonnes, $baliseTitle, $baliseTitleComplement, $lienPageVignette, $lienPageIntermediaire, $aExecuterApresClicBd)
{
	$bloc = '';
	
	if ($lienPage && !$erreur404 && !$estPageDerreur && empty($courrielContact))
	{
		if ($fusionnerBlocsPartageLienPage)
		{
			$bloc .= '<div id="lienPage" class="fusionBlocsPartageLienPage">' . "\n";
			$bloc .= '<h3>' . T_("Faire un lien vers cette page") . "</h3>\n";
		}
		else
		{
			$classesBloc = classesBloc($blocsAvecFondParDefaut, $blocsAvecFondSpecifiques, $blocsArrondis, 'lien-page', $nombreDeColonnes);
			$bloc .= '<div id="lienPage" class="bloc ' . $classesBloc . '">' . "\n";
			$bloc .= '<h2 class="bDtitre">' . T_("Faire un lien vers cette page") . "</h2>\n";
			$bloc .= "<div class=\"bDcorps\">\n";
		}
		
		$urlSansAction = variableGet(0, $url, 'action');
		$codeLienPage = '<pre><code>' . securiseTexte('<a href="' . $urlSansAction . '">' . $baliseTitle . $baliseTitleComplement . '</a>') . "</code></pre>\n";
		$liensImage = array ();
		
		if (isset($lienPageVignette) && preg_match('#(<img .+? />)#', $lienPageVignette, $resultat))
		{
			$liensImage['vignette']['description'] = '<p>' . T_("Lien avec l'image en vignette:") . "</p>\n";
			$liensImage['vignette']['balise'] = $resultat[1];
		}
		
		if (isset($lienPageIntermediaire) && preg_match('#(<img .+? />)#', $lienPageIntermediaire, $resultat))
		{
			$liensImage['intermediaire']['description'] = '<p>' . T_("Lien avec l'image en taille intermédiaire:") . "</p>\n";
			$liensImage['intermediaire']['balise'] = $resultat[1];
		}
		
		if (empty($liensImage))
		{
			$bloc .= '<p>' . T_("Ajoutez le code ci-dessous sur votre site:") . "</p>\n$codeLienPage";
		}
		else
		{
			$bloc .= '<p>' . T_("Lien textuel seulement:") . "</p>\n$codeLienPage";
			
			foreach ($liensImage as $lienImageType => $lienImageInfo)
			{
				$bloc .= $lienImageInfo['description'];
				$bloc .= '<pre><code>' . securiseTexte('<a href="' . $urlSansAction . '" title="' . $baliseTitle . $baliseTitleComplement . '">' . $lienImageInfo['balise'] . '</a>') . "</code></pre>\n";
			}
		}
		
		if (!$fusionnerBlocsPartageLienPage)
		{
			$bloc .= "</div>\n";
		}
		
		$bloc .= '</div><!-- /#lienPage -->' . "\n";
		
		if (!$fusionnerBlocsPartageLienPage)
		{
			$bloc .= '<script type="text/javascript">' . "\n";
			$bloc .= "//<![CDATA[\n";
			$bloc .= "boiteDeroulante('#lienPage', \"$aExecuterApresClicBd\");\n";
			$bloc .= "//]]>\n";
			$bloc .= "</script>\n";
		}
	}
	
	return $bloc;
}

/*
Effectue l'action demandée (0: suppression; 1: envoi; 2: ajout) pour les notifications en attente. Retourne une chaîne vide.
*/
function gereNotificationsEnAttente($racine, $idCommentaire, $action, $infos = array ())
{
	$cheminNotificationsEnAttente = $racine . '/site/inc/notifications-en-attente.txt';
	
	if (file_exists($cheminNotificationsEnAttente) || @touch($cheminNotificationsEnAttente))
	{
		$contenuFichierCodeEnAttente = @file_get_contents($cheminNotificationsEnAttente);
		
		if ($contenuFichierCodeEnAttente !== FALSE)
		{
			$codeEnAttente = unserialize($contenuFichierCodeEnAttente);
			
			if (($action == 0 || $action == 1) && is_array($codeEnAttente) && isset($codeEnAttente[$idCommentaire]))
			{
				if ($action == 1)
				{
					foreach ($codeEnAttente[$idCommentaire] as $infosCourriel)
					{
						courriel($infosCourriel);
					}
				}
				
				unset($codeEnAttente[$idCommentaire]);
			}
			elseif ($action == 2)
			{
				if (is_array($codeEnAttente))
				{
					$codeEnAttente = array_merge($codeEnAttente, $infos);
				}
				else
				{
					$codeEnAttente = $infos;
				}
			}
			
			if (!empty($codeEnAttente))
			{
				@file_put_contents($cheminNotificationsEnAttente, serialize($codeEnAttente), LOCK_EX);
			}
			else
			{
				@unlink($cheminNotificationsEnAttente);
			}
		}
	}
	
	return '';
}

/*
Retourne le code HTML d'une catégorie à inclure dans le menu des catégories automatisé.
*/
function htmlCategorie($urlRacine, $categories, $categorie, $afficherNombreArticlesCategorie)
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
	
	$categories[$categorie]['url'] = urlCat($categories[$categorie], $categorie);
	
	$htmlCategorie .= '<a href="' . $urlRacine . '/' . $categories[$categorie]['url'] . '">' . securiseTexte($nomCategorie) . '</a>';
	
	if ($afficherNombreArticlesCategorie && isset($categories[$categorie]['pages']))
	{
		$htmlCategorie .= sprintf(T_(" (%1\$s)"), count($categories[$categorie]['pages']));
	}
	
	$categoriesEnfants = categoriesEnfants($categories, $categorie);
	
	if (!empty($categoriesEnfants))
	{
		$htmlCategorie .= "<ul>\n";
		
		foreach ($categoriesEnfants as $enfant)
		{
			$htmlCategorie .= htmlCategorie($urlRacine, $categories, $enfant, $afficherNombreArticlesCategorie);
		}
		
		$htmlCategorie .= "</ul>\n";
	}
	
	$htmlCategorie .= "</li>\n";
	
	return $htmlCategorie;
}

/*
Retourne l'`id` réel d'une catégorie à partir de l'`id` filtré. Si aucun `id` n'a été trouvé, retourne une chaîne vide.
*/
function idCategorie($categories, $idCategorieFiltre)
{
	$idReel = '';
	
	foreach($categories as $idCategorie => $infosCategorie)
	{
		if ($idCategorieFiltre == filtreChaine($idCategorie))
		{
			$idReel = $idCategorie;
			break;
		}
	}
	
	return $idReel;
}

/*
Retourne l'`id` réel d'une galerie à partir de l'`id` filtré. Si aucun `id` n'a été trouvé, retourne une chaîne vide.
*/
function idGalerie($galeries, $idGalerieFiltre)
{
	$idReel = '';
	
	foreach($galeries as $idGalerie => $infosGalerie)
	{
		if ($idGalerieFiltre == filtreChaine($idGalerie))
		{
			$idReel = $idGalerie;
			break;
		}
	}
	
	return $idReel;
}

/*
Retourne le nom du dossier d'une galerie. Si aucun dossier n'a été trouvé, retourne un nom qui pourra être utilisé pour en créer un.
*/
function idGalerieDossier($racine, $idGalerie)
{
	$dossier = '';
	$listeGaleries = listeGaleries($racine, $idGalerie);
	
	if (!empty($listeGaleries[$idGalerie]['dossier']))
	{
		$dossier = $listeGaleries[$idGalerie]['dossier'];
	}
	else
	{
		$dossier = filtreChaine($idGalerie);
	}
	
	return $dossier;
}

/*
Retourne l'`id` d'une image d'une galerie.
*/
function idImage($image)
{
	$idImage = '';
	
	if (!empty($image['id']))
	{
		$idImage = $image['id'];
	}
	elseif (!empty($image['titre']))
	{
		$idImage = $image['titre'];
	}
	else
	{
		$idImage = $image['intermediaireNom'];
	}
	
	return filtreChaine($idImage);
}

/*
Construit et retourne le code pour afficher une image dans la galerie. Si la taille de l'image n'est pas valide, retourne une chaîne vide.

Si une vignette doit être créée et que la bibliothèque GD n'est pas installée, la vignette utilisée est une image par défaut livrée avec Squeletml.
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
	$titreImage = titreImage($infosImage);
	
	####################################################################
	#
	# Taille intermédiaire.
	#
	####################################################################
	
	if ($taille == 'intermediaire')
	{
		if (!empty($infosImage['intermediaireLargeur']) || !empty($infosImage['intermediaireHauteur']))
		{
			if (!empty($infosImage['intermediaireLargeur']))
			{
				$width = 'width="' . securiseTexte($infosImage['intermediaireLargeur']) . '"';
			}
			
			if (!empty($infosImage['intermediaireHauteur']))
			{
				$height = 'height="' . securiseTexte($infosImage['intermediaireHauteur']) . '"';
			}
		}
		else
		{
			list ($larg, $haut) = @getimagesize($racineImgSrc . '/' . $infosImage['intermediaireNom']);
			{
				$width = 'width="' . $larg . '"';
				$height = 'height="' . $haut . '"';
			}
		}
		
		if (!empty($infosImage['intermediaireAlt']))
		{
			$alt = 'alt="' . securiseTexte($infosImage['intermediaireAlt']) . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Image %1\$s"), securiseTexte($titreImage)) . '"';
		}
		
		if (!empty($infosImage['intermediaireAttributTitle']))
		{
			$attributTitle = ' title="' . securiseTexte($infosImage['intermediaireAttributTitle']) . '" ';
		}
		else
		{
			$attributTitle = ' ';
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
				$urlLienOriginal = $urlRacine . '/telecharger.php?fichier=' . encodeTexteGet(preg_replace('#^' . preg_quote($racine, '#') . '/#', '', $racineImgSrc . '/' . $originalNom));
				$texteLienOriginal = sprintf(T_("Télécharger l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), '<em>' . securiseTexte($titreImage) . '</em>', "<em>$originalExtension</em>", octetsVersKio(@filesize($racineImgSrc . '/' . $originalNom)));
				$texteAltLienOriginal = sprintf(T_("Télécharger l'image %1\$s au format original"), securiseTexte($titreImage));
			}
			else
			{
				$urlLienOriginal = $urlImgSrc . '/' . encodeTexte($originalNom);
				$texteLienOriginal = sprintf(T_("Voir l'image %1\$s au format original (extension: %2\$s; taille: %3\$s Kio)."), '<em>' . securiseTexte($titreImage) . '</em>', "<em>$originalExtension</em>", octetsVersKio(@filesize($racineImgSrc . '/' . $originalNom)));
				$texteAltLienOriginal = sprintf(T_("Voir l'image %1\$s au format original"), securiseTexte($titreImage));
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
			
				$divLienOriginalIcone = '<div id="galerieLienOriginalIcone">' . $aLienOriginalDebut . '<img src="' . $iconeLienOriginalSrc . '" alt="' . $texteAltLienOriginal . '" title="' . $texteAltLienOriginal . '" width="22" height="22" />' . $aLienOriginalFin . '</div><!-- /#galerieLienOriginalIcone -->' . "\n";
			}
		}
		
		// Légende.
		if (!empty($infosImage['intermediaireLegende']))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . intermediaireLegende($infosImage['intermediaireLegende'], $galerieLegendeMarkdown) . "</div>\n";
		}
		elseif ($galerieLegendeAutomatique && (!$originalExiste || ($originalExiste && !$galerieLienOriginalEmplacement['legende'])))
		{
			$legende = '<div id="galerieIntermediaireLegende">' . sprintf(T_("Image %1\$s (extension: %2\$s; taille: %3\$s Kio)."), '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . extension($infosImage['intermediaireNom']) . '</em>', octetsVersKio(@filesize($racineImgSrc . '/' . $infosImage['intermediaireNom']))) . "</div>\n";
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
						
						$exif .= "<li><em>$exifTrad:</em> " . securiseTexte($tableauExif[$cle]) . "</li>\n";
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
			return '<div id="galerieIntermediaireTexte">' . $legende . $exif . $divLienOriginalLegende . "</div><!-- /#galerieIntermediaireTexte -->\n" . '<div id="galerieIntermediaireImg">' . $aLienOriginalImgIntermediaireDebut . '<img src="' . $urlImgSrc . '/' . encodeTexte($infosImage['intermediaireNom']) . '"' . " $width $height $alt$attributTitle/>" . $aLienOriginalImgIntermediaireFin . "</div><!-- /#galerieIntermediaireImg -->\n" . $divLienOriginalIcone;
		}
		elseif ($galerieLegendeEmplacement[$nombreDeColonnes] == 'bas')
		{
			return '<div id="galerieIntermediaireImg">' . $aLienOriginalImgIntermediaireDebut . '<img src="' . $urlImgSrc . '/' . encodeTexte($infosImage['intermediaireNom']) . '"' . " $width $height $alt$attributTitle/>" . $aLienOriginalImgIntermediaireFin . "</div><!-- /#galerieIntermediaireImg -->\n" . $divLienOriginalIcone . '<div id="galerieIntermediaireTexte">' . $legende . $exif . $divLienOriginalLegende . "</div><!-- /#galerieIntermediaireTexte -->\n";
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
		$width = '';
		$height = '';
		
		if ($galerieNavigation == 'fleches' && ($sens == 'precedent' || $sens == 'suivant'))
		{
			$class .= ' galerieFleche';
			
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
				$src = 'src="' . $urlImgSrc . '/' . encodeTexte($infosImage['vignetteNom']) . '"';
			}
			// Sinon on génère un nom automatique selon le nom de la version intermediaire de l'image.
			else
			{
				$vignetteNom = nomSuffixe($infosImage['intermediaireNom'], '-vignette');
				
				if (!file_exists($racineImgSrc . '/' . $vignetteNom))
				{
					// Si la bibliothèque GD est installée, on génère une vignette.
					if (gdEstInstallee())
					{
						nouvelleImage($racineImgSrc . '/' . $infosImage['intermediaireNom'], $racineImgSrc . '/' . $vignetteNom, $typeMime, $galerieDimensionsVignette, $galerieForcerDimensionsVignette, $galerieQualiteJpg, $galerieCouleurAlloueeImage, array ('nettete' => FALSE));
					}
					// Sinon on utilise une image par défaut livrée avec Squeletml.
					else
					{
						switch ($typeMime)
						{
							case 'image/gif':
								@copy($racine . '/fichiers/vignette-par-defaut.gif', $racineImgSrc . '/' . $vignetteNom);
								break;
								
							case 'image/jpeg':
								@copy($racine . '/fichiers/vignette-par-defaut.jpg', $racineImgSrc . '/' . $vignetteNom);
								break;
								
							case 'image/png':
								@copy($racine . '/fichiers/vignette-par-defaut.png', $racineImgSrc . '/' . $vignetteNom);
								break;
						}
					}
				}
				
				$src = 'src="' . $urlImgSrc . '/' . encodeTexte($vignetteNom) . '"';
			}
			
			if ($vignetteAvecDimensions)
			{
				if (!empty($infosImage['vignetteLargeur']) || !empty($infosImage['vignetteHauteur']))
				{
					if (!empty($infosImage['vignetteLargeur']))
					{
						$width = 'width="' . securiseTexte($infosImage['vignetteLargeur']) . '"';
					}
				
					if (!empty($infosImage['vignetteHauteur']))
					{
						$height = 'height="' . securiseTexte($infosImage['vignetteHauteur']) . '"';
					}
				}
				else
				{
					list ($larg, $haut) = @getimagesize($racineImgSrc . '/' . $vignetteNom);
					$width = 'width="' . $larg . '"';
					$height = 'height="' . $haut . '"';
				}
			}
		}
		
		if (!empty($infosImage['vignetteAlt']))
		{
			$alt = 'alt="' . securiseTexte($infosImage['vignetteAlt']) . '"';
		}
		else
		{
			$alt = 'alt="' . sprintf(T_("Image %1\$s"), securiseTexte($titreImage)) . '"';
		}
		
		if (!empty($infosImage['vignetteAttributTitle']))
		{
			$attributTitle = ' title="' . securiseTexte($infosImage['vignetteAttributTitle']) . '" ';
		}
		else
		{
			$attributTitle = ' ';
		}
		
		if ($estAccueil)
		{
			$classAccueil = 'Accueil ';
		}
		else
		{
			$classAccueil = ' ';
		}
		
		$ancre = ancreDeNavigationGalerie($galerieAncreDeNavigation);
		$hrefPageIndividuelleImage = variableGet(1, url(), 'image', idImage($infosImage)) . $ancre;
		$hrefPageIndividuelleImage = variableGet(0, $hrefPageIndividuelleImage, 'action');
		
		if ($estAccueil && $galerieAccueilJavascript)
		{
			$title = '<a href="' . $hrefPageIndividuelleImage . '">' . T_("Partager cette image ou voir plus d'information.") . '</a>';
			
			if (!empty($infosImage['intermediaireLegende']))
			{
				$title = $infosImage['intermediaireLegende'] . '<br />' . $title;
			}
			
			$aHref = '<a href="' . $urlImgSrc . '/' . encodeTexte($infosImage['intermediaireNom']) . '" rel="lightbox-galerie" title="' . securiseTexte($title) . '">';
		}
		else
		{
			$aHref = '<a href="' . $hrefPageIndividuelleImage . '" title="' . securiseTexte($titreImage) . '">';
		}
		
		if ($minivignetteImageEnCours)
		{
			$class .= ' minivignetteImageEnCours';
		}
		
		return '<div class="galerieNavigation' . $classAccueil . $class . '">' . $aHref . '<img ' . "$src $width $height $alt$attributTitle/></a></div>\n";
	}
	else
	{
		return '';
	}
}

/*
Retourne un tableau contenant les fichiers à inclure au début du script.
*/
function inclureAuDebut($racine)
{
	$fichiers = array ();
	
	foreach (cheminsInc($racine, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/*
Retourne un tableau contenant les fichiers à inclure une seule fois au début du script.
*/
function inclureUneFoisAuDebut($racine)
{
	$fichiers = array ();
	$fichiers[] = $racine . '/inc/php-markdown/markdown.inc.php';
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc.php';
	$fichiers[] = $racine . '/inc/simplehtmldom/simple_html_dom.inc.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/common.inc.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/filter.inc.php';
	
	if (file_exists($racine . '/site/inc/fonctions.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/fonctions.inc.php';
	}
	
	foreach (cheminsInc($racine, 'constantes') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/*
Retourne un tableau d'informations au sujet du contenu local accessible à l'URL `$urlPage`, ou directement au sujet du contenu fourni si `$html` n'est pas vide. Le tableau contient les informations suivantes:

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
*/
function infosPage($racine, $urlRacine, $urlPage, $inclureApercu, $tailleApercuAutomatique, $dureeCache, $desactiverLectureCachePartiel = FALSE, $estPageCron = FALSE, $html = '')
{
	$infosPage = array ();
	
	if (empty($html))
	{
		$html = simuleVisite($racine, $urlRacine, $urlPage, $dureeCache, $desactiverLectureCachePartiel, $estPageCron);
	}
	
	if (!empty($html))
	{
		$dom = str_get_html($html);
		
		if (method_exists($dom, 'find'))
		{
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
				$infosPage['titre'] = $urlPage;
			}
		
			// Contenu.
		
			if ($contenu = $dom->find('div#galerieIntermediaireImg img'))
			{
				$infosPage['contenu'] = '<div class="galerieIntermediaireImgApercu"><a href="' . $urlPage . '">' . $contenu[0]->outertext . "</a></div>\n";
			
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
	
			if ($inclureApercu && preg_match('/<!-- APERÇU: (.+?) -->/s', $infosPage['contenu'], $resultatApercu))
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
					$contenuSansCommentairesHtml = trim(supprimeCommentairesHtml($infosPage['contenu']));
					$commentairesHtmlSupprimes = TRUE;
					$infosPage['apercu'] = tronqueTexte($contenuSansCommentairesHtml, $tailleApercuAutomatique, array (), TRUE);
					
					if ($infosPage['apercu'] == $contenuSansCommentairesHtml)
					{
						$infosPage['apercu'] = '';
					}
					else
					{
						$infosPage['apercu'] .= "<div class=\"sep\"></div>\n";
					}
					
					unset($contenuSansCommentairesHtml);
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
		}
		
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
			$listeCategories .= '<a href="' . $urlRacine . '/' . $urlCat . '">' . securiseTexte($categorie) . '</a>, ';
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
		
		if (method_exists($dom, 'find'))
		{
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
			
			$codeMenuLangues = $dom->save();
			$dom->clear();
		}
		
		unset($dom);
	}
	
	return $codeMenuLangues;
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
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://artlibre.org/licence/lal\"><img %1\$s alt=\"Licence Art Libre\" /></a> Mis à disposition sous la <a href=\"http://artlibre.org/licence/lal\">licence Art Libre</a>."), "src=\"$urlRacine/fichiers/licence-art-libre-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Attribution 3.0 non transposé (CC BY 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by-sa':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-sa/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Paternité – Partage dans les mêmes conditions 3.0 non transposé (CC BY-SA 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by-sa/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-sa/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by-nd':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nd/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Attribution – Pas de modification 3.0 non transposé (CC BY-ND 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by-nd/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nd/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by-nc':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Attribution – Pas d'utilisation commerciale 3.0 non transposé (CC BY-NC 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by-nc-sa':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc-sa/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Attribution – Pas d'utilisation commerciale – Partage dans les mêmes conditions 3.0 non transposé (CC BY-NC-SA 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by-nc-sa/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'cc-by-nc-nd':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/licenses/by-nc-nd/3.0/deed.fr\"><img %1\$s alt=\"Licence Creative Commons Attribution – Pas d'utilisation commerciale – Pas de modification 3.0 non transposé (CC BY-NC-ND 3.0)\" /></a> Mis à disposition sous une <a href=\"http://creativecommons.org/licenses/by-nc-nd/3.0/deed.fr\">licence Creative Commons</a>."), "src=\"http://i.creativecommons.org/l/by-nc-nd/3.0/80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'gplv2':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl-2.0.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 2\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl-2.0.html\">licence publique générale de GNU, version 2</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'gplv2+':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 2 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl.html\">licence publique générale de GNU, version 2 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'gplv3':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl-3.0.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl-3.0.html\">licence publique générale de GNU, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'gplv3+':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/gpl.html\"><img %1\$s alt=\"Licence publique générale de GNU, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/gpl.html\">licence publique générale de GNU, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-gpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'agplv3':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/agpl-3.0.html\"><img %1\$s alt=\"Licence publique générale GNU Affero, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/agpl-3.0.html\">licence publique générale GNU Affero, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-agpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'agplv3+':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/agpl.html\"><img %1\$s alt=\"Licence publique générale GNU Affero, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/agpl.html\">licence publique générale GNU Affero, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-agpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'lgplv2.1':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl-2.1.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 2.1\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl-2.1.html\">licence publique générale amoindrie de GNU, version 2.1</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'lgplv2.1+':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 2.1 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl.html\">licence publique générale amoindrie de GNU, version 2.1 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'lgplv3':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl-3.0.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 3\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl-3.0.html\">licence publique générale amoindrie de GNU, version 3</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'lgplv3+':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.gnu.org/licenses/lgpl.html\"><img %1\$s alt=\"Licence publique générale amoindrie de GNU, version 3 ou toute version ultérieure\" /></a> Mis à disposition sous la <a href=\"http://www.gnu.org/licenses/lgpl.html\">licence publique générale amoindrie de GNU, version 3 ou toute version ultérieure</a>."), "src=\"$urlRacine/fichiers/licence-gnu-lgpl-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'bsd':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://fr.wikipedia.org/wiki/Licence_BSD#Texte_de_la_licence\"><img %1\$s alt=\"Licence BSD modifiée\" /></a> Mis à disposition sous la <a href=\"http://fr.wikipedia.org/wiki/Licence_BSD#Texte_de_la_licence\">licence BSD modifiée</a>."), "src=\"$urlRacine/fichiers/licence-bsd-modifiee-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'mdp':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/publicdomain/mark/1.0/deed.fr\"><img %1\$s alt=\"Marque du domaine public 1.0\" /></a> Identifié comme étant dans le <a href=\"http://creativecommons.org/publicdomain/mark/1.0/deed.fr\">domaine public</a>."), "src=\"$urlRacine/fichiers/domaine-public-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'mit':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://www.opensource.org/licenses/mit-license.php\"><img %1\$s alt=\"Licence MIT\" /></a> Mis à disposition sous la <a href=\"http://www.opensource.org/licenses/mit-license.php\">licence MIT</a>."), "src=\"$urlRacine/fichiers/licence-mit-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'tdp':
			$licence = '<span class="licence">' . sprintf(T_("<a href=\"http://creativecommons.org/publicdomain/zero/1.0/deed.fr\"><img %1\$s alt=\"Transfert dans le domaine public – CC0 1.0 universel (CC0 1.0)\" /></a> Transféré dans le <a href=\"http://creativecommons.org/publicdomain/zero/1.0/deed.fr\">domaine public</a>."), "src=\"$urlRacine/fichiers/domaine-public-80x15.png\" width=\"80\" height=\"15\"") . '</span>';
			break;

		case 'verbatim':
			$licence = '<span class="licence">' . T_("La reproduction exacte et la distribution intégrale de cet article est permise sur n'importe quel support d'archivage, pourvu que cette notice soit préservée.") . '</span>';
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
function lienActif($urlRacine, $html, $inclureGet, $parent = '')
{
	$url = url();
	$urlRelative = supprimeUrlRacine($urlRacine, $url);
	$infosUrlRelative = parse_url($urlRelative);
	$dom = str_get_html($html);
	
	if (method_exists($dom, 'find'))
	{
		foreach ($dom->find('a') as $a)
		{
			$lienActif = FALSE;
			$aHref = $a->href;
			$aHref = preg_replace('/#.+$/', '', $aHref);
			$aHrefRelatif = supprimeUrlRacine($urlRacine, $aHref);
			$infosAhrefRelatif = parse_url($aHrefRelatif);
		
			if (isset($infosAhrefRelatif['path']) && isset($infosUrlRelative['path']) && $infosAhrefRelatif['path'] == $infosUrlRelative['path'])
			{
				// A: même nom de page (mais pas nécessairement mêmes variables `GET`).
			
				$getUrlRelative = array ();
			
				if (isset($infosUrlRelative['query']))
				{
					parse_str(str_replace('&amp;', '&', $infosUrlRelative['query']), $getUrlRelative);
					uksort($getUrlRelative, 'strnatcasecmp');
				}
			
				$getAhrefRelatif = array ();
			
				if (isset($infosAhrefRelatif['query']))
				{
					parse_str(str_replace('&amp;', '&', $infosAhrefRelatif['query']), $getAhrefRelatif);
					uksort($getAhrefRelatif, 'strnatcasecmp');
				}
			
				if ($getUrlRelative == $getAhrefRelatif)
				{
					$lienActif = TRUE;
				}
				elseif (isset($getAhrefRelatif['action']))
				{
					$lienActif = FALSE;
				}
				else
				{
					if (isset($getUrlRelative['image']))
					{
						unset($getUrlRelative['image']);
					}
				
					if (isset($getUrlRelative['action']))
					{
						unset($getUrlRelative['action']);
					}
				
					if (isset($getAhrefRelatif['image']))
					{
						unset($getAhrefRelatif['image']);
					}
				
					if (($getUrlRelative == $getAhrefRelatif) || !$inclureGet)
					{
						$lienActif = TRUE;
					}
				}
			}
		
			if ($lienActif)
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
		
		$html = $dom->save();
		$dom->clear();
	}
	
	unset($dom);
	
	return $html;
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
	
	if (method_exists($dom, 'find'))
	{
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
		
				if ($ulParent->tag == 'li' && preg_match('/\bactif\b/', $ulParent->class))
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
						if (preg_match('/\bmasquer\b/', $ul->class))
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
	
		$html = $dom->save();
		$dom->clear();
	}
	
	unset($dom);
	
	return $html;
}

/*
Construit des balises `link` et `script`. Voir le fichier de configuration `inc/config.inc.php` pour les détails au sujet de la syntaxe utilisée.

Le paramètre `$dossierAdmin` doit être vide si la fonction est utilisée pour le site et non pour la section d'administration.
*/
function linkScript($racine, $urlRacine, $fusionnerCssJs, $dossierAdmin, $balisesBrutes, $versionParDefautLinkScript = array ('css' => '', 'favicon' => '', 'js' => ''))
{
	$balisesBrutesAinclure = linkScriptAinclure($balisesBrutes);
	$balisesFormatees = '';
	$favicon = '';
	
	if ($fusionnerCssJs)
	{
		$balisesBrutesFusionneesAinclure = array ();
		$balisesBrutesCssAinclure = array ();
		$balisesBrutesCssIe6Ainclure = array ();
		$balisesBrutesCssIe7Ainclure = array ();
		$balisesBrutesCssIe8Ainclure = array ();
		$balisesBrutesJsAinclure = array ();
		$balisesBrutesJsIe6Ainclure = array ();
		$listeFichiersCss = array ();
		$listeFichiersCssIe6 = array ();
		$listeFichiersCssIe7 = array ();
		$listeFichiersCssIe8 = array ();
		$listeFichiersJs = array ();
		$listeFichiersJsIe6 = array ();
		
		foreach ($balisesBrutesAinclure as $fichierBrut)
		{
			// On récupère les infos.
			list ($type, $fichier) = explode('#', $fichierBrut, 2);
			
			if ($type == 'css')
			{
				$listeFichiersCss[] = $fichier;
				$balisesBrutesCssAinclure[] = $fichierBrut;
			}
			elseif (preg_match('/^css[lI]/', $type))
			{
				if ($type == 'cssltIE7' || $type == 'csslteIE7' || $type == 'csslteIE8')
				{
					$listeFichiersCssIe6[] = $fichier;
					$balisesBrutesCssIe6Ainclure[] = $fichierBrut;
				}
				
				if ($type == 'cssIE7' || $type == 'csslteIE7' || $type == 'csslteIE8')
				{
					$listeFichiersCssIe7[] = $fichier;
					$balisesBrutesCssIe7Ainclure[] = $fichierBrut;
				}
				
				if ($type == 'cssIE8' || $type == 'csslteIE8')
				{
					$listeFichiersCssIe8[] = $fichier;
					$balisesBrutesCssIe8Ainclure[] = $fichierBrut;
				}
			}
			elseif ($type == 'js')
			{
				$listeFichiersJs[] = $fichier;
				$balisesBrutesJsAinclure[] = $fichierBrut;
			}
			elseif ($type == 'jsltIE7')
			{
				$listeFichiersJsIe6[] = $fichier;
				$balisesBrutesJsIe6Ainclure[] = $fichierBrut;
			}
			else
			{
				$balisesBrutesFusionneesAinclure[] = $fichierBrut;
			}
		}
		
		if (!empty($listeFichiersJsIe6))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'jsltIE7', 'js', $listeFichiersJsIe6, $balisesBrutesJsIe6Ainclure, $balisesBrutesFusionneesAinclure);
		}
		
		if (!empty($listeFichiersJs))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'js', 'js', $listeFichiersJs, $balisesBrutesJsAinclure, $balisesBrutesFusionneesAinclure);
		}
		
		if (!empty($listeFichiersCssIe6))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'cssltIE7', 'css', $listeFichiersCssIe6, $balisesBrutesCssIe6Ainclure, $balisesBrutesFusionneesAinclure);
		}
		
		if (!empty($listeFichiersCssIe7))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'cssIE7', 'css', $listeFichiersCssIe7, $balisesBrutesCssIe7Ainclure, $balisesBrutesFusionneesAinclure);
		}
		
		if (!empty($listeFichiersCssIe8))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'cssIE8', 'css', $listeFichiersCssIe8, $balisesBrutesCssIe8Ainclure, $balisesBrutesFusionneesAinclure);
		}
		
		if (!empty($listeFichiersCss))
		{
			$balisesBrutesFusionneesAinclure = fusionneCssJs($racine, $urlRacine, $dossierAdmin, 'css', 'css', $listeFichiersCss, $balisesBrutesCssAinclure, $balisesBrutesFusionneesAinclure);
		}
		
		$balisesBrutesAinclure = $balisesBrutesFusionneesAinclure;
	}
	
	// Si une tentative de fusion de fichiers CSS pour IE a échoué, il risque d'y avoir des doublons dans le tableau des balises brutes à inclure (par exemple, un style `csslteIE7` s'applique à la fois à IE 6 et à IE 7). Tout doublon potentiel est donc supprimé.
	$balisesBrutesAinclure = array_unique($balisesBrutesAinclure);
	
	foreach ($balisesBrutesAinclure as $fichierBrut)
	{
		// On récupère les infos.
		list ($type, $fichier) = explode('#', $fichierBrut, 2);
		
		if (($type == 'rss' || $type == 'hreflang') && strpos($fichier, '#') !== FALSE)
		{
			list ($fichier, $extra) = explode('#', $fichier, 2);
		}
		else
		{
			$extra = '';
		}
		
		switch ($type)
		{
			case 'favicon':
				// On ne conserve qu'une déclaration de favicon.
				$favicon = '<link rel="shortcut icon" type="images/x-icon" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['favicon']) . '" />' . "\n";
				break;
	
			case 'css':
				$balisesFormatees .= '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n";
				break;
				
			case 'cssDirectlteIE8':
				$balisesFormatees .= "<!--[if lte IE 8]>\n<style type=\"text/css\">\n$fichier\n</style>\n<![endif]-->\n";
				break;
				
			case 'cssltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
		
			case 'cssIE7':
				$balisesFormatees .= '<!--[if IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'csslteIE7':
				$balisesFormatees .= '<!--[if lte IE 7]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'cssIE8':
				$balisesFormatees .= '<!--[if IE 8]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'csslteIE8':
				$balisesFormatees .= '<!--[if lte IE 8]>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . variableGet(2, $fichier, $versionParDefautLinkScript['css']) . '" media="screen" />' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'hreflang':
				if (!empty($extra))
				{
					$balisesFormatees .= '<link rel="alternate" hreflang="' . $extra . '" href="' . $fichier . '" />' . "\n";
				}
				
				break;
				
			case 'js':
				$balisesFormatees .= '<script type="text/javascript" src="' . variableGet(2, $fichier, $versionParDefautLinkScript['js']) . '"></script>' . "\n";
				break;
				
			case 'jsDirect':
				$balisesFormatees .= "<script type=\"text/javascript\">\n//<![CDATA[\n
$fichier\n//]]>\n</script>\n";
				break;
				
			case 'jsDirectltIE7':
				$balisesFormatees .= "<!--[if lt IE 7]>\n<script type=\"text/javascript\">\n//<![CDATA[\n$fichier\n//]]>\n</script>\n<![endif]-->\n";
				break;
				
			case 'jsltIE7':
				$balisesFormatees .= '<!--[if lt IE 7]>' . "\n" . '<script type="text/javascript" src="' . variableGet(2, $fichier, $versionParDefautLinkScript['js']) . '"></script>' . "\n" . '<![endif]-->' . "\n";
				break;
				
			case 'rss':
				if (!empty($extra))
				{
					$title = ' title="' . securiseTexte($extra) . '"';
				}
				else
				{
					$title = '';
				}
				
				$balisesFormatees .= '<link rel="alternate" type="application/rss+xml" href="' . $fichier . '"' . $title . ' />' . "\n";
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
Retourne un tableau des catégories auxquelles appartient la page donnée. La structure du tableau est:

	$listeCategories['idCategorie'] = 'urlCategorie';
*/
function listeCategoriesPage($racine, $urlRacine, $urlPage)
{
	$listeCategories = array ();
	$cheminFichier = cheminConfigCategories($racine);
	
	if ($cheminFichier && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE && !empty($categories))
	{
		foreach ($categories as $categorie => $categorieInfos)
		{
			if (!empty($categorieInfos['pages']))
			{
				foreach ($categorieInfos['pages'] as $page)
				{
					$urlDeComparaison = $urlRacine . '/' . $page;
					
					if ($urlDeComparaison == $urlPage)
					{
						$listeCategories[$categorie] = urlCat($categorieInfos, $categorie);
					}
				}
			}
		}
	}
	
	uksort($listeCategories, 'strnatcasecmp');
	
	return $listeCategories;
}

/*
Retourne un tableau dont chaque élément contient un fichier d'index possible. Cette liste correspond à la valeur par défaut de `DirectoryIndex` sous Apache 2.
*/
function listeFichiersIndex()
{
	return array ('index.html', 'index.cgi', 'index.pl', 'index.php', 'index.xhtml', 'index.htm');
}

/*
Retourne un tableau listant les galeries sous la forme suivante:

	"$idGalerie" => array ("dossier" => "$idGalerieDossier", "url" => "$urlGalerie")

Si le paramètre `$avecConfigSeulement` vaut TRUE, retourne seulement les galeries ayant un fichier de configuration.
*/
function listeGaleries($racine, $galerieSpecifique = '', $avecConfigSeulement = FALSE)
{
	$galeries = array ();
	$configGaleries = super_parse_ini_file(cheminConfigGaleries($racine), TRUE);
	
	if (!empty($configGaleries))
	{
		if (!empty($galerieSpecifique) && isset($configGaleries[$galerieSpecifique]))
		{
			$galeriesTmp = array ($galerieSpecifique => $configGaleries[$galerieSpecifique]);
		}
		else
		{
			$galeriesTmp = $configGaleries;
		}
		
		if ($avecConfigSeulement)
		{
			foreach ($galeriesTmp as $idGalerie => $infosGalerie)
			{
				if (!empty($infosGalerie['dossier']) && cheminConfigGalerie($racine, $infosGalerie['dossier']) !== FALSE)
				{
					$galeries[$idGalerie] = $infosGalerie;
				}
			}
		}
		else
		{
			$galeries = $galeriesTmp;
		}
	}
	
	uksort($galeries, 'strnatcasecmp');
	
	return $galeries;
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
Ajoute le formulaire fourni en paramètre au fichier de configuration des formulaires envoyés. Retourne une chaîne vide.
*/
function majConfigFormulairesEnvoyes($racine, $idFormulaire)
{
	$fichierConfig = "$racine/site/cache/formulaires-envoyes.ini.txt";
	
	if (!file_exists($fichierConfig) && !@touch($fichierConfig))
	{
		return '';
	}
	
	$contenuFichier = "[formulaires]\n";
	$formulairesEnvoyes = super_parse_ini_file($fichierConfig, TRUE);
	
	if (!is_array($formulairesEnvoyes))
	{
		$formulairesEnvoyes = array ();
	}
	
	if (!isset($formulairesEnvoyes['formulaires']))
	{
		$formulairesEnvoyes['formulaires'] = array ();
	}
	
	if (!isset($formulairesEnvoyes['formulaires']['id']))
	{
		$formulairesEnvoyes['formulaires']['id'] = array ();
	}
	
	if (!in_array($idFormulaire, $formulairesEnvoyes['formulaires']['id']))
	{
		$contenuFichier .= "id[]=$idFormulaire\n";
	}
	
	if (!empty($formulairesEnvoyes['formulaires']['id']))
	{
		foreach ($formulairesEnvoyes['formulaires']['id'] as $idFormulaireEnvoye)
		{
			$contenuFichier .= "id[]=$idFormulaireEnvoye\n";
		}
	}
	
	@file_put_contents($fichierConfig, $contenuFichier);
	
	return '';
}

/*
Met à jour les langues actives dans le fichier `init.inc.php`. Si `$initIncPhpFourni` est vide, enregistre les modifications et retourne le résultat sous forme de message concaténable dans `$messagesScript`, sinon retourne un tableau dont le premier élément contient le message concaténable dans `$messagesScript`; et le second, le contenu du fichier `init.inc.php` modifié.
*/
function majLanguesActives($racine, $urlRacine, $langues, $initIncPhpFourni = '')
{
	$messagesScript = '';
	
	if (empty($initIncPhpFourni) && !file_exists($racine . '/init.inc.php'))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Mise à jour des langues actives impossible, puisque le fichier %1\$s n'existe pas."), '<code>init.inc.php</code>') . "</li>\n";
	}
	elseif (empty($langues))
	{
		$messagesScript .= '<li class="erreur">' . T_("Mise à jour des langues actives impossible, puisqu'aucune langue n'a été fournie.") . "</li>\n";
	}
	else
	{
		if (empty($initIncPhpFourni))
		{
			$initIncPhp = @file_get_contents($racine . '/init.inc.php');
		}
		else
		{
			$initIncPhp = $initIncPhpFourni;
		}
		
		if ($initIncPhp === FALSE)
		{
			if (empty($initIncPhpFourni))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>init.inc.php</code>') . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . T_("Le contenu fourni ne peut être analysé.") . "</li>\n";
			}
		}
		else
		{
			$erreurInit = FALSE;
			preg_match_all('/^\s*(#|\/\/)?\s*\$accueil\[\'([a-z]{2})\'\]\s*=\s*([^;]+);/m', $initIncPhp, $resultatAccueil, PREG_SET_ORDER);
			$languesAccueil = array ();
			
			foreach ($resultatAccueil as $resultatAccueilTableauLangue)
			{
				$resultatAccueilLangue = $resultatAccueilTableauLangue[2];
				$languesAccueil[$resultatAccueilLangue] = eval('return ' . $resultatAccueilTableauLangue[3] . ';');
			}
			
			$langueAccueilChoisie = FALSE;
			
			foreach ($languesAccueil as $langueAccueil => $urlLangueAccueil)
			{
				if (in_array($langueAccueil, $langues))
				{
					$langueAccueilChoisie = TRUE;
					break;
				}
			}
			
			if (!$langueAccueilChoisie)
			{
				$erreurInit = TRUE;
				$messagesScript .= '<li class="erreur">' . T_("Mise à jour des langues actives impossible, puisqu'aucune langue n'a été fournie.") . "</li>\n";
			}
			else
			{
				foreach ($languesAccueil as $langueAccueil => $urlLangueAccueil)
				{
					if (in_array($langueAccueil, $langues))
					{
						$initIncPhp = preg_replace('~^\s*(#|//)?\s*(' . preg_quote('$accueil[\'' . $langueAccueil . '\']', '~') . ')~m', '$2', $initIncPhp);

					}
					else
					{
						$initIncPhp = preg_replace('~^\s*(#|//)?\s*(' . preg_quote('$accueil[\'' . $langueAccueil . '\']', '~') . ')~m', '#$2', $initIncPhp);
					}
					
					$cheminLangueAccueil = supprimeUrlRacine($urlRacine, $urlLangueAccueil);
					
					if (!empty($cheminLangueAccueil))
					{
						$cheminLangueAccueil = $racine . '/' . $cheminLangueAccueil;
					}
					else
					{
						$cheminLangueAccueil = $racine;
					}
					
					if (file_exists($cheminLangueAccueil) && $cheminLangueAccueil != $racine)
					{
						if (file_exists($cheminLangueAccueil . '/.htaccess'))
						{
							$htaccess = @file_get_contents($cheminLangueAccueil . '/.htaccess');
						}
						else
						{
							$htaccess = '';
						}
					
						if ($htaccess !== FALSE)
						{
							if (in_array($langueAccueil, $langues))
							{
								$htaccess = preg_replace('/\s*Deny from all/', '', $htaccess);
							}
							elseif (!preg_match('/^\s*Deny from all/m', $htaccess))
							{
								if (!empty($htaccess))
								{
									$htaccess .= "\n";
								}
							
								$htaccess .= 'Deny from all';
							}
						
							if (!empty($htaccess))
							{
								if (@file_put_contents($cheminLangueAccueil . '/.htaccess', $htaccess) === FALSE)
								{
									$messagesScript .= '<li class="erreur">' . sprintf(T_("Mise à jour du fichier %1\$s impossible."), '<code>' . securiseTexte("$cheminLangueAccueil/.htaccess") . '</code>') . "</li>\n";
								}
								else
								{
									$messagesScript .= '<li>' . sprintf(T_("Mise à jour du fichier %1\$s effectuée."), '<code>' . securiseTexte("$cheminLangueAccueil/.htaccess") . '</code>') . "</li>\n";
								}
							}
							elseif (file_exists($cheminLangueAccueil . '/.htaccess'))
							{
								if (@unlink($cheminLangueAccueil . '/.htaccess'))
								{
									$messagesScript .= '<li>' . sprintf(T_("Suppression du fichier %1\$s effectuée."), '<code>' . securiseTexte("$cheminLangueAccueil/.htaccess") . '</code>') . "</li>\n";
								}
								else
								{
									$messagesScript .= '<li class="erreur">' . sprintf(T_("Suppression du fichier %1\$s impossible. Ce fichier est maintenant inutile."), '<code>' . securiseTexte("$cheminLangueAccueil/.htaccess") . '</code>') . "</li>\n";
								}
							}
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Mise à jour du fichier %1\$s impossible."), '<code>' . securiseTexte("$cheminLangueAccueil/.htaccess") . '</code>') . "</li>\n";
						}
					}
				}
			}
			
			if (empty($initIncPhpFourni) && !$erreurInit)
			{
				if (@file_put_contents($racine . '/init.inc.php', $initIncPhp) === FALSE)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>init.inc.php</code>') . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Mise à jour des langues actives dans le fichier %1\$s effectuée."), '<code>init.inc.php</code>') . "</li>\n";
				}
			}
		}
	}
	
	if (empty($initIncPhpFourni))
	{
		return $messagesScript;
	}
	else
	{
		return array ($messagesScript, $initIncPhp);
	}
}

/*
Accepte en paramètre un fichier dont le contenu est rédigé en Markdown, et retourne le contenu de ce fichier converti en HTML.
*/
function mkd($fichier)
{
	return Markdown(@file_get_contents($fichier));
}

/*
Accepte en paramètre une chaîne rédigée en Markdown, et retourne cette chaîne convertie en HTML.
*/
function mkdChaine($chaine)
{
	return Markdown($chaine);
}

/*
Retourne le menu des catégories, qui doit être entouré par la balise `ul` (seuls les `li` sont retournés).
*/
function menuCategoriesAutomatise($racine, $urlRacine, $langue, $categories, $afficherNombreArticlesCategorie, $activerCategoriesGlobales, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger)
{
	$menuCategoriesAutomatise = '';
	uksort($categories, 'strnatcasecmp');
	
	$categoriesGlobalesAactiver = array ();
	
	if ($activerCategoriesGlobales['site'])
	{
		$categoriesGlobalesAactiver[] = 'site';
	}
	
	if ($activerCategoriesGlobales['galeries'])
	{
		$categoriesGlobalesAactiver[] = 'galeries';
	}
	
	if (!empty($categoriesGlobalesAactiver))
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, $langue, $categories, $categoriesGlobalesAactiver, $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
	
	foreach ($categories as $categorie => $categorieInfos)
	{
		if (empty($categorieInfos['parent']) && isset($categorieInfos['langue']) && $categorieInfos['langue'] == $langue)
		{
			$menuCategoriesAutomatise .= htmlCategorie($urlRacine, $categories, $categorie, $afficherNombreArticlesCategorie);
		}
	}
	
	return $menuCategoriesAutomatise;
}

/*
Fait le ménage dans le message fourni et retourne le résultat.
*/
function messageDansConfigCommentaires($racine, $message, $attributNofollowLiensCommentaires)
{
	$messageDansConfig = mkdChaine($message);
	$messageDansConfig = corrigeHtml($messageDansConfig);
	require_once $racine . '/inc/htmlpurifier/HTMLPurifier.standalone.php';
	$htmlPurifierConfig = HTMLPurifier_Config::createDefault();
	$htmlPurifierConfig->set('Cache.SerializerPath', $racine . '/site/cache/htmlpurifier');
	$htmlPurifierConfig->set('HTML.Allowed', 'p,em,strong,strike,ul,ol,li,a[href],pre,code,q,blockquote,br');
	
	if ($attributNofollowLiensCommentaires)
	{
		$htmlPurifierConfig->set("HTML.Nofollow", TRUE);
	}
	
	$htmlPurifier = new HTMLPurifier($htmlPurifierConfig);
	$messageDansConfig = $htmlPurifier->purify($messageDansConfig);
	$messageDansConfig = str_replace(array ("\r\n", "\n\r", "\r"), "\n", $messageDansConfig);
	
	return trim($messageDansConfig);
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
Génère une image de dimensions données à partir d'une image source. Si les dimensions voulues de la nouvelle image sont au moins aussi grandes que celles de l'image source, il y a seulement copie et non génération, à moins que `$galerieForcerDimensionsVignette` vaille TRUE. Dans ce cas, il y a ajout de bordures blanches (ou transparentes pour les PNG) pour compléter l'espace manquant.

Retourne le résultat sous forme de message concaténable dans `$messagesScript`.

La fonction ne vérifie pas si la bibliothèque GD est bien installée.
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
			$messagesScript = sprintf(T_("Copie de <code>%1\$s</code> sous le nom <code>%2\$s</code> effectuée."), securiseTexte($nomImageSource), securiseTexte($nomNouvelleImage)) . "\n";
		}
		else
		{
			$messagesScript = sprintf(T_("Copie de <code>%1\$s</code> sous le nom <code>%2\$s</code> impossible."), securiseTexte($nomImageSource), securiseTexte($nomNouvelleImage)) . "\n";
			$erreur = TRUE;
		}
	}
	// Sinon on génère une nouvelle image avec la bibliothèque GD.
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
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
					$erreur = TRUE;
				}
				
				break;
		
			case 'image/jpeg':
				if (imagejpeg($nouvelleImage, $cheminNouvelleImage, $galerieQualiteJpg))
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
					$erreur = TRUE;
				}
				
				break;
		
			case 'image/png':
				if (imagepng($nouvelleImage, $cheminNouvelleImage, 9))
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> effectuée."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
				}
				else
				{
					$messagesScript = sprintf(T_("Création de <code>%1\$s</code> à partir de <code>%2\$s</code> impossible."), securiseTexte($nomNouvelleImage), securiseTexte($nomImageSource)) . "\n";
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
	$nombre = number_format($octets / 1024, 1, ',', '');
	
	if ($nombre == '0,0')
	{
		$nombre = 0;
	}
	
	return $nombre;
}

/*
Conversion des octets en Mio.
*/
function octetsVersMio($octets)
{
	$nombre = number_format($octets / 1048576, 1, ',', '');
	
	if ($nombre == '0,0')
	{
		$nombre = 0;
	}
	
	return $nombre;
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
function pagination($racine, $urlRacine, $type, $paginationAvecFond, $paginationArrondie, $nombreElements, $elementsParPage, $urlSansGet, $baliseTitle, $description)
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
			
			$pagination['pagination'] .= '<img class="paginationPrecedent" src="' . $srcPrecedent . '" alt="' . T_("Page précédente") . '" />';
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
			
			$pagination['pagination'] .= '<img class="paginationSuivant" src="' . $srcSuivant . '" alt="' . T_("Page suivante") . '" />';
		}
		elseif ($type == 'texte')
		{
			$pagination['pagination'] .= T_("Page suivante");
		}
		
		$pagination['pagination'] .= '</a>';
	}
	
	$classesPagination = '';
	
	if (!empty($pagination['pagination']))
	{
		if ($paginationAvecFond)
		{
			$classesPagination .= 'blocAvecFond ';
		}
		
		if ($paginationArrondie)
		{
			$classesPagination .= 'blocArrondi';
		}
	}
	
	if (!empty($pagination['pagination']))
	{
		$pagination['pagination'] = "<div class=\"pagination $classesPagination\">\n" . $pagination['pagination'] . '</div><!-- /.pagination -->' . "\n";
	}
	
	return $pagination;
}

/*
Retourne le texte supplémentaire d'une image pour le message envoyé par le module de partage (par courriel).
*/
function partageCourrielSupplementImage($urlRacine, $idGalerieDossier, $image, $galerieLegendeMarkdown)
{
	$messagePartageCourrielSupplement = '';
	$titreImage = titreImage($image);
	
	if (!empty($image['vignetteNom']))
	{
		$vignetteNom = $image['vignetteNom'];
	}
	else
	{
		$vignetteNom = nomSuffixe($image['intermediaireNom'], '-vignette');
	}
	
	$vignetteAlt = partageCourrielSupplementImageAlt($image);
	$imgSrc = $urlRacine;
	
	if ($idGalerieDossier != 'demo')
	{
		$imgSrc .= '/site';
	}
	
	$imgSrc .= '/fichiers/galeries/' . encodeTexte("$idGalerieDossier/$vignetteNom");
	
	$messagePartageCourrielSupplement .= "<p style=\"text-align: center;\"><img src=\"$imgSrc\" alt=\"$vignetteAlt\" /></p>\n";
	
	if (!empty($image['titre']))
	{
		$messagePartageCourrielSupplement .= '<p>' . $image['titre'] . "</p>\n";
	}
	
	if (!empty($image['intermediaireLegende']))
	{
		$messagePartageCourrielSupplement .= intermediaireLegende($image['intermediaireLegende'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['intermediaireAlt']))
	{
		$messagePartageCourrielSupplement .= intermediaireLegende($image['intermediaireAlt'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['vignetteAlt']))
	{
		$messagePartageCourrielSupplement .= intermediaireLegende($image['vignetteAlt'], $galerieLegendeMarkdown);
	}
	elseif (!empty($image['pageIntermediaireDescription']))
	{
		$messagePartageCourrielSupplement .= '<p>' . $image['pageIntermediaireDescription'] . "</p>\n";
	}
	elseif (!empty($image['pageIntermediaireBaliseTitle']))
	{
		$messagePartageCourrielSupplement .= '<p>' . $image['pageIntermediaireBaliseTitle'] . "</p>\n";
	}
	
	$messagePartageCourrielSupplement = "<div style=\"border: 1px solid #cccccc; border-radius: 2px; padding: 10px;\">$messagePartageCourrielSupplement</div>\n";
	$messagePartageCourrielSupplement .= '<p><a href="' . variableGet(0, url(), 'action') . '">' . sprintf(T_("Voyez l'image %1\$s en plus grande taille!"), '<em>' . securiseTexte($titreImage) . '</em>') . '</a> ' . T_("En espérant qu'elle vous intéresse!") . "</p>\n";
	
	return $messagePartageCourrielSupplement;
}

/*
Retourne le texte alternatif d'une image pour le message envoyé par le module de partage (par courriel).
*/
function partageCourrielSupplementImageAlt($image)
{
	if (!empty($image['vignetteAlt']))
	{
		$imgAlt = $image['vignetteAlt'];
	}
	elseif (!empty($image['intermediaireAlt']))
	{
		$imgAlt = $image['intermediaireAlt'];
	}
	else
	{
		$titreImage = titreImage($image);
		$imgAlt = sprintf(T_("Image %1\$s"), $titreImage);
	}
	
	return securiseTexte($imgAlt);
}

/*
Retourne le texte supplémentaire d'une page pour le message envoyé par le module de partage (par courriel).
*/
function partageCourrielSupplementPage($description, $baliseTitle, $extra = '')
{
	$messagePartageCourrielSupplement = '';
	
	if (!empty($baliseTitle))
	{
		$messagePartageCourrielSupplement .= '<p>' . $baliseTitle . "</p>\n";
	}
	
	if (!empty($description))
	{
		$messagePartageCourrielSupplement .= '<p>' . $description . "</p>\n";
	}
	
	if (!empty($extra))
	{
		$messagePartageCourrielSupplement .= "$extra\n";
	}
	
	$urlPageSansAction = variableGet(0, url(), 'action');
	$urlPageSansActionCode = '<p><a href="' . $urlPageSansAction . '">' . $urlPageSansAction . "</a></p>\n";
	$messagePartageCourrielSupplement = "<div style=\"border: 1px solid #cccccc; border-radius: 2px; padding: 10px;\">$messagePartageCourrielSupplement$urlPageSansActionCode</div>\n";
	$messagePartageCourrielSupplement .= '<p> ' . T_("En espérant que cette page vous intéresse!") . "</p>\n";
	
	return $messagePartageCourrielSupplement;
}

/*
Retourne un tableau de liens de marque-pages et de réseaux sociaux pour la page en cours. Les liens ont été en partie récupérés dans le module Service links pour Drupal, sous licence GPL. Voir <http://drupal.org/project/service_links>.
*/
function partageReseaux($url, $titre)
{
	$url = urlencode($url);
	$titre = urlencode($titre);
	
	if ($titre == $url)
	{
		$titre = '';
	}
	
	$liens = array ();
	
	$liens['Delicious'] = array (
		'id' => 'partageDelicious',
		'nom' => 'Delicious',
		'lien' => "http://delicious.com/post?url=$url&amp;title=$titre",
	);
	
	$liens['Digg'] = array (
		'id' => 'partageDigg',
		'nom' => 'Digg',
		'lien' => "http://digg.com/submit?phase=2&amp;url=$url&amp;title=$titre",
	);
	
	$liens['Facebook'] = array (
		'id' => 'partageFacebook',
		'nom' => 'Facebook',
		'lien' => "http://www.facebook.com/sharer.php?u=$url&amp;t=$titre",
	);
	
	$liens['GooglePlus'] = array (
		'id' => 'partageGooglePlus',
		'nom' => 'Google+',
		'lien' => "https://plus.google.com/share?url=$url",
	);
	
	$liens['Identica'] = array (
		'id' => 'partageIdentica',
		'nom' => 'Identi.ca',
		'lien' => "http://identi.ca/index.php?action=newnotice&amp;status_textarea=$titre $url",
	);
	
	$liens['Linkedin'] = array (
		'id' => 'partageLinkedin',
		'nom' => 'LinkedIn',
		'lien' => "http://www.linkedin.com/shareArticle?mini=true&amp;url=$url&amp;title=$titre",
	);
	
	$liens['MySpace'] = array (
		'id' => 'partageMySpace',
		'nom' => 'MySpace',
		'lien' => "http://www.myspace.com/index.cfm?fuseaction=postto&amp;t=$titre&amp;u=$url",
	);
	
	$liens['Reddit'] = array (
		'id' => 'partageReddit',
		'nom' => 'Reddit',
		'lien' => "http://www.reddit.com/submit?url=$url&amp;title=$titre",
	);
	
	$liens['StumbleUpon'] = array (
		'id' => 'partageStumbleUpon',
		'nom' => 'StumbleUpon',
		'lien' => "http://www.stumbleupon.com/submit?url=$url&amp;title=$titre",
	);
	
	$liens['Twitter'] = array (
		'id' => 'partageTwitter',
		'nom' => 'Twitter',
		'lien' => "http://twitter.com/home/?status=$url+--+$titre",
	);
	
	return $liens;
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
	
	include_once $racine . '/inc/php-gettext/gettext.inc.php';
	
	$locale = locale($langue);
	T_setlocale(LC_MESSAGES, $locale);
	$domain = 'squeletml';
	T_bindtextdomain($domain, $racine . '/locale');
	T_bind_textdomain_codeset($domain, 'UTF-8');
	T_textdomain($domain);
	
	return TRUE;
}

/*
Retourne la profondeur d'une page par rapport à l'URL racine du site. Par exemple, si l'URL racine est:

	http://localhost/serveur_local/squeletml

et que l'URL de la page est:

	http://localhost/serveur_local/squeletml/categories/sous-categorie/page.php

la fonction va retourner `2`.
*/
function profondeurPage($urlRacine, $url)
{
	$urlRelative = supprimeUrlRacine($urlRacine, $url);
	
	return substr_count($urlRelative, '/');
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

Le paramètre `$ajouterLienVersPublication` peut valoir TRUE ou FALSE. S'il vaut TRUE, un lien est ajouté vers la page Web de la publication en question.

Le paramètre `$ajouterLienPlus` peut valoir TRUE ou FALSE. S'il vaut TRUE, un lien est ajouté vers la liste complète des publications pour le type donné (par exemple vers la liste de toutes les pages appartenant à une catégorie).

Aussi, une galerie doit être présente dans le flux RSS global des galeries pour que la fonction puisse lister ses images, car c'est le seul fichier faisant un lien entre une galerie et sa page web. Voir la section «Syndication globale des galeries» de la documentation pour plus de détails.
*/
function publicationsRecentes($racine, $urlRacine, $langue, $type, $id, $nombreVoulu, $ajouterLienVersPublication, $ajouterLienPlus, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $dureeCache, $estPageCron)
{
	$html = '';
	$dossierTmp = "$racine/site/cache/publications-recentes-$langue-$type-" . encodeTexte($id);
	
	// Éviter une boucle infinie.
	if (!@mkdir($dossierTmp))
	{
		return $html;
	}
	
	if ($type == 'categorie')
	{
		$itemsFluxRss = array ();
		$lienDesactive = FALSE;
		$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
		if (!empty($categories) && !empty($categories[$id]['pages']))
		{
			$nombreReel = count($categories[$id]['pages']);
			
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
					$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", "$urlRacine/$page", FALSE, 600, $dureeCache, $estPageCron);
					
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
					$html .= '<li>';
					
					if ($ajouterLienVersPublication)
					{
						$html .= '<a href="' . $valeur['link'] . '">' . desecuriseTexte($valeur['title']) . '</a>';
					}
					else
					{
						$html .= desecuriseTexte($valeur['title']);
					}
					
					$html .= "</li>\n";
				}
				
				if (!empty($html))
				{
					if ($ajouterLienPlus && !$lienDesactive)
					{
						$categories[$id]['url'] = urlCat($categories[$id], $id);
						$lien = $urlRacine . '/' . $categories[$id]['url'];
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
	}
	elseif ($type == 'galerie')
	{
		$lienDesactive = FALSE;
		$urlGalerie = '';
		$listeGaleriesRss = fluxRssGlobalGaleries($racine);
		
		if ($id == 'démo' || in_array($id, $listeGaleriesRss))
		{
			$urlGalerie = urlGalerie(0, $racine, $urlRacine, $id, $langue);
		}
		
		if (!empty($urlGalerie))
		{
			$idDossier = idGalerieDossier($racine, $id);
			$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idDossier), TRUE);
			
			if ($tableauGalerie !== FALSE)
			{
				$racineImgSrc = $racine;
				$urlImgSrc = $urlRacine;
				
				if ($id != 'démo')
				{
					$racineImgSrc .= '/site';
					$urlImgSrc .= '/site';
				}
				
				$racineImgSrc .= '/fichiers/galeries/' . $idDossier;
				$urlImgSrc .= '/fichiers/galeries/' . encodeTexte($idDossier);
				$vignettes = array ();
				
				foreach ($tableauGalerie as $image)
				{
					$titreImage = titreImage($image);
					$title = securiseTexte($titreImage);
					$alt = securiseTexte($titreImage);
					
					if (!empty($image['dateAjout']))
					{
						$date = $image['dateAjout'];
					}
					else
					{
						$date = date('Y-m-d H:i', @filemtime($racineImgSrc . '/' . $image['intermediaireNom']));
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
							$width = securiseTexte($image['vignetteLargeur']);
						}
			
						if (!empty($image['vignetteHauteur']))
						{
							$height = securiseTexte($image['vignetteHauteur']);
						}
					}
					else
					{
						list ($width, $height) = @getimagesize($racineImgSrc . '/' . $vignetteNom);
					}
					
					$lienVignette = variableGet(2, $urlGalerie, 'image', idImage($image));
					$vignettesImg = '<img src="' . $urlImgSrc . '/' . encodeTexte($vignetteNom) . '" alt="' . $alt . '" width="' . $width . '" height="' . $height . '" />';
					$vignettesCode = '<li>';
					
					if ($ajouterLienVersPublication)
					{
						$vignettesCode .= '<a href="' . $lienVignette . '" title="' . $title . '">' . $vignettesImg . '</a>';
					}
					else
					{
						$vignettesCode .= $vignettesImg;
					}
					
					$vignettesCode .= "</li>\n";
					$vignettes[] = array (
						'code' => $vignettesCode,
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
					if ($ajouterLienPlus && !$lienDesactive)
					{
						$codeLien = '<p class="publicationsRecentesLien"><a href="' . $urlGalerie . '">' . T_("Voir plus d'images") . "</a></p>\n";
					}
					else
					{
						$codeLien = '';
					}
					
					$html = "<div class=\"publicationsRecentes publicationsRecentesGalerie\">\n<ul>\n$html</ul>\n<div class=\"sep\"></div>\n$codeLien</div>\n";
				}
			}
		}
	}
	elseif ($type == 'galeries')
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
				preg_match('/<img src="([^"]+)"/', desecuriseTexte($itemsFluxRss[$i]['description']), $resultat);
				$intermediaireSrc = decodeTexte($resultat[1]);
				$intermediaireNom = superBasename($intermediaireSrc);
				$idGalerieDossier = superBasename(str_replace("/$intermediaireNom", '', $intermediaireSrc));
			
				if (!empty($idGalerieDossier) && cheminConfigGalerie($racine, $idGalerieDossier))
				{
					$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerieDossier), TRUE);
				
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
							$width = securiseTexte($tableauGalerie[$intermediaireNom]['vignetteLargeur']);
						}
			
						if (!empty($tableauGalerie[$intermediaireNom]['vignetteHauteur']))
						{
							$height = securiseTexte($tableauGalerie[$intermediaireNom]['vignetteHauteur']);
						}
					}
					else
					{
						list ($width, $height) = @getimagesize($racine . '/site/fichiers/galeries/' . $idGalerieDossier . '/' . $vignetteNom);
					}
					
					$vignetteImg = '<img src="' . $urlRacine . '/site/fichiers/galeries/' . encodeTexte("$idGalerieDossier/$vignetteNom") . '" alt="' . $itemsFluxRss[$i]['title'] . '" width="' . $width . '" height="' . $height . '" />';
				}
				
				$html .= '<li>';
				
				if ($ajouterLienVersPublication)
				{
					$html .= '<a href="' . $itemsFluxRss[$i]['link'] . '" title="' . $itemsFluxRss[$i]['title'] . '">' . "$vignetteImg</a>";
				}
				else
				{
					$html .= $vignetteImg;
				}
				
				$html .= "</li>\n";
			}
		
			if (!empty($html))
			{
				if ($ajouterLienPlus && !$lienDesactive)
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
					$lien = $urlRacine . '/' . $categories['galeries']['url'];
					$codeLien = '<p class="publicationsRecentesLien"><a href="' . $lien . '">' . T_("Voir plus d'images") . "</a></p>\n";
				}
				else
				{
					$codeLien = '';
				}
				
				$html = "<div class=\"publicationsRecentes publicationsRecentesGaleries\">\n<ul>\n$html</ul>\n<div class=\"sep\"></div>\n$codeLien</div>\n";
			}
		}
	}
	elseif ($type == 'site')
	{
		$lienDesactive = FALSE;
		$itemsFluxRss = array ();
		$pages = super_parse_ini_file(cheminConfigFluxRssGlobalSite($racine), TRUE);
		
		if (!empty($pages) && !empty($pages[$langue]['pages']))
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
					$fluxRssPageTableauBrut = fluxRssPageTableauBrut($racine, $urlRacine, "$racine/$page", $urlRacine . '/' . $page, FALSE, 600, $dureeCache, $estPageCron);
				
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
					$html .= '<li>';
					
					if ($ajouterLienVersPublication)
					{
						$html .= '<a href="' . $valeur['link'] . '">' . desecuriseTexte($valeur['title']) . '</a>';
					}
					else
					{
						$html .= desecuriseTexte($valeur['title']);
					}
					
					$html .= "</li>\n";
				}
				
				if (!empty($html))
				{
					if ($ajouterLienPlus && !$lienDesactive)
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
						$lien = $urlRacine . '/' . $categories['site']['url'];
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
	}
	
	@rmdir($dossierTmp);
	
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
Retourne TRUE si le RSS est activé pour la galerie demandée, sinon retourne FALSE.
*/
function rssGalerieActif($racine, $idGalerie)
{
	$rssGalerie = FALSE;
	$listeGaleries = listeGaleries($racine, $idGalerie);
	
	if (!empty($listeGaleries[$idGalerie]['rss']) && $listeGaleries[$idGalerie]['rss'] == 1)
	{
		$rssGalerie = TRUE;
	}
	
	return $rssGalerie;
}

/*
Si la valeur passée en paramètre est une chaîne de caractères, retourne la chaîne traitée pour un affichage sécuritaire à l'écran, sinon si la valeur passée en paramètre est un tableau, retourne un tableau dont chaque élément a été sécurisé, sinon si la valeur passée en paramètre n'est ni une chaîne ni un tableau, retourne une chaîne vide.

La fonction opposée est `desecuriseTexte()`.
*/
function securiseTexte($texte)
{
	if (is_array($texte))
	{
		return array_map('securiseTexte', $texte);
	}
	elseif (is_int($texte) || is_string($texte))
	{
		return htmlspecialchars($texte, ENT_COMPAT, 'UTF-8');
	}
	
	return '';
}

/*
Récupère le code XHTML d'une page locale, comme si elle était visitée dans un navigateur.
*/
function simuleVisite($racine, $urlRacine, $urlAsimuler, $dureeCache, $desactiverLectureCachePartiel = FALSE, $estPageCron = FALSE)
{
	$estVisiteSimulee = TRUE;
	$urlAsimuler = str_replace('&amp;', '&', $urlAsimuler);
	$cheminRelatifPage = supprimeUrlRacine($urlRacine, $urlAsimuler);
	$cheminRelatifPage = preg_replace('/\?.*/', '', $cheminRelatifPage);
	$cheminRelatifPage = preg_replace('/\#.*/', '', $cheminRelatifPage);
	$cheminPage = $racine . '/' . decodeTexte($cheminRelatifPage);
	
	if (preg_match('#/$#', $cheminPage))
	{
		$listeFichiersIndex = listeFichiersIndex();
		
		foreach ($listeFichiersIndex as $fichierIndex)
		{
			if (file_exists($cheminPage . $fichierIndex))
			{
				$cheminPage .= $fichierIndex;
				break;
			}
		}
	}
	
	$codePage = '';
	$dossierTmp = "$racine/site/cache/simule-visite-" . encodeTexte($cheminPage, TRUE);
	
	// Éviter une boucle infinie.
	if (!@mkdir($dossierTmp))
	{
		return $codePage;
	}
	
	if (is_file($cheminPage))
	{
		$dossierActuel = getcwd();
		chdir(dirname($cheminPage));
		
		# Ajustement des variables relatives à l'URL.
		
		$infosUrl = parse_url($urlAsimuler);
		
		if ($infosUrl !== FALSE)
		{
			if (!isset($_SERVER))
			{
				$_SERVER = array ();
			}
			
			$_SERVER_TMP = $_SERVER;
			
			if (isset($infosUrl['scheme']) && strtolower($infosUrl['scheme']) == 'https')
			{
				$_SERVER['HTTPS'] = 1;
			}
			else
			{
				$_SERVER['HTTPS'] = '';
			}
			
			if (isset($infosUrl['host']))
			{
				$_SERVER['SERVER_NAME'] = $infosUrl['host'];
			}
			else
			{
				$_SERVER['SERVER_NAME'] = '';
			}
			
			if (isset($infosUrl['port']))
			{
				$_SERVER['SERVER_PORT'] = $infosUrl['port'];
			}
			else
			{
				$_SERVER['SERVER_PORT'] = '';
			}
			
			if (isset($infosUrl['path']))
			{
				$_SERVER['REQUEST_URI'] = $infosUrl['path'];
			}
			else
			{
				$_SERVER['REQUEST_URI'] = '';
			}
			
			if (!isset($_GET))
			{
				$_GET = array ();
			}
			
			$_GET_TMP = $_GET;
			unset($_GET);
			
			if (isset($infosUrl['query']))
			{
				$_SERVER['REQUEST_URI'] .= '?' . $infosUrl['query'];
				parse_str($infosUrl['query'], $_GET);
			}
		}
		
		ob_start();
		$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlAsimuler);
	
		if ($dureeCache && file_exists($cheminFichierCache) && !cacheExpire($cheminFichierCache, $dureeCache) && !$estPageCron)
		{
			@readfile($cheminFichierCache);
		}
		else
		{
			include $cheminPage;
		}
		
		$codePage = ob_get_contents();
		ob_end_clean();
		chdir($dossierActuel);
		
		# Restauration des variables relatives à l'URL.
		if ($infosUrl !== FALSE)
		{
			$_SERVER = $_SERVER_TMP;
			unset($_SERVER_TMP);
			$_GET = $_GET_TMP;
			unset($_GET_TMP);
		}
	}
	
	@rmdir($dossierTmp);
	
	return $codePage;
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
			
			if (strpos($ligne, '# Ajout automatique de Squeletml (maintenance). Ne pas modifier.') === 0)
			{
				return TRUE;
			}
		}
		
		fclose($fic);
	}
	
	return FALSE;
}

/*
Retourne `TRUE` si l'adresse du site Web a une forme valide, sinon retourne `FALSE`.
*/
function siteWebValide($site)
{
	return preg_match('#^https?://[^\s\.]+\.[^\s]+$#', $site);
}

/*
Simule la fonction `basename()` sans dépendre de la locale. Merci à <http://drupal.org/node/278425>.
*/
function superBasename($chemin, $suffixe = '')
{
	$chemin = preg_replace('#^.+[\\/]#', '', $chemin);
	
	if ($suffixe)
	{
		$chemin = preg_replace('/' . preg_quote($suffixe, '/') . '$/', '', $chemin);
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
			
			if (preg_match('/^\s*\[(.+)\]\s*$/', $ligne, $resultat) && $creerSections)
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
Supprime les balises HTML du code fourni, et retourne le résultat.
*/
function supprimeBalisesHtml($codeHtml)
{
	return trim(strip_tags($codeHtml));
}

/*
Retourne le code HTML sans les commentaires.
*/
function supprimeCommentairesHtml($html)
{
	$dom = str_get_html($html);
	
	if (method_exists($dom, 'find'))
	{
		foreach ($dom->find('comment') as $commentaire)
		{
			$commentaire->outertext = '';
		}
		
		$html = $dom->save();
		$dom->clear();
	}
	
	unset($dom);
	
	return $html;
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
Supprime s'il y a lieu l'URL racine du début de l'URL fournie, et retourne le résultat.
*/
function supprimeUrlRacine($urlRacine, $urlAanalyser)
{
	return preg_replace('#^' . preg_quote($urlRacine, '#') . '/?#', '', $urlAanalyser);
}

/*
Transforme un fichier de configuration `.ini` d'une galerie en tableau PHP. Chaque section du fichier `.ini` devient un tableau dans le tableau principal. Le titre d'une section est transformé en paramètre `intermediaireNom`. Si `$exclure` vaut TRUE, ne tient pas compte des sections ayant un paramètre `exclure=oui` ou `exclure=1`. Par exemple, le fichier `.ini` suivant:

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
			if (!$exclure || !(isset($infos['exclure']) && ($infos['exclure'] == 1 || strtolower($infos['exclure']) == 'oui')))
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
Génère une table des matières pour le code HTML fourni, et retourne ce dernier avec la table incluse.

Inspiré de <http://stackoverflow.com/a/4912737/643933>.
*/
function tableDesMatieres($codeHtml, $parent, $tDmBaliseTable, $tDmBaliseTitre, $tDmNiveauDepart, $tDmNiveauArret)
{
	$dom = str_get_html($codeHtml);
	
	if (method_exists($dom, 'find'))
	{
		$parent = $dom->find($parent, 0);
		
		if ($tDmNiveauDepart < 1 || $tDmNiveauDepart > 6)
		{
			$tDmNiveauDepart = 1;
		}
		
		if ($tDmNiveauArret < 1 || $tDmNiveauArret > 6)
		{
			$tDmNiveauArret = 6;
		}
		
		if ($tDmNiveauArret < $tDmNiveauDepart)
		{
			$tDmNiveauArret = $tDmNiveauDepart;
		}
		
		$balisesAchercher = "h$tDmNiveauDepart";
		
		for ($i = $tDmNiveauDepart + 1; $i <= $tDmNiveauArret; $i++)
		{
			$balisesAchercher .= ", h$i";
		}
		
		$tableDesMatieres = '';
		$niveauPrecedent = 0;
		$premiereBalise = TRUE;
		
		foreach ($parent->find($balisesAchercher) as $h)
		{
			$contenuH = trim($h->innertext);
			
			if (!empty($h->id))
			{
				$idH = $h->id;
			}
			else
			{
				$idH = filtreChaine(supprimeBalisesHtml($contenuH));
				$h->id = $idH;
			}
			
			$niveauActuel = intval($h->tag[1]);
			
			if ($niveauActuel > $niveauPrecedent)
			{
				if ($premiereBalise)
				{
					$tableDesMatieres .= "<$tDmBaliseTable id=\"tableDesMatieresBdCorps\" class=\"bDcorps afficher\">\n";
					$premiereBalise = FALSE;
				}
				else
				{
					$tableDesMatieres .= "<$tDmBaliseTable>\n";
				}
			}
			else
			{
				$tableDesMatieres .= str_repeat("</li></$tDmBaliseTable>\n", max($niveauPrecedent - $niveauActuel, 0));
				$tableDesMatieres .= "</li>\n";
			}
			
			$tableDesMatieres .= "<li><a href=\"#$idH\">$contenuH</a>";
			$niveauPrecedent = $niveauActuel;
		}
		
		$tableDesMatieres .= str_repeat("</li></$tDmBaliseTable>\n", max($niveauPrecedent - ($tDmNiveauDepart - 1), 0));
		
		if (!empty($tableDesMatieres))
		{
			$contenuTableDesMatieres = "<$tDmBaliseTitre id=\"tableDesMatieresBdTitre\" class=\"bDtitre\">" . T_("Table des matières") . "</$tDmBaliseTitre>\n$tableDesMatieres";
			
			if ($divTableDesMatieres = $parent->find('div#tableDesMatieres', 0))
			{
				$divTableDesMatieres->innertext = $divTableDesMatieres->innertext . $contenuTableDesMatieres;
			}
			else
			{
				$tableDesMatieres = "<div id=\"tableDesMatieres\">\n$contenuTableDesMatieres</div><!-- /#tableDesMatieres -->\n";
				
				if ($chapeau = $parent->find('div.chapeau', 0))
				{
					$chapeau->outertext = $chapeau->outertext . $tableDesMatieres;
				}
				elseif ($debutInterieurContenu = $parent->find('div#debutInterieurContenu', 0))
				{
					$debutInterieurContenu->outertext = $debutInterieurContenu->outertext . $tableDesMatieres;
				}
				elseif ($h1 = $parent->find('h1', 0))
				{
					$h1->outertext = $h1->outertext . $tableDesMatieres;
				}
				else
				{
					$parent->first_child()->outertext = $tableDesMatieres . $parent->first_child()->outertext;
				}
			}
			
			$codeHtml = $dom->save();
		}
		
		$dom->clear();
	}
	
	unset($dom);
	
	return $codeHtml;
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
S'assure que la langue fournie soit en premier dans le tableau `$accueil`. Retourne le tableau trié.
*/
function triTableauAccueil($accueil, $langue)
{
	$tableauTrie = $accueil;
	
	if (isset($tableauTrie[$langue]))
	{
		unset($tableauTrie[$langue]);
		$tableauTrie = array_merge(array ($langue => $accueil[$langue]), $tableauTrie);
	}
	
	return $tableauTrie;
}

/*
Tronque le texte à la taille spécifiée et retourne le résultat.

Provient de la fonction `truncate()` du fichier `lib/Cake/Utility/String.php` de CakePHP 2.2.1, sous licence MIT. Le commentaire original de la fonction est le suivant:

Truncates text.

Cuts a string to the length of $length and replaces the last characters
with the ending if the text is longer than length.

### Options:

- `ending` Will be used as Ending and appended to the trimmed string
- `exact` If false, $text will not be cut mid-word
- `html` If true, HTML tags would be handled correctly

@param string $text String to truncate.
@param integer $length Length of returned string, including ellipsis.
@param array $options An array of html attributes and options.
@return string Trimmed string.
@link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::truncate
*/
function tronqueTexte($text, $length, $options = array(), $commentairesHtmlSupprimes = FALSE)
{
	$default = array(
		'ending' => ' […]', 'exact' => FALSE, 'html' => TRUE
	);
	$options = array_merge($default, $options);
	extract($options);
	
	if (!empty($text) && $html && !$commentairesHtmlSupprimes)
	{
		$text = supprimeCommentairesHtml($text);
	}
	
	if ($html) {
		if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		$totalLength = mb_strlen(strip_tags($ending));
		$openTags = array();
		$truncate = '';

		preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
		foreach ($tags as $tag) {
			if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
				if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
					array_unshift($openTags, $tag[2]);
				} elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
					$pos = array_search($closeTag[1], $openTags);
					if ($pos !== false) {
						array_splice($openTags, $pos, 1);
					}
				}
			}
			$truncate .= $tag[1];

			$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
			if ($contentLength + $totalLength > $length) {
				$left = $length - $totalLength;
				$entitiesLength = 0;
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
					foreach ($entities[0] as $entity) {
						if ($entity[1] + 1 - $entitiesLength <= $left) {
							$left--;
							$entitiesLength += mb_strlen($entity[0]);
						} else {
							break;
						}
					}
				}

				$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
				break;
			} else {
				$truncate .= $tag[3];
				$totalLength += $contentLength;
			}
			if ($totalLength >= $length) {
				break;
			}
		}
	} else {
		if (mb_strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
		}
	}
	if (!$exact) {
		$spacepos = mb_strrpos($truncate, ' ');
		if ($html) {
			$truncateCheck = mb_substr($truncate, 0, $spacepos);
			$lastOpenTag = mb_strrpos($truncateCheck, '<');
			$lastCloseTag = mb_strrpos($truncateCheck, '>');
			if ($lastOpenTag > $lastCloseTag) {
				preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
				$lastTag = array_pop($lastTagMatches[0]);
				$spacepos = mb_strrpos($truncate, $lastTag) + mb_strlen($lastTag);
			}
			$bits = mb_substr($truncate, $spacepos);
			preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
			if (!empty($droppedTags)) {
				if (!empty($openTags)) {
					foreach ($droppedTags as $closingTag) {
						if (!in_array($closingTag[1], $openTags)) {
							array_unshift($openTags, $closingTag[1]);
						}
					}
				} else {
					foreach ($droppedTags as $closingTag) {
						array_push($openTags, $closingTag[1]);
					}
				}
			}
		}
		$truncate = mb_substr($truncate, 0, $spacepos);
	}
	$truncate .= $ending;

	if ($html) {
		foreach ($openTags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}

	return $truncate;
}

/*
Retourne le type MIME du fichier.
*/
function typeMime($cheminFichier)
{
	$typeMime = '';
	
	if (function_exists('finfo_file'))
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$typeMime = finfo_file($finfo, $cheminFichier);
		finfo_close($finfo);
	}
	
	if (empty($typeMime) && function_exists('mime_content_type'))
	{
		$typeMime = mime_content_type($cheminFichier);
	}
	
	if (empty($typeMime))
	{
		$associations = typeMimeAssociations();
		
		foreach ($associations['extensions'] as $ext => $index)
		{
			if (preg_match("/\.$ext$/i", $cheminFichier))
			{
				$typeMime = $associations['mimetypes'][$index];
				break;
			}
		}
	}
	
	if (empty($typeMime))
	{
		if (is_dir($cheminFichier))
		{
			$typeMime = 'directory';
		}
		elseif (@filesize($cheminFichier) === 0)
		{
			$typeMime = 'application/x-empty';
		}
	}
	
	return $typeMime;
}

/*
Retourne des associations entre une extension et un type MIME. Provient de la fonction `file_default_mimetype_mapping()` du fichier `includes/file.mimetypes.inc` de Drupal 7. Voir <http://api.drupal.org/api/drupal/includes!file.mimetypes.inc/7>. Ce fichier est sous licence GPL version 2 ou toute version ultérieure (voir <http://drupal.org/licensing/faq/#q1>).
*/
function typeMimeAssociations()
{
	return array (
		'mimetypes' => array (
			0 => 'application/andrew-inset',
			1 => 'application/atom',
			2 => 'application/atomcat+xml',
			3 => 'application/atomserv+xml',
			4 => 'application/cap',
			5 => 'application/cu-seeme',
			6 => 'application/dsptype',
			7 => 'application/hta',
			8 => 'application/java-archive',
			9 => 'application/java-serialized-object',
			10 => 'application/java-vm',
			11 => 'application/mac-binhex40',
			12 => 'application/mathematica',
			13 => 'application/msaccess',
			14 => 'application/msword',
			15 => 'application/octet-stream',
			16 => 'application/oda',
			17 => 'application/ogg',
			18 => 'application/pdf',
			19 => 'application/pgp-keys',
			20 => 'application/pgp-signature',
			21 => 'application/pics-rules',
			22 => 'application/postscript',
			23 => 'application/rar',
			24 => 'application/rdf+xml',
			25 => 'application/rss+xml',
			26 => 'application/rtf',
			27 => 'application/smil',
			28 => 'application/vnd.cinderella',
			29 => 'application/vnd.google-earth.kml+xml',
			30 => 'application/vnd.google-earth.kmz',
			31 => 'application/vnd.mozilla.xul+xml',
			32 => 'application/vnd.ms-excel',
			33 => 'application/vnd.ms-excel.addin.macroEnabled.12',
			34 => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			35 => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			36 => 'application/vnd.ms-excel.template.macroEnabled.12',
			37 => 'application/vnd.ms-pki.seccat',
			38 => 'application/vnd.ms-pki.stl',
			39 => 'application/vnd.ms-powerpoint',
			40 => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			41 => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			42 => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			43 => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
			44 => 'application/vnd.ms-word.document.macroEnabled.12',
			45 => 'application/vnd.ms-word.template.macroEnabled.12',
			46 => 'application/vnd.ms-xpsdocument',
			47 => 'application/vnd.oasis.opendocument.chart',
			48 => 'application/vnd.oasis.opendocument.database',
			49 => 'application/vnd.oasis.opendocument.formula',
			50 => 'application/vnd.oasis.opendocument.graphics',
			51 => 'application/vnd.oasis.opendocument.graphics-template',
			52 => 'application/vnd.oasis.opendocument.image',
			53 => 'application/vnd.oasis.opendocument.presentation',
			54 => 'application/vnd.oasis.opendocument.presentation-template',
			55 => 'application/vnd.oasis.opendocument.spreadsheet',
			56 => 'application/vnd.oasis.opendocument.spreadsheet-template',
			57 => 'application/vnd.oasis.opendocument.text',
			58 => 'application/vnd.oasis.opendocument.text-master',
			59 => 'application/vnd.oasis.opendocument.text-template',
			60 => 'application/vnd.oasis.opendocument.text-web',
			61 => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			62 => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			63 => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			64 => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			65 => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			66 => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			67 => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			68 => 'application/vnd.rim.cod',
			69 => 'application/vnd.smaf',
			70 => 'application/vnd.stardivision.calc',
			71 => 'application/vnd.stardivision.chart',
			72 => 'application/vnd.stardivision.draw',
			73 => 'application/vnd.stardivision.impress',
			74 => 'application/vnd.stardivision.math',
			75 => 'application/vnd.stardivision.writer',
			76 => 'application/vnd.stardivision.writer-global',
			77 => 'application/vnd.sun.xml.calc',
			78 => 'application/vnd.sun.xml.calc.template',
			79 => 'application/vnd.sun.xml.draw',
			80 => 'application/vnd.sun.xml.draw.template',
			81 => 'application/vnd.sun.xml.impress',
			82 => 'application/vnd.sun.xml.impress.template',
			83 => 'application/vnd.sun.xml.math',
			84 => 'application/vnd.sun.xml.writer',
			85 => 'application/vnd.sun.xml.writer.global',
			86 => 'application/vnd.sun.xml.writer.template',
			87 => 'application/vnd.symbian.install',
			88 => 'application/vnd.visio',
			89 => 'application/vnd.wap.wbxml',
			90 => 'application/vnd.wap.wmlc',
			91 => 'application/vnd.wap.wmlscriptc',
			92 => 'application/wordperfect',
			93 => 'application/wordperfect5.1',
			94 => 'application/x-123',
			95 => 'application/x-7z-compressed',
			96 => 'application/x-abiword',
			97 => 'application/x-apple-diskimage',
			98 => 'application/x-bcpio',
			99 => 'application/x-bittorrent',
			100 => 'application/x-cab',
			101 => 'application/x-cbr',
			102 => 'application/x-cbz',
			103 => 'application/x-cdf',
			104 => 'application/x-cdlink',
			105 => 'application/x-chess-pgn',
			106 => 'application/x-cpio',
			107 => 'application/x-debian-package',
			108 => 'application/x-director',
			109 => 'application/x-dms',
			110 => 'application/x-doom',
			111 => 'application/x-dvi',
			112 => 'application/x-flac',
			113 => 'application/x-font',
			114 => 'application/x-freemind',
			115 => 'application/x-futuresplash',
			116 => 'application/x-gnumeric',
			117 => 'application/x-go-sgf',
			118 => 'application/x-graphing-calculator',
			119 => 'application/x-gtar',
			120 => 'application/x-hdf',
			121 => 'application/x-httpd-eruby',
			122 => 'application/x-httpd-php',
			123 => 'application/x-httpd-php-source',
			124 => 'application/x-httpd-php3',
			125 => 'application/x-httpd-php3-preprocessed',
			126 => 'application/x-httpd-php4',
			127 => 'application/x-ica',
			128 => 'application/x-internet-signup',
			129 => 'application/x-iphone',
			130 => 'application/x-iso9660-image',
			131 => 'application/x-java-jnlp-file',
			132 => 'application/x-javascript',
			133 => 'application/x-jmol',
			134 => 'application/x-kchart',
			135 => 'application/x-killustrator',
			136 => 'application/x-koan',
			137 => 'application/x-kpresenter',
			138 => 'application/x-kspread',
			139 => 'application/x-kword',
			140 => 'application/x-latex',
			141 => 'application/x-lha',
			142 => 'application/x-lyx',
			143 => 'application/x-lzh',
			144 => 'application/x-lzx',
			145 => 'application/x-maker',
			146 => 'application/x-mif',
			147 => 'application/x-ms-wmd',
			148 => 'application/x-ms-wmz',
			149 => 'application/x-msdos-program',
			150 => 'application/x-msi',
			151 => 'application/x-netcdf',
			152 => 'application/x-ns-proxy-autoconfig',
			153 => 'application/x-nwc',
			154 => 'application/x-object',
			155 => 'application/x-oz-application',
			156 => 'application/x-pkcs7-certreqresp',
			157 => 'application/x-pkcs7-crl',
			158 => 'application/x-python-code',
			159 => 'application/x-quicktimeplayer',
			160 => 'application/x-redhat-package-manager',
			161 => 'application/x-shar',
			162 => 'application/x-shockwave-flash',
			163 => 'application/x-stuffit',
			164 => 'application/x-sv4cpio',
			165 => 'application/x-sv4crc',
			166 => 'application/x-tar',
			167 => 'application/x-tcl',
			168 => 'application/x-tex-gf',
			169 => 'application/x-tex-pk',
			170 => 'application/x-texinfo',
			171 => 'application/x-trash',
			172 => 'application/x-troff',
			173 => 'application/x-troff-man',
			174 => 'application/x-troff-me',
			175 => 'application/x-troff-ms',
			176 => 'application/x-ustar',
			177 => 'application/x-wais-source',
			178 => 'application/x-wingz',
			179 => 'application/x-x509-ca-cert',
			180 => 'application/x-xcf',
			181 => 'application/x-xfig',
			182 => 'application/x-xpinstall',
			183 => 'application/xhtml+xml',
			184 => 'application/xml',
			185 => 'application/zip',
			186 => 'audio/basic',
			187 => 'audio/midi',
			346 => 'audio/mp4',
			188 => 'audio/mpeg',
			189 => 'audio/ogg',
			190 => 'audio/prs.sid',
			191 => 'audio/x-aiff',
			192 => 'audio/x-gsm',
			193 => 'audio/x-mpegurl',
			194 => 'audio/x-ms-wax',
			195 => 'audio/x-ms-wma',
			196 => 'audio/x-pn-realaudio',
			197 => 'audio/x-realaudio',
			198 => 'audio/x-scpls',
			199 => 'audio/x-sd2',
			200 => 'audio/x-wav',
			201 => 'chemical/x-alchemy',
			202 => 'chemical/x-cache',
			203 => 'chemical/x-cache-csf',
			204 => 'chemical/x-cactvs-binary',
			205 => 'chemical/x-cdx',
			206 => 'chemical/x-cerius',
			207 => 'chemical/x-chem3d',
			208 => 'chemical/x-chemdraw',
			209 => 'chemical/x-cif',
			210 => 'chemical/x-cmdf',
			211 => 'chemical/x-cml',
			212 => 'chemical/x-compass',
			213 => 'chemical/x-crossfire',
			214 => 'chemical/x-csml',
			215 => 'chemical/x-ctx',
			216 => 'chemical/x-cxf',
			217 => 'chemical/x-embl-dl-nucleotide',
			218 => 'chemical/x-galactic-spc',
			219 => 'chemical/x-gamess-input',
			220 => 'chemical/x-gaussian-checkpoint',
			221 => 'chemical/x-gaussian-cube',
			222 => 'chemical/x-gaussian-input',
			223 => 'chemical/x-gaussian-log',
			224 => 'chemical/x-gcg8-sequence',
			225 => 'chemical/x-genbank',
			226 => 'chemical/x-hin',
			227 => 'chemical/x-isostar',
			228 => 'chemical/x-jcamp-dx',
			229 => 'chemical/x-kinemage',
			230 => 'chemical/x-macmolecule',
			231 => 'chemical/x-macromodel-input',
			232 => 'chemical/x-mdl-molfile',
			233 => 'chemical/x-mdl-rdfile',
			234 => 'chemical/x-mdl-rxnfile',
			235 => 'chemical/x-mdl-sdfile',
			236 => 'chemical/x-mdl-tgf',
			237 => 'chemical/x-mmcif',
			238 => 'chemical/x-mol2',
			239 => 'chemical/x-molconn-Z',
			240 => 'chemical/x-mopac-graph',
			241 => 'chemical/x-mopac-input',
			242 => 'chemical/x-mopac-out',
			243 => 'chemical/x-mopac-vib',
			244 => 'chemical/x-ncbi-asn1-ascii',
			245 => 'chemical/x-ncbi-asn1-binary',
			246 => 'chemical/x-ncbi-asn1-spec',
			247 => 'chemical/x-pdb',
			248 => 'chemical/x-rosdal',
			249 => 'chemical/x-swissprot',
			250 => 'chemical/x-vamas-iso14976',
			251 => 'chemical/x-vmd',
			252 => 'chemical/x-xtel',
			253 => 'chemical/x-xyz',
			254 => 'image/gif',
			255 => 'image/ief',
			256 => 'image/jpeg',
			257 => 'image/pcx',
			258 => 'image/png',
			259 => 'image/svg+xml',
			260 => 'image/tiff',
			261 => 'image/vnd.djvu',
			262 => 'image/vnd.microsoft.icon',
			263 => 'image/vnd.wap.wbmp',
			264 => 'image/x-cmu-raster',
			265 => 'image/x-coreldraw',
			266 => 'image/x-coreldrawpattern',
			267 => 'image/x-coreldrawtemplate',
			268 => 'image/x-corelphotopaint',
			269 => 'image/x-jg',
			270 => 'image/x-jng',
			271 => 'image/x-ms-bmp',
			272 => 'image/x-photoshop',
			273 => 'image/x-portable-anymap',
			274 => 'image/x-portable-bitmap',
			275 => 'image/x-portable-graymap',
			276 => 'image/x-portable-pixmap',
			277 => 'image/x-rgb',
			278 => 'image/x-xbitmap',
			279 => 'image/x-xpixmap',
			280 => 'image/x-xwindowdump',
			281 => 'message/rfc822',
			282 => 'model/iges',
			283 => 'model/mesh',
			284 => 'model/vrml',
			285 => 'text/calendar',
			286 => 'text/css',
			287 => 'text/csv',
			288 => 'text/h323',
			289 => 'text/html',
			290 => 'text/iuls',
			291 => 'text/mathml',
			292 => 'text/plain',
			293 => 'text/richtext',
			294 => 'text/scriptlet',
			295 => 'text/tab-separated-values',
			296 => 'text/texmacs',
			297 => 'text/vnd.sun.j2me.app-descriptor',
			298 => 'text/vnd.wap.wml',
			299 => 'text/vnd.wap.wmlscript',
			300 => 'text/x-bibtex',
			301 => 'text/x-boo',
			302 => 'text/x-c++hdr',
			303 => 'text/x-c++src',
			304 => 'text/x-chdr',
			305 => 'text/x-component',
			306 => 'text/x-csh',
			307 => 'text/x-csrc',
			308 => 'text/x-diff',
			309 => 'text/x-dsrc',
			310 => 'text/x-haskell',
			311 => 'text/x-java',
			312 => 'text/x-literate-haskell',
			313 => 'text/x-moc',
			314 => 'text/x-pascal',
			315 => 'text/x-pcs-gcd',
			316 => 'text/x-perl',
			317 => 'text/x-python',
			318 => 'text/x-setext',
			319 => 'text/x-sh',
			320 => 'text/x-tcl',
			321 => 'text/x-tex',
			322 => 'text/x-vcalendar',
			323 => 'text/x-vcard',
			324 => 'video/3gpp',
			325 => 'video/dl',
			326 => 'video/dv',
			327 => 'video/fli',
			328 => 'video/gl',
			329 => 'video/mp4',
			330 => 'video/mpeg',
			331 => 'video/ogg',
			332 => 'video/quicktime',
			333 => 'video/vnd.mpegurl',
			347 => 'video/x-flv',
			334 => 'video/x-la-asf',
			348 => 'video/x-m4v',
			335 => 'video/x-mng',
			336 => 'video/x-ms-asf',
			337 => 'video/x-ms-wm',
			338 => 'video/x-ms-wmv',
			339 => 'video/x-ms-wmx',
			340 => 'video/x-ms-wvx',
			341 => 'video/x-msvideo',
			342 => 'video/x-sgi-movie',
			343 => 'x-conference/x-cooltalk',
			344 => 'x-epoc/x-sisx-app',
			345 => 'x-world/x-vrml',
		),
		'extensions' => array (
			'ez' => 0,
			'atom' => 1,
			'atomcat' => 2,
			'atomsrv' => 3,
			'cap' => 4,
			'pcap' => 4,
			'cu' => 5,
			'tsp' => 6,
			'hta' => 7,
			'jar' => 8,
			'ser' => 9,
			'class' => 10,
			'hqx' => 11,
			'nb' => 12,
			'mdb' => 13,
			'dot' => 14,
			'doc' => 14,
			'bin' => 15,
			'oda' => 16,
			'ogx' => 17,
			'pdf' => 18,
			'key' => 19,
			'pgp' => 20,
			'prf' => 21,
			'eps' => 22,
			'ai' => 22,
			'ps' => 22,
			'rar' => 23,
			'rdf' => 24,
			'rss' => 25,
			'rtf' => 26,
			'smi' => 27,
			'smil' => 27,
			'cdy' => 28,
			'kml' => 29,
			'kmz' => 30,
			'xul' => 31,
			'xlb' => 32,
			'xlt' => 32,
			'xls' => 32,
			'xlam' => 33,
			'xlsb' => 34,
			'xlsm' => 35,
			'xltm' => 36,
			'cat' => 37,
			'stl' => 38,
			'pps' => 39,
			'ppt' => 39,
			'ppam' => 40,
			'pptm' => 41,
			'ppsm' => 42,
			'potm' => 43,
			'docm' => 44,
			'dotm' => 45,
			'xps' => 46,
			'odc' => 47,
			'odb' => 48,
			'odf' => 49,
			'odg' => 50,
			'otg' => 51,
			'odi' => 52,
			'odp' => 53,
			'otp' => 54,
			'ods' => 55,
			'ots' => 56,
			'odt' => 57,
			'odm' => 58,
			'ott' => 59,
			'oth' => 60,
			'pptx' => 61,
			'ppsx' => 62,
			'potx' => 63,
			'xlsx' => 64,
			'xltx' => 65,
			'docx' => 66,
			'dotx' => 67,
			'cod' => 68,
			'mmf' => 69,
			'sdc' => 70,
			'sds' => 71,
			'sda' => 72,
			'sdd' => 73,
			'sdw' => 75,
			'sgl' => 76,
			'sxc' => 77,
			'stc' => 78,
			'sxd' => 79,
			'std' => 80,
			'sxi' => 81,
			'sti' => 82,
			'sxm' => 83,
			'sxw' => 84,
			'sxg' => 85,
			'stw' => 86,
			'sis' => 87,
			'vsd' => 88,
			'wbxml' => 89,
			'wmlc' => 90,
			'wmlsc' => 91,
			'wpd' => 92,
			'wp5' => 93,
			'wk' => 94,
			'7z' => 95,
			'abw' => 96,
			'dmg' => 97,
			'bcpio' => 98,
			'torrent' => 99,
			'cab' => 100,
			'cbr' => 101,
			'cbz' => 102,
			'cdf' => 103,
			'vcd' => 104,
			'pgn' => 105,
			'cpio' => 106,
			'udeb' => 107,
			'deb' => 107,
			'dir' => 108,
			'dxr' => 108,
			'dcr' => 108,
			'dms' => 109,
			'wad' => 110,
			'dvi' => 111,
			'flac' => 112,
			'pfa' => 113,
			'pfb' => 113,
			'pcf' => 113,
			'gsf' => 113,
			'pcf.z' => 113,
			'mm' => 114,
			'spl' => 115,
			'gnumeric' => 116,
			'sgf' => 117,
			'gcf' => 118,
			'taz' => 119,
			'gtar' => 119,
			'tgz' => 119,
			'hdf' => 120,
			'rhtml' => 121,
			'phtml' => 122,
			'pht' => 122,
			'php' => 122,
			'phps' => 123,
			'php3' => 124,
			'php3p' => 125,
			'php4' => 126,
			'ica' => 127,
			'ins' => 128,
			'isp' => 128,
			'iii' => 129,
			'iso' => 130,
			'jnlp' => 131,
			'js' => 132,
			'jmz' => 133,
			'chrt' => 134,
			'kil' => 135,
			'skp' => 136,
			'skd' => 136,
			'skm' => 136,
			'skt' => 136,
			'kpr' => 137,
			'kpt' => 137,
			'ksp' => 138,
			'kwd' => 139,
			'kwt' => 139,
			'latex' => 140,
			'lha' => 141,
			'lyx' => 142,
			'lzh' => 143,
			'lzx' => 144,
			'maker' => 145,
			'frm' => 145,
			'frame' => 145,
			'fm' => 145,
			'book' => 145,
			'fb' => 145,
			'fbdoc' => 145,
			'mif' => 146,
			'wmd' => 147,
			'wmz' => 148,
			'dll' => 149,
			'bat' => 149,
			'exe' => 149,
			'com' => 149,
			'msi' => 150,
			'nc' => 151,
			'pac' => 152,
			'nwc' => 153,
			'o' => 154,
			'oza' => 155,
			'p7r' => 156,
			'crl' => 157,
			'pyo' => 158,
			'pyc' => 158,
			'qtl' => 159,
			'rpm' => 160,
			'shar' => 161,
			'swf' => 162,
			'swfl' => 162,
			'sitx' => 163,
			'sit' => 163,
			'sv4cpio' => 164,
			'sv4crc' => 165,
			'tar' => 166,
			'gf' => 168,
			'pk' => 169,
			'texi' => 170,
			'texinfo' => 170,
			'sik' => 171,
			'~' => 171,
			'bak' => 171,
			'%' => 171,
			'old' => 171,
			't' => 172,
			'roff' => 172,
			'tr' => 172,
			'man' => 173,
			'me' => 174,
			'ms' => 175,
			'ustar' => 176,
			'src' => 177,
			'wz' => 178,
			'crt' => 179,
			'xcf' => 180,
			'fig' => 181,
			'xpi' => 182,
			'xht' => 183,
			'xhtml' => 183,
			'xml' => 184,
			'xsl' => 184,
			'zip' => 185,
			'au' => 186,
			'snd' => 186,
			'mid' => 187,
			'midi' => 187,
			'kar' => 187,
			'mpega' => 188,
			'mpga' => 188,
			'm4a' => 188,
			'mp3' => 188,
			'mp2' => 188,
			'ogg' => 189,
			'oga' => 189,
			'spx' => 189,
			'sid' => 190,
			'aif' => 191,
			'aiff' => 191,
			'aifc' => 191,
			'gsm' => 192,
			'm3u' => 193,
			'wax' => 194,
			'wma' => 195,
			'rm' => 196,
			'ram' => 196,
			'ra' => 197,
			'pls' => 198,
			'sd2' => 199,
			'wav' => 200,
			'alc' => 201,
			'cac' => 202,
			'cache' => 202,
			'csf' => 203,
			'cascii' => 204,
			'cbin' => 204,
			'ctab' => 204,
			'cdx' => 205,
			'cer' => 206,
			'c3d' => 207,
			'chm' => 208,
			'cif' => 209,
			'cmdf' => 210,
			'cml' => 211,
			'cpa' => 212,
			'bsd' => 213,
			'csml' => 214,
			'csm' => 214,
			'ctx' => 215,
			'cxf' => 216,
			'cef' => 216,
			'emb' => 217,
			'embl' => 217,
			'spc' => 218,
			'gam' => 219,
			'inp' => 219,
			'gamin' => 219,
			'fchk' => 220,
			'fch' => 220,
			'cub' => 221,
			'gau' => 222,
			'gjf' => 222,
			'gjc' => 222,
			'gal' => 223,
			'gcg' => 224,
			'gen' => 225,
			'hin' => 226,
			'istr' => 227,
			'ist' => 227,
			'dx' => 228,
			'jdx' => 228,
			'kin' => 229,
			'mcm' => 230,
			'mmd' => 231,
			'mmod' => 231,
			'mol' => 232,
			'rd' => 233,
			'rxn' => 234,
			'sdf' => 235,
			'sd' => 235,
			'tgf' => 236,
			'mcif' => 237,
			'mol2' => 238,
			'b' => 239,
			'gpt' => 240,
			'mopcrt' => 241,
			'zmt' => 241,
			'mpc' => 241,
			'dat' => 241,
			'mop' => 241,
			'moo' => 242,
			'mvb' => 243,
			'prt' => 244,
			'aso' => 245,
			'val' => 245,
			'asn' => 246,
			'ent' => 247,
			'pdb' => 247,
			'ros' => 248,
			'sw' => 249,
			'vms' => 250,
			'vmd' => 251,
			'xtel' => 252,
			'xyz' => 253,
			'gif' => 254,
			'ief' => 255,
			'jpeg' => 256,
			'jpe' => 256,
			'jpg' => 256,
			'pcx' => 257,
			'png' => 258,
			'svgz' => 259,
			'svg' => 259,
			'tif' => 260,
			'tiff' => 260,
			'djvu' => 261,
			'djv' => 261,
			'ico' => 262,
			'wbmp' => 263,
			'ras' => 264,
			'cdr' => 265,
			'pat' => 266,
			'cdt' => 267,
			'cpt' => 268,
			'art' => 269,
			'jng' => 270,
			'bmp' => 271,
			'psd' => 272,
			'pnm' => 273,
			'pbm' => 274,
			'pgm' => 275,
			'ppm' => 276,
			'rgb' => 277,
			'xbm' => 278,
			'xpm' => 279,
			'xwd' => 280,
			'eml' => 281,
			'igs' => 282,
			'iges' => 282,
			'silo' => 283,
			'msh' => 283,
			'mesh' => 283,
			'icz' => 285,
			'ics' => 285,
			'css' => 286,
			'csv' => 287,
			'323' => 288,
			'html' => 289,
			'htm' => 289,
			'shtml' => 289,
			'uls' => 290,
			'mml' => 291,
			'txt' => 292,
			'pot' => 292,
			'text' => 292,
			'asc' => 292,
			'rtx' => 293,
			'wsc' => 294,
			'sct' => 294,
			'tsv' => 295,
			'ts' => 296,
			'tm' => 296,
			'jad' => 297,
			'wml' => 298,
			'wmls' => 299,
			'bib' => 300,
			'boo' => 301,
			'hpp' => 302,
			'hh' => 302,
			'h++' => 302,
			'hxx' => 302,
			'cxx' => 303,
			'cc' => 303,
			'cpp' => 303,
			'c++' => 303,
			'h' => 304,
			'htc' => 305,
			'csh' => 306,
			'c' => 307,
			'patch' => 308,
			'diff' => 308,
			'd' => 309,
			'hs' => 310,
			'java' => 311,
			'lhs' => 312,
			'moc' => 313,
			'pas' => 314,
			'p' => 314,
			'gcd' => 315,
			'pm' => 316,
			'pl' => 316,
			'py' => 317,
			'etx' => 318,
			'sh' => 319,
			'tk' => 320,
			'tcl' => 320,
			'cls' => 321,
			'ltx' => 321,
			'sty' => 321,
			'tex' => 321,
			'vcs' => 322,
			'vcf' => 323,
			'3gp' => 324,
			'dl' => 325,
			'dif' => 326,
			'dv' => 326,
			'fli' => 327,
			'gl' => 328,
			'mp4' => 329,
			'f4v' => 329,
			'f4p' => 329,
			'mpe' => 330,
			'mpeg' => 330,
			'mpg' => 330,
			'ogv' => 331,
			'qt' => 332,
			'mov' => 332,
			'mxu' => 333,
			'lsf' => 334,
			'lsx' => 334,
			'mng' => 335,
			'asx' => 336,
			'asf' => 336,
			'wm' => 337,
			'wmv' => 338,
			'wmx' => 339,
			'wvx' => 340,
			'avi' => 341,
			'movie' => 342,
			'ice' => 343,
			'sisx' => 344,
			'wrl' => 345,
			'vrm' => 345,
			'vrml' => 345,
			'f4a' => 346,
			'f4b' => 346,
			'flv' => 347,
			'm4v' => 348,
		),
	);
}

/*
Retourne l'URL de la page courante. Un premier paramètre optionnel, s'il vaut FALSE, permet de ne pas retourner les variables GET. Un deuxième paramètre optionnel, s'il vaut FALSE, permet de retourner seulement l'URL demandée sans la partie serveur. Un troisième paramètre optionnel, s'il vaut TRUE, active la recherche d'un fichier d'index (par exemple `index.php`) pour l'ajouter, s'il y a lieu, à l'URL.

Note: si l'URL contient une ancre, cette dernière sera perdue, car le serveur n'en a pas connaissance. Par exemple, si l'URL fournie est `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3#ancre`, la fonction va retourner `http://www.NomDeDomaine.ext/fichier.php?a=2&b=3` si `$retourneVariablesGet` et `$retourneServeur` valent TRUE.

Fonction inspirée de <http://api.drupal.org/api/function/drupal_detect_baseurl> et de <http://www.mediawiki.org/wiki/Manual:$wgServer/fr>.
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
	
	if (!empty($_SERVER['SERVER_PORT']) && (($protocole == 'https://' && $_SERVER['SERVER_PORT'] != 443) || ($protocole == 'http://' && $_SERVER['SERVER_PORT'] != 80)))
	{
		$port = ':' . securiseTexte($_SERVER['SERVER_PORT']);
	}
	else
	{
		$port = '';
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
	if (preg_match('#/$#', $url))
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
function urlCat($categorie, $idCategorie)
{
	if (!empty($categorie['url']))
	{
		if (preg_match('/^categorie\.php\b/', $categorie['url']) && estCatSpeciale($idCategorie) && !empty($categorie['langue']))
		{
			$categorie['url'] = variableGet(1, $categorie['url'], 'langue', $categorie['langue']);
		}
	}
	else
	{
		$categorie['url'] = 'categorie.php?id=' . filtreChaine($idCategorie);
		
		if (estCatSpeciale($idCategorie) && !empty($categorie['langue']))
		{
			$categorie['url'] .= '&amp;langue=' . $categorie['langue'];
		}
	}
	
	return $categorie['url'];
}

/*
Retourne TRUE si l'URL existe, sinon retourne FALSE.
*/
function urlExiste($url)
{
	$enTetes = '';
	@file_get_contents($url, 0, NULL, 0, 1);
	
	if (isset($http_response_header[0]))
	{
		$enTetes = $http_response_header[0];
	}
	
	return preg_match('#^HTTP/\d+\.\d+\s+[23]#', $enTetes);
}

/*
Retourne l'URL d'une galerie. Si aucune URL n'a été trouvée, retourne une URL par défaut.

Si le paramètre `$action` vaut `0`, récupère les informations de la galerie `$info` dans le fichier de configuration des galeries, sinon s'il vaut `1`, utilise directement `$info` comme URL brute de la galerie.
*/
function urlGalerie($action, $racine, $urlRacine, $info, $langue, $remplacerLangue = TRUE)
{
	if ($action == 0)
	{
		$listeGaleries = listeGaleries($racine, $info);
		
		if (!empty($listeGaleries[$info]['url']))
		{
			$urlGalerie = $listeGaleries[$info]['url'];
		}
		else
		{
			$urlGalerie = 'galerie.php?id=' . filtreChaine($info) . "&amp;langue=$langue";
		}
	}
	elseif ($action == 1)
	{
		$urlGalerie = $info;
	}
	else
	{
		$urlGalerie = '';
	}
	
	if ($remplacerLangue)
	{
		$urlGalerie = str_replace('{LANGUE}', $langue, $urlGalerie);
	}
	
	return $urlRacine . '/' . $urlGalerie;
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
	
	if (preg_match('#/$#', $urlSansGet))
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
Effectue l'action demandée à la variable GET `$cle` de l'adresse fournie et retourne le résultat.
*/
function variableGet($action, $adresse, $cle, $valeur = '')
{
	if (!empty($cle))
	{
		$infosAdresse = parse_url($adresse);
		$adresseGet = array ();
		
		if (isset($infosAdresse['query']))
		{
			parse_str(str_replace('&amp;', '&', $infosAdresse['query']), $adresseGet);
		}
		
		# Supprimer.
		if ($action == 0 && isset($adresseGet[$cle]))
		{
			unset($adresseGet[$cle]);
		}
		# Écraser.
		elseif ($action == 1)
		{
			$adresseGet[$cle] = $valeur;
		}
		# Ajouter
		elseif ($action == 2 && !isset($adresseGet[$cle]))
		{
			$adresseGet[$cle] = $valeur;
		}
		
		$adresse = preg_replace('/(\?|#).*$/', '', $adresse);
		$premierGet = TRUE;
		
		foreach ($adresseGet as $c => $v)
		{
			if ($premierGet)
			{
				$adresse .= '?';
				$premierGet = FALSE;
			}
			else
			{
				$adresse .= '&amp;';
			}
			
			$adresse .= $c;
			
			if (!empty($v))
			{
				$adresse .= "=$v";
			}
		}
		
		if (isset($infosAdresse['fragment']))
		{
			$adresse .= '#' . $infosAdresse['fragment'];
		}
	}
	
	return $adresse;
}

/*
Retourne sous forme de chaîne le code PHP nécessaire aux premières affectations du script. La chaîne retournée doit ensuite être exécutée par la fonction PHP `eval()`.
*/
function variablesAaffecterAuDebut()
{
	$variables = '$nomPage = nomPage();
	$url = url();
	$urlSansGet = url(FALSE);
	$urlAvecIndexSansGet = url(FALSE, TRUE, TRUE);
	$urlSansIndexSansGet = preg_replace("#(?<=/)index\.php$#", "", $urlSansGet);';
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
	
	list ($larg, $haut) = @getimagesize($cheminImage);
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

Si la vignette tatouée n'existe pas déjà et que la bibliothèque GD n'est pas installée, la vignette utilisée est celle sans tatouage.
*/
function vignetteTatouee($paragraphe, $sens, $racine, $racineImgSrc, $urlImgSrc, $galerieQualiteJpg)
{
	preg_match('/src="([^"]+)"/', $paragraphe, $resultat);
	$srcContenu = $resultat[1];
	$nomImgSrcContenu = superBasename($srcContenu);
	$vignetteNom = nomSuffixe($nomImgSrcContenu, '-' . $sens);
	
	if (!file_exists($racineImgSrc . '/tatouage/' . $vignetteNom))
	{
		if (!file_exists($racineImgSrc . '/tatouage'))
		{
			@mkdir($racineImgSrc . '/tatouage');
		}
	
		@copy($racineImgSrc . '/' . $nomImgSrcContenu, $racineImgSrc . '/tatouage/' . $vignetteNom);
		
		if (gdEstInstallee())
		{
			if (file_exists($racine . '/site/fichiers/' . $sens . '-tatouage.png'))
			{
				$imgSrc = imagecreatefrompng($racine . '/site/fichiers/' . $sens . '-tatouage.png');
			}
			else
			{
				$imgSrc = imagecreatefrompng($racine . '/fichiers/' . $sens . '-tatouage.png');
			}
		
			$typeMime = typeMime($racineImgSrc . '/tatouage/' . $vignetteNom);
		
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
	}
	
	// On retourne le paragraphe avec l'attribut `src` modifié.
	return preg_replace('/src="[^"]+"/', 'src="' . $urlImgSrc . '/tatouage/' . $vignetteNom . '"', $paragraphe);
}
?>
