<?php
include 'inc/zero.inc.php';
$baliseTitle = "Porte-documents";
include 'inc/premier.inc.php';

// Création de la variable $tableauDossiersFiltres
if ($typeFiltreDossiers == 'dossiersPermis' || $typeFiltreDossiers == 'dossiersExclus'
	&& !empty($filtreDossiers))
{
	$tableauDossiersFiltres = explode('|', $filtreDossiers);
}
else
{
	$tableauDossiersFiltres = array ();
}

// Motif à rechercher dans les noms de fichiers téléchargés
$motifNom = "^[- \+\.\(\)_0-9a-zA-Z]*$";
?>

<h1><?php echo T_("Porte-documents"); ?></h1>

<div class="boite">
<h2><?php echo T_("Information"); ?></h2>

<ul>
	<li><?php printf(T_('<strong>Taille maximale d\'un transfert de fichier:</strong> %1$s octets (%2$s Mio).'), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)); ?></li>
	<li><strong><?php echo T_("Extensions permises:"); ?></strong>
	<?php
	if ($filtreExtensions)
	{
		foreach ($extensionsPermises as $ext)
		{
			echo "$ext ";
		}
		?>
		<br /><em><?php echo T_("Si vous voulez télécharger un fichier avec une extension qui n'est pas dans la liste, en faire la demande à la personne administratrice de ce site, ou si vous avez les droits d'administration, modifiez le fichier de configuration."); ?></em></li>
		<?php
	}

	else
	{
		echo T_("toutes.") . '</li>';
	}
	?>
</ul>

</div><!-- /boite -->

<div id="boiteMessages" class="boite">
<h2 id="messagesPorteDocuments"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

<?php
if (isset($_POST['telechargerSuppr']))
{
	$suppr = $_POST['telechargerSuppr'];
	echo "<ul>\n";
	foreach ($suppr as $valeur)
	{
		if (file_exists($valeur) && !is_dir($valeur))
		{
			if (unlink($valeur))
			{
				echo "<li class='succes'>" . sprintf(T_('Suppression de <span class="porteDocumentsNom">%1$s</span> effectuée.'), $valeur) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Impossible de supprimer le fichier <span class="porteDocumentsNom">%1$s</span>.'), $valeur) . "</li>";
			}
		}

		elseif (file_exists($valeur) && is_dir($valeur))
		{
			if (rmdir($valeur))
			{
				echo "<li class='succes'>" . sprintf(T_('Suppression de <span class="porteDocumentsNom">%1$s</span> effectuée.'), $valeur) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Impossible de supprimer le dossier <span class="porteDocumentsNom">%1$s</span>. Vérifiez qu\'il est vide.'), $valeur) . "</li>";
			}
		}
		elseif (!file_exists($valeur))
		{
			echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> n\'existe pas.'), $valeur) . "</li>\n";
		}
	}
	echo "</ul>\n";
}

