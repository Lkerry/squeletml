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
## Ajouter des images
##
########################################################################

if (isset($_POST['ajouter']))
{
	$cheminGaleries = $racine . '/site/fichiers/galeries';
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'];
	
	$listeModifs = array ();
	
	if (!file_exists($cheminGalerie))
	{
		if (mkdir($cheminGalerie))
		{
			$listeModifs[] = sprintf(T_('Création du dossier %1$s.'), '<code>' . $cheminGalerie . '</code>');
		}
		
		else
		{
			unlink($_FILES['fichier']['tmp_name']);
			echo "<p class='erreur'>" . sprintf(T_('Impossible de créer le dossier %1$s.'), '<code>' . $cheminGalerie . '</code>') . "</p>\n";
			$listeModifs[] = T_('Archive supprimée.');
		}
	}
	
	if (file_exists($cheminGalerie))
	{
		if (isset($_FILES['fichier']))
		{
			$nomArchive = basename($_FILES['fichier']['name']);
			
			if (!preg_match('/(\.tar|\.tar\.gz|\.tgz)$/i', $nomArchive))
			{
				$cheminDeplacement = $cheminGalerie;
			}
			else
			{
				$cheminDeplacement = $cheminGaleries;
			}
			
			if (file_exists($cheminDeplacement . '/' . $nomArchive))
			{
				unlink($_FILES['fichier']['tmp_name']);
				echo '<p class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), '<code>' . $nomArchive . '</code>', '<code>' . $cheminDeplacement . '/</code>') . "</p>\n";
				$listeModifs[] = T_('Le fichier que vous avez téléversé sur le serveur a été supprimé.');
			}
			else
			{
				if (move_uploaded_file($_FILES['fichier']['tmp_name'], $cheminDeplacement . '/' . $nomArchive))
				{
					if ($cheminDeplacement == $cheminGalerie)
					{
						$listeModifs[] = sprintf(T_('Ajout de %1$s dans le dossier %2$s.'), '<code>' . $nomArchive . '</code>', '<code>' . $cheminGaleries . '/' . $_POST['id'] . '/</code>');
					}
					else
					{
						$resultatArchive = 0;
					
						if (preg_match('/(\.tar|\.tar\.gz|\.tgz)$/i', $nomArchive))
						{
							$resultatArchive = PclTarExtract($cheminGaleries . '/' . $nomArchive, $cheminGaleries . '/' . $_POST['id'] . '/');
						}
						elseif (preg_match('/\.zip$/i', $nomArchive))
						{
							$archive = new PclZip($cheminGaleries . '/' . $nomArchive);
							$resultatArchive = $archive->extract(PCLZIP_OPT_PATH, $cheminGaleries . '/' . $_POST['id'] . '/');
						}
					
						if ($resultatArchive == 0)
						{
							unlink($cheminGaleries . '/' . $nomArchive);
							$listeModifs[] = T_('Archive supprimée.');
							if (preg_match('/(\.tar|\.tar\.gz|\.tgz)$/i', $nomArchive))
							{
								echo '<p class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s."), '<code>' . $nomArchive . '</code>') . "</p>\n";
							}
							elseif (preg_match('/\.zip$/i', $nomArchive))
							{
								echo '<p class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s: "), '<code>' . $nomArchive . '</code>') . $archive->errorInfo(true) . "</p>\n";
							}
							else
							{
								echo '<p class="erreur">' . sprintf(T_("Impossible d'utiliser l'archive %1\$s."), '<code>' . $nomArchive . '</code>') . "</p>\n";
							}
						}
						else
						{
							foreach ($resultatArchive as $infoImage)
							{
								if ($infoImage['status'] == 'ok')
								{
									$listeModifs[] = sprintf(T_('Ajout de %1$s dans le dossier %2$s.'), '<code>' . basename($infoImage['filename']) . '</code>', '<code>' . $cheminGaleries . '/' . $_POST['id'] . '/</code>');
								}
								elseif ($infoImage['status'] == 'newer_exist')
								{
									$listeModifs[] = sprintf(T_('Un fichier %1$s existe déjà, et est plus récent que celui de l\'archive. Il n\'y a donc pas eu extraction.'), '<code>' . $infoImage['filename'] . '</code>', '<code>' . $cheminGaleries . '/' . $_POST['id'] . '/</code>');
								}
								else
								{
									$listeModifs[] = sprintf(T_('Attention: une erreur a eu lieu avec le fichier %1$s. Vérifiez son état sur le serveur (s\'il s\'y trouve), et ajoutez-le à la main si nécessaire.'), '<code>' . $infoImage['filename'] . '</code>');
								}
							}
							unlink($cheminGaleries . '/' . $nomArchive);
							$listeModifs[] = T_('Archive supprimée.');
						}
					}
				}
				else
				{
					unlink($_FILES['fichier']['tmp_name']);
					echo '<p class="erreur">' . T_("Erreur lors du déplacement du fichier %1\$s.", '<code>' . $nomArchive . '</code>') . "</p>\n";
					$listeModifs[] = T_('Le fichier que vous avez téléversé sur le serveur a été supprimé.');
				}
			}
		}
		
		if (empty($listeModifs))
		{
			$listeModifs[] = T_("Aucune image n'a été extraite. Veuillez vérifier les instructions.");
		}
	}
	
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Ajout d'images") . '</h3>' . "\n";
	echo '<ul>' . "\n";
	foreach ($listeModifs as $modif)
	{
		echo '<li>' . $modif . '</li>' . "\n";
	}
	echo '</ul>' . "\n";
	echo '</div><!-- /boite2 -->' . "\n";
}

