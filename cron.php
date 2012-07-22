<?php
$racine = dirname(__FILE__);

if (!isset($lancementCronDansAdmin))
{
	$lancementCronDansAdmin = FALSE;
}

if (file_exists($racine . '/init.inc.php'))
{
	include $racine . '/init.inc.php';
	include_once $racine . '/inc/fonctions.inc.php';
	include_once $racineAdmin . '/inc/fonctions.inc.php';
	include_once $racine . '/inc/php-gettext/gettext.inc.php';
	
	eval(variablesAvantConfig());
	
	foreach (cheminsInc($racine, 'config') as $cheminFichier)
	{
		include $cheminFichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'config') as $cheminFichier)
	{
		include $cheminFichier;
	}
	
	if ($lancementCronDansAdmin || ($activerPageCron && (empty($cleCron) || (isset($_GET['cle']) && $_GET['cle'] == $cleCron))))
	{
		$t1 = time();
		@file_put_contents("$racine/site/inc/cron.txt", $t1);
		
		$langueRapports = !empty($langueRapports) ? $langueRapports : $langueParDefaut;
		phpGettext($racine, $langueRapports); // Nécessaire à la traduction.
		
		$rapport = '';
		$dateJour = date('Y-m-d', $t1);
		$dateHeure = date('H:i:s', $t1);
		$rapport .= '<h1>' . sprintf(T_("Rapport d'exécution du cron du %1\$s à %2\$s"), $dateJour, $dateHeure) . "</h1>\n";
		
		$rapport .= '<p id="rapportCronNoteEnvoi"><em>' . sprintf(T_("Note: pour ne plus recevoir le rapport d'exécution du cron, <a href=\"%1\$s\">modifier la variable %2\$s dans le fichier de configuration du site</a>."), $urlRacineAdmin . '/porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php&amp;dossierCourant=../site/inc#messages', '<code>$envoyerRapportCron</code>') . "</em></p>\n";
		
		$rapport .= "<ul id=\"rapportCronLiensAdmin\">\n";
		$rapport .= '<li><a href="' . $urlRacine . '/cron.php">' . T_("Page de lancement du cron") . "</a></li>\n";
		$rapport .= '<li><a href="' . $urlRacineAdmin . '/">' . T_("Section d'administration du site") . "</a></li>\n";
		$rapport .= "</ul>\n";
		
		########################################################################
		##
		## Cache.
		##
		########################################################################
		
		$rapport .= '<h2>' . T_("Cache") . "</h2>\n";
		
		$rapportLi = '';
		
		if ($dureeCache)
		{
			$listeUrl = adminListeUrl($racine, $urlRacine, $accueil, $activerCategoriesGlobales, $nombreArticlesParPageCategorie, $nombreItemsFluxRss, $activerFluxRssGlobalSite, $galerieActiverFluxRssGlobal, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieVignettesParPage, $activerGalerieDemo);
			
			foreach ($listeUrl as $url => $infosUrl)
			{
				if (!empty($infosUrl['cache']))
				{
					if (@unlink($racine . '/site/cache/' . $infosUrl['cache']))
					{
						$rapportLi .= '<li>1: ';
					}
					else
					{
						$rapportLi .= '<li class="erreur">0: ';
					}
					
					$rapportLi .= '<code>unlink("' . $racine . '/site/cache/' . $infosUrl['cache'] . '");</code>' . "</li>\n";
				}
				
				if (!empty($infosUrl['cacheEnTete']))
				{
					if (@unlink($racine . '/site/cache/' . $infosUrl['cacheEnTete']))
					{
						$rapportLi .= '<li>1: ';
					}
					else
					{
						$rapportLi .= '<li class="erreur">0: ';
					}
					
					$rapportLi .= '<code>unlink("' . $racine . '/site/cache/' . $infosUrl['cacheEnTete'] . '");</code>' . "</li>\n";
				}
				
				if (empty($infosUrl['cache']) || !file_exists($racine . '/site/cache/' . $infosUrl['cache']))
				{
					simuleVisite($racine, $urlRacine, $url, $dureeCache);
					$rapportLi .= '<li>';
					$rapportLi .= '<code>simuleVisite("' . $racine . '", "' . $urlRacine . '", "' . $url . '", "' . $dureeCache . '");</code>' . "</li>\n";
				}
			}
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
		
		$rapport .= '<h2>' . T_("Fichier Sitemap") . "</h2>\n";
		
		if ($ajouterPagesParCronDansSitemap)
		{
			$rapport .= '<h3>' . T_("Ajout de pages dans le fichier Sitemap") . "</h3>\n";
			
			$rapport .= "<ul>\n";
			
			if (!isset($listeUrl))
			{
				$listeUrl = adminListeUrl($racine, $urlRacine, $accueil, $activerCategoriesGlobales, $nombreArticlesParPageCategorie, $nombreItemsFluxRss, $activerFluxRssGlobalSite, $galerieActiverFluxRssGlobal, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieVignettesParPage, $activerGalerieDemo);
			}
			
			$contenuSitemap = adminGenereContenuSitemap($listeUrl);
			$rapport .= adminEnregistreSitemap($racine, $contenuSitemap);
			$rapport .= "</ul>\n";
		}
		
		$rapport .= '<h3>' . sprintf(T_("Vérification de la déclaration du fichier Sitemap dans le fichier %1\$s"), '<code>robots.txt</code>') . "</h3>\n" ;
		
		$rapport .= "<ul>\n";
		$rapport .= adminDeclareSitemapDansRobots($racine, $urlRacine);
		$rapport .= "</ul>\n";
		
		$t2 = time();
		$t = $t2 - $t1;
		$rapport .= "<hr />\n";
		$rapport .= '<p>' . sprintf(T_ngettext("Cron exécuté en %1\$s seconde.", "Cron exécuté en %1\$s secondes.", $t), $t) . "</p>\n";
		
		########################################################################
		##
		## Envoi du rapport.
		##
		########################################################################
		
		if ($lancementCronDansAdmin)
		{
			$rapport = preg_replace('#<p id="rapportCronNoteEnvoi">.+?</p>#', '', $rapport);
			$rapport = preg_replace('#<ul id="rapportCronLiensAdmin">.+?</ul>#s', '', $rapport);
		}
		elseif ($envoyerRapportCron && (!empty($courrielAdmin) || !empty($contactCourrielParDefaut)))
		{
			$rapport = str_replace('class="erreur"', 'style="color: #630000;"', $rapport);
			$rapport = str_replace('<code>', '<code style="background-color: #F2F2F2;">', $rapport);
			$rapport = str_replace('<pre ', '<pre style="overflow: auto; padding: 5px; border: 1px solid #B3B3B3; background-color: #F2F2F2;" ', $rapport);
			$rapport = preg_replace("#<ul>\n<li><a href=\"javascript:adminSelectionneTexte\('[^']+'\);\">[^<]+</a></li>\n</ul>#", '', $rapport);
			
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
	elseif (!$lancementCronDansAdmin)
	{
		header('HTTP/1.1 401 Unauthorized');
	}
}
elseif (!$lancementCronDansAdmin)
{
	header('HTTP/1.1 404 Not found');
}
?>
