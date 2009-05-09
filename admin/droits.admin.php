<?php
$baliseTitle = "Gestion des droits d'accès";
include 'inc/premier.inc.php';

if (isset($_POST['ajouter']) || isset($_POST['modifier']) || isset($_POST['supprimer']))
{
	if (!file_exists($racine . '/.acces'))
	{
		$htaccess = '';
		$htaccess .= "\n";
		$htaccess .= '<FilesMatch "\.admin\.php$">' . "\n";
		$htaccess .= "\tAuthType Basic\n";
		$htaccess .= "\tAuthName \"Zone d'identification\"\n";
		$htaccess .= "\tAuthUserFile $racine/.acces\n";
		$htaccess .= "\tRequire valid-user\n";
		$htaccess .= "</FilesMatch>";
		
		$fic = fopen($racine . '/.htaccess', 'a+');
		fputs($fic, $htaccess);
		fclose($fic);
	}
	
	// Ajout d'un utilisateur
	if (isset($_POST['ajouter']))
	{
		$fic2 = fopen($racine . '/.acces', 'a+');
		
		$acces = formateTexte($_POST['nom']) . ':' . crypt(formateTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n"; 
		
		// On vérifie si l'utilisateur est déjà présent
		$utilisateurAbsent = TRUE;
		while (!feof($fic2))
		{
			$ligne = fgets($fic2);
			if (preg_match('/^' . formateTexte($_POST['nom']) . ':/', $ligne))
			{
				$utilisateurAbsent = FALSE;
				break;
			}
		}
		
		if ($utilisateurAbsent)
		{
			fputs($fic2, $acces);
			echo '<p class="succes">Utilisateur <em>' . formateTexte($_POST['nom']) . '</em> ajouté.</p>';
		}
		else
		{
			echo '<p class="erreur">L\'utilisateur <em>' . formateTexte($_POST['nom']) . '</em> a déjà les droits.</p>';
		}
		
		fclose($fic2);
	}
	
	// Modification d'un utilisateur
	elseif (isset($_POST['modifier']))
	{
		$fic2 = fopen($racine . '/.acces', 'r');
		$utilisateurs = array ();
		// On vérifie si l'utilisateur est déjà présent
		$utilisateurAbsent = TRUE;
		
		while (!feof($fic2))
		{
			$ligne = fgets($fic2);
			if (preg_match('/^' . formateTexte($_POST['nom']) . ':/', $ligne))
			{
				$utilisateurAbsent = FALSE;
				$ligne = formateTexte($_POST['nom']) . ':' . crypt(formateTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n";
			}
			
			$utilisateurs[] = $ligne;
		}
		
		fclose($fic2);
		$fic2 = fopen($racine . '/.acces', 'w');
		fputs($fic2, implode("\n", $utilisateurs));
		fclose($fic2);
		
		if ($utilisateurAbsent)
		{
			echo '<p class="erreur">L\'utilisateur <em>' . formateTexte($_POST['nom']) . '</em> n\'a pas les droits. Son mot de passe ne peut donc pas être modifié.</p>';
		}
		else
		{
			echo '<p class="succes">Mot de passe de l\'utilisateur <em>' . formateTexte($_POST['nom']) . '</em> modifié.</p>';
		}
	}
	
		// Suppression d'un utilisateur
	elseif (isset($_POST['supprimer']))
	{
		$fic2 = fopen($racine . '/.acces', 'r');
		$utilisateurs = array ();
		// On vérifie si l'utilisateur est déjà présent
		$utilisateurAbsent = TRUE;
		
		while (!feof($fic2))
		{
			$ligne = fgets($fic2);
			if (preg_match('/^' . formateTexte($_POST['nom']) . ':/', $ligne))
			{
				$utilisateurAbsent = FALSE;
			}
			else
			{
				$utilisateurs[] = $ligne;
			}
		}
		
		fclose($fic2);
		$fic2 = fopen($racine . '/.acces', 'w');
		fputs($fic2, implode("\n", $utilisateurs));
		fclose($fic2);
		
		if ($utilisateurAbsent)
		{
			echo '<p class="erreur">L\'utilisateur <em>' . formateTexte($_POST['nom']) . '</em> n\'a pas les droits. Il ne peut donc pas être supprimé.</p>';
		}
		else
		{
			echo '<p class="succes">Utilisateur <em>' . formateTexte($_POST['nom']) . '</em> supprimé.</p>';
		}
	}
}
?>

<h1>Gestion des droits d'accès</h1>

<h2>Utilisateurs</h2>

<p>Voici les utilisateurs ayant accès à l'administration:</p>

<ul>
<?php
$fic3 = @fopen($racine . '/.acces', 'r');
$i = 0;
if ($fic3)
{
	while (!feof($fic3))
	{
		$ligne = fgets($fic3);
		if (preg_match('/^[^:]:/', $ligne))
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
	echo '<li>Aucun</li>';
}
?>
</ul>

<h2>Gestion</h2>

<p>Vous pouvez ajouter ou supprimer un utilisateur en remplissant le formulaire ci-dessous. Vous pouvez également modifier le mot de passe d'un utilisateur existant.</p>

<form action="<? echo $action; ?>" method="post">
<div>
<p><label>Nom:</label><br />
<input type="text" name="nom" /></p>
<p><label>Mot de passe:</label><br />
<input type="text" name="motDePasse" /></p>
<p><input type="submit" name="ajouter" value="Ajouter" /> <input type="submit" name="modifier" value="Modifier" /> <input type="submit" name="supprimer" value="Supprimer" /></p>
</div>
</form>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
