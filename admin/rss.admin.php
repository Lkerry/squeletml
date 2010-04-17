<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Flux RSS globaux");
$boitesDeroulantes = '.aideAdminRss .configActuelleAdminRss .contenuFichierPourSauvegarde';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des flux RSS globaux"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	if (isset($_GET['global']) && ($_GET['global'] == 'galeries' || $_GET['global'] == 'site'))
	{
		$_POST['global'] = $_GET['global'];
	}
	
	##################################################################
	#
	# Flux RSS des derniers ajouts aux galeries.
	#
	##################################################################
	
	if (isset($_POST['global']) && $_POST['global'] == 'galeries')
	{
		$messagesScript = '';
		$cheminFichier = cheminConfigFluxRssGlobal($racine, 'galeries');
		
		if (!$cheminFichier)
		{
			$cheminFichier = cheminConfigFluxRssGlobal($racine, 'galeries', TRUE);
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				if (!@touch($cheminFichier))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS des derniers ajouts aux galeries puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS des derniers ajouts aux galeries puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($cheminFichier) && ($galeries = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
		{
			echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
			echo "<div>\n";
			$listeGaleries = '';
			
			if (!empty($galeries))
			{
				$i = 0;
				
				foreach ($galeries as $codeLangue => $langueInfos)
				{
					$listeGaleries .= '<li class="liParent">';
					$listeOption = '';
					
					foreach ($accueil as $langueAccueil => $urlLangueAccueil)
					{
						$listeOption .= '<option value="' . $langueAccueil . '"';
						
						if ($langueAccueil == $codeLangue)
						{
							$listeOption .= ' selected="selected"';
						}
						
						$listeOption .= '>' . $langueAccueil . "</option>\n";
					}
					
					if (!empty($listeOption))
					{
						$listeGaleries .= '<select name="langue[' . $i . ']">' . "\n";
						$listeGaleries .= '<option value=""></option>' . "\n";
						$listeGaleries .= $listeOption;
						$listeGaleries .= "</select>\n";
					}
					else
					{
						$listeGaleries .= '<input type="text" name="langue[' . $i . ']" value="' . $codeLangue . '" />';
					}
					
					$listeGaleries .= "<ul class=\"triable\">\n";
					
					foreach ($langueInfos as $idGalerie => $urlRelativeGalerie)
					{
						$listeGaleries .= '<li><input type="text" name="id[' . $i . '][]" value="' . $idGalerie . '" /><code>=</code><input class="long" type="text" name="url[' . $i . '][]" value="' . $urlRelativeGalerie . '" /></li>' . "\n";
					}
					
					$listeGaleries .= "</ul></li>\n";
					$i++;
				}
			}
			
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages du flux RSS des derniers ajouts aux galeries") . "</h3>\n";
			
			echo '<div class="aideAdminRss">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps afficher\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant la langue. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>' . T_("identifiant de la galerie") . '=' . T_("URL relative de la galerie") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>fr\n";
			echo "<ul>\n";
			echo "<li><code>chiens=animaux/chiens.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à une galerie en français dont l'identifiant est %1\$s et dont l'URL est %2\$s."), "<code>chiens</code>", "<code>$urlRacine/animaux/chiens.php</code>") . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une langue ou une galerie, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.aideAdminRss -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div class="configActuelleAdminRss">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Configuration actuelle") . "</h4>\n";
			
			if (empty($listeGaleries))
			{
				$listeGaleries = '<li>' . T_("Le fichier est vide. Aucune galerie n'y est listée.") . "</li>\n";
				echo "<ul class=\"bDcorps afficher\">\n";
			}
			else
			{
				echo "<ul class=\"triable bDcorps afficher\">\n";
			}
			
			echo $listeGaleries;
			echo "</ul>\n";
			echo "</div><!-- /.configActuelleAdminRss -->\n";
			
			echo '<h4>' . T_("Ajouter une galerie") . "</h4>\n";
			
			echo "<ul>\n";
			echo '<li><select name="langueAjout">' . "\n";
			
			foreach ($accueil as $langueAccueil => $urlLangueAccueil)
			{
				echo '<option value="' . $langueAccueil . '"';
				
				if ($langueAccueil == $langueParDefaut)
				{
					echo ' selected="selected"';
				}
				
				echo '>' . $langueAccueil . "</option>\n";
			}
			
			echo "</select>\n";
			
			echo "<ul>\n";
			echo '<li><input type="text" name="idAjout" value="" /><code>=</code><input class="long" type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsGaleries" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
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
	##################################################################
	#
	# Flux RSS des dernières publications.
	#
	##################################################################
	elseif (isset($_POST['global']) && $_POST['global'] == 'site')
	{
		$messagesScript = '';
		$cheminFichier = cheminConfigFluxRssGlobal($racine, 'site');
		
		if (!$cheminFichier)
		{
			$cheminFichier = cheminConfigFluxRssGlobal($racine, 'site', TRUE);
			
			if ($adminPorteDocumentsDroits['creer'])
			{
				if (!@touch($cheminFichier))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), "<code>$cheminFichier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas."), "<code>$cheminFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($cheminFichier) && ($pages = super_parse_ini_file($cheminFichier, TRUE)) !== FALSE)
		{
			echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
			echo "<div>\n";
		
			if (!empty($pages))
			{
				$listePages = '';
				$i = 0;
				
				foreach ($pages as $codeLangue => $langueInfos)
				{
					$listePages .= '<li class="liParent">';
					$listeOption = '';
					
					foreach ($accueil as $langueAccueil => $urlLangueAccueil)
					{
						$listeOption .= '<option value="' . $langueAccueil . '"';
						
						if ($langueAccueil == $codeLangue)
						{
							$listeOption .= ' selected="selected"';
						}
						
						$listeOption .= '>' . $langueAccueil . "</option>\n";
					}
					
					if (!empty($listeOption))
					{
						$listePages .= '<select name="langue[' . $i . ']">' . "\n";
						$listePages .= '<option value=""></option>' . "\n";
						$listePages .= $listeOption;
						$listePages .= "</select>\n";
					}
					else
					{
						$listePages .= '<input type="text" name="langue[' . $i . ']" value="' . $codeLangue . '" />';
					}
					
					$listePages .= "<ul class=\"triable\">\n";
					$j = 0;
					
					foreach ($langueInfos['pages'] as $page)
					{
						$page = rtrim($page);
						$listePages .= '<li><label for="inputUrl-' . $i . '-' . $j . '"><code>pages[]=</code></label><input id="inputUrl-' . $i . '-' . $j . '" class="long" type="text" name="url[' . $i . '][]" value="' . $page . '" /></li>' . "\n";
						$j++;
					}
					
					$listePages .= "</ul></li>\n";
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages du flux RSS des dernières publications") . "</h3>\n";
			
			echo '<div class="aideAdminRss">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps afficher\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant la langue. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li>fr\n";
			echo "<ul>\n";
			echo "<li><code>pages[]=animaux/chiens.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à une page en français dont l'URL est %1\$s."), "<code>$urlRacine/animaux/chiens.php</code>") . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une langue ou une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Aussi, chaque ligne est triable. Pour ce faire, cliquer sur la flèche correspondant à la ligne à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
			echo "</div><!-- /.bDcorps -->\n";
			echo "</div><!-- /.aideAdminRss -->\n";
			
			echo "<fieldset>\n";
			echo '<legend>' . T_("Options") . "</legend>\n";
			
			echo '<div class="configActuelleAdminRss">' . "\n";
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
			echo "</div><!-- /.configActuelleAdminRss -->\n";
			
			echo '<h4>' . T_("Ajouter une page") . "</h4>\n";
			
			echo "<ul>\n";
			echo '<li><select name="langueAjout">' . "\n";
			
			foreach ($accueil as $langueAccueil => $urlLangueAccueil)
			{
				echo '<option value="' . $langueAccueil . '"';
				
				if ($langueAccueil == $langueParDefaut)
				{
					echo ' selected="selected"';
				}
				
				echo '>' . $langueAccueil . "</option>\n";
			}
			
			echo "</select>\n";
			
			echo "<ul>\n";
			echo '<li><label for="inputUrlAjout"><code>pages[]=</code></label><input id="inputUrlAjout" class="long" type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p><input id="inputSitemapAjout" type="checkbox" name="sitemapAjout" value="ajout" checked="checked" /> <label for="inputSitemapAjout">' . sprintf(T_("Ajouter la page dans le <a href=\"%1\$s\">fichier Sitemap du site</a>."), 'sitemap.admin.php?sitemap=site') . "</label></p>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsSite" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
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
	
	if (isset($_POST['modifsGaleries']))
	{
		$messagesScript = '';
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
		
		$messagesScript .= adminEnregistreConfigFluxRssGlobalGaleries($racine, $contenuFichier, $adminPorteDocumentsDroits);
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	elseif (isset($_POST['modifsSite']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications pour les pages du site") . "</h3>\n" ;
	
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
		
		$messagesScript .= adminEnregistreConfigFluxRssGlobalSite($racine, $contenuFichier, $adminPorteDocumentsDroits);
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
		
		if (isset($_POST['sitemapAjout']) && !empty($_POST['urlAjout']))
		{
			$urlAjout = $urlRacine . '/' . superRawurlencode($_POST['urlAjout']);
			$messagesScript = adminAjouteUrlDansSitemap($racine, 'site', array ($urlAjout => array ()), $adminPorteDocumentsDroits);
			
			echo adminMessagesScript($messagesScript, T_("Ajout dans le fichier Sitemap du site"));
		}
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite">
	<h2 id="config"><?php echo T_("Configuration actuelle"); ?></h2>
	
	<ul>
		<?php if ($activerFluxRssGlobalSite): ?>
			<li><?php echo T_("Le flux RSS des dernières publications est activé") . ' (<code>$activerFluxRssGlobalSite = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("Le flux RSS des dernières publications n'est pas activé") . ' (<code>$activerFluxRssGlobalSite = FALSE;</code>).'; ?></li>
		<?php endif; ?>
		
		<?php if ($galerieActiverFluxRssGlobal): ?>
			<li><?php echo T_("Le flux RSS des derniers ajouts aux galeries est activé") . ' (<code>$galerieActiverFluxRssGlobal = TRUE;</code>).'; ?></li>
		<?php else: ?>
			<li><?php echo T_("Le flux RSS des derniers ajouts aux galeries n'est pas activé") . ' (<code>$galerieActiverFluxRssGlobal = FALSE;</code>).'; ?></li>
		<?php endif; ?>
	</ul>
	
	<?php if ($adminPorteDocumentsDroits['editer']): ?>
		<p><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
	<?php endif; ?>
</div><!-- /.boite -->

<div class="boite">
	<h2 id="choixTypePages"><?php echo T_("Pages ajoutées aux flux RSS globaux"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<ul>
					<li><input id="inputGlobalSite" type="radio" name="global" value="site" checked="checked" /> <label for="inputGlobalSite"><?php echo T_("Dernières publications"); ?></label></li>
					<li><input id="inputGlobalGaleries" type="radio" name="global" value="galeries" /> <label for="inputGlobalGaleries"><?php echo T_("Derniers ajouts aux galeries"); ?></label></li>
				</ul>
			</fieldset>
			
			<p><input type="submit" name="action" value="<?php echo T_('Lister les pages'); ?>" /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