if (isset($_GET['action']))
{
	// Action Renommer
	if ($_GET['action'] == 'renommer')
	{
		$ancienNom = $_GET['valeur'];
		echo "<h3>" . T_("Insctructions de renommage") . "</h3>";
		echo "<ul>\n";
		echo "<li>" . sprintf(T_('Pour renommer <span class="porteDocumentsNom">%1$s</span>, saisir le nouveau nom dans le champ.'), $ancienNom) . "</li>";
		echo "<li>" . T_("Ne pas oublier de mettre le chemin dans le nom.");
		echo "<li>" . T_("Exemples:");
		echo "<ul>\n";
		echo "<li><span class='porteDocumentsNom'>$dossierRacine/nouveau-nom-dossier</span></li>";
		echo "<li><span class='porteDocumentsNom'>$dossierRacine/nouveau-nom.txt</span></li>";
		echo "<li><span class='porteDocumentsNom'>fichiers/nouveau-nom-dossier/nouveau-nom-fichier.txt</span>.</li>";
		echo "</ul></li>";
		echo "<li>" . T_("Important: ne pas mettre de barre oblique / dans le nouveau nom du fichier. N'utiliser ce signe que pour marquer le chemin vers le fichier.") . "</li>";
		echo "</ul>\n";
		?>
		<form action="<?php echo $action; ?>" method="post"><div>
		<input type="checkbox" name="porteDocumentsRenommerDupliquer" value="dupliquer" /> <?php echo T_("Dupliquer le fichier (en faire une copie et renommer la copie)"); ?><br />
		<input type="hidden" name="porteDocumentsAncienNom" value="<?php echo $ancienNom; ?>" /> <input type="text" name="porteDocumentsNouveauNom" value="<?php echo $ancienNom; ?>" size="50" />
		<input type="submit" value="<?php echo T_('Renommer'); ?>" />
		</div></form>
		<?php
	}
	
	// Action Modifier
	if ($_GET['action'] == 'modifier')
	{
		echo "<h3>" . T_("Insctructions de modification") . "</h3>";
		if (file_exists($_GET['valeur']))
		{
			echo "<p>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications».'), $_GET['valeur']) . "</p>";
		}
		else
		{
			echo "<p>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> n\'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisi.'), $_GET['valeur']) . "</p>";
		}
		
		?>
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post"><div>
		<?php
		clearstatcache();
		if (file_exists($_GET['valeur']) && filesize($_GET['valeur']))
		{
			$fic = fopen($_GET['valeur'], 'r');
			$contenuFichier = fread($fic, filesize($_GET['valeur']));
			fclose($fic);
		}
		else
		{
			$contenuFichier = '';
		}
		
		if (!$colorationSyntaxique)
		{
			$style = 'style="width: 93%;"';
		}
		else
		{
			$style = '';
		}
		?>
		<div id="redimensionnable"><textarea id="code" cols="80" rows="25" <?php echo $style; ?> name="porteDocumentsContenuFichier"><?php echo $contenuFichier; ?></textarea><img src="fichiers/redimensionner.png" alt="<?php echo T_('Appuyez sur Maj, cliquez sur l\'image et glissez-là pour redimensionner le champ de saisie'); ?>" title="<?php echo T_('Appuyez sur Maj, cliquez sur l\'image et glissez-là pour redimensionner le champ de saisie'); ?>" width="41" height="20" /></div>
		<input type="hidden" name="porteDocumentsModifierNom" value="<?php echo $_GET['valeur']; ?>" />
		<input type="submit" value="<?php echo T_('Sauvegarder les modifications'); ?>" />
		
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
		<div>
		<input type="submit" name="porteDocumentsModifierAnnuler" value="<?php echo T_('Annuler'); ?>" />
		<input type="hidden" name="porteDocumentsModifierNom" value="<?php echo $_GET['valeur']; ?>" />
		</div></form>
		
		</div></form>
		<?php
	}
}

if (isset($_POST['porteDocumentsModifierAnnuler']))
{
	echo "<p class='succes'>" . sprintf(T_('Aucune modification apportée au fichier %1$s.'), $_POST['porteDocumentsModifierNom']) . "</p>";
}
elseif (isset($_POST['porteDocumentsContenuFichier']))
{
	$messageErreurModifier = '';
	$messageErreurModifier .= "<p class='erreur'>" . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
	$messageErreurModifier .= '<p><textarea name="porteDocumentsContenuFichier" readonly="readonly">';
	$messageErreurModifier .= adminFormateTexte($_POST['porteDocumentsContenuFichier']);
	$messageErreurModifier .= "</textarea></p>\n";

	echo "<ul>\n";

	if (!$fic = fopen($_POST['porteDocumentsModifierNom'], 'w'))
	{
		echo "<li><p class='erreur'>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> n\'a pas pu être ouvert.'), $_POST['porteDocumentsModifierNom']) . "</p>\n$messageErreurModifier</li>\n";
	}

	if (fwrite($fic, adminFormateTexte($_POST['porteDocumentsContenuFichier'])) === FALSE)
	{
		echo "<li><p class='erreur'>" . sprintf(T_('Impossible d\'écrire dans le fichier <span class="porteDocumentsNom">%1$s</span>.'), $_POST['porteDocumentsModifierNom']) . "</p>\n$messageErreurModifier</li>\n";
	}

	echo "<li class='succes'>" . sprintf(T_('Modification du fichier <span class="porteDocumentsNom">%1$s</span> effectuée.'), $_POST['porteDocumentsModifierNom']) . "</li>\n";

	fclose($fic);

	echo "</ul>\n";
}

if (isset($_POST['porteDocumentsNouveauNom']))
{
	$ancienNom = $_POST['porteDocumentsAncienNom'];
	$nouveauNom = $_POST['porteDocumentsNouveauNom'];
	if (isset($_POST['porteDocumentsRenommerDupliquer']) && $_POST['porteDocumentsRenommerDupliquer'] == 'dupliquer')
	{
		$dupliquer = TRUE;
	}
	else
	{
		$dupliquer = FALSE;
	}

	echo "<ul>\n";
	if (file_exists($ancienNom))
	{
		if ($dupliquer)
		{
			if (copy($ancienNom, $nouveauNom))
			{
				echo "<li class='succes'>" . sprintf(T_('Copie et renommage de <span class="porteDocumentsNom">%1$s</span> en <span class="porteDocumentsNom">%2$s</span> effectués.'), $ancienNom, $nouveauNom) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Copie et renommage de <span class="porteDocumentsNom">%1$s</span> en <span class="porteDocumentsNom">%2$s</span> impossibles.'), $ancienNom, $nouveauNom) . "</li>\n";
			}
		}
		else
		{
			if (rename($ancienNom, $nouveauNom))
			{
				echo "<li class='succes'>" . sprintf(T_('Renommage de <span class="porteDocumentsNom">%1$s</span> en <span class="porteDocumentsNom">%2$s</span> effectué.'), $ancienNom, $nouveauNom) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Renommage de <span class="porteDocumentsNom">%1$s</span> en <span class="porteDocumentsNom">%2$s</span> impossible.'), $ancienNom, $nouveauNom) . "</li>\n";
			}
		}
	}

	elseif (!file_exists($ancienNom))
	{
		echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> n\'existe pas. Renommage en <span class="porteDocumentsNom">%2$s</span> impossible.'), $ancienNom, $nouveauNom) . "</li>\n";
	}
	echo "</ul>\n";
}

