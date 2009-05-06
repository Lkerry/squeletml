<?php
$baliseTitle = "Gestion des droits d'accès";
include 'inc/premier.inc.php';

include '../init.inc.php';

if (isset($_POST['soumettre']))
{
	$chemin = $racine . dirname($_SERVER['PHP_SELF']) . '/.acces';
	
	$htaccess = "AuthType Basic\n";
	$htaccess .= "AuthName \"Zone d'identification\"\n";
	$htaccess .= "AuthUserFile $chemin\n";
	$htaccess .= "require valid-user\n";
	
	$acces = formateTexte($_POST['nom']) . ':' . crypt(formateTexte($_POST['motDePasse']), CRYPT_STD_DES) . "\n"; 
	
	$fic = fopen('.htaccess', 'w');
	$fic2 = fopen('.acces', 'a+');
	
	fputs($fic, $htaccess);
	fputs($fic2, $acces);
	fclose($fic);
	fclose($fic2);
	
	echo '<p class="succes">Utilisateur ajouté.</p>';
}
?>

<h1>Gestion des droits d'accès</h1>

<p>Vous pouvez ajouter un utilisateur ayant les droits d'accès à l'administration en remplissant le formulaire ci-dessous.</p>

<form action="<? echo $action; ?>" method="post">
<div>
<p><label>Nom:</label><br />
<input type="text" name="nom" /></p>
<p><label>Mot de passe:</label><br />
<input type="text" name="motDePasse" /></p>
<p><input type="submit" name="soumettre" value="Soumettre" /></p>
</div>
</form>

<?php include 'inc/dernier.inc.php'; ?>
