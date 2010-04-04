<?php
$racine = dirname(__FILE__);
include_once $racine . '/inc/fonctions.inc.php';
$urlParente = urlParente();

if (file_exists($racine . '/site/inc/squeletml-est-installe.txt'))
{
	header("Location: $urlParente/", TRUE, 301);
}
else
{
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
		$urlSansServeurRacine = preg_replace('|/[^/]+$|', '', $urlSansServeur);
		$serveurFreeFr = FALSE;
		
		if (isset($_POST['freeFr']))
		{
			$serveurFreeFr = TRUE;
		}
		
		if (!file_exists($racine . '/init.inc.php') && file_exists($racine . '/init.inc.php.defaut'))
		{
			if (@copy($racine . '/init.inc.php.defaut', $racine . '/init.inc.php'))
			{
				$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>init.inc.php</code>', '<code>init.inc.php.defaut</code>') . "</li>\n";
				$initIncPhp = @file_get_contents($racine . '/init.inc.php');
		
				if ($initIncPhp !== FALSE)
				{
					$initIncPhp = preg_replace('|^(\$urlRacine \= ")[^"]+(";)|m', '$1' . $urlParente . '$2', $initIncPhp);
		
					if ($serveurFreeFr)
					{
						$initIncPhp = preg_replace('|^(\$serveurFreeFr \= )FALSE(;)|m', '$1TRUE$2', $initIncPhp);
					}

					if (isset($_POST['langues']))
					{
						if (!in_array('fr', $_POST['langues']) && !in_array('en', $_POST['langues']))
						{
							$passerAlEtape2 = FALSE;
						$messagesScript .= '<li class="erreur">' . T_("Veuillez choisir au moins une langue.") . "</li>\n";
						}
						elseif (!in_array('fr', $_POST['langues']))
						{
							$initIncPhp = preg_replace('|^(' . preg_quote('$accueil[\'fr\']') . ')|m', '#$1', $initIncPhp);
						}
						elseif (!in_array('en', $_POST['langues']))
						{
							$initIncPhp = preg_replace('|^(' . preg_quote('$accueil[\'en\']') . ')|m', '#$1', $initIncPhp);
						}
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>init.inc.php</code>', '<code>init.inc.php.defaut</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>init.inc.php</code>') . "</li>\n";
		}
	
		if (!file_exists($racine . '/robots.txt') && file_exists($racine . '/robots.txt.defaut'))
		{
			if (@copy($racine . '/robots.txt.defaut', $racine . '/robots.txt'))
			{
				$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>robots.txt</code>', '<code>robots.txt.defaut</code>') . "</li>\n";

				if (!empty($urlSansServeurRacine))
				{
					$robotsTxt = @file_get_contents($racine . '/robots.txt');
			
					if ($robotsTxt !== FALSE)
					{
						$robotsTxt = preg_replace('|^(Disallow\: )(/telecharger\.php)|m', '$1' . $urlSansServeurRacine . '$2', $robotsTxt);
						
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>robots.txt</code>', '<code>robots.txt.defaut</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li>' . sprintf(T_("Le fichier %1\$s existe."), '<code>robots.txt</code>') . "</li>\n";
		}

		if (!file_exists($racine . '/.htaccess') && file_exists($racine . '/.htaccess.defaut'))
		{
			if (@copy($racine . '/.htaccess.defaut', $racine . '/.htaccess'))
			{
				$messagesScript .= '<li>' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s effectuée."), '<code>.htaccess</code>', '<code>.htaccess.defaut</code>') . "</li>\n";
				if (!empty($urlSansServeurRacine) || $serveurFreeFr)
				{
					$htaccess = @file_get_contents($racine . '/.htaccess');
			
					if ($htaccess !== FALSE)
					{
						$htaccess = preg_replace('|^(ErrorDocument 401 )(/401\.php)|m', '$1' . $urlSansServeurRacine . '$2', $htaccess);
						$htaccess = preg_replace('|^(ErrorDocument 404 )(/404\.php)|m', '$1' . $urlSansServeurRacine . '$2', $htaccess);
						$htaccess = preg_replace('|^(ErrorDocument 503 )(/maintenance\.php)|m', '$1' . $urlSansServeurRacine . '$2', $htaccess);

						if ($serveurFreeFr)
						{
							$htaccess = preg_replace('|^#(php 1)|m', '$1', $htaccess);
						}
						
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s à partir du modèle %2\$s impossible."), '<code>.htaccess</code>', '<code>.htaccess.defaut</code>') . "</li>\n";
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
	}
	
	########################################################################
	##
	## Ajout d'un utilisateur.
	##
	########################################################################
	
	if (isset($_POST['ajouter']) && file_exists($racine . '/init.inc.php') && file_exists($racine . '/.htaccess') && file_exists($racine . '/.acces'))
	{
		include_once $racine . '/init.inc.php';
		$installationTerminee = TRUE;
		
		if (empty($_POST['identifiant']))
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . T_("Aucun identifiant spécifié.") . "</li>\n";
		}
		elseif ($_POST['motDePasse'] != $_POST['motDePasse2'])
		{
			$installationTerminee = FALSE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez confirmer correctement le mot de passe.") . "</li>\n";
		}
		else
		{
			if ($fic = @fopen($racine . '/.acces', 'a+'))
			{
				if (stristr(PHP_OS, 'win') || $serveurFreeFr)
				{
					$acces = securiseTexte($_POST['identifiant']) . ':' . securiseTexte($_POST['motDePasse']) . "\n";
				}
				else
				{
					$acces = securiseTexte($_POST['identifiant']) . ':' . crypt(securiseTexte($_POST['motDePasse'])) . "\n";
				} 

				// On vérifie si l'utilisateur est déjà présent.
				$utilisateurAbsent = TRUE;

				while (!feof($fic))
				{
					$ligne = fgets($fic);
	
					if (preg_match('/^' . securiseTexte($_POST['identifiant']) . ':/', $ligne))
					{
						$utilisateurAbsent = FALSE;
						break;
					}
				}

				if ($utilisateurAbsent)
				{
					fputs($fic, $acces);
					$messagesScript .= '<li>' . sprintf(T_("Ajout de l'utilisateur <em>%1\$s</em> effectué."), securiseTexte($_POST['identifiant'])) . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("L'utilisateur <em>%1\$s</em> a déjà les droits."), securiseTexte($_POST['identifiant'])) . "</li>\n";
				}
		
				fclose($fic);

				$accesDansHtaccess = accesDansHtaccess($racine, $serveurFreeFr);

				if (!empty($accesDansHtaccess))
				{
					$installationTerminee = FALSE;
					$messagesScript .= $accesDansHtaccess;
				}
			}
			else
			{
				$installationTerminee = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), "<code>$racine/.acces</code>") . "</li>\n";
			}
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
	echo '<link rel="shortcut icon" type="images/x-icon" href="' . $urlParente . '/fichiers/favicon.png" />' . "\n";
	echo '<style type="text/css">' . "\n";
	echo "body {font-family: Arial, Helvetica, \"Liberation Sans\", FreeSans, sans-serif; font-size: 0.96em;}\n";
	echo "#page {width: 50%; margin: 10px auto; background-color: #F7F7F7;}\n";
	echo "#page, .messages {border: 2px solid #EBEBEB; padding: 10px; -moz-border-radius: 8px; /* Gecko. */ -webkit-border-radius: 8px; /* Webkit. */ border-radius: 8px; /* CSS 3. */}\n";
	echo "h1 {margin-top: 0px; font-size: 1.9em;}\n";
	echo "h2 {font-size: 1.6em;}\n";
	echo "h1, h2 {color: #007070;}\n";
	echo "a:visited {color: #4C177D;}\n";
	echo "a:hover {text-decoration: none;}\n";
	echo "code {color: #0000E2;}\n";
	echo "ul {padding: 0px; margin-left: 20px;}\n";
	echo "ul {list-style-type: circle;}\n";
	echo "li {margin-top: 5px; margin-bottom: 5px;}\n";
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
			echo "<div class=\"messages\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			
			echo '<p class="erreur">' . sprintf(T_("Vérifiez que le dossier racine de Squeletml est bien accessible en écriture. Ensuite, <a href=\"%1\$s\">visitez de nouveau la présente page</a>."), url()) . "</p>\n";
			echo "</div><!-- /.messages -->\n";
		}
		
		echo '<form action="' . url() . '" method="post">' . "\n";
		echo "<div>\n";
		echo '<p><label for="inputLangues">' . T_("Langues à activer dans le menu:") . "</label><br />\n" . '<select id="inputLangues" name="langues[]" multiple="multiple">' . "\n";
	
		$selectedFr = ' selected="selected"';
		$selectedEn = ' selected="selected"';
	
		if (isset($_POST['langues']))
		{
			if (!in_array('fr', $_POST['langues']))
			{
				$selectedFr = '';
			}

			if (!in_array('en', $_POST['langues']))
			{
				$selectedEn = '';
			}
		}
		
		echo "<option value=\"fr\"$selectedFr>fr</option>\n";
		echo "<option value=\"en\"$selectedEn>en</option>\n";
		echo '</select></p>' . "\n";

		echo '<p><input id="inputFreeFr" type="checkbox" name="freeFr" ';
	
		if (isset($_POST['freeFr']))
		{
			echo 'checked="checked" ';
		}
	
		echo '/> <label for="inputFreeFr">' . T_("J'héberge mon site sur un serveur de Free.fr.") . "</label></p>\n";

		echo '<p><input type="submit" name="creer" value="' . T_("Créer les fichiers") . '" /></p>' . "\n";
		echo "</div>\n";
		echo "</form>\n";
	}
	// Étape 2.
	elseif (!$installationTerminee)
	{
		if (isset($_POST['creer']) && !empty($messagesScript))
		{
			echo "<div class=\"messages\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			
			echo '<p><strong>' . sprintf(T_("Étape %1\$s terminée."), 1) . "</strong></p>\n";
			echo "</div><!-- /.messages -->\n";
		}
		
		echo '<h2>' . sprintf(T_("Étape %1\$s de %2\$s:"), 2, 2) . "\n" .  T_("ajout d'un utilisateur") . "</h2>\n";

		if (!isset($_POST['creer']) && !empty($messagesScript))
		{
			echo "<div class=\"messages\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";
			echo "</div><!-- /.messages -->\n";
		}

		echo '<p>' . T_("Veuillez ajouter un utilisateur pour restreindre l'accès à la section d'administration de votre site:") . "</p>\n";

		echo '<form action="' . url() . '" method="post">' . "\n";
		echo "<div>\n";
		echo '<p><label for="inputIdentifiant">' . T_("Identifiant:") . "</label><br />\n" . '<input id="inputIdentifiant" type="text" name="identifiant" ';

		if (!empty($_POST['identifiant']))
		{
			echo 'value="' . securiseTexte($_POST['identifiant']) . '" ';
		}

		echo '/></p>' . "\n";

		echo '<p><label for="inputMotDePasse">' . T_("Mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse" type="password" name="motDePasse" /></p>' . "\n";

		echo '<p><label for="inputMotDePasse2">' . T_("Confirmer le mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse2" type="password" name="motDePasse2" /></p>' . "\n";
	
		echo '<p><input type="submit" name="ajouter" value="' . T_("Ajouter l'utilisateur") . '" /></p>' . "\n";
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
			echo "<div class=\"messages\">\n";
			echo "<ul>\n";
			echo $messagesScript;
			echo "</ul>\n";

			echo '<p><strong>' . sprintf(T_("Étape %1\$s terminée."), 2) . "</strong></p>\n";
			echo "</div><!-- /.messages -->\n";
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