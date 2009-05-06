<?php
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

// Niveaux en HTML des titres suivant le titre principal (de 1 à 4)
$niveauTitreSuivant = $niveauTitreScript + 1;
$niveauTitreSuivant2 = $niveauTitreScript + 2;
?>

<h<?php echo $niveauTitreScript; ?>>Porte-documents</h<?php echo $niveauTitreScript; ?>>

<div class="porteDocumentsBoite">
<h<?php echo $niveauTitreSuivant; ?>>Information</h<?php echo $niveauTitreSuivant; ?>>

<ul>
	<li><strong>Taille maximale d'un fichier:</strong> <?php echo $tailleMaxFichiers; ?> octets (<?php echo octetsVersMo($tailleMaxFichiers); ?> Mo).</li>
	<li><strong>Extensions permises:</strong>
	<?php
	if ($filtreExtensions)
	{
		foreach ($extensionsPermises as $ext)
		{
			echo "$ext ";
		}
		?>
		<br /><em>Si vous voulez télécharger un fichier avec une extension qui n'est pas dans la liste, en faire la demande à la personne administratrice de ce site, ou si vous avez les droits d'administration, modifiez le fichier de configuration.</em></li>
		<?php
	}

	else
	{
		echo 'toutes.</li>';
	}
	?>
</ul>

</div>

<div id="porteDocumentsBoiteMessages" class="porteDocumentsBoite">
<h<?php echo $niveauTitreSuivant; ?> id="messagesPorteDocuments">Messages d'avancement, de confirmation ou d'erreur</h<?php echo $niveauTitreSuivant; ?>>

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
				echo "<li class='succes'>Suppression de <span class='porteDocumentsNom'>$valeur</span> effectuée.</li>\n";
			}

			else
			{
				echo "<li class='erreur'>Impossible de supprimer le fichier <span class='porteDocumentsNom'>$valeur</span>.</li>";
			}
		}

		elseif (file_exists($valeur) && is_dir($valeur))
		{
			if (rmdir($valeur))
			{
				echo "<li class='succes'>Suppression de <span class='porteDocumentsNom'>$valeur</span> effectuée.</li>\n";
			}

			else
			{
				echo "<li class='erreur'>Impossible de supprimer le dossier <span class='porteDocumentsNom'>$valeur</span>. Vérifiez qu'il est vide.</li>";
			}
		}
		elseif (!file_exists($valeur))
		{
			echo "<li class='erreur'><span class='porteDocumentsNom'>$valeur</span> n'existe pas.</li>\n";
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
		echo "<h$niveauTitreSuivant2>Insctructions de renommage</h$niveauTitreSuivant2>";
		echo "<ul>\n";
		echo "<li>Pour renommer <span class='porteDocumentsNom'>$ancienNom</span>, taper le nouveau nom dans le champ.</li>";
		echo "<li>Ne pas oublier de mettre le chemin dans le nom.";
		echo "<li>Exemples:";
		echo "<ul>\n";
		echo "<li><span class='porteDocumentsNom'>$dossierRacine/nouveau_nom_dossier</span></li>";
		echo "<li><span class='porteDocumentsNom'>$dossierRacine/nouveau_nom.txt</span></li>";
		echo "<li><span class='porteDocumentsNom'>fichiers/nouveau_nom_dossier/nouveau_nom_fichier.ext</span>.</li>";
		echo "</ul></li>";
		echo "<li>Important: ne pas mettre de barre oblique / dans le nouveau nom du fichier. N'utiliser ce signe que pour marquer le chemin vers le fichier.</li>";
		echo "</ul>\n";
		?>
		<form action="<?php echo $action; ?>" method="post">
		<div>
		<input type="hidden" name="porteDocumentsAncienNom" value="<?php echo $ancienNom; ?>" /> <input type="text/css" name="porteDocumentsNouveauNom" value="<?php echo $ancienNom; ?>" size="50" />
		<input type="submit" value="Renommer" />
		</div>
		</form>
		<?php
	}
	
	// Action Modifier
	if ($_GET['action'] == 'modifier')
	{
		echo "<h$niveauTitreSuivant2>Insctructions de modification</h$niveauTitreSuivant2>";
		echo "<p>Le fichier est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications».</p>";
		?>
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
		<div>
		<?php
		$fic = fopen($_GET['valeur'], 'r');
		$contenuFichier = fread($fic, filesize($_GET['valeur']));
		fclose($fic);
		?>
		<textarea name="porteDocumentsContenuFichier"><?php echo $contenuFichier; ?></textarea>
		<input type="hidden" name="porteDocumentsModifierNom" value="<?php echo $_GET['valeur']; ?>" />
		<input type="submit" value="Sauvegarder les modifications" />
		
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
		<div>
		<input type="submit" name="porteDocumentsModifierAnnuler" value="Annuler" />
		<input type="hidden" name="porteDocumentsModifierNom" value="<?php echo $_GET['valeur']; ?>" />
		</div>
		</form>
		
		</div>
		</form>
		<?php
	}
}

