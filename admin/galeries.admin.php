<?php
$baliseTitle = "Gestion des galeries";
include 'inc/premier.inc.php';

include '../init.inc.php';

if (isset($_POST['soumettre']))
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'] . '/';
	
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>La galerie {$_POST['id']} n'existe pas.</p>";
	}
	else
	{
		$fic = opendir($cheminGalerie) or die("<p class='erreur'>Erreur lors de l'ouverture du dossier $cheminGalerie.</p>");
		
		$listeFichiers = '';
		
		while($fichier = @readdir($fic))
		{
			if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
			{
				if ($_POST['exclusion'] != 'vignette' || !preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier))
				{
					$listeFichiers .= "grandeNom=$fichier\n__IMG__\n";
				}
			}
		}
		
		closedir($fic);
		
		echo '<h2>Résultat</h2>' . "\n" . '<textarea name="listeFichiers" readonly="readonly">' . $listeFichiers . '</textarea>' . "\n";
	}
}
?>

<h1>Gestion des galeries</h1>

<h2>Générer un fichier de configuration de base d'une galerie</h2>

<p>Pour générer automatiquement la liste des images d'une galerie, et ce sous la forme <code>grandeNom=grandeImage.extension</code>, remplissez le formulaire ci-dessous. Optionnellement, vous pouvez exclure des images du résultat.</p>

<form action="<? echo $action; ?>" method="post">
<div>
<p><label>Id de la galerie:</label><br />
<input type="text" name="id" /><br />
<input type=checkbox name="exclusion" value="vignette" checked="checked" /> <label>Ne pas tenir compte des fichiers terminant par <code>-vignette.extension</code></label></p>
<p><input type="submit" name="soumettre" value="Soumettre" /></p>
</div>
</form>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
