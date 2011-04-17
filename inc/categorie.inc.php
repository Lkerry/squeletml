<?php
/*
Ce fichier construit et analyse la liste des articles classés dans la catégorie demandée. Après son inclusion, la variable `$categorie` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Nom pour le cache.
if ($dureeCache['categorie'])
{
	if (!empty($_GET['page']))
	{
		$getPage = securiseTexte($_GET['page']);
	}
	else
	{
		$getPage = '1';
	}
	
	$nomFichierCache = filtreChaine($racine, "categorie-$idCategorie-page-$getPage-" . LANGUE . '.cache.html');
}

// Liste des articles à afficher.
if ($idCategorie == 'site')
{
	$categories = array ();
	
	if ($activerCategoriesGlobales['site'])
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, LANGUE, $categories, array ('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
	
	if (empty($categories))
	{
		$nomCategorie = T_("Dernières publications");
		$idCategorie = '';
	}
}
// La catégorie spéciale `galeries` est basée sur le flux RSS des derniers ajouts aux galeries, donc son schéma est légèrement différent des autres catégories.
elseif ($idCategorie == 'galeries')
{
	$categories = array ();
	
	if ($activerCategoriesGlobales['galeries'])
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, LANGUE, $categories, array ('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
	}
	
	if (empty($categories))
	{
		$nomCategorie = T_("Derniers ajouts aux galeries");
		$idCategorie = '';
	}
}
else
{
	$cheminFichier = cheminConfigCategories($racine);
	
	if ($cheminFichier)
	{
		$categories = super_parse_ini_file($cheminFichier, TRUE);
	}

	if (!isset($categories[$idCategorie]))
	{
		$nomCategorie = $idCategorie;
		$idCategorie = '';
	}
}

// Initialisation du contenu de la catégorie.
$categorie = '';

########################################################################
##
## Une catégorie en particulier est demandée.
##
########################################################################

if (!empty($idCategorie))
{
	if ($desactiverIndexationPagesCategorie)
	{
		$robots = 'noindex, follow, noarchive';
	}
	
	// Si aucune valeur n'a été donnée aux balises de l'en-tête de la page, on génère une valeur automatiquement.
	
	if (empty($baliseTitle))
	{
		if ($idCategorie == 'site')
		{
			$baliseTitle = T_("Dernières publications");
		}
		elseif ($idCategorie == 'galeries')
		{
			$baliseTitle = T_("Derniers ajouts aux galeries");
		}
		else
		{
			$baliseTitle = sprintf(T_("%1\$s – Tous les articles classés dans la catégorie %2\$s"), $idCategorie, $idCategorie);
		}
	}
	
	if (empty($description))
	{
		if ($idCategorie == 'site')
		{
			$description = T_("Consulter les dernières publications sur le site.") . $baliseTitleComplement;
		}
		elseif ($idCategorie == 'galeries')
		{
			$description = T_("Consulter les derniers ajouts effectués aux galeries du site.") . $baliseTitleComplement;
		}
		else
		{
			$description = sprintf(T_("Consulter tous les articles de la catégorie %1\$s."), $idCategorie) . $baliseTitleComplement;
		}
	}
	
	if ($inclureMotsCles && empty($motsCles))
	{
		$motsCles = motsCles('', $description);
	}
	
	$nombreArticles = count($categories[$idCategorie]['pages']);
	
	if ($nombreArticlesParPageCategorie)
	{
		$pagination = pagination($racine, $urlRacine, $typePaginationCategorie, $paginationAvecFond, $paginationArrondie, $nombreArticles, $nombreArticlesParPageCategorie, $urlSansGet, $baliseTitle, $description);
		
		if ($pagination['estPageDerreur'])
		{
			$erreur404 = TRUE;
		}
		else
		{
			$indicePremierArticle = $pagination['indicePremierElement'];
			$indiceDernierArticle = $pagination['indiceDernierElement'];
			$baliseTitle = $pagination['baliseTitle'];
			$description = $pagination['description'];
			$pagination = $pagination['pagination'];
		}
	}
	elseif (isset($_GET['page']) && $_GET['page'] != 1)
	{
		$erreur404 = TRUE;
	}
	else
	{
		$nombreDePages = 1;
		$indicePremierArticle = 0;
		$indiceDernierArticle = $nombreArticles - 1;
	}
	
	if ($erreur404)
	{
		// Titre de la catégorie.
		if ($genererTitrePageCategories)
		{
			if ($idCategorie == 'site')
			{
				$baliseH1 = T_("Dernières publications");
			}
			elseif ($idCategorie == 'galeries')
			{
				$baliseH1 = T_("Derniers ajouts aux galeries");
			}
			elseif ($titrePageCategoriesAvecMotCategorie)
			{
				$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), "<em>$idCategorie</em>");
			}
			else
			{
				$baliseH1 = "<em>$idCategorie</em>";
			}
		}
		
		// Ajustement des métabalises.
		
		if (isset($_GET['page']))
		{
			$categorie .= '<p>' . sprintf(T_("La page %1\$s est introuvable."), securiseTexte($_GET['page'])) . "</p>\n";
			$baliseTitle = sprintf(T_("La page %1\$s est introuvable"), securiseTexte($_GET['page']));
			$description = '';
		}
		else
		{
			$categorie .= '<p>' . T_("La page est introuvable.") . "</p>\n";
			$baliseTitle = T_("La page est introuvable");
			$description = '';
		}
		
		if ($inclureMotsCles)
		{
			$motsCles = '';
		}
		
		$robots = "noindex, follow, noarchive";
	}
	else
	{
		// Titre de la catégorie.
		if ($genererTitrePageCategories)
		{
			if ($idCategorie == 'site')
			{
				$baliseH1 = T_("Dernières publications");
			}
			elseif ($idCategorie == 'galeries')
			{
				$baliseH1 = T_("Derniers ajouts aux galeries");
			}
			elseif ($titrePageCategoriesAvecMotCategorie)
			{
				$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), "<em>$idCategorie</em>");
			}
			else
			{
				$baliseH1 = "<em>$idCategorie</em>";
			}
		}
		
		// On vérifie si la catégorie existe en cache ou si le cache est expiré.
		if ($dureeCache['categorie'] && file_exists("$racine/site/cache/$nomFichierCache") && !cacheExpire("$racine/site/cache/$nomFichierCache", $dureeCache['categorie']))
		{
			$categorie .= file_get_contents("$racine/site/cache/$nomFichierCache");
		}
		else
		{
			if (function_exists('curl_init'))
			{
				$ch = new RollingCurl('cUrlCategorie');
				$ch->options = array(CURLOPT_FAILONERROR => TRUE);
				
				for ($indice = $indicePremierArticle; $indice <= $indiceDernierArticle && $indice < $nombreArticles; $indice++)
				{
					$adresse = $urlRacine . '/' . superRawurlencode($categories[$idCategorie]['pages'][$indice]);
					$requete = new Request($adresse);
					$ch->add($requete);
				}
				
				ob_start();
				$ch->execute();
				$cUrlCategorie = ob_get_contents();
				ob_end_clean();
				
				for ($indice = $indicePremierArticle; $indice <= $indiceDernierArticle && $indice < $nombreArticles; $indice++)
				{
					$adresseNonEncodee = $urlRacine . '/' . $categories[$idCategorie]['pages'][$indice];
					$adresse = superRawurlencode($adresseNonEncodee);
					
					if (preg_match('/' . preg_quote("<!-- `cUrlCategorie()`: $adresse -->", '/') . '(.+?)' . preg_quote("<!-- /`cUrlCategorie()`: $adresse -->", '/') . '/s', $cUrlCategorie, $resultat))
					{
						$infosPage = infosPage($adresseNonEncodee, $inclureApercu, $tailleApercuAutomatique, $resultat[1]);
						
						if (!empty($infosPage))
						{
							$categorie .= apercuDansCategorie($racine, $urlRacine, $infosPage, $adresse, $baliseTitleComplement, $langueParDefaut);
						}
					}
				}
			}
			else
			{
				for ($indice = $indicePremierArticle; $indice <= $indiceDernierArticle && $indice < $nombreArticles; $indice++)
				{
					$adresseNonEncodee = $urlRacine . '/' . $categories[$idCategorie]['pages'][$indice];
					$adresse = superRawurlencode($adresseNonEncodee);
					$infosPage = infosPage($adresseNonEncodee, $inclureApercu, $tailleApercuAutomatique);
	
					if (!empty($infosPage))
					{
						$categorie .= apercuDansCategorie($racine, $urlRacine, $infosPage, $adresse, $baliseTitleComplement, $langueParDefaut);
					}
				}
			}
			
			$categorie .= $pagination;
			
			if ($dureeCache['categorie'])
			{
				creeDossierCache($racine);
				@file_put_contents("$racine/site/cache/$nomFichierCache", $categorie);
			}
		}
	}
}
########################################################################
##
## La catégorie n'existe pas.
##
########################################################################
else
{
	$erreur404 = TRUE;
	
	// Titre de la catégorie.
	if ($genererTitrePageCategories)
	{
		if ($idCategorie == 'site')
		{
			$baliseH1 = T_("Dernières publications");
		}
		elseif ($idCategorie == 'galeries')
		{
			$baliseH1 = T_("Derniers ajouts aux galeries");
		}
		elseif ($titrePageCategoriesAvecMotCategorie)
		{
			$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), "<em>$nomCategorie</em>");
		}
		else
		{
			$baliseH1 = "<em>$nomCategorie</em>";
		}
	}
	
	$categorie .= '<p>' . sprintf(T_("La catégorie %1\$s est introuvable."), "<em>$nomCategorie</em>") . "</p>\n";
	
	// Ajustement des métabalises.
	
	$baliseTitle = sprintf(T_("La catégorie %1\$s est introuvable"), $nomCategorie);
	$description = '';
	
	if ($inclureMotsCles)
	{
		$motsCles = '';
	}
	
	$robots = "noindex, follow, noarchive";
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/categorie.inc.php'))
{
	include_once $racine . '/site/inc/categorie.inc.php';
}
?>
