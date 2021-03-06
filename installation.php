<?php
$racine = dirname(__FILE__);
include_once $racine . '/inc/fonctions.inc.php';

if (file_exists($racine . '/init.inc.php'))
{
	include $racine . '/init.inc.php';
}

if (!isset($urlRacine))
{
	$urlRacine = urlParente();
}

$urlSansGet = url(FALSE);

if (!isset($_POST['ajouter']) && !file_exists($racine . '/site/inc/squeletml-est-installe.txt') && file_exists($racine . '/init.inc.php') && accesAdminEstProtege($racine))
{
	@touch($racine . '/site/inc/squeletml-est-installe.txt');
}

if (file_exists($racine . '/site/inc/squeletml-est-installe.txt'))
{
	header("Location: $urlRacine/", TRUE, 301);
}
else
{
	$serveurFreeFr = FALSE;
	
	if (preg_match('/\.free\.fr$/', php_uname()))
	{
		$serveurFreeFr = TRUE;
	}
	
	if ($serveurFreeFr && !file_exists($racine . '/.htaccess'))
	{
		// Serveurs de Free.fr: création d'un fichier `.htaccess` temporaire dans le but d'utiliser PHP 5 durant l'installation, le temps que le `.htaccess` permanent soit créé.
		if ($fic = @fopen($racine . '/.htaccess', 'w'))
		{
			fwrite($fic, 'php 1');
			fclose($fic);
			header("Location: $urlSansGet", TRUE, 302);
		}
	}
	
	$codeLangue = langue('navigateur', '');
	phpGettext('.', $codeLangue); // Nécessaire à la traduction.
	
	$passerAlEtape2 = FALSE;

	if (isset($_POST['ajouter']))
	{
		$passerAlEtape2 = TRUE;
	}
	
	$installationTerminee = FALSE;
	$messagesScript = '';
	
	########################################################################
	##
	## Création des fichiers.
	##
	########################################################################
	
	if (isset($_POST['creer']))
	{
		$passerAlEtape2 = TRUE;
		$urlSansServeur = url(FALSE, FALSE);
		$urlSansServeurRacine = preg_replace('#/[^/]+$#', '', $urlSansServeur);
		
		if (!isset($_POST['langues']))
		{
			$_POST['langues'] = array ();
		}
		
		if (!file_exists($racine . '/init.inc.php'))
		{
			if (file_exists($racine . '/modeles/init.inc.php.modele'))
			{
				if (@copy($racine . '/modeles/init.inc.php.modele', $racine . '/init.inc.php'))
				{
					$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>init.inc.php</code>', '<code>modeles/init.inc.php.modele</code>') . "</li>\n";
					$initIncPhp = @file_get_contents($racine . '/init.inc.php');
				
					if ($initIncPhp !== FALSE)
					{
						$initIncPhp = preg_replace('#^(\$urlRacine \= ")[^"]+(";)#m', '$1' . $urlRacine . '$2', $initIncPhp);
		
						if ($serveurFreeFr)
						{
							$initIncPhp = preg_replace('#^(\$serveurFreeFr \= )FALSE(;)#m', '$1TRUE$2', $initIncPhp);
						}
					
						list ($messagesScriptActiveLangues, $initIncPhp) = majLanguesActives($racine, $urlRacine, $_POST['langues'], $initIncPhp);
						$messagesScript .= $messagesScriptActiveLangues;
					
						if (strpos($messagesScriptActiveLangues, '<li class="erreur">') !== FALSE)
						{
							$passerAlEtape2 = FALSE;
						}
					
						if (@file_put_contents($racine . '/init.inc.php', $initIncPhp) === FALSE)
						{
							$passerAlEtape2 = FALSE;
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>init.inc.php</code>') . "</li>\n";
						}
					}
					else
					{
						$passerAlEtape2 = FALSE;
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>init.inc.php</code>') . "</li>\n";
					}
				}
				else
				{
					$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible, car le modèle n'existe pas. Veuillez vérifier que tous les fichiers de Squeletml ont été copiés sur le serveur."), '<code>init.inc.php</code>', '<code>modeles/init.inc.php.modele</code>') . "</li>\n";
				}
			}
			else
			{
				$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>init.inc.php</code>', '<code>modeles/init.inc.php.modele</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>init.inc.php</code>') . "</li>\n";
			$messagesScriptActiveLangues = majLanguesActives($racine, $urlRacine, $_POST['langues']);
			$messagesScript .= $messagesScriptActiveLangues;
			
			if (strpos($messagesScriptActiveLangues, '<li class="erreur">') !== FALSE)
			{
				$passerAlEtape2 = FALSE;
			}
		}
		
		if (!file_exists($racine . '/robots.txt'))
		{
			if (file_exists($racine . '/modeles/robots.txt.modele'))
			{
				if (@copy($racine . '/modeles/robots.txt.modele', $racine . '/robots.txt'))
				{
					$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>robots.txt</code>', '<code>modeles/robots.txt.modele</code>') . "</li>\n";

					if (!empty($urlSansServeurRacine))
					{
						$robotsTxt = @file_get_contents($racine . '/robots.txt');
			
						if ($robotsTxt !== FALSE)
						{
							$robotsTxt = preg_replace('#^(Disallow: )(/telecharger\.php)#m', '$1' . $urlSansServeurRacine . '$2', $robotsTxt);
							$robotsTxt = preg_replace("/\n{2,}/", "\n", $robotsTxt);
						
							if (@file_put_contents($racine . '/robots.txt', $robotsTxt) === FALSE)
							{
								$passerAlEtape2 = FALSE;
								$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>robots.txt</code>') . "</li>\n";
							}
						}
						else
						{
							$passerAlEtape2 = FALSE;
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>robots.txt</code>') . "</li>\n";
						}
					}
				}
				else
				{
					$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>robots.txt</code>', '<code>modeles/robots.txt.modele</code>') . "</li>\n";
				}
			}
			else
			{
				$passerAlEtape2 = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible, car le modèle n'existe pas. Veuillez vérifier que tous les fichiers de Squeletml ont été copiés sur le serveur."), '<code>robots.txt</code>', '<code>modeles/robots.txt.modele</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>robots.txt</code>') . "</li>\n";
		}

		if (!file_exists($racine . '/.htaccess') || ($serveurFreeFr && @file_get_contents($racine . '/.htaccess') == 'php 1'))
		{
			if ($serveurFreeFr && @file_get_contents($racine . '/.htaccess') == 'php 1')
			{
				// Suppression du `.htaccess` temporaire.
				@unlink($racine . '/.htaccess');
			}
			
			if ($serveurFreeFr)
			{
				$modeleHtaccess = 'modeles/.htaccess.free.fr.modele';
				$cheminModeleHtaccess = $racine . '/' . $modeleHtaccess;
			}
			else
			{
				$modeleHtaccess = 'modeles/.htaccess.modele';
				$cheminModeleHtaccess = $racine . '/' . $modeleHtaccess;
			}
			
			if (file_exists($cheminModeleHtaccess))
			{
				if (@copy($cheminModeleHtaccess, $racine . '/.htaccess'))
				{
					$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>.htaccess</code>', "<code>$modeleHtaccess</code>") . "</li>\n";
					if (!empty($urlSansServeurRacine))
					{
						$htaccess = @file_get_contents($racine . '/.htaccess');
			
						if ($htaccess !== FALSE)
						{
							$htaccess = preg_replace('#^(ErrorDocument 401 )(/401\.php)#m', '$1' . $urlSansServeurRacine . '$2', $htaccess);
							$htaccess = preg_replace('#^(ErrorDocument 404 )(/404\.php)#m', '$1' . $urlSansServeurRacine . '$2', $htaccess);
							$htaccess = preg_replace('#^(ErrorDocument 503 )(/maintenance\.php)#m', '$1' . $urlSansServeurRacine . '$2', $htaccess);

							if (@file_put_contents($racine . '/.htaccess', $htaccess) === FALSE)
							{
								$passerAlEtape2 = FALSE;
								$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>.htaccess</code>') . "</li>\n";
							}
						}
						else
						{
							$passerAlEtape2 = FALSE;
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Renseignement du fichier %1\$s impossible."), '<code>.htaccess</code>') . "</li>\n";
						}
					}
				}
				else
				{
					$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>.htaccess</code>', "<code>$modeleHtaccess</code>") . "</li>\n";
				}
			}
			else
			{
				$passerAlEtape2 = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible, car le modèle n'existe pas. Veuillez vérifier que tous les fichiers de Squeletml ont été copiés sur le serveur."), '<code>.htaccess</code>', "<code>$modeleHtaccess</code>") . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>.htaccess</code>') . "</li>\n";
		}
		
		if (!file_exists($racine . '/.acces'))
		{
			if ($fic = @fopen($racine . '/.acces', 'a+'))
			{
				$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s effectuée."), '<code>.acces</code>') . "</li>\n";
				fclose($fic);
			}
			else
			{
				$passerAlEtape2 = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>.acces</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>.acces</code>') . "</li>\n";
		}
		
		$dossiersAcreer = array (
			'site',
			'site/admin',
			'site/admin/cache',
			'site/admin/css',
			'site/admin/fichiers',
			'site/admin/inc',
			'site/admin/js',
			'site/admin/xhtml',
			'site/cache',
			'site/cache/htmlpurifier',
			'site/css',
			'site/fichiers',
			'site/fichiers/commentaires',
			'site/fichiers/galeries',
			'site/inc',
			'site/inc/commentaires',
			'site/js',
			'site/xhtml',
			'site/xhtml/en',
			'site/xhtml/fr',
		);
		
		foreach ($dossiersAcreer as $dossierAcreer)
		{
			if (!file_exists($racine . '/' . $dossierAcreer))
			{
				if (@mkdir($racine . '/' . $dossierAcreer, 0755, TRUE))
				{
					$messagesScript .= '<li>' . sprintf(T_("Création du dossier %1\$s effectuée."), '<code>' . securiseTexte($dossierAcreer) . '</code>') . "</li>\n";
				}
				else
				{
					$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du dossier %1\$s impossible."), '<code>' . securiseTexte($dossierAcreer) . '</code>') . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li>' . sprintf(T_("Le dossier %1\$s existe."), '<code>' . securiseTexte($dossierAcreer) . '</code>') . "</li>\n";
			}
		}
		
		$modelesDansSite = array (
			'modeles/site/admin/inc/config.inc.php.modele' => 'site/admin/inc/config.inc.php',
			'modeles/site/css/style.css.modele' => 'site/css/style.css',
			'modeles/site/inc/config.inc.php.modele' => 'site/inc/config.inc.php',
		);
		
		foreach ($modelesDansSite as $modeleDansSite => $destination)
		{
			if (!file_exists($racine . '/' . $destination))
			{
				if (file_exists($racine . '/' . $modeleDansSite))
				{
					if (@copy($racine . '/' . $modeleDansSite, $racine . '/' . $destination))
					{
						$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>' . securiseTexte($destination) . '</code>', '<code>' . securiseTexte($modeleDansSite) . '</code>') . "</li>\n";
					}
					else
					{
						$passerAlEtape2 = FALSE;
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>' . securiseTexte($destination) . '</code>', '<code>' . securiseTexte($modeleDansSite) . '</code>') . "</li>\n";
					}
				}
				else
				{
					$passerAlEtape2 = FALSE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible, car le modèle n'existe pas. Veuillez vérifier que tous les fichiers de Squeletml ont été copiés sur le serveur."), '<code>' . securiseTexte($destination) . '</code>', '<code>' . securiseTexte($modeleDansSite) . '</code>') . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>' . securiseTexte($destination) . '</code>') . "</li>\n";
			}
		}
	}
	
	########################################################################
	##
	## Ajout d'un utilisateur.
	##
	########################################################################
	
	$identifiant = '';
	
	if (!empty($_POST['identifiant']))
	{
		$identifiant = preg_replace('/[^A-Za-z0-9]/', '', $_POST['identifiant']);
	}
	
	if (isset($_POST['ajouter']) && file_exists($racine . '/init.inc.php') && file_exists($racine . '/.htaccess') && file_exists($racine . '/.acces'))
	{
		$installationTerminee = TRUE;
		$accesAvecUtilisateur = FALSE;
		
		if (($acces = @file_get_contents($racine . '/.acces')) && preg_match('/^[^:]+:/m', $acces))
		{
			$accesAvecUtilisateur = TRUE;
		}
		
		if (accesAdminEstProtege($racine))
		{
			$messagesScript .= '<li>' . T_("L'administration est déjà protégée.") . "</li>\n";
		}
		elseif ($accesAvecUtilisateur)
		{
			$accesDansHtaccess = accesDansHtaccess($racine, $serveurFreeFr);
			
			if (!empty($accesDansHtaccess))
			{
				$installationTerminee = FALSE;
				$messagesScript .= $accesDansHtaccess;
			}
			else
			{
				$messagesScript .= '<li>' . T_("Protection de l'administration effectuée.") . "</li>\n";
			}
		}
		elseif (empty($identifiant))
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . T_("Aucun identifiant spécifié.") . "</li>\n";
		}
		elseif (strlen($_POST['motDePasse']) < 8 || !preg_match('/\d+/', $_POST['motDePasse']) || !preg_match('/[A-Za-z]+/', $_POST['motDePasse']))
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . T_("Pour une question de sécurité, le mot de passe doit contenir au moins huit caractères ainsi qu'au moins un chiffre et une lettre.") . "</li>\n";
		}
		elseif ($_POST['motDePasse'] != $_POST['motDePasse2'])
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez confirmer correctement le mot de passe.") . "</li>\n";
		}
		elseif ($fic = @fopen($racine . '/.acces', 'a+'))
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

			while (!feof($fic))
			{
				$ligne = fgets($fic);

				if (strpos($ligne, $identifiant . ':') === 0)
				{
					$utilisateurAbsent = FALSE;
					break;
				}
			}

			if ($utilisateurAbsent)
			{
				fputs($fic, $acces);
				$messagesScript .= '<li>' . sprintf(T_("Ajout de l'utilisateur <em>%1\$s</em> effectué."), $identifiant) . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li>' . sprintf(T_("L'utilisateur <em>%1\$s</em> a déjà les droits."), $identifiant) . "</li>\n";
			}

			fclose($fic);

			$accesDansHtaccess = accesDansHtaccess($racine, $serveurFreeFr);
			
			if (!empty($accesDansHtaccess))
			{
				$installationTerminee = FALSE;
				$messagesScript .= $accesDansHtaccess;
			}
			else
			{
				$messagesScript .= '<li>' . T_("Protection de l'administration effectuée.") . "</li>\n";
			}
		}
		else
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), "<code>$racine/.acces</code>") . "</li>\n";
		}
	}
	
	########################################################################
	##
	## Formulaires.
	##
	########################################################################
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $codeLangue . '" lang="' . $codeLangue . '">' . "\n";
	echo "<head>\n";
	echo '<title>' . T_("Installation de Squeletml") . "</title>\n";
	echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />' . "\n";
	echo '<meta name="robots" content="noindex, nofollow, noarchive" />' . "\n";
	echo '<link rel="shortcut icon" type="images/x-icon" href="' . $urlRacine . '/fichiers/favicon.png" />' . "\n";
	echo '<style type="text/css">' . "\n";
	echo "html, body { margin: 0px; padding: 0px;}\n";
	echo "body {font-family: Arial, Helvetica, \"Liberation Sans\", FreeSans, sans-serif; font-size: 0.96em; margin: 0}\n";
	echo "#page {width: 50%; margin: 0px auto; padding: 5px; border: 1px solid #f2f2f2; border-top: 0px;}\n";
	echo "h1 {margin-top: 0px; font-size: 1.9em;}\n";
	echo "h2 {font-size: 1.6em;}\n";
	echo "h1, h2 {color: #005959;}\n";
	echo "a:visited {color: #4c177d;}\n";
	echo "a:hover {text-decoration: none;}\n";
	echo "code {color: #0000e2;}\n";
	echo "ul {padding: 0px; margin-left: 20px;}\n";
	echo "ul {list-style-type: circle;}\n";
	echo ".blocMessagesScript ul {margin-top: 0px; margin-bottom: 0px;}\n";
	echo "li {margin-top: 5px; margin-bottom: 5px;}\n";
	echo ".blocMessagesScript {padding: 10px; background-color: #f5f5f5;}\n";
	echo ".erreur {color: #630000;}\n";
	echo "</style>\n";
	echo "</head>\n";
	echo "<body>\n";
	echo '<div id="page">' . "\n";
	echo '<h1>' . T_("Installation de Squeletml") . "</h1>\n";

	// Étape 1.
	
	if (!$passerAlEtape2)
	{
		echo '<h2>' . sprintf(T_("Étape %1\$s de %2\$s:"), 1, 2) . "\n" . T_("création de fichiers") . "</h2>\n";
		
		if (!empty($messagesScript))
		{
			echo "<div class=\"blocMessagesScript\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			
			echo '<p class="erreur">' . sprintf(T_("Vérifiez que le dossier racine de Squeletml est bien accessible en écriture. Ensuite, <a href=\"%1\$s\">visitez de nouveau la présente page</a>."), $urlSansGet) . "</p>\n";
			echo "</div><!-- /.blocMessagesScript -->\n";
		}
		
		echo '<form action="' . $urlSansGet . '" method="post">' . "\n";
		echo "<div>\n";
		echo '<p><label for="inputLangues">' . T_("Langues à activer dans le menu:") . "</label><br />\n" . '<select id="inputLangues" name="langues[]" multiple="multiple">' . "\n";
		
		if (!isset($languesAccueil))
		{
			if (!isset($initIncPhp))
			{
				$initIncPhp = FALSE;
				
				if (file_exists($racine . '/init.inc.php'))
				{
					$initIncPhp = @file_get_contents($racine . '/init.inc.php');
				}
				elseif (file_exists($racine . '/modeles/init.inc.php.modele'))
				{
					$initIncPhp = @file_get_contents($racine . '/modeles/init.inc.php.modele');
				}
			}
			
			if ($initIncPhp !== FALSE)
			{
				preg_match_all('/^\s*(#|\/\/)?\s*\$accueil\[\'([a-z]{2})\'\]\s*=/m', $initIncPhp, $resultatAccueil);
				$languesAccueil = $resultatAccueil[2];
			}
		}
		
		if (isset($languesAccueil))
		{
			foreach ($languesAccueil as $langueAccueil)
			{
				echo "<option value=\"$langueAccueil\"";
				
				if ((isset($accueil) && isset($accueil[$langueAccueil])) || (isset($_POST['langues']) && in_array($langueAccueil, $_POST['langues'])) || (!isset($accueil) && !isset($_POST['langues'])))
				{
					echo ' selected="selected"';
				}
				
				echo ">$langueAccueil</option>\n";
			}
		}
		
		echo '</select></p>' . "\n";
		
		echo '<p><input type="submit" name="creer" value="' . T_("Créer les fichiers") . '" /></p>' . "\n";
		echo "</div>\n";
		echo "</form>\n";
	}
	// Étape 2.
	elseif (!$installationTerminee)
	{
		if (isset($_POST['creer']) && !empty($messagesScript))
		{
			echo "<div class=\"blocMessagesScript\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			
			echo '<p><strong>' . sprintf(T_("Étape %1\$s terminée."), 1) . "</strong></p>\n";
			echo "</div><!-- /.blocMessagesScript -->\n";
		}
		
		echo '<h2>' . sprintf(T_("Étape %1\$s de %2\$s:"), 2, 2) . "\n" .  T_("ajout d'un utilisateur") . "</h2>\n";

		if (!isset($_POST['creer']) && !empty($messagesScript))
		{
			echo "<div class=\"blocMessagesScript\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			echo "</div><!-- /.blocMessagesScript -->\n";
		}
		
		echo '<form action="' . $urlSansGet . '" method="post">' . "\n";
		echo "<div>\n";
		
		if (file_exists($racine . '/.acces') && ($acces = @file_get_contents($racine . '/.acces')) && preg_match('/^[^:]+:/m', $acces))
		{
			echo '<p>' . T_("Un utilisateur est déjà présent. Veuillez activer la protection de l'administration.") . "</p>\n";
			
			echo '<p><input type="submit" name="ajouter" value="' . T_("Protéger l'administration") . '" /></p>' . "\n";
		}
		else
		{
			echo '<p>' . T_("Veuillez ajouter un utilisateur pour restreindre l'accès à la section d'administration de votre site:") . "</p>\n";
		
			echo '<p><label for="inputIdentifiant">' . T_("Identifiant:") . "</label><br />\n" . '<input id="inputIdentifiant" type="text" name="identifiant" ';

			if (!empty($identifiant))
			{
				echo 'value="' . $identifiant . '" ';
			}

			echo '/></p>' . "\n";

			echo '<p><label for="inputMotDePasse">' . T_("Mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse" type="password" name="motDePasse" /></p>' . "\n";

			echo '<p><label for="inputMotDePasse2">' . T_("Confirmer le mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse2" type="password" name="motDePasse2" /></p>' . "\n";
	
			echo '<p><input type="submit" name="ajouter" value="' . T_("Ajouter l'utilisateur") . '" /></p>' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	// Installation terminée.
	else
	{
		$erreurFichierSqueletmlEstInstalle = FALSE;
		
		if (!file_exists($racine . '/site/inc/squeletml-est-installe.txt'))
		{
			if (!@touch($racine . '/site/inc/squeletml-est-installe.txt'))
			{
				$erreurFichierSqueletmlEstInstalle = TRUE;
			}
		}

		if (isset($_POST['ajouter']) && !empty($messagesScript))
		{
			echo "<div class=\"blocMessagesScript\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";

			echo '<p><strong>' . sprintf(T_("Étape %1\$s terminée."), 2) . "</strong></p>\n";
			echo "</div><!-- /.blocMessagesScript -->\n";
		}
		
		echo '<h2>' . T_("Installation terminée") . "</h2>\n";

		if ($erreurFichierSqueletmlEstInstalle)
		{
			echo '<p class="erreur">' . sprintf(T_("L'installation est terminée, mais le fichier %1\$s n'a pu être créé. Vérifiez que son dossier parent est bien accessible en écriture, et crééez le fichier manuellement (laisser le contenu vide). Ce fichier est important pour informer Squeletml de ne plus lancer l'installation automatisée."), '<code>site/inc/squeletml-est-installe.txt</code>') . "</p>\n";
		}
		
		echo '<p>' . T_("La suite vous appartient:") . "</p>\n";
		
		echo "<ul>\n";
		echo '<li>' . sprintf(T_("<a href=\"%1\$s\">Visiter la page d'accueil de votre site.</a>"), "$urlRacine/") . "</li>\n";
		echo '<li>' . sprintf(T_("<a href=\"%1\$s\">Administrer Squeletml.</a>"), "$urlRacine/$dossierAdmin/") . "</li>\n";
		echo "</ul>\n";
	}
	
	echo "</div><!-- /#page -->\n";
	echo "</body>\n";
	echo '</html>';
}
?>