if (isset($_POST['porteDocumentsCreer']))
{
	$fichierCreeNom = $_POST['porteDocumentsFichierCreeNom'];

	if (!eregi("^$dossierRacine/", $fichierCreeNom))
	{
		$fichierCreeNomTemp = "$dossierRacine/$fichierCreeNom";
		unset($fichierCreeNom);
		$fichierCreeNom = $fichierCreeNomTemp;
	}

	$fichierCreeType = $_POST['porteDocumentsFichierCreeType'];

	echo "<ul>\n";
	if (!file_exists($fichierCreeNom))
	{
		if ($fichierCreeType == 'Fichier')
		{
			if (touch($fichierCreeNom))
			{
				echo "<li class='succes'>" . sprintf(T_('Création du fichier <span class="porteDocumentsNom">%1$s</span> effectuée.'), $fichierCreeNom) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Impossible de créer le fichier <span class="porteDocumentsNom">%1$s</span>.'), $fichierCreeNom) . "</li>\n";
			}
		}

		elseif ($fichierCreeType == 'Dossier')
		{
			if (mkdir($fichierCreeNom))
			{
				echo "<li class='succes'>" . sprintf(T_('Création du dossier <span class="porteDocumentsNom">%1$s</span> effectuée.'), $fichierCreeNom) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Impossible de créer le dossier <span class="porteDocumentsNom">%1$s</span>.'), $fichierCreeNom) . "</li>\n";
			}
		}
	}

	elseif (file_exists($fichierCreeNom))
	{
		echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> existe déjà.'), $fichierCreeNom) . "</li>\n";
	}
	echo "</ul>\n";
}

if (isset($_FILES['fichier']))
{
	unset($erreur);
	$rep = $_POST['rep'];
	$nomFichier = basename($_FILES['fichier']['name']);

	// Affichage du motif dans le message d'erreur
	$motifNom2 = substr($motifNom, 2, -3);
	$motifNom2 = str_replace('\\', '', $motifNom2);

	if ($filtreExtensions)
	{
		if (!in_array(substr(strrchr($_FILES['fichier']['name'], '.'), 1), $extensionsPermises))
		{
			$erreur .= "<li class='erreur'>" . T_("Veuillez sélectionner un bon format de fichier ou demandez à ce que l'extension de votre fichier soit ajoutée dans la liste.") . "</li>\n";
		}
	}

	if (file_exists($_FILES['fichier']['tmp_name']) && filesize($_FILES['fichier']['tmp_name']) > $tailleMaxFichiers)
	{
		$erreur .= "<li class='erreur'>" . sprintf(T_('Votre fichier doit faire moins de %1$s octets (%2$s Mio).'), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)) . "</li>\n";
	}

	if ($filtreNom)
	{
		if (!ereg($motifNom, $nomFichier))
		{
			$erreur .= "<li class='erreur'>" . sprintf(T_('Le nom de votre fichier risque de mal s\'afficher dans une adresse html. Veuillez le renommer en n\'utilisant que les caractères suivants:<br />%1$s<br />(les espaces sont automatiquement remplacées par des caractères de soulignement _).'), $motifNom2) . "</li>\n";
		}
	}

	if (file_exists($rep . '/' . $nomFichier))
	{
		$erreur .= "<li class='erreur'>" . T_("Un fichier existe déjà avec ce nom dans le dossier sélectionné.") . "</li>";
	}

	if (!isset($erreur))
	{
		/*$nomFichier = strtr($nomFichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$nomFichier = preg_replace('/([^.a-z0-9]+)/i', '_', $nomFichier);*/

		if ($filtreNom)
		{
			$nomFichier = preg_replace('/ /', '_', $nomFichier);
		}

		$resultat = move_uploaded_file($_FILES['fichier']['tmp_name'], $rep . '/' . $nomFichier);

		if ($resultat == TRUE)
		{
			echo "<p class='succes'>" . sprintf(T_('Transfert de <span class="porteDocumentsNom">%1$s</span> complété.'), $nomFichier) . "</p>";
		}

		else
		{
			echo "<p class='erreur'>" . sprintf(T_('Erreur de transfert de <span class="porteDocumentsNom">%1$s</span>.'), $nomFichier) . "</p>";
		}
	}
}

