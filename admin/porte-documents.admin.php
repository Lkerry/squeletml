<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Porte-documents");
include 'inc/premier.inc.php';

if (isset($_GET['valeur']))
{
	$getValeur = securiseTexte($_GET['valeur']);
}

// Création de la variable $tableauFiltresDossiers
if ($typeFiltreDossiers == 'dossiersPermis' || $typeFiltreDossiers == 'dossiersExclus'
	&& !empty($filtreDossiers))
{
	$tableauFiltresDossiers = explode('|', $filtreDossiers);
}
else
{
	$tableauFiltresDossiers = array ();
}

// Motif à rechercher dans les noms de fichiers téléchargés
$motifNom = "^[- \+\.\(\)_0-9a-zA-Z]*$";

echo '<h1>' . T_("Porte-documents") . "</h1>\n";

echo '<div class="boite">' . "\n";
echo '<h2>' . T_("Information") . "</h2>\n";

echo "<ul>\n";
echo '<li>' . sprintf(T_("<strong>Taille maximale d'un transfert de fichier:</strong> %1\$s octets (%2\$s Mio)."), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)) . "</li>\n";
echo '<li><strong>' . T_("Extensions permises:") . "</strong>\n";
if ($filtreExtensions)
{
	foreach ($extensionsPermises as $ext)
	{
		echo "$ext ";
	}
	echo "<br />\n<em>" . T_("Si vous voulez télécharger un fichier avec une extension qui n'est pas dans la liste, en faire la demande à la personne administratrice de ce site, ou si vous avez les droits d'administration, éditez le fichier de configuration.") . "</em></li>\n";
}
else
{
	echo T_("toutes.") . "</li>\n";
}

echo "</ul>\n";
echo "</div><!-- /class=boite -->\n";

echo '<div id="boiteMessages" class="boite">' . "\n";
echo '<h2 id="messagesPorteDocuments">' . T_("Messages d'avancement, de confirmation ou d'erreur") . "</h2>\n";

########################################################################
##
## Supprimer
##
########################################################################

if (isset($_POST['porteDocumentsSupprimer']))
{
	$messagesScript = array ();
	$fichiersAsupprimer = securiseTexte($_POST['porteDocumentsFichiersAsupprimer']);
	
	if (!empty($fichiersAsupprimer))
	{
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			$fichierAsupprimer = securiseTexte($fichierAsupprimer);
			if (file_exists($fichierAsupprimer) && !is_dir($fichierAsupprimer))
			{
				if (@unlink($fichierAsupprimer))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Suppression du fichier %1\$s effectuée."), "<code>$fichierAsupprimer</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de supprimer le fichier %1\$s."), "<code>$fichierAsupprimer</code>") . "</li>\n";
				}
			}
			elseif (file_exists($fichierAsupprimer) && is_dir($fichierAsupprimer) && basename($fichierAsupprimer) != '.' && basename($fichierAsupprimer) != '..')
			{
				if (!dossierEstVide($fichierAsupprimer))
				{
					$dossiersAtraiter = array ($fichierAsupprimer);
				
					while (!empty($dossiersAtraiter))
					{
						foreach ($dossiersAtraiter as $dossierAtraiter)
						{
							if (basename($dossierAtraiter) != '.' && basename($dossierAtraiter) != '..')
							{
								if ($dossier = @opendir($dossierAtraiter))
								{
									while (($fichier = @readdir($dossier)) !== FALSE)
									{
										if (!is_dir("$dossierAtraiter/$fichier"))
										{
											if (@unlink("$dossierAtraiter/$fichier"))
											{
												$messagesScript[] = '<li>' . sprintf(T_("Suppression du fichier %1\$s effectuée."), "<code>$dossierAtraiter/$fichier</code>") . "</li>\n";
											}
											else
											{
												$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de supprimer le fichier %1\$s."), "<code>$dossierAtraiter/$fichier</code>") . "</li>\n";
											}
										}
										else
										{
											$dossiersAtraiter[] = "$dossierAtraiter/$fichier";
										}
									}
							
									closedir($dossier);
							
									if (dossierEstVide($dossierAtraiter))
									{
										if (@rmdir($dossierAtraiter))
										{
											$messagesScript[] = '<li>' . sprintf(T_("Suppression du dossier %1\$s effectuée."), "<code>$dossierAtraiter</code>") . "</li>\n";
										}
										else
										{
											$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de supprimer le dossier %1\$s."), "<code>$dossierAtraiter</code>") . "</li>\n";
										}
									}
								}
								else
								{
									$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible d'avoir accès au dossier %1\$s."), "<code>$dossierAtraiter</code>") . "</li>\n";
								}
							}
						
							unset($dossiersAtraiter[array_search($dossierAtraiter, $dossiersAtraiter)]);
						}
					}
				}
				elseif (@rmdir($fichierAsupprimer))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Suppression du dossier %1\$s effectuée."), "<code>$fichierAsupprimer</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de supprimer le dossier %1\$s. Vérifiez entre autres que vous avez les droits."), "<code>$fichierAsupprimer</code>") . "</li>\n";
				}
			}
		}
	}
	else
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	
	echo adminMessagesScript(T_("Suppression de fichiers"), $messagesScript);
}

