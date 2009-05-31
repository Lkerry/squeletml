<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Gestion des galeries");
include 'inc/premier.inc.php';

include '../init.inc.php';
?>

<h1><?php echo T_("Gestion des galeries"); ?></h1>

<h2 id="generer"><?php echo T_("Générer un fichier de configuration de base d'une galerie"); ?></h2>

<?php
if (isset($_POST['generer']))
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'] . '/';
	
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$fic = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
		
		if (file_exists($racine . '/site/inc/galerie-' . $_POST['id'] . '.txt'))
		{
			$fichierConfigExiste = TRUE;
			$fichierConfigChemin = $racine . '/site/inc/galerie-' . $_POST['id'] . '.txt';
			$galerie = construitTableauGalerie($racine . '/site/inc/galerie-' . $_POST['id'] . '.txt');
		}
		else
		{
			$fichierConfigExiste = FALSE;
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
					
						if ($_POST['info'][0] != 'aucun')
						{
							foreach ($_POST['info'] as $champ)
							{
								$listeFichiers .= "$champ=\n";
							}
						}
					
						$listeFichiers .= "__IMG__\n";
					}
				}
			}
		}
		
		closedir($fic);
		
		echo '<h2>' . T_("Résultat") . '</h2>' . "\n";
		echo '<pre id="listeFichiers">' . rtrim($listeFichiers) . '</pre>' . "\n";
		echo "<ul>\n";
		echo "<li><a href=\"javascript:selectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
		if ($fichierConfigExiste)
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/' . basename($fichierConfigChemin) . '#messagesPorteDocuments">' . T_("Modifier le fichier.") . '</a></li>' . "\n";
		}
		echo "</ul>\n";
	}
}
?>

<p><?php echo T_("Pour générer automatiquement la liste des images d'une galerie, et ce sous la forme <code>grandeNom=grandeImage.extension</code>, remplissez le formulaire ci-dessous. Optionnellement, vous pouvez exclure des images du résultat."); ?></p>

<form action="<? echo $action; ?>#generer" method="post">
<div>

<p><label><?php echo T_("Id de la galerie"); ?>:</label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("Ajouter des champs vides pour chaque oeuvre"); ?></label><br />
<select name="info[]" multiple="multiple">
<option value="aucun" selected="selected"><?php echo T_("Aucun"); ?></option>
<option value="id">id</option>
<option value="vignetteNom">vignetteNom</option>
<option value="vignetteLargeur">vignetteLargeur</option>
<option value="vignetteHauteur">vignetteHauteur</option>
<option value="vignetteAlt">vignetteAlt</option>
<option value="grandeLargeur">grandeLargeur</option>
<option value="grandeHauteur">grandeHauteur</option>
<option value="grandeAlt">grandeAlt</option>
<option value="grandeLegende">grandeLegende</option>
<option value="pageGrandeBaliseTitle">pageGrandeBaliseTitle</option>
<option value="pageGrandeDescription">pageGrandeDescription</option>
<option value="pageGrandeMotsCles">pageGrandeMotsCles</option>
</select></p>

<fieldset>
<legend><?php echo T_("Exclusions"); ?></legend>
<p><input type=checkbox name="exclureVignette" value="vignette" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des fichiers terminant par <code>-vignette.extension</code>"); ?></label></p>

<p><input type=checkbox name="exclureOrig" value="orig" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des fichiers terminant par <code>-orig.extension</code>"); ?></label></p>

<p><input type=checkbox name="exclureSiExiste" value="existe" checked="checked" /> <label><?php echo T_("Ne pas tenir compte des images déjà présentes dans le fichier de configuration de cette galerie (s'il existe)"); ?></label></p>
</fieldset>

<p><input type="submit" name="generer" value="<?php echo T_('Générer'); ?>" /></p>

</div>
</form>

<h2 id="lister"><?php echo T_("Lister les fichiers de configuration existants"); ?></h2>

<p><?php echo T_("Afficher la liste des fichiers de configuration existants. Chaque fichier dans la liste aura un lien permettant de le modifier dans le porte-documents."); ?></p>

<form action="<? echo $action; ?>#lister" method="post">
<div>
<p><input type="submit" name="lister" value="<?php echo T_('Lister'); ?>" /></p>
</div>
</form>

<?php
if (isset($_POST['lister']))
{
	$fic = opendir($racine . '/site/inc') or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $racine . '/site/inc') . "</p>");
	
	$listeFichiers = '';
	while($fichier = @readdir($fic))
	{
		if(!is_dir($racine . '/site/inc/' . $fichier) && $fichier != '.' && $fichier != '..')
		{
			if (preg_match('/^galerie-(.*)\.txt$/', $fichier))
			{
				$listeFichiers .= '<li><a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/' . $fichier . '#messagesPorteDocuments">' . $fichier . "</a></li>\n";
			}
		}
	}
	
	closedir($fic);
	
	echo '<h3>' . T_("Liste") . '</h3>' . "\n";
	
	echo "<ul>\n";
	
	if (!empty($listeFichiers))
	{
		echo $listeFichiers;
	}
	else
	{
		echo '<li>' . T_("Aucun fichier") . "</li>\n";
	}
	
	echo "</ul>\n";
}
?>













<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