if (isset($erreur))
{
	echo "<p class='erreur'>" . sprintf(T_('Erreur de transfert de <span class="porteDocumentsNom">%1$s</span>.'), $nomFichier) . "</p>\n\n<ul>\n", $erreur, "</ul>\n";
}
?>
</div><!-- /boiteMessages -->

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post"><div>
<div class="boite">
<h2 id="fichiersEtDossiers"><?php echo T_("Liste des fichiers et dossiers"); ?></h2>

<div class="boite2">
<h3><?php echo T_("Affichage détaillé"); ?></h3>

<?php

if (isset($_GET['action']))
{
	if ($_GET['action'] == 'parcourir')
	{
		if (!is_dir($_GET['valeur']))
		{
			$erreur .= "<li class='erreur'>" . sprintf(T_('Impossible d\'avoir accès au dossier %1$s'), $_GET['valeur']) . "</li>\n";
		}

		else
		{
			$liste = adminParcourirTout($_GET['valeur'], $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
			ksort($liste);

			echo "<ul>\n";
			foreach ($liste as $cle => $valeur)
			{
				echo "<li class='porteDocumentsListeDossiers'>" . T_("Dossier") . " <span class='porteDocumentsNom'>$cle</span><ul>\n";
				$cle = array();
				foreach ($valeur as $valeur2)
				{
					$cle[] = $valeur2;
				}

				natcasesort($cle);

				foreach ($cle as $valeur3)
				{
					echo "<li>$valeur3</li>\n";
				}
				echo "</ul></li>\n";
			}
			echo "</ul>\n";
		}
	}
}
?>
</div><!-- /boite2 -->

<div class="boite2">
<h3><?php echo T_("Affichage général des dossiers"); ?></h3>

<?php
$liste2 = adminParcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres);
asort($liste2);
echo "<ul>\n";
foreach ($liste2 as $valeur)
{
	echo "<li><a href=\"$action" . $symboleUrl . "action=parcourir&amp;valeur=$valeur#fichiersEtDossiers\">" . T_("Parcourir") . "</a> <span class='porteDocumentsSep'>|</span> <a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$valeur#messagesPorteDocuments\">" . T_("Renommer") . "</a> <span class='porteDocumentsSep'>|</span> " . T_("Supprimer") . " <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$valeur\" /> <span class='porteDocumentsSep'>|</span> <span class='porteDocumentsNom'>$valeur</span></li>\n";
}
echo "</ul>\n";
?>
</div><!-- /boite2 -->

</div><!-- /boite -->

<div class="boite">
<h2><?php echo T_("Supprimer"); ?></h2>

<p><?php echo T_("Pour supprimer des fichiers, cocher la case correspondante et cliquer ensuite sur le bouton ci-dessous."); ?></p>

<input type="submit" value="<?php echo T_('Supprimer'); ?>" />
</div><!-- /boite -->
</div></form>

<div class="boite">
<h2><?php echo T_("Ajouter un fichier"); ?></h2>

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post" enctype="multipart/form-data"><div>
<label><?php echo T_("Fichier:"); ?></label> <input type="file" name="fichier" size="25"/><br /><br />
<label><?php echo T_("Dossier:"); ?></label> <select name="rep" size="1">
<?php
$liste = adminParcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres);
asort($liste);
foreach ($liste as $valeur)
{
	echo '<option value="' . $valeur . '">' . $valeur . "</option>\n";
}
?>
</select><br /><br />
<input type="submit" value="<?php echo T_('Ajouter'); ?>" />
</div></form>
</div><!-- /boite -->

<div class="boite">
<h2><?php echo T_("Créer un fichier ou un dossier"); ?></h2>

<p><?php echo T_("Saisir le nom du nouveau fichier ou dossier à créer. Mettre le chemin dans le nom. Exemples:"); ?></p>

<ul>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau-dossier</span></li>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau-fichier.txt</span></li>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau-dossier/nouveau-fichier.txt</span></li>
</ul>

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post"><div>
<label><?php echo T_("Nom:"); ?></label> <input type="text" name="porteDocumentsFichierCreeNom" size="50" value="<?php echo $dossierRacine . '/'; ?>" /><br /><br />
<label><?php echo T_("Type:"); ?></label> <select name="porteDocumentsFichierCreeType" size="1">
<option value="Dossier"><?php echo T_("Dossier"); ?></option>
<option value="Fichier"><?php echo T_("Fichier"); ?></option>
</select><br /><br />
<input type="submit" name="porteDocumentsCreer" value="<?php echo T_('Créer'); ?>" />
</div></form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