########################################################################
##
## Modifier les permissions
##
########################################################################

if (isset($_POST['porteDocumentsPermissions']))
{
	$messagesScript = array ();
	$fichiersAmodifier = securiseTexte($_POST['porteDocumentsPermissionsFichiers']);
	
	if (isset($_POST['porteDocumentsPermissionsRecursives']) && $_POST['porteDocumentsPermissionsRecursives'] == 'permissionsRecursives')
	{
		$recursivite = TRUE;
	}
	else
	{
		$recursivite = FALSE;
	}
	
	if (isset($_POST['porteDocumentsPermissionsValeur']))
	{
		$permissions = securiseTexte($_POST['porteDocumentsPermissionsValeur']);
	}
	else
	{
		$permissions = '';
	}
	
	if (empty($fichiersAmodifier))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la modification des permissions.") . "</li>\n";
	}
	elseif (empty($permissions))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucune permission spécifiée.") . "</li>\n";
	}
	elseif (!preg_match('/^[[:digit:]]{4}$/', $permissions))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Les permissions spécifiées ne sont pas valides.") . "</li>\n";
	}
	else
	{
		foreach ($fichiersAmodifier as $fichierAmodifier)
		{
			$fichierAmodifier = securiseTexte($fichierAmodifier);
			if ((file_exists($fichierAmodifier) && !is_dir($fichierAmodifier)) || (is_dir($fichierAmodifier) && $recursivite == FALSE))
			{
				$anciennesPermissions = adminPermissionsFichier($fichierAmodifier);
				
				if (@chmod($fichierAmodifier, $permissions))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Modification des permissions de %1\$s effectuée (de %2\$s vers %3\$s)."), "<code>$fichierAmodifier</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Modification des permissions de %1\$s impossible (de %2\$s vers %3\$s)."), "<code>$fichierAmodifier</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
				}
			}
			elseif (file_exists($fichierAmodifier) && is_dir($fichierAmodifier) && basename($fichierAmodifier) != '.' && basename($fichierAmodifier) != '..')
			{
				if (!dossierEstVide($fichierAmodifier))
				{
					$dossiersAtraiter = array ($fichierAmodifier);
				
					while (!empty($dossiersAtraiter))
					{
						foreach ($dossiersAtraiter as $dossierAtraiter)
						{
							if (basename($dossierAtraiter) != '.' && basename($dossierAtraiter) != '..')
							{
								if ($dossier = @opendir($dossierAtraiter))
								{
									while (($fichier = @readdir($dossier)) !== FALSE)
									{
										if (!is_dir("$dossierAtraiter/$fichier"))
										{
											$anciennesPermissions = adminPermissionsFichier("$dossierAtraiter/$fichier");
											
											if (@chmod("$dossierAtraiter/$fichier", $permissions))
											{
												$messagesScript[] = '<li>' . sprintf(T_("Modification des permissions du fichier %1\$s effectuée (de %2\$s vers %3\$s)."), "<code>$dossierAtraiter/$fichier</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
											}
											else
											{
												$messagesScript[] = '<li class="erreur">' . sprintf(T_("Modification des permissions du fichier %1\$s impossible (de %2\$s vers %3\$s)."), "<code>$dossierAtraiter/$fichier</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
											}
										}
										else
										{
											$dossiersAtraiter[] = "$dossierAtraiter/$fichier";
										}
									}
							
									closedir($dossier);
							
									if (dossierEstVide($dossierAtraiter))
									{
										$anciennesPermissions = adminPermissionsFichier($dossierAtraiter);
										
										if (@chmod($dossierAtraiter, $permissions))
										{
											$messagesScript[] = '<li>' . sprintf(T_("Modification des permissions du fichier %1\$s effectuée (de %2\$s vers %3\$s)."), "<code>$dossierAtraiter</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
										}
										else
										{
											$messagesScript[] = '<li class="erreur">' . sprintf(T_("Modification des permissions du fichier %1\$s impossible (de %2\$s vers %3\$s)."), "<code>$dossierAtraiter</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
										}
									}
								}
								else
								{
									$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible d'avoir accès au dossier %1\$s."), "<code>$dossierAtraiter</code>") . "</li>\n";
								}
							}
						
							unset($dossiersAtraiter[array_search($dossierAtraiter, $dossiersAtraiter)]);
						}
					}
				}
				elseif ($anciennesPermissions = adminPermissionsFichier($fichierAmodifer) && @chmod($fichierAmodifer, $permissions))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Modification des permissions du fichier %1\$s effectuée (de %2\$s vers %3\$s)."), "<code>$fichierAmodifer</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Modification des permissions du fichier %1\$s impossible (de %2\$s vers %3\$s)."), "<code>$fichierAmodifer</code>", "<code>$anciennesPermissions</code>", "<code>$permissions</code>") . "</li>\n";
				}
			}
		}
	}
	
	echo adminMessagesScript(T_("Modification des permissions"), $messagesScript);
}

########################################################################
##
## $_GET['action']
##
########################################################################

if (isset($_GET['action']))
{
	// Action Renommer
	if ($_GET['action'] == 'renommer')
	{
		$ancienNom = $getValeur;
		
		echo '<h3>' . T_("Insctructions de renommage") . "</h3>\n";
		
		echo "<ul>\n";
		echo '<li>' . sprintf(T_("Pour renommer %1\$s, saisir le nouveau nom dans le champ."), "<code>$ancienNom</code>") . "</li>\n";
		echo '<li>' . T_("Ne pas oublier de mettre le chemin dans le nom.");
		echo '<li>' . T_("Exemples:");
		echo "<ul>\n";
		echo "<li><code>$dossierRacine/nouveau-nom-dossier</code></li>\n";
		echo "<li><code>$dossierRacine/nouveau-nom.txt</code></li>\n";
		echo "<li><code>fichiers/nouveau-nom-dossier/nouveau-nom-fichier.txt</code>.</li>\n";
		echo "</ul></li>\n";
		echo '<li>' . T_("Important: ne pas mettre de barre oblique / dans le nouveau nom du fichier. N'utiliser ce signe que pour marquer le chemin vers le fichier.") . "</li>\n";
		echo "</ul>\n";
		
		echo "<form action='$action' method='post'>\n";
		echo "<div>\n";
		echo '<input type="checkbox" name="porteDocumentsRenommerDupliquer" value="dupliquer" />' . T_("Dupliquer le fichier (en faire une copie et renommer la copie)") . "<br />\n";
		echo '<input type="hidden" name="porteDocumentsAncienNom" value="' . $ancienNom . '" /> <input type="text" name="porteDocumentsNouveauNom" value="' . $ancienNom . '" size="50" />' . "\n";
		echo '<input type="submit" name="porteDocumentsRenommer" value="' . T_("Renommer") . '" />' . "\n";
		echo "</div>\n";
		echo "</form>\n";
	}

	// Action Éditer
	if ($_GET['action'] == 'editer')
	{
		echo '<h3>' . T_("Insctructions d'édition") . "</h3>\n";
		
		if (file_exists($getValeur))
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications». <strong>Attention</strong> de ne pas modifier un fichier binaire. Ceci pourrait le corrompre."), "<code>$getValeur</code>") . "</p>\n";
		}
		else
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s n'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisie."), "<code>$getValeur</code>") . "</p>\n";
		}
	
		echo "<form action='$action#messagesPorteDocuments' method='post'>\n";
		echo "<div>\n";
		clearstatcache();
		if (file_exists($getValeur) && filesize($getValeur))
		{
			$fic = @fopen($getValeur, 'r');
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
			$imageRedimensionner = '<img src="fichiers/redimensionner.png" alt="' . T_("Appuyez sur Maj, cliquez sur l'image et glissez-là pour redimensionner le champ de saisie") . '" title="' . T_("Appuyez sur Maj, cliquez sur l'image et glissez-là pour redimensionner le champ de saisie") . '" width="41" height="20" />';
		}
		
		echo '<div id="redimensionnable"><textarea id="code" cols="80" rows="25" ' . $style . ' name="porteDocumentsContenuFichier">' . $contenuFichier . '</textarea>' . $imageRedimensionner . "</div>\n";
		
		echo '<input type="hidden" name="porteDocumentsEditerNom" value="' . $getValeur . '" />' . "\n";
		echo '<input type="submit" name="porteDocumentsEditerSauvegarder" value="' . T_("Sauvegarder les modifications") . '" />' . "\n";
	
		echo "<form action='$action#messagesPorteDocuments' method='post'>\n";
		echo "<div>\n";
		echo '<input type="submit" name="porteDocumentsEditerAnnuler" value="' . T_("Annuler") . '" />' . "\n";
		echo '<input type="hidden" name="porteDocumentsEditerNom" value="' . $getValeur . '" />' . "\n";
		echo "</div></form>\n";
		echo "</div></form>\n";
	}
}

