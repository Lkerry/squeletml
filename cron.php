<?php
$racine = dirname(__FILE__);

if (file_exists($racine . '/init.inc.php'))
{
	include_once $racine . '/init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	include_once $racineAdmin . '/inc/fonctions.inc.php';
	include_once $racine . '/inc/php-gettext/gettext.inc';
	
	eval(variablesAvantConfig());
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'config') as $cheminFichier)
	{
		include_once $cheminFichier;
	}
	
	if ($activerPageCron)
	{
		$timestamp = time();
		@file_put_contents("$racine/site/inc/cron.txt", $timestamp);

		$langueRapports = !empty($langueRapports) ? $langueRapports : $langueParDefaut;
		phpGettext($racine, $langueRapports); // Nécessaire à la traduction.
		
		$rapport = '';

		if (!isset($adminPorteDocumentsDroits))
		{
			$adminPorteDocumentsDroits = array ('creer' => TRUE);
		}
		
		$dateJour = date('Y-m-d', $timestamp);
		$dateHeure = date('H:i:s', $timestamp);
		$rapport .= '<h1>' . sprintf(T_("Rapport d'exécution du cron du %1\$s à %2\$s"), $dateJour, $dateHeure) . "</h1>\n";
		
		$rapport .= '<p><em>' . sprintf(T_("Note: pour ne plus recevoir le rapport d'exécution du cron, <a href=\"%1\$s\">modifier la variable %2\$s dans le fichier de configuration du site</a>."), $urlRacineAdmin . '/porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php&amp;dossierCourant=../site/inc#messages', '<code>$envoyerRapportCron</code>') . "</em></p>\n";
		
		$rapport .= "<ul>\n";
		$rapport .= '<li><a href="' . $urlRacine . '/cron.php">' . T_("Page de lancement du cron") . "</a></li>\n";
		$rapport .= '<li><a href="' . $urlRacineAdmin . '/">' . T_("Section d'administration du site") . "</a></li>\n";
		$rapport .= "</ul>\n";
	
		########################################################################
		##
		## Cache.
		##
		########################################################################
	
		$rapport .= '<h2>' . T_("Cache") . "</h2>\n";
	
		$cUrlEstDisponible = FALSE;
	
		if (function_exists('curl_init'))
		{
			$cUrlEstDisponible = TRUE;
			require $racine . '/inc/rolling-curl/RollingCurl.php';
			$ch = new RollingCurl('cUrlCronRapport');
		}
	
		$tableauUrlCache = array ();
		$extraAsupprimer = array ();
	
		// Flux RSS.
		if ($dureeCache['fluxRss'])
		{
			if (cheminConfigCategories($racine))
			{
				$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
			
				if (!empty($categories))
				{
					foreach ($categories as $categorie => $categorieInfos)
					{
						$langueCat = langueCat($categorieInfos, $langueParDefaut);
					
						$nomFichierCache = filtreChaine($racine, "rss-categorie-$categorie-$langueCat.cache.xml");
					
						if (empty($categorieInfos['urlCat']) || strpos($categorieInfos['urlCat'], 'categorie.php?id=') !== FALSE)
						{
							$tableauUrlCache[] = array ('url' => "$urlRacine/rss.php?type=categorie&amp;id=$categorie", 'cache' => $nomFichierCache);
						}
						else
						{
							$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=categorie&amp;chemin=' . $categorieInfos['urlCat'], 'cache' => $nomFichierCache);
						}
					}
				}
			}
		
			if (cheminConfigFluxRssGlobal($racine, 'galeries'))
			{
				$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
			
				if (!empty($pages))
				{
					foreach ($pages as $codeLangue => $langueInfos)
					{
						$nomFichierCache = filtreChaine($racine, "rss-galeries-$codeLangue.cache.xml");
						$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=galeries&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
					
						foreach ($langueInfos as $idGalerie => $urlGalerie)
						{
							$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=galerie&amp;chemin=' . $urlGalerie, 'cache' => '');
						
							foreach ($accueil as $langueCache => $infosLangueCache)
							{
								$extraAsupprimer[] = filtreChaine($racine, "rss-galerie-$idGalerie-$langueCache.cache.xml");
							}
						}
					}
				}
			}
		
			if (cheminConfigFluxRssGlobal($racine, 'site'))
			{
				$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
			
				if (!empty($pages))
				{
					foreach ($pages as $codeLangue => $langueInfos)
					{
						$nomFichierCache = filtreChaine($racine, "rss-site-$codeLangue.cache.xml");
						$tableauUrlCache[] = array ('url' => $urlRacine . '/rss.php?type=site&amp;langue=' . $codeLangue, 'cache' => $nomFichierCache);
					}
				}
			}
		}
	
		// Galeries.
		if ($dureeCache['galerie'] && cheminConfigFluxRssGlobal($racine, 'galeries'))
		{
			$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
		
			if (!empty($pages))
			{
				foreach ($pages as $codeLangue => $langueInfos)
				{
					foreach ($langueInfos as $idGalerie => $urlGalerie)
					{
						if (cheminConfigGalerie($racine, $idGalerie))
						{
							$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $idGalerie), TRUE);
						
							if ($galerieVignettesParPage)
							{
								$nombreDimages = count($tableauGalerie);
								$nombreDePages = ceil($nombreDimages / $galerieVignettesParPage);
							}
							else
							{
								$nombreDePages = 1;
							}
						
							$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-1-$codeLangue.cache.html");
							$tableauUrlCache[] = array ('url' => $urlRacine . '/' . $urlGalerie, 'cache' => $nomFichierCache);
				
							if ($nombreDePages > 1)
							{
								for ($i = 2; $i <= $nombreDePages; $i++)
								{
									$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "page=$i");
									$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-page-$i-$codeLangue.cache.html");
									$tableauUrlCache[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
								}
							}
						
							foreach ($tableauGalerie as $image)
							{
								$id = idImage($racine, $image);
								$adresse = ajouteGet($urlRacine . '/' . $urlGalerie, "image=$id");
								$nomFichierCache = filtreChaine($racine, "galerie-$idGalerie-image-$id-$codeLangue.cache.html");
								$tableauUrlCache[] = array ('url' => $adresse, 'cache' => $nomFichierCache);
							}
						}
					}
				}
			}
		}
	
		// Catégories.
		if ($dureeCache['categorie'] && cheminConfigCategories($racine))
		{
			$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
			if (!empty($categories))
			{
				foreach ($categories as $categorie => $categorieInfos)
				{
					$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorieInfos, $categorie, $nombreArticlesParPageCategorie, $langueParDefaut));
				}
			}
		
			if (cheminConfigFluxRssGlobal($racine, 'galeries'))
			{
				$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'galeries'), TRUE);
		
				if (!empty($pages))
				{
					foreach ($pages as $codeLangue => $langueInfos)
					{
						$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					
						if (!empty($categorie))
						{
							$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorie['galeries'], 'galeries', $nombreArticlesParPageCategorie, $langueParDefaut));
						}
					}
				}
			}
		
			if (cheminConfigFluxRssGlobal($racine, 'site'))
			{
				$pages = super_parse_ini_file(cheminConfigFluxRssGlobal($racine, 'site'), TRUE);
		
				if (!empty($pages))
				{
					foreach ($pages as $codeLangue => $langueInfos)
					{
						$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
					
						if (!empty($categorie))
						{
							$tableauUrlCache = array_merge($tableauUrlCache, cronUrlCategorie($racine, $urlRacine, $categorie['site'], 'site', $nombreArticlesParPageCategorie, $langueParDefaut));
						}
					}
				}
			}
		}
	
		// Mise en action.
	
		$rapportLi = '';
	
		foreach ($extraAsupprimer as $aSupprimer)
		{
			if (@unlink($racine . '/site/cache/' . $aSupprimer))
			{
				$rapportLi .= '<li>1: ';
			}
			else
			{
				$rapportLi .= '<li class="erreur">0: ';
			}
		
			$rapportLi .= '<code>unlink("' . $racine . '/site/cache/' . $aSupprimer . '");</code>' . "</li>\n";
		}
	
		foreach ($tableauUrlCache as $url)
		{
			if (!empty($url['cache']))
			{
				if (@unlink($racine . '/site/cache/' . $url['cache']))
				{
					$rapportLi .= '<li>1: ';
				}
				else
				{
					$rapportLi .= '<li class="erreur">0: ';
				}
			
				$rapportLi .= '<code>unlink("' . $racine . '/site/cache/' . $url['cache'] . '");</code>' . "</li>\n";
			}
		
			if (empty($url['cache']) || !file_exists($racine . '/site/cache/' . $url['cache']))
			{
				$urlEncodee = superRawurlencode($url['url'], TRUE);
			
				if ($cUrlEstDisponible)
				{
					$requete = new Request(superRawurlencode($url['url'], TRUE));
					$ch->add($requete);
				}
				else
				{
					if (@file_get_contents($urlEncodee) !== FALSE)
					{
						$rapportLi .= '<li>1: ';
					}
					else
					{
						$rapportLi .= '<li class="erreur">0: ';
					}
				
					$rapportLi .= '<code>file_get_contents("' . $urlEncodee . '");</code>' . "</li>\n";
				}
			}
		}
	
		if ($cUrlEstDisponible)
		{
			ob_start();
			$ch->execute();
			$rapportLi .= ob_get_contents();
			ob_end_clean();
		}

		if (empty($rapportLi))
		{
			$rapportLi = '<li>' . T_("Aucune action à effectuer.") . "</li>\n";
		}
		
		$rapport .= "<ul>\n";
		$rapport .= $rapportLi;
		$rapport .= "</ul>\n";
	
		########################################################################
		##
		## Fichiers Sitemap.
		##
		########################################################################
	
		$rapport .= '<h2>' . T_("Fichiers Sitemap") . "</h2>\n";

		$rapport .= '<h3>' . T_("Vérification de l'existence du fichier Sitemap du site") . "</h3>\n";

		$cheminFichierSitemapSite = $racine . '/sitemap_site.xml';
		$rapport .= "<ul>\n";
	
		if (file_exists($cheminFichierSitemapSite))
		{
			$rapport .= '<li>' . sprintf(T_("Le fichier Sitemap du site (%1\$s) existe."), "<code>$cheminFichierSitemapSite</code>") . "</li>\n";
		}
		elseif (@file_put_contents($cheminFichierSitemapSite, adminPlanSitemapXml()) !== FALSE)
		{
			$rapport .= '<li>' . sprintf(T_("Le fichier Sitemap du site (%1\$s) n'existait pas, donc un plan modèle vide a été créé."), "<code>$cheminFichierSitemapSite</code>") . "</li>\n";
		}
		else
		{
			$rapport .= '<li class="erreur">' . sprintf(T_("Le fichier Sitemap du site (%1\$s) n'existe pas, et la création automatique d'un plan modèle vide a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichierSitemapSite</code>") . "</li>\n";
		}

		$rapport .= "</ul>\n";

		if (file_exists($cheminFichierSitemapSite) && $ajouterPagesParCronDansSitemapSite)
		{
			$rapport .= '<h3>' . T_("Ajout de pages dans le fichier Sitemap du site") . "</h3>\n";
			
			$rapport .= "<ul>\n";
			$rapport .= adminAjoutePagesCategoriesEtFluxRssDansSitemapSite($racine, $urlRacine, $adminPorteDocumentsDroits);
			$rapport .= "</ul>\n";
		}

		if ($activerSitemapGaleries)
		{
			$rapport .= '<h3>' . T_("Génération automatique du fichier Sitemap des galeries") . "</h3>\n";

			$rapport .= "<ul>\n";
			$rapport .= adminGenereSitemapGaleries($racine, $urlRacine, $galerieVignettesParPage, $adminPorteDocumentsDroits);
			$rapport .= "</ul>\n";
		}
		
		$rapport .= '<h3>' . T_("Vérification de l'existence du fichier d'index Sitemap") . "</h3>\n";

		$cheminFichierSitemapIndex = $racine . '/sitemap_index.xml';
		$rapport .= "<ul>\n";
	
		if (file_exists($cheminFichierSitemapIndex))
		{
			$rapport .= '<li>' . sprintf(T_("Le fichier d'index Sitemap (%1\$s) existe."), "<code>$cheminFichierSitemapIndex</code>") . "</li>\n";
		}
		elseif (@file_put_contents($cheminFichierSitemapIndex, adminPlanSitemapIndexXml($urlRacine, $activerSitemapGaleries)) !== FALSE)
		{
			$rapport .= '<li>' . sprintf(T_("Le fichier d'index Sitemap (%1\$s) n'existait pas, donc un plan modèle vide a été créé."), "<code>$cheminFichierSitemapIndex</code>") . "</li>\n";
		}
		else
		{
			$rapport .= '<li class="erreur">' . sprintf(T_("Le fichier d'index Sitemap (%1\$s) n'existe pas, et la création automatique d'un plan modèle vide a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichierSitemapIndex</code>") . "</li>\n";
		}

		$rapport .= "</ul>\n";

		$rapport .= '<h3>' . T_("Vérification de la déclaration du fichier d'index Sitemap dans le fichier <code>robots.txt</code>") . "</h3>\n" ;

		$rapport .= "<ul>\n";
		$rapport .= adminDeclareSitemapDansRobots($racine, $urlRacine, $adminPorteDocumentsDroits);
		$rapport .= "</ul>\n";
		
		########################################################################
		##
		## Envoi du rapport.
		##
		########################################################################
		
		$rapport = str_replace('class="erreur"', 'style="color: #630000;"', $rapport);
		$rapport = preg_replace("#<ul>\n<li><a href=\"javascript:adminSelectionneTexte\('[^']+'\);\">[^<]+</a></li>\n</ul>#", '', $rapport);
		
		if ($envoyerRapportCron && (!empty($courrielAdmin) || !empty($contactCourrielParDefaut)))
		{
			$infosCourriel = array ();
		
			if (!empty($courrielExpediteurRapports))
			{
				$infosCourriel['From'] = $courrielExpediteurRapports;
			}
		
			$infosCourriel['format'] = 'html';
			$infosCourriel['destinataire'] = !empty($courrielAdmin) ? $courrielAdmin : $contactCourrielParDefaut;
			$infosCourriel['objet'] = sprintf(T_("Cron du %1\$s à %2\$s"), $dateJour, $dateHeure) . baliseTitleComplement($tableauBaliseTitleComplement, array ($langueParDefaut), FALSE);
			$infosCourriel['message'] = $rapport;
			courriel($infosCourriel);
		}
	}
}
?>