########################################################################
##
## Retailler les images originales
##
########################################################################

if (isset($_POST['retailler']))
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
					if (!preg_match('/-orig\.' . $infoFichier['extension'] . '/', $fichier))
					{
						$nouveauNom = basename($fichier, '.' . $infoFichier['extension']);
						$nouveauNom .= '-orig.' . $infoFichier['extension'];
						if (!file_exists($cheminGalerie . '/' . $nouveauNom) && rename($cheminGalerie . '/' . $fichier, $cheminGalerie . '/' . $nouveauNom))
						{
							$listeModifs[] = sprintf(T_('Renommage de <code>%1$s</code> en <code>%2$s</code>'), $fichier, $nouveauNom) . "\n";
						}
						else
						{
							$listeModifs[] = sprintf(T_('Impossible de renommer <code>%1$s</code> en <code>%2$s</code>'), $fichier, $nouveauNom) . "\n";
						}
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
				if (isset($_POST['actions']) && $_POST['actions'] == 'nettete')
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
## Lister les galeries existantes
##
########################################################################

if (isset($_POST['lister']))
{
	$fic = opendir($racine . '/site/inc') or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $racine . '/site/inc') . "</p>");
	
	$listeFichiers = '';
	$i = 0;
	while($fichier = @readdir($fic))
	{
		if(!is_dir($racine . '/site/inc/' . $fichier) && $fichier != '.' && $fichier != '..')
		{
			if (preg_match('/^galerie-(.*)\.txt$/', $fichier, $res))
			{
				$i++;
				$res[1] = str_replace('\\', '', $res[1]);
				$fichier = str_replace('\\', '', $fichier);
				$idLien = str_replace(array ("'", '"'), array ('%27', '%22'), $res[1]);
				$fichierLien = str_replace(array ("'", '"'), array ('%27', '%22'), $fichier);
				$listeFichiers .= '<li>' . sprintf(T_('Galerie %1$s:'), $i) . '<ul><li><em>' . T_("id:") . '</em> ' . $res[1] . '</li><li><em>' . T_("dossier:") . '</em> <a href="porte-documents.admin.php?action=parcourir&valeur=../site/fichiers/galeries/' . $idLien . '#fichiersEtDossiers">' . $res[1] . '</a></li><li><em>' . T_("Fichier de configuration:") . '</em> <a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/' . $fichierLien . '#messagesPorteDocuments">' . $fichier . "</a></li></ul></li>\n";
			}
		}
	}
	
	closedir($fic);
	
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Liste des galeries") . '</h3>' . "\n";
	echo "<ul>\n";
	
	if (!empty($listeFichiers))
	{
		echo $listeFichiers;
	}
	else
	{
		echo '<li>' . T_("Aucune galerie") . "</li>\n";
	}
	
	echo "</ul>\n";
	echo "</div><!-- /boite2 -->\n";
}

