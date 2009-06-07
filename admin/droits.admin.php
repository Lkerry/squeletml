<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Gestion des droits d'accès");
include 'inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des droits d'accès"); ?></h1>

<div id="boiteMessages" class="boite">
<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

<?php
// Début des tests pour vérifier l'accessibilité des fichiers nécessaires au script
if ($ficTest = fopen($racine . '/.htaccess', 'a+'))
{
	fclose($ficTest);
}
else
{
	echo '<p>' . sprintf(T_('Impossible d\'ouvrir le fichier %1$s en lecture et en écriture. Veuillez lui assigner les bons droits et revisiter la présente page.'), $racine . '/.htaccess') . '</p>';
	exit(1);
}

if ($ficTest = fopen($racine . '/.acces', 'a+'))
{
	fclose($ficTest);
}
else
{
	echo '<p>' . sprintf(T_('Impossible d\'ouvrir le fichier %1$s en lecture et en écriture. Veuillez lui assigner les bons droits et revisiter la présente page.'), $racine . '/.acces') . '</p>';
	exit(1);
}
// Fin des tests

if (isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))
{
	$lienAccesDansHtaccess = FALSE;
	if ($fic = fopen($racine . '/.htaccess', 'r'))
	{
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			if (preg_match('/^# Ajout automatique de Squeletml. Ne pas modifier./', $ligne))
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
		$htaccess .= "# Ajout automatique de Squeletml. Ne pas modifier.\n";
		$htaccess .= '<FilesMatch "\.admin\.php$">' . "\n";
		$htaccess .= "\tAuthType Basic\n";
		$htaccess .= "\tAuthName \"Zone d'identification\"\n";
		$htaccess .= "\tAuthUserFile $racine/.acces\n";
		$htaccess .= "\tRequire valid-user\n";
		$htaccess .= "</FilesMatch>\n";
		$htaccess .= "# Fin de l'ajout automatique de Squeletml.\n";
		
		if ($fic = fopen($racine . '/.htaccess', 'a+'))
		{
			fputs($fic, $htaccess);
			fclose($fic);
		}
	}
	
	// Ajout d'un utilisateur
	if (isset($_POST['ajouter']))
	{
		if ($fic2 = fopen($racine . '/.acces', 'a+'))
		{
			$acces = adminFormateTexte($_POST['nom']) . ':' . crypt(adminFormateTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n"; 
			
			// On vérifie si l'utilisateur est déjà présent
			$utilisateurAbsent = TRUE;
			while (!feof($fic2))
			{
				$ligne = fgets($fic2);
				if (preg_match('/^' . adminFormateTexte($_POST['nom']) . ':/', $ligne))
				{
					$utilisateurAbsent = FALSE;
					break;
				}
			}
		
			if ($utilisateurAbsent)
			{
				fputs($fic2, $acces);
				echo '<p class="succes">' . sprintf(T_('Utilisateur <em>%1$s</em> ajouté.'), adminFormateTexte($_POST['nom'])) . '</p>';
			}
			else
			{
				echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> a déjà les droits.'), adminFormateTexte($_POST['nom'])) . '</p>';
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
				if (preg_match('/^' . adminFormateTexte($_POST['nom']) . ':/', $ligne))
				{
					$utilisateurAbsent = FALSE;
					$ligne = adminFormateTexte($_POST['nom']) . ':' . crypt(adminFormateTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n";
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
			echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> n\'a pas les droits. Son mot de passe ne peut donc pas être modifié.'), adminFormateTexte($_POST['nom'])) . '</p>';
		}
		else
		{
			echo '<p class="succes">' . sprintf(T_('Mot de passe de l\'utilisateur <em>%1$s</em> modifié.'), adminFormateTexte($_POST['nom'])) . '</p>';
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
				if (preg_match('/^' . adminFormateTexte($_POST['nom']) . ':/', $ligne))
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
		
		// S'il n'y a plus d'utilisateurs dans le fichier `.acces`, on supprime l'authentification dans le .htaccess.
		// Note: auparavant, je faisais:
		// clearstatcache();
		// if (filesize($racine . '/.acces') == 0) {...}
		// mais des lignes vides faisaient en sorte que la taille du fichier n'était pas à 0. Maintenant je fais simplement regarder s'il y a un : dans le fichier, ce qui signifierait qu'il y a au moins un utilisateur.
		$accesVide = TRUE;
		if ($fic = fopen($racine . '/.acces', 'r'))
		{
			while (!feof($fic))
			{
				$ligne = rtrim(fgets($fic));
				if (strstr($ligne, ':'))
				{
					$accesVide = FALSE;
					break;
				}
			}
			fclose($fic);
		}
		
		if ($accesVide)
		{
			if ($fic2 = fopen($racine . '/.htaccess', 'r'))
			{
				$fichierHtaccess = array ();
				while (!feof($fic2))
				{
					$ligne = rtrim(fgets($fic2));
					if (preg_match('/^# Ajout automatique de Squeletml. Ne pas modifier./', $ligne))
					{
						while (!preg_match('/^# Fin de l\'ajout automatique de Squeletml./', $ligne))
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
			echo '<p class="erreur">' . sprintf(T_('L\'utilisateur <em>%1$s</em> n\'a pas les droits. Il ne peut donc pas être supprimé.'), adminFormateTexte($_POST['nom'])) . '</p>';
		}
		else
		{
			echo '<p class="succes">' . sprintf(T_('Utilisateur <em>%1$s</em> supprimé.'), adminFormateTexte($_POST['nom'])) . '</p>';
		}
	}
}
?>

<h3><?php echo T_("Utilisateurs"); ?></h3>

<p><?php echo T_("Voici les utilisateurs ayant accès à l'administration:"); ?></p>

<ul>
<?php

$i = 0;
if ($fic3 = fopen($racine . '/.acces', 'r'))
{
	while (!feof($fic3))
	{
		$ligne = fgets($fic3);
		if (preg_match('/^[^:]+:/', $ligne))
		{
			list($utilisateur, $motDePasse) = split(':', $ligne, 2);
			echo '<li>' . $utilisateur . '</li>';
			$i++;
		}
	}

	fclose($fic3);
}

if (!$i)
{
	echo '<li>' . T_("Aucun") . '</li>';
}
?>
</ul>
</div><!-- /boiteMessages -->

<div class="boite">
<h2><?php echo T_("Gestion"); ?></h2>

<p><?php echo T_("Vous pouvez ajouter ou supprimer un utilisateur en remplissant le formulaire ci-dessous. Vous pouvez également modifier le mot de passe d'un utilisateur existant."); ?></p>

<form action="<? echo $action; ?>#messages" method="post">
<div>
<p><label><?php echo T_("Nom"); ?>:</label><br />
<input type="text" name="nom" /></p>
<p><label><?php echo T_("Mot de passe"); ?>:</label><br />
<input type="password" name="motDePasse" /></p>
<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter'); ?>" /> <input type="submit" name="modifier" value="<?php echo T_('Modifier'); ?>" /> <input type="submit" name="supprimer" value="<?php echo T_('Supprimer'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