########################################################################
##
## Éditer
##
########################################################################

if (isset($_POST['porteDocumentsEditerAnnuler']) || isset(isset($_POST['porteDocumentsEditerSauvegarder'])))
{
	$messagesScript = array ();
	
	if (isset($_POST['porteDocumentsEditerAnnuler']))
	{
		$porteDocumentsEditerNom = securiseTexte($_POST['porteDocumentsEditerNom']);
		
		$messagesScript[] = '<li>' . sprintf(T_("Aucune modification apportée au fichier %1\$s."), "<code>$porteDocumentsEditerNom</code>") . "</li>\n";
	}
	elseif (isset($_POST['porteDocumentsEditerSauvegarder']))
	{
		$porteDocumentsEditerNom = securiseTexte($_POST['porteDocumentsEditerNom']);

		$messageErreurEditer = '';
		$messageErreurEditer .= '<p class="erreur">' . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
		$messageErreurEditer .= '<p><textarea class="consulterModifications" name="porteDocumentsContenuFichier" readonly="readonly">';
		$messageErreurEditer .= securiseTexte($_POST['porteDocumentsContenuFichier']);
		$messageErreurEditer .= "</textarea></p>\n";

		$messageErreurEditerAffiche = FALSE;

		if (!$fic = @fopen($porteDocumentsEditerNom, 'w'))
		{
			$messagesScript[] = "<li><p class='erreur'>" . sprintf(T_("Le fichier %1\$s n'a pas pu être ouvert."), "<code>$porteDocumentsEditerNom</code>") . "</p>\n$messageErreurEditer</li>\n";
			$messageErreurEditerAffiche = TRUE;
		}

		if (@fwrite($fic, securiseTexte($_POST['porteDocumentsContenuFichier'])) === FALSE)
		{
			$messagesScript[] = "<li><p class='erreur'>" . sprintf(T_("Impossible d'écrire dans le fichier %1\$s."), "<code>$porteDocumentsEditerNom</code>") . "</p>\n";
			if (!$messageErreurEditerAffiche)
			{
				$messagesScript[] = $messageErreurEditer;
				$messageErreurEditerAffiche = TRUE;
			}
			$messagesScript[] = "</li>\n";
		}

		if (!$messageErreurEditerAffiche)
		{
			$messagesScript[] = '<li>' . sprintf(T_("Édition du fichier %1\$s effectuée. <a href=\"%2\$s\">Éditer à nouveau</a>."), "<code>$porteDocumentsEditerNom</code>", 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditerNom . '#messagesPorteDocuments') . "</li>\n";
		}
		else
		{
			$messagesScript[] = '<li>' . sprintf(T_("<a href=\"%1\$s\">Tenter à nouveau d'éditer le fichier.</a>"), 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditerNom . '#messagesPorteDocuments') . "</li>\n";
		}
	
		fclose($fic);
	}
	
	echo adminMessagesScript(T_("Édition d'un fichier"), $messagesScript);
}

