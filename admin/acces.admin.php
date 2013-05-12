<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Accès");
$boitesDeroulantes = '.contenuFichierPourSauvegarde';
include $racineAdmin . '/inc/premier.inc.php';
?>

<div id="sousMenu">
	<ul>
		<li><a href="#messages"><?php echo T_("Messages"); ?></a></li>
		<li><a href="#utilisateurs"><?php echo T_("Utilisateurs"); ?></a></li>
		<li><a href="#droits"><?php echo T_("Droits d'accès"); ?></a></li>
		<li><a href="#langues"><?php echo T_("Langues"); ?></a></li>
		<li><a href="#maintenance"><?php echo T_("Maintenance"); ?></a></li>
		<li><a href="#cache"><?php echo T_("Cache"); ?></a></li>
		<li><a href="#cron"><?php echo T_("Cron"); ?></a></li>
		<li><a href="#sauvegarde"><?php echo T_("Sauvegarde"); ?></a></li>
		<li><a href="#page"><?php echo T_("Haut"); ?></a></li>
	</ul>
</div>

<div id="contenuPrincipal">
	<h1><?php echo T_("Gestion de l'accès au site et à l'administration"); ?></h1>

	<div id="boiteMessages" class="boite">
		<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

		<?php
		// Début des tests pour vérifier l'accessibilité des fichiers nécessaires au script.
		$messagesScript = '';
		$erreurAccesFichiers = FALSE;
	
		if ($ficTest = @fopen($racine . '/.htaccess', 'a+'))
		{
			fclose($ficTest);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s en lecture et en écriture impossible. Veuillez lui assigner les bons droits et revisiter la présente page."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
			$erreurAccesFichiers = TRUE;
		}

		if ($ficTest = @fopen($racine . '/.acces', 'a+'))
		{
			fclose($ficTest);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s en lecture et en écriture impossible. Veuillez lui assigner les bons droits et revisiter la présente page."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
			$erreurAccesFichiers = TRUE;
		}
	
		echo adminMessagesScript($messagesScript);
		// Fin des tests.

		########################################################################
		##
		## Gestion des droits d'accès à l'administration.
		##
		########################################################################

		if (!$erreurAccesFichiers && isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))
		{
			$messagesScript = '';
			$identifiant = '';
			
			if (!empty($_POST['identifiant']))
			{
				$identifiant = preg_replace('/[^A-Za-z0-9]/', '', $_POST['identifiant']);
			}
			
			if (empty($identifiant))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucun identifiant spécifié.") . "</li>\n";
			}
			elseif ((isset($_POST['ajouter']) || isset($_POST['modifier'])) && (strlen($_POST['motDePasse']) < 8 || !preg_match('/\d/', $_POST['motDePasse']) || !preg_match('/[A-Za-z]/', $_POST['motDePasse'])))
			{
				$messagesScript .= '<li class="erreur">' . T_("Pour une question de sécurité, le mot de passe doit contenir au moins huit caractères ainsi qu'au moins un chiffre et une lettre.") . "</li>\n";
			}
			elseif ((isset($_POST['ajouter']) || isset($_POST['modifier'])) && $_POST['motDePasse'] != $_POST['motDePasse2'])
			{
				$messagesScript .= '<li class="erreur">' . T_("Veuillez confirmer correctement le mot de passe.") . "</li>\n";
			}
			// Ajout d'un utilisateur.
			elseif (isset($_POST['ajouter']))
			{
				if ($fic2 = @fopen($racine . '/.acces', 'a+'))
				{
					if (stristr(PHP_OS, 'win') || $serveurFreeFr)
					{
						$acces = $identifiant . ':' . $_POST['motDePasse'] . "\n";
					}
					else
					{
						$acces = $identifiant . ':' . chiffreMotDePasse($_POST['motDePasse']) . "\n";
					} 
			
					// On vérifie si l'utilisateur est déjà présent.
					$utilisateurAbsent = TRUE;
				
					while (!feof($fic2))
					{
						$ligne = fgets($fic2);
					
						if (strpos($ligne, $identifiant . ':') === 0)
						{
							$utilisateurAbsent = FALSE;
							break;
						}
					}
		
					if ($utilisateurAbsent)
					{
						fputs($fic2, $acces);
						$messagesScript .= '<li>' . sprintf(T_("Ajout de l'utilisateur <em>%1\$s</em> effectué."), $identifiant) . "</li>\n";
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("L'utilisateur <em>%1\$s</em> a déjà les droits."), $identifiant) . "</li>\n";
					}
		
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
			}
			// Modification d'un utilisateur.
			elseif (isset($_POST['modifier']))
			{
				if ($fic2 = @fopen($racine . '/.acces', 'r'))
				{
					$utilisateurs = array ();
				
					// On vérifie si l'utilisateur est déjà présent.
					$utilisateurAbsent = TRUE;
				
					while (!feof($fic2))
					{
						$ligne = fgets($fic2);
					
						if (strpos($ligne, $identifiant . ':') === 0)
						{
							$utilisateurAbsent = FALSE;
							$ligne = $identifiant . ':' . chiffreMotDePasse($_POST['motDePasse']) . "\n";
						}
			
						$utilisateurs[] = $ligne;
					}
		
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
		
				if ($fic2 = @fopen($racine . '/.acces', 'w'))
				{
					fputs($fic2, implode("\n", $utilisateurs));
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
		
				if ($utilisateurAbsent)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("L'utilisateur <em>%1\$s</em> n'a pas les droits. Son mot de passe ne peut donc pas être modifié."), $identifiant) . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Modification du mot de passe de l'utilisateur <em>%1\$s</em> effectuée."), $identifiant) . "</li>\n";
				}
			}
	
			// Suppression d'un utilisateur.
			elseif (isset($_POST['supprimer']))
			{
				if ($fic2 = @fopen($racine . '/.acces', 'r'))
				{
					$utilisateurs = '';
				
					// On vérifie si l'utilisateur est déjà présent.
					$utilisateurAbsent = TRUE;
		
					while (!feof($fic2))
					{
						$ligne = fgets($fic2);
					
						if (strpos($ligne, $identifiant . ':') === 0)
						{
							$utilisateurAbsent = FALSE;
						}
						elseif ($ligne != "\n")
						{
							$utilisateurs .= $ligne;
						}
					}
		
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
		
				if ($fic2 = @fopen($racine . '/.acces', 'w'))
				{
					fputs($fic2, $utilisateurs);
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
		
				// S'il n'y a plus d'utilisateur dans le fichier `.acces`, on supprime l'authentification dans le `.htaccess`.
				/*
				Note: auparavant, je faisais:
					clearstatcache();
					if (@filesize($racine . '/.acces') === 0) {...}
				mais des lignes vides faisaient en sorte que la taille du fichier n'était pas à 0. Maintenant je fais simplement regarder s'il y a un `:` dans le fichier, ce qui signifierait qu'il y a au moins un utilisateur.
				*/
				if (strpos(file_get_contents($racine . '/.acces'), ':') === FALSE)
				{
					if ($fic2 = @fopen($racine . '/.htaccess', 'r'))
					{
						$fichierHtaccess = array ();
					
						while (!feof($fic2))
						{
							$ligne = rtrim(fgets($fic2));
						
							if (strpos($ligne, '# Ajout automatique de Squeletml (accès admin). Ne pas modifier.') === 0)
							{
								while (strpos($ligne, '# Fin de l\'ajout automatique de Squeletml (accès admin).') !== 0)
								{
									$ligne = fgets($fic2);
								}
							}
							else
							{
								$fichierHtaccess[] = $ligne;
							}
						}
		
						fclose($fic2);
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
					}
			
					if ($fic2 = @fopen($racine . '/.htaccess', 'w'))
					{
						fputs($fic2, implode("\n", $fichierHtaccess));
						fclose($fic2);
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
					}
				}
		
				if ($utilisateurAbsent)
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("L'utilisateur <em>%1\$s</em> n'a pas les droits. Il ne peut donc pas être supprimé."), $identifiant) . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Utilisateur <em>%1\$s</em> supprimé."), $identifiant) . "</li>\n";
				}
			}
	
			// Lien vers `.acces` à partir de `.htaccess`.
			$messagesScript .= accesDansHtaccess($racine, $serveurFreeFr);
			
			echo adminMessagesScript($messagesScript, T_("Gestion des droits d'accès à l'administration"));
		}

		########################################################################
		##
		## Listage des utilisateurs.
		##
		########################################################################

		if (!$erreurAccesFichiers && ((isset($_POST['lister'])) || (isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))))
		{
			echo '<div class="sousBoite">' . "\n";
			echo '<h3>' . T_("Liste des utilisateurs") . "</h3>\n";

			echo '<p>' . T_("Voici les utilisateurs ayant accès à l'administration:") . "</p>\n" . "\n";

			echo '<ul>' . "\n";
			$i = 0;
		
			if (file_exists($racine . '/.acces'))
			{
				if ($fic3 = @fopen($racine . '/.acces', 'r'))
				{
					$listeUtilisateurs = array ();
				
					while (!feof($fic3))
					{
						$ligne = fgets($fic3);
					
						if (preg_match('/^[^:]+:/', $ligne))
						{
							list ($utilisateur) = explode(':', $ligne);
							$listeUtilisateurs[] = $utilisateur;
							$i++;
						}
					}
				
					fclose($fic3);
				
					if (!empty($listeUtilisateurs))
					{
						natcasesort($listeUtilisateurs);
					
						foreach ($listeUtilisateurs as $utilisateur)
						{
							echo '<li>' . securiseTexte($utilisateur) . "</li>\n";
						}
					}
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.acces") . '</code>') . "</li>\n";
				}
			}

			if (!$i)
			{
				echo '<li>' . T_("Aucun") . "</li>\n";
			}
		
			echo "</ul>\n";
			echo "</div><!-- /.sousBoite -->\n";
		}
		
		########################################################################
		##
		## Gestion des langues livrées par défaut.
		##
		########################################################################
		
		if (isset($_POST['activerLangues']))
		{
			$messagesScript = '';
			$messagesScript .= majLanguesActives($racine, $urlRacine, $_POST['langues']);
			
			echo adminMessagesScript($messagesScript, T_("Gestion des langues livrées par défaut"));
		}
		
		########################################################################
		##
		## Mise hors ligne du site pour maintenance.
		##
		########################################################################

		if (!$erreurAccesFichiers && isset($_POST['changerEtat']))
		{
			$messagesScript = '';
			$maintenanceDansHtaccess = FALSE;
		
			if ($fic = @fopen($racine . '/.htaccess', 'r'))
			{
				while (!feof($fic))
				{
					$ligne = rtrim(fgets($fic));
				
					if (strpos($ligne, '# Ajout automatique de Squeletml (maintenance). Ne pas modifier.') === 0)
					{
						$maintenanceDansHtaccess = TRUE;
						break;
					}
				}
				fclose($fic);
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
			}

			if ($_POST['etat'] == 'horsLigne' && !$maintenanceDansHtaccess)
			{
				$htaccess = '';
				$htaccess .= "# Ajout automatique de Squeletml (maintenance). Ne pas modifier.\n";
				$htaccess .= "<IfModule mod_rewrite.c>\n";
				$htaccess .= "\tOptions +FollowSymLinks\n";
				$htaccess .= "\tRewriteEngine on\n";
				$htaccess .= "\tRewriteBase /\n";
		
				if (!empty($_POST['ip']))
				{
					$ip = str_replace('.', '\.', securiseTexte($_POST['ip']));
					$htaccess .= "\tRewriteCond %{REMOTE_ADDR} !^$ip\n";
				}
		
				preg_match('#^[a-z]+://' . preg_quote($_SERVER['SERVER_NAME'], '#') . '(/.+)#i', $urlRacine . '/' . $adminUrlMaintenance, $resultat);
				$adminUrlMaintenanceDansHtaccess = $resultat[1];
		
				$htaccess .= "\tRewriteCond %{REQUEST_URI} !$adminUrlMaintenanceDansHtaccess$\n";
				$htaccess .= "\tRewriteRule .* $adminUrlMaintenanceDansHtaccess [L]\n";
				$htaccess .= "</IfModule>\n";
				$htaccess .= "# Fin de l'ajout automatique de Squeletml (maintenance).\n";
	
				if ($fic = @fopen($racine . '/.htaccess', 'a+'))
				{
					fputs($fic, $htaccess);
					fclose($fic);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), "<code>$racine/.htaccess</code>") . "</li>\n";
				}
			}
			elseif ($_POST['etat'] == 'horsLigne' && $maintenanceDansHtaccess && $_POST['ip'] != adminSiteEnMaintenanceIp($racine . '/.htaccess'))
			{
				if ($fic2 = @fopen($racine . '/.htaccess', 'r'))
				{
					$fichierHtaccess = array ();
				
					while (!feof($fic2))
					{
						$ligne = rtrim(fgets($fic2));
					
						if (strpos($ligne, '# Ajout automatique de Squeletml (maintenance). Ne pas modifier.') === 0)
						{
							$fichierHtaccess[] = $ligne;
						
							while (strpos($ligne, '# Fin de l\'ajout automatique de Squeletml (maintenance).') !== 0)
							{
								$ligne = rtrim(fgets($fic2));
							
								if (preg_match('/^\tRewriteCond %{REMOTE_ADDR} !\^(([0-9]{1,4}\\\.){3}[0-9]{1,4})/', $ligne))
								{
									if (!empty($_POST['ip']))
									{
										$ip = str_replace('.', '\.', securiseTexte($_POST['ip']));
										$fichierHtaccess[] = "\tRewriteCond %{REMOTE_ADDR} !^$ip";
									}
								}
								else
								{
									$fichierHtaccess[] = $ligne;
								}
							}
						}
						else
						{
							$fichierHtaccess[] = $ligne;
						}
					}
	
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
				}
		
				if ($fic2 = @fopen($racine . '/.htaccess', 'w'))
				{
					fputs($fic2, implode("\n", $fichierHtaccess));
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
				}
			}
			elseif ($_POST['etat'] == 'enLigne' && $maintenanceDansHtaccess)
			{
				if ($fic2 = @fopen($racine . '/.htaccess', 'r'))
				{
					$fichierHtaccess = array ();
				
					while (!feof($fic2))
					{
						$ligne = rtrim(fgets($fic2));
					
						if (strpos($ligne, '# Ajout automatique de Squeletml (maintenance). Ne pas modifier.') === 0)
						{
							while (strpos($ligne, '# Fin de l\'ajout automatique de Squeletml (maintenance).') !== 0)
							{
								$ligne = fgets($fic2);
							}
						}
						else
						{
							$fichierHtaccess[] = $ligne;
						}
					}
	
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
				}
		
				if ($fic2 = @fopen($racine . '/.htaccess', 'w'))
				{
					fputs($fic2, implode("\n", $fichierHtaccess));
					fclose($fic2);
				}
				else
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte("$racine/.htaccess") . '</code>') . "</li>\n";
				}
			}
	
			if (siteEstEnMaintenance($racine . '/.htaccess'))
			{
				$messagesScript .= '<li>' . T_("Le site est en maintenance (hors ligne).") . "</li>\n";
			
				if ($ip = adminSiteEnMaintenanceIp($racine . '/.htaccess'))
				{
					$messagesScript .= '<li>' . sprintf(T_("L'IP %1\$s a accès au site hors ligne."), $ip) . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . T_("Aucune IP n'a accès au site hors ligne.") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li>' . T_("Le site est en ligne.") . "</li>\n";
			}
		
			echo adminMessagesScript($messagesScript, T_("Maintenance du site"));
		}
	
		########################################################################
		##
		## Suppression du cache.
		##
		########################################################################

		if (isset($_POST['supprimerCache']))
		{
			$messagesScript = '';
		
			if (!isset($_POST['cache']))
			{
				$messagesScript .= '<li class="erreur">' . T_("Aucun type de cache sélectionné.") . "</li>\n";
			}
			else
			{
				if (in_array('admin', $_POST['cache']))
				{
					if (adminVideCache($racineAdmin, 'admin'))
					{
						$messagesScript .= '<li>' . T_("Suppression du cache de l'administration effectuée.") . "</li>\n";
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . T_("Erreur lors de la suppression du cache de l'administration. Veuillez vérifier manuellement son contenu.") . "</li>\n";
					}
				}
			
				if (in_array('site', $_POST['cache']))
				{
					if (adminVideCache($racineAdmin, 'site'))
					{
						$messagesScript .= '<li>' . T_("Suppression du cache du site effectuée.") . "</li>\n";
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . T_("Erreur lors de la suppression du cache du site. Veuillez vérifier manuellement son contenu.") . "</li>\n";
					}
				}
			
				if (empty($messagesScript))
				{
					$messagesScript .= '<li class="erreur">' . T_("Le type de cache sélectionné n'est pas valide.") . "</li>\n";
				}
			}
		
			echo adminMessagesScript($messagesScript, T_("Suppression du cache"));
		}
	
		########################################################################
		##
		## Lancement du cron.
		##
		########################################################################

		if (isset($_POST['lancerCron']))
		{
			$lancementCronDansAdmin = TRUE;
			include $racine . '/cron.php';
			$messagesScript = '';
			$messagesScript .= '<li>';
			$messagesScript .= '<p>' . T_("Lancement du cron effectué et terminé.") . "</p>\n";
			
			if (!empty($rapport))
			{
				$messagesScript .= "<div id=\"rapportCron\">\n";
				$messagesScript .= $rapport;
				$messagesScript .= "</div><!-- /#rapportCron -->\n";
			}
			
			$messagesScript .= "</li>\n";
			
			echo adminMessagesScript($messagesScript, T_("Lancement du cron"));
		}
		?>
	</div><!-- /#boiteMessages -->

	<?php
	########################################################################
	##
	## Formulaires.
	##
	########################################################################
	?>
	<?php if (!$erreurAccesFichiers): ?>
		<div class="boite">
			<h2 id="utilisateurs"><?php echo T_("Lister les utilisateurs ayant accès à l'administration"); ?></h2>

			<p><?php echo T_("Vous pouvez afficher la liste des utilisateurs ayant accès à l'administration."); ?></p>

			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<p><input type="submit" name="lister" value="<?php echo T_('Lister les utilisateurs'); ?>" /></p>
				</div>
			</form>
		</div><!-- /.boite -->

		<div class="boite">
			<h2 id="droits"><?php echo T_("Gérer les droits d'accès à l'administration"); ?></h2>

			<p><?php echo T_("Vous pouvez ajouter ou supprimer un utilisateur en remplissant le formulaire ci-dessous. Vous pouvez également modifier le mot de passe d'un utilisateur existant."); ?></p>

			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<fieldset>
						<legend><?php echo T_("Options"); ?></legend>
					
						<p><label for="inputIdentifiant"><?php echo T_("Identifiant (caractères alphanumériques):"); ?></label><br />
						<input id="inputIdentifiant" type="text" name="identifiant" /></p>
			
						<p><label for="inputMotDePasse"><?php echo T_("Mot de passe:"); ?></label><br />
						<input id="inputMotDePasse" type="password" name="motDePasse" /></p>
					
						<p><label for="inputMotDePasse2"><?php echo T_("Confirmer le mot de passe:"); ?></label><br />
						<input id="inputMotDePasse2" type="password" name="motDePasse2" /></p>
					</fieldset>
				
					<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter l\'utilisateur'); ?>" /> <input type="submit" name="supprimer" value="<?php echo T_('Supprimer l\'utilisateur'); ?>" /> <input type="submit" name="modifier" value="<?php echo T_('Modifier le mot de passe'); ?>" /></p>
				</div>
			</form>
		</div><!-- /.boite -->

		<div class="boite">
			<h2 id="langues"><?php echo T_("Gérer les langues livrées par défaut"); ?></h2>
			
			<p><?php echo T_("Une langue inactive n'apparaîtra pas dans le menu des langues par défaut, et son dossier d'accueil par défaut ne sera pas accessible sur le web. Prendre note que cela n'empêche pas de pouvoir créer des pages dans cette langue."); ?></p>
			
			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<fieldset>
						<legend><?php echo T_("Options"); ?></legend>
					
						<p><label for="inputLangues"><?php echo T_("Langues à activer:"); ?></label><br />
						<select id="inputLangues" name="langues[]" multiple="multiple">
							<?php $initIncPhp = @file_get_contents($racine . '/init.inc.php'); ?>
							
							<?php if ($initIncPhp !== FALSE): ?>
								<?php preg_match_all('/^\s*(#|\/\/)?\s*\$accueil\[\'([a-z]{2})\'\]\s*=/m', $initIncPhp, $resultatAccueil, PREG_SET_ORDER); ?>
								<?php $languesAccueil = array (); ?>
								
								<?php foreach ($resultatAccueil as $resultatAccueilTableauLangue): ?>
									<?php $languesAccueil[] = $resultatAccueilTableauLangue[2]; ?>
								<?php endforeach; ?>
								
								<?php preg_match_all('/^\s*\$accueil\[\'([a-z]{2})\'\]\s*=/m', $initIncPhp, $resultatLanguesActivesApresModif, PREG_SET_ORDER); ?>
								<?php $languesActivesApresModif = array (); ?>
								
								<?php foreach ($resultatLanguesActivesApresModif as $resultatLanguesActivesApresModifTableau): ?>
									<?php $languesActivesApresModif[] = $resultatLanguesActivesApresModifTableau[1]; ?>
								<?php endforeach; ?>
								
								<?php foreach ($languesAccueil as $langueAccueil): ?>
									<?php if (in_array($langueAccueil, $languesActivesApresModif)): ?>
										<?php $selected = ' selected="selected"'; ?>
									<?php else: ?>
										<?php $selected = ''; ?>
									<?php endif; ?>
									
									<option value="<?php echo $langueAccueil; ?>"<?php echo $selected; ?>><?php echo $langueAccueil; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select></p>
					</fieldset>
				
					<p><input type="submit" name="activerLangues" value="<?php echo T_('Activer les langues sélectionnées'); ?>" /></p>
				</div>
			</form>
		</div><!-- /.boite -->
		
		<div class="boite">
			<h2 id="maintenance"><?php echo T_("Mettre le site hors ligne pour maintenance"); ?></h2>

			<p><?php echo T_("Si le site est hors ligne, tous les internautes visitant une page du site seront redirigés vers la page de maintenance."); ?></p>

			<?php if (adminReecritureDurl(FALSE) == 'n'): ?>
				<p><strong><?php echo T_("La réécriture d'URL (module <code>mod_rewrite</code> d'Apache) n'est pas activée sur votre serveur. La fonction de mise hors ligne du site ne peut donc pas être utilisée."); ?></strong></p>
			<?php else: ?>
				<?php if (adminReecritureDurl(FALSE) == '?'): ?>
					<p><strong><?php echo T_("Note: impossible de savoir si la réécriture d'URL (module <code>mod_rewrite</code> d'Apache) est activée sur votre serveur. Si tel n'est pas le cas, la mise hors ligne du site ne fonctionnera pas et risque de provoquer une erreur 500."); ?></strong></p>
				<?php endif; ?>
				
				<form action="<?php echo $adminAction; ?>#messages" method="post">
					<div>
						<fieldset>
							<legend><?php echo T_("Options"); ?></legend>
				
							<?php $siteEstEnMaintenance = siteEstEnMaintenance($racine . '/.htaccess'); ?>
				
							<p><?php echo T_("Le site est présentement:"); ?><br />
							<input id="inputEtatEnLigne" type="radio" name="etat" value="enLigne" <?php if (!$siteEstEnMaintenance) {echo 'checked="checked"';} ?> /> <label for="inputEtatEnLigne"><?php echo T_("en ligne."); ?></label><br />
							<input id="inputEtatHorsLigne" type="radio" name="etat" value="horsLigne" <?php if ($siteEstEnMaintenance) {echo 'checked="checked"';} ?> /> <label for="inputEtatHorsLigne"><?php echo T_("en maintenance (hors ligne)."); ?></label> <?php if ($siteEstEnMaintenance): ?>
								<?php if ($ip = adminSiteEnMaintenanceIp($racine . '/.htaccess')): ?>
									<?php echo sprintf(T_("L'IP %1\$s a accès au site hors ligne."), $ip); ?>
								<?php else: ?>
									<?php echo T_("Aucune IP n'a accès au site hors ligne."); ?>
								<?php endif; ?>
							<?php endif; ?>
							</p>
		
							<p><label for="inputIp"><?php echo T_("IP ayant droit d'accès au site en maintenance (optionnel; laisser vide pour désactiver cette option):"); ?></label><br />
							<?php $ip = adminSiteEnMaintenanceIp($racine . '/.htaccess'); ?>
				
							<?php if ($ip): ?>
								<?php $valeurChampIp = $ip; ?>
							<?php else: ?>
								<?php $valeurChampIp = ipInternaute(); ?>
							<?php endif; ?>
				
							<input id="inputIp" type="text" name="ip" value="<?php echo $valeurChampIp; ?>" /></p>
						</fieldset>
			
						<p><input type="submit" name="changerEtat" value="<?php echo T_('Changer l\'état du site'); ?>" /></p>
					</div>
				</form>
			<?php endif; ?>
		</div><!-- /.boite -->
	
		<div class="boite">
			<h2 id="cache"><?php echo T_("Supprimer le cache"); ?></h2>

			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<fieldset>
						<legend><?php echo T_("Options"); ?></legend>
					
						<p><?php echo T_("Supprimer le cache:"); ?><br />
						<input id="inputCacheAdmin" type="checkbox" name="cache[]" value="admin" /> <label for="inputCacheAdmin"><?php echo T_("de l'administration."); ?></label><br />
						<input id="inputCacheSite" type="checkbox" name="cache[]" value="site" /> <label for="inputCacheSite"><?php echo T_("du site."); ?></label>
						</p>
					</fieldset>
				
					<p><input type="submit" name="supprimerCache" value="<?php echo T_('Supprimer le cache'); ?>" /></p>
				</div>
			</form>
		</div><!-- /.boite -->
	
		<div class="boite">
			<h2 id="cron"><?php echo T_("Lancer le cron manuellement"); ?></h2>

			<form action="<?php echo $adminAction; ?>#messages" method="post">
				<div>
					<?php $dateCron = @file_get_contents("$racine/site/inc/cron.txt"); ?>
				
					<?php if ($dateCron !== FALSE): ?>
						<p><?php printf(T_("Dernier lancement du cron le %1\$s à %2\$s."), date('Y-m-d', $dateCron), date('H:i:s', $dateCron)); ?></p>
					<?php endif; ?>
				
					<p><?php echo T_("Cette action peut prendre un certain temps."); ?></p>
				
					<p><input type="submit" name="lancerCron" value="<?php echo T_('Lancer le cron'); ?>" /></p>
				</div>
			</form>
		</div><!-- /.boite -->
	
		<div class="boite">
			<h2 id="sauvegarde"><?php echo T_("Obtenir une copie de sauvegarde du site"); ?></h2>
	
			<p><?php echo T_("Vous pouvez télécharger sur votre ordinateur une archive contenant tout le site (sauf le cache)."); ?></p>
	
			<p><a href="telecharger.admin.php?fichier=<?php echo encodeTexteGet($racine); ?>&amp;action=date"><?php echo T_('Télécharger une copie de sauvegarde du site.'); ?></a></p>
		</div><!-- /.boite -->
	<?php endif; ?>
</div><!-- /#contenuPrincipal -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
