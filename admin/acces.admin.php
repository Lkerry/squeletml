<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Accès");
include 'inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion de l'accès au site et à l'administration"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	// Début des tests pour vérifier l'accessibilité des fichiers nécessaires au script
	$erreurAccesFichiers = FALSE;
	
	if ($ficTest = @fopen($racine . '/.htaccess', 'a+'))
	{
		fclose($ficTest);
	}
	else
	{
		echo '<p class="erreur">' . sprintf(T_('Impossible d\'ouvrir le fichier %1$s en lecture et en écriture. Veuillez lui assigner les bons droits et revisiter la présente page.'), "<code>$racine/.htaccess</code>") . "</p>\n";
		$erreurAccesFichiers = TRUE;
	}

	if ($ficTest = @fopen($racine . '/.acces', 'a+'))
	{
		fclose($ficTest);
	}
	else
	{
		echo '<p class="erreur">' . sprintf(T_('Impossible d\'ouvrir le fichier %1$s en lecture et en écriture. Veuillez lui assigner les bons droits et revisiter la présente page.'), "<code>$racine/.acces</code>") . "</p>\n";
		$erreurAccesFichiers = TRUE;
	}
	// Fin des tests

	########################################################################
	##
	## Gérer les droits d'accès à l'administration
	##
	########################################################################

	if (!$erreurAccesFichiers && isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))
	{
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Gestion des droits d'accès à l'administration") . "</h3>\n";
	
		// Ajout d'un utilisateur
		if (isset($_POST['ajouter']))
		{
			if ($fic2 = fopen($racine . '/.acces', 'a+'))
			{
				if (stristr(PHP_OS, 'win') || $serveurFreeFr)
				{
					$acces = securiseTexte($_POST['nom']) . ':' . securiseTexte($_POST['motDePasse']) . "\n";
				}
				else
				{
					$acces = securiseTexte($_POST['nom']) . ':' . crypt(securiseTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n";
				} 
			
				// On vérifie si l'utilisateur est déjà présent
				$utilisateurAbsent = TRUE;
				while (!feof($fic2))
				{
					$ligne = fgets($fic2);
					if (preg_match('/^' . securiseTexte($_POST['nom']) . ':/', $ligne))
					{
						$utilisateurAbsent = FALSE;
						break;
					}
				}
		
				if ($utilisateurAbsent)
				{
					fputs($fic2, $acces);
					echo '<p class="succes">' . sprintf(T_('Utilisateur <em>%1$s</em> ajouté.'), securiseTexte($_POST['nom'])) . "</p>\n";
				}
				else
				{
					echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> a déjà les droits.'), securiseTexte($_POST['nom'])) . "</p>\n";
				}
		
				fclose($fic2);
			}
		}
	
		// Modification d'un utilisateur
		elseif (isset($_POST['modifier']))
		{
			if ($fic2 = fopen($racine . '/.acces', 'r'))
			{
				$utilisateurs = array ();
				// On vérifie si l'utilisateur est déjà présent
				$utilisateurAbsent = TRUE;
		
				while (!feof($fic2))
				{
					$ligne = fgets($fic2);
					if (preg_match('/^' . securiseTexte($_POST['nom']) . ':/', $ligne))
					{
						$utilisateurAbsent = FALSE;
						$ligne = securiseTexte($_POST['nom']) . ':' . crypt(securiseTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n";
					}
			
					$utilisateurs[] = $ligne;
				}
		
				fclose($fic2);
			}
		
			if ($fic2 = fopen($racine . '/.acces', 'w'))
			{
				fputs($fic2, implode("\n", $utilisateurs));
				fclose($fic2);
			}
		
			if ($utilisateurAbsent)
			{
				echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> n\'a pas les droits. Son mot de passe ne peut donc pas être modifié.'), securiseTexte($_POST['nom'])) . "</p>\n";
			}
			else
			{
				echo '<p class="succes">' . sprintf(T_('Mot de passe de l\'utilisateur <em>%1$s</em> modifié.'), securiseTexte($_POST['nom'])) . "</p>\n";
			}
		}
	
		// Suppression d'un utilisateur
		elseif (isset($_POST['supprimer']))
		{
			if ($fic2 = fopen($racine . '/.acces', 'r'))
			{
				$utilisateurs = array ();
				// On vérifie si l'utilisateur est déjà présent
				$utilisateurAbsent = TRUE;
		
				while (!feof($fic2))
				{
					$ligne = fgets($fic2);
					if (preg_match('/^' . securiseTexte($_POST['nom']) . ':/', $ligne))
					{
						$utilisateurAbsent = FALSE;
					}
					else
					{
						$utilisateurs[] = $ligne;
					}
				}
		
				fclose($fic2);
			}
		
			if ($fic2 = fopen($racine . '/.acces', 'w'))
			{
				fputs($fic2, implode("\n", $utilisateurs));
				fclose($fic2);
			}
		
			// S'il n'y a plus d'utilisateur dans le fichier `.acces`, on supprime ce fichier ainsi que l'authentification dans le .htaccess.
			/* Note: auparavant, je faisais:
			clearstatcache();
			if (filesize($racine . '/.acces') == 0) {...}
			mais des lignes vides faisaient en sorte que la taille du fichier n'était pas à 0. Maintenant je fais simplement regarder s'il y a un `:` dans le fichier, ce qui signifierait qu'il y a au moins un utilisateur. */
			if (strpos(file_get_contents($racine . '/.acces'), ':') === FALSE)
			{
				unlink($racine . '/.acces');
			
				if ($fic2 = fopen($racine . '/.htaccess', 'r'))
				{
					$fichierHtaccess = array ();
					while (!feof($fic2))
					{
						$ligne = rtrim(fgets($fic2));
						if (preg_match('/^# Ajout automatique de Squeletml \(accès admin\). Ne pas modifier./', $ligne))
						{
							while (!preg_match('/^# Fin de l\'ajout automatique de Squeletml \(accès admin\)./', $ligne))
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
			
				if ($fic2 = fopen($racine . '/.htaccess', 'w'))
				{
					fputs($fic2, implode("\n", $fichierHtaccess));
					fclose($fic2);
				}
			}
		
			if ($utilisateurAbsent)
			{
				echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> n\'a pas les droits. Il ne peut donc pas être supprimé.'), securiseTexte($_POST['nom'])) . "</p>\n";
			}
			else
			{
				echo '<p class="succes">' . sprintf(T_('Utilisateur <em>%1$s</em> supprimé.'), securiseTexte($_POST['nom'])) . "</p>\n";
			}
		}
	
		// Lien vers `.acces` à partir de `.htaccess`
		if (file_exists($racine . '/.acces') && strpos(file_get_contents($racine . '/.acces'), ':') !== FALSE)
		{
			$lienAccesDansHtaccess = FALSE;
			if ($fic = fopen($racine . '/.htaccess', 'r'))
			{
				while (!feof($fic))
				{
					$ligne = rtrim(fgets($fic));
					if (preg_match('/^# Ajout automatique de Squeletml \(accès admin\). Ne pas modifier./', $ligne))
					{
						$lienAccesDansHtaccess = TRUE;
						break;
					}
				}
				fclose($fic);
			}
	
			if (!$lienAccesDansHtaccess)
			{
				$htaccess = '';
				$htaccess .= "# Ajout automatique de Squeletml (accès admin). Ne pas modifier.\n";
				$htaccess .= "# Empêcher l'affichage direct de certains fichiers.\n";
		
				$htaccessFilesModele = "(ChangeLog|ChangeLogDerniereVersion|\.acces|\.admin\.php|\.defaut|\.mdtxt|\.pc|\.txt|\.xml)$";
		
				if ($serveurFreeFr)
				{
					$htaccess .= "<Files ~ \"$htaccessFilesModele\">\n";
			
					preg_match('|/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+/[^/]+(.+)|', $racine . '/.acces', $cheminAcces);
			
					$htaccess .= "\tPerlSetVar AuthFile " . $cheminAcces[1] . "\n";
				}
				else
				{
					$htaccess .= "<FilesMatch \"$htaccessFilesModele\">\n";
					$htaccess .= "\tAuthUserFile $racine/.acces\n";
				}
		
				$htaccess .= "\tAuthType Basic\n";
				$htaccess .= "\tAuthName \"Zone d'identification\"\n";
				$htaccess .= "\tRequire valid-user\n";
		
				if ($serveurFreeFr)
				{
					$htaccess .= "</Files>\n";
				}
				else
				{
					$htaccess .= "</FilesMatch>\n";
				}
		
				$htaccess .= "# Fin de l'ajout automatique de Squeletml (accès admin).\n";
		
				if ($fic = fopen($racine . '/.htaccess', 'a+'))
				{
					fputs($fic, $htaccess);
					fclose($fic);
				}
			}
		}
	
		echo "</div><!-- /class=boite2 -->\n";
	}

	########################################################################
	##
	## Lister les utilisateurs
	##
	########################################################################

	if (!$erreurAccesFichiers && ((isset($_POST['lister'])) || (isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))))
	{
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Liste des utilisateurs") . "</h3>\n";

		echo '<p>' . T_("Voici les utilisateurs ayant accès à l'administration:") . "</p>\n" . "\n";

		echo '<ul>' . "\n";
		$i = 0;
		if (file_exists($racine . '/.acces') && $fic3 = fopen($racine . '/.acces', 'r'))
		{
			while (!feof($fic3))
			{
				$ligne = fgets($fic3);
				if (preg_match('/^[^:]+:/', $ligne))
				{
					list($utilisateur, $motDePasse) = explode(':', $ligne, 2);
					echo '<li>' . $utilisateur . "</li>\n";
					$i++;
				}
			}

			fclose($fic3);
		}

		if (!$i)
		{
			echo '<li>' . T_("Aucun") . "</li>\n";
		}
		echo "</ul>\n";
		echo "</div><!-- /class=boite2 -->\n";
	}

	########################################################################
	##
	## Mettre le site hors ligne pour maintenance
	##
	########################################################################

	if (!$erreurAccesFichiers && isset($_POST['changerEtat']))
	{
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Maintenance du site") . "</h3>\n";
	
		$maintenanceDansHtaccess = FALSE;
		if ($fic = fopen($racine . '/.htaccess', 'r'))
		{
			while (!feof($fic))
			{
				$ligne = rtrim(fgets($fic));
				if (preg_match('/^# Ajout automatique de Squeletml \(maintenance\). Ne pas modifier./', $ligne))
				{
					$maintenanceDansHtaccess = TRUE;
					break;
				}
			}
			fclose($fic);
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
		
			preg_match('|^[a-z]+://' . $_SERVER['SERVER_NAME'] . '(/.+)|i', $urlRacine . '/' . $urlMaintenance, $resultat);
			$urlMaintenanceDansHtaccess = $resultat[1];
		
			$htaccess .= "\tRewriteCond %{REQUEST_URI} !$urlMaintenanceDansHtaccess$\n";
			$htaccess .= "\tRewriteRule .* $urlMaintenanceDansHtaccess [L]\n";
			$htaccess .= "</IfModule>\n";
			$htaccess .= "# Fin de l'ajout automatique de Squeletml (maintenance).\n";
	
			if ($fic = fopen($racine . '/.htaccess', 'a+'))
			{
				fputs($fic, $htaccess);
				fclose($fic);
			}
		}
		elseif ($_POST['etat'] == 'horsLigne' && $maintenanceDansHtaccess && $_POST['ip'] != adminSiteEnMaintenanceIp($racine . '/.htaccess'))
		{
			if ($fic2 = fopen($racine . '/.htaccess', 'r'))
			{
				$fichierHtaccess = array ();
				while (!feof($fic2))
				{
					$ligne = rtrim(fgets($fic2));
					if (preg_match('/^# Ajout automatique de Squeletml \(maintenance\). Ne pas modifier./', $ligne))
					{
						$fichierHtaccess[] = $ligne;
						while (!preg_match('/^# Fin de l\'ajout automatique de Squeletml \(maintenance\)./', $ligne))
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
		
			if ($fic2 = fopen($racine . '/.htaccess', 'w'))
			{
				fputs($fic2, implode("\n", $fichierHtaccess));
				fclose($fic2);
			}
		}
		elseif ($_POST['etat'] == 'enLigne' && $maintenanceDansHtaccess)
		{
			if ($fic2 = fopen($racine . '/.htaccess', 'r'))
			{
				$fichierHtaccess = array ();
				while (!feof($fic2))
				{
					$ligne = rtrim(fgets($fic2));
					if (preg_match('/^# Ajout automatique de Squeletml \(maintenance\). Ne pas modifier./', $ligne))
					{
						while (!preg_match('/^# Fin de l\'ajout automatique de Squeletml \(maintenance\)./', $ligne))
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
		
			if ($fic2 = fopen($racine . '/.htaccess', 'w'))
			{
				fputs($fic2, implode("\n", $fichierHtaccess));
				fclose($fic2);
			}
		}
	
		if (adminSiteEnMaintenance($racine . '/.htaccess'))
		{
			echo '<p>' . T_("Le site est en maintenance (hors ligne).") . "</p>\n";
			if ($ip = adminSiteEnMaintenanceIp($racine . '/.htaccess'))
			{
				echo '<p>' . sprintf(T_("L'IP %1\$s a accès au site hors ligne."), $ip) . "</p>\n";
			}
			else
			{
				echo '<p>' . T_("Aucune IP n'a accès au site hors ligne.") . "</p>\n";
			}
		}
		else
		{
			echo '<p>' . T_("Le site est en ligne.") . "</p>\n";
		}
	
		echo "</div><!-- /class=boite2 -->\n";
	}
	?>
</div><!-- /boiteMessages -->

<?php
########################################################################
##
## Formulaires
##
########################################################################
?>
<?php if (!$erreurAccesFichiers): ?>
	<div class="boite">
		<h2><?php echo T_("Lister les utilisateurs ayant accès à l'administration"); ?></h2>

		<p><?php echo T_("Vous pouvez afficher la liste des utilisateurs ayant accès à l'administration."); ?></p>

		<form action="<?php echo $action; ?>#messages" method="post">
			<div>
				<p><input type="submit" name="lister" value="<?php echo T_('Lister les utilisateurs'); ?>" /></p>
			</div>
		</form>
	</div><!-- /class=boite -->

	<div class="boite">
		<h2><?php echo T_("Gérer les droits d'accès à l'administration"); ?></h2>

		<p><?php echo T_("Vous pouvez ajouter ou supprimer un utilisateur en remplissant le formulaire ci-dessous. Vous pouvez également modifier le mot de passe d'un utilisateur existant."); ?></p>

		<form action="<?php echo $action; ?>#messages" method="post">
			<div>
				<p><label><?php echo T_("Nom:"); ?></label><br />
				<input type="text" name="nom" /></p>
			
				<p><label><?php echo T_("Mot de passe:"); ?></label><br />
				<input type="password" name="motDePasse" /></p>
			
				<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter'); ?>" /> <input type="submit" name="modifier" value="<?php echo T_('Modifier'); ?>" /> <input type="submit" name="supprimer" value="<?php echo T_('Supprimer'); ?>" /></p>
			</div>
		</form>
	</div><!-- /class=boite -->

	<div class="boite">
		<h2><?php echo T_("Mettre le site hors ligne pour maintenance"); ?></h2>

		<p><?php echo T_("Si le site est hors ligne, tous les internautes visitant une page du site seront redirigés vers la page de maintenance."); ?></p>

		<?php if (reecritureDurl(FALSE) == 'n'): ?>
			<p><strong><?php echo T_("Note: la réécriture d'URL (module <code>mod_rewrite</code> d'Apache) n'est pas activée sur votre serveur. La mise hors ligne du site ne fonctionnera pas."); ?></strong></p>
		<?php elseif (reecritureDurl(FALSE) == '?'): ?>
			<p><strong><?php echo T_("Note: impossible de savoir si la réécriture d'URL (module <code>mod_rewrite</code> d'Apache) est activée sur votre serveur. Si tel n'est pas le cas, la mise hors ligne du site ne fonctionnera pas."); ?></strong></p>
		<?php endif; ?>

		<form action="<?php echo $action; ?>#messages" method="post">
			<div>
				<p><?php echo T_("Le site est présentement:"); ?><br />
				<input type="radio" name="etat" value="enLigne" <?php if (!adminSiteEnMaintenance($racine . '/.htaccess')) {echo 'checked="checked"';} ?> /> <?php echo T_("en ligne."); ?><br />
				<input type="radio" name="etat" value="horsLigne" <?php if (adminSiteEnMaintenance($racine . '/.htaccess')) {echo 'checked="checked"';} ?> /> <?php echo T_("en maintenance (hors ligne)."); ?> <?php if (adminSiteEnMaintenance($racine . '/.htaccess')): ?>
					<?php if ($ip = adminSiteEnMaintenanceIp($racine . '/.htaccess')): ?>
						<?php echo sprintf(T_("L'IP %1\$s a accès au site hors ligne."), $ip); ?>
					<?php else: ?>
						<?php echo T_("Aucune IP n'a accès au site hors ligne."); ?>
					<?php endif; ?>
				<?php endif; ?>
				</p>
			
				<p><label><?php echo T_("IP ayant droit d'accès au site en maintenance (optionnel; laisser vide pour désactiver cette option):"); ?></label><br />
				<?php $ip = adminSiteEnMaintenanceIp($racine . '/.htaccess'); ?>
				<?php if ($ip): ?>
					<?php $valeurChampIp = $ip; ?>
				<?php else: ?>
					<?php $valeurChampIp = ipInternaute(); ?>
				<?php endif; ?>
				<input type="text" name="ip" value="<?php echo $valeurChampIp; ?>" /></p>
			
				<p><input type="submit" name="changerEtat" value="<?php echo T_('Changer l\'état du site'); ?>" /></p>
			</div>
		</form>
	</div><!-- /class=boite -->
<?php endif; ?>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