########################################################################
##
## Renommer
##
########################################################################

if (isset($_POST['porteDocumentsRenommer']))
{
	$messagesScript = array ();
	$ancienNom = securiseTexte($_POST['porteDocumentsAncienNom']);
	$nouveauNom = securiseTexte($_POST['porteDocumentsNouveauNom']);
	if (isset($_POST['porteDocumentsRenommerDupliquer']) && $_POST['porteDocumentsRenommerDupliquer'] == 'dupliquer')
	{
		$dupliquer = TRUE;
	}
	else
	{
		$dupliquer = FALSE;
	}

	if (file_exists($ancienNom) && !file_exists($nouveauNom))
	{
		if ($dupliquer)
		{
			if (!file_exists(dirname($nouveauNom)))
			{
				if (!@mkdir(dirname($nouveauNom), 0755, TRUE))
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Création du dossier %1\$s impossible."), '<code>' . dirname($nouveauNom) . '</code>') . "</li>\n";
				}
			}
		
			if (file_exists(dirname($nouveauNom)))
			{
				if (copy($ancienNom, $nouveauNom))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Copie et renommage de %1\$s en %2\$s effectués."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Copie et renommage de %1\$s en %2\$s impossibles."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
				}
			}
		}
		else
		{
			if (!file_exists(dirname($nouveauNom)))
			{
				if (!@mkdir(dirname($nouveauNom), 0755, TRUE))
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Création du dossier %1\$s impossible."), '<code>' . dirname($nouveauNom) . '</code>') . "</li>\n";
				}
			}
		
			if (file_exists(dirname($nouveauNom)))
			{
				if (@rename($ancienNom, $nouveauNom))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Renommage de %1\$s en %2\$s effectué."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Renommage de %1\$s en %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
				}
			}
		}
	}
	else
	{
		if (empty($ancienNom))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Aucun nom spécifié pour le fichier à renommer.") . "</li>\n";
		}
		elseif (!file_exists($ancienNom))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Renommage en %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
		}
		
		if (empty($nouveauNom))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Aucun nouveau nom spécifié.") . "</li>\n";
		}
		elseif (file_exists($nouveauNom))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Renommage de %2\$s impossible."), "<code>$nouveauNom</code>", "<code>$ancienNom</code>") . "</li>\n";
		}
	}
	
	echo adminMessagesScript(T_("Renommage d'un fichier"), $messagesScript);
}

