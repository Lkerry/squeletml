<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Fichier Sitemap");
$boitesDeroulantes = '.aideAdminSitemap #configActuelleAdminSitemap #optionsAjoutAdminSitemap';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion du fichier Sitemap"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_GET['action']) && $_GET['action'] == 'lister')
	{
		$messagesScript = '';
		$cheminFichier = $racine . '/sitemap_site.xml';
		
		if (!file_exists($cheminFichier))
		{
			if ($adminPorteDocumentsDroits['creer'])
			{
				adminAjouteUrlDansSitemap($racine, array (), $adminPorteDocumentsDroits);
				
				if (!file_exists($cheminFichier))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion du fichier Sitemap est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion du fichier Sitemap est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($cheminFichier) && ($contenuSitemap = @file_get_contents($cheminFichier)) !== FALSE)
		{
			echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
			echo "<div>\n";
		
			if (!empty($contenuSitemap))
			{
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($contenuSitemap);
				$eUrlListe = $dom->getElementsByTagName('url');
				$listePages = '';
				$i = 0;
				
				foreach ($eUrlListe as $eUrl)
				{
					$loc = $eUrl->getElementsByTagName('loc')->item(0)->firstChild->nodeValue;
					$listePages .= '<li class="liParent"><input class="tresLong" type="text" name="loc[' . $i . ']" value="' . $loc . '" />';
					$listePages .= "<ul class=\"nonTriable\">\n";
					
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
					
					$listePages .= '<li><label for="inputLastmodLoc-' . $i . '">lastmod=</label><input id="inputLastmodLoc-' . $i . '" type="text" name="lastmodLoc[' . $i . ']" value="' . $contenuLastmod . '" /></li>' . "\n";
					
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
					
					$listePages .= '<li><label for="changefreqLoc-' . $i . '">changefreq=</label>';
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
					
					$listePages .= '<li><label for="priorityLoc-' . $i . '">priority=</label>';
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

					$listePages .= "</ul></li>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages contenues dans le fichier Sitemap") . "</h3>\n";
			
			echo '<div class="aideAdminSitemap">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps afficher\">\n";
			echo '<p><em>' . T_("Prendre note que le fichier Sitemap des galeries n'est pas géré dans cette interface. Ce dernier est généré automatiquement.") . "</em></p>\n";
			
			echo '<p>' . T_("La syntaxe est la suivante:") . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>loc\n";
			echo "<ul>\n";
			echo "<li>lastmod</li>\n";
			echo "<li>changefreq</li>\n";
			echo "<li>priority</li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . T_("Pour enlever une page, simplement supprimer son URL du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque page est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.aideAdminSitemap -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div id="configActuelleAdminSitemap">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Configuration actuelle") . "</h4>\n";
			
			echo "<ul class=\"triable bDcorps afficher\">\n";
			
			if (!empty($listePages))
			{
				echo $listePages;
			}
			else
			{
				echo '<li>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</li>\n";
			}
			
			echo "</ul>\n";
			echo "</div><!-- /#configActuelleAdminSitemap -->\n";
			
			echo '<h4>' . T_("Ajouter une page") . "</h4>\n";
			
			echo "<ul>\n";
			echo '<li><input class="tresLong" type="text" name="loc[' . $i . ']" value="" />' . "\n";
			echo "<ul>\n";

			// `lastmod`.
			echo '<li><label for="inputLastmodLoc-' . $i . '">lastmod=</label><input id="inputLastmodLoc-' . $i . '" type="text" name="lastmodLoc[' . $i . ']" value="" /></li>' . "\n";
			
			// `changefreq`.
			
			echo '<li><label for="changefreqLoc-' . $i . '">changefreq=</label>';
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
			
			echo '<li><label for="priorityLoc-' . $i . '">priority=</label>';
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

			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo "<fieldset id=\"optionsAjoutAdminSitemap\">\n";
			echo '<legend class="bDtitre">' . T_("Options d'ajout") . "</legend>\n";
			
			echo '<div class="bDcorps afficher">' . "\n";
			echo "<ul>\n";
			$cheminFichierRss = cheminConfigFluxRssGlobal($racine, 'site');
			
			if ($cheminFichierRss)
			{
				$rssPages = super_parse_ini_file($cheminFichierRss, TRUE);
				
				if (!empty($rssPages))
				{
					$rssListeLangues = '';
					$rssListeLangues .= '<select name="rssLangueAjout">' . "\n";
					
					foreach ($rssPages as $rssCodeLangue => $rssLangueInfos)
					{
						$rssListeLangues .= "<option value=\"$rssCodeLangue\">$rssCodeLangue</option>\n";
					}
					
					$rssListeLangues .= "</select>";
					
					echo '<li><input id="inputRssAjout" type="checkbox" name="rssAjout" value="ajout" checked="checked" /> <label for="inputRssAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">flux RSS des dernières publications</a> pour la langue %2\$s."), "rss.admin.php?global=site", $rssListeLangues) . "</label></li>\n";
				}
			}
			
			echo "</ul>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</fieldset>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsSitemap" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
			echo "</div>\n";
			echo "</form>\n";
			echo "</div><!-- /.sousBoite -->\n";
		}
		elseif (file_exists($cheminFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
	}
	
	if (isset($_POST['modifsSitemap']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications du fichier Sitemap") . "</h3>\n" ;
	
		$contenuFichierTableau = array ();
		
		if (isset($_POST['loc']))
		{
			foreach ($_POST['loc'] as $cle => $loc)
			{
				$loc = securiseTexte($loc);
				
				if (!empty($loc))
				{
					$contenuFichierTableau[$loc] = array ();
					
					if (!empty($_POST['lastmodLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['lastmodLoc'] = securiseTexte($_POST['lastmodLoc'][$cle]);
					}
					
					if (!empty($_POST['changefreqLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['changefreq'] = securiseTexte($_POST['changefreqLoc'][$cle]);
					}
					
					if (!empty($_POST['priorityLoc'][$cle]))
					{
						$contenuFichierTableau[$loc]['priorityLoc'] = securiseTexte($_POST['priorityLoc'][$cle]);
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
			
			$contenuFichier .= "  </url>\n";
		}

		$contenuFichier .= '</urlset>';
		$messagesScript .= adminEnregistreSitemap($racine, $contenuFichier, $adminPorteDocumentsDroits);
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
		
		if (isset($_POST['rssAjout']) && !empty($_POST['urlAjout']) && !empty($_POST['rssLangueAjout']))
		{
			$messagesScript = '';
			$urlAjout = securiseTexte($_POST['urlAjout']);
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
			
			array_unshift($contenuFichierRssTableau[$rssLangueAjout], "pages[]=$urlAjout\n");
			
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
	?>
</div><!-- /#boiteMessages -->

<?php if (!isset($_GET['action']) || $_GET['action'] != 'lister'): ?>
	<div class="boite">
		<h2 id="actions"><?php echo T_("Actions"); ?></h2>
	
		<ul>
			<li><a href="<?php echo $adminAction; ?>?action=lister#messages"><?php echo T_('Lister les pages contenues dans le fichier Sitemap.'); ?></a></li>
		</ul>
	</div><!-- /.boite -->
<?php endif; ?>

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
