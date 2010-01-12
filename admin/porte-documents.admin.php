<?php
/* ____________________ Inclusions et affectations. ____________________ */

include 'inc/zero.inc.php';

// Jeton utilisé pour vérifier la provenance d'un formulaire complété d'édition de fichier.
if (
	$adminPorteDocumentsDroits['editer'] &&
	(
		(isset($_GET['action']) && $_GET['action'] == 'editer') ||
		isset($_POST['porteDocumentsEditionAnnulation']) || isset($_POST['porteDocumentsEditionSauvegarder'])
	)
)
{
	session_start();
	
	if (!isset($_SESSION['jeton']))
	{
		$_SESSION['jeton'] = md5(uniqid(mt_rand(), TRUE));
		
		if (isset($_GET['valeur']))
		{
			$_SESSION['jeton'] .= md5($_GET['valeur']);
		}
	}
}

$baliseTitle = T_("Porte-documents");

if ($adminFiltreTypesMime && !empty($adminTypesMimePermis))
{
	$boitesDeroulantes = "affichageDetailleTypesMimePermis";
}

include $racineAdmin . '/inc/premier.inc.php';

if (!empty($adminFiltreDossiers))
{
	$tableauFiltresDossiers = explode('|', $adminFiltreDossiers);
	$tableauFiltresDossiers = array_map('realpath', $tableauFiltresDossiers);
}
else
{
	$tableauFiltresDossiers = array ();
}

if (isset($_GET['valeur']))
{
	$getValeur = securiseTexte($_GET['valeur']);
}
else
{
	$getValeur = '';
}

if (isset($_GET['dossierCourant']))
{
	$dossierCourant = securiseTexte($_GET['dossierCourant']);
}
elseif (isset($_POST['porteDocumentsDossierCourant']))
{
	$dossierCourant = securiseTexte($_POST['porteDocumentsDossierCourant']);
}

if (!isset($dossierCourant) || !file_exists($dossierCourant) || !is_dir($dossierCourant) || !adminEmplacementPermis($dossierCourant, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
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

/* ____________________ Début de l'affichage du porte-documents. ____________________ */

echo '<h1>' . T_("Porte-documents") . "</h1>\n";

echo '<div id="boiteMessages" class="boite">' . "\n";
echo '<h2 id="messagesPorteDocuments">' . T_("Messages d'avancement, de confirmation ou d'erreur") . "</h2>\n";

########################################################################
##
## Copie.
##
########################################################################

/* ____________________ Confirmation. ____________________ */

if ($adminPorteDocumentsDroits['copier'] && isset($_POST['porteDocumentsCopie']))
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Copie de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la copie.") . "</li>\n";
	}
	else
	{
		$fichiersAcopier = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAcopier = adminEmplacementsPermis($fichiersAcopier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAcopier = adminEmplacementsModifiables($fichiersAcopier, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel copier les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			echo '<li>' . $fichierAcopier;
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAcopier . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><select name="porteDocumentsCopieChemin" size="1">' . "\n";
		$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		
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
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if ($adminPorteDocumentsDroits['copier'] && isset($_POST['porteDocumentsCopieConfirmation']))
{
	$messagesScript = '';
	$cheminDeCopie = securiseTexte($_POST['porteDocumentsCopieChemin']);
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la copie.") . "</li>\n";
	}
	elseif (empty($cheminDeCopie))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun emplacement sélectionné pour la copie.") . "</li>\n";
	}
	elseif (!file_exists($cheminDeCopie))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'existe pas."), "<code>$cheminDeCopie</code>\n") . "</li>\n";
	}
	elseif (!is_dir($cheminDeCopie))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'est pas un dossier."), "<code>$cheminDeCopie</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($cheminDeCopie, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'est pas gérable par le porte-documents."), "<code>$cheminDeCopie</code>") . "</li>\n";
	}
	else
	{
		$fichiersAcopier = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAcopier = adminEmplacementsPermis($fichiersAcopier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAcopier = adminEmplacementsModifiables($fichiersAcopier, $adminDossierRacinePorteDocuments);
		$fichiersAcopier = adminTriParProfondeur($fichiersAcopier);
		
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			$fichierSource = $fichierAcopier;
			$fichierDeDestination = $cheminDeCopie . '/' . superBasename($fichierAcopier);
		
			if (!file_exists($fichierSource))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Copie vers %2\$s impossible."), "<code>$fichierSource</code>", "<code>$cheminDeCopie</code>") . "</li>\n";
			}
			elseif (file_exists($fichierDeDestination))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Copie de %2\$s impossible."), "<code>$fichierDeDestination</code>", "<code>$fichierSource</code>") . "</li>\n";
			}
			elseif (preg_match("|^$fichierSource/|", $fichierDeDestination))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible. La destination se trouve à l'intérieur de la source."), "<code>$fichierSource</code>", "<code>$fichierDeDestination</code>") . "</li>\n";
			}
			elseif (!is_dir($fichierAcopier))
			{
				$messagesScript .= adminCopy($fichierSource, $fichierDeDestination);
			}
			elseif ($fichierSource != '.' && $fichierSource != '..')
			{
				$messagesScript .= adminCopyDossier($fichierSource, $fichierDeDestination);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Copie de fichiers"));
}

########################################################################
##
## Déplacement.
##
########################################################################

/* ____________________ Confirmation. ____________________ */