########################################################################
##
## Créer
##
########################################################################

if (isset($_POST['porteDocumentsCreer']))
{
	$messagesScript = array ();
	
	if (empty($_POST['porteDocumentsFichierCreerNomChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun chemin spécifié.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsFichierCreerNom']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun nom spécifié.") . "</li>\n";
	}
	else
	{
		$fichierCreerNom = securiseTexte($_POST['porteDocumentsFichierCreerNomChemin']) . '/' . securiseTexte($_POST['porteDocumentsFichierCreerNom']);

		if (!preg_match("|^$dossierRacine/|i", $fichierCreerNom))
		{
			$fichierCreerNom = "$dossierRacine/$fichierCreerNom";
		}

		$fichierCreerType = $_POST['porteDocumentsFichierCreerType'];

		
		if (!file_exists($fichierCreerNom))
		{
			if ($fichierCreerType == 'Dossier')
			{
				if (@mkdir($fichierCreerNom, 0755, TRUE))
				{
					$messagesScript[] = '<li>' . sprintf(T_("Création du dossier %1\$s effectuée."), "<code>$fichierCreerNom</code>") . "</li>\n";
				}
				else
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de créer le dossier %1\$s."), "<code>$fichierCreerNom</code>") . "</li>\n";
				}
			}
			elseif ($fichierCreerType == 'FichierVide' || $fichierCreerType == 'FichierModele')
			{
				$page = basename($fichierCreerNom);
				$cheminPage = dirname($fichierCreerNom);
				if ($cheminPage == '../.')
				{
					$cheminPage = '..';
				}
		
				if (!file_exists($cheminPage))
				{
					if (!@mkdir($cheminPage, 0755, TRUE))
					{
						$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de créer le dossier %1\$s."), "<code>$cheminPage</code>") . "</li>\n";
					}
				}
		
				if (file_exists($cheminPage))
				{
					if (touch($fichierCreerNom))
					{
						// Ouverture de <li>
						$messagesScript[] = "<li>";
						$messagesScript[] = sprintf(T_("Création du fichier %1\$s effectuée."), "<code>$fichierCreerNom</code>");
				
						if ($fichierCreerType == 'FichierModele')
						{
							$messagesScript[] = sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . $fichierCreerNom . '#messagesPorteDocuments', $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
					
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

							if ($fic = @fopen($cheminPage . '/' . $page, 'a'))
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
								$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible d'ajouter un modèle de page web dans le fichier %1\$s."), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>\n";
							}
						}
						else
						{
							$messagesScript[] = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . $fichierCreerNom . '#messagesPorteDocuments">' . T_("Vous pouvez l'éditer.") . "</a>";
						}
				
						// Fermeture de <li>
						$messagesScript[] = "</li>\n";
					}
					else
					{
						$messagesScript[] = '<li class="erreur">' . sprintf(T_("Impossible de créer le fichier %1\$s."), "<code>$fichierCreerNom</code>") . "</li>\n";
					}
				}
			}
		}
		else
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), "<code>$fichierCreerNom</code>") . "</li>\n";
		}
	}
	
	echo adminMessagesScript(T_("Création d'un fichier"), $messagesScript);
}

