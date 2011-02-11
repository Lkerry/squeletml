<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Catégories");
$boitesDeroulantes = '#configActuelleAdminCat #optionsAjoutAdminCat .aideAdminCat .contenuFichierPourSauvegarde .pagesCategorie';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des catégories"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_GET['action']) && $_GET['action'] == 'lister')
	{
		$messagesScript = '';
		$cheminFichier = cheminConfigCategories($racine);
		
		if (!$cheminFichier)
		{
			$cheminFichier = cheminConfigCategories($racine, TRUE);
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				if (!@touch($cheminFichier))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($cheminFichier) && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
		{
			echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
			echo "<div>\n";
		
			if (!empty($categories))
			{
				$listeCategories = array ();
				$listePages = '';
				$i = 0;
				
				foreach ($categories as $categorie => $categorieInfos)
				{
					$listeCategories[] = $categorie;
					$listePages .= '<li class="liParent"><input type="text" name="cat[' . $i . ']" value="' . $categorie . '" />';
					$listePages .= "<ul class=\"nonTriable\">\n";
					
					// Langue.
					
					if (!isset($categorieInfos['langueCat']))
					{
						$categorieInfos['langueCat'] = '';
					}
					
					$listePages .= '<li><label for="langueCat-' . $i . '"><code>langueCat=</code></label>';
					$listeOption = '';
					
					foreach ($accueil as $codeLangue => $urlLangue)
					{
						$listeOption .= '<option value="' . $codeLangue . '"';
						
						if ($codeLangue == $categorieInfos['langueCat'])
						{
							$listeOption .= ' selected="selected"';
						}
						
						$listeOption .= '>' . $codeLangue . "</option>\n";
					}
					
					if (!empty($listeOption))
					{
						$listePages .= '<select id="langueCat-' . $i . '" name="langueCat[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input id="langueCat-' . $i . '" type="text" name="langueCat[' . $i . ']" value="' . $categorieInfos['langueCat'] . '" />';
					}
					
					$listePages .= "</li>\n";
					
					// URL.
					
					if (!isset($categorieInfos['urlCat']))
					{
						$categorieInfos['urlCat'] = '';
					}
					
					$listePages .= '<li><label for="inputUrlCat-' . $i . '"><code>urlCat=</code></label><input id="inputUrlCat-' . $i . '" class="long" type="text" name="urlCat[' . $i . ']" value="' . $categorieInfos['urlCat'] . '" /></li>' . "\n";
					
					// Catégorie parente.
					
					if (!isset($categorieInfos['catParente']))
					{
						$categorieInfos['catParente'] = '';
					}
					
					$listePages .= '<li><label for="catParente-' . $i . '"><code>catParente=</code></label>';
					$listeOption = '';
					
					foreach ($categories as $cat => $catInfos)
					{
						if ($cat != $categorie)
						{
							$listeOption .= '<option value="' . $cat . '"';
							
							if ($cat == $categorieInfos['catParente'])
							{
								$listeOption .= ' selected="selected"';
							}
							
							$listeOption .= '>' . $cat . "</option>\n";
						}
					}
					
					if (!empty($listeOption))
					{
						$listePages .= '<select id="catParente-' . $i . '" name="catParente[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input id="catParente-' . $i . '" type="text" name="catParente[' . $i . ']" value="' . $categorieInfos['catParente'] . '" />';
					}
					
					$listePages .= "</li>\n";
					
					// Pages.
					
					if (!empty($categorieInfos['pages']))
					{
						$listePages .= '<li class="liParent pagesCategorie"><code class="bDtitre">pages</code>';
						$listePages .= "<ul class=\"bDcorps afficher triable\">\n";
						$j = 0;
						
						foreach ($categorieInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$listePages .= '<li><label for="inputUrl-' . $i . '-' . $j . '"><code>pages[]=</code></label><input id="inputUrl-' . $i . '-' . $j . '" class="long" type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
							$j++;
						}

						$listePages .= "</ul></li>\n";
					}
					
					$listePages .= "</ul></li>\n";
#					$listePages .= "</ul>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages classées par catégorie") . "</h3>\n";
			
			echo '<div class="aideAdminCat">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps afficher\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant une catégorie. À l'intérieur d'une section, chaque page est déclarée sous la forme %1\$s. Optionnellement, vous pouvez préciser la langue à laquelle appartient une catégorie, et ce à l'aide du paramètre %2\$s. Vous pouvez également préciser l'URL relative de la page d'accueil de chaque catégorie à l'aide du paramètre %3\$s (dans ce cas, vous devez créer la page d'accueil manuellement) ainsi que la catégorie parente, s'il y a lieu, grâce à %4\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>', '<code>langueCat=' . T_("langue à laquelle appartient la catégorie") . '</code>', '<code>urlCat=' . T_("URL relative de la page d'accueil de la catégorie") . '</code>', '<code>catParente=' . T_("identifiant de la catégorie parente") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>Chiens\n";
			echo "<ul>\n";
			echo "<li><code>langueCat=fr</code></li>\n";
			echo "<li><code>urlCat=animaux/chiens/</code></li>\n";
			echo "<li><code>catParente=Animaux</code></li>\n";
			echo "<li><code>pages</code>";
			echo "<ul>";
			echo "<li><code>pages[]=animaux/chiens/husky.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à la catégorie en français «%1\$s», accessible à l'adresse %2\$s, enfant de la catégorie «%3\$s» et contenant une page dont l'URL est %4\$s."), "Chiens", "<code>$urlRacine/animaux/chiens/</code>", "Animaux", "<code>$urlRacine/animaux/chiens/husky.php</code>") . "</p>\n";
			
			echo '<p>' . sprintf(T_("Si la langue d'une catégorie n'est pas précisée à l'aide du paramètre %1\$s, la langue sera celle déclarée par défaut dans le fichier de configuration du site."), '<code>langueCat</code>') . "</p>\n";
			
			echo '<p>' . sprintf(T_("Aussi, si la page d'accueil d'une catégorie n'est pas précisée à l'aide du paramètre %1\$s, l'URL sera générée automatiquement, et ce sous la forme %2\$s (%3\$s représente la variable %4\$s filtrée). Dans ce cas, il n'est pas nécessaire de créer la page d'accueil manuellement puisque %5\$s est une page livrée par défaut avec Squeletml et gérant l'affichage des articles d'une catégorie."), '<code>urlCat</code>', '<code>$urlRacine/categorie.php?id=idCategorieFiltre</code>', '<code>idCategorieFiltre</code>', '<code>$idCategorie</code>', '<code>categorie.php</code>') . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une catégorie ou une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.aideAdminCat -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div id="configActuelleAdminCat">' . "\n";
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
			echo "</div><!-- /#configActuelleAdminCat -->\n";
			
			echo '<h4>' . T_("Ajouter une page") . "</h4>\n";
			
			echo '<p>' . T_("Il est possible d'ajouter une page à plus d'une catégorie. Il est aussi possible de créer une catégorie. Pour ce faire, ajouter «Nouvelle catégorie» à votre sélection et saisir le nom dans le champ. Pour créer plus d'une catégorie, séparer les noms par un carré (exemple: <code>animaux#chiens</code>).") . "</p>\n";
			
			echo "<ul>\n";
			echo '<li><select name="catAjoutSelect[]" multiple="multiple">' . "\n";
			echo '<option value="nouvelleCategorie">' . T_("Nouvelle catégorie:") . "</option>\n";
			
			if (!empty($listeCategories))
			{
				foreach ($listeCategories as $c)
				{
					echo '<option value="' . $c . '">' . $c . "</option>\n";
				}
			}
			
			echo '</select> <input type="text" name="catAjoutInput" value="" />' . "\n";
			echo "<ul>\n";
			echo '<li><label for="inputUrlAjout"><code>pages[]=</code></label><input id="inputUrlAjout" type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo "<fieldset id=\"optionsAjoutAdminCat\">\n";
			echo '<legend class="bDtitre">' . T_("Options d'ajout") . "</legend>\n";
			
			echo '<div class="bDcorps afficher">' . "\n";
			echo "<ul>\n";
			echo '<li><input id="inputParentAjout" type="checkbox" name="parentAjout" value="ajout" checked="checked" /> <label for="inputParentAjout">' . T_("S'il y a lieu, inclure la page dans la catégorie parente.") . "</label>\n";
			echo "<ul>\n";
			echo '<li><input id="inputParentsAjout" type="checkbox" name="parentsAjout" value="ajout" checked="checked" /> <label for="inputParentsAjout">' . T_("S'il y a lieu, inclure la page également dans les catégories parentes indirectes.") . "</label></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . T_("Explications: par exemple, une page est ajoutée à la catégorie «Miniatures». Cette catégorie a comme parent «Chiens», qui a elle-même comme parent la catégorie «Animaux». Si l'option d'ajout dans la catégorie parente est sélectionnée, la page sera ajoutée dans la catégorie «Miniatures» et dans la catégorie parente «Chiens». Aussi, si l'option d'ajout dans les catégories parentes indirectes est sélectionnée, la page sera également ajoutée à la catégorie «Animaux».") . "</p>\n";
			echo "</li>\n";
			
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
			
			$rssListeLangues .= "</select>\n";
			
			echo '<li><input id="inputRssAjout" type="checkbox" name="rssAjout" value="ajout" checked="checked" /> <label for="inputRssAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">flux RSS des dernières publications</a> pour la langue %2\$s."), "rss.admin.php?global=site", $rssListeLangues) . "</label></li>\n";
			echo '<li><input id="inputSitemapAjout" type="checkbox" name="sitemapAjout" value="ajout" checked="checked" /> <label for="inputSitemapAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">fichier Sitemap du site</a>."), 'sitemap.admin.php?sitemap=site') . "</label></li>\n";
			echo "</ul>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</fieldset>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsCategories" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
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
	
	if (isset($_POST['modifsCategories']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications des catégories") . "</h3>\n" ;
	
		$contenuFichierTableau = array ();
		
		if (isset($_POST['cat']))
		{
			foreach ($_POST['cat'] as $cle => $cat)
			{
				$cat = securiseTexte($cat);
				
				if (!empty($cat) && (!empty($_POST['langueCat'][$cle]) || !empty($_POST['catParente'][$cle]) || !empty($_POST['urlCat'][$cle]) || !empty($_POST['url'][$cle])))
				{
					$contenuFichierTableau[$cat] = array ();
					$contenuFichierTableau[$cat]['infos'] = array ();
					$contenuFichierTableau[$cat]['pages'] = array ();
					
					if (!empty($_POST['langueCat'][$cle]))
					{
						$langueCat = securiseTexte($_POST['langueCat'][$cle]);
						$contenuFichierTableau[$cat]['infos'][] = "langueCat=$langueCat\n";
					}
					else
					{
						$langueCat = $langueParDefaut;
						$contenuFichierTableau[$cat]['infos'][] = "langueCat=$langueCat\n";
					}
					
					if (!empty($_POST['urlCat'][$cle]))
					{
						$contenuFichierTableau[$cat]['infos'][] = 'urlCat=' . securiseTexte($_POST['urlCat'][$cle]) . "\n";
					}
					else
					{
						$urlCat = 'urlCat=categorie.php?id=' . filtreChaine($racine, $cat);
						
						if (estCatSpeciale($cat))
						{
							$urlCat .= "&amp;langue=$langueCat";
						}
						
						$contenuFichierTableau[$cat]['infos'][] = "$urlCat\n";
					}
					
					if (!empty($_POST['catParente'][$cle]))
					{
						$contenuFichierTableau[$cat]['infos'][] = 'catParente=' . securiseTexte($_POST['catParente'][$cle]) . "\n";
					}
					
					if (!empty($_POST['url'][$cle]))
					{
						foreach ($_POST['url'][$cle] as $page)
						{
							if (!empty($page) && !preg_grep('/^pages\[\]=' . preg_quote(securiseTexte($page), '/') . "\n/", $contenuFichierTableau[$cat]['pages']))
							{
								$contenuFichierTableau[$cat]['pages'][] = 'pages[]=' . securiseTexte($page) . "\n";
							}
						}
					}
				}
			}
		}
		
		if (!empty($_POST['catAjoutSelect']) && !empty($_POST['urlAjout']))
		{
			$catAjout = array ();
			
			foreach ($_POST['catAjoutSelect'] as $catAjoutSelect)
			{
				if ($catAjoutSelect == 'nouvelleCategorie')
				{
					if (!empty($_POST['catAjoutInput']))
					{
						$catAjout = array_merge($catAjout, explode('#', securiseTexte($_POST['catAjoutInput'])));
					}
				}
				else
				{
					$catAjout[] = securiseTexte($catAjoutSelect);
				}
			}
			
			$cheminFichier = cheminConfigCategories($racine);
			
			if (isset($_POST['parentAjout']) && $cheminFichier && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
			{
				if (isset($_POST['parentsAjout']))
				{
					$parentsAjout = array ();
					
					foreach ($catAjout as $c)
					{
						if (!empty($categories[$c]['catParente']))
						{
							if (!in_array($categories[$c]['catParente'], $parentsAjout))
							{
								$parentsAjout[] = $categories[$c]['catParente'];
							}
							
							$parentsAjout = array_merge($parentsAjout, categoriesParentesIndirectes($categories, $categories[$c]['catParente'], $langueParDefaut));
						}
					}
					
					foreach ($parentsAjout as $parent)
					{
						if (!in_array($parent, $catAjout))
						{
							$catAjout[] = $parent;
						}
					}
				}
				else
				{
					foreach ($catAjout as $c)
					{
						if (!empty($categories[$c]['catParente']) && !in_array($categories[$c]['catParente'], $catAjout))
						{
							$catAjout[] = $categories[$c]['catParente'];
						}
					}
				}
			}
			
			foreach ($catAjout as $c)
			{
				if (!isset($contenuFichierTableau[$c]))
				{
					$contenuFichierTableau[$c] = array ();
					$contenuFichierTableau[$c]['infos'] = array ();
					$contenuFichierTableau[$c]['pages'] = array ();
				}

				if (!preg_grep('/^pages\[\]=' . preg_quote(securiseTexte($_POST['urlAjout']), '/') . "\n/", $contenuFichierTableau[$c]['pages']))
				{
					array_unshift($contenuFichierTableau[$c]['pages'], 'pages[]=' . securiseTexte($_POST['urlAjout']) . "\n");
				}
			}
		}
		
		$contenuFichier = '';
		
		foreach ($contenuFichierTableau as $categorie => $categorieInfos)
		{
			if (!empty($categorieInfos['infos']) || !empty($categorieInfos['pages']))
			{
				$contenuFichier .= "[$categorie]\n";
				
				foreach ($categorieInfos['infos'] as $ligne)
				{
					$contenuFichier .= $ligne;
				}
				
				foreach ($categorieInfos['pages'] as $ligne)
				{
					$contenuFichier .= $ligne;
				}
				
				$contenuFichier .= "\n";
			}
		}
		
		$cheminFichier = cheminConfigCategories($racine);
		
		if (!$cheminFichier)
		{
			$cheminFichier = cheminConfigCategories($racine, TRUE);
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				if (!@touch($cheminFichier))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		
		$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
		
		if (file_exists($cheminFichier))
		{
			if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
			{
				$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

				$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . $cheminFichier . '</code>') . "</p>\n";
			}
			else
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				
				$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
			}
		}
		else
		{
			$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
		}

		$messagesScript .= "<div class=\"bDcorps afficher\">\n";
		$messagesScript .= '<pre id="contenuFichierCategories">' . $contenuFichier . "</pre>\n";
		
		$messagesScript .= "<ul>\n";
		$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierCategories');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
		$messagesScript .= "</ul>\n";
		$messagesScript .= "</div><!-- /.bDcorps -->\n";
		$messagesScript .= "</li>\n";
		
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
			
			if (!preg_grep('/^pages\[\]=' . preg_quote($urlAjout, '/') . "\n/", $contenuFichierRssTableau[$rssLangueAjout]))
			{
				array_unshift($contenuFichierRssTableau[$rssLangueAjout], "pages[]=$urlAjout\n");
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
		
		if (isset($_POST['sitemapAjout']) && !empty($_POST['urlAjout']))
		{
			$urlAjout = $urlRacine . '/' . superRawurlencode($_POST['urlAjout']);
			$messagesScript = adminAjouteUrlDansSitemap($racine, 'site', array ($urlAjout => array ()), $adminPorteDocumentsDroits);
			
			echo adminMessagesScript($messagesScript, T_("Ajout dans le fichier Sitemap du site"));
		}
	}
	?>
</div><!-- /#boiteMessages -->

<?php if (!isset($_GET['action']) || $_GET['action'] != 'lister'): ?>
	<div class="boite">
		<h2 id="actions"><?php echo T_("Actions"); ?></h2>
	
		<ul>
			<li><a href="<?php echo $adminAction; ?>?action=lister#messages"><?php echo T_('Lister les pages classées par catégorie.'); ?></a></li>
		</ul>
	</div><!-- /.boite -->
<?php endif; ?>

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
