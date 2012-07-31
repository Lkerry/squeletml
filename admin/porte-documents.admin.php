<?php
/* ____________________ Inclusions et affectations. ____________________ */

include 'inc/zero.inc.php';

$baliseTitle = T_("Porte-documents");
$boitesDeroulantes = '#divContenuDossierAdminPorteDoc #divListeDossiersAdminPorteDoc #varPageModele';
$boitesDeroulantes .= ' .aideAdminPorteDocuments .optionsAvanceesAdminPorteDocuments';

if ($adminFiltreTypesMime && !empty($adminTypesMimePermis))
{
	$boitesDeroulantes .= ' #typesMimePermisAdminPorteDoc';
}

include $racineAdmin . '/inc/premier.inc.php';

$tailleMaxFichier = adminPhpIniOctets(ini_get('upload_max_filesize'));

if (!empty($adminFiltreAccesDossiers))
{
	$tableauFiltresAccesDossiers = explode('|', $adminFiltreAccesDossiers);
	$tableauFiltresAccesDossiers = adminTableauCheminsCanoniques($tableauFiltresAccesDossiers);
}
else
{
	$tableauFiltresAccesDossiers = array ();
}

if (!empty($adminFiltreAffichageDansListe))
{
	$tableauFiltresAffichageDansListe = explode('|', $adminFiltreAffichageDansListe);
	$tableauFiltresAffichageDansListe = adminTableauCheminsCanoniques($tableauFiltresAffichageDansListe);
}
else
{
	$tableauFiltresAffichageDansListe = array ();
}

if (!empty($adminFiltreAffichageDansContenu))
{
	$tableauFiltresAffichageDansContenu = explode('|', $adminFiltreAffichageDansContenu);
	$tableauFiltresAffichageDansContenu = adminTableauCheminsCanoniques($tableauFiltresAffichageDansContenu);
}
else
{
	$tableauFiltresAffichageDansContenu = array ();
}

if (isset($_GET['valeur']))
{
	$getValeur = decodeTexte($_GET['valeur']);
}
else
{
	$getValeur = '';
}

if (isset($_GET['dossierCourant']))
{
	$dossierCourant = decodeTexte($_GET['dossierCourant']);
}
elseif (isset($_POST['porteDocumentsDossierCourant']))
{
	$dossierCourant = decodeTexte($_POST['porteDocumentsDossierCourant']);
}

if (!isset($dossierCourant) || !file_exists($dossierCourant) || !is_dir($dossierCourant) || !adminEmplacementPermis($dossierCourant, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
{
	$dossierCourant = '';
}

if (!empty($dossierCourant))
{
	$dossierCourantDansUrl = '&amp;dossierCourant=' . encodeTexte($dossierCourant);
}
else
{
	$dossierCourantDansUrl = '';
}

// Liste des dossiers.

$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, TRUE);

if ($adminAfficherSousDossiersDansListe)
{
	$listeDossiersPourListe = $listeDossiers;
}
else
{
	$listeDossiersPourListe = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, $adminAfficherSousDossiersDansListe);
}

$majListeDossiers = FALSE;

/* ____________________ Début de l'affichage du porte-documents. ____________________ */

echo '<h1>' . T_("Porte-documents") . "</h1>\n";

echo '<div id="boiteMessages" class="boite">' . "\n";
echo '<h2 id="messages">' . T_("Messages d'avancement, de confirmation ou d'erreur") . "</h2>\n";

########################################################################
##
## Copie.
##
########################################################################

/* ____________________ Confirmation. ____________________ */

