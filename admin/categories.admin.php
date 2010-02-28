<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Catégories");
$boitesDeroulantes = '.aideAdminCat #configActuelleAdminCat #optionsAjoutAdminCat';
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
					$listePages .= "<ul class=\"triable\">\n";
					
					if (!isset($categorieInfos['urlCategorie']))
					{
						$categorieInfos['urlCategorie'] = '';
					}
					
					$listePages .= '<li><label for="inputUrlCat-' . $i . '">urlCategorie=</label><input id="inputUrlCat-' . $i . '" class="long" type="text" name="urlCat[' . $i . ']" value="' . $categorieInfos['urlCategorie'] . '" /></li>' . "\n";
					
					if (!isset($categorieInfos['categorieParente']))
					{
						$categorieInfos['categorieParente'] = '';
					}
					
					$listePages .= '<li><label for="catParente-' . $i . '">categorieParente=</label>';
					$listeOption = '';
					
					foreach ($categories as $cat => $catInfos)
					{
						if ($cat != $categorie)
						{
							$listeOption .= '<option value="' . $cat . '"';
							
							if ($cat == $categorieInfos['categorieParente'])
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
						$listePages .= '<input id="catParente-' . $i . '" type="text" name="catParente[' . $i . ']" value="' . $categorieInfos['categorieParente'] . '" />';
					}
					
					$listePages .= "</li>\n";
					
					if (!empty($categorieInfos['pages']))
					{
						$j = 0;
						
						foreach ($categorieInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$listePages .= '<li><label for="inputUrl-' . $i . '-' . $j . '">pages[]=</label><input id="inputUrl-' . $i . '-' . $j . '" class="long" type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
							$j++;
						}
					}
					
					$listePages .= "</ul></li>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages classées par catégorie") . "</h3>\n";
			
			echo '<div class="aideAdminCat">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps afficher\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant une catégorie. À l'intérieur d'une section, chaque page est déclarée sous la forme %1\$s. Optionnellement, vous pouvez préciser l'URL relative de la page d'accueil de chaque catégorie à l'aide du paramètre %2\$s ainsi que la catégorie parente, s'il y a lieu, grâce à %3\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>', '<code>urlCategorie=' . T_("URL relative de la page d'accueil de la catégorie") . '</code>', '<code>categorieParente=' . T_("identifiant de la catégorie parente") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>Chiens\n";
			echo "<ul>\n";
			echo "<li>urlCategorie=animaux/chiens/</li>\n";
			echo "<li>categorieParente=Animaux</li>\n";
			echo "<li>pages[]=animaux/chiens/husky.php</li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à la catégorie %1\$s, accessible à l'adresse %2\$s, enfant de la catégorie %3\$s et contenant une page dont l'URL est %4\$s."), "<em>Chiens</em>", "<code>$urlRacine/animaux/chiens/</code>", "<em>Animaux</em>", "<code>$urlRacine/animaux/chiens/husky.php</code>") . "</p>\n";
			
			echo '<p>' . sprintf(T_("Si la page d'accueil d'une catégorie n'est pas précisée à l'aide du paramètre %1\$s, l'URL sera générée automatiquement, et ce sous la forme %2\$s."), 'urlCategorie', '$urlRacine/categorie.php?id=$idCategorie') . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une catégorie ou une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.aideAdminCat -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div id="configActuelleAdminCat">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Configuration actuelle") . "</h4>\n";
			
			echo "<ul class=\"bDcorps afficher\">\n";
			
			if (!empty($listePages))
			{
				echo $listePages;
			}
			else
			{
				echo '<li>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</li>\n";
			}
			
			echo "</ul>\n";
			echo "</div><!-- /#configActuelleAdminCat -->\n";
			
			echo '<p><strong>' . T_("Ajouter une page:") . "</strong></p>\n";
			
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
			echo '<li><label for="inputUrlAjout">pages[]=</label><input id="inputUrlAjout" type="text" name="urlAjout" value="" /></li>' . "\n";
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
			
			$rssCheminFichier = cheminConfigFluxRssGlobal($racine, 'site');
			
			if ($rssCheminFichier)
			{
				$rssPages = super_parse_ini_file($rssCheminFichier, TRUE);
				
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
			
			echo '<p><input type="submit" name="modifsCategories" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
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
				
				if (!empty($cat) && (!empty($_POST['catParente'][$cle]) || !empty($_POST['urlCat'][$cle]) || !empty($_POST['url'][$cle])))
				{
					$contenuFichierTableau[$cat] = array ();
					$contenuFichierTableau[$cat]['infos'] = array ();
					$contenuFichierTableau[$cat]['pages'] = array ();
					
					if (!empty($_POST['urlCat'][$cle]))
					{
						$contenuFichierTableau[$cat]['infos'][] = 'urlCategorie=' . securiseTexte($_POST['urlCat'][$cle]) . "\n";
					}
					else
					{
						$contenuFichierTableau[$cat]['infos'][] = "urlCategorie=categorie.php?id=$cat\n";
					}
					
					if (!empty($_POST['catParente'][$cle]))
					{
						$contenuFichierTableau[$cat]['infos'][] = 'categorieParente=' . securiseTexte($_POST['catParente'][$cle]) . "\n";
					}
					
					if (!empty($_POST['url'][$cle]))
					{
						foreach ($_POST['url'][$cle] as $page)
						{
							if (!empty($page))
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
						if (!empty($categories[$c]['categorieParente']))
						{
							if (!in_array($categories[$c]['categorieParente'], $parentsAjout))
							{
								$parentsAjout[] = $categories[$c]['categorieParente'];
							}
							
							$parentsAjout = array_merge($parentsAjout, categoriesParentesIndirectes($categories, $categories[$c]['categorieParente']));
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
						if (!empty($categories[$c]['categorieParente']) && !in_array($categories[$c]['categorieParente'], $catAjout))
						{
							$catAjout[] = $categories[$c]['categorieParente'];
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
				
				array_unshift($contenuFichierTableau[$c]['pages'], 'pages[]=' . securiseTexte($_POST['urlAjout']) . "\n");
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
		
		if (file_exists($cheminFichier))
		{
			if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
			{
				$messagesScript .= '<li>';
				$messagesScript .= '<p>' . sprintf(T_("Les modifications ont été enregistrées. Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				
				$messagesScript .= '<pre id="contenuFichierCategories">' . $contenuFichier . "</pre>\n";
				
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierCategories');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</li>\n";
			}
			else
			{
				$messagesScript .= '<li>';
				$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				$messagesScript .= '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				$messagesScript .= '<pre id="contenuFichierCategories">' . $contenuFichier . "</pre>\n";
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierCategories');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>';
			$messagesScript .= '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
			
			$messagesScript .= '<pre id="contenuFichierCategories">' . $contenuFichier . "</pre>\n";
			
			$messagesScript .= "<ul>\n";
			$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierCategories');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			$messagesScript .= "</ul>\n";
			$messagesScript .= "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
		
		if (isset($_POST['rssAjout']) && !empty($_POST['urlAjout']) && !empty($_POST['rssLangueAjout']))
		{
			$messagesScript = '';
			$urlAjout = securiseTexte($_POST['urlAjout']);
			$rssLangueAjout = securiseTexte($_POST['rssLangueAjout']);
			$contenuFichierRssTableau = array ();
			$rssCheminFichier = cheminConfigFluxRssGlobal($racine, 'site');
			
			if (!$rssCheminFichier)
			{
				$rssCheminFichier = cheminConfigFluxRssGlobal($racine, 'site', TRUE);
				
				if ($adminPorteDocumentsDroits['creer'])
				{
					@touch($rssCheminFichier);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas."), "<code>$rssCheminFichier</code>") . "</li>\n";
				}
			}
			
			if (file_exists($rssCheminFichier) && ($rssPages = super_parse_ini_file($rssCheminFichier, TRUE)) === FALSE)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $rssCheminFichier . '</code>') . "</li>\n";
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

<div class="boite">
	<h2 id="actions"><?php echo T_("Actions"); ?></h2>
	
	<ul>
		<li><a href="<?php echo $adminAction; ?>?action=lister#messages"><?php echo T_('Lister les pages classées par catégorie.'); ?></a></li>
	</ul>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
