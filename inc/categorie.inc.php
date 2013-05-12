<?php
/*
Ce fichier construit et analyse la liste des articles classés dans la catégorie demandée. Après son inclusion, la variable `$categorie` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Par défaut, on suppose que le RSS est désactivé. On ajustera plus loin s'il y a lieu.
$rssCategorie = FALSE;

// Liste des articles à afficher.
if ($idCategorie == 'site')
{
	$categories = array ();
	
	if ($activerCategoriesGlobales['site'])
	{
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, eval(LANGUE), $categories, array ('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
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
		$categories = ajouteCategoriesSpeciales($racine, $urlRacine, eval(LANGUE), $categories, array ('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieLegendeMarkdown);
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
	
	if (!empty($categories[$idCategorie]['rss']) && $categories[$idCategorie]['rss'] == 1)
	{
		$rssCategorie = TRUE;
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
			$baliseTitle = sprintf(T_("%1\$s – Tous les articles classés dans la catégorie %2\$s"), securiseTexte($idCategorie), securiseTexte($idCategorie));
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
			$description = sprintf(T_("Consulter tous les articles de la catégorie %1\$s."), securiseTexte($idCategorie)) . $baliseTitleComplement;
		}
	}
	
	if ($inclureMotsCles && empty($motsCles))
	{
		$motsCles = motsCles('', $description);
	}
	
	$nombreArticles = 0;
	
	if (!empty($categories[$idCategorie]['pages']))
	{
		$nombreArticles = count($categories[$idCategorie]['pages']);
	}
	
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
				$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), '<em>' . securiseTexte($idCategorie) . '</em>');
			}
			else
			{
				$baliseH1 = '<em>' . securiseTexte($idCategorie) . '</em>';
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
				$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), '<em>' . securiseTexte($idCategorie) . '</em>');
			}
			else
			{
				$baliseH1 = '<em>' . securiseTexte($idCategorie) . '</em>';
			}
		}
		
		if (!$inclureCachePartiel || !file_exists($cheminFichierCachePartiel) || (cacheExpire($cheminFichierCachePartiel, $dureeCachePartiel) && !$mettreAjourCacheSeulementParCron) || $estPageCron)
		{
			for ($indice = $indicePremierArticle; $indice <= $indiceDernierArticle && $indice < $nombreArticles; $indice++)
			{
				$adresse = $urlRacine . '/' . $categories[$idCategorie]['pages'][$indice];
				$infosPage = infosPage($racine, $urlRacine, $adresse, $inclureApercu, $tailleApercuAutomatique, $marqueTroncatureApercu, $dureeCache, TRUE, $estPageCron, $mettreAjourCacheSeulementParCron);
				
				if (!empty($infosPage))
				{
					$categorie .= apercuDansCategorie($racine, $urlRacine, $infosPage, $adresse, $baliseTitleComplement);
				}
			}
		}
		
		if (empty($categorie))
		{
			$categorie .= '<p>' . sprintf(T_("La catégorie %1\$s ne contient aucun article."), '<em>' . securiseTexte($idCategorie) . '</em>') . "</p>\n";
		}
		
		$categorie .= $pagination;
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
			$baliseH1 = sprintf(T_("Articles dans la catégorie %1\$s"), '<em>' . securiseTexte($nomCategorie) . '</em>');
		}
		else
		{
			$baliseH1 = '<em>' . securiseTexte($nomCategorie) . '</em>';
		}
	}
	
	$categorie .= '<p>' . sprintf(T_("La catégorie %1\$s est introuvable."), '<em>' . securiseTexte($nomCategorie) . '</em>') . "</p>\n";
	
	// Ajustement des métabalises.
	
	$baliseTitle = sprintf(T_("La catégorie %1\$s est introuvable"), securiseTexte($nomCategorie));
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
	include $racine . '/site/inc/categorie.inc.php';
}
?>