if (isset($_POST['porteDocumentsCopie']))
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
		$fichiersAcopier = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAcopier = adminEmplacementsPermis($fichiersAcopier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAcopier = adminEmplacementsModifiables($fichiersAcopier, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel copier les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			echo '<li><code>' . securiseTexte($fichierAcopier) . '</code>';
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . encodeTexte($fichierAcopier) . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><label for="selectPorteDocumentsCopieChemin">' . T_("Emplacement:") . '</label><br />' . "\n" . '<select id="selectPorteDocumentsCopieChemin" name="porteDocumentsCopieChemin" size="1">' . "\n";
		
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
			
			echo '<option value="' . encodeTexte($valeur) . $selected . '">' . securiseTexte($valeur) . "</option>\n";
		}
		
		echo "</select></p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsCopieConfirmation" value="' . T_("Copier") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if (isset($_POST['porteDocumentsCopieConfirmation']))
{
	$messagesScript = '';
	$cheminDeCopie = decodeTexte($_POST['porteDocumentsCopieChemin']);
	
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
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'existe pas."), '<code>' . securiseTexte($cheminDeCopie) . '</code>') . "</li>\n";
	}
	elseif (!is_dir($cheminDeCopie))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'est pas un dossier."), '<code>' . securiseTexte($cheminDeCopie) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementPermis($cheminDeCopie, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour la copie (%1\$s) n'est pas gérable par le porte-documents."), '<code>' . securiseTexte($cheminDeCopie) . '</code>') . "</li>\n";
	}
	else
	{
		$majListeDossiers = TRUE;
		$fichiersAcopier = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAcopier = adminEmplacementsPermis($fichiersAcopier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAcopier = adminEmplacementsModifiables($fichiersAcopier, $adminDossierRacinePorteDocuments);
		$fichiersAcopier = adminTriParProfondeur($fichiersAcopier);
		
		foreach ($fichiersAcopier as $fichierAcopier)
		{
			$fichierSource = $fichierAcopier;
			$fichierDeDestination = $cheminDeCopie . '/' . superBasename($fichierAcopier);
		
			if (!file_exists($fichierSource))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Copie vers %2\$s impossible."), '<code>' . securiseTexte($fichierSource) . '</code>', '<code>' . securiseTexte($cheminDeCopie) . '</code>') . "</li>\n";
			}
			elseif (file_exists($fichierDeDestination))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Copie de %2\$s impossible."), '<code>' . securiseTexte($fichierDeDestination) . '</code>', '<code>' . securiseTexte($fichierSource) . '</code>') . "</li>\n";
			}
			elseif (preg_match("|^$fichierSource/|", $fichierDeDestination))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible. La destination se trouve à l'intérieur de la source."), '<code>' . securiseTexte($fichierSource) . '</code>', '<code>' . securiseTexte($fichierDeDestination) . '</code>') . "</li>\n";
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

if (isset($_POST['porteDocumentsDeplacement']))
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
		$fichiersAdeplacer = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAdeplacer = adminEmplacementsPermis($fichiersAdeplacer, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAdeplacer = adminEmplacementsModifiables($fichiersAdeplacer, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Choisir l'emplacement vers lequel déplacer les fichiers ci-dessous.") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			echo '<li><code>' . securiseTexte($fichierAdeplacer) . '</code>';
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . encodeTexte($fichierAdeplacer) . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><label for="selectPorteDocumentsDeplacementChemin">' . T_("Emplacement:") . '</label><br />' . "\n" . '<select id="selectPorteDocumentsDeplacementChemin" name="porteDocumentsDeplacementChemin" size="1">' . "\n";
		
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
			echo '<option value="' . encodeTexte($valeur) . $selected . '">' . securiseTexte($valeur) . "</option>\n";
		}
		
		echo "</select></p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsDeplacementConfirmation" value="' . T_("Déplacer") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if (isset($_POST['porteDocumentsDeplacementConfirmation']))
{
	$messagesScript = '';
	$cheminDeDeplacement = decodeTexte($_POST['porteDocumentsDeplacementChemin']);
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (empty($cheminDeDeplacement))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun emplacement sélectionné pour le déplacement.") . "</li>\n";
	}
	elseif (!file_exists($cheminDeDeplacement))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'existe pas."), '<code>' . securiseTexte($cheminDeDeplacement) . '</code>') . "</li>\n";
	}
	elseif (!is_dir($cheminDeDeplacement))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'est pas un dossier."), '<code>' . securiseTexte($cheminDeDeplacement) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementPermis($cheminDeDeplacement, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour le déplacement (%1\$s) n'est pas gérable par le porte-documents."), '<code>' . securiseTexte($cheminDeDeplacement) . '</code>') . "</li>\n";
	}
	else
	{
		$majListeDossiers = TRUE;
		$fichiersAdeplacer = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAdeplacer = adminEmplacementsPermis($fichiersAdeplacer, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAdeplacer = adminEmplacementsModifiables($fichiersAdeplacer, $adminDossierRacinePorteDocuments);
		$fichiersAdeplacer = adminTriParProfondeur($fichiersAdeplacer);
		
		foreach ($fichiersAdeplacer as $fichierAdeplacer)
		{
			$ancienChemin = $fichierAdeplacer;
			$nouveauChemin = $cheminDeDeplacement . '/' . superBasename($fichierAdeplacer);
			
			if (!file_exists($ancienChemin))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Déplacement vers %2\$s impossible."), '<code>' . securiseTexte($ancienChemin) . '</code>', '<code>' . securiseTexte($nouveauChemin) . '</code>') . "</li>\n";
			}
			elseif (file_exists($nouveauChemin))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Déplacement de %2\$s impossible."), '<code>' . securiseTexte($nouveauChemin) . '</code>', '<code>' . securiseTexte($ancienChemin) . '</code>') . "</li>\n";
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

if (isset($_POST['porteDocumentsSuppression']))
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
		$fichiersAsupprimer = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAsupprimer = adminEmplacementsPermis($fichiersAsupprimer, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAsupprimer = adminEmplacementsModifiables($fichiersAsupprimer, $adminDossierRacinePorteDocuments);
		
		echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
		echo "<div>\n";
		echo '<p>' . T_("Confirmer la suppression des fichiers ci-dessous. <strong>La suppression d'un dossier amène la suppression de tout son contenu.</strong>") . "</p>\n";
		
		echo "<ul>\n";
		
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			echo '<li><code>' . securiseTexte($fichierAsupprimer) . '</code>';
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . encodeTexte($fichierAsupprimer) . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo '<p><input type="submit" name="porteDocumentsSuppressionConfirmation" value="' . T_("Supprimer") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if (isset($_POST['porteDocumentsSuppressionConfirmation']))
{
	$messagesScript = '';
	
	if (empty($_POST['porteDocumentsFichiers']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier sélectionné pour la suppression.") . "</li>\n";
	}
	else
	{
		$majListeDossiers = TRUE;
		$fichiersAsupprimer = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAsupprimer = adminEmplacementsPermis($fichiersAsupprimer, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAsupprimer = adminEmplacementsModifiables($fichiersAsupprimer, $adminDossierRacinePorteDocuments);
		$fichiersAsupprimer = adminTriParProfondeur($fichiersAsupprimer);
		
		foreach ($fichiersAsupprimer as $fichierAsupprimer)
		{
			if (!file_exists($fichierAsupprimer))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Suppression impossible."), '<code>' . securiseTexte($fichierAsupprimer) . '</code>') . "</li>\n";
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

if (isset($_POST['porteDocumentsPermissions']))
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
		$fichiersAmodifier = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAmodifier = adminEmplacementsPermis($fichiersAmodifier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
		$fichiersAmodifier = adminEmplacementsModifiables($fichiersAmodifier, $adminDossierRacinePorteDocuments);
		
		echo '<p>' . T_("Spécifier les nouvelles permissions pour les fichiers ci-dessous.") . "</p>\n";
		
		echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
		echo "<div>\n";
		echo "<ul>\n";
		
		foreach ($fichiersAmodifier as $fichierAmodifier)
		{
			echo '<li><code>' . securiseTexte($fichierAmodifier) . '</code> (' . adminPermissionsFichier($fichierAmodifier) . ')';
			echo '<input type="hidden" name="porteDocumentsFichiers[]" value="' . encodeTexte($fichierAmodifier) . '" />';
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo "<p><input id=\"inputPorteDocumentsPermissionsRecursives\" type=\"checkbox\" name=\"porteDocumentsPermissionsRecursives\" value=\"permissionsRecursives\" /> <label for=\"inputPorteDocumentsPermissionsRecursives\">Pour chaque dossier sélectionné, modifier ses permissions ainsi que celles de tout son contenu.</label></p>\n";

		echo '<p><label for="inputPorteDocumentsPermissionsValeur">' . T_("Nouvelles permissions (notation octale sur trois chiffres, par exemple 755):") . "</label><br />\n" . '<input id="inputPorteDocumentsPermissionsValeur" type="text" name="porteDocumentsPermissionsValeur" size="3" value="" />' . "</p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsPermissionsConfirmation" value="' . T_("Modifier les permissions") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Mise en action. ____________________ */

if (isset($_POST['porteDocumentsPermissionsConfirmation']))
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
		$fichiersAmodifier = decodeTexte($_POST['porteDocumentsFichiers']);
		$fichiersAmodifier = adminEmplacementsPermis($fichiersAmodifier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers);
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Modification des permissions impossible."), '<code>' . securiseTexte($fichierAmodifier) . '</code>') . "</li>\n";
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

if (isset($_GET['action']) && $_GET['action'] == 'editer')
{
	$messagesScript = '';
	
	echo '<div class="sousBoite">' . "\n";
	echo '<h3>' . T_("Édition d'un fichier") . "</h3>\n";
	
	if (!adminEmplacementPermis($getValeur, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), '<code>' . securiseTexte($getValeur) . '</code>') . "</li>\n";
	}
	else
	{
		if (file_exists($getValeur))
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s est consultable dans le champ ci-dessous. Vous pouvez y effectuer des modifications et ensuite cliquer sur «Sauvegarder les modifications». <strong>Attention</strong> à ne pas modifier un <a href=\"http://fr.wikipedia.org/wiki/Fichier_binaire\">fichier binaire</a>. Ceci le corromprait."), '<code>' . securiseTexte($getValeur) . '</code> ' . adminInfobulle($racineAdmin, $urlRacineAdmin, $getValeur, TRUE, $adminTailleCache, $galerieQualiteJpg, $galerieCouleurAlloueeImage)) . "</p>\n";
		}
		else
		{
			echo '<p>' . sprintf(T_("Le fichier %1\$s n'existe pas. Toutefois, si vous cliquez sur «Sauvegarder les modifications», le fichier sera créé avec le contenu du champ de saisie (qui peut être vide)."), '<code>' . securiseTexte($getValeur) . '</code>') . "</p>\n";
		}
		
		echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
		echo "<div>\n";
		clearstatcache();
		
		if (file_exists($getValeur) && filesize($getValeur))
		{
			$fic = @fopen($getValeur, 'r');
			$contenuFichier = fread($fic, filesize($getValeur));
			$contenuFichier = securiseTexte($contenuFichier);
			fclose($fic);
		}
		else
		{
			$contenuFichier = '';
		}

		echo '<textarea id="code" cols="80" rows="25" name="porteDocumentsContenuFichier">' . $contenuFichier . "</textarea>\n";
	
		echo '<input type="hidden" name="porteDocumentsEditionNom" value="' . encodeTexte($getValeur) . '" />' . "\n";
		
		echo '<p id="porteDocumentsBoutonSauvegarder"><input type="submit" name="porteDocumentsEditionSauvegarder" value="' . T_("Sauvegarder les modifications") . '" />' . "</p>\n";
		
		echo "<form action=\"$adminAction#messages\" method=\"post\">\n";
		echo "<div>\n";
		echo '<p><input type="submit" name="porteDocumentsEditionAnnuler" value="' . T_("Annuler") . '" />' . "</p>\n";
		
		echo '<input type="hidden" name="porteDocumentsEditionNom" value="' . encodeTexte($getValeur) . '" />' . "\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "<div class=\"sep\"></div>\n";
		echo "</div></form>\n";
		echo "</div></form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
	echo "</div><!-- /.sousBoite -->\n";
}

/* ____________________ Annulation d'édition. ____________________ */

if (isset($_POST['porteDocumentsEditionAnnuler']))
{
	$messagesScript = '';
	$porteDocumentsEditionNom = decodeTexte($_POST['porteDocumentsEditionNom']);
	$messagesScript .= '<li>' . sprintf(T_("Aucune modification apportée au fichier %1\$s."), '<code>' . securiseTexte($porteDocumentsEditionNom) . '</code>') . "</li>\n";
	
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

/* ____________________ Sauvegarde des modifications. ____________________ */

if (isset($_POST['porteDocumentsEditionSauvegarder']))
{
	$messagesScript = '';
	$porteDocumentsEditionNom = decodeTexte($_POST['porteDocumentsEditionNom']);
	
	if (!adminEmplacementPermis($porteDocumentsEditionNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), '<code>' . securiseTexte($porteDocumentsEditionNom) . '</code>') . "</li>\n";
	}
	else
	{
		$messageErreurEdition = '';
		$messageErreurEdition .= '<p class="erreur">' . T_("Les modifications n'ont donc pas été sauvegardées. Vous pouvez toutefois les consulter ci-dessous, et en enregistrer une copie sur votre ordinateur.") . "</p>\n";
		
		$messageErreurEdition .= '<p><pre id="porteDocumentsContenuFichier" class="consulterModifications">' . securiseTexte($_POST['porteDocumentsContenuFichier']) . "</pre></p>\n";
		
		$messageErreurEdition .= "<p><a href=\"javascript:adminSelectionneTexte('porteDocumentsContenuFichier');\">" . T_("Sélectionner le contenu.") . "</a></p>\n";
		
		$messageErreurEditionAafficher = FALSE;
		
		if ($fic = @fopen($porteDocumentsEditionNom, 'w'))
		{
			if (@fwrite($fic, $_POST['porteDocumentsContenuFichier']) !== FALSE)
			{
				$messagesScript .= '<li>' . sprintf(T_("Édition du fichier %1\$s effectuée. <a href=\"%2\$s\">Éditer à nouveau.</a>"), '<code>' . securiseTexte($porteDocumentsEditionNom) . '</code>', 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($porteDocumentsEditionNom) . $dossierCourantDansUrl . '#messages') . "</li>\n";
			}
			else
			{
				$messageErreurEditionAafficher = FALSE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Écriture dans le fichier %1\$s impossible."), '<code>' . securiseTexte($porteDocumentsEditionNom) . '</code>') . "</li>\n";
			}
			
			fclose($fic);
		}
		else
		{
			$messageErreurEditionAafficher = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s n'a pas pu être ouvert."), '<code>' . securiseTexte($porteDocumentsEditionNom) . '</code>') . "</li>\n";
		}
		
		if ($messageErreurEditionAafficher)
		{
			$messagesScript .= '<li>' . $messageErreurEdition . "</li>\n";
			$messagesScript .= '<li>' . sprintf(T_("<a href=\"%1\$s\">Tenter à nouveau d'éditer le fichier.</a>"), 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($porteDocumentsEditionNom) . $dossierCourantDansUrl . '#messages') . "</li>\n";
		}
	}
	
	echo adminMessagesScript($messagesScript, T_("Édition d'un fichier"));
}

########################################################################
##
## Renommage.
##
########################################################################

/* ____________________ Formulaire de renommage. ____________________ */

if (isset($_GET['action']) && $_GET['action'] == 'renommer')
{
	$messagesScript = '';
	$ancienNom = $getValeur;
	
	echo '<h3>' . T_("Renommage d'un fichier") . "</h3>\n";
	
	if (!adminEmplacementPermis($ancienNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), '<code>' . securiseTexte($ancienNom) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementModifiable($ancienNom, $adminDossierRacinePorteDocuments))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Il n'est pas possible d'effectuer cette action sur le dossier %1\$s."), '<code>' . securiseTexte($ancienNom) . '</code>') . "</li>\n";
	}
	else
	{
		echo '<p>' . sprintf(T_("Pour renommer %1\$s, spécifier le nouveau nom dans le champ. Il est possible de déplacer le fichier en insérant un chemin dans le nom, par exemple <code>dossier1/dossier2/fichier.txt</code>."), '<code>' . securiseTexte($ancienNom) . '</code>') . "</p>\n";
		
		echo "<form action=\"$adminAction\" method=\"post\">\n";
		echo "<div>\n";
		
		echo "<fieldset>\n";
		echo '<legend>' . T_("Options") . "</legend>\n";
		
		echo '<p><input id="inputPorteDocumentsRenommageCopie" type="checkbox" name="porteDocumentsRenommageCopie" value="copie" /><label for="inputPorteDocumentsRenommageCopie">' . T_("Copier avant de renommer") . "</label></p>\n";
		
		echo '<input type="hidden" name="porteDocumentsAncienNom" value="' . encodeTexte($ancienNom) . '" />';
		
		echo '<p><label for="inputPorteDocumentsNouveauNom">' . T_("Nouveau nom:") . "</label><br />\n" . '<input id="inputPorteDocumentsNouveauNom" type="text" name="porteDocumentsNouveauNom" value="' . securiseTexte($ancienNom) . '" size="50" />' . "</p>\n";
		echo "</fieldset>\n";
		
		echo '<p><input type="submit" name="porteDocumentsRenommage" value="' . T_("Renommer") . '" />' . "</p>\n";
		
		if (!empty($dossierCourant))
		{
			echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
		}
		
		echo "</div>\n";
		echo "</form>\n";
	}
	
	echo adminMessagesScript($messagesScript);
}

/* ____________________ Mise en action. ____________________ */

if (isset($_POST['porteDocumentsRenommage']))
{
	$messagesScript = '';
	$ancienNom = decodeTexte($_POST['porteDocumentsAncienNom']);
	$nouveauNom = $_POST['porteDocumentsNouveauNom'];
	
	if (isset($_POST['porteDocumentsRenommageCopie']) && $_POST['porteDocumentsRenommageCopie'] == 'copie')
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
		$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s n'existe pas. Renommage en %2\$s impossible."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
	}
	elseif (empty($nouveauNom))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun nouveau nom spécifié.") . "</li>\n";
	}
	elseif (file_exists($nouveauNom))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà. Renommage de %2\$s impossible."), '<code>' . securiseTexte($nouveauNom) . '</code>', '<code>' . securiseTexte($ancienNom) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementPermis($ancienNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), '<code>' . securiseTexte($ancienNom) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementPermis($nouveauNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement de %1\$s n'est pas gérable par le porte-documents."), '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
	}
	elseif (!adminEmplacementModifiable($ancienNom, $adminDossierRacinePorteDocuments))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Il n'est pas possible d'effectuer cette action sur le dossier %1\$s."), '<code>' . securiseTexte($ancienNom) . '</code>') . "</li>\n";
	}
	else
	{
		$majListeDossiers = TRUE;
		
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
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible. La destination se trouve à l'intérieur de la source."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
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

if (isset($_POST['porteDocumentsCreation']))
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
		$fichierAcreerNom = decodeTexte($_POST['porteDocumentsCreationNom']);
		
		if (isset($_POST['filtrerNom']) && in_array('filtrer', $_POST['filtrerNom']))
		{
			$fichierAcreerAncienNom = $fichierAcreerNom;
			$titrePotentiel = extension($fichierAcreerAncienNom, TRUE);
			$casse = '';
			
			if (in_array('min', $_POST['filtrerNom']))
			{
				$casse = 'min';
			}
			
			$fichierAcreerNom = filtreChaine($fichierAcreerNom, $casse);
			
			if ($fichierAcreerNom != $fichierAcreerAncienNom)
			{
				$messagesScript .= '<li>' . sprintf(T_("Filtrage de %1\$s en %2\$s effectué."), '<code>' . securiseTexte($fichierAcreerAncienNom) . '</code>', '<code>' . securiseTexte($fichierAcreerNom) . '</code>') . "</li>\n";
			}
		}
		else
		{
			$titrePotentiel = '';
		}
		
		$fichierAcreerNom = decodeTexte($_POST['porteDocumentsCreationChemin']) . '/' . $fichierAcreerNom;
		
		if (!preg_match("|^$adminDossierRacinePorteDocuments/|i", $fichierAcreerNom))
		{
			$fichierAcreerNom = "$adminDossierRacinePorteDocuments/$fichierAcreerNom";
		}
		
		if ($fichierAcreerType == 'FichierModeleMarkdown')
		{
			$fichierMarkdownAcreerNom = $fichierAcreerNom . '.mkd';
		}
		
		if (file_exists($fichierAcreerNom))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), '<code>' . securiseTexte($fichierAcreerNom) . '</code>') . "</li>\n";
		}
		elseif ($fichierAcreerType == 'FichierModeleMarkdown' && file_exists($fichierMarkdownAcreerNom))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("%1\$s existe déjà."), '<code>' . securiseTexte($fichierMarkdownAcreerNom) . '</code>') . "</li>\n";
		}
		elseif (!adminEmplacementPermis($fichierAcreerNom, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier %1\$s ne se trouve pas dans un emplacement gérable par le porte-documents."), '<code>' . securiseTexte($fichierAcreerNom) . '</code>') . "</li>\n";
		}
		else
		{
			if ($fichierAcreerType == 'Dossier')
			{
				$majListeDossiers = TRUE;
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
						$messagesScript .= '<li>'; // Ouverture de `<li>`.
						$messagesScript .= sprintf(T_("Création du fichier %1\$s effectuée."), '<code>' . securiseTexte($fichierAcreerNom) . '</code>');
						
						if ($fichierAcreerType == 'FichierModeleHtml' || $fichierAcreerType == 'FichierModeleMarkdown')
						{
							$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($fichierAcreerNom) . $dossierCourantDansUrl . '#messages', $urlRacine . '/' . encodeTexte(substr("$cheminPage/$page", 3)));
						}
						else
						{
							$messagesScript .= ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($fichierAcreerNom) . $dossierCourantDansUrl . '#messages">' . T_("Vous pouvez l'éditer.") . "</a>";
						}
						
						$messagesScript .= "</li>\n"; // Fermeture de `<li>`.
					}
					else
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . securiseTexte($fichierAcreerNom) . '</code>') . "</li>\n";
					}
					
					if ($fichierAcreerType == 'FichierModeleMarkdown')
					{
						if (@touch($fichierMarkdownAcreerNom))
						{
							$messagesScript .= '<li>'; // Ouverture de `<li>`.
							$messagesScript .= sprintf(T_("Création du fichier %1\$s effectuée."), '<code>' . securiseTexte($fichierMarkdownAcreerNom) . '</code>');
							$messagesScript .= sprintf(T_("Vous pouvez <a href=\"%1\$s\">l'éditer</a> ou <a href=\"%2\$s\">l'afficher</a>."), 'porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($fichierMarkdownAcreerNom) . $dossierCourantDansUrl . '#messages', $urlRacine . '/' . encodeTexte(substr("$cheminPage/$page.mkd", 3)));
							$messagesScript .= "</li>\n"; // Fermeture de `<li>`.
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Création du fichier %1\$s impossible."), '<code>' . securiseTexte($fichierMarkdownAcreerNom) . '</code>') . "</li>\n";
						}
					}
					
					if (($fichierAcreerType == 'FichierModeleHtml' && file_exists($fichierAcreerNom)) || ($fichierAcreerType == 'FichierModeleMarkdown' && file_exists($fichierAcreerNom) && file_exists($fichierMarkdownAcreerNom)))
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
							if (isset($_POST['porteDocumentsCreationVar']))
							{
								$fichierAcreerVar = securiseTexte($_POST['porteDocumentsCreationVar']);
							}
							else
							{
								$fichierAcreerVar = array ();
							}
							
							$contenu = '';
							$contenu .= '<?php' . "\n";
							$varPageModeleBaliseH1 = FALSE;
							
							foreach ($fichierAcreerVar as $var)
							{
								switch ($var)
								{
									case 'apercu':
										$contenu .= '$apercu = "";' . "\n";
										break;
										
									case 'auteur':
										$contenu .= '$auteur = ' . var_export($auteurParDefaut, TRUE) . ";\n";
										break;
										
									case 'baliseH1':
										$varPageModeleBaliseH1 = TRUE;
										$contenu .= '$baliseH1 = "';
										
										if (!empty($titrePotentiel))
										{
											$contenu .= $titrePotentiel;
										}
										else
										{
											$contenu .= T_("Titre de premier niveau");
										}
										
										$contenu .= '";' . "\n";
										break;
										
									case 'baliseTitle':
										$contenu .= '$baliseTitle = "';
										
										if (!empty($titrePotentiel))
										{
											$contenu .= $titrePotentiel;
										}
										else
										{
											$contenu .= T_("Titre (contenu de la balise `title`)");
										}
										
										$contenu .= '";' . "\n";
										break;
										
									case 'boitesDeroulantes':
										$contenu .= '$boitesDeroulantes = "";' . "\n";
										break;
										
									case 'boitesDeroulantesAlaMain':
										$contenu .= '$boitesDeroulantesAlaMain = TRUE;' . "\n";
										break;
										
									case 'classesBody':
										$contenu .= '$classesBody = "";' . "\n";
										break;
										
									case 'classesContenu':
										$contenu .= '$classesContenu = "";' . "\n";
										break;
										
									case 'courrielContact':
										$contenu .= '$courrielContact = "@";' . "\n";
										break;
										
									case 'dateCreation':
										$contenu .= '$dateCreation = "' . date('Y-m-d') . '";' . "\n";
										break;
										
									case 'dateRevision':
										$contenu .= '$dateRevision = "' . date('Y-m-d') . '";' . "\n";
										break;
										
									case 'desactiverCache':
										$contenu .= '$desactiverCache = TRUE;' . "\n";
										break;
										
									case 'desactiverCachePartiel':
										$contenu .= '$desactiverCachePartiel = TRUE;' . "\n";
										break;
										
									case 'description':
										$contenu .= '$description = "' . T_("Description de la page.") . '";' . "\n";
										break;
										
									case 'idCategorie':
										$contenu .= '$idCategorie = "";' . "\n";
										break;
										
									case 'idGalerie':
										$contenu .= '$idGalerie = "";' . "\n";
										break;
										
									case 'inclureCodeFenetreJavascript':
										$contenu .= '$inclureCodeFenetreJavascript = TRUE;' . "\n";
										break;
										
									case 'infosPublication':
										if ($afficherInfosPublicationParDefaut)
										{
											$contenu .= '$infosPublication = FALSE;' . "\n";
										}
										else
										{
											$contenu .= '$infosPublication = TRUE;' . "\n";
										}
										
										break;
										
									case 'langue':
										$contenu .= '$langue = ' . var_export($langueParDefaut, TRUE) . ";\n";
										break;
										
									case 'licence':
										$contenu .= '$licence = ' . var_export($licenceParDefaut, TRUE) . ";\n";
										break;
										
									case 'lienPage':
										if ($afficherLienPageParDefaut)
										{
											$contenu .= '$lienPage = FALSE;' . "\n";
										}
										else
										{
											$contenu .= '$lienPage = TRUE;' . "\n";
										}
										
										break;
										
									case 'motsCles':
										$contenu .= '$motsCles = "";' . "\n";
										break;
										
									case 'partageCourriel':
										if ($activerPartageCourrielParDefaut)
										{
											$contenu .= '$partageCourriel = FALSE;' . "\n";
										}
										else
										{
											$contenu .= '$partageCourriel = TRUE;' . "\n";
										}
										
										break;
										
									case 'partageReseaux':
										if ($activerPartageReseauxParDefaut)
										{
											$contenu .= '$partageReseaux = FALSE;' . "\n";
										}
										else
										{
											$contenu .= '$partageReseaux = TRUE;' . "\n";
										}
										
										break;
										
									case 'robots':
										$contenu .= '$robots = ' . var_export($robotsParDefaut, TRUE) . ";\n";
										break;
										
									case 'tableDesMatieres':
										if ($afficherTableDesMatieresParDefaut)
										{
											$contenu .= '$tableDesMatieres = FALSE;' . "\n";
										}
										else
										{
											$contenu .= '$tableDesMatieres = TRUE;' . "\n";
										}
										
										break;
								}
							}
							
							$contenu .= 'include "' . $cheminInclude . 'inc/premier.inc.php";' . "\n";
							$contenu .= '?>' . "\n";
							$contenu .= "\n";
							
							if ($fichierAcreerType == 'FichierModeleHtml')
							{
								if (!$varPageModeleBaliseH1)
								{
									$contenu .= '<h1>' . T_("Titre de la page") . "</h1>\n";
									$contenu .= "\n";
								}
								
								$contenu .= "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.</p>\n";
							}
							elseif ($fichierAcreerType == 'FichierModeleMarkdown')
							{
								$contenu .= '<?php echo mkd("' . superBasename($fichierMarkdownAcreerNom) . '"); ?>' . "\n";
							}
							
							$contenu .= "\n";
							$contenu .= '<?php include $racine . "/inc/dernier.inc.php"; ?>';
							fputs($fic, $contenu);
							fclose($fic);
						}
						else
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout d'un modèle de page web dans le fichier %1\$s impossible."), '<code>' . securiseTexte($cheminPage . '/' . $page) . '</code>') . "</li>\n";
						}
						
						if ($fichierAcreerType == 'FichierModeleMarkdown')
						{
							if ($fic = @fopen($cheminPage . '/' . "$page.mkd", 'a'))
							{
								$contenu = '';
								
								if (!$varPageModeleBaliseH1)
								{
									$contenu .= '# ' . T_("Titre de la page") . "\n";
									$contenu .= "\n";
								}
								
								$contenu .= "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.";
								fputs($fic, $contenu);
								fclose($fic);
							}
							else
							{
								$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout d'un modèle de page web avec syntaxe Markdown dans le fichier %1\$s impossible."), '<code>' . securiseTexte($cheminPage . '/' . "$page.mkd") . '</code>') . "</li>\n";
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

if ((!$adminFiltreTypesMime || ($adminFiltreTypesMime && !empty($adminTypesMimePermis))) && (isset($_POST['porteDocumentsAjouter']) || (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > adminPhpIniOctets(ini_get('post_max_size')))))
{
	$messagesScript = '';
	$dossier = decodeTexte($_POST['porteDocumentsAjouterDossier']);
	
	if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > adminPhpIniOctets(ini_get('post_max_size')))
	{
		// Explications: À la page <http://www.php.net/manual/fr/ini.core.php#ini.post-max-size>, on peut lire: «Dans le cas où la taille des données reçues par la méthode POST est plus grande que post_max_size , les superglobales  $_POST et $_FILES  seront vides». Je repère donc une erreur potentielle par le test ci-dessus.
		
		$messagesScript .= '<li class="erreur">' . T_("Le fichier téléchargé excède la taille de <code>post_max_size</code>, configurée dans le <code>php.ini</code>.") . "</li>\n";
	}
	elseif (empty($_FILES['porteDocumentsAjouterFichier']['name']))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun fichier spécifié.") . "</li>\n";
	}
	elseif (file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']) && filesize($_FILES['porteDocumentsAjouterFichier']['tmp_name']) > $tailleMaxFichier)
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichier), $tailleMaxFichier) . "</li>\n";
	}
	elseif (empty($dossier))
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucun dossier spécifié.") . "</li>\n";
	}
	elseif (!adminEmplacementPermis($dossier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("L'emplacement spécifié pour l'ajout (%1\$s) n'est pas gérable par le porte-documents."), "<code>" . securiseTexte($dossier) . "</code>") . "</li>\n";
	}
	elseif ($_FILES['porteDocumentsAjouterFichier']['error'])
	{
		$messagesScript .= adminMessageFilesError($_FILES['porteDocumentsAjouterFichier']['error']);
	}
	else
	{
		$nomFichier = superBasename($_FILES['porteDocumentsAjouterFichier']['name']);
		$nouveauNomFichier = superBasename($_POST['porteDocumentsAjouterNom']);
		
		if (!empty($nouveauNomFichier))
		{
			$nomFichier = $nouveauNomFichier;
		}
		
		if (isset($_POST['filtrerNom']) && in_array('filtrer', $_POST['filtrerNom']))
		{
			$ancienNomFichier = $nomFichier;
			$casse = '';
			
			if (in_array('min', $_POST['filtrerNom']))
			{
				$casse = 'min';
			}
			
			$nomFichier = filtreChaine($nomFichier, $casse);
			
			if ($nomFichier != $ancienNomFichier)
			{
				$messagesScript .= '<li>' . sprintf(T_("Filtrage de %1\$s en %2\$s effectué."), '<code>' . securiseTexte($ancienNomFichier) . '</code>', '<code>' . securiseTexte($nomFichier) . '</code>') . "</li>\n";
			}
		}
		
		if (file_exists($dossier . '/' . $nomFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà dans le dossier %2\$s."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($dossier) . '</code>') . "</li>\n";
		}
		else
		{
			if (@move_uploaded_file($_FILES['porteDocumentsAjouterFichier']['tmp_name'], $dossier . '/' . $nomFichier))
			{
				$typeMime = typeMime($dossier . '/' . $nomFichier);
				
				if (!adminTypeMimePermis($typeMime, $adminFiltreTypesMime, $adminTypesMimePermis))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), '<code>' . securiseTexte($nomFichier) . '</code>', "<code>$typeMime</code>") . "</li>\n";
					@unlink($dossier . '/' . $nomFichier);
				}
				else
				{
					$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s dans %2\$s effectué."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($dossier) . '</code>') . "</li>\n";
				}
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ajout de %1\$s dans %2\$s impossible."), '<code>' . securiseTexte($nomFichier) . '</code>', '<code>' . securiseTexte($dossier) . '</code>') . "</li>\n";
			}
		}
	}
	
	if (!empty($_FILES) && file_exists($_FILES['porteDocumentsAjouterFichier']['tmp_name']))
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

