<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Commentaires et abonnements");
$boitesDeroulantes = '.aideAdminCommentaires .configActuelle .configActuelleAdminCommentaires';
$boitesDeroulantes .= ' .contenuFichierPourSauvegarde .liParent';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des commentaires et des abonnements"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>
	
	<?php
	if (!empty($_GET['gererType']) && ($_GET['gererType'] == 'commentaires' || $_GET['gererType'] == 'commentairesModeration'))
	{
		$_POST['gererType'] = $_GET['gererType'];
	}
	
	if (!empty($_GET['page']))
	{
		$_POST['page'] = $_GET['page'];
	}
	
	$messagesScriptUrlPage = '';
	$urlPage = '';
	
	if (!empty($_POST['page']))
	{
		if (!empty($_GET['page']))
		{
			$urlPage = decodeTexteGet($_POST['page']);
		}
		else
		{
			$urlPage = decodeTexte($_POST['page']);
		}
		
		$lienEditionPage = '';
		$valeurHrefUrlPage = $urlPage;
		
		if (strpos($urlPage, 'galerie.php?') === 0)
		{
			$valeurHrefUrlPage = variableGet(2, $urlPage, 'langue', eval(LANGUE_ADMIN));
		}
		else
		{
			$cheminRelatifPage = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($urlPage));
			$lienEditionPage = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexteGet($cheminRelatifPage) . '&amp;dossierCourant=' . encodeTexteGet(dirname($cheminRelatifPage)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifPage)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifPage)) . '" width="16" height="16" /></a>';
		}
		
		$messagesScriptUrlPage .= '<li>' . sprintf(T_("Page sélectionnée: %1\$s"), '<a class="lienSurCode" href="' . $urlRacine . '/' . $valeurHrefUrlPage . '"><code>' . securiseTexte($urlPage) . '</code></a>' . $lienEditionPage) . "</li>\n";
		
		$cheminConfigCommentaires = cheminConfigCommentaires($racine, $urlRacine, $urlPage, TRUE);
		$cheminRelatifConfigCommentaires = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, $cheminConfigCommentaires);
		$lienEditionConfigCommentaires = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexteGet($cheminRelatifConfigCommentaires) . '&amp;dossierCourant=' . encodeTexteGet(dirname($cheminRelatifConfigCommentaires)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigCommentaires)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigCommentaires)) . '" width="16" height="16" /></a>';
		$messagesScriptUrlPage .= '<li>' . sprintf(T_("Fichier de configuration des commentaires associé: %1\$s"), '<code>' . securiseTexte($cheminRelatifConfigCommentaires) . '</code>' . $lienEditionConfigCommentaires) . "</li>\n";
		
		$cheminConfigAbonnementsCommentaires = cheminConfigAbonnementsCommentaires($cheminConfigCommentaires);
		$cheminRelatifConfigAbonnementsCommentaires = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, $cheminConfigAbonnementsCommentaires);
		$lienEditionConfigAbonnementsCommentaires = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexteGet($cheminRelatifConfigAbonnementsCommentaires) . '&amp;dossierCourant=' . encodeTexteGet(dirname($cheminRelatifConfigAbonnementsCommentaires)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigAbonnementsCommentaires)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigAbonnementsCommentaires)) . '" width="16" height="16" /></a>';
		$messagesScriptUrlPage .= '<li>' . sprintf(T_("Fichier de configuration des abonnements aux notifications associé: %1\$s"), '<code>' . securiseTexte($cheminRelatifConfigAbonnementsCommentaires) . '</code>' . $lienEditionConfigAbonnementsCommentaires) . "</li>\n";
	}
	else
	{
		$messagesScriptUrlPage .= '<li class="erreur">' . T_("Aucune page sélectionnée.") . "</li>\n";
	}
	
	##################################################################
	#
	# Publication en ligne d'un commentaire.
	#
	##################################################################
	if (isset($_GET['action']) && $_GET['action'] == 'publier' && isset($_GET['id']) && !empty($urlPage))
	{
		$messagesScript = '';
		$messagesScript .= $messagesScriptUrlPage;
		$listeCommentaires = super_parse_ini_file($cheminConfigCommentaires, TRUE);
		
		if ($listeCommentaires === FALSE)
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigCommentaires) . '</code>') . "</li>\n";
		}
		elseif (isset($listeCommentaires[$_GET['id']]))
		{
			$listeCommentaires[$_GET['id']]['enAttenteDeModeration'] = 0;
			$listeCommentaires[$_GET['id']]['afficher'] = 1;
			gereNotificationsEnAttente($racine, $_GET['id'], 1);
			$messagesScript .= '<li>' . sprintf(T_("Le commentaire %1\$s a été publié."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
			$contenuFichier = '';
			$retourConversionTableauVersTexte = adminTableauConfigCommentairesVersTexte($racine, $commentairesChampsObligatoires, $moderationCommentaires, $listeCommentaires);
			
			if (!empty($retourConversionTableauVersTexte['config']))
			{
				$contenuFichier = $retourConversionTableauVersTexte['config'];
			}
			
			if (!empty($retourConversionTableauVersTexte['messagesScript']))
			{
				$messagesScript .= $retourConversionTableauVersTexte['messagesScript'];
			}
			
			if (!empty($contenuFichier))
			{
				$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigCommentaires, $contenuFichier);
			}
			else
			{
				$messagesScript .= adminUnlink($cheminConfigCommentaires);
				$messagesScript .= adminUnlink($cheminConfigAbonnementsCommentaires);
			}
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le commentaire %1\$s n'a pas été trouvé."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript, T_("Publication en ligne d'un commentaire"));
	}
	##################################################################
	#
	# Désactivation de l'affichage en ligne d'un commentaire.
	#
	##################################################################
	elseif (isset($_GET['action']) && $_GET['action'] == 'cacher' && isset($_GET['id']) && !empty($urlPage))
	{
		$messagesScript = '';
		$messagesScript .= $messagesScriptUrlPage;
		$listeCommentaires = super_parse_ini_file($cheminConfigCommentaires, TRUE);
		
		if ($listeCommentaires === FALSE)
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigCommentaires) . '</code>') . "</li>\n";
		}
		elseif (isset($listeCommentaires[$_GET['id']]))
		{
			$listeCommentaires[$_GET['id']]['enAttenteDeModeration'] = 0;
			$listeCommentaires[$_GET['id']]['afficher'] = 0;
			gereNotificationsEnAttente($racine, $_GET['id'], 0);
			$messagesScript .= '<li>' . sprintf(T_("L'affichage du commentaire %1\$s a été désactivé."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
			$contenuFichier = '';
			$retourConversionTableauVersTexte = adminTableauConfigCommentairesVersTexte($racine, $commentairesChampsObligatoires, $moderationCommentaires, $listeCommentaires);
			
			if (!empty($retourConversionTableauVersTexte['config']))
			{
				$contenuFichier = $retourConversionTableauVersTexte['config'];
			}
			
			if (!empty($retourConversionTableauVersTexte['messagesScript']))
			{
				$messagesScript .= $retourConversionTableauVersTexte['messagesScript'];
			}
			
			if (!empty($contenuFichier))
			{
				$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigCommentaires, $contenuFichier);
			}
			else
			{
				$messagesScript .= adminUnlink($cheminConfigCommentaires);
				$messagesScript .= adminUnlink($cheminConfigAbonnementsCommentaires);
			}
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le commentaire %1\$s n'a pas été trouvé."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript, T_("Désactivation de l'affichage en ligne d'un commentaire"));
	}
	##################################################################
	#
	# Suppression d'un commentaire.
	#
	##################################################################
	elseif (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id']) && !empty($urlPage))
	{
		$messagesScript = '';
		$messagesScript .= $messagesScriptUrlPage;
		$listeCommentaires = super_parse_ini_file($cheminConfigCommentaires, TRUE);
		
		if ($listeCommentaires === FALSE)
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigCommentaires) . '</code>') . "</li>\n";
		}
		elseif (isset($listeCommentaires[$_GET['id']]))
		{
			if (!empty($listeCommentaires[$_GET['id']]['pieceJointe']))
			{
				$messagesScript .= adminUnlink($racine . '/site/fichiers/commentaires/' . $listeCommentaires[$_GET['id']]['pieceJointe']);
			}
			
			unset($listeCommentaires[$_GET['id']]);
			gereNotificationsEnAttente($racine, $_GET['id'], 0);
			$messagesScript .= '<li>' . sprintf(T_("Le commentaire %1\$s a été supprimé."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
			$contenuFichier = '';
			$retourConversionTableauVersTexte = adminTableauConfigCommentairesVersTexte($racine, $commentairesChampsObligatoires, $moderationCommentaires, $listeCommentaires);
			
			if (!empty($retourConversionTableauVersTexte['config']))
			{
				$contenuFichier = $retourConversionTableauVersTexte['config'];
			}
			
			if (!empty($retourConversionTableauVersTexte['messagesScript']))
			{
				$messagesScript .= $retourConversionTableauVersTexte['messagesScript'];
			}
			
			if (!empty($contenuFichier))
			{
				$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigCommentaires, $contenuFichier);
			}
			else
			{
				$messagesScript .= adminUnlink($cheminConfigCommentaires);
				$messagesScript .= adminUnlink($cheminConfigAbonnementsCommentaires);
			}
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Le commentaire %1\$s n'a pas été trouvé."), '<code>' . securiseTexte($_GET['id']) . '</code>') . "</li>\n";
		}
		
		echo adminMessagesScript($messagesScript, T_("Suppression d'un commentaire"));
	}
	
	##################################################################
	#
	# Gestion des commentaires en attente de modération.
	#
	##################################################################
	if (isset($_POST['gererType']) && $_POST['gererType'] == 'commentairesModeration')
	{
		$messagesScript = '';
		$contenu = '';
		$codeListeCommentaires = '';
		$nombreCommentaires = 0;
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Liste des commentaires en attente de modération") . "</h3>\n";
		
		$listePagesAvecCommentaires = adminListePagesAvecCommentaires($racine);
		
		foreach ($listePagesAvecCommentaires as $listePage)
		{
			$cheminConfigCommentaires = cheminConfigCommentaires($racine, $urlRacine, $listePage, TRUE);
			$listeCommentaires = super_parse_ini_file($cheminConfigCommentaires, TRUE);
			
			if ($listeCommentaires === FALSE)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigCommentaires) . '</code>') . "</li>\n";
			}
			else
			{
				foreach ($listeCommentaires as $idCommentaire => $infosCommentaire)
				{
					if (!isset($infosCommentaire['enAttenteDeModeration']))
					{
						if ($moderationCommentaires)
						{
							$infosCommentaire['enAttenteDeModeration'] = 1;
						}
						else
						{
							$infosCommentaire['enAttenteDeModeration'] = 0;
						}
					}
					
					if ($infosCommentaire['enAttenteDeModeration'] != 1)
					{
						continue;
					}
					
					if (!isset($infosCommentaire['nom']))
					{
						$infosCommentaire['nom'] = '';
					}
					
					if (!isset($infosCommentaire['site']))
					{
						$infosCommentaire['site'] = '';
					}
					
					$auteurAffiche = auteurAfficheCommentaire($infosCommentaire['nom'], $infosCommentaire['site'], $attributNofollowLiensCommentaires);
					$dateAffichee = '';
					$heureAffichee = '';
					
					if (!empty($infosCommentaire['date']))
					{
						$dateAffichee = date('Y-m-d', $infosCommentaire['date']);
						$heureAffichee = date('H:i', $infosCommentaire['date']);
					}
					
					if (strpos($listePage, 'galerie.php?') === 0)
					{
						$valeurHrefListePage = variableGet(2, $listePage, 'langue', eval(LANGUE_ADMIN));
					}
					else
					{
						$valeurHrefListePage = $listePage;
					}
					
					$valeurHrefListePage = $urlRacine . '/' . $valeurHrefListePage;
					$codeListeCommentaires .= '<li id="' . $idCommentaire . '" class="liParent">' . "\n";
					$codeListeCommentaires .= '<p><strong>' . sprintf(T_("%1\$s a écrit sur la page %2\$s le %3\$s à %4\$s:"), $auteurAffiche, '<a class="lienSurCode" href="' . $valeurHrefListePage . '"><code>' . securiseTexte($listePage) . '</code></a>', $dateAffichee, $heureAffichee) . "</strong></p>\n";
					
					$message = '';
					
					if (!isset($infosCommentaire['message']))
					{
						$infosCommentaire['message'] = array ();
					}
					
					foreach ($infosCommentaire['message'] as $ligneMessage)
					{
						$message .= "$ligneMessage\n";
					}
					
					$message = trim($message);
					$codeListeCommentaires .= "<div class=\"contenuCommentaireAmoderer\">\n$message</div>\n";
					
					$infosSupplementaires = array ();
					$infosSupplementaires[] = sprintf(T_("Identifiant: %1\$s"), "<code>$idCommentaire</code>");
					
					if (!empty($infosCommentaire['pieceJointe']))
					{
						$infosSupplementaires[] = T_("Pièce jointe: ") .'<a href="' . $urlFichiers . '/commentaires/' . $infosCommentaire['pieceJointe'] . '">' . $infosCommentaire['pieceJointe'] . '</a>';
					}
					
					if (!empty($infosCommentaire['courriel']))
					{
						$infosSupplementaires[] = sprintf(T_("Courriel: %1\$s"), '<a href="mailto:' . $infosCommentaire['courriel'] . '">' . $infosCommentaire['courriel'] . '</a>');
					}
					
					if (!empty($infosCommentaire['ip']))
					{
						$infosSupplementaires[] = sprintf(T_("IP: %1\$s"), $infosCommentaire['ip']);
					}
					
					if (isset($infosCommentaire['notification']))
					{
						$infosSupplementaires[] = sprintf(T_("Notification: %1\$s"), $infosCommentaire['notification']);
					}
					
					$codeListeCommentaires .= '<ul class="infosSupplementaires">' . "\n";
					
					foreach ($infosSupplementaires as $infoSupplementaire)
					{
						$codeListeCommentaires .= "<li>$infoSupplementaire</li>\n";
					}
					
					$codeListeCommentaires .= "</ul>\n";
					$pageGet = encodeTexteGet($listePage);
					$codeListeCommentaires .= '<ul class="listeActions">' . "\n";
					$codeListeCommentaires .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=publier&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '&amp;gererType=commentairesModeration#messages">' . T_("Publier") . "</a></li>\n";
					
					$codeListeCommentaires .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=cacher&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '&amp;gererType=commentairesModeration#messages">' . T_("Désactiver l'affichage sans le supprimer") . "</a></li>\n";
					
					$codeListeCommentaires .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=supprimer&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '&amp;gererType=commentairesModeration#messages">' . T_("Supprimer") . "</a></li>\n";
					
					$codeListeCommentaires .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?gererType=commentaires&amp;page=' . $pageGet . '#' . $idCommentaire . '">' . T_("Modifier") . "</a></li>\n";
					$codeListeCommentaires .= "</ul>\n";
					$codeListeCommentaires .= "</li>\n";
					$nombreCommentaires++;
				}
			}
		}
		
		if (empty($codeListeCommentaires))
		{
			$messagesScript .= '<li class="erreur">' . T_("Aucun commentaire en attente de modération.") . "</li>\n";
		}
		else
		{
			$contenu .= '<div class="configActuelleAdminCommentaires">' . "\n";
			$contenu .= '<h4 class="bDtitre">' . sprintf(T_ngettext("Configuration actuelle (%1\$s commentaire)", "Configuration actuelle (%1\$s commentaires)", $nombreCommentaires), $nombreCommentaires) . "</h4>\n";
			
			$contenu .= "<ul class=\"bDcorps afficher\">\n$codeListeCommentaires</ul>\n";
			$contenu .= "</div><!-- /.configActuelleAdminCommentaires -->\n";
		}
		
		echo adminMessagesScript($messagesScript);
		echo $contenu;
		echo "</div><!-- /.sousBoite -->\n";
	}
	##################################################################
	#
	# Gestion des commentaires.
	#
	##################################################################
	elseif (isset($_POST['gererType']) && $_POST['gererType'] == 'commentaires')
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Liste des commentaires") . "</h3>\n";
		
		$contenuFormulaire = '';
		
		if (!empty($urlPage))
		{
			$listeCommentaires = super_parse_ini_file($cheminConfigCommentaires, TRUE);
			
			if ($listeCommentaires === FALSE)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigCommentaires) . '</code>') . "</li>\n";
			}
			else
			{
				$contenuFormulaire .= "<form action=\"$adminAction#messages\" method=\"post\">\n";
				$contenuFormulaire .= "<div>\n";
				$codeListeCommentaires = '';
				
				foreach ($listeCommentaires as $idCommentaire => $infosCommentaire)
				{
					$codeListeCommentaires .= '<li id="' . $idCommentaire . '" class="liParent"><span class="bDtitre">' . sprintf(T_("Commentaire %1\$s"), '<code>' . $idCommentaire . '</code></span>') . "\n";
					$codeListeCommentaires .= '<input type="hidden" name="idCommentaire[]" value="' . $idCommentaire . "\" />\n";
					$codeListeCommentaires .= "<ul class=\"nonTriable bDcorps afficher\">\n";
					
					// IP.
					
					if (!isset($infosCommentaire['ip']))
					{
						$infosCommentaire['ip'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputIp-' . $idCommentaire . '"><code>ip=</code></label><input id="inputIp-' . $idCommentaire . '" type="text" name="ip[' . $idCommentaire . ']" value="' . $infosCommentaire['ip'] . "\" /></li>\n";
					
					// Date.
					
					if (!isset($infosCommentaire['date']))
					{
						$infosCommentaire['date'] = '';
					}
					
					if (!empty($infosCommentaire['date']))
					{
						$dateAffichee = ' (<code>' . $infosCommentaire['date'] . '=' . date('Y-m-d H:i', $infosCommentaire['date']) . '</code>)';
					}
					else
					{
						$dateAffichee = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputDate-' . $idCommentaire . '"><code>date=</code></label><input id="inputDate-' . $idCommentaire . '" type="text" name="date[' . $idCommentaire . ']" value="' . $infosCommentaire['date'] . '" />' . $dateAffichee . "</li>\n";
					
					// Nom.
					
					if (!isset($infosCommentaire['nom']))
					{
						$infosCommentaire['nom'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputNom-' . $idCommentaire . '"><code>nom=</code></label><input id="inputNom-' . $idCommentaire . '" type="text" name="nom[' . $idCommentaire . ']" value="' . $infosCommentaire['nom'] . "\" /></li>\n";
					
					// Courriel.
					
					if (!isset($infosCommentaire['courriel']))
					{
						$infosCommentaire['courriel'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputCourriel-' . $idCommentaire . '"><code>courriel=</code></label><input id="inputCourriel-' . $idCommentaire . '" type="text" name="courriel[' . $idCommentaire . ']" value="' . $infosCommentaire['courriel'] . "\" /></li>\n";
					
					// Site.
					
					if (!isset($infosCommentaire['site']))
					{
						$infosCommentaire['site'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputSite-' . $idCommentaire . '"><code>site=</code></label><input id="inputSite-' . $idCommentaire . '" type="text" name="site[' . $idCommentaire . ']" value="' . $infosCommentaire['site'] . "\" /></li>\n";
					
					// Pièce jointe.
					
					if (!isset($infosCommentaire['pieceJointe']))
					{
						$infosCommentaire['pieceJointe'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputPieceJointe-' . $idCommentaire . '"><code>pieceJointe=</code></label><input id="inputPieceJointe-' . $idCommentaire . '" type="text" name="pieceJointe[' . $idCommentaire . ']" value="' . $infosCommentaire['pieceJointe'] . '" /><input type="hidden" name="pieceJointeAncienneValeur[' . $idCommentaire . ']" value="' . $infosCommentaire['pieceJointe'] . "\" /></li>\n";
					
					// Notification.
					
					if (!isset($infosCommentaire['notification']))
					{
						$infosCommentaire['notification'] = 0;
					}
					
					$codeListeCommentaires .= '<li><label for="notification-' . $idCommentaire . '"><code>notification=</code></label>';
					$codeListeCommentaires .= '<select id="notification-' . $idCommentaire . '" name="notification[' . $idCommentaire . ']">' . "\n";
					$codeListeCommentaires .= '<option value="1"';
					
					if ($infosCommentaire['notification'] == 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Activée") . "</option>\n";
					$codeListeCommentaires .= '<option value="0"';
					
					if ($infosCommentaire['notification'] != 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Désactivée") . "</option>\n";
					$codeListeCommentaires .= "</select>\n";
					$codeListeCommentaires .= "</li>\n";
					
					// Langue de la page.
					
					if (!isset($infosCommentaire['languePage']))
					{
						$infosCommentaire['languePage'] = '';
					}
					
					$codeListeCommentaires .= '<li><label for="inputLanguePage-' . $idCommentaire . '"><code>languePage=</code></label><input id="inputLanguePage-' . $idCommentaire . '" type="text" name="languePage[' . $idCommentaire . ']" value="' . $infosCommentaire['languePage'] . "\" /></li>\n";
					
					// En attente de modération.
					
					if (!isset($infosCommentaire['enAttenteDeModeration']))
					{
						if ($moderationCommentaires)
						{
							$infosCommentaire['enAttenteDeModeration'] = 1;
						}
						else
						{
							$infosCommentaire['enAttenteDeModeration'] = 0;
						}
					}
					
					if ($infosCommentaire['enAttenteDeModeration'] == 1)
					{
						$disabled = ' disabled="disabled"';
						$noteEnAttenteDeModeration = '<span class="noteChampCommentaire">' . T_("(utiliser le formulaire réservé à cette fin)") . "</span>\n";
					}
					else
					{
						$disabled = '';
						$noteEnAttenteDeModeration = '';
					}
					
					$codeListeCommentaires .= '<li><label for="enAttenteDeModeration-' . $idCommentaire . '"><code>enAttenteDeModeration=</code></label>';
					$codeListeCommentaires .= '<select id="enAttenteDeModeration-' . $idCommentaire . '" name="enAttenteDeModeration[' . $idCommentaire . ']"' . $disabled . '>' . "\n";
					$codeListeCommentaires .= '<option value="1"';
					
					if ($infosCommentaire['enAttenteDeModeration'] == 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Oui") . "</option>\n";
					$codeListeCommentaires .= '<option value="0"';
					
					if ($infosCommentaire['enAttenteDeModeration'] != 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Non") . "</option>\n";
					$codeListeCommentaires .= "</select>\n";
					$codeListeCommentaires .= $noteEnAttenteDeModeration;
					
					if ($infosCommentaire['enAttenteDeModeration'] == 1)
					{
						$codeListeCommentaires .= '<input type="hidden" name="enAttenteDeModerationLectureSeule[' . $idCommentaire . ']" value="' . $infosCommentaire['enAttenteDeModeration'] . "\" />\n";
					}
					
					$codeListeCommentaires .= "</li>\n";
					
					// Afficher.
					
					if (!isset($infosCommentaire['afficher']))
					{
						if ($moderationCommentaires)
						{
							$infosCommentaire['afficher'] = 0;
						}
						else
						{
							$infosCommentaire['afficher'] = 1;
						}
					}
					
					$codeListeCommentaires .= '<li><label for="afficher-' . $idCommentaire . '"><code>afficher=</code></label>';
					$codeListeCommentaires .= '<select id="afficher-' . $idCommentaire . '" name="afficher[' . $idCommentaire . ']">' . "\n";
					$codeListeCommentaires .= '<option value="1"';
					
					if ($infosCommentaire['afficher'] == 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Oui") . "</option>\n";
					$codeListeCommentaires .= '<option value="0"';
					
					if ($infosCommentaire['afficher'] != 1)
					{
						$codeListeCommentaires .= ' selected="selected"';
					}
					
					$codeListeCommentaires .= '>' . T_("Non") . "</option>\n";
					$codeListeCommentaires .= "</select>\n";
					$codeListeCommentaires .= "</li>\n";
					
					// Message
					
					$message = '';
					
					if (!isset($infosCommentaire['message']))
					{
						$infosCommentaire['message'] = array ();
					}
					
					foreach ($infosCommentaire['message'] as $ligneMessage)
					{
						$message .= "$ligneMessage\n";
					}
					
					$message = trim($message);
					
					$codeListeCommentaires .= '<li><label for="message-' . $idCommentaire . '"><code>message[]=</code></label><textarea id="message-' . $idCommentaire . '" cols="50" rows="10" name="message[' . $idCommentaire . ']">' . securiseTexte($message) . "</textarea></li>\n";
					$codeListeCommentaires .= "</ul>\n";
					$codeListeCommentaires .= '<p class="lienVersBoutonSoumettre"><a href="#enregistrerModifications">' . T_("Lien vers «Enregistrer»") . "</a></p>\n";
					$codeListeCommentaires .= "</li>\n";
				}
				
				$contenuFormulaire .= '<div class="aideAdminCommentaires aide">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
				
				$contenuFormulaire .= "<div class=\"bDcorps\">\n";
				$contenuFormulaire .= '<p>' . sprintf(T_("Pour désactiver l'affichage d'un commentaire, simplement définir à «Non» le paramètre %1\$s associé."), '<code>afficher</code>') . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Pour supprimer un commentaire, simplement effacer tout le contenu du message associé.") . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Pour supprimer une pièce jointe, effacer le contenu du champ associé.") . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Prendre note que les modifications effectuées dans ce formulaire ne sont pas appliquées aux abonnements aux notifications (nom, courriel, état de la notification). Pour ce faire, utiliser plutôt le formulaire réservé à cette fin.") . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Aussi, la liste des commentaires est triable. Pour ce faire, cliquer sur la flèche correspondant au commentaire à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
				$contenuFormulaire .= "</div><!-- /.bDcorps -->\n";
				$contenuFormulaire .= "</div><!-- /.aideAdminCommentaires -->\n";
				
				$contenuFormulaire .= "<ul>\n$messagesScriptUrlPage</ul>\n";
				
				$contenuFormulaire .= "<fieldset>\n";
				$contenuFormulaire .= '<legend>' . T_("Options") . "</legend>\n";
				
				$contenuFormulaire .= '<div class="configActuelleAdminCommentaires">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . sprintf(T_ngettext("Configuration actuelle (%1\$s commentaire)", "Configuration actuelle (%1\$s commentaires)", count($listeCommentaires)), count($listeCommentaires)) . "</h4>\n";
				
				if (empty($codeListeCommentaires))
				{
					$contenuFormulaire = '';
					echo '<p class="erreur">' . sprintf(T_("La page %1\$s ne contient aucun commentaire."), '<code>' . securiseTexte($urlPage) . '</code>') . "</p>\n";
				}
				else
				{
					$contenuFormulaire .= "<ul class=\"triable bDcorps afficher\">\n";
					$contenuFormulaire .= $codeListeCommentaires;
					$contenuFormulaire .= "</ul>\n";
					
					$contenuFormulaire .= '<p><input id="inputSupprimerTout" type="checkbox" name="inputSupprimerTout" value="supprimerTout" /> <label for="inputSupprimerTout">' . T_("Supprimer tous les commentaires de la page sélectionnée") . "</label></p>\n";
					$contenuFormulaire .= "</div><!-- /.configActuelleAdminCommentaires -->\n";
					$contenuFormulaire .= "</fieldset>\n";
					
					$contenuFormulaire .= '<input type="hidden" name="page" value="' . $urlPage . '" />' . "\n";
					
					$contenuFormulaire .= '<p><input id="enregistrerModifications" type="submit" name="modifsCommentaires" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
					$contenuFormulaire .= "</div>\n";
					$contenuFormulaire .= "</form>\n";
				}
			}
		}
		
		echo adminMessagesScript($messagesScript);
		echo $contenuFormulaire;
		echo "</div><!-- /.sousBoite -->\n";
	}
	##################################################################
	#
	# Gestion des abonnements.
	#
	##################################################################
	elseif (isset($_POST['gererType']) && $_POST['gererType'] == 'abonnements')
	{
		$messagesScript = '';
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Liste des abonnements aux notifications des nouveaux commentaires") . "</h3>\n";
		
		$contenuFormulaire = '';
		
		if (!empty($urlPage))
		{
			$listeAbonnements = super_parse_ini_file($cheminConfigAbonnementsCommentaires, TRUE);
			
			if ($listeAbonnements === FALSE)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminConfigAbonnementsCommentaires) . '</code>') . "</li>\n";
			}
			else
			{
				$contenuFormulaire .= "<form action=\"$adminAction#messages\" method=\"post\">\n";
				$contenuFormulaire .= "<div>\n";
				$codeListeAbonnements = '';
				$i = 0;
				
				foreach ($listeAbonnements as $courrielAbonnement => $infosAbonnement)
				{
					$codeListeAbonnements .= '<li class="liParent"><label for="inputCourriel-' . $i . '">' . T_("Courriel:") . '</label> <input id="inputCourriel-' . $i . '" type="text" name="courriel[' . $i . ']" value="' . $courrielAbonnement . "\" />\n";
					$codeListeAbonnements .= "<ul class=\"nonTriable\">\n";
					
					// Nom.
					
					if (!isset($infosAbonnement['nom']))
					{
						$infosAbonnement['nom'] = '';
					}
					
					$codeListeAbonnements .= '<li><label for="inputNom-' . $i . '"><code>nom=</code></label><input id="inputNom-' . $i . '" type="text" name="nom[' . $i . ']" value="' . $infosAbonnement['nom'] . "\" /></li>\n";
					
					// Identifiant de l'abonnement.
					
					if (!isset($infosAbonnement['idAbonnement']))
					{
						$infosAbonnement['idAbonnement'] = '';
					}
					
					$codeListeAbonnements .= '<li><label for="inputIdAbonnement-' . $i . '"><code>idAbonnement=</code></label><input id="inputIdAbonnement-' . $i . '" type="text" name="idAbonnement[' . $i . ']" value="' . $infosAbonnement['idAbonnement'] . "\" /></li>\n";
					
					$codeListeAbonnements .= "</ul>\n";
					$codeListeAbonnements .= '<p class="lienVersBoutonSoumettre"><a href="#enregistrerModifications">' . T_("Lien vers «Enregistrer»") . "</a></p>\n";
					$codeListeAbonnements .= "</li>\n";
					$i++;
				}
				
				$contenuFormulaire .= '<div class="aideAdminCommentaires aide">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
				
				$contenuFormulaire .= "<div class=\"bDcorps\">\n";
				$contenuFormulaire .= '<p>' . T_("Pour supprimer un abonnement, simplement effacer le contenu du champ du courriel associé.") . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Prendre note que les modifications effectuées dans ce formulaire ne sont pas appliquées au fichier de configuration des commentaires. Pour ce faire, utiliser plutôt le formulaire réservé à cette fin.") . "</p>\n";
				$contenuFormulaire .= "</div><!-- /.bDcorps -->\n";
				$contenuFormulaire .= "</div><!-- /.aideAdminCommentaires -->\n";
				
				$contenuFormulaire .= "<ul>\n$messagesScriptUrlPage</ul>\n";
				
				$contenuFormulaire .= "<fieldset>\n";
				$contenuFormulaire .= '<legend>' . T_("Options") . "</legend>\n";
				
				$contenuFormulaire .= '<div class="configActuelleAdminCommentaires">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . sprintf(T_ngettext("Configuration actuelle (%1\$s abonnement)", "Configuration actuelle (%1\$s abonnements)", count($listeAbonnements)), count($listeAbonnements)) . "</h4>\n";
				
				if (empty($codeListeAbonnements))
				{
					$contenuFormulaire = '';
					echo '<p class="erreur">' . sprintf(T_("La page %1\$s ne contient aucun abonnement."), '<code>' . securiseTexte($urlPage) . '</code>') . "</p>\n";
				}
				else
				{
					$contenuFormulaire .= "<ul class=\"nonTriable bDcorps afficher\">\n";
					$contenuFormulaire .= $codeListeAbonnements;
					$contenuFormulaire .= "</ul>\n";
					
					$contenuFormulaire .= '<p><input id="inputSupprimerTout" type="checkbox" name="inputSupprimerTout" value="supprimerTout" /> <label for="inputSupprimerTout">' . T_("Supprimer tous les abonnements de la page sélectionnée") . "</label></p>\n";
					$contenuFormulaire .= "</div><!-- /.configActuelleAdminCommentaires -->\n";
					
					$contenuFormulaire .= '<h4>' . T_("Ajouter un abonnement") . "</h4>\n";
					
					$contenuFormulaire .= "<ul>\n";
					$contenuFormulaire .='<li class="liParent"><label for="inputAjoutCourriel">' . T_("Courriel:") . '</label> <input id="inputAjoutCourriel" type="text" name="courrielAjout" value="" />' . "\n";
					$contenuFormulaire .= "<ul>\n";
					$contenuFormulaire .= '<li><label for="inputAjoutNom"><code>nom=</code></label><input id="inputAjoutNom" type="text" name="nomAjout" value="" /></li>' . "\n";
					$contenuFormulaire .= '<li><label for="inputAjoutIdAbonnement"><code>idAbonnement=</code></label><input id="inputAjoutIdAbonnement" type="text" name="idAbonnementAjout" value="' . chaineAleatoire(16) . '" /></li>' . "\n";
					$contenuFormulaire .= "</ul></li>\n";
					$contenuFormulaire .= "</ul>\n";
					$contenuFormulaire .= "</fieldset>\n";
					
					$contenuFormulaire .= '<input type="hidden" name="page" value="' . $urlPage . '" />' . "\n";
					
					$contenuFormulaire .= '<p><input id="enregistrerModifications" type="submit" name="modifsAbonnements" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
					$contenuFormulaire .= "</div>\n";
					$contenuFormulaire .= "</form>\n";
				}
			}
		}
		
		echo adminMessagesScript($messagesScript);
		echo $contenuFormulaire;
		echo "</div><!-- /.sousBoite -->\n";
	}
	
	##################################################################
	#
	# Enregistrement des modifications des commentaires.
	#
	##################################################################
	if (isset($_POST['modifsCommentaires']))
	{
		$messagesScript = '';
		$messagesScript .= $messagesScriptUrlPage;
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications aux commentaires") . "</h3>\n" ;
		
		if (!empty($urlPage))
		{
			$contenuFichier = '';
			
			if (!isset($_POST['inputSupprimerTout']) && !empty($_POST['idCommentaire']))
			{
				$tableauConfigCommentaires = array ();
				
				foreach ($_POST['idCommentaire'] as $idCommentaire)
				{
					$idCommentaire = securiseTexte($idCommentaire);
					
					if (!empty($_POST['pieceJointeAncienneValeur'][$idCommentaire]) && (empty($_POST['pieceJointe'][$idCommentaire]) || empty($_POST['message'][$idCommentaire])))
					{
						$messagesScript .= adminUnlink($racine . '/site/fichiers/commentaires/' . $_POST['pieceJointeAncienneValeur'][$idCommentaire]);
					}
					
					if (empty($_POST['message'][$idCommentaire]))
					{
						continue;
					}
					
					$tableauConfigCommentaires[$idCommentaire] = array ();
					
					if (!empty($_POST['ip'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['ip'] = securiseTexte($_POST['ip'][$idCommentaire]);
					}
					
					if (!empty($_POST['date'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['date'] = securiseTexte($_POST['date'][$idCommentaire]);
					}
					
					if (!empty($_POST['nom'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['nom'] = securiseTexte($_POST['nom'][$idCommentaire]);
					}
					
					if (!empty($_POST['courriel'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['courriel'] = securiseTexte($_POST['courriel'][$idCommentaire]);
					}
					
					if (!empty($_POST['site'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['site'] = securiseTexte($_POST['site'][$idCommentaire]);
					}
					
					if (!empty($_POST['pieceJointe'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['pieceJointe'] = securiseTexte($_POST['pieceJointe'][$idCommentaire]);
					}
					
					if (isset($_POST['notification'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['notification'] = $_POST['notification'][$idCommentaire];
					}
					
					if (!empty($_POST['languePage'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['languePage'] = securiseTexte($_POST['languePage'][$idCommentaire]);
					}
					
					if (isset($_POST['enAttenteDeModerationLectureSeule'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['enAttenteDeModeration'] = $_POST['enAttenteDeModerationLectureSeule'][$idCommentaire];
					}
					elseif (isset($_POST['enAttenteDeModeration'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['enAttenteDeModeration'] = $_POST['enAttenteDeModeration'][$idCommentaire];
					}
					
					if (isset($_POST['afficher'][$idCommentaire]))
					{
						$tableauConfigCommentaires[$idCommentaire]['afficher'] = $_POST['afficher'][$idCommentaire];
					}
					
					$messageDansConfig = messageDansConfigCommentaires($racine, $_POST['message'][$idCommentaire], $attributNofollowLiensCommentaires);
					$tableauConfigCommentaires[$idCommentaire]['message'] = explode("\n", trim($messageDansConfig));
				}
				
				$retourConversionTableauVersTexte = adminTableauConfigCommentairesVersTexte($racine, $commentairesChampsObligatoires, $moderationCommentaires, $tableauConfigCommentaires);
				
				if (!empty($retourConversionTableauVersTexte['config']))
				{
					$contenuFichier = $retourConversionTableauVersTexte['config'];
				}
				
				if (!empty($retourConversionTableauVersTexte['messagesScript']))
				{
					$messagesScript .= $retourConversionTableauVersTexte['messagesScript'];
				}
			}
			
			if (!empty($contenuFichier))
			{
				$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigCommentaires, $contenuFichier);
			}
			else
			{
				$messagesScript .= adminUnlink($cheminConfigCommentaires);
				$messagesScript .= adminUnlink($cheminConfigAbonnementsCommentaires);
			}
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	##################################################################
	#
	# Enregistrement des modifications des abonnements.
	#
	##################################################################
	elseif (isset($_POST['modifsAbonnements']))
	{
		$messagesScript = '';
		$messagesScript .= $messagesScriptUrlPage;
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications aux abonnements aux notifications des nouveaux commentaires") . "</h3>\n" ;
		
		if (!empty($urlPage))
		{
			$contenuFichier = '';
			
			if (!isset($_POST['courriel']))
			{
				$_POST['courriel'] = array ();
			}
			
			if (!empty($_POST['courrielAjout']) && !in_array($_POST['courrielAjout'], $_POST['courriel']))
			{
				end($_POST['courriel']);
				$indexFin = key($_POST['courriel']);
				
				if ($indexFin === NULL)
				{
					$indexFin = 0;
				}
				else
				{
					$indexFin++;
				}
				
				$_POST['courriel'][$indexFin] = $_POST['courrielAjout'];
				
				if (!empty($_POST['nomAjout']))
				{
					$_POST['nom'][$indexFin] = $_POST['nomAjout'];
				}
				
				if (empty($_POST['idAbonnementAjout']))
				{
					$_POST['idAbonnementAjout'] = chaineAleatoire(16);
				}
				
				$_POST['idAbonnement'][$indexFin] = $_POST['idAbonnementAjout'];
			}
			
			if (!isset($_POST['inputSupprimerTout']) && !empty($_POST['courriel']))
			{
				foreach ($_POST['courriel'] as $cle => $courrielAbonnement)
				{
					if (!empty($courrielAbonnement))
					{
						$courrielAbonnement = securiseTexte($courrielAbonnement);
						
						if (!courrielValide($courrielAbonnement))
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: le courriel %1\$s ne semble pas avoir une forme valide."), '<code>' . $courrielAbonnement . '</code>') . "</li>\n";
						}
						
						$contenuFichier .= "[$courrielAbonnement]\n";
						
						// Nom.
						
						$contenuFichier .= 'nom=';
						
						if (!empty($_POST['nom'][$cle]))
						{
							$contenuFichier .= securiseTexte($_POST['nom'][$cle]);
						}
						elseif ($commentairesChampsObligatoires['nom'])
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: selon la configuration des commentaires, le nom est obligatoire, mais celui associé à l'abonnement du courriel %1\$s est vide."), '<code>' . $courrielAbonnement . '</code>') . "</li>\n";
						}
						
						$contenuFichier .= "\n";
						
						// Identifiant de l'abonnement.
						
						if (empty($_POST['idAbonnement'][$cle]))
						{
							$_POST['idAbonnement'][$cle] = chaineAleatoire(16);
						}
						
						$contenuFichier .= 'idAbonnement=' . securiseTexte($_POST['idAbonnement'][$cle]) . "\n";
						$contenuFichier .= "\n";
					}
				}
			}
			
			$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigAbonnementsCommentaires, $contenuFichier, FALSE);
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite configActuelle">
	<h2 id="config" class="bDtitre"><?php echo T_("Configuration actuelle"); ?></h2>
	
	<div class="bDcorps afficher">
		<ul>
			<?php if ($ajoutCommentairesParDefaut): ?>
				<li><?php echo T_("L'ajout de commentaires est activé par défaut") . ' (<code>$ajoutCommentairesParDefaut = TRUE;</code>).'; ?></li>
			<?php else: ?>
				<li><?php echo T_("L'ajout de commentaires est désactivé par défaut") . ' (<code>$ajoutCommentairesParDefaut = FALSE;</code>).'; ?></li>
			<?php endif; ?>
			
			<?php if ($affichageCommentairesSiAjoutDesactive): ?>
				<li><?php echo T_("L'affichage des commentaires existants est activé pour les pages dont l'ajout est désactivé") . ' (<code>$affichageCommentairesSiAjoutDesactive = TRUE;</code>).'; ?></li>
			<?php else: ?>
				<li><?php echo T_("L'affichage des commentaires existants est désactivé pour les pages dont l'ajout est désactivé") . ' (<code>$affichageCommentairesSiAjoutDesactive = FALSE;</code>).'; ?></li>
			<?php endif; ?>
			
			<?php if ($moderationCommentaires): ?>
				<li><?php echo T_("La modération des commentaires est activée") . ' (<code>$moderationCommentaires = TRUE;</code>).'; ?></li>
			<?php else: ?>
				<li><?php echo T_("La modération des commentaires est désactivée") . ' (<code>$moderationCommentaires = FALSE;</code>).'; ?></li>
			<?php endif; ?>
		</ul>
		
		<p><a href="porte-documents.admin.php?action=editer&amp;valeur=<?php echo encodeTexteGet('../site/inc/config.inc.php'); ?>#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
	</div><!-- /.bDcorps -->
</div><!-- /.boite -->

<div class="boite">
	<h2 id="gerer"><?php echo T_("Gérer les commentaires et les abonnements"); ?></h2>
	
	<div class="aideAdminCommentaires aide">
		<h4 class="bDtitre"><?php echo T_("Aide"); ?></h4>
		
		<div class="bDcorps">
			<p><?php echo T_("Seules les pages ayant au moins un commentaire sont listées."); ?></p>
		</div><!-- /.bDcorps -->
	</div><!-- /.aideAdminCommentaires -->
	
	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<?php $nombreCommentairesAmoderer = adminNombreCommentairesAmoderer($racine, $urlRacine, $moderationCommentaires); ?>
				
				<fieldset>
					<legend><?php echo T_("Page individuelle"); ?></legend>
					
					<p>
						<?php $listePagesAvecCommentaires = adminListePagesAvecCommentaires($racine); ?>
						
						<?php if (!empty($listePagesAvecCommentaires)): ?>
							<?php $disabled = ''; ?>
							<label for="gererSelectPage"><?php echo T_("URL de la page:"); ?></label><br />
							<select id="gererSelectPage" name="page">
								<?php foreach ($listePagesAvecCommentaires as $listePage): ?>
									<option value="<?php echo encodeTexte($listePage); ?>"><?php echo securiseTexte($listePage); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else: ?>
							<?php $disabled = ' disabled="disabled"'; ?>
							<?php echo '<strong>' . T_("Aucune page ayant au moins un commentaire.") . '</strong>'; ?>
						<?php endif; ?>
					</p>
					
					<ul>
						<?php $checkedCommentaires = ''; ?>
						
						<?php if ($nombreCommentairesAmoderer == 0): ?>
							<?php $checkedCommentaires = ' checked="checked"'; ?>
						<?php endif; ?>
						
						<li><input id="gererInputListeCommentaires" type="radio" name="gererType" value="commentaires"<?php echo $checkedCommentaires; ?> /> <label for="gererInputListeCommentaires"><?php echo T_("Commentaires"); ?></label></li>
						<li><input id="gererInputListeAbonnements" type="radio" name="gererType" value="abonnements" /> <label for="gererInputListeAbonnements"><?php echo T_("Abonnements aux notifications des nouveaux commentaires"); ?></label></li>
					</ul>
				</fieldset>
				
				<fieldset>
					<legend><?php echo T_("Toutes les pages"); ?></legend>
					
					<ul>
						<?php $checkedCommentairesModeration = ''; ?>
						
						<?php if ($nombreCommentairesAmoderer > 0): ?>
							<?php $checkedCommentairesModeration = ' checked="checked"'; ?>
						<?php endif; ?>
						
						<li><input id="gererInputListeCommentairesModeration" type="radio" name="gererType" value="commentairesModeration"<?php echo $checkedCommentairesModeration; ?> /> <label for="gererInputListeCommentairesModeration"><?php printf(T_ngettext("Commentaires en attente de modération (%1\$s commentaire)", "Commentaires en attente de modération (%1\$s commentaires)", $nombreCommentairesAmoderer), $nombreCommentairesAmoderer); ?></label></li>
					</ul>
				</fieldset>
			</fieldset>
			
			<p><input type="submit" name="action" value="<?php echo T_('Gérer'); ?>"<?php echo $disabled; ?> /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
