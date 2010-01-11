<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Catégories");
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/categories.ini.txt#messagesPorteDocuments') . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		else
		{
			if (($categories = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
			{
				echo "<form action='$adminAction#messages' method='post'>\n";
				echo "<div>\n";
			
				if (!empty($categories))
				{
					$listePages = '';
					$i = 0;
					
					foreach ($categories as $categorie => $categorieInfos)
					{
						$listePages .= '<li class="liParent"><input type="text" name="cat[' . $i . ']" value="' . $categorie . '" />';
						$listePages .= "<ul class=\"triable\">\n";
						
						foreach ($categorieInfos['pages'] as $page)
						{
							$page = rtrim($page);
							$listePages .= '<li>pages[]=<input type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
						}
						
						$listePages .= "</ul></li>\n";
						$i++;
					}
				}
			
				echo '<div class="sousBoite">' . "\n";
				echo '<h3>' . T_("Liste des pages classées par catégorie") . "</h3>\n";
				
				echo '<p>' . sprintf(T_("Les pages sont classées par section représentant une catégorie. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>') . "</p>\n";
				
				echo "<ul>\n";
				echo "<li>animaux\n";
				echo "<ul>\n";
				echo "<li>pages[]=animaux/chiens.php</li>\n";
				echo "</ul></li>\n";
				echo "</ul>\n";
				
				echo '<p>' . sprintf(T_("Cet exemple fait référence à une page dont l'URL est %1\$s et qui est classée dans la catégorie %2\$s."), "<code>$urlRacine/animaux/chiens.php</code>", "<em>animaux</em>") . "</p>\n";
				
				echo '<p>' . T_("Pour enlever une catégorie ou une page, simplement supprimer le contenu du champ.") . "</p>\n";
				
				echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
				
				echo "<fieldset>\n";
				echo '<legend>' . T_("Options") . "</legend>\n";
				
				echo "<ul>\n";
				
				if (!empty($listePages))
				{
					echo $listePages;
				}
				else
				{
					echo '<li>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</li>\n";
				}
				
				echo "</ul>\n";
				
				echo '<p><strong>' . T_("Ajouter une page:") . "</strong></p>\n";
				
				echo '<p>' . T_("Pour ajouter une page à plus d'une catégorie, séparer les catégories par un carré (<em>#</em>). Exemple:") . "</p>\n";
				
				echo "<ul>\n";
				echo "<li>animaux#chiens\n";
				echo "<ul>\n";
				echo "<li>pages[]=animaux/chiens.php</li>\n";
				echo "</ul></li>\n";
				echo "</ul>\n";
				
				echo "<ul>\n";
				echo '<li><input type="text" name="catAjout" value="" />';
				echo "<ul>\n";
				echo '<li>pages[]=<input type="text" name="urlAjout" value="" /></li>' . "\n";
				echo "</ul></li>\n";
				echo "</ul>\n";
				echo "</fieldset>\n";
				
				echo "<p><input type='submit' name='modifsCategories' value='" . T_("Enregistrer les modifications") . "' /></p>\n";
				
				echo "</div>\n";
				echo "</form>\n";
				echo "</div><!-- /.sousBoite -->\n";
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
			}
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
			foreach ($_POST['cat'] as $cle => $postLangueValeur)
			{
				if (!empty($postLangueValeur) && !empty($_POST['url'][$cle]))
				{
					$contenuFichierTableau[$postLangueValeur] = array ();
					
					foreach ($_POST['url'][$cle] as $page)
					{
						if (!empty($page))
						{
							$contenuFichierTableau[$postLangueValeur][] = 'pages[]=' . securiseTexte($page) . "\n";
						}
					}
				}
			}
		}
		
		if (!empty($_POST['catAjout']) && !empty($_POST['urlAjout']))
		{
			$catsAjout = explode('#', securiseTexte($_POST['catAjout']));
			
			foreach ($catsAjout as $catAjout)
			{
				if (!isset($contenuFichierTableau[$catAjout]))
				{
					$contenuFichierTableau[$catAjout] = array ();
				}
				
				array_unshift($contenuFichierTableau[$catAjout], 'pages[]=' . securiseTexte($_POST['urlAjout']) . "\n");
			}
		}
		
		$contenuFichier = '';
		
		foreach ($contenuFichierTableau as $categorie => $categorieInfos)
		{
			if (!empty($categorieInfos))
			{
				$contenuFichier .= "[$categorie]\n";
				
				foreach ($categorieInfos as $ligne)
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
				$messagesScript .= '<p class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/categories.ini.txt#messagesPorteDocuments') . "</p>\n";
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