if ($majListeDossiers)
{
	$listeDossiers = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, TRUE);
	
	if ($adminAfficherSousDossiersDansListe)
	{
		$listeDossiersPourListe = $listeDossiers;
	}
	else
	{
		$listeDossiersPourListe = adminListeFiltreeDossiers($adminDossierRacinePorteDocuments, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, $adminAfficherSousDossiersDansListe);
	}
}

echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
echo "<div>\n";

if (!empty($dossierCourant))
{
	echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
}

echo '<div class="boite">' . "\n";
echo '<h2 id="fichiersEtDossiers">' . T_("Liste des fichiers et dossiers") . "</h2>\n";

$boiteActions = '';
$boiteActions .= "<fieldset>\n";
$boiteActions .= '<legend>' . T_("Actions") . "</legend>";

$boiteActions .= ' <a href="#ajouter">' . T_("Ajouter") . '</a> |';

$boiteActions .= ' <a href="#creer">' . T_("Créer") . '</a> |';

$boiteActions .= ' <input type="submit" name="porteDocumentsCopie" value="' . T_("Copier") . '" /> |';

$boiteActions .= ' <input type="submit" name="porteDocumentsDeplacement" value="' . T_("Déplacer") . '" /> |';

$boiteActions .= ' <input type="submit" name="porteDocumentsPermissions" value="' . T_("Permissions") . '" /> |';

