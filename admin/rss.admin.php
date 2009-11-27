<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Flux RSS globaux");
include 'inc/premier.inc.php';

include '../init.inc.php';
?>

<h1><?php echo T_("Gestion des flux RSS globaux"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_POST['lister']))
	{
		if (isset($_POST['global']) && $_POST['global'] == 'galeries')
		{
			###############################################################
			#
			# Pages des galeries
			#
			###############################################################
			
			$messagesScript = array ();
			$cheminFichier = adminCheminConfigFluxRssGlobalGaleries($racine);
			
			if (!$cheminFichier)
			{
				$cheminFichier = adminCheminConfigFluxRssGlobalGaleries($racine, TRUE);
				
				if ($adminPorteDocumentsDroits['creer'])
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/rss-global-galeries.ini.txt#messagesPorteDocuments') . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				if (($galeries = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
				{
					echo "<form action='$adminAction#messages' method='post'>\n";
					echo "<div>\n";
					
					$listeGaleries = '';
					if (!empty($galeries))
					{
						$i = 0;
						foreach ($galeries as $codeLangue => $langueInfos)
						{
							$listeGaleries .= '<li class="langue"><input type="text" name="langue[' . $i . ']" value="' . $codeLangue . '" />';
							$listeGaleries .= "<ul class=\"triable\">\n";
							foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
							{
								$listeGaleries .= '<li><input type="text" name="id[' . $i . '][]" value="' . $idGalerie . '" />=<input type="text" name="url[' . $i . '][]" value="' . $urlRelativeGalerie . '" /></li>' . "\n";
							}
							$listeGaleries .= "</ul></li>\n";
							
							$i++;
						}
					}
					
					echo '<div class="sousBoite">' . "\n";
					echo '<h3>' . T_("Liste des pages des galeries") . "</h3>\n";
					
					echo '<p>' . sprintf(T_("Les pages sont classées par section représentant la langue. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>' . T_("identifiant de la galerie") . '=' . T_("URL relative de la galerie") . '</code>') . "</p>\n";
					
					echo "<ul>\n";
					echo "<li>fr\n";
					echo "<ul>\n";
					echo "<li>chiens=animaux/chiens.php</li>\n";
					echo "</ul></li>\n";
					echo "</ul>\n";
					
					echo '<p>' . sprintf(T_("Cet exemple fait référence à une galerie en français dont l'identifiant est %1\$s et dont l'URL est %2\$s."), "<code>chiens</code>", "<code>$urlRacine/animaux/chiens.php</code>") . "</p>\n";
					
					echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
					
					echo "<fieldset>\n";
					echo '<legend>' . T_("Options") . "</legend>\n";
					
					echo "<ul>\n";
					if (!empty($listeGaleries))
					{
						echo $listeGaleries;
					}
					else
					{
						echo '<li>' . T_("Le fichier est vide. Aucune galerie n'y est listée.") . "</li>\n";
					}
					echo "</ul>\n";
					
					echo '<p><strong>' . T_("Ajouter une galerie:") . "</strong></p>\n";
					
					echo "<ul>\n";
					echo '<li><input type="text" name="langueAjout" value="" />';
					echo "<ul>\n";
					echo '<li><input type="text" name="idAjout" value="" />=<input type="text" name="urlAjout" value="" /></li>' . "\n";
					echo "</ul></li>\n";
					echo "</ul>\n";
					echo "</fieldset>\n";
					
					echo "<p><input type='submit' name='modifsGaleries' value='" . T_("Enregistrer les modifications") . "' /></p>\n";
					echo "</div>\n";
					echo "</form>\n";
					echo "</div><!-- /class=sousBoite -->\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
				}
			}
			
			echo adminMessagesScript($messagesScript);
		}
		elseif (isset($_POST['global']) && $_POST['global'] == 'site')
		{
			###############################################################
			#
			# Autres pages
			#
			###############################################################
			
			$messagesScript = array ();
			$cheminFichier = adminCheminConfigFluxRssGlobalSite($racine);
			
			if (!$cheminFichier)
			{
				$cheminFichier = adminCheminConfigFluxRssGlobalSite($racine, TRUE);
				
				if ($adminPorteDocumentsDroits['creer'])
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/rss-global-site.ini.txt#messagesPorteDocuments') . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				if (($pages = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
				{
					echo "<form action='$adminAction#messages' method='post'>\n";
					echo "<div>\n";
				
					if (!empty($pages))
					{
						$listePages = '';
						$i = 0;
						
						foreach ($pages as $codeLangue => $langueInfos)
						{
							$listePages .= '<li class="langue"><input type="text" name="langue[' . $i . ']" value="' . $codeLangue . '" />';
							$listePages .= "<ul class=\"triable\">\n";
							foreach ($langueInfos['pages'] as $page)
							{
								$page = rtrim($page);
								$listePages .= '<li>pages[]=<input type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
							}
							$listePages .= "</ul></li>\n";
							
							$i++;
						}
					}
				
					echo '<div class="sousBoite">' . "\n";
					echo '<h3>' . T_("Liste des pages autres que les galeries") . "</h3>\n";
					
					echo '<p>' . sprintf(T_("Les pages sont classées par section représentant la langue. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>') . "</p>\n";
					
					echo "<ul>\n";
					echo "<li>fr\n";
					echo "<ul>\n";
					echo "<li>pages[]=animaux/chiens.php</li>\n";
					echo "</ul></li>\n";
					echo "</ul>\n";
					
					echo '<p>' . sprintf(T_("Cet exemple fait référence à une page en français dont l'URL est %1\$s."), "<code>$urlRacine/animaux/chiens.php</code>") . "</p>\n";
					
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
					
					echo "<ul>\n";
					echo '<li><input type="text" name="langueAjout" value="" />';
					echo "<ul>\n";
					echo '<li>pages[]=<input type="text" name="urlAjout" value="" /></li>' . "\n";
					echo "</ul></li>\n";
					echo "</ul>\n";
					echo "</fieldset>\n";
					
					echo "<p><input type='submit' name='modifsSite' value='" . T_("Enregistrer les modifications") . "' /></p>\n";
					
					echo "</div>\n";
					echo "</form>\n";
					echo "</div><!-- /class=sousBoite -->\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</li>\n";
				}
			}
			
			echo adminMessagesScript($messagesScript);
		}
	}
	
	if (isset($_POST['modifsGaleries']))
	{
		$messagesScript = array ();
		
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications pour les galeries") . "</h3>\n" ;
		
		$contenuFichierTableau = array ();
		if (isset($_POST['langue']))
		{
			foreach ($_POST['langue'] as $cle => $postLangueValeur)
			{
				$postLangueValeur = securiseTexte($postLangueValeur);
				
				if (!empty($postLangueValeur) && !empty($_POST['id'][$cle]) && !empty($_POST['url'][$cle]))
				{
					$contenuFichierTableau[$postLangueValeur] = array ();
					
					foreach ($_POST['id'][$cle] as $cle2 => $idGalerie)
					{
						if (!empty($idGalerie) && !empty($_POST['url'][$cle][$cle2]))
						{
							$contenuFichierTableau[$postLangueValeur][] = securiseTexte($idGalerie) . '=' . securiseTexte($_POST['url'][$cle][$cle2]) . "\n";
						}
					}
				}
			}
		}
		
		if (!empty($_POST['langueAjout']) && !empty($_POST['idAjout']) && !empty($_POST['urlAjout']))
		{
			$langueAjout = securiseTexte($_POST['langueAjout']);
			
			if (!isset($contenuFichierTableau[$langueAjout]))
			{
				$contenuFichierTableau[$langueAjout] = array ();
			}
			
			array_unshift($contenuFichierTableau[$langueAjout], securiseTexte($_POST['idAjout']) . '=' . securiseTexte($_POST['urlAjout']) . "\n");
		}
		
		$contenuFichier = '';
		foreach ($contenuFichierTableau as $codeLangue => $langueInfos)
		{
			if (!empty($langueInfos))
			{
				$contenuFichier .= "[$codeLangue]\n";
				foreach ($langueInfos as $ligne)
				{
					$contenuFichier .= $ligne;
				}
				$contenuFichier .= "\n";
			}
		}
		
		$cheminFichier = adminCheminConfigFluxRssGlobalGaleries($racine);
		
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
				$messagesScript[] = '<li>';
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				$messagesScript[] = '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				$messagesScript[] = '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
				$messagesScript[] = "<ul>\n";
				$messagesScript[] = "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript[] = "</ul>\n";
				$messagesScript[] = "</li>\n";
			}
		}
		else
		{
			$cheminFichier = adminCheminConfigFluxRssGlobalGaleries($racine, TRUE);
			$messagesScript[] = '<li>';
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/rss-global-galeries.ini.txt#messagesPorteDocuments') . "</p>\n";
			}
			else
			{
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</p>\n";
			}
			$messagesScript[] = '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
			$messagesScript[] = '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
			$messagesScript[] = "<ul>\n";
			$messagesScript[] = "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			$messagesScript[] = "</ul>\n";
			$messagesScript[] = "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /class=sousBoite -->\n";
	}
	elseif (isset($_POST['modifsSite']))
	{
		$messagesScript = array ();
		
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications pour les pages autres que les galeries") . "</h3>\n" ;
	
		$contenuFichierTableau = array ();
		if (isset($_POST['langue']))
		{
			foreach ($_POST['langue'] as $cle => $postLangueValeur)
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
		
		if (!empty($_POST['langueAjout']) && !empty($_POST['urlAjout']))
		{
			$langueAjout = securiseTexte($_POST['langueAjout']);
			
			if (!isset($contenuFichierTableau[$langueAjout]))
			{
				$contenuFichierTableau[$langueAjout] = array ();
			}
			
			array_unshift($contenuFichierTableau[$langueAjout], 'pages[]=' . securiseTexte($_POST['urlAjout']) . "\n");
		}
		
		$contenuFichier = '';
		foreach ($contenuFichierTableau as $codeLangue => $langueInfos)
		{
			if (!empty($langueInfos))
			{
				$contenuFichier .= "[$codeLangue]\n";
				foreach ($langueInfos as $ligne)
				{
					$contenuFichier .= $ligne;
				}
				$contenuFichier .= "\n";
			}
		}
		
		$cheminFichier = adminCheminConfigFluxRssGlobalSite($racine);
		
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
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . $cheminFichier . '</code>') . "</p>\n";
				$messagesScript[] = '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				$messagesScript[] = '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
				$messagesScript[] = "<ul>\n";
				$messagesScript[] = "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript[] = "</ul>\n";
				$messagesScript[] = "</li>\n";
			}
		}
		else
		{
			$cheminFichier = adminCheminConfigFluxRssGlobalSite($racine, TRUE);
			$messagesScript[] = '<li>';
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), "<code>$cheminFichier</code>", 'porte-documents.admin.php?action=editer&amp;valeur=../site/inc/rss-global-site.ini.txt#messagesPorteDocuments') . "</p>\n";
			}
			else
			{
				$messagesScript[] = '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</p>\n";
			}
			$messagesScript[] = '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
			$messagesScript[] = '<pre id="contenuFichier">' . $contenuFichier . "</pre>\n";
			$messagesScript[] = "<ul>\n";
			$messagesScript[] = "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			$messagesScript[] = "</ul>\n";
			$messagesScript[] = "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /class=sousBoite -->\n";
	}
	?>
