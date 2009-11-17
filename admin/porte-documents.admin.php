<?php
/* _______________ Inclusions et initialisations _______________ */

include 'inc/zero.inc.php';
$baliseTitle = T_("Porte-documents");

if ($filtreTypesMime && !empty($typesMimePermis))
{
	$boitesDeroulantes = "affichageDetailleTypesMimePermis affichageDetailleTypesMimePermisTitre affichageDetailleTypesMimePermisCorps";
}

include 'inc/premier.inc.php';

if (isset($_GET['valeur']))
{
	$getValeur = securiseTexte($_GET['valeur']);
}

if (isset($_GET['dossierCourant']) || isset($_POST['porteDocumentsDossierCourant']))
{
	if (isset($_GET['dossierCourant']))
	{
		$dossierCourant = securiseTexte($_GET['dossierCourant']);
	}
	elseif (isset($_POST['porteDocumentsDossierCourant']))
	{
		$dossierCourant = securiseTexte($_POST['porteDocumentsDossierCourant']);
	}
}

if (!isset($dossierCourant) || !file_exists($dossierCourant) || !is_dir($dossierCourant))
{
	$dossierCourant = '';
}

if (!empty($dossierCourant))
{
	$dossierCourantDansUrl = "&amp;dossierCourant=$dossierCourant";
}
else
{
	$dossierCourantDansUrl = '';
}

if ($typeFiltreDossiers == 'dossiersPermis' || $typeFiltreDossiers == 'dossiersExclus'
	&& !empty($filtreDossiers))
{
	$tableauFiltresDossiers = explode('|', $filtreDossiers);
}
else
{
	$tableauFiltresDossiers = array ();
}

/* _______________ Début d'affichage du porte-documents _______________ */

echo '<h1>' . T_("Porte-documents") . "</h1>\n";

echo '<div id="boiteMessages" class="boite">' . "\n";
echo '<h2 id="messagesPorteDocuments">' . T_("Messages d'avancement, de confirmation ou d'erreur") . "</h2>\n";

########################################################################
##
## Copie
##
########################################################################

/* _______________ Confirmation _______________ */

if ($porteDocumentsDroits['copier'] && isset($_POST['porteDocumentsCopie']))
{
	$messagesScript = array ();
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Copie de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la copie.") . "</li>\n";
	}
	else
	{
		$fichiersAcopier = securiseTexte($_POST['porteDocumentsFichiers']);
		
		echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel copier les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			echo "<li>$fichierAcopier</li>\n";
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAcopier . '" />' . "\n";
		}
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><select name="porteDocumentsCopieChemin" size="1">' . "\n";
		$listeDossiers = adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
		
		foreach ($listeDossiers as $valeur)
		{
			if (!empty($dossierCourant) && $valeur == $dossierCourant)
			{
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}
			
			echo '<option value="' . $valeur . $selected . '">' . $valeur . "</option>\n";
		}
		echo "</select></p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsCopieConfirmation" value="' . T_("Copier") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Mise en action _______________ */

if ($porteDocumentsDroits['copier'] && isset($_POST['porteDocumentsCopieConfirmation']))
{
	$messagesScript = array ();
	$cheminDeCopie = securiseTexte($_POST['porteDocumentsCopieChemin']);
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la copie.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsCopieChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun emplacement sélectionné pour la copie.") . "</li>\n";
	}
	elseif (!file_exists($cheminDeCopie))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'existe pas."), "<code>$cheminDeCopie</li>\n") . "</li>\n";
	}
	elseif (!is_dir($cheminDeCopie))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'est pas un dossier."), "<code>$cheminDeCopie</code>") . "</li>\n";
	}
	else
	{
		$fichiersAcopier = securiseTexte($_POST['porteDocumentsFichiers']);
		
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			$fichierSource = $fichierAcopier;
			$fichierDeDestination = $cheminDeCopie . '/' . basename($fichierAcopier);
			
			if (!file_exists($fichierSource))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Copie vers %2\$s impossible."), "<code>$fichierSource</code>", "<code>$cheminDeCopie</code>") . "</li>\n";
			}
			elseif (file_exists($fichierDeDestination))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Copie de %2\$s impossible."), "<code>$fichierDeDestination</code>", "<code>$fichierSource</code>") . "</li>\n";
			}
			elseif (!is_dir($fichierAcopier))
			{
				$messagesScript[] = adminCopy($fichierSource, $fichierDeDestination);
			}
			elseif ($fichierSource != '.' && $fichierSource != '..')
			{
				$messagesScript[] = adminCopyDossier($fichierSource, $fichierDeDestination);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Copie de fichiers"));
}

########################################################################
##
## Déplacement
##
########################################################################

/* _______________ Confirmation _______________ */