if (isset($_POST['porteDocumentsModifierAnnuler']))
{
	echo "<p class='succes'>Aucune modification apportée au fichier " . $_POST['porteDocumentsModifierNom'] . ".</p>";
}
elseif (isset($_POST['porteDocumentsContenuFichier']))
{
	$messageErreurModifier = '';
	$messageErreurModifier .= "<p class='erreur'>Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.</p>\n";
	$messageErreurModifier .= '<p><textarea name="porteDocumentsContenuFichier" readonly="readonly">';
	$messageErreurModifier .= formateTexte($_POST['porteDocumentsContenuFichier']);
	$messageErreurModifier .= "</textarea></p>\n";

	echo "<ul>\n";

	if (is_writable($_POST['porteDocumentsModifierNom']))
	{
		if (!$fic = fopen($_POST['porteDocumentsModifierNom'], 'w'))
		{
			echo "<li><p class='erreur'>Le fichier <span class='porteDocumentsNom'>" . $_POST['porteDocumentsModifierNom'] . "</span> n'a pas pu être ouvert.</p>\n$messageErreurModifier</li>\n";
		}
	
		if (fwrite($fic, formateTexte($_POST['porteDocumentsContenuFichier'])) === FALSE)
		{
			echo "<li><p class='erreur'>Impossible d'écrire dans le fichier <span class='porteDocumentsNom'>" . $_POST['porteDocumentsModifierNom'] . "</span>.</p>\n$messageErreurModifier</li>\n";
		}
	
		echo "<li class='succes'>Modification du fichier <span class='porteDocumentsNom'>" . $_POST['porteDocumentsModifierNom'] . "</span> effectuée.</li>\n";
	
		fclose($fic);
	}
	else
	{
		echo "<li><p class='erreur'>Le fichier <span class='porteDocumentsNom'>" . $_POST['porteDocumentsModifierNom'] . "</span> n'est pas accessible en écriture.</p>\n$messageErreurModifier</li>\n";
	}

	echo "</ul>\n";
}

if (isset($_POST['porteDocumentsNouveauNom']))
{
	$ancienNom = $_POST['porteDocumentsAncienNom'];
	$nouveauNom = $_POST['porteDocumentsNouveauNom'];

	echo "<ul>\n";
	if (file_exists($ancienNom))
	{
		if (rename($ancienNom, $nouveauNom))
		{
			echo "<li class='succes'>Renommage de <span class='porteDocumentsNom'>$ancienNom</span> en <span class='porteDocumentsNom'>$nouveauNom</span> effectué.</li>\n";
		}

		else
		{
			echo "<li class='erreur'>Renommage de <span class='porteDocumentsNom'>$ancienNom</span> en <span class='porteDocumentsNom'>$nouveauNom</span> impossible.</li>\n";
		}
	}

	elseif (!file_exists($ancienNom))
	{
		echo "<li class='erreur'><span class='porteDocumentsNom'>$ancienNom</span> n'existe pas. Renommage en <span class='porteDocumentsNom'>$nouveauNom</span> impossible.</li>\n";
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
				echo "<li class='succes'>Création du fichier <span class='porteDocumentsNom'>$fichierCreeNom</span> effectuée.</li>\n";
			}

			else
			{
				echo "<li class='erreur'>Impossible de créer le fichier <span class='porteDocumentsNom'>$fichierCreeNom</span>.</li>\n";
			}
		}

		elseif ($fichierCreeType == 'Dossier')
		{
			if (mkdir($fichierCreeNom))
			{
				echo "<li class='succes'>Création du dossier <span class='porteDocumentsNom'>$fichierCreeNom</span> effectuée.</li>\n";
			}

			else
			{
				echo "<li class='erreur'>Impossible de créer le dossier <span class='porteDocumentsNom'>$fichierCreeNom</span>.</li>\n";
			}
		}
	}

	elseif (file_exists($fichierCreeNom))
	{
		echo "<li class='erreur'><span class='porteDocumentsNom'>$fichierCreeNom</span> existe déjà.</li>\n";
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
			$erreur .= "<li class='erreur'>Veuillez sélectionner un bon format de fichier ou demandez à ce que l'extension de votre fichier soit ajoutée dans la liste.</li>\n";
		}
	}

	if (file_exists($_FILES['fichier']['tmp_name']) && filesize($_FILES['fichier']['tmp_name']) > $tailleMaxFichiers)
	{
		$erreur .= "<li class='erreur'>Votre fichier doit faire moins de $tailleMaxFichiers octets (" . octetsVersMo($tailleMaxFichiers) . " Mo).</li>\n";
	}

	if ($filtreNom)
	{
		if (!ereg($motifNom, $nomFichier))
		{
			$erreur .= "<li class='erreur'>Le nom de votre fichier risque de mal s'afficher dans une adresse html. Veuillez le renommer en n'utilisant que les caractères suivants:<br />$motifNom2<br />(les espaces sont automatiquement remplacées par des caractères de soulignement _).</li>\n";
		}
	}

	if (file_exists($rep . '/' . $nomFichier))
	{
		$erreur .= "<li class='erreur'>Un fichier existe déjà avec ce nom dans le dossier sélectionné.</li>";
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
			echo "<p class='succes'>Transfert de <span class='porteDocumentsNom'>$nomFichier</span> complété.</p>";
		}

		else
		{
			echo "<p class='erreur'>Erreur de transfert de <span class='porteDocumentsNom'>$nomFichier</span>.</p>";
		}
	}
}