########################################################################
##
## Créer une page web de galerie
##
########################################################################

if (isset($_POST['creerPage']))
{
	$page = basename($_POST['page']);
	$cheminPage = '../' . dirname($_POST['page']);
	$id = str_replace('\\', '', $_POST['id']);
	$idDansVar = str_replace('"', '\"', $id);
	if ($cheminPage == '../.')
	{
		$cheminPage = '..';
	}
	$cheminInclude = preg_replace('|[^/]+/|', '../', $cheminPage);
	$cheminInclude = dirname($cheminInclude);
	if ($cheminInclude == '.')
	{
		$cheminInclude = '';
	}
	if (!empty($cheminInclude))
	{
		$cheminInclude .= '/';
	}
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'];
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$fichierConfigChemin = $racine . '/site/inc/galerie-' . $_POST['id'] . '.txt';
		
		if (!file_exists($fichierConfigChemin))
		{
			echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'a pas de fichier de configuration.'), $_POST['id']) . "</p>";
		}
		else
		{
			if (!file_exists($cheminPage))
			{
				echo "<p class='erreur'>" . sprintf(T_('Le chemin %1$s vers la page à créer n\'existe pas. Veuillez vous assurer que les dossiers existent.'), '<code>' . $cheminPage . '</code>') . "</p>";
			}
			else
			{
				if (file_exists($cheminPage . '/' . $page))
				{
					$trad = sprintf(T_("La page web %1\$s existe déjà. Vous pouvez modifier la fichier."), '<code>' . $cheminPage . '/' . $page . '</code>');
				}
				else
				{
					if ($fic = fopen($cheminPage . '/' . $page, 'a'))
					{
						$contenu = '';
						$contenu .= '<?php' . "\n";
						$contenu .= '$baliseTitle = "Galerie ' . $idDansVar . '";' . "\n";
						$contenu .= '$description = "Galerie ' . $idDansVar . '";' . "\n";
						$contenu .= '$idGalerie = "' . $idDansVar . '";' . "\n";
						$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
						$contenu .= '?>' . "\n";
						$contenu .= "\n";
						$contenu .= '<h1>Galerie ' . $id . '</h1>' . "\n";
						$contenu .= "\n";
						$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
						fputs($fic, $contenu);
						
						fclose($fic);
						$trad = T_('Le modèle de page a été créé. Vous pouvez modifier le fichier.');
					}
					else
					{
						echo "<p class='erreur'>" . sprintf(T_('Impossible de créer le fichier %1$s.'), '<code>' . $cheminPage . '/' . $page . '</code>') . "</p>";
					}
				}
				
				if (file_exists($cheminPage . '/' . $page))
				{
					echo '<div class="boite2">' . "\n";
					echo '<h3>' . T_("Page web") . '</h3>' . "\n";
				
					echo '<p><a href="porte-documents.admin.php?action=modifier&valeur=' . $cheminPage . '/' . $page . '#messagesPorteDocuments">' . $trad . '</a></p>';
					echo "</div><!-- /boite2 -->\n";
				}
			}
		}
	}
}

########################################################################
##
## Afficher un modèle de fichier de configuration
##
########################################################################

if (isset($_POST['modeleConf']))
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'];
	$fic = opendir($cheminGalerie) or die("<p class='erreur'>" . sprintf(T_('Erreur lors de l\'ouverture du dossier %1$s.'), $cheminGalerie) . "</p>");
