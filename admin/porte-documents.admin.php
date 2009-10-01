<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Porte-documents");
include 'inc/premier.inc.php';

if (isset($_GET['valeur']))
{
	$getValeur = sansEchappement($_GET['valeur']);
}

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
		<br /><em><?php echo T_("Si vous voulez télécharger un fichier avec une extension qui n'est pas dans la liste, en faire la demande à la personne administratrice de ce site, ou si vous avez les droits d'administration, éditez le fichier de configuration."); ?></em></li>
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
		$valeur = sansEchappement($valeur);
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
		$ancienNom = $getValeur;
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
	
	// Action Éditer
	if ($_GET['action'] == 'editer')
	{
		echo "<h3>" . T_("Insctructions d'édition") . "</h3>";
		if (file_exists($getValeur))
		{
			echo "<p>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications».'), $getValeur) . "</p>";
		}
		else
		{
			echo "<p>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> n\'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisi.'), $getValeur) . "</p>";
		}
		
		?>
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post"><div>
		<?php
		clearstatcache();
		if (file_exists($getValeur) && filesize($getValeur))
		{
			$fic = fopen($getValeur, 'r');
			$contenuFichier = fread($fic, filesize($getValeur));
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
		
		if (adminEstIE())
		{
			$imageRedimensionner = '';
		}
		else
		{
			$imageRedimensionner = '<img src="fichiers/redimensionner.png" alt="' . T_('Appuyez sur Maj, cliquez sur l\'image et glissez-là pour redimensionner le champ de saisie') . '" title="' . T_('Appuyez sur Maj, cliquez sur l\'image et glissez-là pour redimensionner le champ de saisie') . '" width="41" height="20" />';
		}
		?>
		<div id="redimensionnable"><textarea id="code" cols="80" rows="25" <?php echo $style; ?> name="porteDocumentsContenuFichier"><?php echo $contenuFichier; ?></textarea><?php echo $imageRedimensionner; ?></div>
		<input type="hidden" name="porteDocumentsEditerNom" value="<?php echo $getValeur; ?>" />
		<input type="submit" value="<?php echo T_('Sauvegarder les modifications'); ?>" />
		
		<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post">
		<div>
		<input type="submit" name="porteDocumentsEditerAnnuler" value="<?php echo T_('Annuler'); ?>" />
		<input type="hidden" name="porteDocumentsEditerNom" value="<?php echo $getValeur; ?>" />
		</div></form>
		
		</div></form>
		<?php
	}
}

if (isset($_POST['porteDocumentsEditerAnnuler']))
{
	$porteDocumentsEditerNom = sansEchappement($_POST['porteDocumentsEditerNom']);
	
	echo "<p class='succes'>" . sprintf(T_('Aucune modification apportée au fichier %1$s.'), $porteDocumentsEditerNom) . "</p>";
}
elseif (isset($_POST['porteDocumentsContenuFichier']))
{
	$porteDocumentsEditerNom = sansEchappement($_POST['porteDocumentsEditerNom']);
	
	$messageErreurEditer = '';
	$messageErreurEditer .= "<p class='erreur'>" . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
	$messageErreurEditer .= '<p><textarea class="consulterModifications" name="porteDocumentsContenuFichier" readonly="readonly">';
	$messageErreurEditer .= adminFormateTexte($_POST['porteDocumentsContenuFichier']);
	$messageErreurEditer .= "</textarea></p>\n";
	
	$messageErreurEditerAffiche = FALSE;

	echo "<ul>\n";

	if (!$fic = fopen($porteDocumentsEditerNom, 'w'))
	{
		echo "<li><p class='erreur'>" . sprintf(T_('Le fichier <span class="porteDocumentsNom">%1$s</span> n\'a pas pu être ouvert.'), $porteDocumentsEditerNom) . "</p>\n$messageErreurEditer</li>\n";
		$messageErreurEditerAffiche = TRUE;
	}

	if (fwrite($fic, adminFormateTexte($_POST['porteDocumentsContenuFichier'])) === FALSE)
	{
		echo "<li><p class='erreur'>" . sprintf(T_('Impossible d\'écrire dans le fichier <span class="porteDocumentsNom">%1$s</span>.'), $porteDocumentsEditerNom) . "</p>\n";
		if (!$messageErreurEditerAffiche)
		{
			echo $messageErreurEditer;
			$messageErreurEditerAffiche = TRUE;
		}
		echo "</li>\n";
	}
	
	if (!$messageErreurEditerAffiche)
	{
		echo "<li class='succes'>" . sprintf(T_('Édition du fichier <span class="porteDocumentsNom">%1$s</span> effectuée. <a href="%2$s">Éditer à nouveau</a>.'), $porteDocumentsEditerNom, 'porte-documents.admin.php?action=editer&valeur=' . $porteDocumentsEditerNom . '#messagesPorteDocuments') . "</li>\n";
	}
	else
	{
		echo "<li>" . sprintf(T_('<a href="%1$s">Tenter à nouveau d\'éditer le fichier.</a>'), 'porte-documents.admin.php?action=editer&valeur=' . $porteDocumentsEditerNom . '#messagesPorteDocuments') . "</li>\n";
	}

	fclose($fic);

	echo "</ul>\n";
}