if (isset($erreur))
{
	echo "<p class='erreur'>Erreur de transfert de <span class='porteDocumentsNom'>$nomFichier</span>.</p>\n\n<ul>\n", $erreur, "</ul>\n";
}
?>
</div>

<div class="porteDocumentsBoite">
<h<?php echo $niveauTitreSuivant; ?> id="fichiersEtDossiers">Fichiers et dossiers</h<?php echo $niveauTitreSuivant; ?>>

<div class="porteDocumentsBoite2">
<h<?php echo $niveauTitreSuivant2; ?>>Affichage détaillé</h<?php echo $niveauTitreSuivant2; ?>>

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
<div>
<?php

if (isset($_GET['action']))
{
	if ($_GET['action'] == 'parcourir')
	{
		if (!is_dir($_GET['valeur']))
		{
			$erreur .= "<li class='erreur'>Impossible d'avoir accès au dossier " . $_GET['valeur'] . "</li>\n";
		}

		else
		{
			$liste = parcourirTout($_GET['valeur'], $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
			ksort($liste);

			echo "<ul>\n";
			foreach ($liste as $cle => $valeur)
			{
				echo "<li class='porteDocumentsListeDossiers'>Dossier <span class='porteDocumentsNom'>$cle</span><ul>\n";
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

<?php
//Tout déplier
/*$liste = parcourirTout($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
ksort($liste);

echo "<ul>\n";
foreach ($liste as $cle => $valeur)
{
	echo "<li>Dossier <span class='porteDocumentsNom'>$cle</span><ul>\n";
	$cle=array();
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
echo "</ul>\n";*/
?>
</div>

<div class="porteDocumentsBoite2">
<h<?php echo $niveauTitreSuivant2; ?>>Affichage général des dossiers</h<?php echo $niveauTitreSuivant2; ?>>

<?php
$liste2 = parcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres);
asort($liste2);
echo "<ul>\n";
foreach ($liste2 as $valeur)
{
	echo "<li><a href=\"$action" . $symboleUrl . "action=parcourir&amp;valeur=$valeur#fichiersEtDossiers\">Parcourir</a> <span class='porteDocumentsSep'>|</span> <a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$valeur#messagesPorteDocuments\">Renommer</a> <span class='porteDocumentsSep'>|</span> Supprimer <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$valeur\" /> <span class='porteDocumentsSep'>|</span> <span class='porteDocumentsNom'>$valeur</span></li>\n";
}
echo "</ul>\n";
?>
</div>

</div>

<div class="porteDocumentsBoite">
<h<?php echo $niveauTitreSuivant; ?>>Tâches</h<?php echo $niveauTitreSuivant; ?>>

<div class="porteDocumentsBoite2">
<h<?php echo $niveauTitreSuivant2; ?>>Supprimer</h<?php echo $niveauTitreSuivant2; ?>>

<p>Pour supprimer des fichiers, cocher la case correspondante et cliquer ensuite sur le bouton ci-dessous.</p>

<input type="submit" value="Supprimer" />
</div>
</form>
</div>

<div class="porteDocumentsBoite2">
<h<?php echo $niveauTitreSuivant2; ?>>Ajouter un fichier</h<?php echo $niveauTitreSuivant2; ?>>

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post" enctype="multipart/form-data">
<div>
<label>Fichier:</label> <input type="file" name="fichier" size="25"/><br /><br />
<label>Dossier:</label> <select name="rep" size="1in/porte-documen">
<?php
$liste = parcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres);
asort($liste);
foreach ($liste as $valeur)
{
	echo '<option value="' . $valeur . '">' . $valeur . "</option>\n";
}
?>
</select><br /><br />
<input type="submit" value="Ajouter" />
</div>
</form>
</div>

<div class="porteDocumentsBoite2">
<h<?php echo $niveauTitreSuivant2; ?>>Créer un fichier ou un dossier</h<?php echo $niveauTitreSuivant2; ?>>

<p>Taper le nom du nouveau fichier ou dossier à créer. Mettre le chemin dans le nom. Exemples:</p>

<ul>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau_dossier</span></li>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau_fichier.ext</span></li>
	<li><span class='porteDocumentsNom'><?php echo $dossierRacine; ?>/nouveau_dossier/nouveau_fichier.ext</span></li>
</ul>

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
<div>
<label>Nom:</label> <input type="text" name="porteDocumentsFichierCreeNom" size="50" value="<?php echo $dossierRacine . '/'; ?>" /><br /><br />
<label>Type:</label> <select name="porteDocumentsFichierCreeType" size="1">
<option value="Dossier">Dossier</option>
<option value="Fichier">Fichier</option>
</select><br /><br />
<input type="submit" name="porteDocumentsCreer" value="Créer" />
</div>
</form>
</div>

</div>

<?php include 'inc/dernier.inc.php'; ?>