if ($porteDocumentsDroits['deplacer'] && isset($_POST['porteDocumentsDeplacement']))
{
	$messagesScript = array ();
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Déplacement de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour le déplacement.") . "</li>\n";
	}
	else
	{
		$fichiersAdeplacer = securiseTexte($_POST['porteDocumentsFichiers']);
		
		echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel déplacer les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			echo "<li>$fichierAdeplacer</li>\n";
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAdeplacer . '" />' . "\n";
		}
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><select name="porteDocumentsDeplacementChemin" size="1">' . "\n";
		$listeDossiers = adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
		
		foreach ($listeDossiers as $valeur)
		{
			if (!empty($dossierCourant) && $valeur == $dossierCourant)
			{
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}
			echo '<option value="' . $valeur . $selected . '">' . $valeur . "</option>\n";
		}
		echo "</select></p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsDeplacementConfirmation" value="' . T_("Déplacer") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Mise en action _______________ */

if ($porteDocumentsDroits['deplacer'] && isset($_POST['porteDocumentsDeplacementConfirmation']))
{
	$messagesScript = array ();
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun emplacement sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (!file_exists($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'existe pas."), "<code>" . securiseTexte($_POST['porteDocumentsDeplacementChemin']) . "</code>") . "</li>\n";
	}
	elseif (!is_dir($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'est pas un dossier."), "<code>" . securiseTexte($_POST['porteDocumentsDeplacementChemin']) . "</code>") . "</li>\n";
	}
	else
	{
		$fichiersAdeplacer = securiseTexte($_POST['porteDocumentsFichiers']);
		$cheminDeDeplacement = securiseTexte($_POST['porteDocumentsDeplacementChemin']);
	
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			$ancienChemin = $fichierAdeplacer;
			$nouveauChemin = $cheminDeDeplacement . '/' . basename($fichierAdeplacer);
			
			if (!file_exists($ancienChemin))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Déplacement vers %2\$s impossible."), "<code>$ancienChemin</code>", "<code>$nouveauChemin</code>") . "</li>\n";
			}
			elseif (file_exists($nouveauChemin))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Déplacement de %2\$s impossible."), "<code>$nouveauChemin</code>", "<code>$ancienChemin</code>") . "</li>\n";
			}
			else
			{
				$messagesScript[] = adminRename($ancienChemin, $nouveauChemin, TRUE);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Déplacement de fichiers"));
}

########################################################################
##
## Suppression
##
########################################################################

/* _______________ Confirmation _______________ */

if ($porteDocumentsDroits['supprimer'] && isset($_POST['porteDocumentsSuppression']))
{
	$messagesScript = array ();
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Suppression de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	else
	{
		$fichiersAsupprimer = securiseTexte($_POST['porteDocumentsFichiers']);
		
		echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Confirmer la suppression des fichiers ci-dessous. <strong>La suppression d'un dossier amène la suppression de tout son contenu.</strong>") . "</p>\n";
		
		echo "<ul>\n";
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			echo "<li>$fichierAsupprimer</li>\n";
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAsupprimer . '" />' . "\n";
		}
		echo "</ul>\n";
		
		echo '<p><input type="submit" name="porteDocumentsSuppressionConfirmation" value="' . T_("Supprimer") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Mise en action _______________ */

if ($porteDocumentsDroits['supprimer'] && isset($_POST['porteDocumentsSuppressionConfirmation']))
{
	$messagesScript = array ();
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	else
	{
		$fichiersAsupprimer = securiseTexte($_POST['porteDocumentsFichiers']);
		
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			if (!file_exists($fichierAsupprimer))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Suppression impossible."), "<code>$fichierAsupprimer</code>") . "</li>\n";
			}
			elseif (!is_dir($fichierAsupprimer))
			{
				$messagesScript[] = adminUnlink($fichierAsupprimer);
			}
			elseif (basename($fichierAsupprimer) != '.' && basename($fichierAsupprimer) != '..')
			{
				$messagesScript[] = adminRmdirRecursif($fichierAsupprimer);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Suppression de fichiers"));
}

########################################################################
##
## Modification des permissions
##
########################################################################

/* _______________ Confirmation _______________ */

if ($porteDocumentsDroits['permissions'] && isset($_POST['porteDocumentsPermissions']))
{
	$messagesScript = array ();
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Modification des permissions") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la modification des permissions.") . "</li>\n";
	}
	else
	{
		$fichiersAmodifier = securiseTexte($_POST['porteDocumentsFichiers']);
		
		echo '<p>' . T_("Spécifier les nouvelles permissions pour les fichiers ci-dessous.") . "</p>\n";
		
		echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo "<ul>\n";
		foreach ($fichiersAmodifier as $fichierAmodifier)
		{
			echo "<li>$fichierAmodifier (" . adminPermissionsFichier($fichierAmodifier) . ")</li>\n";
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAmodifier . '" />' . "\n";
		}
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo "<p><input type=\"checkbox\" name=\"porteDocumentsPermissionsRecursives\" value=\"permissionsRecursives\" /> Pour chaque dossier sélectionné, modifier ses permissions ainsi que celles de tout son contenu.</p>\n";

		echo '<p><label>' . T_("Nouvelles permissions (notation octale sur trois chiffres, par exemple 755):") . "</label><br />\n" . '<input type="text" name="porteDocumentsPermissionsValeur" size="3" value="" />' . "</p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsPermissionsConfirmation" value="' . T_("Modifier les permissions") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Mise en action _______________ */

if ($porteDocumentsDroits['permissions'] && isset($_POST['porteDocumentsPermissionsConfirmation']))
{
	$messagesScript = array ();
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier sélectionné pour la modification des permissions.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsPermissionsValeur']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucune permission spécifiée.") . "</li>\n";
	}
	elseif (!preg_match('/^[0-7]{3}$/', $_POST['porteDocumentsPermissionsValeur']))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("Les permissions spécifiées (%1\$s) ne sont pas valides."), "<code>" . securiseTexte($_POST['porteDocumentsPermissionsValeur']) . "</code>") . "</li>\n";
	}
	else
	{
		$fichiersAmodifier = securiseTexte($_POST['porteDocumentsFichiers']);
		$permissions = octdec($_POST['porteDocumentsPermissionsValeur']);
		
		if (isset($_POST['porteDocumentsPermissionsRecursives']) && $_POST['porteDocumentsPermissionsRecursives'] == 'permissionsRecursives')
		{
			$recursivite = TRUE;
		}
		else
		{
			$recursivite = FALSE;
		}
		
		foreach ($fichiersAmodifier as $fichierAmodifier)
		{
			if (!file_exists($fichierAmodifier))
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Modification des permissions impossible."), "<code>$fichierAmodifier</code>") . "</li>\n";
			}
			elseif (basename($fichierAmodifier) != '.' && basename($fichierAmodifier) != '..')
			{
				if (!is_dir($fichierAmodifier) || $recursivite == FALSE)
				{
					$messagesScript[] = adminChmod($fichierAmodifier, $permissions);
				}
				else
				{
					$messagesScript[] = adminChmodRecursif($fichierAmodifier, $permissions);
				}
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Modification des permissions"));
}

########################################################################
##
## Édition
##
########################################################################

/* _______________ Formulaire d'édition _______________ */

if ($porteDocumentsDroits['editer'] && isset($_GET['action']) && $_GET['action'] == 'editer')
{
	$messagesScript = array ();
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Édition d'un fichier") . "</h3>\n";
	
	if (!file_exists($getValeur) && !$porteDocumentsDroits['creer'])
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'existe pas."), "<code>$getValeur</code>") . "</li>\n";
	}
	else
	{
		if (file_exists($getValeur))
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications». <strong>Attention</strong> de ne pas modifier un fichier binaire. Ceci le corromprait."), "<code>$getValeur</code> " . adminInfobulle($racine, $urlRacine, $getValeur, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)) . "</p>\n";
		}
		else
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s n'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisie (qui peut être vide)."), "<code>$getValeur</code>") . "</p>\n";
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
	
		echo '<input type="hidden" name="porteDocumentsEditionNom" value="' . $getValeur . '" />' . "\n";
		echo '<p><input type="submit" name="porteDocumentsEditionSauvegarder" value="' . T_("Sauvegarder les modifications") . '" />' . "</p>\n";

		echo "<form action='$action#messagesPorteDocuments' method='post'>\n";
		echo "<div>\n";
		echo '<p><input type="submit" name="porteDocumentsEditionAnnulation" value="' . T_("Annuler") . '" />' . "</p>\n";
		echo '<input type="hidden" name="porteDocumentsEditionNom" value="' . $getValeur . '" />' . "\n";
	
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
		}
	
		echo "</div></form>\n";
		echo "</div></form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Annulation d'édition _______________ */

if ($porteDocumentsDroits['editer'] && isset($_POST['porteDocumentsEditionAnnulation']))
{
	$messagesScript = array ();
	$porteDocumentsEditionNom = securiseTexte($_POST['porteDocumentsEditionNom']);
	
	$messagesScript[] = '<li>' . sprintf(T_("Aucune modification apportée au fichier %1\$s."), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
	
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

/* _______________ Sauvegarde des modifications _______________ */

if ($porteDocumentsDroits['editer'] && isset($_POST['porteDocumentsEditionSauvegarder']))
{
	$messagesScript = array ();
	$porteDocumentsEditionNom = securiseTexte($_POST['porteDocumentsEditionNom']);
	
	if (!file_exists($porteDocumentsEditionNom) && !$porteDocumentsDroits['creer'])
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'existe pas."), "<code>$getValeur</code>") . "</li>\n";
	}
	else
	{
		$messageErreurEdition = '';
		$messageErreurEdition .= '<p class="erreur">' . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
		$messageErreurEdition .= '<p><textarea class="consulterModifications" name="porteDocumentsContenuFichier" readonly="readonly">';
		$messageErreurEdition .= securiseTexte($_POST['porteDocumentsContenuFichier']);
		$messageErreurEdition .= "</textarea></p>\n";

		$messageErreurEditionAffiche = FALSE;

		if (!$fic = @fopen($porteDocumentsEditionNom, 'w'))
		{
			$messagesScript[] = "<li><p class='erreur'>" . sprintf(T_("Le fichier %1\$s n'a pas pu être ouvert."), "<code>$porteDocumentsEditionNom</code>") . "</p>\n$messageErreurEdition</li>\n";
			$messageErreurEditionAffiche = TRUE;
		}

		if (@fwrite($fic, securiseTexte($_POST['porteDocumentsContenuFichier'])) === FALSE)
		{
			$messagesScript[] = "<li><p class='erreur'>" . sprintf(T_("Écriture dans le fichier %1\$s impossible."), "<code>$porteDocumentsEditionNom</code>") . "</p>\n";
			if (!$messageErreurEditionAffiche)
			{
				$messagesScript[] = $messageErreurEdition;
				$messageErreurEditionAffiche = TRUE;
			}
			$messagesScript[] = "</li>\n";
		}

		if (!$messageErreurEditionAffiche)
		{
			$messagesScript[] = '<li>' . sprintf(T_("Édition du fichier %1\$s effectuée. <a href=\"%2\$s\">Éditer à nouveau.</a>"), "<code>$porteDocumentsEditionNom</code>", 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditionNom . $dossierCourantDansUrl . '#messagesPorteDocuments') . "</li>\n";
		}
		else
		{
			$messagesScript[] = '<li>' . sprintf(T_("<a href=\"%1\$s\">Tenter à nouveau d'éditer le fichier.</a>"), 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditionNom . $dossierCourantDansUrl . '#messagesPorteDocuments') . "</li>\n";
		}

		fclose($fic);
	}
	
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

########################################################################
##
## Renommage
##
########################################################################

/* _______________ Formulaire de renommage _______________ */

if ($porteDocumentsDroits['renommer'] && isset($_GET['action']) && $_GET['action'] == 'renommer')
{
	$ancienNom = $getValeur;
	
	echo '<h3>' . T_("Renommage d'un fichier") . "</h3>\n";
	
	if ($porteDocumentsDroits['deplacer'])
	{
		echo '<p>' . sprintf(T_("Pour renommer %1\$s, spécifier le nouveau nom dans le champ. Il est possible de déplacer le fichier en insérant un chemin dans le nom, par exemple <code>dossier1/dossier2/fichier.txt</code>."), "<code>$ancienNom</code>") . "</p>\n";
	}
	else
	{
		echo '<p>' . sprintf(T_("Pour renommer %1\$s, spécifier le nouveau nom dans le champ."), "<code>$ancienNom</code>") . "</p>\n";
	}
	
	echo "<form action='$action' method='post'>\n";
	echo "<div>\n";
	
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	if ($porteDocumentsDroits['copier'])
	{
		echo '<p><input type="checkbox" name="porteDocumentsRenommageCopie" value="copie" />' . T_("Copier avant de renommer") . "</p>\n";
	}
	
	echo '<input type="hidden" name="porteDocumentsAncienNom" value="' . $ancienNom . '" />';
	
	if ($porteDocumentsDroits['deplacer'])
	{
		echo '<p><input type="text" name="porteDocumentsNouveauNom" value="' . $ancienNom . '" size="50" />' . "</p>\n";
	}
	else
	{
		echo '<p>' . dirname($ancienNom) . '/<input type="text" name="porteDocumentsNouveauNom" value="' . basename($ancienNom) . '" size="50" />' . "</p>\n";
	}
	
	echo "</fieldset>\n";
	
	echo '<p><input type="submit" name="porteDocumentsRenommage" value="' . T_("Renommer") . '" />' . "</p>\n";
	
	if (!empty($dossierCourant))
	{
		echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
	}
	
	echo "</div>\n";
	echo "</form>\n";
}

/* _______________ Mise en action _______________ */

if ($porteDocumentsDroits['renommer'] && isset($_POST['porteDocumentsRenommage']))
{
	$messagesScript = array ();
	$ancienNom = securiseTexte($_POST['porteDocumentsAncienNom']);
	
	if ($porteDocumentsDroits['deplacer'])
	{
		$nouveauNom = securiseTexte($_POST['porteDocumentsNouveauNom']);
	}
	else
	{
		$nouveauNom = securiseTexte(dirname($_POST['porteDocumentsAncienNom']) . '/' . basename($_POST['porteDocumentsNouveauNom']));
	}
	
	if ($porteDocumentsDroits['copier'] && isset($_POST['porteDocumentsRenommageCopie']) && $_POST['porteDocumentsRenommageCopie'] == 'copie')
	{
		$copie = TRUE;
	}
	else
	{
		$copie = FALSE;
	}
	
	if (empty($ancienNom))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier à renommer spécifié.") . "</li>\n";
	}
	elseif (!file_exists($ancienNom))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Renommage en %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
	}
	elseif (empty($nouveauNom))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun nouveau nom spécifié.") . "</li>\n";
	}
	elseif (file_exists($nouveauNom))
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Renommage de %2\$s impossible."), "<code>$nouveauNom</code>", "<code>$ancienNom</code>") . "</li>\n";
	}
	else
	{
		if (!file_exists(dirname($nouveauNom)))
		{
			$messagesScript[] = adminMkdir(dirname($nouveauNom), octdec(755), TRUE);
		}
		
		if (file_exists(dirname($nouveauNom)))
		{
			if ($copie)
			{
				if (basename($ancienNom) != '.' && basename($ancienNom) != '..')
				{
					if (!is_dir($ancienNom))
					{
						$messagesScript[] = adminCopy($ancienNom, $nouveauNom);
					}
					else
					{
						$messagesScript[] = adminCopyDossier($ancienNom, $nouveauNom);
					}
				}
			}
			else
			{
				$messagesScript[] = adminRename($ancienNom, $nouveauNom);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Renommage d'un fichier"));
}

########################################################################
##
## Création
##
########################################################################

if ($porteDocumentsDroits['creer'] && isset($_POST['porteDocumentsCreation']))
{
	$messagesScript = array ();
	
	if (empty($_POST['porteDocumentsCreationChemin']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun chemin spécifié.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsCreationNom']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun nom spécifié.") . "</li>\n";
	}
	else
	{
		$fichierAcreerNom = securiseTexte($_POST['porteDocumentsCreationChemin']) . '/' . securiseTexte($_POST['porteDocumentsCreationNom']);

		if (!preg_match("|^$dossierRacine/|i", $fichierAcreerNom))
		{
			$fichierAcreerNom = "$dossierRacine/$fichierAcreerNom";
		}
		
		$fichierAcreerType = $_POST['porteDocumentsCreationType'];
		
		if (file_exists($fichierAcreerNom))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), "<code>$fichierAcreerNom</code>") . "</li>\n";
		}
		else
		{
			if ($fichierAcreerType == 'Dossier')
			{
				$messagesScript[] = adminMkdir($fichierAcreerNom, octdec(755), TRUE);
			}
			elseif ($fichierAcreerType == 'FichierVide' || $fichierAcreerType == 'FichierModele')
			{
				$page = basename($fichierAcreerNom);
				$cheminPage = dirname($fichierAcreerNom);
				if ($cheminPage == '../.')
				{
					$cheminPage = '..';
				}
				
				if (!file_exists($cheminPage))
				{
					$messagesScript[] = adminMkdir($cheminPage, octdec(755), TRUE);
				}
				
				if (file_exists($cheminPage))
				{
					if (@touch($fichierAcreerNom))
					{
						// Ouverture de <li>
						$messagesScript[] = "<li>";
						$messagesScript[] = sprintf(T_("Création du fichier %1\$s effectuée."), "<code>$fichierAcreerNom</code>");
				
						if ($fichierAcreerType == 'FichierModele')
						{
							if ($porteDocumentsDroits['editer'])
							{
								$messagesScript[] = sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . $fichierAcreerNom . $dossierCourantDansUrl . '#messagesPorteDocuments', $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
							}
							else
							{
								$messagesScript[] = sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'afficher</a>."), $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
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
								$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ajout d'un modèle de page web dans le fichier %1\$s impossible."), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>\n";
							}
						}
						elseif ($porteDocumentsDroits['editer'])
						{
							$messagesScript[] = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . $fichierAcreerNom . $dossierCourantDansUrl . '#messagesPorteDocuments">' . T_("Vous pouvez l'éditer.") . "</a>";
						}
						
						// Fermeture de <li>
						$messagesScript[] = "</li>\n";
					}
					else
					{
						$messagesScript[] = '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), "<code>$fichierAcreerNom</code>") . "</li>\n";
					}
				}
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Création d'un fichier"));
}

########################################################################
##
## Ajout
##
########################################################################

if ($porteDocumentsDroits['ajouter'] && (!$filtreTypesMime || ($filtreTypesMime && !empty($typesMimePermis))) && isset($_POST['porteDocumentsAjouter']))
{
	$messagesScript = array ();
	
	if (empty($_FILES['porteDocumentsAjouterFichier']['name']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun fichier spécifié.") . "</li>\n";
	}
	elseif (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']) && filesize($_FILES['porteDocumentsAjouterFichier']['tmp_name']) > $tailleMaxFichiers)
	{
		$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le fichier doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichiers), $tailleMaxFichiers) . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsAjouterDossier']))
	{
		$messagesScript[] = '<li class="erreur">' . T_("Aucun dossier spécifié.") . "</li>\n";
	}
	elseif ($_FILES['porteDocumentsAjouterFichier']['error'])
	{
		$messagesScript[] = adminMessageFilesError($_FILES['porteDocumentsAjouterFichier']['error']);
	}
	else
	{
		$dossier = securiseTexte($_POST['porteDocumentsAjouterDossier']);
		$nomFichier = basename(securiseTexte($_FILES['porteDocumentsAjouterFichier']['name']));
		$nouveauNomFichier = basename(securiseTexte($_POST['porteDocumentsAjouterNom']));
		
		if (!empty($nouveauNomFichier))
		{
			$nomFichier = $nouveauNomFichier;
		}
		
		if ($filtreNom)
		{
			$ancienNomFichier = $nomFichier;
			$transliteration = parse_ini_file($racine . '/admin/inc/i18n-ascii.txt');
			$nomFichier = strtr($nomFichier, $transliteration);
			$nomFichier = preg_replace('/[^-A-Za-z0-9._\+]/', '-', $nomFichier);
			$nomFichier = preg_replace('/-+/', '-', $nomFichier);
			$nomFichier = str_replace('-.', '.', $nomFichier);
			
			if ($nomFichier != $ancienNomFichier)
			{
				$messagesScript[] = '<li>' . sprintf(T_("Filtre du nom de fichier activé: renommage de %1\$s en %2\$s effectué."), "<code>$ancienNomFichier</code>", "<code>$nomFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($dossier . '/' . $nomFichier))
		{
			$messagesScript[] = '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
		}
		else
		{
			if (@move_uploaded_file($_FILES['porteDocumentsAjouterFichier']['tmp_name'], $dossier . '/' . $nomFichier))
			{
				$typeMime = mimedetect_mime(array ('filepath' => $dossier . '/' . $nomFichier, 'filename' => $nomFichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				
				if ($filtreTypesMime && array_search($typeMime, $typesMimePermis) === FALSE)
				{
					$messagesScript[] = '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, et il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), "<code>$nomFichier</code>", "<code>$typeMime</code>") . "</li>\n";
					@unlink($dossier . '/' . $nomFichier);
				}
				else
				{
					$messagesScript[] = '<li>' . sprintf(T_("Ajout de %1\$s dans %2\$s effectué."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript[] = '<li class="erreur">' . sprintf(T_("Ajout de %1\$s dans %2\$s impossible."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
			}
		}
	}
	
	if (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']))
	{
		@unlink($_FILES['porteDocumentsAjouterFichier']['tmp_name']);
	}
	
	echo adminMessagesScript($messagesScript, T_("Ajout d'un fichier"));
}

echo "</div><!-- /boiteMessages -->\n";

########################################################################
##
## Formulaires
##
########################################################################

echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
echo "<div>\n";

if (!empty($dossierCourant))
{
	echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
}

echo '<div class="boite">' . "\n";
echo '<h2 id="fichiersEtDossiers">' . T_("Liste des fichiers et dossiers") . "</h2>\n";

if ($porteDocumentsDroits['ajouter'] && $porteDocumentsDroits['creer'] && $porteDocumentsDroits['copier'] && $porteDocumentsDroits['deplacer'] && $porteDocumentsDroits['permissions'] && $porteDocumentsDroits['supprimer'])
{
	$afficherBoiteActions = TRUE;
	
	$boiteActions = '';
	$boiteActions .= "<fieldset>\n";
	$boiteActions .= '<legend>' . T_("Actions") . "</legend>";
	if ($porteDocumentsDroits['ajouter'])
	{
		$boiteActions .= ' <a href="#ajouter">' . T_("Ajouter") . '</a> |';
	}
	if ($porteDocumentsDroits['creer'])
	{
		$boiteActions .= ' <a href="#creer">' . T_("Créer") . '</a> |';
	}
	if ($porteDocumentsDroits['copier'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsCopie" value="' . T_("Copier") . '" /> |';
	}
	if ($porteDocumentsDroits['deplacer'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsDeplacement" value="' . T_("Déplacer") . '" /> |';
	}
	if ($porteDocumentsDroits['permissions'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsPermissions" value="' . T_("Permissions") . '" /> |';
	}
	if ($porteDocumentsDroits['supprimer'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsSuppression" value="' . T_("Supprimer") . '" /> |';
	}
	$boiteActions = substr($boiteActions, 0, -2);
	$boiteActions .= "</fieldset>\n";
}

if ($afficherBoiteActions)
{
	echo $boiteActions;
}

/* _______________ Parcours d'un dossier _______________ */

if ((isset($_GET['action']) && $_GET['action'] == 'parcourir') || !empty($dossierCourant))
{
	if (isset($_GET['action']) && $_GET['action'] == 'parcourir')
	{
		$dossierAparcourir = $getValeur;
	}
	else
	{
		$dossierAparcourir = $dossierCourant;
	}
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . sprintf(T_("Contenu du dossier %1\$s"), "<code>$dossierAparcourir</code>") . "</h3>\n";
	
	if (!file_exists($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'existe pas."), "<code>$dossierAparcourir</code>") . "</p>\n";
	}
	elseif (!is_dir($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'est pas un dossier."), "<code>$dossierAparcourir</code>") . "</p>\n";
	}
	else
	{
		$listeFormateeFichiers = adminListeFormateeFichiers($racine, $urlRacine, $dossierAparcourir, $typeFiltreDossiers, $tableauFiltresDossiers, $action, $symboleUrl, $dossierCourant, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance, $porteDocumentsDroits);
		
		if (!empty($listeFormateeFichiers))
		{
			echo "<ul class=\"porteDocumentsListe\">\n";
			foreach ($listeFormateeFichiers as $cle => $valeur1)
			{
				echo '<li class="porteDocumentsListeContenuDossier"><strong>' . T_("Dossier") . " <code>$cle</code></strong><ul class=\"porteDocumentsListeDernierNiveau\">\n";
				$cle = array();
				foreach ($valeur1 as $valeur2)
				{
					$cle[] = $valeur2;
				}
				
				natcasesort($cle);
				
				$classe = 'impair';
				foreach ($cle as $valeur3)
				{
					echo "<li class=\"$classe\">$valeur3</li>\n";
					$classe = ($classe == 'impair') ? 'pair' : 'impair';
				}
				echo "</ul></li>\n";
			}
			echo "</ul>\n";
		}
	}
	echo "</div><!-- /class=sousBoite -->\n";
}

/* _______________ Liste des dossiers _______________ */

echo '<div class="sousBoite">' . "\n";
echo '<h3>' . T_("Liste des dossiers") . "</h3>\n";

$listeDossiers = adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);

echo "<ul class=\"porteDocumentsListe porteDocumentsListeDernierNiveau\">\n";

$classe = 'impair';
foreach ($listeDossiers as $listeDossier)
{
	$dossierMisEnForme = '';
	
	$dossierMisEnForme .= "<li class=\"$classe\">";
	
	if ($porteDocumentsDroits['copier'] && $porteDocumentsDroits['deplacer'] && $porteDocumentsDroits['permissions'] && $porteDocumentsDroits['supprimer'])
	{
		$dossierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsFichiers[]\" value=\"$listeDossier\" />";
	
		$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	if ($porteDocumentsDroits['telecharger'])
	{
		$dossierMisEnForme .= "<a href=\"$urlRacine/admin/telecharger.admin.php?fichier=$listeDossier\"><img src=\"$urlRacine/admin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
		
		$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	if ($porteDocumentsDroits['renommer'])
	{
		$dossierMisEnForme .= "<a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$listeDossier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacine/admin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
	
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	$dossierMisEnForme .= adminInfobulle($racine, $urlRacine, $listeDossier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	
	$dossierMisEnForme .= "<a  class=\"porteDocumentsFichier\" href=\"$action" . $symboleUrl . "action=parcourir&amp;valeur=$listeDossier&amp;dossierCourant=$listeDossier#fichiersEtDossiers\" title=\"" . sprintf(T_("Parcourir «%1\$s»"), $listeDossier) . "\"><code>$listeDossier</code></a></li>\n";
	
	echo $dossierMisEnForme;
	
	$classe = ($classe == 'impair') ? 'pair' : 'impair';
}

echo "</ul>\n";
echo "</div><!-- /class=sousBoite -->\n";

if ($afficherBoiteActions)
{
	echo $boiteActions;
}

echo "</div><!-- /class=boite -->\n";
echo "</div>\n";
echo "</form>\n";

/* _______________ Ajout _______________ */

if ($porteDocumentsDroits['ajouter'] && !$filtreTypesMime || ($filtreTypesMime && !empty($typesMimePermis)))
{
	echo '<div class="boite">' . "\n";
	echo '<h2 id="ajouter">' . T_("Ajouter un fichier") . "</h2>\n";
	
	echo "<p>Choisir le fichier à ajouter et le dossier parent. Optionnellement, vous pouvez renommer le fichier.</p>\n";
	
	echo '<p>' . sprintf(T_("La taille maximale d'un transfert de fichier est %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichiers), $tailleMaxFichiers) . "</p>\n";
	
	if ($filtreTypesMime && !empty($typesMimePermis))
	{
		$affichageTypesMimePermis = ' ';
		foreach ($typesMimePermis as $extensions => $type)
		{
			$extensions = str_replace('|', ', ', $extensions);
			$affichageTypesMimePermis .= "$extensions, ";
		}
		
		$affichageTypesMimePermis = substr($affichageTypesMimePermis, 0, -2);
		echo '<p>' . sprintf(T_("Les types de fichier permis sont: %1\$s."), $affichageTypesMimePermis) . "</p>\n";
	
		echo '<div id="affichageDetailleTypesMimePermis">' . "\n";
		echo '<p id="affichageDetailleTypesMimePermisTitre"><strong>' . T_("Liste détaillée des types MIME permis") . "</strong></p>\n";
	
		echo '<div id="affichageDetailleTypesMimePermisCorps" class="masquer">' . "\n";
		echo "<ul>\n";
	
		$affichageDetailleTypesMimePermis = '';
		foreach ($typesMimePermis as $extensions => $type)
		{
			$affichageDetailleTypesMimePermis .= "<li>$type ($extensions)</li>";
		}
	
		echo $affichageDetailleTypesMimePermis;
		echo "</ul>\n";
		echo "</div>\n";
		echo "</div>\n";
	}
	
	echo '<form action="' . $action . '#messagesPorteDocuments" method="post" enctype="multipart/form-data">' . "\n";
	echo "<div>\n";
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	echo '<p><label>' . T_("Fichier:") . "</label><br />\n" . '<input type="file" name="porteDocumentsAjouterFichier" size="25"/>' . "</p>\n";
	echo '<p><label>' . T_("Dossier:") . "</label><br />\n" . '<select name="porteDocumentsAjouterDossier" size="1">' . "\n";
	$listeDossiers = adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
	
	foreach ($listeDossiers as $valeur)
	{
		if (!empty($dossierCourant) && $valeur == $dossierCourant)
		{
			$selected = ' selected="selected"';
		}
		else
		{
			$selected = '';
		}
		
		echo '<option value="' . $valeur . '"' . $selected. '>' . $valeur . "</option>\n";
	}
	echo "</select></p>\n";

	echo "<p><label>Nouveau nom du fichier (optionnel):</label><br />\n";
	echo '<input type="text" name="porteDocumentsAjouterNom" size="25" value="" /></p>' . "\n";;
	echo "</fieldset>\n";
	
	echo '<p><input type="submit" name="porteDocumentsAjouter" value="' . T_("Ajouter") . '" />' . "</p>\n";
	
	if (!empty($dossierCourant))
	{
		echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
	}
	
	echo "</div></form>\n";
	echo "</div><!-- /class=boite -->\n";
}

/* _______________ Création _______________ */

if ($porteDocumentsDroits['creer'])
{
	echo '<div class="boite">' . "\n";
	echo '<h2 id="creer">' . T_("Créer un fichier ou un dossier") . "</h2>\n";

	echo '<p>' . T_("Choisir le dossier parent et spécifier le nom du nouveau fichier ou dossier à créer. De nouveaux dossiers parents peuvent être ajoutés dans le nom, séparés par des barres obliques (<code>/</code>). Ils seront créés en même temps que le fichier ou le dossier.") . "</p>\n";

	echo '<form action="' . $action . '#messagesPorteDocuments" method="post">' . "\n";
	echo "<div>\n";
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	echo '<p><label>' . T_("Chemin et nom:") . "</label><br />\n";

	echo '<select name="porteDocumentsCreationChemin" size="1">' . "\n";
	$listeDossiers = adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauFiltresDossiers);
	
	foreach ($listeDossiers as $valeur)
	{
		if (!empty($dossierCourant) && $valeur == $dossierCourant)
		{
			$selected = ' selected="selected"';
		}
		else
		{
			$selected = '';
		}
		
		echo '<option value="' . $valeur . '"' . $selected. '>' . $valeur . "</option>\n";
	}
	echo '</select>';
	echo ' / <input type="text" name="porteDocumentsCreationNom" size="25" value="" /></p>' . "\n";
	echo '<p><label>' . T_("Type:") . "</label><br />\n";
	echo '<select name="porteDocumentsCreationType" size="1">' . "\n";
	echo '<option value="Dossier">' . T_("Dossier") . "</option>\n";
	echo '<option value="FichierModele">' .  T_("Fichier modèle de page web") . "</option>\n";
	echo '<option value="FichierVide">' . T_("Fichier vide") . "</option>\n";
	echo "</select></p>\n";
	echo "</fieldset>\n";
	
	echo '<p><input type="submit" name="porteDocumentsCreation" value="' . T_("Créer") . '" />' . "</p>\n";
	
	if (!empty($dossierCourant))
	{
		echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
	}
	
	echo "</div>\n";
	echo "</form>\n";
	echo "</div><!-- /class=boite -->\n";
}

include $racine . '/admin/inc/dernier.inc.php';
?>
