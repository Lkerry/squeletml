<?php
/*
Ce fichier construit et analyse la liste des articles faisant partie de la catégorie demandée. Après son inclusion, la variable `$categorie` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Nécessaire à la traduction.
phpGettext($racine, LANGUE);

// URL pour le cache.
if ($dureeCache)
{
	$urlMd5 = md5($url);
}

// Liste des articles à afficher.

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

// Initialisation du contenu de la catégorie.
$categorie = '';

########################################################################
##
## Une catégorie en particulier est demandée.
##
########################################################################

if (!empty($idCategorie))
{
	// On écrase les valeurs par défaut des balises de l'en-tête de la page (pour éviter le contenu dupliqué). Si aucune valeur n'a été donnée à ces balises, on génère une valeur automatiquement.
	
	if (empty($baliseTitle))
	{
		$baliseTitle = sprintf(T_("Articles de la catégorie %1\$s"), "<em>$idCategorie</em>");
	}
	
	if (empty($description))
	{
		$description = sprintf(T_("Articles de la catégorie %1\$s"), "<em>$idCategorie</em>");
	}
	
	if ($inclureMotsCles && empty($motsCles))
	{
		$motsCles = motsCles('', $description);
	}
	
	$nombreArticles = count($categories[$idCategorie]['pages']);
	
	if ($articlesParPage)
	{
		$pagination = pagination($nombreArticles, $articlesParPage, $urlSansGet, $baliseTitle, $description);
		$indicePremierArticle = $pagination['indicePremierElement'];
		$indiceDernierArticle = $pagination['indiceDernierElement'];
		$baliseTitle = $pagination['baliseTitle'];
		$description = $pagination['description'];
		$pagination = $pagination['pagination'];
	}
	else
	{
		$nombreDePages = 1;
		$indicePremierArticle = 0;
		$indiceDernierArticle = $nombreArticles - 1;
	}
	
	// On vérifie si la catégorie existe en cache ou si le cache est expiré.
	if ($dureeCache && file_exists("$racine/site/cache/$urlMd5") && !cacheExpire("$racine/site/cache/$urlMd5", $dureeCache))
	{
		$categorie .= file_get_contents("$racine/site/cache/$urlMd5");
	}
	else
	{
		for ($indice = $indicePremierArticle; $indice <= $indiceDernierArticle && $indice < $nombreArticles; $indice++)
		{
			$adresse = $urlRacine . '/' . $categories[$idCategorie]['pages'][$indice];
			$infosPage = infosPage($adresse, $inclureApercu);
		
			if (!empty($infosPage))
			{
				$categorie .= "<div class=\"apercu\">\n";
				$baliseTitleComplement = baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut));
		
				if (!empty($baliseTitleComplement))
				{
					$infosPage['titre'] = preg_replace('/' . preg_quote($baliseTitleComplement) . '$/', '', $infosPage['titre']);
				}
		
				$categorie .= "<h2 class=\"titreApercu\"><a href=\"$adresse\">{$infosPage['titre']}</a></h2>\n";
		
				if (!empty($infosPage['auteur']) || !empty($infosPage['dateCreation']) || !empty($infosPage['dateRevision']))
				{
					$infosApercu = TRUE;
					$categorie .= "<div class=\"infosApercu\">\n";
				}
				else
				{
					$infosApercu = FALSE;
				}
		
				if (!empty($infosPage['auteur']))
				{
					if (!empty($infosPage['dateCreation']))
					{
						$categorie .= sprintf(T_("Écrit par %1\$s le %2\$s."), $infosPage['auteur'], $infosPage['dateCreation']) . "\n";
					}
					else
					{
						$categorie .= sprintf(T_("Écrit par %1\$s."), $infosPage['auteur']) . "\n";
					}
				}
				elseif (!empty($infosPage['dateCreation']))
				{
					$categorie .= sprintf(T_("Écrit le %1\$s."), $infosPage['dateCreation']) . "\n";
				}
		
				if (!empty($infosPage['dateRevision']))
				{
					$categorie .= sprintf(T_("Dernière révision le %1\$s."), $infosPage['dateRevision']) . "\n";
				}
		
				if ($infosApercu)
				{
					$categorie .= "</div><!-- /.infosApercu -->\n";
				}
		
				$categorie .= "<div class=\"descriptionApercu\">\n";
				$categorie .= $infosPage['description'] . "\n";
				$categorie .= "</div><!-- /.descriptionApercu -->\n";
		
				if (!$infosPage['descriptionComplete'])
				{
					$categorie .= "<div class=\"lienApercu\">\n";
					$categorie .= sprintf(T_("Lire la suite de %1\$s"), "<em><a href=\"$adresse\">" . $infosPage['titre'] . '</a></em>') . "\n";
					$categorie .= "</div><!-- /.lienApercu -->\n";
				}
		
				$categorie .= "</div><!-- /.apercu -->\n";
			}
		}
		
		$categorie .= $pagination;
		
		if ($dureeCache)
		{
			creeDossierCache($racine);
			@file_put_contents("$racine/site/cache/$urlMd5", $categorie);
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
	$estPageDerreur = TRUE;
	$categorie .= '<p>' . T_("La catégorie demandée est introuvable.") . "</p>\n";
	
	// Ajustement des métabalises.
	$baliseTitle = sprintf(T_("La catégorie %1\$s est introuvable"), "<em>$nomCategorie</em>");
	$description = sprintf(T_("La catégorie %1\$s est introuvable"), "<em>$nomCategorie</em>") . $baliseTitleComplement[LANGUE];
	
	if ($inclureMotsCles)
	{
		$motsCles = motsCles('', $description);
		$motsCles .= ', ' . $nomCategorie;
	}
	
	$robots = "noindex, follow, noarchive";
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/categorie.inc.php'))
{
	include_once $racine . '/site/inc/categorie.inc.php';
}
?>