$boiteActions .= ' <input type="submit" name="porteDocumentsSuppression" value="' . T_("Supprimer") . '" /> |';

$boiteActions = substr($boiteActions, 0, -2);
$boiteActions .= "</fieldset>\n";

echo $boiteActions;

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
	echo '<div id="divContenuDossierAdminPorteDoc">' . "\n";
	echo '<h3 id="contenuDossier" class="bDtitre">' . sprintf(T_("Contenu du dossier %1\$s"), '<code>' . securiseTexte($dossierAparcourir) . '</code>') . "</h3>\n";
	
	echo '<div class="bDcorps afficher">' . "\n";
	if (!file_exists($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'existe pas."), '<code>' . securiseTexte($dossierAparcourir) . '</code>') . "</p>\n";
	}
	elseif (!is_dir($dossierAparcourir))
	{
		echo '<p class="erreur">' . sprintf(T_("%1\$s n'est pas un dossier."), '<code>' . securiseTexte($dossierAparcourir) . '</code>') . "</p>\n";
	}
	elseif (!adminEmplacementPermis($dossierAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
	{
		echo '<p class="erreur">' . sprintf(T_("L'emplacement %1\$s n'est pas gérable par le porte-documents."), '<code>' . securiseTexte($dossierAparcourir) . '</code>') . "</p>\n";
	}
	else
	{
		$dossierDeDepartAparcourir = $dossierAparcourir;
		$listeFormateeFichiers = adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierDeDepartAparcourir, $dossierAparcourir, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminAfficherSousDossiersDansContenu, $adminTypeFiltreAffichageDansContenu, $tableauFiltresAffichageDansContenu, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminActiverInfobulle, $galerieQualiteJpg, $galerieCouleurAlloueeImage);
		
		if (!empty($listeFormateeFichiers))
		{
			echo "<ul class=\"porteDocumentsListe\">\n";
			
			foreach ($listeFormateeFichiers as $cle => $valeur1)
			{
				echo '<li class="porteDocumentsListeContenuDossier"><strong>' . T_("Dossier") . ' <code>' . securiseTexte($cle) . "</code></strong><ul class=\"porteDocumentsListeDernierNiveau\">\n";
				$cle = array ();
				
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
	
	echo "</div><!-- /.bDcorps -->\n";
	echo "</div><!-- /#divContenuDossierAdminPorteDoc -->\n";
	echo "</div><!-- /.sousBoite -->\n";
	
	echo '<p id="porteDocumentsLienHaut"><a href="#ancres">' . T_("Haut") . "</a></p>\n";
}

/* ____________________ Listage des dossiers. ____________________ */

echo '<div class="sousBoite">' . "\n";
echo '<div id="divListeDossiersAdminPorteDoc">' . "\n";
echo '<h3 id="listeDossiers" class="bDtitre">';

if ($adminAfficherSousDossiersDansListe)
{
	echo T_("Liste des dossiers");
}
else
{
	echo T_("Liste des dossiers de premier niveau");
}

echo "</h3>\n";

echo "<ul class=\"bDcorps afficher porteDocumentsListe porteDocumentsListeDernierNiveau\">\n";

$classe = 'impair';

foreach ($listeDossiersPourListe as $listeDossier)
{
	$dossierMisEnForme = '';
	$dossierMisEnForme .= "<li class=\"$classe\">";
	
	if (adminEmplacementModifiable($listeDossier, $adminDossierRacinePorteDocuments))
	{
		$disabled = '';
	}
	else
	{
		$disabled = " disabled=\"disabled\"";
	}
	
	$dossierMisEnForme .= '<input type="checkbox" name="porteDocumentsFichiers[]" value="' . encodeTexte($listeDossier) . "\"$disabled />";
	$dossierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
	
	$dossierMisEnForme .= "<a href=\"$urlRacineAdmin/telecharger.admin.php?fichier=" . encodeTexte($listeDossier) . "\"><img src=\"$urlRacineAdmin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
	$dossierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
	
	if (adminEmplacementModifiable($listeDossier, $adminDossierRacinePorteDocuments))
	{
		$dossierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . 'action=renommer&amp;valeur=' . encodeTexte($listeDossier) . $dossierCourantDansUrl . "#messages\"><img src=\"$urlRacineAdmin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
	}
	else
	{
		$dossierMisEnForme .= "<img src=\"$urlRacineAdmin/fichiers/renommer-desactive.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" />\n";
	}
	
	$dossierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
	
	if ($adminActiverInfobulle['listeDesDossiers'])
	{
		$dossierMisEnForme .= adminInfobulle($racineAdmin, $urlRacineAdmin, $listeDossier, TRUE, $adminTailleCache, $galerieQualiteJpg, $galerieCouleurAlloueeImage);
		$dossierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
	}
	
	$dossierMisEnForme .= "<a  class=\"porteDocumentsFichier\" href=\"$adminAction" . $adminSymboleUrl . 'action=parcourir&amp;valeur=' . encodeTexte($listeDossier) . '&amp;dossierCourant=' . encodeTexte($listeDossier) . '#fichiersEtDossiers" title="' . sprintf(T_("Parcourir «%1\$s»"), securiseTexte($listeDossier)) . '"><code>' . securiseTexte($listeDossier) . "</code></a></li>\n";
	echo $dossierMisEnForme;
	$classe = ($classe == 'impair') ? 'pair' : 'impair';
}

echo "</ul>\n";
echo "</div><!-- /#divListeDossiersAdminPorteDoc -->\n";
echo "</div><!-- /.sousBoite -->\n";

echo $boiteActions;

echo "</div><!-- /.boite -->\n";
echo "</div>\n";
echo "</form>\n";

/* ____________________ Ajout. ____________________ */

if (!$adminFiltreTypesMime || ($adminFiltreTypesMime && !empty($adminTypesMimePermis)))
{
	echo '<div class="boite">' . "\n";
	echo '<h2 id="ajouter">' . T_("Ajouter un fichier") . "</h2>\n";
	
	echo '<div class="aideAdminPorteDocuments aide">' . "\n";
	echo '<h3 class="bDtitre">' . T_("Aide") . "</h3>\n";
	
	echo '<div class="bDcorps">' . "\n";
	echo "<p>Choisir le fichier à ajouter et le dossier parent. Optionnellement, vous pouvez renommer le fichier.</p>\n";
	
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
		echo "</div><!-- .bDcorps -->\n";
		echo "</div><!-- .aideAdminPorteDocuments -->\n";
		
		echo '<div id="typesMimePermisAdminPorteDoc">' . "\n";
		echo '<p class="bDtitre"><strong>' . T_("Liste détaillée des types de fichier permis") . "</strong></p>\n";

		echo '<div class="bDcorps">' . "\n";
		echo "<ul>\n";

		$typesMimePermisAdminPorteDoc = '';

		foreach ($adminTypesMimePermis as $extensions => $type)
		{
			$typesMimePermisAdminPorteDoc .= "<li>$type ($extensions)</li>";
		}

		echo $typesMimePermisAdminPorteDoc;
		echo "</ul>\n";
		echo "</div>\n";
		echo "</div>\n";
		
		echo '<p>' . sprintf(T_("<strong>Taille maximale d'un transfert de fichier:</strong> %1\$s Mio (%2\$s octets)."), octetsVersMio($tailleMaxFichier), $tailleMaxFichier) . "</p>\n";
	}
	
	echo '<form action="' . $adminAction . '#messages" method="post" enctype="multipart/form-data">' . "\n";
	echo "<div>\n";
	echo "<fieldset>\n";
	echo '<legend>' . T_("Options") . "</legend>\n";
	
	echo '<p><label for="inputPorteDocumentsAjouterFichier">' . T_("Fichier:") . "</label><br />\n" . '<input id="inputPorteDocumentsAjouterFichier" type="file" name="porteDocumentsAjouterFichier" size="25"/>' . "</p>\n";
	
	echo '<p><label for="selectPorteDocumentsAjouterDossier">' . T_("Dossier:") . "</label><br />\n" . '<select id="selectPorteDocumentsAjouterDossier" name="porteDocumentsAjouterDossier" size="1">' . "\n";
	
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
		
		echo '<option value="' . encodeTexte($valeur) . '"' . $selected. '>' . securiseTexte($valeur) . "</option>\n";
	}
	
	echo "</select></p>\n";

	echo "<p><label for=\"inputPorteDocumentsAjouterNom\">Nouveau nom du fichier (optionnel):</label><br />\n";
	echo '<input id="inputPorteDocumentsAjouterNom" type="text" name="porteDocumentsAjouterNom" size="25" value="" /></p>' . "\n";;
	
	echo '<fieldset class="optionsAvanceesAdminPorteDocuments">' . "\n";
	echo '<legend class="bDtitre">' . T_("Options avancées") . "</legend>\n";
	echo '<div class="bDcorps">' . "\n";
	echo "<ul>\n" . '<li><input id="inputPorteDocumentsAjouterFiltrerNom" type="checkbox" name="filtrerNom[]" value="filtrer" /> <label for="inputPorteDocumentsAjouterFiltrerNom">' . sprintf(T_("Filtrer le nom. Le filtre convertit automatiquement les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e») et ensuite les caractères différents de %1\$s par un tiret."), '<code>a-zA-Z0-9.-_+</code>') . "</label>\n<ul>\n" . '<li><input id="inputPorteDocumentsAjouterFiltrerCasse" type="checkbox" name="filtrerNom[]" value="min" /> <label for="inputPorteDocumentsAjouterFiltrerCasse">' . T_("Filtrer également les majuscules en minuscules.") . "</label></li>\n</ul>\n</li>\n</ul>\n";
	echo "</div><!-- /.bDcorps -->\n";
	echo "</fieldset>\n";
	echo "</fieldset>\n";
	
	echo '<p><input type="submit" name="porteDocumentsAjouter" value="' . T_("Ajouter") . '" />' . "</p>\n";
	
	if (!empty($dossierCourant))
	{
		echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
	}
	
	echo "</div></form>\n";
	echo "</div><!-- /.boite -->\n";
}

/* ____________________ Création. ____________________ */

echo '<div class="boite">' . "\n";
echo '<h2 id="creer">' . T_("Créer un fichier ou un dossier") . "</h2>\n";

echo '<div class="aideAdminPorteDocuments aide">' . "\n";
echo '<h3 class="bDtitre">' . T_("Aide") . "</h3>\n";

echo '<div class="bDcorps">' . "\n";
echo '<p>' . T_("Choisir le dossier parent et spécifier le nom du nouveau fichier ou dossier à créer. De nouveaux dossiers parents peuvent être ajoutés dans le nom, séparés par des barres obliques (<code>/</code>). Ils seront créés en même temps que le fichier ou le dossier.") . "</p>\n";
echo "</div><!-- .bDcorps -->\n";
echo "</div><!-- .aideAdminPorteDocuments -->\n";

echo '<form action="' . $adminAction . '#messages" method="post">' . "\n";
echo "<div>\n";
echo "<fieldset>\n";
echo '<legend>' . T_("Options") . "</legend>\n";

echo '<p>' . sprintf(T_("<label for=\"%1\$s\">Chemin</label> et <label for=\"%2\$s\">nom</label>:"), "selectPorteDocumentsCreationChemin", "inputPorteDocumentsCreationNom") . "<br />\n";

echo '<select id="selectPorteDocumentsCreationChemin" name="porteDocumentsCreationChemin" size="1">' . "\n";

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
	
	echo '<option value="' . encodeTexte($valeur) . '"' . $selected. '>' . securiseTexte($valeur) . "</option>\n";
}

echo '</select>';
echo ' / <input id="inputPorteDocumentsCreationNom" type="text" name="porteDocumentsCreationNom" size="25" value="" /></p>' . "\n";

echo '<p><label for="selectPorteDocumentsCreationType">' . T_("Type:") . "</label><br />\n";
echo '<select id="selectPorteDocumentsCreationType" name="porteDocumentsCreationType" size="1">' . "\n";
echo '<option value="FichierModeleHtml">' .  T_("Page web modèle") . "</option>\n";
echo '<option value="FichierModeleMarkdown">' .  T_("Page web modèle avec syntaxe Markdown") . "</option>\n";
echo '<option value="FichierVide">' . T_("Fichier vide") . "</option>\n";
echo '<option value="Dossier">' . T_("Dossier") . "</option>\n";
echo "</select></p>\n";

echo '<fieldset class="optionsAvanceesAdminPorteDocuments">' . "\n";
echo '<legend class="bDtitre">' . T_("Options avancées") . "</legend>\n";
echo '<div class="bDcorps">' . "\n";
echo '<p id="varPageModele"><label class="bDtitre" for="selectPorteDocumentsCreationVar">' . T_("Si le type est une page web modèle, ajouter au début du fichier les variables suivantes:") . "</label><br />\n";
echo '<select id="selectPorteDocumentsCreationVar" class="bDcorps afficher" name="porteDocumentsCreationVar[]" multiple="multiple">' . "\n";
echo '<option value="apercu">$apercu</option>' . "\n";
echo '<option value="auteur">$auteur</option>' . "\n";
echo '<option value="baliseH1">$baliseH1</option>' . "\n";
echo '<option value="baliseTitle">$baliseTitle</option>' . "\n";
echo '<option value="boitesDeroulantes">$boitesDeroulantes</option>' . "\n";
echo '<option value="boitesDeroulantesAlaMain">$boitesDeroulantesAlaMain</option>' . "\n";
echo '<option value="classesBody">$classesBody</option>' . "\n";
echo '<option value="classesContenu">$classesContenu</option>' . "\n";
echo '<option value="courrielContact">$courrielContact</option>' . "\n";
echo '<option value="dateCreation">$dateCreation</option>' . "\n";
echo '<option value="dateRevision">$dateRevision</option>' . "\n";
echo '<option value="desactiverCache">$desactiverCache</option>' . "\n";
echo '<option value="desactiverCachePartiel">$desactiverCachePartiel</option>' . "\n";
echo '<option value="description">$description</option>' . "\n";
echo '<option value="idCategorie">$idCategorie</option>' . "\n";
echo '<option value="idGalerie">$idGalerie</option>' . "\n";
echo '<option value="inclureCodeFenetreJavascript">$inclureCodeFenetreJavascript</option>' . "\n";
echo '<option value="infosPublication">$infosPublication</option>' . "\n";
echo '<option value="langue">$langue</option>' . "\n";
echo '<option value="licence">$licence</option>' . "\n";
echo '<option value="lienPage">$lienPage</option>' . "\n";
echo '<option value="motsCles">$motsCles</option>' . "\n";
echo '<option value="partageCourriel">$partageCourriel</option>' . "\n";
echo '<option value="partageReseaux">$partageReseaux</option>' . "\n";
echo '<option value="robots">$robots</option>' . "\n";
echo '<option value="tableDesMatieres">$tableDesMatieres</option>' . "\n";
echo "</select></p>\n";

echo "<ul>\n" . '<li><input id="inputPorteDocumentsCreationFiltrerNom" type="checkbox" name="filtrerNom[]" value="filtrer" /> <label for="inputPorteDocumentsCreationFiltrerNom">' . sprintf(T_("Filtrer le nom du fichier ou du dossier créé. Le filtre convertit automatiquement les caractères accentués par leur équivalent non accentué (par exemple «é» devient «e») et ensuite les caractères différents de %1\$s par un tiret."), '<code>a-zA-Z0-9.-_+</code>') . "</label>\n<ul>\n" . '<li><input id="inputPorteDocumentsCreationFiltrerCasse" type="checkbox" name="filtrerNom[]" value="min" /> <label for="inputPorteDocumentsCreationFiltrerCasse">' . T_("Filtrer également les majuscules en minuscules.") . "</label></li>\n</ul>\n</li>\n</ul>\n";
echo "</div><!-- /.bDcorps -->\n";
echo "</fieldset>\n";
echo "</fieldset>\n";

echo '<p><input type="submit" name="porteDocumentsCreation" value="' . T_("Créer") . '" />' . "</p>\n";

if (!empty($dossierCourant))
{
	echo '<input type="hidden" name="porteDocumentsDossierCourant" value="' . encodeTexte($dossierCourant) . '" />' . "\n";
}

echo "</div>\n";
echo "</form>\n";
echo "</div><!-- /.boite -->\n";

include $racineAdmin . '/inc/dernier.inc.php';
?>