if (isset($_POST['porteDocumentsNouveauNom']))
{
	$ancienNom = sansEchappement($_POST['porteDocumentsAncienNom']);
	$nouveauNom = sansEchappement($_POST['porteDocumentsNouveauNom']);
	if (isset($_POST['porteDocumentsRenommerDupliquer']) && $_POST['porteDocumentsRenommerDupliquer'] == 'dupliquer')
	{
		$dupliquer = TRUE;
	}
	else
	{
		$dupliquer = FALSE;
	}

	echo "<ul>\n";
	if (file_exists($ancienNom) && !file_exists($nouveauNom))
	{
		if ($dupliquer)
		{
			if (!file_exists(dirname($nouveauNom)))
			{
				if (!mkdir(dirname($nouveauNom), 0755, TRUE))
				{
					echo "<li class='erreur'>" . sprintf(T_('Création du dossier <span class="porteDocumentsNom">%1$s</span> impossible.'), dirname($nouveauNom)) . "</li>\n";
				}
			}
			
			if (file_exists(dirname($nouveauNom)))
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
		}
		else
		{
			if (!file_exists(dirname($nouveauNom)))
			{
				if (!mkdir(dirname($nouveauNom), 0755, TRUE))
				{
					echo "<li class='erreur'>" . sprintf(T_('Création du dossier <span class="porteDocumentsNom">%1$s</span> impossible.'), dirname($nouveauNom)) . "</li>\n";
				}
			}
			
			if (file_exists(dirname($nouveauNom)))
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
	}

	else
	{
		if (!file_exists($ancienNom))
		{
			echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> n\'existe pas. Renommage en <span class="porteDocumentsNom">%2$s</span> impossible.'), $ancienNom, $nouveauNom) . "</li>\n";
		}
		
		if (file_exists($nouveauNom))
		{
			echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> existe déjà. Renommage de <span class="porteDocumentsNom">%2$s</span> impossible.'), $nouveauNom, $ancienNom) . "</li>\n";
		}
	}
	echo "</ul>\n";
}