########################################################################
##
## Ajouter
##
########################################################################

if (isset($_POST['porteDocumentsAjouter']))
{
	$messagesScript = array ();
	
	if (!isset($_FILES['porteDocumentsAjouterFichier']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier transféré.") . "</li>\n";
	}
	elseif (!isset($_FILES['porteDocumentsAjouterDossier']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun dossier sélectionné.") . "</li>\n";
	}
	else
	{
		$dossier = securiseTexte($_POST['porteDocumentsAjouterDossier']);
		$nomFichier = basename(securiseTexte($_FILES['porteDocumentsAjouterFichier']['name']));
	
		// Affichage du motif dans le message d'erreur
		$motifNom2 = substr($motifNom, 2, -3);
		$motifNom2 = sansEchappement($motifNom2);

		if ($filtreExtensions && !in_array(substr(strrchr($_FILES['porteDocumentsAjouterFichier']['name'], '.'), 1), $extensionsPermises))
		{
			$messagesScript[] = '<li class="erreur">' . T_("Veuillez sélectionner un bon format de fichier ou demandez à ce que l'extension de votre fichier soit ajoutée dans la liste.") . "</li>\n";
		}
		elseif (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']) && filesize($_FILES['porteDocumentsAjouterFichier']['tmp_name']) > $tailleMaxFichiers)
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("Votre fichier doit faire moins de %1\$s octets (%2\$s Mio)."), $tailleMaxFichiers, octetsVersMio($tailleMaxFichiers)) . "</li>\n";
		}
		elseif ($filtreNom && !preg_match("/$motifNom/", $nomFichier))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le nom de votre fichier utilise des caractères non permis. Veuillez le renommer en n'utilisant que les caractères suivants:<br />%1\$s<br />(les espaces sont automatiquement remplacées par des caractères de soulignement _)."), $motifNom2) . "</li>\n";
		}
		elseif (file_exists($dossier . '/' . $nomFichier))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un fichier existe déjà avec le nom %1\$s dans le dossier sélectionné."), $nomFichier) . "</li>\n";
		}
		else
		{
			if ($filtreNom)
			{
				$nomFichier = preg_replace('/ /', '_', $nomFichier);
			}
			
			if ($resultat = move_uploaded_file($_FILES['porteDocumentsAjouterFichier']['tmp_name'], $dossier . '/' . $nomFichier))
			{
				$messagesScript[] = '<li>' . sprintf(T_("Transfert de %1\$s complété."), "<code>$nomFichier</code>") . "</li>\n";
			}
			else
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("Erreur de transfert de %1\$s."), "<code>$nomFichier</code>") . "</li>\n";
			}
		}
	}
	
	echo adminMessagesScript(T_("Ajout d'un fichier"), $messagesScript);
}