if ($adminPorteDocumentsDroits['deplacer'] && isset($_POST['porteDocumentsDeplacement']))
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Déplacement de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour le déplacement.") . "</li>\n";
	}
	else
	{
		$fichiersAdeplacer = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAdeplacer = adminEmplacementsPermis($fichiersAdeplacer, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAdeplacer = adminEmplacementsModifiables($fichiersAdeplacer, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel déplacer les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			echo '<li>' . $fichierAdeplacer;
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAdeplacer . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><select name="porteDocumentsDeplacementChemin" size="1">' . "\n";
		$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		
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
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if ($adminPorteDocumentsDroits['deplacer'] && isset($_POST['porteDocumentsDeplacementConfirmation']))
{
	$messagesScript = '';
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun emplacement sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (!file_exists($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'existe pas."), "<code>" . securiseTexte($_POST['porteDocumentsDeplacementChemin']) . "</code>") . "</li>\n";
	}
	elseif (!is_dir($_POST['porteDocumentsDeplacementChemin']))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'est pas un dossier."), "<code>" . securiseTexte($_POST['porteDocumentsDeplacementChemin']) . "</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($_POST['porteDocumentsDeplacementChemin'], $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'est pas gérable par le porte-documents."), "<code>" . securiseTexte($_POST['porteDocumentsDeplacementChemin']) . "</code>") . "</li>\n";
	}
	else
	{
		$fichiersAdeplacer = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAdeplacer = adminEmplacementsPermis($fichiersAdeplacer, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAdeplacer = adminEmplacementsModifiables($fichiersAdeplacer, $adminDossierRacinePorteDocuments);
		$fichiersAdeplacer = adminTriParProfondeur($fichiersAdeplacer);
		$cheminDeDeplacement = securiseTexte($_POST['porteDocumentsDeplacementChemin']);
	
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			$ancienChemin = $fichierAdeplacer;
			$nouveauChemin = $cheminDeDeplacement . '/' . superBasename($fichierAdeplacer);
			
			if (!file_exists($ancienChemin))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Déplacement vers %2\$s impossible."), "<code>$ancienChemin</code>", "<code>$nouveauChemin</code>") . "</li>\n";
			}
			elseif (file_exists($nouveauChemin))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Déplacement de %2\$s impossible."), "<code>$nouveauChemin</code>", "<code>$ancienChemin</code>") . "</li>\n";
			}
			else
			{
				$messagesScript .= adminRename($ancienChemin, $nouveauChemin, TRUE);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Déplacement de fichiers"));
}

########################################################################
##
## Suppression.
##
########################################################################

/* ____________________ Confirmation. ____________________ */

if ($adminPorteDocumentsDroits['supprimer'] && isset($_POST['porteDocumentsSuppression']))
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Suppression de fichiers") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	else
	{
		$fichiersAsupprimer = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAsupprimer = adminEmplacementsPermis($fichiersAsupprimer, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAsupprimer = adminEmplacementsModifiables($fichiersAsupprimer, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Confirmer la suppression des fichiers ci-dessous. <strong>La suppression d'un dossier amène la suppression de tout son contenu.</strong>") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			echo '<li>' . $fichierAsupprimer;
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAsupprimer . '" />';
			echo "</li>\n";
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
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if ($adminPorteDocumentsDroits['supprimer'] && isset($_POST['porteDocumentsSuppressionConfirmation']))
{
	$messagesScript = '';
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	else
	{
		$fichiersAsupprimer = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAsupprimer = adminEmplacementsPermis($fichiersAsupprimer, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAsupprimer = adminEmplacementsModifiables($fichiersAsupprimer, $adminDossierRacinePorteDocuments);
		$fichiersAsupprimer = adminTriParProfondeur($fichiersAsupprimer);
		
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			if (!file_exists($fichierAsupprimer))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Suppression impossible."), "<code>$fichierAsupprimer</code>") . "</li>\n";
			}
			elseif (!is_dir($fichierAsupprimer))
			{
				$messagesScript .= adminUnlink($fichierAsupprimer);
			}
			elseif (superBasename($fichierAsupprimer) != '.' && superBasename($fichierAsupprimer) != '..')
			{
				$messagesScript .= adminRmdirRecursif($fichierAsupprimer);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Suppression de fichiers"));
}

########################################################################
##
## Modification des permissions.
##
########################################################################

/* ____________________ Confirmation. ____________________ */

if ($adminPorteDocumentsDroits['modifier-permissions'] && isset($_POST['porteDocumentsPermissions']))
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Modification des permissions") . "</h3>\n";
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la modification des permissions.") . "</li>\n";
	}
	else
	{
		$fichiersAmodifier = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAmodifier = adminEmplacementsPermis($fichiersAmodifier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAmodifier = adminEmplacementsModifiables($fichiersAmodifier, $adminDossierRacinePorteDocuments);
		
		echo '<p>' . T_("Spécifier les nouvelles permissions pour les fichiers ci-dessous.") . "</p>\n";
		
		echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
		echo "<div>\n";
		echo "<ul>\n";
		
		foreach ($fichiersAmodifier as $fichierAmodifier)
		{
			echo '<li>' . $fichierAmodifier . ' (' . adminPermissionsFichier($fichierAmodifier) . ')';
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . $fichierAmodifier . '" />';
			echo "</li>\n";
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
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if ($adminPorteDocumentsDroits['modifier-permissions'] && isset($_POST['porteDocumentsPermissionsConfirmation']))
{
	$messagesScript = '';
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la modification des permissions.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsPermissionsValeur']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucune permission spécifiée.") . "</li>\n";
	}
	elseif (!preg_match('/^[0-7]{3}$/', $_POST['porteDocumentsPermissionsValeur']))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Les permissions spécifiées (%1\$s) ne sont pas valides."), "<code>" . securiseTexte($_POST['porteDocumentsPermissionsValeur']) . "</code>") . "</li>\n";
	}
	else
	{
		$fichiersAmodifier = securiseTexte($_POST['porteDocumentsFichiers']);
		$fichiersAmodifier = adminEmplacementsPermis($fichiersAmodifier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
		$fichiersAmodifier = adminEmplacementsModifiables($fichiersAmodifier, $adminDossierRacinePorteDocuments);
		$fichiersAmodifier = adminTriParProfondeur($fichiersAmodifier);
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Modification des permissions impossible."), "<code>$fichierAmodifier</code>") . "</li>\n";
			}
			elseif (superBasename($fichierAmodifier) != '.' && superBasename($fichierAmodifier) != '..')
			{
				if (!is_dir($fichierAmodifier) || $recursivite == FALSE)
				{
					$messagesScript .= adminChmod($fichierAmodifier, $permissions);
				}
				else
				{
					$messagesScript .= adminChmodRecursif($fichierAmodifier, $permissions);
				}
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Modification des permissions"));
}

########################################################################
##
## Édition.
##
########################################################################

/* ____________________ Formulaire d'édition. ____________________ */

if ($adminPorteDocumentsDroits['editer'] && isset($_GET['action']) && $_GET['action'] == 'editer')
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Édition d'un fichier") . "</h3>\n";
	
	if (!file_exists($getValeur) && !$adminPorteDocumentsDroits['creer'])
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'existe pas."), "<code>$getValeur</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($getValeur, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), "<code>$getValeur</code>") . "</li>\n";
	}
	else
	{
		if (file_exists($getValeur))
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications». <strong>Attention</strong> de ne pas modifier un fichier binaire. Ceci le corromprait."), "<code>$getValeur</code> " . adminInfobulle($racineAdmin, $urlRacineAdmin, $getValeur, TRUE, $adminTailleCache, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)) . "</p>\n";
		}
		else
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s n'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisie (qui peut être vide)."), "<code>$getValeur</code>") . "</p>\n";
		}
		
		echo "<form action='" . adminAjouteGet($adminAction, securiseTexte(SID)) . "#messagesPorteDocuments' method='post'>\n";
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

		if ($adminAideEdition != 'BUEditor' && $adminAideEdition != 'CodeMirror')
		{
			$styleTextarea = 'style="width: 93%;"';
		}
		else
		{
			$styleTextarea = '';
		}
		
		if ($adminAideEdition == 'BUEditor')
		{
			$classTextarea = 'class="editor-textarea"';
		}
		else
		{
			$classTextarea = '';
		}
		
		if (!adminEstIe() && $adminAideEdition != 'BUEditor')
		{
			$imageRedimensionner = '<img src="fichiers/redimensionner.png" alt="' . T_("Appuyez sur Maj, cliquez sur l'image et glissez-là pour redimensionner le champ de saisie.") . '" title="' . T_("Appuyez sur Maj, cliquez sur l'image et glissez-là pour redimensionner le champ de saisie.") . '" width="41" height="20" />';
		}
		else
		{
			$imageRedimensionner = '';
		}
		
		echo '<div id="redimensionnable"><textarea id="code" ' . $classTextarea . ' cols="80" rows="25" ' . $styleTextarea . ' name="porteDocumentsContenuFichier">' . $contenuFichier . '</textarea>' . $imageRedimensionner . "</div>\n";
	
		echo '<input type="hidden" name="porteDocumentsEditionNom" value="' . $getValeur . '" />' . "\n";
		echo '<input type="hidden" name="porteDocumentsEditionJeton" value="' . $_SESSION['jeton'] . '" />' . "\n";
		
		echo '<p><input type="submit" name="porteDocumentsEditionSauvegarder" value="' . T_("Sauvegarder les modifications") . '" />' . "</p>\n";
		
		echo "<form action='$adminAction#messagesPorteDocuments' method='post'>\n";
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
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Annulation d'édition. ____________________ */

if ($adminPorteDocumentsDroits['editer'] && isset($_POST['porteDocumentsEditionAnnulation']))
{
	$messagesScript = '';
	$porteDocumentsEditionNom = securiseTexte($_POST['porteDocumentsEditionNom']);
	$messagesScript .= '<li>' . sprintf(T_("Aucune modification apportée au fichier %1\$s."), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
	
	adminDetruitSession();
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

/* ____________________ Sauvegarde des modifications. ____________________ */

if ($adminPorteDocumentsDroits['editer'] && isset($_POST['porteDocumentsEditionSauvegarder']))
{
	$messagesScript = '';
	$porteDocumentsEditionNom = securiseTexte($_POST['porteDocumentsEditionNom']);
	
	if (!file_exists($porteDocumentsEditionNom) && !$adminPorteDocumentsDroits['creer'])
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'existe pas."), "<code>$getValeur</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($porteDocumentsEditionNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
	}
	else
	{
		$messageErreurEdition = '';
		$messageErreurEdition .= '<p class="erreur">' . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
		
		$messageErreurEdition .= '<p><pre id="porteDocumentsContenuFichier" class="consulterModifications">' . securiseTexte($_POST['porteDocumentsContenuFichier']) . "</pre></p>\n";
		
		$messageErreurEdition .= "<p><a href=\"javascript:adminSelectionneTexte('porteDocumentsContenuFichier');\">" . T_("Sélectionner le contenu.") . "</a></p>\n";
		
		$messageErreurEditionAafficher = FALSE;
		
		if ($_POST['porteDocumentsEditionJeton'] != $_SESSION['jeton'])
		{
			$messageErreurEditionAafficher = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La demande de modification du fichier %1\$s ne peut aboutir. Il peut y avoir deux raisons à ce problème:\n<ul>\n<li>votre session a expiré. Dans ce cas, copiez le contenu qui devait être sauvegardé et tentez à nouveau d'éditer le fichier;</li>\n<li>la demande ne provient pas du serveur hébergeant le porte-documents. Vérifiez dans ce cas que vous n'êtes pas la cible d'une attaque de type <acronym lang='en' title='Cross-site request forgery'>CSRF</acronym> (<a href='http://fr.wikipedia.org/wiki/CSRF'>voir la définition de «<acronym lang='en'>CSRF</acronym>» sur Wikipédia</a>). Vérifiez entre autre que le contenu qui allait être sauvegardé ne renferme pas de code malicieux.</li>\n</ul>\n"), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
		}
		elseif ($fic = @fopen($porteDocumentsEditionNom, 'w'))
		{
			if (@fwrite($fic, sansEchappement($_POST['porteDocumentsContenuFichier'])) !== FALSE)
			{
				$messagesScript .= '<li>' . sprintf(T_("Édition du fichier %1\$s effectuée. <a href=\"%2\$s\">Éditer à nouveau.</a>"), "<code>$porteDocumentsEditionNom</code>", 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditionNom . $dossierCourantDansUrl . '#messagesPorteDocuments') . "</li>\n";
			}
			else
			{
				$messageErreurEditionAafficher = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Écriture dans le fichier %1\$s impossible."), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
			}
			
			fclose($fic);
		}
		else
		{
			$messageErreurEditionAafficher = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'a pas pu être ouvert."), "<code>$porteDocumentsEditionNom</code>") . "</li>\n";
		}
		
		if ($messageErreurEditionAafficher)
		{
			$messagesScript .= '<li>' . $messageErreurEdition . "</li>\n";
			$messagesScript .= '<li>' . sprintf(T_("<a href=\"%1\$s\">Tenter à nouveau d'éditer le fichier.</a>"), 'porte-documents.admin.php?action=editer&amp;valeur=' . $porteDocumentsEditionNom . $dossierCourantDansUrl . '#messagesPorteDocuments') . "</li>\n";
		}
	}
	
	adminDetruitSession();
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

########################################################################
##
## Renommage.
##
########################################################################

/* ____________________ Formulaire de renommage. ____________________ */

if ($adminPorteDocumentsDroits['renommer'] && isset($_GET['action']) && $_GET['action'] == 'renommer')
{
	$messagesScript = '';
	$ancienNom = $getValeur;
	
	echo '<h3>' . T_("Renommage d'un fichier") . "</h3>\n";
	
	if (!adminEmplacementPermis($ancienNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), "<code>$ancienNom</code>") . "</li>\n";
	}
	elseif (!adminEmplacementModifiable($ancienNom, $adminDossierRacinePorteDocuments))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Il n'est pas possible d'effectuer cette action sur le dossier %1\$s."), "<code>$ancienNom</code>") . "</li>\n";
	}
	else
	{
		if ($adminPorteDocumentsDroits['deplacer'])
		{
			echo '<p>' . sprintf(T_("Pour renommer %1\$s, spécifier le nouveau nom dans le champ. Il est possible de déplacer le fichier en insérant un chemin dans le nom, par exemple <code>dossier1/dossier2/fichier.txt</code>."), "<code>$ancienNom</code>") . "</p>\n";
		}
		else
		{
			echo '<p>' . sprintf(T_("Pour renommer %1\$s, spécifier le nouveau nom dans le champ."), "<code>$ancienNom</code>") . "</p>\n";
		}
	
		echo "<form action='$adminAction' method='post'>\n";
		echo "<div>\n";
	
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
	
		if ($adminPorteDocumentsDroits['copier'])
		{
			echo '<p><input type="checkbox" name="porteDocumentsRenommageCopie" value="copie" />' . T_("Copier avant de renommer") . "</p>\n";
		}
	
		echo '<input type="hidden" name="porteDocumentsAncienNom" value="' . $ancienNom . '" />';
	
		if ($adminPorteDocumentsDroits['deplacer'])
		{
			echo '<p><input type="text" name="porteDocumentsNouveauNom" value="' . $ancienNom . '" size="50" />' . "</p>\n";
		}
		else
		{
			echo '<p>' . dirname($ancienNom) . '/<input type="text" name="porteDocumentsNouveauNom" value="' . superBasename($ancienNom) . '" size="50" />' . "</p>\n";
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
	
	echo adminMessagesScript($messagesScript);
}

/* ____________________ Mise en action. ____________________ */

if ($adminPorteDocumentsDroits['renommer'] && isset($_POST['porteDocumentsRenommage']))
{
	$messagesScript = '';
	$ancienNom = securiseTexte($_POST['porteDocumentsAncienNom']);
	
	if ($adminPorteDocumentsDroits['deplacer'])
	{
		$nouveauNom = securiseTexte($_POST['porteDocumentsNouveauNom']);
	}
	else
	{
		$nouveauNom = securiseTexte(dirname($_POST['porteDocumentsAncienNom']) . '/' . superBasename($_POST['porteDocumentsNouveauNom']));
	}
	
	if ($adminPorteDocumentsDroits['copier'] && isset($_POST['porteDocumentsRenommageCopie']) && $_POST['porteDocumentsRenommageCopie'] == 'copie')
	{
		$copie = TRUE;
	}
	else
	{
		$copie = FALSE;
	}
	
	if (empty($ancienNom))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier à renommer spécifié.") . "</li>\n";
	}
	elseif (!file_exists($ancienNom))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Renommage en %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
	}
	elseif (empty($nouveauNom))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun nouveau nom spécifié.") . "</li>\n";
	}
	elseif (file_exists($nouveauNom))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Renommage de %2\$s impossible."), "<code>$nouveauNom</code>", "<code>$ancienNom</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($ancienNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), "<code>$ancienNom</code>") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($nouveauNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement de %1\$s n'est pas gérable par le porte-documents."), "<code>$nouveauNom</code>") . "</li>\n";
	}
	elseif (!adminEmplacementModifiable($ancienNom, $adminDossierRacinePorteDocuments))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Il n'est pas possible d'effectuer cette action sur le dossier %1\$s."), "<code>$ancienNom</code>") . "</li>\n";
	}
	else
	{
		if (!file_exists(dirname($nouveauNom)))
		{
			$messagesScript .= adminMkdir(dirname($nouveauNom), octdec(755), TRUE);
		}
		
		if (file_exists(dirname($nouveauNom)))
		{
			if ($copie)
			{
				if (preg_match("|^$ancienNom/|", $nouveauNom))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible. La destination se trouve à l'intérieur de la source."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
				}
				elseif (is_dir($ancienNom))
				{
					$messagesScript .= adminCopyDossier($ancienNom, $nouveauNom);
				}
				else
				{
					$messagesScript .= adminCopy($ancienNom, $nouveauNom);
				}
			}
			else
			{
				$messagesScript .= adminRename($ancienNom, $nouveauNom);
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Renommage d'un fichier"));
}

########################################################################
##
## Création.
##
########################################################################

if ($adminPorteDocumentsDroits['creer'] && isset($_POST['porteDocumentsCreation']))
{
	$messagesScript = '';
	
	if (empty($_POST['porteDocumentsCreationChemin']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun chemin spécifié.") . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsCreationNom']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun nom spécifié.") . "</li>\n";
	}
	else
	{
		$fichierAcreerType = $_POST['porteDocumentsCreationType'];
		$fichierAcreerNom = securiseTexte($_POST['porteDocumentsCreationChemin']) . '/' . securiseTexte($_POST['porteDocumentsCreationNom']);
		
		if (!preg_match("|^$adminDossierRacinePorteDocuments/|i", $fichierAcreerNom))
		{
			$fichierAcreerNom = "$adminDossierRacinePorteDocuments/$fichierAcreerNom";
		}
		
		if ($fichierAcreerType == 'FichierModeleMarkdown')
		{
			$fichierMarkdownAcreerNom = $fichierAcreerNom . '.mdtxt';
		}
		
		if (file_exists($fichierAcreerNom))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), "<code>$fichierAcreerNom</code>") . "</li>\n";
		}
		elseif ($fichierAcreerType == 'FichierModeleMarkdown' && file_exists($fichierMarkdownAcreerNom))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), "<code>$fichierMarkdownAcreerNom</code>") . "</li>\n";
		}
		elseif (!adminEmplacementPermis($fichierAcreerNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), "<code>$fichierAcreerNom</code>") . "</li>\n";
		}
		else
		{
			if ($fichierAcreerType == 'Dossier')
			{
				$messagesScript .= adminMkdir($fichierAcreerNom, octdec(755), TRUE);
			}
			elseif ($fichierAcreerType == 'FichierVide' || $fichierAcreerType == 'FichierModeleHtml' || $fichierAcreerType == 'FichierModeleMarkdown')
			{
				$page = superBasename($fichierAcreerNom);
				$cheminPage = dirname($fichierAcreerNom);
				
				if ($cheminPage == '../.')
				{
					$cheminPage = '..';
				}
				
				if (!file_exists($cheminPage))
				{
					$messagesScript .= adminMkdir($cheminPage, octdec(755), TRUE);
				}
				
				if (file_exists($cheminPage))
				{
					if (@touch($fichierAcreerNom))
					{
						$messagesScript .= "<li>"; // Ouverture de `<li>`.
						$messagesScript .= sprintf(T_("Création du fichier %1\$s effectuée."), "<code>$fichierAcreerNom</code>");
						
						if ($fichierAcreerType == 'FichierModeleHtml' || $fichierAcreerType == 'FichierModeleMarkdown')
						{
							if ($adminPorteDocumentsDroits['editer'])
							{
								$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . $fichierAcreerNom . $dossierCourantDansUrl . '#messagesPorteDocuments', $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
							}
							else
							{
								$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'afficher</a>."), $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode($page), 3));
							}
						}
						elseif ($adminPorteDocumentsDroits['editer'])
						{
							$messagesScript .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . $fichierAcreerNom . $dossierCourantDansUrl . '#messagesPorteDocuments">' . T_("Vous pouvez l'éditer.") . "</a>";
						}
						
						$messagesScript .= "</li>\n"; // Fermeture de `<li>`.
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), "<code>$fichierAcreerNom</code>") . "</li>\n";
					}
					
					if ($fichierAcreerType == 'FichierModeleMarkdown')
					{
						if (@touch($fichierMarkdownAcreerNom))
						{
							$messagesScript .= "<li>"; // Ouverture de `<li>`.
							$messagesScript .= sprintf(T_("Création du fichier %1\$s effectuée."), "<code>$fichierMarkdownAcreerNom</code>");
						
							if ($adminPorteDocumentsDroits['editer'])
							{
								$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . $fichierMarkdownAcreerNom . $dossierCourantDansUrl . '#messagesPorteDocuments', $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode("$page.mdtxt"), 3));
							}
							else
							{
								$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'afficher</a>."), $urlRacine . '/' . substr($cheminPage . '/' . rawurlencode("$page.mdtxt"), 3));
							}
						
							$messagesScript .= "</li>\n"; // Fermeture de `<li>`.
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), "<code>$fichierMarkdownAcreerNom</code>") . "</li>\n";
						}
					}
					
					if (($fichierAcreerType != 'FichierModeleMarkdown' && file_exists($fichierAcreerNom)) || ($fichierAcreerType == 'FichierModeleMarkdown' && file_exists($fichierAcreerNom) && file_exists($fichierMarkdownAcreerNom)))
					{
						if ($fichierAcreerType == 'FichierModeleHtml' || $fichierAcreerType == 'FichierModeleMarkdown')
						{
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
								
								if ($fichierAcreerType == 'FichierModeleHtml')
								{
									$contenu .= '<h1>Titre de la page</h1>' . "\n";
									$contenu .= "\n";
									$contenu .= "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.</p>\n";
								}
								elseif ($fichierAcreerType == 'FichierModeleMarkdown')
								{
									$contenu .= '<?php echo mdtxt("' . superBasename($fichierMarkdownAcreerNom) . '"); ?>' . "\n";
								}
								
								$contenu .= "\n";
								$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
								fputs($fic, $contenu);
								fclose($fic);
							}
							else
							{
								$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout d'un modèle de page web dans le fichier %1\$s impossible."), '<code>' . $cheminPage . '/' . $page . '</code>') . "</li>\n";
							}
							
							if ($fic = @fopen($cheminPage . '/' . "$page.mdtxt", 'a'))
							{
								$contenu = '';
								$contenu .= '# Titre de la page' . "\n";
								$contenu .= "\n";
								$contenu .= "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.";
								fputs($fic, $contenu);
								fclose($fic);
							}
							else
							{
								$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout d'un modèle de page web avec syntaxe Markdown dans le fichier %1\$s impossible."), '<code>' . $cheminPage . '/' . "$page.mdtxt" . '</code>') . "</li>\n";
							}
						}
					}
				}
			}
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Création d'un fichier"));
}