if (isset($_POST['porteDocumentsCreer']))
{
	$fichierCreeNom = sansEchappement($_POST['porteDocumentsFichierCreeNom']);

	if (!preg_match("|^$dossierRacine/|i", $fichierCreeNom))
	{
		$fichierCreeNomTemp = "$dossierRacine/$fichierCreeNom";
		unset($fichierCreeNom);
		$fichierCreeNom = $fichierCreeNomTemp;
	}

	$fichierCreeType = $_POST['porteDocumentsFichierCreeType'];

	echo "<ul>\n";
	if (!file_exists($fichierCreeNom))
	{
		if ($fichierCreeType == 'Dossier')
		{
			if (mkdir($fichierCreeNom, 0755, TRUE))
			{
				echo "<li class='succes'>" . sprintf(T_('Création du dossier <span class="porteDocumentsNom">%1$s</span> effectuée.'), $fichierCreeNom) . "</li>\n";
			}

			else
			{
				echo "<li class='erreur'>" . sprintf(T_('Impossible de créer le dossier <span class="porteDocumentsNom">%1$s</span>.'), $fichierCreeNom) . "</li>\n";
			}
		}
		
		elseif ($fichierCreeType == 'FichierVide' || $fichierCreeType == 'FichierModele')
		{
			$page = basename($fichierCreeNom);
			$cheminPage = dirname($fichierCreeNom);
			if ($cheminPage == '../.')
			{
				$cheminPage = '..';
			}
			
			if (!file_exists($cheminPage))
			{
				if (!mkdir($cheminPage, 0755, TRUE))
				{
					echo "<li class='erreur'>" . sprintf(T_('Impossible de créer le dossier <span class="porteDocumentsNom">%1$s</span>.'), $cheminPage) . "</li>\n";
				}
			}
			
			if (file_exists($cheminPage))
			{
				if (touch($fichierCreeNom))
				{
					// Ouverture de <li class='succes'>
					echo "<li class='succes'>";
					echo sprintf(T_('Création du fichier <span class="porteDocumentsNom">%1$s</span> effectuée.'), $fichierCreeNom);
					
					if ($fichierCreeType == 'FichierModele')
					{
						echo sprintf(T_('Vous pouvez <a href="%1$s">l\'éditer</a> ou <a href="%2$s">l\'afficher</a>.'), 'porte-documents.admin.php?action=editer&valeur=' . $fichierCreeNom . '#messagesPorteDocuments', $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
						
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

						if ($fic = fopen($cheminPage . '/' . $page, 'a'))
						{
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$contenu .= '$baliseTitle = "Titre (contenu de la balise `title`)";' . "\n";
							$contenu .= '$description = "Description de la page";' . "\n";
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							$contenu .= '<h1>Titre de la page</h1>' . "\n";
							$contenu .= "\n";
							$contenu .= "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.</p>\n";
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
							fclose($fic);
						}
						else
						{
							echo "<li class='erreur'>" . sprintf(T_('Impossible d\'ajouter un modèle de page web dans le fichier %1$s.'), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>";
						}
					}
					else
					{
						echo ' <a href="porte-documents.admin.php?action=editer&valeur=' . $fichierCreeNom . '#messagesPorteDocuments">' . T_("Vous pouvez l'éditer.") . "</a>";
					}
					
					// Fermeture de <li class='succes'>
					echo "</li>\n";
				}
			
				else
				{
					echo "<li class='erreur'>" . sprintf(T_('Impossible de créer le fichier <span class="porteDocumentsNom">%1$s</span>.'), $fichierCreeNom) . "</li>\n";
				}
			}
		}
	}
	
	else
	{
		echo "<li class='erreur'>" . sprintf(T_('<span class="porteDocumentsNom">%1$s</span> existe déjà.'), $fichierCreeNom) . "</li>\n";
	}
	echo "</ul>\n";
}

if (isset($_FILES['fichier']))
{
	$erreur = '';
	$rep = sansEchappement($_POST['rep']);
	$nomFichier = sansEchappement(basename($_FILES['fichier']['name']));

	// Affichage du motif dans le message d'erreur
	$motifNom2 = substr($motifNom, 2, -3);
	$motifNom2 = sansEchappement($motifNom2);

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
		if (!preg_match("/$motifNom/", $nomFichier))
		{
			$erreur .= "<li class='erreur'>" . sprintf(T_('Le nom de votre fichier risque de mal s\'afficher dans une adresse html. Veuillez le renommer en n\'utilisant que les caractères suivants:<br />%1$s<br />(les espaces sont automatiquement remplacées par des caractères de soulignement _).'), $motifNom2) . "</li>\n";
		}
	}

	if (file_exists($rep . '/' . $nomFichier))
	{
		$erreur .= "<li class='erreur'>" . T_("Un fichier existe déjà avec ce nom dans le dossier sélectionné.") . "</li>";
	}

	if (empty($erreur))
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

if (!empty($erreur))
{
	echo "<p class='erreur'>" . sprintf(T_('Erreur de transfert de <span class="porteDocumentsNom">%1$s</span>.'), $nomFichier) . "</p>\n\n<ul>\n", $erreur, "</ul>\n";
}
?>
</div><!-- /boiteMessages -->

<form action="<?php echo $action; ?>#messagesPorteDocuments" method="post"><div>
<div class="boite">
<h2 id="fichiersEtDossiers"><?php echo T_("Liste des fichiers et dossiers"); ?></h2>

<?php

if (isset($_GET['action']) && $_GET['action'] == 'parcourir')
{
	if (!isset($erreur))
	{
		$erreur = '';
	}
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . sprintf(T_("Contenu du dossier %1\$s"), '<span class="porteDocumentsNom">' . $getValeur . '</span>') . "</h3>\n";

	if (!is_dir($getValeur))
	{
		$erreur .= "<li class='erreur'>" . sprintf(T_('Impossible d\'avoir accès au dossier %1$s'), $getValeur) . "</li>\n";
	}

	else
	{
		$liste = adminParcourirTout($getValeur, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
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
	
	echo "</div><!-- /boite2 -->\n";
}
?>

<div class="boite2">
<h3><?php echo T_("Liste des dossiers"); ?></h3>

<?php
$liste2 = adminParcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres);
asort($liste2);
echo "<ul>\n";
foreach ($liste2 as $valeur)
{
	echo "<li><a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$valeur#messagesPorteDocuments\">" . T_("Renommer/Déplacer") . "</a> <span class='porteDocumentsSep'>|</span> " . T_("Supprimer") . " <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$valeur\" /> <span class='porteDocumentsSep'>|</span> <a href=\"$action" . $symboleUrl . "action=parcourir&amp;valeur=$valeur#fichiersEtDossiers\"><span class='porteDocumentsNom'>$valeur</span></a></li>\n";
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
<label><?php echo T_("Type:"); ?></label>
<select name="porteDocumentsFichierCreeType" size="1">
<option value="Dossier"><?php echo T_("Dossier"); ?></option>
<option value="FichierModele"><?php echo T_("Fichier modèle de page web"); ?></option>
<option value="FichierVide"><?php echo T_("Fichier vide"); ?></option>
</select><br /><br />
<input type="submit" name="porteDocumentsCreer" value="<?php echo T_('Créer'); ?>" />
</div></form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
