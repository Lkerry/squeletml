<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Catégories");
$boitesDeroulantes = '.bD';
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/categories.ini.txt#messages') . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		elseif (($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
		{
			echo "<form action='$adminAction#messages' method='post'>\n";
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
					
					$listePages .= '<li>urlCategorie=<input class="url" type="text" name="urlCat[' . $i . ']" value="' . $categorieInfos['urlCategorie'] . '" /></li>' . "\n";
					
					if (!isset($categorieInfos['categorieParente']))
					{
						$categorieInfos['categorieParente'] = '';
					}
					
					$listePages .= '<li>categorieParente=';
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
						$listePages .= '<select name="catParente[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input type="text" name="catParente[' . $i . ']" value="' . $categorieInfos['categorieParente'] . '" />';
					}
					
					$listePages .= "</li>\n";
					
					if (!empty($categorieInfos['pages']))
					{
						foreach ($categorieInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$listePages .= '<li>pages[]=<input class="url" type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
						}
					}
					
					$listePages .= "</ul></li>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages classées par catégorie") . "</h3>\n";
			
			echo '<div class="bD">' . "\n";
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
			
			echo '<p>' . T_("Pour enlever une catégorie ou une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.bD -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div class="bD">' . "\n";
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
			echo "</div><!-- /.bD -->\n";
			
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
			echo '<li>pages[]=<input type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options d'ajout") . "</legend>\n";
			
			echo "<ul>\n";
			echo '<li><input type="checkbox" name="parentAjout" value="ajout" checked="checked" /> <label>' . T_("S'il y a lieu, inclure la page dans la catégorie parente.") . '</label>' . "\n";
			echo "<ul>\n";
			echo '<li><input type="checkbox" name="parentsAjout" value="ajout" checked="checked" /> <label>' . T_("S'il y a lieu, inclure la page également dans les catégories parentes indirectes.") . '</label></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . T_("Explications: par exemple, une page est ajoutée à la catégorie «Miniatures». Cette catégorie a comme parent «Chiens», qui a elle-même comme parent la catégorie «Animaux». Si l'option d'ajout dans la catégorie parente est sélectionnée, la page sera ajoutée dans la catégorie «Miniatures» et dans la catégorie parente «Chiens». Aussi, si l'option d'ajout dans les catégories parentes indirectes est sélectionnée, la page sera également ajoutée à la catégorie «Animaux».") . "</p>\n";
			echo "</fieldset>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsCategories" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
			echo "</div>\n";
			echo "</form>\n";
			echo "</div><!-- /.sousBoite -->\n";
		}
		else
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
				if (!empty($cat) && (!empty($_POST['catParente'][$cle]) || !empty($_POST['urlCat'][$cle]) || !empty($_POST['url'][$cle])))
				{
					$contenuFichierTableau[$cat] = array ();
					$contenuFichierTableau[$cat]['infos'] = array ();
					$contenuFichierTableau[$cat]['pages'] = array ();
					
					if (!empty($_POST['urlCat'][$cle]))
					{
						$contenuFichierTableau[$cat]['infos'][] = 'urlCategorie=' . securiseTexte($_POST['urlCat'][$cle]) . "\n";
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
		
		if ($cheminFichier)
		{
			if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
			{
				echo '<p>' . sprintf(T_("Les modifications ont été enregistrées. Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				
				echo '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
				
				echo "<ul>\n";
				echo "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				echo "</ul>\n";
			}
			else
			{
				$messagesScript = '<li>';
				$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				$messagesScript .= '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				$messagesScript .= '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</li>\n";
			}
		}
		else
		{
			$cheminFichier = cheminConfigCategories($racine, TRUE);
			$messagesScript .= '<li>';
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/categories.ini.txt#messages') . "</p>\n";
			}
			else
			{
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</p>\n";
			}
			
			$messagesScript .= '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
			
			$messagesScript .= '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
			
			$messagesScript .= "<ul>\n";
			$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			$messagesScript .= "</ul>\n";
			$messagesScript .= "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
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
