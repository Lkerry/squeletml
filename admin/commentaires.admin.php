<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Commentaires");
$boitesDeroulantes = '.aideAdminCommentaires .configActuelleAdminCommentaires';
$boitesDeroulantes .= ' .contenuFichierPourSauvegarde .liParent';
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Gestion des commentaires"); ?></h1>

<div id="boiteMessages" class="boite">
	<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

	<?php
	$messagesScript = '';
	$urlPage = '';
	
	if (!empty($_POST['page']))
	{
		$urlPage = decodeTexte($_POST['page']);
		
		$lienEditionPage = '';
		$valeurHrefUrlPage = $urlPage;
		
		if (strpos($urlPage, 'galerie.php?') === 0)
		{
			$valeurHrefUrlPage = variableGet(2, $urlPage, 'langue', LANGUE_ADMIN);
		}
		else
		{
			$cheminRelatifPage = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, decodeTexte($urlPage));
			$lienEditionPage = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminRelatifPage) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminRelatifPage)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifPage)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifPage)) . '" width="16" height="16" /></a>';
		}
		
		$messagesScript .= '<li>' . sprintf(T_("Page sélectionnée: %1\$s"), '<a href="' . $urlRacine . '/' . $valeurHrefUrlPage . '"><code>' . securiseTexte($urlPage) . '</code></a>' . $lienEditionPage) . "</li>\n";
		
		$cheminConfigCommentaires = cheminConfigCommentaires($racine, $urlRacine, $urlPage, '', TRUE);
		$cheminRelatifConfigCommentaires = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, $cheminConfigCommentaires);
		$lienEditionConfigCommentaires = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminRelatifConfigCommentaires) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminRelatifConfigCommentaires)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigCommentaires)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigCommentaires)) . '" width="16" height="16" /></a>';
		$messagesScript .= '<li>' . sprintf(T_("Fichier de configuration des commentaires associé: %1\$s"), '<code>' . securiseTexte($cheminRelatifConfigCommentaires) . '</code>' . $lienEditionConfigCommentaires) . "</li>\n";
		
		$cheminConfigAbonnementsCommentaires = cheminConfigAbonnementsCommentaires($cheminConfigCommentaires);
		$cheminRelatifConfigAbonnementsCommentaires = adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, $cheminConfigAbonnementsCommentaires);
		$lienEditionConfigAbonnementsCommentaires = ' <a href="porte-documents.admin.php?action=editer&amp;valeur=' . encodeTexte($cheminRelatifConfigAbonnementsCommentaires) . '&amp;dossierCourant=' . encodeTexte(dirname($cheminRelatifConfigAbonnementsCommentaires)) . '#messages"><img src="' . $urlRacineAdmin . '/fichiers/editer.png" alt="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigAbonnementsCommentaires)) . '" title="' . sprintf(T_("Éditer «%1\$s»"), securiseTexte($cheminRelatifConfigAbonnementsCommentaires)) . '" width="16" height="16" /></a>';
		$messagesScript .= '<li>' . sprintf(T_("Fichier de configuration des abonnements aux notifications associé: %1\$s"), '<code>' . securiseTexte($cheminRelatifConfigAbonnementsCommentaires) . '</code>' . $lienEditionConfigAbonnementsCommentaires) . "</li>\n";
	}
	else
	{
		$messagesScript .= '<li class="erreur">' . T_("Aucune page sélectionnée.") . "</li>\n";
	}
	
	if (isset($_POST['action']))
	{
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Liste des commentaires de la page sélectionnée") . "</h3>\n";
		
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
				$listePages = '';
				$i = 0;
				
				foreach ($listeCommentaires as $idCommentaire => $infosCommentaire)
				{
					$listePages .= '<li class="liParent"><span class="bDtitre">' . sprintf(T_("Commentaire %1\$s"), '<code>' . securiseTexte($idCommentaire) . '</code></span>') . "\n";
					$listePages .= "<ul class=\"nonTriable bDcorps afficher\">\n";
					
					// Identifiant du formulaire.
					
					if (!isset($infosCommentaire['idFormulaire']))
					{
						$infosCommentaire['idFormulaire'] = '';
					}
					
					$listePages .= '<li><label for="inputIdFormulaire-' . $idCommentaire . '"><code>idFormulaire=</code></label><input type="hidden" name="idCommentaire[]" value="' . $idCommentaire . '" /><input id="inputIdFormulaire-' . $idCommentaire . '" type="text" name="idFormulaire[' . $idCommentaire . ']" value="' . $infosCommentaire['idFormulaire'] . "\" /></li>\n";
					
					// IP.
					
					if (!isset($infosCommentaire['ip']))
					{
						$infosCommentaire['ip'] = '';
					}
					
					$listePages .= '<li><label for="inputIp-' . $idCommentaire . '"><code>ip=</code></label><input id="inputIp-' . $idCommentaire . '" type="text" name="ip[' . $idCommentaire . ']" value="' . $infosCommentaire['ip'] . "\" /></li>\n";
					
					// Date.
					
					if (!isset($infosCommentaire['date']))
					{
						$infosCommentaire['date'] = '';
					}
					
					if (!empty($infosCommentaire['date']))
					{
						$dateAffichee = ' (<code>' . $infosCommentaire['date'] . '=' . date('Y-m-d H:i T', $infosCommentaire['date']) . '</code>)';
					}
					else
					{
						$dateAffichee = '';
					}
					
					$listePages .= '<li><label for="inputDate-' . $idCommentaire . '"><code>date=</code></label><input id="inputDate-' . $idCommentaire . '" type="text" name="date[' . $idCommentaire . ']" value="' . $infosCommentaire['date'] . '" />' . $dateAffichee . "</li>\n";
					
					// Nom.
					
					if (!isset($infosCommentaire['nom']))
					{
						$infosCommentaire['nom'] = '';
					}
					
					$listePages .= '<li><label for="inputNom-' . $idCommentaire . '"><code>nom=</code></label><input id="inputNom-' . $idCommentaire . '" type="text" name="nom[' . $idCommentaire . ']" value="' . $infosCommentaire['nom'] . "\" /></li>\n";
					
					// Courriel.
					
					if (!isset($infosCommentaire['courriel']))
					{
						$infosCommentaire['courriel'] = '';
					}
					
					$listePages .= '<li><label for="inputCourriel-' . $idCommentaire . '"><code>courriel=</code></label><input id="inputCourriel-' . $idCommentaire . '" type="text" name="courriel[' . $idCommentaire . ']" value="' . $infosCommentaire['courriel'] . "\" /></li>\n";
					
					// Site.
					
					if (!isset($infosCommentaire['site']))
					{
						$infosCommentaire['site'] = '';
					}
					
					$listePages .= '<li><label for="inputSite-' . $idCommentaire . '"><code>site=</code></label><input id="inputSite-' . $idCommentaire . '" type="text" name="site[' . $idCommentaire . ']" value="' . $infosCommentaire['site'] . "\" /></li>\n";
					
					// Notification.
					
					if (!isset($infosCommentaire['notification']))
					{
						$infosCommentaire['notification'] = 0;
					}
					
					$listePages .= '<li><label for="notification-' . $idCommentaire . '"><code>notification=</code></label>';
					$listePages .= '<select id="notification-' . $idCommentaire . '" name="notification[' . $idCommentaire . ']">' . "\n";
					$listePages .= '<option value="1"';
					
					if ($infosCommentaire['notification'] == 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Activée") . "</option>\n";
					$listePages .= '<option value="0"';
					
					if ($infosCommentaire['notification'] != 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Désactivée") . "</option>\n";
					$listePages .= "</select>\n";
					$listePages .= "</li>\n";
					
					// Langue de la page.
					
					if (!isset($infosCommentaire['languePage']))
					{
						$infosCommentaire['languePage'] = '';
					}
					
					$listePages .= '<li><label for="inputLanguePage-' . $idCommentaire . '"><code>languePage=</code></label><input id="inputLanguePage-' . $idCommentaire . '" type="text" name="languePage[' . $idCommentaire . ']" value="' . $infosCommentaire['languePage'] . "\" /></li>\n";
					
					// A été modéré.
					
					if (!isset($infosCommentaire['aEteModere']))
					{
						$infosCommentaire['aEteModere'] = 0;
					}
					
					$listePages .= '<li><label for="aEteModere-' . $idCommentaire . '"><code>aEteModere=</code></label>';
					$listePages .= '<select id="aEteModere-' . $idCommentaire . '" name="aEteModere[' . $idCommentaire . ']">' . "\n";
					$listePages .= '<option value="1"';
					
					if ($infosCommentaire['aEteModere'] == 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Oui") . "</option>\n";
					$listePages .= '<option value="0"';
					
					if ($infosCommentaire['aEteModere'] != 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Non") . "</option>\n";
					$listePages .= "</select>\n";
					$listePages .= "</li>\n";
					
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
					
					$listePages .= '<li><label for="afficher-' . $idCommentaire . '"><code>afficher=</code></label>';
					$listePages .= '<select id="afficher-' . $idCommentaire . '" name="afficher[' . $idCommentaire . ']">' . "\n";
					$listePages .= '<option value="1"';
					
					if ($infosCommentaire['afficher'] == 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Oui") . "</option>\n";
					$listePages .= '<option value="0"';
					
					if ($infosCommentaire['afficher'] != 1)
					{
						$listePages .= ' selected="selected"';
					}
					
					$listePages .= '>' . T_("Non") . "</option>\n";
					$listePages .= "</select>\n";
					$listePages .= "</li>\n";
					
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
					
					$listePages .= '<li><label for="message-' . $idCommentaire . '"><code>message[]=</code></label><textarea id="message-' . $idCommentaire . '" cols="50" rows="10" name="message[' . $idCommentaire . ']">' . securiseTexte($message) . "</textarea></li>\n";
					$listePages .= "</ul>\n";
					$listePages .= "</li>\n";
				}
				
				$contenuFormulaire .= '<div class="aideAdminCommentaires aide">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . T_("Aide") . "</h4>\n";
				
				$contenuFormulaire .= "<div class=\"bDcorps\">\n";
				$contenuFormulaire .= '<p>' . sprintf(T_("Pour désactiver l'affichage d'un commentaire, simplement définir à «Non» le paramètre %1\$s associé."), '<code>afficher</code>') . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Pour supprimer un commentaire, simplement effacer tout le contenu du message associé.") . "</p>\n";
				
				$contenuFormulaire .= '<p>' . T_("Aussi, la liste des commentaires est triable. Pour ce faire, cliquer sur la flèche correspondant au commentaire à déplacer et glisser-la à l'endroit désiré à l'intérieur de la liste.") . "</p>\n";
				$contenuFormulaire .= "</div><!-- /.bDcorps -->\n";
				$contenuFormulaire .= "</div><!-- /.aideAdminCommentaires -->\n";
				
				$contenuFormulaire .= "<fieldset>\n";
				$contenuFormulaire .= '<legend>' . T_("Options") . "</legend>\n";
				
				$contenuFormulaire .= '<div class="configActuelleAdminCommentaires">' . "\n";
				$contenuFormulaire .= '<h4 class="bDtitre">' . T_("Configuration actuelle") . "</h4>\n";
				
				if (empty($listePages))
				{
					$contenuFormulaire = '';
					echo '<p class="erreur">' . sprintf(T_("La page %1\$s ne contient aucun commentaire."), '<code>' . securiseTexte($urlPage) . '</code>') . "</p>\n";
				}
				else
				{
					$contenuFormulaire .= "<ul class=\"triable bDcorps afficher\">\n";
					$contenuFormulaire .= $listePages;
					$contenuFormulaire .= "</ul>\n";
					$contenuFormulaire .= "</div><!-- /.configActuelleAdminCommentaires -->\n";
					$contenuFormulaire .= "</fieldset>\n";
					
					$contenuFormulaire .= '<input type="hidden" name="page" value="' . $urlPage . '" />' . "\n";
					
					$contenuFormulaire .= '<p><input type="submit" name="modifsCommentaires" value="' . T_("Enregistrer les modifications") . '" /></p>' . "\n";
			
					$contenuFormulaire .= "</div>\n";
					$contenuFormulaire .= "</form>\n";
				}
			}
		}
		
		echo adminMessagesScript($messagesScript);
		echo $contenuFormulaire;
		echo "</div><!-- /.sousBoite -->\n";
	}
	elseif (isset($_POST['modifsCommentaires']))
	{
		echo '<div class="sousBoite">' . "\n";
		echo '<h3>' . T_("Enregistrement des modifications aux commentaires de la page sélectionnée") . "</h3>\n" ;
		
		if (!empty($urlPage))
		{
			$contenuFichier = '';
			
			if (!empty($_POST['idCommentaire']))
			{
				foreach ($_POST['idCommentaire'] as $idCommentaire)
				{
					$messageDansConfig = '';
					
					if (isset($_POST['message'][$idCommentaire]))
					{
						$messageDansConfig = messageDansConfigCommentaires($racine, $_POST['message'][$idCommentaire], $attributNofollowLiensCommentaires);
					}
					
					if (!empty($messageDansConfig))
					{
						$contenuFichier .= "[$idCommentaire]\n";
						
						// Identifiant du formulaire.
						
						$contenuFichier .= 'idFormulaire=';
						
						if (isset($_POST['idFormulaire'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['idFormulaire'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// IP.
						
						$contenuFichier .= 'ip=';
						
						if (isset($_POST['ip'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['ip'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// Date.
						
						$contenuFichier .= 'date=';
						
						if (isset($_POST['date'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['date'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// Nom.
						
						$contenuFichier .= 'nom=';
						
						if (isset($_POST['nom'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['nom'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// Courriel.
						
						$contenuFichier .= 'courriel=';
						
						if (isset($_POST['courriel'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['courriel'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// Site.
						
						$contenuFichier .= 'site=';
						
						if (isset($_POST['site'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['site'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// Notification.
						
						$contenuFichier .= 'notification=';
						
						if (isset($_POST['notification'][$idCommentaire]) && $_POST['notification'][$idCommentaire] == 1)
						{
							$contenuFichier .= 1;
						}
						else
						{
							$contenuFichier .= 0;
						}
						
						$contenuFichier .= "\n";
						
						// Langue de la page.
						
						$contenuFichier .= 'languePage=';
						
						if (isset($_POST['languePage'][$idCommentaire]))
						{
							$contenuFichier .= $_POST['languePage'][$idCommentaire];
						}
						
						$contenuFichier .= "\n";
						
						// A été modéré.
						
						$contenuFichier .= 'aEteModere=';
						
						if (isset($_POST['aEteModere'][$idCommentaire]) && $_POST['aEteModere'][$idCommentaire] == 1)
						{
							$contenuFichier .= 1;
						}
						else
						{
							$contenuFichier .= 0;
						}
						
						$contenuFichier .= "\n";
						
						// Afficher.
						
						$contenuFichier .= 'afficher=';
						
						if (!isset($_POST['afficher'][$idCommentaire]))
						{
							if ($moderationCommentaires)
							{
								$_POST['afficher'][$idCommentaire] = 0;
							}
							else
							{
								$_POST['afficher'][$idCommentaire] = 1;
							}
						}
						
						if ($_POST['afficher'][$idCommentaire] == 1)
						{
							$contenuFichier .= 1;
						}
						else
						{
							$contenuFichier .= 0;
						}
						
						$contenuFichier .= "\n";
						
						// Message.
						
						$tableauMessageDansConfig = explode("\n", trim($messageDansConfig));
						
						foreach ($tableauMessageDansConfig as $ligneMessageDansConfig)
						{
							$contenuFichier .= "message[]=$ligneMessageDansConfig\n";
						}
						
						$contenuFichier .= "\n";
					}
				}
			}
			
			$messagesScript .= adminEnregistreConfigCommentaires($racine, $cheminConfigCommentaires, $contenuFichier);
		}
		
		echo adminMessagesScript($messagesScript);
		echo "</div><!-- /.sousBoite -->\n";
	}
	?>
</div><!-- /#boiteMessages -->

<div class="boite">
	<h2 id="config"><?php echo T_("Configuration actuelle"); ?></h2>
	
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
	
	<p><a href="porte-documents.admin.php?action=editer&amp;valeur=../site/inc/config.inc.php#messages"><?php echo T_("Modifier cette configuration."); ?></a></p>
</div><!-- /.boite -->

<div class="boite">
	<h2 id="gerer"><?php echo T_("Gérer les commentaires"); ?></h2>

	<form action="<?php echo $adminAction; ?>#messages" method="post">
		<div>
			<fieldset>
				<legend><?php echo T_("Options"); ?></legend>
				
				<p>
					<?php $listePagesAvecCommentaires = adminListePagesAvecCommentaires($racine); ?>
					
					<?php if (!empty($listePagesAvecCommentaires)): ?>
						<?php $disabled = ''; ?>
						<?php printf(T_("<label for=\"%1\$s\">Choisir une page parmi celles ayant au moins un commentaire</label>:"), "gererSelectPage"); ?><br />
						<select id="gererSelectPage" name="page">
							<?php foreach ($listePagesAvecCommentaires as $listePage): ?>
								<option value="<?php echo encodeTexte($listePage); ?>"><?php echo securiseTexte($listePage); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<?php $disabled = ' disabled="disabled"'; ?>
						<?php echo T_("Aucune page ayant au moins un commentaire."); ?>
					<?php endif; ?>
				</p>
			</fieldset>
			
			<p><input type="submit" name="action" value="<?php echo T_('Gérer les commentaires'); ?>"<?php echo $disabled; ?> /></p>
		</div>
	</form>
</div><!-- /.boite -->

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