$listeFichiers = '';
	while($fichier = @readdir($fic))
	{
		if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
		{
			if (!preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier) && !preg_match('/-orig\.[[:alpha:]]{3,4}$/', $fichier))
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
	closedir($fic);
	
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Modèle") .'</h3>' ."\n" ;
	echo '<pre id="listeFichiers">' . $listeFichiers . '</pre>' . "\n";
	echo "<ul>\n";
	echo "<li><a href=\"javascript:selectionneTexte('listeFichiers');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
	echo "</ul>\n";
	echo "</div><!-- /boite2 -->\n";
}

########################################################################
##
## Créer ou mettre à jour le fichier de configuration
##
########################################################################

if (isset($_POST['conf']) && $_POST['conf'] == 'maj')
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $_POST['id'];
	if (!file_exists($cheminGalerie))
	{
		echo "<p class='erreur'>" . sprintf(T_('La galerie %1$s n\'existe pas.'), $_POST['id']) . "</p>";
	}
	else
	{
		$fichierConfigChemin = $racine . '/site/inc/galerie-' . $_POST['id'] . '.txt';
		
		if (file_exists($fichierConfigChemin))
		{
			$configExisteAuDepart = TRUE;
		}
		else
		{
			$configExisteAuDepart = FALSE;
		}
		
		if (adminMajConfGalerie($racine, $_POST['id'], ''))
		{
			$listeModifs = array ();
			if ($configExisteAuDepart)
			{
				$listeModifs[] = sprintf(T_("Mise à jour du fichier de configuration %1\$s."), '<code>' . $fichierConfigChemin . '</code>');
			}
			else
			{
				$listeModifs[] = sprintf(T_("Création du fichier de configuration %1\$s."), '<code>' . $fichierConfigChemin . '</code>');
			}
		}
		else
		{
			echo "<p class='erreur'>" . sprintf(T_('Erreur lors de la création ou de la mise à jour du fichier de configuration <code>%1$s</code>. Veuillez vérifier manuellement son contenu.'), $fichierConfigChemin) . "</p>";
		}
		
		echo '<div class="boite2">' . "\n";
		echo '<h3>' . T_("Fichier de configuration") . '</h3>' . "\n";
		if (!empty($listeModifs))
		{
			echo '<h4>' . T_("Actions effectuées") .'</h4>' ."\n" ;
			echo "<ul>\n";
			foreach ($listeModifs as $modif)
			{
				echo "<li>$modif</li>\n";
			}
			echo "</ul>\n";
		}
		echo "</div><!-- /boite2 -->\n";
	}
}

########################################################################

if (isset($_POST['modeleConf']) ||
	(isset($_POST['conf']) && $_POST['conf'] == 'maj'))
{
	if (file_exists($racine . '/site/inc/galerie-' . $_POST['id'] . '.txt'))
	{
		$id = str_replace('\\', '', $_POST['id']);
		$id = str_replace(array ("'", '"'), array ('%27', '%22'), $id);
		$fichierConfigChemin = $racine . '/site/inc/galerie-' . $id . '.txt';
		echo '<p>' . T_("Un fichier de configuration existe pour cette galerie.") . ' <a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/' . basename($fichierConfigChemin) . '#messagesPorteDocuments">' . T_("Modifier le fichier.") . '</a></p>' . "\n";
	}
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

<div class="boite">
<h2><?php echo T_("Ajouter des images"); ?></h2>

<p><?php echo T_("Vous pouvez téléverser vers votre site en une seule fois plusieurs images contenues dans une archive de format TAR (.tar, .tar.gz ou .tgz) ou ZIP (.zip). Veuillez créer votre archive de telle sorte que les images y soient à la racine, et non contenues dans un dossier. Prenez note que si la galerie existe déjà et qu'un fichier de l'archive possède le même nom qu'un fichier déjà existant sur le serveur, le fichier sur le serveur sera écrasé seulement si sa date est plus ancienne que celle du fichier dans l'archive."); ?></p>

<p><?php echo T_("Vous pouvez également ajouter une seule image en choisissant un fichier image au lieu d'une archive."); ?></p>

<p><?php printf(T_('<strong>Taille maximale d\'un transfert de fichier:</strong> %1$s octets (%2$s Mio).'), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)); ?></p>