echo "</div><!-- /boiteMessages -->\n";

########################################################################
##
## Formulaires
##
########################################################################

echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
echo "<div>\n";
echo '<div class="boite">' . "\n";
echo '<h2 id="fichiersEtDossiers">' . T_("Liste des fichiers et dossiers") . "</h2>\n";

if (isset($_GET['action']) && $_GET['action'] == 'parcourir')
{
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . sprintf(T_("Contenu du dossier %1\$s"), "<code>$getValeur</code>") . "</h3>\n";
	
	if (!is_dir($getValeur))
	{
		echo "<ul>\n";
		echo '<li class="erreur">' . sprintf(T_("%1\$s n'est pas un dossier."), "<code>$getValeur</code>") . "</li>\n";
		echo "</ul>\n";
	}
	else
	{
		$listeFichiersFormatee = adminListeFichiersFormatee($urlRacine, $getValeur, $typeFiltreDossiers, $tableauFiltresDossiers, $action, $symboleUrl, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
		if (!empty($listeFichiersFormatee))
		{
			ksort($listeFichiersFormatee);
		
			echo "<ul class=\"porteDocumentsListe\">\n";
			foreach ($listeFichiersFormatee as $cle => $valeur1)
			{
				echo "<li class='porteDocumentsListeDossiers'>" . T_("Dossier") . " <code>$cle</code><ul>\n";
				$cle = array();
				foreach ($valeur1 as $valeur2)
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
	echo "</div><!-- /class=sousBoite -->\n";
}

echo '<div class="sousBoite">' . "\n";
echo '<h3>' . T_("Liste des dossiers") . "</h3>\n";

$listeDossiers = adminListeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
asort($listeDossiers);
echo "<ul class=\"porteDocumentsListe\">\n";

foreach ($listeDossiers as $listeDossier)
{
	$dossierMisEnForme = '';
	$dossierMisEnForme .= "<li><a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$listeDossier#messagesPorteDocuments\"><img src=\"$urlRacine/admin/fichiers/copier.png\" alt=\"" . T_("Renommer/Déplacer") . "\" title=\"" . T_("Renommer/Déplacer") . "\" width=\"16\" height=\"16\" /></a>\n";
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	$dossierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsFichiersAsupprimer[]\" value=\"$listeDossier\" /> <img src=\"$urlRacine/admin/fichiers/supprimer.png\" alt=\"" . T_("Supprimer") . "\" title=\"" . T_("Supprimer") . "\" width=\"16\" height=\"16\" />\n";
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	$dossierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsPermissionsFichiers[]\" value=\"$listeDossier\" /> <img src=\"$urlRacine/admin/fichiers/permissions.png\" alt=\"" . T_("Modifier les permissions") . "\" title=\"" . T_("Modifier les permissions") . "\" width=\"16\" height=\"16\" />\n";
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	$dossierMisEnForme .= adminInfobulle($urlRacine, $listeDossier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	$dossierMisEnForme .= "<a  class=\"porteDocumentsFichier\" href=\"$action" . $symboleUrl . "action=parcourir&amp;valeur=$listeDossier#fichiersEtDossiers\" title=\"" . sprintf(T_("Parcourir «%1\$s»"), $listeDossier) . "\"><code>$listeDossier</code></a></li>\n";
	
	echo $dossierMisEnForme;
}

echo "</ul>\n";
echo "</div><!-- /class=sousBoite -->\n";
echo "</div><!-- /class=boite -->\n";

echo '<div class="boite">' . "\n";
echo '<h2>' . T_("Supprimer des fichiers") . "</h2>\n";

echo '<p>' . T_("Pour supprimer des fichiers, cocher la case correspondante et cliquer ensuite sur le bouton ci-dessous. <strong>La suppression d'un dossier amène la suppression de tout son contenu.</strong>") . "</p>\n";

echo '<input type="submit" name="porteDocumentsSupprimer" value="' . T_("Supprimer") . '" />' . "\n";
echo "</div><!-- /class=boite -->\n";

echo '<div class="boite">' . "\n";
echo '<h2>' . T_("Modifier les permissions") . "</h2>\n";

echo '<p>' . T_("Pour modifier les permissions de fichiers, cocher la case correspondante et cliquer ensuite sur le bouton ci-dessous. Vous pouvez préciser de modifier les permissions de tous les fichiers contenus dans un dossier.") . "</p>\n";

echo "<p><input type=\"checkbox\" name=\"porteDocumentsPermissionsRecursives\" value=\"permissionsRecursives\" /> Si un dossier est coché, modifier les permissions de ce dossier ainsi que de tout son contenu.</p>\n";

echo '<p><label>' . T_("Nouvelles permissions (notation octale, par exemple 0755):") . "</label><br />\n" . '<input type="text" name="porteDocumentsPermissionsValeur" size="4" value="" />' . "</p>\n";

echo '<p><input type="submit" name="porteDocumentsPermissions" value="' . T_("Modifier les permissions") . '" />' . "</p>\n";
echo "</div><!-- /class=boite -->\n";

echo "</div>\n";
echo "</form>\n";

echo '<div class="boite">' . "\n";
echo '<h2>' . T_("Ajouter un fichier") . "</h2>\n";

echo '<form action="' . $action . '#messagesPorteDocuments" method="post" enctype="multipart/form-data">' . "\n";
echo "<div>\n";
echo '<p><label>' . T_("Fichier:") . "</label><br />\n" . '<input type="file" name="porteDocumentsAjouterFichier" size="25"/>' . "</p>\n";
echo '<p><label>' . T_("Dossier:") . "</label><br />\n" . '<select name="porteDocumentsAjouterDossier" size="1">' . "\n";
$listeDossiers = adminListeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
asort($listeDossiers);
foreach ($listeDossiers as $valeur)
{
	echo '<option value="' . $valeur . '">' . $valeur . "</option>\n";
}
echo "</select></p>\n";
echo '<p><input type="submit" name="porteDocumentsAjouter" value="' . T_("Ajouter") . '" />' . "</p>\n";
echo "</div></form>\n";
echo "</div><!-- /class=boite -->\n";

echo '<div class="boite">' . "\n";
echo '<h2>' . T_("Créer un fichier ou un dossier") . "</h2>\n";

echo '<p>' . T_("Spécifier le nom du nouveau fichier ou dossier à créer. Mettre le chemin dans le nom. Exemples:") . "</p>\n";

echo "<ul>\n";
echo '<li><code>' . $dossierRacine . "/nouveau-dossier</code></li>\n";
echo '<li><code>' . $dossierRacine . "/nouveau-fichier.txt</code></li>\n";
echo '<li><code>' . $dossierRacine . "/nouveau-dossier/nouveau-fichier.txt</code></li>\n";
echo "</ul>\n";

echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
echo "<div>\n";
echo '<p><label>' . T_("Chemin et nom:") . "</label><br />\n";

echo '<select name="porteDocumentsFichierCreerNomChemin" size="1">' . "\n";
$listeDossiers = adminListeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
asort($listeDossiers);
foreach ($listeDossiers as $valeur)
{
	echo '<option value="' . $valeur . '">' . $valeur . "</option>\n";
}
echo '</select>';
echo ' / <input type="text" name="porteDocumentsFichierCreerNom" size="25" value="" /></p>' . "\n";
echo '<p><label>' . T_("Type:") . "</label><br />\n";
echo '<select name="porteDocumentsFichierCreerType" size="1">' . "\n";
echo '<option value="Dossier">' . T_("Dossier") . "</option>\n";
echo '<option value="FichierModele">' .  T_("Fichier modèle de page web") . "</option>\n";
echo '<option value="FichierVide">' . T_("Fichier vide") . "</option>\n";
echo "</select></p>\n";
echo '<p><input type="submit" name="porteDocumentsCreer" value="' . T_("Créer") . '" />' . "</p>\n";
echo "</div>\n";
echo "</form>\n";
echo "</div><!-- /class=boite -->\n";

include $racine . '/admin/inc/dernier.inc.php';
?>