########################################################################
##
## Ajout.
##
########################################################################

if ($adminPorteDocumentsDroits['ajouter'] && (!$adminFiltreTypesMime || ($adminFiltreTypesMime && !empty($adminTypesMimePermis))) && isset($_POST['porteDocumentsAjouter']))
{
	$messagesScript = '';
	
	if (empty($_FILES['porteDocumentsAjouterFichier']['name']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier spécifié.") . "</li>\n";
	}
	elseif (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']) && filesize($_FILES['porteDocumentsAjouterFichier']['tmp_name']) > $adminTailleMaxFichiers)
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($adminTailleMaxFichiers), $adminTailleMaxFichiers) . "</li>\n";
	}
	elseif (empty($_POST['porteDocumentsAjouterDossier']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun dossier spécifié.") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($_POST['porteDocumentsAjouterDossier'], $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour l'ajout (%1\$s) n'est pas gérable par le porte-documents."), "<code>" . securiseTexte($_POST['porteDocumentsAjouterDossier']) . "</code>") . "</li>\n";
	}
	elseif ($_FILES['porteDocumentsAjouterFichier']['error'])
	{
		$messagesScript .= adminMessageFilesError($_FILES['porteDocumentsAjouterFichier']['error']);
	}
	else
	{
		$dossier = securiseTexte($_POST['porteDocumentsAjouterDossier']);
		$nomFichier = superBasename(securiseTexte($_FILES['porteDocumentsAjouterFichier']['name']));
		$nouveauNomFichier = superBasename(securiseTexte($_POST['porteDocumentsAjouterNom']));
		
		if (!empty($nouveauNomFichier))
		{
			$nomFichier = $nouveauNomFichier;
		}
		
		if ($adminFiltreNom)
		{
			$ancienNomFichier = $nomFichier;
			$transliteration = parse_ini_file($racineAdmin . '/inc/i18n-ascii.txt');
			$nomFichier = strtr($nomFichier, $transliteration);
			$nomFichier = preg_replace('/[^-A-Za-z0-9._\+]/', '-', $nomFichier);
			$nomFichier = preg_replace('/-+/', '-', $nomFichier);
			$nomFichier = str_replace('-.', '.', $nomFichier);
			
			if ($nomFichier != $ancienNomFichier)
			{
				$messagesScript .= '<li>' . sprintf(T_("Filtre du nom de fichier activé: renommage de %1\$s en %2\$s effectué."), "<code>$ancienNomFichier</code>", "<code>$nomFichier</code>") . "</li>\n";
			}
		}
		
		if (file_exists($dossier . '/' . $nomFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
		}
		else
		{
			if (@move_uploaded_file($_FILES['porteDocumentsAjouterFichier']['tmp_name'], $dossier . '/' . $nomFichier))
			{
				$typeMime = typeMime($dossier . '/' . $nomFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				
				if (!adminTypeMimePermis($typeMime, $adminFiltreTypesMime, $adminTypesMimePermis))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), "<code>$nomFichier</code>", "<code>$typeMime</code>") . "</li>\n";
					@unlink($dossier . '/' . $nomFichier);
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans %2\$s effectué."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout de %1\$s dans %2\$s impossible."), "<code>$nomFichier</code>", "<code>$dossier</code>") . "</li>\n";
			}
		}
	}
	
	if (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']))
	{
		@unlink($_FILES['porteDocumentsAjouterFichier']['tmp_name']);
	}
	
	echo adminMessagesScript($messagesScript, T_("Ajout d'un fichier"));
}