<form action="<?php echo $action; ?>#messages" method="post" enctype="multipart/form-data">
<div>
<p><label><?php echo T_("Id de la galerie (si elle n'existe pas, elle sera créée):"); ?></label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("Fichier:"); ?></label><br />
<input type="file" name="fichier" size="25"/></p>

<p><input type="checkbox" name="conf" value="maj" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie avec les paramètres par défaut (les fichiers <code>-vignette.extension</code> et <code>-orig.extension</code> sont ignorés, les autres sont considérés comme étant la version grande à afficher)."); ?></label></p>

<p><input type="submit" name="ajouter" value="<?php echo T_('Ajouter des images'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<!--  -->

<div class="boite">
<h2><?php echo T_("Créer des images de taille intermédiaire à partir des images originales"); ?></h2>

<p><?php echo T_("Vous pouvez faire générer automatiquement une copie réduite (qui sera utilisée comme étant la version grande dans la galerie) de chaque image au format original. Aucune image au format original ne sera modifiée."); ?></p>

<form action="<?php echo $action; ?>#messages" method="post">
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

<p><input type="checkbox" name="actions" value="nettete" /> <label><?php echo T_("Renforcer la netteté des images redimensionnées (donne de mauvais résultats pour des images PNG avec transparence)"); ?></label></p>

<p><input type="checkbox" name="conf" value="maj" /> <label><?php echo T_("Créer ou mettre à jour le fichier de configuration de cette galerie avec les paramètres par défaut (les fichiers <code>-vignette.extension</code> et <code>-orig.extension</code> sont ignorés, les autres sont considérés comme étant la version grande à afficher)."); ?></label></p>

<p><strong><?php echo T_("Note: s'il y a de grosses images ou s'il y a beaucoup d'images dans le dossier, vous allez peut-être rencontrer une erreur de dépassement du temps alloué. Dans ce cas, relancez le script en rafraîchissant la page dans votre navigateur.") ?></strong></p>

<p><input type="submit" name="retailler" value="<?php echo T_('Retailler les images originales'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<!--  -->

<div class="boite">
<h2><?php echo T_("Créer une page web de galerie"); ?></h2>

<p><?php echo T_("Vous pouvez ajouter une page sur votre site pour présenter une galerie."); ?></p>

<form action="<?php echo $action; ?>#messages" method="post">
<div>
<p><label><?php echo T_("Id de la galerie:"); ?></label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("Emplacement de la page web:"); ?></label><br />
<?php echo $urlRacine . '/'; ?><input type="text" name="page" /></p>

<p><input type="submit" name="creerPage" value="<?php echo T_('Créer une page web'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<!--  -->

<div class="boite">
<h2><?php echo T_("Lister les galeries existantes"); ?></h2>

<p><?php echo T_("Vous pouvez afficher la liste des galeries existantes. Chaque galerie dans la liste aura un lien vous permettant de modifier son fichier de configuration dans le porte-documents."); ?></p>

<form action="<?php echo $action; ?>#messages" method="post">
<div>
<p><input type="submit" name="lister" value="<?php echo T_('Lister les galeries'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<!--  -->

<div class="boite">
<h2><?php echo T_("Afficher un modèle de fichier de configuration"); ?></h2>

<form action="<?php echo $action; ?>#messages" method="post">
<div>

<p><label><?php echo T_("Id de la galerie:"); ?></label><br />
<input type="text" name="id" /></p>

<p><label><?php echo T_("En plus du champ obligatoire <code>grandeNom</code>, ajouter des champs vides:"); ?></label><br />
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
<option value="origNom">origNom</option>
</select></p>

<p><input type="submit" name="modeleConf" value="<?php echo T_('Afficher un fichier de configuration'); ?>" /></p>

</div>
</form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
