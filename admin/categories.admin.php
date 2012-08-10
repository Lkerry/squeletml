<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Catégories");
$boitesDeroulantes = '#configActuelleAdminCat #optionsNouvelleCatAdminCat #optionsAjoutAdminCat .aideAdminCat .contenuFichierPourSauvegarde .pagesCategorie';
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
			
			if (!@touch($cheminFichier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
			}
		}
		
		if (file_exists($cheminFichier) && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
		{
			uksort($categories, 'strnatcasecmp');
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
					$listePages .= '<li class="liParent"><input class="long" type="text" name="cat[' . $i . ']" value="' . securiseTexte($categorie) . '" />';
					$listePages .= "<ul class=\"nonTriable\">\n";
					
					// URL.
					
					if (empty($categorieInfos['url']))
					{
						$categorieInfos['url'] = urlCat($categorieInfos, $categorie);
					}
					
					$listePages .= '<li><label for="inputUrl-' . $i . '"><code>url=</code></label><input id="inputUrl-' . $i . '" class="long" type="text" name="url[' . $i . ']" value="' . securiseTexte($categorieInfos['url']) . '" />';
					
					if (strpos($categorieInfos['url'], 'categorie.php?') !== 0)
					{
						$cheminPageCategorie = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($categorieInfos['url']));
						$listePages .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminPageCategorie) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminPageCategorie)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPageCategorie)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPageCategorie)) . '" width="16" height="16" /></a>';
					}
					
					$listePages .= "</li>\n";
					
					// Catégorie parente.
					
					if (!isset($categorieInfos['parent']))
					{
						$categorieInfos['parent'] = '';
					}
					
					$listePages .= '<li><label for="parent-' . $i . '"><code>parent=</code></label>';
					$listeOption = '';
					
					foreach ($categories as $cat => $catInfos)
					{
						if ($cat != $categorie)
						{
							$listeOption .= '<option value="' . securiseTexte($cat) . '"';
							
							if ($cat == $categorieInfos['parent'])
							{
								$listeOption .= ' selected="selected"';
							}
							
							$listeOption .= '>' . securiseTexte($cat) . "</option>\n";
						}
					}
					
					if (!empty($listeOption))
					{
						$listePages .= '<select id="parent-' . $i . '" name="parent[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input id="parent-' . $i . '" class="long" type="text" name="parent[' . $i . ']" value="' . securiseTexte($categorieInfos['parent']) . '" />';
					}
					
					$listePages .= "</li>\n";
					
					// Langue.
					
					if (empty($categorieInfos['langue']))
					{
						$categorieInfos['langue'] = $langueParDefaut;
					}
					
					$listePages .= '<li><label for="langue-' . $i . '"><code>langue=</code></label>';
					$listeOption = '';
					
					foreach ($accueil as $codeLangue => $urlLangue)
					{
						$listeOption .= '<option value="' . $codeLangue . '"';
						
						if ($codeLangue == $categorieInfos['langue'])
						{
							$listeOption .= ' selected="selected"';
						}
						
						$listeOption .= '>' . $codeLangue . "</option>\n";
					}
					
					if (!empty($listeOption))
					{
						$listePages .= '<select id="langue-' . $i . '" name="langue[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input id="langue-' . $i . '" type="text" name="langue[' . $i . ']" value="' . securiseTexte($categorieInfos['langue']) . '" />';
					}
					
					$listePages .= "</li>\n";
					
					// RSS.
					
					if (!isset($categorieInfos['rss']))
					{
						$categorieInfos['rss'] = 1;
					}
					
					$listePages .= '<li><label for="rss-' . $i . '"><code>rss=</code></label>';
					$listePages .= '<select id="rss-' . $i . '" name="rss[' . $i . ']">' . "\n";
					$listePages .= '<option value="1"';
					
					if ($categorieInfos['rss'] == 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Activé") . "</option>\n";
					$listePages .= '<option value="0"';
					
					if ($categorieInfos['rss'] != 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Désactivé") . "</option>\n";
					$listePages .= "</select>\n";
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
							$listePages .= '<li><label for="inputUrlPages-' . $i . '-' . $j . '"><code>pages[]=</code></label><input id="inputUrlPages-' . $i . '-' . $j . '" class="long" type="text" name="urlPages[' . $i . '][]" value="' . securiseTexte($page) . '" />';
							$cheminPage = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($page));
							$listePages .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminPage) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminPage)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPage)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPage)) . '" width="16" height="16" /></a>';
							$listePages .= "</li>\n";
							$j++;
						}
						
						$listePages .= "</ul></li>\n";
					}
					
					$listePages .= "</ul></li>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages classées par catégorie") . "</h3>\n";
			
			echo '<div class="aideAdminCat aide">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant une catégorie. À l'intérieur d'une section, chaque page est déclarée sous la forme %1\$s. Optionnellement, vous pouvez préciser la langue à laquelle appartient une catégorie, et ce à l'aide du paramètre %2\$s. Vous pouvez également préciser l'URL relative de la page d'accueil de chaque catégorie à l'aide du paramètre %3\$s ainsi que la catégorie parente, s'il y a lieu, grâce à %4\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>', '<code>langue=' . T_("langue à laquelle appartient la catégorie") . '</code>', '<code>url=' . T_("URL relative de la page d'accueil de la catégorie") . '</code>', '<code>parent=' . T_("identifiant de la catégorie parente") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>Chiens\n";
			echo "<ul>\n";
			echo "<li><code>langue=fr</code></li>\n";
			echo "<li><code>url=animaux/chiens/</code></li>\n";
			echo "<li><code>parent=Animaux</code></li>\n";
			echo "<li><code>pages</code>";
			echo "<ul>";
			echo "<li><code>pages[]=animaux/chiens/husky.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à la catégorie en français «%1\$s», accessible à l'adresse %2\$s, enfant de la catégorie «%3\$s» et contenant une page dont l'URL est %4\$s."), "Chiens", '<code>' . securiseTexte("$urlRacine/animaux/chiens/") . '</code>', "Animaux", '<code>' . securiseTexte("$urlRacine/animaux/chiens/husky.php") . '</code>') . "</p>\n";
			
			echo '<p>' . sprintf(T_("Si la langue d'une catégorie n'est pas précisée à l'aide du paramètre %1\$s, la langue sera celle déclarée par défaut dans le fichier de configuration du site."), '<code>langue</code>') . "</p>\n";
			
			echo '<p>' . sprintf(T_("Aussi, si la page d'accueil d'une catégorie n'est pas précisée à l'aide du paramètre %1\$s, l'URL sera générée automatiquement, et ce sous la forme %2\$s (%3\$s représente la variable %4\$s filtrée). Dans ce cas, il n'est pas nécessaire de créer la page d'accueil manuellement puisque %5\$s est une page livrée par défaut avec Squeletml et gérant l'affichage des articles d'une catégorie."), '<code>url</code>', '<code>$urlRacine/categorie.php?id=idCategorieFiltre</code>', '<code>idCategorieFiltre</code>', '<code>$idCategorie</code>', '<code>categorie.php</code>') . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Prendre note que si une URL contient des caractères spéciaux, elle devra être fournie sous forme encodée. Le plus simple est de copier l'adresse dans la barre de navigation du navigateur utilisé et de coller le résultat dans le champ approprié. L'URL racine sera automatiquement supprimée pour convertir l'adresse fournie en adresse relative.") . "</p>\n";
			
			echo '<p>' . T_("Voici un exemple pour l'URL d'une nouvelle catégorie:") . "</p>\n";
			
			echo "<p><code>http://www.monsite.ext/animaux/canid%C3%A9s/</code></p>\n";
			
			echo '<p>' . T_("et pour une nouvelle page dans cette catégorie:") . "</p>\n";
			
			echo "<p><code>http://www.monsite.ext/animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></p>\n";
			
			echo '<p>' . T_("Le résultat sera semblable à ci-dessous:") . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>Canidés\n";
			echo "<ul>\n";
			echo "<li><code>langue=fr</code></li>\n";
			echo "<li><code>url=animaux/canid%C3%A9s/</code></li>\n";
			echo "<li><code>parent=Animaux</code></li>\n";
			echo "<li><code>pages</code>";
			echo "<ul>";
			echo "<li><code>pages[]=animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
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
				echo "<ul class=\"bDcorps\">\n";
			}
			else
			{
				echo "<ul class=\"triable bDcorps\">\n";
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
					echo '<option value="' . encodeTexte($c) . '">' . securiseTexte($c) . "</option>\n";
				}
			}
			
			echo '</select> <input class="long" type="text" name="catAjoutInput" value="" />' . "\n";
			echo "<ul>\n";
			echo '<li><label for="inputUrlAjout"><code>pages[]=</code></label><input id="inputUrlAjout" class="long" type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo "<fieldset id=\"optionsNouvelleCatAdminCat\">\n";
			echo '<legend class="bDtitre">' . T_("Nouvelle catégorie") . "</legend>\n";
			
			echo '<div class="bDcorps afficher">' . "\n";
			echo '<p><label for="nouvelleCatInputUrl">' . T_("Si nouvelle catégorie, URL de la page Web (laisser vide pour génération automatique; si plusieurs catégories sont créées, il y aura génération automatique):") . "</label><br />\n";
			echo '<input id="nouvelleCatInputUrl" class="long" type="text" name="urlNouvelleCat" /></p>' . "\n";
			
			$listeLangues = '';
			$listeLangues .= '<select id="nouvelleCatLangue" name="mettreEnLigneLangue">' . "\n";
			
			foreach ($accueil as $langueAccueil => $urlLangueAccueil)
			{
				$listeLangues .= '<option value="' . $langueAccueil . '"';
				
				if ($langueAccueil == $langueParDefaut)
				{
					$listeLangues .= ' selected="selected"';
				}
				
				$listeLangues .= '>' . $langueAccueil . "</option>\n";
			}
			
			$listeLangues .= "</select>";
			
			echo '<p><label for="nouvelleCatLangue">' . T_("Si nouvelle catégorie, langue:") . "</label><br />\n$listeLangues</p>\n";
			
			echo '<p><label for="nouvelleCatRss">' . T_("Si nouvelle catégorie, RSS:") . "</label><br />\n";
			echo '<select id="nouvelleCatRss" name="mettreEnLigneRss">' . "\n";
			echo '<option value="1" selected="selected">' . T_("Activé") . "</option>\n";
			echo '<option value="0">' . T_("Désactivé") . "</option>\n";
			echo "</select></p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</fieldset>\n";
			
			echo "<fieldset id=\"optionsAjoutAdminCat\">\n";
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
			
			$rssListeLangues .= "</select>\n";
			
			echo '<li><input id="inputRssAjout" type="checkbox" name="rssAjout" value="ajout" checked="checked" /> <label for="inputRssAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">flux RSS des dernières publications</a> pour la langue %2\$s."), 'rss.admin.php?global=site', $rssListeLangues) . "</label></li>\n";
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
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
	}
	
	if (isset($_POST['modifsCategories']))
	{
		$messagesScript = '';
		$contenuFichierTableau = array ();
		
		if (isset($_POST['cat']))
		{
			foreach ($_POST['cat'] as $cle => $cat)
			{
				$urlCle = '';
				
				if (!empty($_POST['url'][$cle]))
				{
					$urlCle = supprimeUrlRacine($urlRacine, $_POST['url'][$cle]);
				}
				
				$urlPagesCle = array ();
				
				if (!empty($_POST['urlPages'][$cle]))
				{
					foreach ($_POST['urlPages'][$cle] as $page)
					{
						$page = supprimeUrlRacine($urlRacine, $page);
						
						if (!empty($page))
						{
							$urlPagesCle[] = $page;
						}
					}
				}
				
				if (!empty($cat) && (!empty($_POST['langue'][$cle]) || !empty($_POST['parent'][$cle]) || !empty($urlCle) || !empty($urlPagesCle)))
				{
					$contenuFichierTableau[$cat] = array ();
					$contenuFichierTableau[$cat]['infos'] = array ();
					$contenuFichierTableau[$cat]['pages'] = array ();
					
					if (!empty($_POST['langue'][$cle]))
					{
						$langueCat = securiseTexte($_POST['langue'][$cle]);
					}
					else
					{
						$langueCat = $langueParDefaut;
					}
					
					$contenuFichierTableau[$cat]['infos'][] = "langue=$langueCat\n";
					
					if (!empty($urlCle))
					{
						$urlCat = $urlCle;
					}
					else
					{
						$urlCat = 'categorie.php?id=' . filtreChaine($cat);
						
						if (estCatSpeciale($cat))
						{
							$urlCat .= "&amp;langue=$langueCat";
						}
					}
					
					$contenuFichierTableau[$cat]['infos'][] = "url=$urlCat\n";
					
					if (!empty($_POST['parent'][$cle]))
					{
						$parentCat = $_POST['parent'][$cle];
					}
					else
					{
						$parentCat = '';
					}
					
					$contenuFichierTableau[$cat]['infos'][] = "parent=$parentCat\n";
					
					if (!empty($_POST['rss'][$cle]) && $_POST['rss'][$cle] == 1)
					{
						$rssCat = 1;
					}
					else
					{
						$rssCat = 0;
					}
					
					$contenuFichierTableau[$cat]['infos'][] = "rss=$rssCat\n";
					
					if (!empty($urlPagesCle))
					{
						foreach ($urlPagesCle as $page)
						{
							if (!empty($page) && !preg_grep('/^pages\[\]=' . preg_quote($page, '/') . "\n/", $contenuFichierTableau[$cat]['pages']))
							{
								$contenuFichierTableau[$cat]['pages'][] = "pages[]=$page\n";
							}
						}
					}
				}
			}
		}
		
		$urlAjout = '';
		
		if (!empty($_POST['urlAjout']))
		{
			$urlAjout = supprimeUrlRacine($urlRacine, $_POST['urlAjout']);
		}
		
		$nombreNouvellesCategories = 0;
		
		if (!empty($_POST['catAjoutSelect']) && !empty($urlAjout))
		{
			$catAjout = array ();
			
			foreach ($_POST['catAjoutSelect'] as $catAjoutSelectEncodee)
			{
				$catAjoutSelect = decodeTexte($catAjoutSelectEncodee);
				
				if ($catAjoutSelect == 'nouvelleCategorie')
				{
					if (!empty($_POST['catAjoutInput']))
					{
						$nouvellesCategories = explode('#', $_POST['catAjoutInput']);
						$nombreNouvellesCategories = count($nouvellesCategories);
						$catAjout = array_merge($catAjout, $nouvellesCategories);
					}
				}
				else
				{
					$catAjout[] = $catAjoutSelect;
				}
			}
			
			$cheminFichier = cheminConfigCategories($racine);
			
			if ($adminInclurePageDansCategorieParente && $cheminFichier && ($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
			{
				if ($adminInclurePageDansCategoriesParentesIndirectes)
				{
					$parentsAjout = array ();
					
					foreach ($catAjout as $c)
					{
						if (!empty($categories[$c]['parent']))
						{
							if (!in_array($categories[$c]['parent'], $parentsAjout))
							{
								$parentsAjout[] = $categories[$c]['parent'];
							}
							
							$parentsAjout = array_merge($parentsAjout, categoriesParentesIndirectes($categories, $categories[$c]['parent']));
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
						if (!empty($categories[$c]['parent']) && !in_array($categories[$c]['parent'], $catAjout))
						{
							$catAjout[] = $categories[$c]['parent'];
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
					
					if (!empty($_POST['mettreEnLigneLangue']))
					{
						$langueCat = securiseTexte($_POST['mettreEnLigneLangue']);
					}
					else
					{
						$langueCat = $langueParDefaut;
					}
					
					$contenuFichierTableau[$c]['infos'][] = "langue=$langueCat\n";
					$urlNouvelleCat = '';
					
					if ($nombreNouvellesCategories == 1 && !empty($_POST['urlNouvelleCat']))
					{
						$urlNouvelleCat = supprimeUrlRacine($urlRacine, $_POST['urlNouvelleCat']);
						$pageCategorie = superBasename(decodeTexte($urlNouvelleCat));
						$dossierPageCategorie = '../' . dirname(decodeTexte($urlNouvelleCat));
						
						if ($dossierPageCategorie == '../.')
						{
							$dossierPageCategorie = '..';
						}
						
						$cheminInclude = preg_replace('|[^/]+/|', '../', $dossierPageCategorie);
						$cheminInclude = dirname($cheminInclude);
						
						if ($cheminInclude == '.')
						{
							$cheminInclude = '';
						}
						
						if (!empty($cheminInclude))
						{
							$cheminInclude .= '/';
						}
						
						if (!file_exists($dossierPageCategorie))
						{
							$messagesScript .= adminMkdir($dossierPageCategorie, octdec(755), TRUE);
						}
						
						if (file_exists($dossierPageCategorie . '/' . $pageCategorie))
						{
							$messagesScript .= '<li>' . sprintf(T_("La page web %1\$s existe déjà. Vous pouvez <a href=\"%2\$s\">éditer le fichier</a> ou <a href=\"%3\$s\">visiter la page</a>."), '<code>' . securiseTexte($dossierPageCategorie . '/' . $pageCategorie) . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($dossierPageCategorie . '/' . $pageCategorie) . '&amp;dossierCourant=' . encodeTexte($dossierParentPageCategorie) . '#messages', $urlRacine . '/' . $urlNouvelleCat) . "</li>\n";
						}
						elseif ($fic = @fopen($dossierPageCategorie . '/' . $pageCategorie, 'a'))
						{
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$contenu .= '$idCategorie = \'' . str_replace("'", "\'", $c) . "';\n";
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
							fclose($fic);
							$messagesScript .= '<li>' . sprintf(T_("Création du modèle de page %1\$s effectuée. Vous pouvez <a href=\"%2\$s\">éditer le fichier</a> ou <a href=\"%3\$s\">visiter la page</a>."), "<code>$dossierPageCategorie/$pageCategorie</code>", 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($dossierPageCategorie . '/' . $pageCategorie) . '&amp;dossierCourant=' . encodeTexte($dossierPageCategorie) . '#messages', $urlRacine . '/' . $urlNouvelleCat) . "</li>\n";
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . securiseTexte($dossierPageCategorie . '/' . $pageCategorie) . '</code>') . "</li>\n";
						}
					}
					
					if (!empty($urlNouvelleCat))
					{
						$urlCat = $urlNouvelleCat;
					}
					else
					{
						$urlCat = 'categorie.php?id=' . filtreChaine($c);
						
						if (estCatSpeciale($c))
						{
							$urlCat .= "&amp;langue=$langueCat";
						}
					}
					
					$contenuFichierTableau[$c]['infos'][] = "url=$urlCat\n";
					$contenuFichierTableau[$c]['infos'][] = "parent=\n";
					
					if (isset($_POST['mettreEnLigneRss']) && $_POST['mettreEnLigneRss'] == 1)
					{
						$rssCat = 1;
					}
					else
					{
						$rssCat = 0;
					}
					
					$contenuFichierTableau[$c]['infos'][] = "rss=$rssCat\n";
				}
				
				if (!preg_grep('/^pages\[\]=' . preg_quote($urlAjout, '/') . "\n/", $contenuFichierTableau[$c]['pages']))
				{
					array_unshift($contenuFichierTableau[$c]['pages'], "pages[]=$urlAjout\n");
				}
			}
		}
		
		$messagesScript .= adminMajConfigCategories($racine, $contenuFichierTableau);
		echo adminMessagesScript($messagesScript, T_("Enregistrement des modifications des catégories"));
		
		if (isset($_POST['rssAjout']) && !empty($urlAjout) && !empty($_POST['rssLangueAjout']))
		{
			$messagesScript = '';
			$rssLangueAjout = securiseTexte($_POST['rssLangueAjout']);
			$contenuFichierRssTableau = array ();
			$cheminFichierRss = cheminConfigFluxRssGlobalSite($racine, TRUE);
			
			if (!file_exists($cheminFichierRss) && !@touch($cheminFichierRss))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des flux RSS est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichierRss) . '</code>') . "</li>\n";
			}
			
			if (file_exists($cheminFichierRss))
			{
				$rssPages = super_parse_ini_file($cheminFichierRss, TRUE);
				
				if ($rssPages === FALSE)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichierRss) . '</code>') . "</li>\n";
				}
				else
				{
					if (!empty($rssPages))
					{
						foreach ($rssPages as $codeLangue => $langueInfos)
						{
							$contenuFichierRssTableau[$codeLangue] = array ();
							
							if (!empty($langueInfos['pages']))
							{
								foreach ($langueInfos['pages'] as $page)
								{
									$contenuFichierRssTableau[$codeLangue][] = "pages[]=$page\n";
								}
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
					
					$messagesScript .= adminEnregistreConfigFluxRssGlobalSite($racine, $contenuFichierRss);
				}
			}
			
			echo adminMessagesScript($messagesScript, T_("Enregistrement des modifications du flux RSS des dernières publications"));
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
