<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Fichiers Sitemap");
$boitesDeroulantes = '#configActuelleAdminSitemapSite #optionsAjoutAdminSitemap';
$boitesDeroulantes .= ' .aideAdminSitemap .contenuFichierPourSauvegarde .sitemapBalisesOptionnelles .sitemapImage';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des fichiers Sitemap"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_GET['sitemap']) && $_GET['sitemap'] == 'site')
	{
		$_POST['sitemap'] = $_GET['sitemap'];
	}

	if (isset($_POST['modifsSitemapSite']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications du fichier Sitemap du site") . "</h3>\n" ;
	
		$contenuFichierTableau = array ();
		$urlAjout = '';
	
		if (!empty($_POST['locAjout']))
		{
			if (!empty($_POST['loc']))
			{
				$_POST['loc'] += $_POST['locAjout'];
			}
			else
			{
				$_POST['loc'] = $_POST['locAjout'];
			}
			
			$urlAjout = array_shift($_POST['locAjout']);
		}
	
		if (!empty($_POST['loc']))
		{
			foreach ($_POST['loc'] as $cle => $loc)
			{
				if (!empty($loc))
				{
					$loc = superRawurlencode($loc);
					$contenuFichierTableau[$loc] = array ();
				
					if (!empty($_POST['lastmodLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['lastmod'] = securiseTexte($_POST['lastmodLoc'][$cle]);
					}
				
					if (!empty($_POST['changefreqLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['changefreq'] = securiseTexte($_POST['changefreqLoc'][$cle]);
					}
				
					if (!empty($_POST['priorityLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['priority'] = securiseTexte($_POST['priorityLoc'][$cle]);
					}

					if (!empty($_POST['imageLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['image'] = array ();

						foreach ($_POST['imageLoc'][$cle] as $cleImage => $imageLoc)
						{
							if (!empty($imageLoc))
							{
								$imageLoc = superRawurlencode($imageLoc);
								$contenuFichierTableau[$loc]['image'][$imageLoc] = array ();

								if (!empty($_POST['captionImageLoc'][$cle][$cleImage]))
								{
									$contenuFichierTableau[$loc]['image'][$imageLoc]['caption'] = securiseTexte($_POST['captionImageLoc'][$cle][$cleImage]);
								}

								if (!empty($_POST['titleImageLoc'][$cle][$cleImage]))
								{
									$contenuFichierTableau[$loc]['image'][$imageLoc]['title'] = securiseTexte($_POST['titleImageLoc'][$cle][$cleImage]);
								}

								if (!empty($_POST['licenseImageLoc'][$cle][$cleImage]))
								{
									$contenuFichierTableau[$loc]['image'][$imageLoc]['license'] = securiseTexte($_POST['licenseImageLoc'][$cle][$cleImage]);
								}

								if (!empty($_POST['geoLocationImageLoc'][$cle][$cleImage]))
								{
									$contenuFichierTableau[$loc]['image'][$imageLoc]['geo_location'] = securiseTexte($_POST['geoLocationImageLoc'][$cle][$cleImage]);
								}
							}
						}
					}
				}
			}
		}
	
		$contenuFichier = '';
		$contenuFichier .= adminPlanSitemapXml(FALSE);
	
		foreach ($contenuFichierTableau as $loc => $infosLoc)
		{
			$contenuFichier .= "  <url>\n";
			$contenuFichier .= "    <loc>$loc</loc>\n";
		
			if (!empty($infosLoc['lastmod']))
			{
				$contenuFichier .= "    <lastmod>{$infosLoc['lastmod']}</lastmod>\n";
			}

			if (!empty($infosLoc['changefreq']))
			{
				$contenuFichier .= "    <changefreq>{$infosLoc['changefreq']}</changefreq>\n";
			}

			if (!empty($infosLoc['priority']))
			{
				$contenuFichier .= "    <priority>{$infosLoc['priority']}</priority>\n";
			}

			if (!empty($infosLoc['image']))
			{
				foreach ($infosLoc['image'] as $imageLoc => $infosImageLoc)
				{
					$contenuFichier .= "    <image:image xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";
					$contenuFichier .= "      <image:loc>$imageLoc</image:loc>\n";
				
					if (!empty($infosImageLoc['caption']))
					{
						$contenuFichier .= "      <image:caption>{$infosImageLoc['caption']}</image:caption>\n";
					}

					if (!empty($infosImageLoc['title']))
					{
						$contenuFichier .= "      <image:title>{$infosImageLoc['title']}</image:title>\n";
					}

					if (!empty($infosImageLoc['license']))
					{
						$contenuFichier .= "      <image:license>{$infosImageLoc['license']}</image:license>\n";
					}

					if (!empty($infosImageLoc['geo_location']))
					{
						$contenuFichier .= "      <image:geo_location>{$infosImageLoc['geo_location']}</image:geo_location>\n";
					}

					$contenuFichier .= "    </image:image>\n";
				}
			}
		
			$contenuFichier .= "  </url>\n";
		}

		$contenuFichier .= '</urlset>';
		$messagesScript .= adminEnregistreSitemap($racine, 'site', $contenuFichier, $adminPorteDocumentsDroits);
	
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
		
		$urlAjoutRss = preg_replace('#^' . preg_quote($urlRacine) . '/#', '', $urlAjout);
		
		if (isset($_POST['rssAjout']) && !empty($urlAjoutRss) && !empty($_POST['rssLangueAjout']))
		{
			$messagesScript = '';
			$urlAjoutRss = securiseTexte($urlAjoutRss);
			$rssLangueAjout = securiseTexte($_POST['rssLangueAjout']);
			$contenuFichierRssTableau = array ();
			$cheminFichierRss = cheminConfigFluxRssGlobal($racine, 'site');
		
			if (!$cheminFichierRss)
			{
				$cheminFichierRss = cheminConfigFluxRssGlobal($racine, 'site', TRUE);
			
				if ($adminPorteDocumentsDroits['creer'])
				{
					@touch($cheminFichierRss);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichierRss</code>") . "</li>\n";
				}
			}
		
			if (file_exists($cheminFichierRss) && ($rssPages = super_parse_ini_file($cheminFichierRss, TRUE)) === FALSE)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichierRss . '</code>') . "</li>\n";
			}
			elseif (!empty($rssPages))
			{
				foreach ($rssPages as $codeLangue => $langueInfos)
				{
					$contenuFichierRssTableau[$codeLangue] = array ();
				
					foreach ($langueInfos['pages'] as $page)
					{
						$contenuFichierRssTableau[$codeLangue][] = "pages[]=$page\n";
					}
				}
			}
		
			if (!isset($contenuFichierRssTableau[$rssLangueAjout]))
			{
				$contenuFichierRssTableau[$rssLangueAjout] = array ();
			}

			if (!preg_grep('/^pages\[\]=' . preg_quote($urlAjoutRss, '/') . "\n/", $contenuFichierRssTableau[$rssLangueAjout]))
			{
				array_unshift($contenuFichierRssTableau[$rssLangueAjout], "pages[]=$urlAjoutRss\n");
			}
			
			$contenuFichierRss = '';
		
			foreach ($contenuFichierRssTableau as $codeLangue => $langueInfos)
			{
				if (!empty($langueInfos))
				{
					$contenuFichierRss .= "[$codeLangue]\n";
				
					foreach ($langueInfos as $ligne)
					{
						$contenuFichierRss .= $ligne;
					}
				
					$contenuFichierRss .= "\n";
				}
			}
		
			$messagesScript .= adminEnregistreConfigFluxRssGlobalSite($racine, $contenuFichierRss, $adminPorteDocumentsDroits);
		
			echo adminMessagesScript($messagesScript, T_("Ajout dans le flux RSS des dernières publications"));
		}
	}
	elseif (isset($_POST['modifsSitemapIndex']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications du fichier d'index Sitemap") . "</h3>\n" ;

		$contenuFichier = '';
		$contenuFichier .= adminPlanSitemapIndexXml($urlRacine, $activerSitemapGaleries, FALSE);
		$contenuFichier .= "  <sitemap>\n";
		$contenuFichier .= "    <loc>$urlRacine/sitemap_site.xml</loc>\n";
		
		if (!empty($_POST['lastmodSite']))
		{
			$contenuFichier .= '    <lastmod>' . securiseTexte($_POST['lastmodSite']) . "</lastmod>\n";
		}
		
		$contenuFichier .= "  </sitemap>\n";

		if ($activerSitemapGaleries)
		{
			$contenuFichier .= "  <sitemap>\n";
			$contenuFichier .= "    <loc>$urlRacine/sitemap_galeries.xml</loc>\n";
		
			if (!empty($_POST['lastmodGaleries']))
			{
				$contenuFichier .= '    <lastmod>' . securiseTexte($_POST['lastmodGaleries']) . "</lastmod>\n";
			}
		
			$contenuFichier .= "  </sitemap>\n";
		}
		
		$contenuFichier .= '</sitemapindex>';
		
		$messagesScript .= adminEnregistreSitemap($racine, 'index', $contenuFichier, $adminPorteDocumentsDroits);
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	elseif (isset($_POST['sitemap']))
	{
		if ($_POST['sitemap'] == 'site')
		{
			$messagesScript = '';
			$cheminFichier = $racine . '/sitemap_site.xml';
		
			if (!file_exists($cheminFichier))
			{
				if ($adminPorteDocumentsDroits['creer'])
				{
					adminAjouteUrlDansSitemap($racine, 'site', array (), $adminPorteDocumentsDroits);
				
					if (!file_exists($cheminFichier))
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du fichier Sitemap puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du fichier Sitemap puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
		
			if (file_exists($cheminFichier) && ($contenuSitemap = @file_get_contents($cheminFichier)) !== FALSE)
			{
				echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
				echo "<div>\n";
				$i = 0;
			
				if (!empty($contenuSitemap))
				{
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($contenuSitemap);
					$eUrlListe = $dom->getElementsByTagName('url');
					$listePages = '';
				
					foreach ($eUrlListe as $eUrl)
					{
						$loc = $eUrl->getElementsByTagName('loc')->item(0)->firstChild->nodeValue;
						$loc = rawurldecode($loc);
						$listePages .= '<li class="liParent"><label for="inputLoc-' . $i . '"><code>loc=</code></label><input id="inputLoc-' . $i . '" class="tresLong" type="text" name="loc[' . $i . ']" value="' . $loc . '" />';
						$listePages .= "<ul class=\"nonTriable\">\n";
						$listePages .= '<li class="liParent sitemapBalisesOptionnelles"><span class="bDtitre">Balises optionnelles</span>';
						$listePages .= "<ul class=\"bDcorps\">\n";
					
						// `lastmod`.
					
						$eLastmodListe = $eUrl->getElementsByTagName('lastmod');
					
						if ($eLastmodListe->length > 0)
						{
							$contenuLastmod = $eLastmodListe->item(0)->firstChild->nodeValue;
						}
						else
						{
							$contenuLastmod = '';
						}
					
						$listePages .= '<li><label for="inputLastmodLoc-' . $i . '"><code>lastmod=</code></label><input id="inputLastmodLoc-' . $i . '" type="text" name="lastmodLoc[' . $i . ']" value="' . $contenuLastmod . '" /></li>' . "\n";
					
						// `changefreq`.
					
						$eChangefreqListe = $eUrl->getElementsByTagName('changefreq');
					
						if ($eChangefreqListe->length > 0)
						{
							$contenuChangefreq = $eChangefreqListe->item(0)->firstChild->nodeValue;
						}
						else
						{
							$contenuChangefreq = '';
						}
					
						$listePages .= '<li><label for="changefreqLoc-' . $i . '"><code>changefreq=</code></label>';
						$valeursChangefreq = array ('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
						$listeOption = '';
					
						foreach ($valeursChangefreq as $valeurChangefreq)
						{
							$listeOption .= '<option value="' . $valeurChangefreq . '"';
						
							if ($valeurChangefreq == $contenuChangefreq)
							{
								$listeOption .= ' selected="selected"';
							}
						
							$listeOption .= ">$valeurChangefreq</option>\n";
						}
					
						$listePages .= '<select id="changefreqLoc-' . $i . '" name="changefreqLoc[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
						$listePages .= "</li>\n";

						// `priority`.
					
						$ePriorityListe = $eUrl->getElementsByTagName('priority');
					
						if ($ePriorityListe->length > 0)
						{
							$contenuPriority = $ePriorityListe->item(0)->firstChild->nodeValue;
						}
						else
						{
							$contenuPriority = '';
						}
					
						$listePages .= '<li><label for="priorityLoc-' . $i . '"><code>priority=</code></label>';
						$listeOption = '';

						for ($compteurPriority = 0; $compteurPriority < 11; $compteurPriority++)
						{
							if ($compteurPriority == 10)
							{
								$valeurPriority = '1.0';
							}
							else
							{
								$valeurPriority = "0.$compteurPriority";
							}
						
							$listeOption .= '<option value="' . $valeurPriority . '"';
						
							if ($valeurPriority == $contenuPriority)
							{
								$listeOption .= ' selected="selected"';
							}
						
							$listeOption .= ">$valeurPriority</option>\n";
						}
					
						$listePages .= '<select id="priorityLoc-' . $i . '" name="priorityLoc[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
						$listePages .= "</li>\n";

						$listePages .= '<li class="liParent sitemapImage"><code class="bDtitre">image:image</code>';
						$listePages .= "<ul class=\"bDcorps afficher triable\">\n";
					
						// `image:image`.
					
						$eImageListe = $eUrl->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-image/1.1', 'image');
						$j = 0;
					
						if ($eImageListe->length > 0)
						{
							foreach ($eImageListe as $eImage)
							{
								$imageLoc = $eImage->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-image/1.1', 'loc')->item(0)->firstChild->nodeValue;
								$imageLoc = rawurldecode($imageLoc);
								$listePages .= '<li class="liParent"><label for="inputImageLoc-' . $i . '-' . $j . '"><code>image:loc=</code></label><input id="inputImageLoc-' . $i . '-' . $j . '" class="tresLong" type="text" name="imageLoc[' . $i . '][' . $j . ']" value="' . $imageLoc . '" />';
								$listePages .= "<ul class=\"nonTriable\">\n";
							
								// `caption`.
							
								$eCaptionListe = $eImage->getElementsByTagName('caption');
							
								if ($eCaptionListe->length > 0)
								{
									$contenuCaption = $eCaptionListe->item(0)->firstChild->nodeValue;
								}
								else
								{
									$contenuCaption = '';
								}
							
								$listePages .= '<li><label for="inputCaptionImageLoc-' . $i . '-' . $j . '"><code>image:caption=</code></label><input id="inputCaptionImageLoc-' . $i . '-' . $j . '" type="text" name="captionImageLoc[' . $i . '][' . $j . ']" value="' . $contenuCaption . '" /></li>' . "\n";

								// `title`.
							
								$eTitleListe = $eImage->getElementsByTagName('title');
							
								if ($eTitleListe->length > 0)
								{
									$contenuTitle = $eTitleListe->item(0)->firstChild->nodeValue;
								}
								else
								{
									$contenuTitle = '';
								}
							
								$listePages .= '<li><label for="inputTitleImageLoc-' . $i . '-' . $j . '"><code>image:title=</code></label><input id="inputTitleImageLoc-' . $i . '-' . $j . '" type="text" name="titleImageLoc[' . $i . '][' . $j . ']" value="' . $contenuTitle . '" /></li>' . "\n";

								// `license`.
							
								$eLicenseListe = $eImage->getElementsByTagName('license');
							
								if ($eLicenseListe->length > 0)
								{
									$contenuLicense = $eLicenseListe->item(0)->firstChild->nodeValue;
								}
								else
								{
									$contenuLicense = '';
								}
							
								$listePages .= '<li><label for="inputLicenseImageLoc-' . $i . '-' . $j . '"><code>image:license=</code></label><input id="inputLicenseImageLoc-' . $i . '-' . $j . '" type="text" name="licenseImageLoc[' . $i . '][' . $j . ']" value="' . $contenuLicense . '" /></li>' . "\n";

								// `geo_location`.
							
								$eGeoLocationListe = $eImage->getElementsByTagName('geo_location');
							
								if ($eGeoLocationListe->length > 0)
								{
									$contenuGeoLocation = $eGeoLocationListe->item(0)->firstChild->nodeValue;
								}
								else
								{
									$contenuGeoLocation = '';
								}
							
								$listePages .= '<li><label for="inputGeoLocationImageLoc-' . $i . '-' . $j . '"><code>image:geo_location=</code></label><input id="inputGeoLocationImageLoc-' . $i . '-' . $j . '" type="text" name="geoLocationImageLoc[' . $i . '][' . $j . ']" value="' . $contenuGeoLocation . '" /></li>' . "\n";

								$listePages .= "</ul></li>\n";
								$j++;
							}
						}

						$listePages .= '<li class="liParent"><label for="inputImageLoc-' . $i . '-' . $j . '"><code>image:loc=</code></label><input id="inputImageLoc-' . $i . '-' . $j . '" class="tresLong" type="text" name="imageLoc[' . $i . '][' . $j . ']" value="" />';
						$listePages .= "<ul class=\"nonTriable\">\n";
						$listePages .= '<li><label for="inputCaptionImageLoc-' . $i . '-' . $j . '"><code>image:caption=</code></label><input id="inputCaptionImageLoc-' . $i . '-' . $j . '" type="text" name="captionImageLoc[' . $i . '][' . $j . ']" value="" /></li>' . "\n";
						$listePages .= '<li><label for="inputTitleImageLoc-' . $i . '-' . $j . '"><code>image:title=</code></label><input id="inputTitleImageLoc-' . $i . '-' . $j . '" type="text" name="titleImageLoc[' . $i . '][' . $j . ']" value="" /></li>' . "\n";
						$listePages .= '<li><label for="inputLicenseImageLoc-' . $i . '-' . $j . '"><code>image:license=</code></label><input id="inputLicenseImageLoc-' . $i . '-' . $j . '" type="text" name="licenseImageLoc[' . $i . '][' . $j . ']" value="" /></li>' . "\n";
						$listePages .= '<li><label for="inputGeoLocationImageLoc-' . $i . '-' . $j . '"><code>image:geo_location=</code></label><input id="inputGeoLocationImageLoc-' . $i . '-' . $j . '" type="text" name="geoLocationImageLoc[' . $i . '][' . $j . ']" value="" /></li>' . "\n";
						$listePages .= "</ul></li>\n";
						$listePages .= "</ul></li>\n";
						$listePages .= "</ul></li>\n";
						$listePages .= "</ul></li>\n";
						$i++;
					}
				}
		
				echo '<div class="sousBoite">' . "\n";
				echo '<h3>' . T_("Liste des pages contenues dans le fichier Sitemap du site") . "</h3>\n";
			
				echo '<div class="aideAdminSitemap">' . "\n";
				echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
				echo "<div class=\"bDcorps afficher\">\n";
				echo '<p>' . sprintf(T_("L'URL du champ %1\$s doit être absolue, par exemple %2\$s, et non simplement %3\$s."), '<code>loc</code>', "<code>$urlRacine/exemple.php</code>", '<code>exemple.php</code>') . "</p>\n";
				
				echo '<p>' . T_("Pour enlever une page, simplement supprimer son URL du champ.") . "</p>\n";
			
				echo '<p>' . T_("Aussi, chaque page est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
				echo "</div><!-- /.bDcorps -->\n";
				echo "</div><!-- /.aideAdminSitemap -->\n";
			
				echo "<fieldset>\n";
				echo '<legend>' . T_("Options") . "</legend>\n";
			
				echo '<div id="configActuelleAdminSitemapSite">' . "\n";
				echo '<h4 class="bDtitre">' . T_("Configuration actuelle") . "</h4>\n";
			
				if (empty($listePages))
				{
					$listePages = '<li>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</li>\n";
					echo "<ul class=\"bDcorps afficher\">\n";
				}
				else
				{
					echo "<ul class=\"triable bDcorps afficher\">\n";
				}
				
				echo $listePages;
				echo "</ul>\n";
				echo "</div><!-- /#configActuelleAdminSitemapSite -->\n";
			
				echo '<h4>' . T_("Ajouter une page") . "</h4>\n";
			
				echo "<ul>\n";
				echo '<li><label for="inputLoc-' . $i . '"><code>loc=</code></label><input id="inputLoc-' . $i . '" class="tresLong" type="text" name="locAjout[' . $i . ']" value="" />' . "\n";
				echo "<ul>\n";
				echo '<li class="liParent sitemapBalisesOptionnelles"><span class="bDtitre">Balises optionnelles</span>';
				echo "<ul class=\"bDcorps\">\n";
				// `lastmod`.
				echo '<li><label for="inputLastmodLoc-' . $i . '"><code>lastmod=</code></label><input id="inputLastmodLoc-' . $i . '" type="text" name="lastmodLoc[' . $i . ']" value="" /></li>' . "\n";
			
				// `changefreq`.
			
				echo '<li><label for="changefreqLoc-' . $i . '"><code>changefreq=</code></label>';
				$valeursChangefreq = array ('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
				$listeOption = '';
			
				foreach ($valeursChangefreq as $valeurChangefreq)
				{
					$listeOption .= '<option value="' . $valeurChangefreq . '">' . $valeurChangefreq . "</option>\n";
				}
			
				echo '<select id="changefreqLoc-' . $i . '" name="changefreqLoc[' . $i . ']">' . "\n";
				echo '<option value=""></option>' . "\n";
				echo $listeOption;
				echo "</select>\n";
				echo "</li>\n";

				// `priority`.
			
				echo '<li><label for="priorityLoc-' . $i . '"><code>priority=</code></label>';
				$listeOption = '';

				for ($compteurPriority = 0; $compteurPriority < 11; $compteurPriority++)
				{
					if ($compteurPriority == 10)
					{
						$valeurPriority = '1.0';
					}
					else
					{
						$valeurPriority = "0.$compteurPriority";
					}
				
					$listeOption .= '<option value="' . $valeurPriority . '">' . $valeurPriority . "</option>\n";
				}
			
				echo '<select id="priorityLoc-' . $i . '" name="priorityLoc[' . $i . ']">' . "\n";
				echo '<option value=""></option>' . "\n";
				echo $listeOption;
				echo "</select>\n";
				echo "</li>\n";
			
				// `image:image`.
				echo '<li class="liParent sitemapImage"><code class="bDtitre">image:image</code>';
				echo "<ul class=\"bDcorps afficher\">\n";
				echo '<li class="liParent"><label for="inputImageLoc-' . $i . '-0"><code>image:loc=</code></label><input id="inputImageLoc-' . $i . '-0" class="tresLong" type="text" name="imageLoc[' . $i . '][0]" value="" />';
				echo "<ul class=\"nonTriable\">\n";
				echo '<li><label for="inputCaptionImageLoc-' . $i . '-0"><code>image:caption=</code></label><input id="inputCaptionImageLoc-' . $i . '-0" type="text" name="captionImageLoc[' . $i . '][0]" value="" /></li>' . "\n";
				echo '<li><label for="inputTitleImageLoc-' . $i . '-0"><code>image:title=</code></label><input id="inputTitleImageLoc-' . $i . '-0" type="text" name="titleImageLoc[' . $i . '][0]" value="" /></li>' . "\n";
				echo '<li><label for="inputLicenseImageLoc-' . $i . '-0"><code>image:license=</code></label><input id="inputLicenseImageLoc-' . $i . '-0" type="text" name="licenseImageLoc[' . $i . '][0]" value="" /></li>' . "\n";
				echo '<li><label for="inputGeoLocationImageLoc-' . $i . '-0"><code>image:geo_location=</code></label><input id="inputGeoLocationImageLoc-' . $i . '-0" type="text" name="geoLocationImageLoc[' . $i . '][0]" value="" /></li>' . "\n";
				echo "</ul></li>\n";
				echo "</ul></li>\n";
				echo "</ul></li>\n";
				echo "</ul></li>\n";
				echo "</ul>\n";
				
				echo "<fieldset id=\"optionsAjoutAdminSitemap\">\n";
				echo '<legend class="bDtitre">' . T_("Options d'ajout") . "</legend>\n";
	
				echo '<div class="bDcorps afficher">' . "\n";
				echo "<ul>\n";
				$rssListeLangues = '';
				$rssListeLangues .= '<select name="rssLangueAjout">' . "\n";
			
				foreach ($accueil as $langueAccueil => $urlLangueAccueil)
				{
					$rssListeLangues .= '<option value="' . $langueAccueil . '"';
				
					if ($langueAccueil == $langueParDefaut)
					{
						$rssListeLangues .= ' selected="selected"';
					}
				
					$rssListeLangues .= '>' . $langueAccueil . "</option>\n";
				}
			
				$rssListeLangues .= "</select>";
			
				echo '<li><input id="inputRssAjout" type="checkbox" name="rssAjout" value="ajout" checked="checked" /> <label for="inputRssAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">flux RSS des dernières publications</a> pour la langue %2\$s."), "rss.admin.php?global=site", $rssListeLangues) . "</label></li>\n";
				echo "</ul>\n";
				echo "</div><!-- /.bDcorps -->\n";
				echo "</fieldset>\n";
				
				echo "</fieldset>\n";
			
				echo '<p><input type="submit" name="modifsSitemapSite" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
				echo "</div><!-- /.sousBoite -->\n";
				echo "</div>\n";
				echo "</form>\n";
			}
			elseif (file_exists($cheminFichier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
			}
		
			echo adminMessagesScript($messagesScript);
		}
		elseif ($_POST['sitemap'] == 'ajoutAutomatiqueSite')
		{
			$messagesScript = '';
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Ajout automatique des pages des catégories et du flux RSS des dernières publications dans le fichier Sitemap du site") . "</h3>\n";
			
			$messagesScript .= adminAjoutePagesCategoriesEtFluxRssDansSitemapSite($racine, $urlRacine, $adminPorteDocumentsDroits);
			echo adminMessagesScript($messagesScript);
			echo "</div><!-- /.sousBoite -->\n";
		}
		elseif ($_POST['sitemap'] == 'galeries' && $activerSitemapGaleries)
		{
			$messagesScript = '';
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Génération automatique du fichier Sitemap des galeries") . "</h3>\n";
			
			$messagesScript .= adminGenereSitemapGaleries($racine, $urlRacine, $galerieVignettesParPage, $adminPorteDocumentsDroits);
			echo adminMessagesScript($messagesScript);
			echo "</div><!-- /.sousBoite -->\n";
		}
		elseif ($_POST['sitemap'] == 'index')
		{
			$messagesScript = '';
			$cheminFichier = $racine . '/sitemap_index.xml';
		
			if (!file_exists($cheminFichier))
			{
				if ($adminPorteDocumentsDroits['creer'])
				{
					@file_put_contents($cheminFichier, adminPlanSitemapIndexXml($urlRacine, $activerSitemapGaleries));
					
					if (!file_exists($cheminFichier))
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du fichier Sitemap puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du fichier Sitemap puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
		
			if (file_exists($cheminFichier) && ($contenuSitemap = @file_get_contents($cheminFichier)) !== FALSE)
			{
				echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
				echo "<div>\n";

				$listePages = '';
				$contenuLastmodSite = '';
				$contenuLastmodGaleries = '';
				
				if (!empty($contenuSitemap))
				{
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($contenuSitemap);
					$eSitemapListe = $dom->getElementsByTagName('sitemap');
					
					foreach ($eSitemapListe as $eSitemap)
					{
						$loc = $eSitemap->getElementsByTagName('loc')->item(0)->firstChild->nodeValue;
						$loc = rawurldecode($loc);
						
						if ($loc == $urlRacine . '/sitemap_site.xml')
						{
							$eLastmodListe = $eSitemap->getElementsByTagName('lastmod');
							
							if ($eLastmodListe->length > 0)
							{
								$contenuLastmodSite = $eLastmodListe->item(0)->firstChild->nodeValue;
							}
						}
						elseif ($loc == $urlRacine . '/sitemap_galeries.xml' && $activerSitemapGaleries)
						{
							$eLastmodListe = $eSitemap->getElementsByTagName('lastmod');
							
							if ($eLastmodListe->length > 0)
							{
								$contenuLastmodGaleries = $eLastmodListe->item(0)->firstChild->nodeValue;
							}
						}
					}
				}
				
				$listePages .= '<li class="liParent"><code>loc=' . $urlRacine . '/sitemap_site.xml</code>';
				$listePages .= "<ul>\n";
				$listePages .= '<li><label for="inputLastmodSite"><code>lastmod=</code></label><input id="inputLastmodSite" type="text" name="lastmodSite" value="' . $contenuLastmodSite . '" /></li>' . "\n";
				$listePages .= "</ul></li>\n";
				
				if ($activerSitemapGaleries)
				{
					$listePages .= '<li class="liParent"><code>loc=' . $urlRacine . '/sitemap_galeries.xml</code>';
					$listePages .= "<ul>\n";
					$listePages .= '<li><label for="inputLastmodGaleries"><code>lastmod=</code></label><input id="inputLastmodGaleries" type="text" name="lastmodGaleries" value="' . $contenuLastmodGaleries . '" /></li>' . "\n";
					$listePages .= "</ul></li>\n";
				}
				
				echo '<div class="sousBoite">' . "\n";
				echo '<h3>' . T_("Liste des fichiers Sitemap regroupés dans le fichier d'index Sitemap") . "</h3>\n";
			
				echo "<fieldset>\n";
				echo '<legend>' . T_("Options") . "</legend>\n";
			
				echo '<h4>' . T_("Configuration actuelle") . "</h4>\n";
			
				echo "<ul>\n";
				echo $listePages;
				echo "</ul>\n";
				echo "</fieldset>\n";
			
				echo '<p><input type="submit" name="modifsSitemapIndex" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
				echo "</div><!-- /.sousBoite -->\n";
				echo "</div>\n";
				echo "</form>\n";
			}
			elseif (file_exists($cheminFichier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
			}
			
			echo adminMessagesScript($messagesScript);
		}
		elseif ($_POST['sitemap'] == 'robots')
		{
			$messagesScript = '';
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Vérification de la déclaration du fichier d'index Sitemap dans le fichier <code>robots.txt</code>") . "</h3>\n" ;
			
			$messagesScript .= adminDeclareSitemapDansRobots($racine, $urlRacine, $adminPorteDocumentsDroits);
			echo adminMessagesScript($messagesScript);
			echo "</div><!-- /.sousBoite -->\n";
		}
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite">
	<h2 id="config"><?php echo T_("Configuration actuelle"); ?></h2>
	
	<ul>
		<?php if ($ajouterPagesParCronDansSitemapSite): ?>
			<li><?php echo T_("L'ajout de pages par le cron dans le fichier Sitemap du site est activé") . ' (<code>$ajouterPagesParCronDansSitemapSite = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("L'ajout de pages par le cron dans le fichier Sitemap du site n'est pas activé") . ' (<code>$ajouterPagesParCronDansSitemapSite = FALSE;</code>).'; ?></li>
		<?php endif; ?>
		
		<?php if ($activerSitemapGaleries): ?>
			<li><?php echo T_("Le Sitemap des galeries est activé") . ' (<code>$activerSitemapGaleries = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("Le Sitemap des galeries n'est pas activé") . ' (<code>$activerSitemapGaleries = FALSE;</code>).'; ?></li>
		<?php endif; ?>
	</ul>
	
	<?php if ($adminPorteDocumentsDroits['editer']): ?>
		<p><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/<?php echo rawurlencode($dossierAdmin); ?>/inc/config.inc.php#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
	<?php endif; ?>
</div><!-- /.boite -->

<div class="boite">
	<h2 id="actions"><?php echo T_("Fichiers Sitemap"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<ul>
					<li><input id="inputSitemapSite" type="radio" name="sitemap" value="site" checked="checked" /> <label for="inputSitemapSite"><?php echo T_("Lister les pages du fichier Sitemap du site"); ?></label></li>
					
					<li><input id="inputSitemapAjoutAutomatiqueSite" type="radio" name="sitemap" value="ajoutAutomatiqueSite" /> <label for="inputSitemapAjoutAutomatiqueSite"><?php echo T_("Ajouter automatiquement les pages des catégories et du flux RSS des dernières publications dans le fichier Sitemap du site"); ?></label></li>
					
					<?php if ($activerSitemapGaleries): ?>
						<li><input id="inputSitemapGaleries" type="radio" name="sitemap" value="galeries" /> <label for="inputSitemapGaleries"><?php echo T_("Générer automatiquement le fichier Sitemap des galeries"); ?></label></li>
					<?php endif; ?>
					
					<li><input id="inputSitemapIndex" type="radio" name="sitemap" value="index" /> <label for="inputSitemapIndex"><?php echo T_("Lister les fichiers Sitemap regroupés dans le fichier d'index Sitemap"); ?></label></li>
					<li><input id="inputSitemapRobots" type="radio" name="sitemap" value="robots" /> <label for="inputSitemapRobots"><?php printf(T_("Vérifier la déclaration du fichier d'index Sitemap dans le fichier %1\$s"), '<code>robots.txt</code>'); ?></label></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="action" value="<?php echo T_('Choisir'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
