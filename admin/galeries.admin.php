<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Gestion des galeries");
include 'inc/premier.inc.php';

include '../init.inc.php';
?>

<h1><?php echo T_("Gestion des galeries"); ?></h1>

<div id="boiteMessages" class="boite">
<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

<?php
########################################################################
##
## Générer une copie réduite des images originales
##
########################################################################

if (isset($_POST['versionGrande']))
{
	$qualiteJpg = intval($_POST['qualiteJpg']);
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'] . '/';
	
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$listeModifs = array ();
		
		if ($_POST['versionOrig'] == 'renommerOrig')
		{
			$fic = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
			
			while($fichier = @readdir($fic))
			{
				if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
				{
					$infoFichier = pathinfo(basename($fichier));
					$nouveauNom = basename($fichier, '.' . $infoFichier['extension']);
					$nouveauNom .= '-orig.' . $infoFichier['extension'];
					if (rename($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom))
					{
						$listeModifs[] = sprintf(T_('Renommage de <code>%1$s</code> en <code>%2$s</code>'), $fichier, $nouveauNom) . "\n";
					}
					else
					{
						$listeModifs[] = sprintf(T_('Impossible de renommer <code>%1$s</code> en <code>%2$s</code>'), $fichier, $nouveauNom) . "\n";
					}
				}
			}
			
			closedir($fic);
		}
		
		// A: les images à traiter ont la forme `nom-orig.extension`
		
		$fic2 = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
		
		while($fichier = @readdir($fic2))
		{
			$infoFichier = pathinfo(basename($fichier));
			$nouveauNom = preg_replace('/-orig\..{3,4}$/', '.', $fichier) . $infoFichier['extension'];
			
			if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..' && preg_match('/-orig\..{3,4}$/', $fichier) && !file_exists($cheminGalerie . '/' . $nouveauNom))
			{
				// On trouve le type de l'image dans le but d'utiliser la bonne fonction php
				$type = typeImage($infoFichier['extension']);
				
				switch ($type)
				{
					case 'gif':
						$imageOrig = imagecreatefromgif($cheminGalerie . '/' . $fichier);
						break;
					
					case 'jpeg':
						$imageOrig = imagecreatefromjpeg($cheminGalerie . '/' . $fichier);
						break;
					
					case 'png':
						$imageOrig = imagecreatefrompng($cheminGalerie . '/' . $fichier);
						break;
				}
				
				// Calcul des dimensions de l'orig
				$imageOrigHauteur = imagesy($imageOrig);
				$imageOrigLargeur = imagesx($imageOrig);
				
				// On trouve les futures dimensions de la version grande
				$imageGrandeHauteur = $_POST['hauteur'];
				if ($imageGrandeHauteur > $imageOrigHauteur)
				{
					$imageGrandeHauteur = $imageOrigHauteur;
				}
				$imageGrandeLargeur = ($imageGrandeHauteur / $imageOrigHauteur) * $imageOrigLargeur;
				if ($imageGrandeLargeur > $_POST['largeur'])
				{
					$imageGrandeLargeur = $_POST['largeur'];
					$imageGrandeHauteur = ($imageGrandeLargeur / $imageOrigLargeur) * $imageOrigHauteur;
				}
				
				// On crée une image grande vide
				$imageGrande = imagecreatetruecolor($imageGrandeLargeur, $imageGrandeHauteur);
				if ($type == 'png')
				{
					imagealphablending($imageGrande, false);
					imagesavealpha($imageGrande, true);
				}
				
				// On crée la version grande à partir de l'orig
				imagecopyresampled($imageGrande, $imageOrig, 0, 0, 0, 0, $imageGrandeLargeur, $imageGrandeHauteur, $imageOrigLargeur, $imageOrigHauteur);
				
				// Netteté
				if (isset($_POST['actions']) && in_array('nettete', $_POST['actions']))
				{
					$imageGrande = UnsharpMask($imageGrande, '100', '1', '3');
				}
				
				// On enregistre la version grande
				switch ($type)
				{
					case 'gif':
						if (imagegif($imageGrande, $cheminGalerie . '/' . $nouveauNom))
						{
							$listeModifs[] = sprintf(T_('Création de <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						else
						{
							$listeModifs[] = sprintf(T_('Impossible de créer <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						break;
					
					case 'jpeg':
						if (imagejpeg($imageGrande, $cheminGalerie . '/' . $nouveauNom, $qualiteJpg))
						{
							$listeModifs[] = sprintf(T_('Création de <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						else
						{
							$listeModifs[] = sprintf(T_('Impossible de créer <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						break;
					
					case 'png':
						if (imagepng($imageGrande, $cheminGalerie . '/' . $nouveauNom, 9))
						{
							$listeModifs[] = sprintf(T_('Création de <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						else
						{
							$listeModifs[] = sprintf(T_('Impossible de créer <code>%1$s</code> à partir de <code>%2$s</code>'), $nouveauNom, $fichier) . "\n";
						}
						break;
				}
			}
		}
		
		closedir($fic2);
		
		if (empty($listeModifs))
		{
			$listeModifs[] = T_("Aucune modification.");
		}
		
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Retaillage des images") . '</h3>' . "\n";
		echo '<ul>' . "\n";
		foreach ($listeModifs as $modif)
		{
			echo '<li>' . $modif . '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '</div><!-- /boite2 -->' . "\n";
	}
}

########################################################################
##
## Créer un fichier de configuration pour une galerie
##
########################################################################

if (isset($_POST['generer']) ||
	(isset($_POST['versionGrande']) && isset($_POST['creerConf'])))
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'] . '/';
	
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$fic = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
		
		$fichierConfigChemin = $racine . '/site/inc/galerie-' . $_POST['id'] . '.txt';
		
		if (file_exists($fichierConfigChemin))
		{
			$fichierConfigExiste = TRUE;
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
				if (((isset($_POST['exclureVignette']) && $_POST['exclureVignette'] != 'vignette') || !preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier)) && ((isset($_POST['exclureOrig']) && $_POST['exclureOrig'] != 'orig') || !preg_match('/-orig\.[[:alpha:]]{3,4}$/', $fichier)))
				{
					if (!$fichierConfigExiste || (isset($_POST['exclureSiExiste']) && $_POST['exclureSiExiste'] != 'existe') || !in_array_multi($fichier, $galerie))
					{
						$listeFichiers .= "grandeNom=$fichier\n";
					
						if (isset($_POST['info']) && $_POST['info'][0] != 'aucun')
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
		
		$listeFichiers = rtrim($listeFichiers);
		
		if (isset($_POST['creerConf']) && ($_POST['creerConf'] == 'vide' || $_POST['creerConf'] == 'rempli') && !$fichierConfigExiste)
		{
			if ($fic = fopen($fichierConfigChemin, 'w'))
			{
				$fichierConfigExiste = TRUE;
				if ($_POST['creerConf'] == 'rempli')
				{
					fputs($fic, $listeFichiers);
				}
				fclose($fic);
			}
			else
			{
				echo "<p class='erreur'>" . sprintf(T_('Impossible de créer le fichier de configuration <code>%1$s</code>. Veuillez vérifier les droits du dossier ou créer le fichier à la main.'), $fichierConfigChemin) . "</p>";
			}
		}
		
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Contenu pour le fichier de configuration") . '</h3>' . "\n";
		echo '<pre id="listeFichiers">' . $listeFichiers . '</pre>' . "\n";
		echo "<ul>\n";
		echo "<li><a href=\"javascript:selectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
		if ($fichierConfigExiste)
		{
			echo '<li>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/' . basename($fichierConfigChemin) . '#messagesPorteDocuments">' . T_("Modifier le fichier.") . '</a></li>' . "\n";
		}
		echo "</ul>\n";
		echo "</div><!-- /boite2 -->\n";
	}
}

########################################################################
##
## Lister les fichiers de configuration existants
##
########################################################################

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
	
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Liste des fichiers de configuration") . '</h3>' . "\n";
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
	echo "</div><!-- /boite2 -->\n";
}
?>

</div><!-- /boiteMessages -->

<div class="boite">
<h2 id="generer"><?php echo T_("Créer un fichier de configuration pour une galerie"); ?></h2>

<p><?php echo T_("Chaque galerie nécessite un fichier de configuration pour être prise en compte par Squeletml. Vous pouvez le créer à la main, ou en générer un automatiquement à l'aide de ce formulaire. Vous pourrez par la suite personnaliser ce fichier à la main si vous le désirez."); ?></p>

<p><?php echo T_("Pour générer automatiquement la liste des images d'une galerie, et ce sous la forme <code>grandeNom=grandeImage.extension</code>, remplissez le formulaire ci-dessous. Optionnellement, vous pouvez exclure certaines images du résultat."); ?></p>

<form action="<? echo $action; ?>#messages" method="post">
<div>

<p><label><?php echo T_("Id de la galerie:"); ?></label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("Si aucun fichier de configuration n'existe pour cette galerie, en créer un:"); ?></label><br />
<input type="radio" name="creerConf" value="vide" /> vide <input type="radio" name="creerConf" value="rempli" /> contenant le résultat de ce formulaire</p>

<p><label><?php echo T_("Ajouter des champs vides pour chaque oeuvre:"); ?></label><br />
<select name="info[]" multiple="multiple" size="4">
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

<p><input type="submit" name="generer" value="<?php echo T_('Générer la liste des images'); ?>" /></p>

</div>
</form>
</div><!-- /boite -->

<div class="boite">
<h2 id="versionGrande"><?php echo T_("Générer une copie réduite des images originales"); ?></h2>

<p><?php echo T_("Vous pouvez faire générer automatiquement une copie réduite (qui sera utilisée comme étant la version grande dans la galerie) de chaque image au format original. Aucune image au format original ne sera modifiée."); ?></p>

<form action="<? echo $action; ?>#messages" method="post">
<div>
<p><label><?php echo T_("Id de la galerie:"); ?></label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("Taille maximale de la version grande (largeur × hauteur):"); ?></label><br />
<?php echo T_("La plus grande taille possible contenable dans les dimensions données sera utilisée. Les proportions de l'image sont conservées."); ?><br />
<input type="text" name="largeur" size="4" /> <?php echo T_("×"); ?> <input type="text" name="hauteur" size="4" /></p>

<p><label><?php echo T_("Comment manipuler les images du dossier?"); ?></label><br />
<input type="radio" name="versionOrig" value="orig" checked="checked" /> <?php echo T_("Le nom des images originales se termine par <code>-orig.extension</code>. Générer un fichier sans <code>-orig</code> pour chaque version grande."); ?><br />
<input type="radio" name="versionOrig" value="renommerOrig" /> <?php echo T_("Renommer préalablement les images du dossier en <code>nom-orig.extension</code> et ensuite gérérer les image en version grande sans le suffixe <code>-orig</code>."); ?></p>

<p><label><?php echo T_("S'il y a lieu, qualité des images JPG générées (0-100):"); ?></label><br />
<input type="text" name="qualiteJpg" value="90" size="2" /></p>

<p><input type="checkbox" name="actions[]" value="nettete" /> <label><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)"); ?></label></p>

<p><input type="radio" name="creerConf" value="rempli" /> <label><?php echo T_("Si aucun fichier de configuration n'existe pour cette galerie, en créer un minimal (contenant le nom des images en version grande)."); ?></label></p>

<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

<p><input type="submit" name="versionGrande" value="<?php echo T_('Retailler les images originales'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<div class="boite">
<h2 id="lister"><?php echo T_("Lister les fichiers de configuration existants"); ?></h2>

<p><?php echo T_("Vous pouvez faire afficher la liste des fichiers de configuration existants. Chaque fichier dans la liste aura un lien vous permettant de le modifier dans le porte-documents."); ?></p>

<form action="<? echo $action; ?>#messages" method="post">
<div>
<p><input type="submit" name="lister" value="<?php echo T_('Lister les fichiers de configuration'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
