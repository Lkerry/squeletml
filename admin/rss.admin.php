<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Flux RSS globaux");
$boitesDeroulantes = '.aideAdminRss .configActuelle .configActuelleAdminRss';
$boitesDeroulantes .= ' .contenuFichierPourSauvegarde';
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
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Liste des pages du flux RSS des derniers ajouts aux galeries") . "</h3>\n";
		
		echo '<p>';
		printf(T_("Cette liste est générée à partir des galeries dont le flux RSS est activé dans le <a href=\"%1\$s\">fichier de configuration des galeries</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexteGet('../site/inc/' . superBasename(cheminConfigGaleries($racine, TRUE))) . '&amp;dossierCourant=../site/inc#messages');
		echo "</p>\n";
		echo "</div><!-- /.sousBoite -->\n";
	}
	##################################################################
	#
	# Flux RSS des dernières publications.
	#
	##################################################################
	elseif (isset($_POST['global']) && $_POST['global'] == 'site')
	{
		$messagesScript = '';
		$cheminFichier = cheminConfigFluxRssGlobalSite($racine);
		
		if (!$cheminFichier)
		{
			$cheminFichier = cheminConfigFluxRssGlobalSite($racine, TRUE);
			
			if (!@touch($cheminFichier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<em>' . securiseTexte($cheminFichier) . '</em>') . "</li>\n";
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
						$listePages .= '<input type="text" name="langue[' . $i . ']" value="' . securiseTexte($codeLangue) . '" />';
					}
					
					$listePagesLangue = '';
					$j = 0;
					
					if (!empty($langueInfos['pages']))
					{
						foreach ($langueInfos['pages'] as $page)
						{
							$page = rtrim($page);
							
							if (!empty($page))
							{
								$listePagesLangue .= '<li><label for="inputUrl-' . $i . '-' . $j . '"><code>pages[]=</code></label><input id="inputUrl-' . $i . '-' . $j . '" class="long" type="text" name="url[' . $i . '][]" value="' . securiseTexte($page) . '" />';
								$cheminPage = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($page));
								$listePagesLangue .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexteGet($cheminPage) . '&amp;dossierCourant=' . encodeTexteGet(dirname($cheminPage)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPage)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminPage)) . '" width="16" height="16" /></a>';
								$listePagesLangue .= "</li>\n";
								$j++;
							}
						}
					}
					
					if (!empty($listePagesLangue))
					{
						$listePages .= "<ul class=\"triable\">\n";
						$listePages .= $listePagesLangue;
						$listePages .= "</ul></li>\n";
					}
					
					$i++;
				}
			}
		
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des pages du flux RSS des dernières publications") . "</h3>\n";
			
			echo '<div class="aideAdminRss aide">' . "\n";
			echo '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
			
			echo "<div class=\"bDcorps\">\n";
			echo '<p>' . sprintf(T_("Les pages sont classées par section représentant la langue. À l'intérieur d'une section, chaque ligne est sous la forme %1\$s. Voici un exemple:"), '<code>pages[]=' . T_("URL relative de la page") . '</code>') . "</p>\n";
			
			echo "<ul>\n";
			echo "<li><code>fr</code>\n";
			echo "<ul>\n";
			echo "<li><code>pages[]=animaux/chiens.php</code></li>\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			
			echo '<p>' . sprintf(T_("Cet exemple fait référence à une page en français dont l'URL est %1\$s."), '<code>' . securiseTexte("$urlRacine/animaux/chiens.php") . '</code>') . "</p>\n";
			
			echo '<p>' . T_("Pour enlever une page, simplement supprimer le contenu du champ.") . "</p>\n";
			
			echo '<p>' . T_("Prendre note que si une URL contient des caractères spéciaux, elle devra être fournie sous forme encodée. Le plus simple est de copier l'adresse dans la barre de navigation du navigateur utilisé et de coller le résultat dans le champ approprié. L'URL racine sera automatiquement supprimée pour convertir l'adresse fournie en adresse relative.") . "</p>\n";
			
			echo '<p>' . T_("Voici un exemple:") . "</p>\n";
			
			echo "<p><code>http://www.monsite.ext/animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></p>\n";
			
			echo '<p>' . T_("Le résultat sera semblable à ci-dessous:") . "</p>\n";
			
			echo "<p><code>pages[]=animaux/canid%C3%A9s/husky%20sib%C3%A9rien.php</code></p>\n";
			
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
				echo "<ul class=\"bDcorps\">\n";
			}
			else
			{
				echo "<ul class=\"triable bDcorps\">\n";
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
			
			echo '<ul id="rssUrlAjout">' . "\n";
			echo '<li><label for="inputUrlAjout"><code>pages[]=</code></label><input id="inputUrlAjout" class="long" type="text" name="urlAjout" value="" /></li>' . "\n";
			echo "</ul></li>\n";
			echo "</ul>\n";
			echo "</fieldset>\n";
			
			echo '<p><input type="submit" name="modifsSite" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
			echo "</div>\n";
			echo "</form>\n";
			echo "</div><!-- /.sousBoite -->\n";
		}
		elseif (file_exists($cheminFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript);
	}
	
	if (isset($_POST['modifsSite']))
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications pour les pages du site") . "</h3>\n" ;
	
		$contenuFichierTableau = array ();
		
		if (!empty($_POST['langue']))
		{
			foreach ($_POST['langue'] as $cle => $postLangueValeur)
			{
				$urlCle = array ();
				
				if (!empty($_POST['url'][$cle]))
				{
					foreach ($_POST['url'][$cle] as $page)
					{
						$page = supprimeUrlRacine($urlRacine, $page);
						
						if (!empty($page))
						{
							$urlCle[] = $page;
						}
					}
				}
				
				if (!empty($postLangueValeur) && !empty($urlCle))
				{
					$contenuFichierTableau[$postLangueValeur] = array ();
					
					foreach ($urlCle as $page)
					{
						if (!empty($page) && !preg_grep('/^pages\[\]=' . preg_quote($page, '/') . "\n/", $contenuFichierTableau[$postLangueValeur]))
						{
							$contenuFichierTableau[$postLangueValeur][] = "pages[]=$page\n";
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
		
		if (!empty($_POST['langueAjout']) && !empty($urlAjout))
		{
			$langueAjout = securiseTexte($_POST['langueAjout']);
			
			if (!isset($contenuFichierTableau[$langueAjout]))
			{
				$contenuFichierTableau[$langueAjout] = array ();
			}

			if (!preg_grep('/^pages\[\]=' . preg_quote($urlAjout, '/') . "\n/", $contenuFichierTableau[$langueAjout]))
			{
				array_unshift($contenuFichierTableau[$langueAjout], "pages[]=$urlAjout\n");
			}
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
		
		$messagesScript .= adminEnregistreConfigFluxRssGlobalSite($racine, $contenuFichier);
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite configActuelle">
	<h2 id="config" class="bDtitre"><?php echo T_("Configuration actuelle"); ?></h2>
	
	<div class="bDcorps afficher">
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
		
		<p><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
	</div><!-- /.bDcorps -->
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