</div><!-- /boiteMessages -->

<div class="boite">
	<h2><?php echo T_("Configuration actuelle"); ?></h2>
	
	<ul>
		<?php if ($galerieFluxRssGlobal): ?>
			<li><?php echo T_("Le flux RSS global des galeries est activé") . ' (<code>$galerieFluxRssGlobal = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("Le flux RSS global des galeries n'est pas activé") . ' (<code>$galerieFluxRssGlobal = FALSE;</code>).'; ?></li>
		<?php endif; ?>
		
		<?php if ($siteFluxRssGlobal): ?>
			<li><?php echo T_("Le flux RSS global du site est activé") . ' (<code>$siteFluxRssGlobal = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("Le flux RSS global du site n'est pas activé") . ' (<code>$siteFluxRssGlobal = FALSE;</code>).'; ?></li>
		<?php endif; ?>
	</ul>
	
	<?php if ($adminPorteDocumentsDroits['editer']): ?>
		<p><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php#messagesPorteDocuments"><?php echo T_("Modifier cette configuration."); ?></a></p>
	<?php endif; ?>
</div><!-- /class=boite -->

<div class="boite">
	<h2><?php echo T_("Pages ajoutées aux flux RSS globaux"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<ul>
					<li><input type="radio" name="global" value="galeries" /> <?php echo T_("Pages des galeries"); ?></li>
				
					<li><input type="radio" name="global" value="site" checked="checked" /> <?php echo T_("Pages autres que les galeries"); ?></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="lister" value="<?php echo T_('Lister les pages'); ?>" /></p>
		</div>
	</form>
</div><!-- /class=boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
