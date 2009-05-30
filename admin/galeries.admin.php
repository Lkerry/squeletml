<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Gestion des galeries");
include 'inc/premier.inc.php';

include '../init.inc.php';

if (isset($_POST['soumettre']))
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'] . '/';
	
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$fic = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
		
		if ($_POST['exclureSiExiste'] == 'existe')
		{
			if (file_exists($racine . '/site/inc/galerie-' . $_POST['id'] . '.txt'))
			{
				$galerie = construitTableauGalerie($racine . '/site/inc/galerie-' . $_POST['id'] . '.txt');
			}
		}
		
		$listeFichiers = '';
		
		while($fichier = @readdir($fic))
		{
			if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
			{
				if (($_POST['exclureVignette'] != 'vignette' || !preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier)) && ($_POST['exclureOrig'] != 'orig' || !preg_match('/-orig\.[[:alpha:]]{3,4}$/', $fichier)))
				{
					if ($_POST['exclureSiExiste'] != 'existe' || !in_array_multi($fichier, $galerie))
					{
						$listeFichiers .= "grandeNom=$fichier\n";
					
						if ($_POST['info'] == 'tout')
						{
							$listeFichiers .= "id=\n";
							$listeFichiers .= "vignetteNom=\n";
							$listeFichiers .= "vignetteLargeur=\n";
							$listeFichiers .= "vignetteHauteur=\n";
							$listeFichiers .= "vignetteAlt=\n";
							$listeFichiers .= "grandeLargeur=\n";
							$listeFichiers .= "grandeHauteur=\n";
							$listeFichiers .= "grandeAlt=\n";
							$listeFichiers .= "grandeLegende=\n";
							$listeFichiers .= "pageGrandeBaliseTitle=\n";
							$listeFichiers .= "pageGrandeDescription=\n";
							$listeFichiers .= "pageGrandeMotsCles=\n";
							$listeFichiers .= "origNom=\n";
						}
					
						$listeFichiers .= "__IMG__\n";
					}
				}
			}
		}
		
		closedir($fic);
		
		echo '<h2>' . T_("Résultat") . '</h2>' . "\n" . '<textarea name="listeFichiers" readonly="readonly">' . $listeFichiers . '</textarea>' . "\n";
	}
}
?>

<h1><?php echo T_("Gestion des galeries"); ?></h1>

<h2><?php echo T_("Générer un fichier de configuration de base d'une galerie"); ?></h2>

<p><?php echo T_("Pour générer automatiquement la liste des images d'une galerie, et ce sous la forme <code>grandeNom=grandeImage.extension</code>, remplissez le formulaire ci-dessous. Optionnellement, vous pouvez exclure des images du résultat."); ?></p>

<form action="<? echo $action; ?>" method="post">
<div>

<p><label><?php echo T_("Id de la galerie"); ?>:</label><br />
<input type="text" name="id" /></p>

<p><input type=checkbox name="info" value="tout" /> <label><?php echo T_("Ajouter des champs vides pour chaque oeuvre"); ?></label></p>

<fieldset>
<legend><?php echo T_("Exclusions"); ?></legend>
<p><input type=checkbox name="exclureVignette" value="vignette" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des fichiers terminant par <code>-vignette.extension</code>"); ?></label></p>

<p><input type=checkbox name="exclureOrig" value="orig" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des fichiers terminant par <code>-orig.extension</code>"); ?></label></p>

<p><input type=checkbox name="exclureSiExiste" value="existe" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des images déjà présentes dans le fichier de configuration de cette galerie (s'il existe)"); ?></label></p>
</fieldset>

<p><input type="submit" name="soumettre" value="<?php echo T_('Soumettre'); ?>" /></p>

</div>
</form>

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