echo "</div><!-- /#boiteMessages -->\n";

########################################################################
##
## Formulaires.
##
########################################################################

echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
echo "<div>\n";

if (!empty($dossierCourant))
{
	echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
}

echo '<div class="boite">' . "\n";
echo '<h2 id="fichiersEtDossiers">' . T_("Liste des fichiers et dossiers") . "</h2>\n";

if ($adminPorteDocumentsDroits['ajouter'] && $adminPorteDocumentsDroits['creer'] && $adminPorteDocumentsDroits['copier'] && $adminPorteDocumentsDroits['deplacer'] && $adminPorteDocumentsDroits['modifier-permissions'] && $adminPorteDocumentsDroits['supprimer'])
{
	$afficherBoiteActions = TRUE;
	$boiteActions = '';
	$boiteActions .= "<fieldset>\n";
	$boiteActions .= '<legend>' . T_("Actions") . "</legend>";
	
	if ($adminPorteDocumentsDroits['ajouter'])
	{
		$boiteActions .= ' <a href="#ajouter">' . T_("Ajouter") . '</a> |';
	}
	
	if ($adminPorteDocumentsDroits['creer'])
	{
		$boiteActions .= ' <a href="#creer">' . T_("Créer") . '</a> |';
	}
	
	if ($adminPorteDocumentsDroits['copier'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsCopie" value="' . T_("Copier") . '" /> |';
	}
	
	if ($adminPorteDocumentsDroits['deplacer'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsDeplacement" value="' . T_("Déplacer") . '" /> |';
	}
	
	if ($adminPorteDocumentsDroits['modifier-permissions'])
	{
		$boiteActions .= ' <input type="submit" name="porteDocumentsPermissions" value="' . T_("Permissions") . '" /> |';
	}
	
	if ($adminPorteDocumentsDroits['supprimer'])
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

/* ____________________ Parcours d'un dossier. ____________________ */

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
	echo '<h3 id="contenuDossier">' . sprintf(T_("Contenu du dossier %1\$s"), "<code>$dossierAparcourir</code>") . "</h3>\n";
	
	if (!file_exists($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'existe pas."), "<code>$dossierAparcourir</code>") . "</p>\n";
	}
	elseif (!is_dir($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'est pas un dossier."), "<code>$dossierAparcourir</code>") . "</p>\n";
	}
	elseif (!adminEmplacementPermis($dossierAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		echo '<p class="erreur">' . sprintf(T_("L'emplacement %1\$s n'est pas gérable par le porte-documents."), "<code>$dossierAparcourir</code>") . "</p>\n";
	}
	else
	{
		$listeFormateeFichiers = adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierAparcourir, $adminTypeFiltreDossiers, $tableauFiltresDossiers, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminPorteDocumentsDroits, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
		
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
	
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Listage des dossiers. ____________________ */

echo '<div class="sousBoite">' . "\n";
echo '<h3 id="listeDossiers">' . T_("Liste des dossiers") . "</h3>\n";

$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);

echo "<ul class=\"porteDocumentsListe porteDocumentsListeDernierNiveau\">\n";

$classe = 'impair';

foreach ($listeDossiers as $listeDossier)
{
	$dossierMisEnForme = '';
	$dossierMisEnForme .= "<li class=\"$classe\">";
	
	if ($adminPorteDocumentsDroits['copier'] && $adminPorteDocumentsDroits['deplacer'] && $adminPorteDocumentsDroits['modifier-permissions'] && $adminPorteDocumentsDroits['supprimer'])
	{
		if (adminEmplacementModifiable($listeDossier, $adminDossierRacinePorteDocuments))
		{
			$disabled = '';
		}
		else
		{
			$disabled = " disabled=\"disabled\"";
		}
		
		$dossierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsFichiers[]\" value=\"$listeDossier\"$disabled />";
		$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	if ($adminPorteDocumentsDroits['telecharger'])
	{
		$dossierMisEnForme .= "<a href=\"$urlRacineAdmin/telecharger.admin.php?fichier=$listeDossier\"><img src=\"$urlRacineAdmin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
		$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	if ($adminPorteDocumentsDroits['renommer'])
	{
		if (adminEmplacementModifiable($listeDossier, $adminDossierRacinePorteDocuments))
		{
			$dossierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . "action=renommer&amp;valeur=$listeDossier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacineAdmin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
		}
		else
		{
			$dossierMisEnForme .= "<img src=\"$urlRacineAdmin/fichiers/renommer-desactive.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" />\n";
		}
		
		$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	}
	
	$dossierMisEnForme .= adminInfobulle($racineAdmin, $urlRacineAdmin, $listeDossier, TRUE, $adminTailleCache, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	$dossierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
	$dossierMisEnForme .= "<a  class=\"porteDocumentsFichier\" href=\"$adminAction" . $adminSymboleUrl . "action=parcourir&amp;valeur=$listeDossier&amp;dossierCourant=$listeDossier#fichiersEtDossiers\" title=\"" . sprintf(T_("Parcourir «%1\$s»"), $listeDossier) . "\"><code>$listeDossier</code></a></li>\n";
	echo $dossierMisEnForme;
	$classe = ($classe == 'impair') ? 'pair' : 'impair';
}

echo "</ul>\n";
echo "</div><!-- /.sousBoite -->\n";

if ($afficherBoiteActions)
{
	echo $boiteActions;
}

echo "</div><!-- /.boite -->\n";
echo "</div>\n";
echo "</form>\n";

/* ____________________ Ajout. ____________________ */

if ($adminPorteDocumentsDroits['ajouter'] && !$adminFiltreTypesMime || ($adminFiltreTypesMime && !empty($adminTypesMimePermis)))
{
	echo '<div class="boite">' . "\n";
	echo '<h2 id="ajouter">' . T_("Ajouter un fichier") . "</h2>\n";
	
	echo "<p>Choisir le fichier à ajouter et le dossier parent. Optionnellement, vous pouvez renommer le fichier.</p>\n";
	
	echo '<p>' . sprintf(T_("La taille maximale d'un transfert de fichier est %1\$s Mio (%2\$s octets)."), octetsVersMio($adminTailleMaxFichiers), $adminTailleMaxFichiers) . "</p>\n";
	
	if ($adminFiltreTypesMime && !empty($adminTypesMimePermis))
	{
		$affichageTypesMimePermis = ' ';
		
		foreach ($adminTypesMimePermis as $extensions => $type)
		{
			$extensions = str_replace('|', ', ', $extensions);
			$affichageTypesMimePermis .= "$extensions, ";
		}
		
		$affichageTypesMimePermis = substr($affichageTypesMimePermis, 0, -2);
		echo '<p>' . sprintf(T_("Les types de fichier permis sont: %1\$s."), $affichageTypesMimePermis) . "</p>\n";
	
		echo '<div id="affichageDetailleTypesMimePermis">' . "\n";
		echo '<p class="bDtitre"><strong>' . T_("Liste détaillée des types MIME permis") . "</strong></p>\n";
	
		echo '<div class="bDcorps masquer">' . "\n";
		echo "<ul>\n";
	
		$affichageDetailleTypesMimePermis = '';
		
		foreach ($adminTypesMimePermis as $extensions => $type)
		{
			$affichageDetailleTypesMimePermis .= "<li>$type ($extensions)</li>";
		}
	
		echo $affichageDetailleTypesMimePermis;
		echo "</ul>\n";
		echo "</div>\n";
		echo "</div>\n";
	}
	
	echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post" enctype="multipart/form-data">' . "\n";
	echo "<div>\n";
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	echo '<p><label>' . T_("Fichier:") . "</label><br />\n" . '<input type="file" name="porteDocumentsAjouterFichier" size="25"/>' . "</p>\n";
	
	echo '<p><label>' . T_("Dossier:") . "</label><br />\n" . '<select name="porteDocumentsAjouterDossier" size="1">' . "\n";
	$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
	
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
	echo "</div><!-- /.boite -->\n";
}

/* ____________________ Création. ____________________ */

if ($adminPorteDocumentsDroits['creer'])
{
	echo '<div class="boite">' . "\n";
	echo '<h2 id="creer">' . T_("Créer un fichier ou un dossier") . "</h2>\n";

	echo '<p>' . T_("Choisir le dossier parent et spécifier le nom du nouveau fichier ou dossier à créer. De nouveaux dossiers parents peuvent être ajoutés dans le nom, séparés par des barres obliques (<code>/</code>). Ils seront créés en même temps que le fichier ou le dossier.") . "</p>\n";

	echo '<form action="' . $adminAction . '#messagesPorteDocuments" method="post">' . "\n";
	echo "<div>\n";
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	echo '<p><label>' . T_("Chemin et nom:") . "</label><br />\n";

	echo '<select name="porteDocumentsCreationChemin" size="1">' . "\n";
	$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
	
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
	echo '<option value="FichierVide">' . T_("Fichier vide") . "</option>\n";
	echo '<option value="FichierModeleHtml">' .  T_("Fichier modèle HTML de page web") . "</option>\n";
	echo '<option value="FichierModeleMarkdown">' .  T_("Fichier modèle HTML de page web avec syntaxe Markdown") . "</option>\n";
	echo "</select></p>\n";
	echo "</fieldset>\n";
	
	echo '<p><input type="submit" name="porteDocumentsCreation" value="' . T_("Créer") . '" />' . "</p>\n";
	
	if (!empty($dossierCourant))
	{
		echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . $dossierCourant . '" />' . "\n";
	}
	
	echo "</div>\n";
	echo "</form>\n";
	echo "</div><!-- /.boite -->\n";
}

include $racineAdmin . '/inc/dernier.inc.php';
?>
