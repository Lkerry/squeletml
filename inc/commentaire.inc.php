<?php
/*
Ce fichier construit et analyse le formulaire d'ajout d'un commentaire. Après son inclusion, la variable `$formulaireCommentaire` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Affectations.
$nom = infoGetAction($_GET['action'], 'nom');
$courriel = infoGetAction($_GET['action'], 'courriel');
$site = '';
$message = infoGetAction($_GET['action'], 'message');
$notification = FALSE;
$idFormulaireCommentaire = '';
$commentaireEnregistre = FALSE;
$formulaireCommentaire = '';

if ($formCommentairePieceJointeActivee)
{
	$commentairesTailleMaxPieceJointe = min($commentairesTailleMaxPieceJointe, phpIniOctets(ini_get('post_max_size')), phpIniOctets(ini_get('upload_max_filesize')));
}

// L'envoi du commentaire est demandé.
if (isset($_POST['envoyerCommentaire']) || ($formCommentairePieceJointeActivee && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > phpIniOctets(ini_get('post_max_size'))))
{
	if (!empty($_POST['nom']))
	{
		$nom = securiseTexte(trim($_POST['nom']));
	}
	
	if (!empty($_POST['courriel']))
	{
		$courriel = securiseTexte(trim($_POST['courriel']));
	}
	
	if (!empty($_POST['site']))
	{
		$site = securiseTexte(trim($_POST['site']));
	}
	
	$message = securiseTexte(trim($_POST['message']));
	
	if (isset($_POST['notification']) && !empty($courriel) && $courriel != $commentairesDestinataireNotification)
	{
		$notification = TRUE;
	}
	
	if (!empty($_POST['idFormulaire']))
	{
		$idFormulaireCommentaire = securiseTexte($_POST['idFormulaire']);
	}
	
	$messagesScript = '';
	$erreurFormulaire = FALSE;
	$erreurEnvoiFormulaire = FALSE;
	$formulaireValide = FALSE;
	
	if (empty($nom) && $commentairesChampsObligatoires['nom'])
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de nom ou de pseudo.") . "</li>\n";
	}
	
	if (empty($courriel) && $commentairesChampsObligatoires['courriel'])
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de courriel.") . "</li>\n";
	}
	elseif (!empty($courriel) && !courrielValide($courriel))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.") . "</li>\n";
	}
	
	if (empty($site) && $commentairesChampsObligatoires['site'])
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas spécifié de site Web.") . "</li>\n";
	}
	elseif (!empty($site) && !siteWebValide($site))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Votre site Web ne semble pas avoir une forme valide. Assurez-vous entre autres qu'il commence bien par <code>http://</code> ou <code>https://</code>.") . "</li>\n";
	}
	
	if (empty($message))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas écrit de commentaire.") . "</li>\n";
	}
	
	$nomPieceJointe = '';
	
	if ($formCommentairePieceJointeActivee)
	{
		if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > phpIniOctets(ini_get('post_max_size')))
		{
			// Explications: À la page <http://www.php.net/manual/fr/ini.core.php#ini.post-max-size>, on peut lire: «Dans le cas où la taille des données reçues par la méthode POST est plus grande que post_max_size , les superglobales  $_POST et $_FILES  seront vides». Je repère donc une erreur potentielle par le test ci-dessus.
			
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La pièce jointe doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($commentairesTailleMaxPieceJointe), $commentairesTailleMaxPieceJointe) . "</li>\n";
		}
		elseif (empty($_FILES['pieceJointe']['name']))
		{
			if ($commentairesChampsObligatoires['pieceJointe'])
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas ajouté de pièce jointe.") . "</li>\n";
			}
		}
		elseif (file_exists($_FILES['pieceJointe']['tmp_name']) && @filesize($_FILES['pieceJointe']['tmp_name']) > $commentairesTailleMaxPieceJointe)
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La pièce jointe doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($commentairesTailleMaxPieceJointe), $commentairesTailleMaxPieceJointe) . "</li>\n";
		}
		elseif ($_FILES['pieceJointe']['error'])
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Erreur lors de l'ajout de la pièce jointe.") . "</li>\n";
		}
		else
		{
			$dossierParentPieceJointe = $racine . '/site/fichiers/commentaires/';
			
			if (!file_exists($dossierParentPieceJointe))
			{
				@mkdir($dossierParentPieceJointe, octdec(755));
			}
			
			if (file_exists($dossierParentPieceJointe))
			{
				$nomPieceJointe = superBasename($_FILES['pieceJointe']['name']);
				$nomPieceJointe = filtreChaine($nomPieceJointe);
				
				if (!$commentairesLienPublicPieceJointe)
				{
					$nomPieceJointe = chaineAleatoire(16) . '-' . $nomPieceJointe;
				}
				
				if (file_exists($dossierParentPieceJointe . $nomPieceJointe))
				{
					for ($i = 2; $i < 1000; $i++)
					{
						$nomTemporairePieceJointe = nomSuffixe($nomPieceJointe, '-' . $i);
						
						if (!file_exists($dossierParentPieceJointe . $nomTemporairePieceJointe))
						{
							$nomPieceJointe = $nomTemporairePieceJointe;
							
							break;
						}
					}
				}
				
				$cheminPieceJointe = $dossierParentPieceJointe . $nomPieceJointe;
				
				if (file_exists($cheminPieceJointe))
				{
					$erreurFormulaire = TRUE;
					$cheminPieceJointe = '';
					$nomPieceJointe = '';
					$messagesScript .= '<li class="erreur">' . T_("Erreur lors de l'ajout de la pièce jointe.") . "</li>\n";
				}
				else
				{
					$typeMimePieceJointe = typeMime($_FILES['pieceJointe']['tmp_name']);
					
					if (!typeMimePermis($typeMimePieceJointe, $commentairesFiltreTypesMimePieceJointe, $commentairesTypesMimePermisPieceJointe))
					{
						$erreurFormulaire = TRUE;
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type de la pièce jointe %1\$s n'est pas permis."), '<code>' . securiseTexte(superBasename($_FILES['pieceJointe']['name'])) . '</code>') . "</li>\n";
					}
					elseif (!@move_uploaded_file($_FILES['pieceJointe']['tmp_name'], $cheminPieceJointe))
					{
						$erreurFormulaire = TRUE;
						$messagesScript .= '<li class="erreur">' . T_("Erreur lors de l'ajout de la pièce jointe.") . "</li>\n";
					}
				}
			}
			else
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= '<li class="erreur">' . T_("Erreur lors de l'ajout de la pièce jointe.") . "</li>\n";
			}
		}
	}
	
	if ($commentairesActiverCaptchaCalcul)
	{
		if (!captchaCalculValide($commentairesCaptchaCalculInverse))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez répondre correctement à la question antipourriel.") . "</li>\n";
		}
	}
	
	if ($commentairesActiverLimiteNombreLiens)
	{
		if (substr_count($message, 'http') > $commentairesNombreLiensMax)
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Votre commentaire a une forme qui le fait malheureusement classer comme du pourriel en raison de ses liens trop nombreux. Veuillez le modifier.") . "</li>\n";
		}
	}
	
	// Traitement personnalisé optionnel 1 de 4.
	if (file_exists($racine . '/site/inc/commentaire.inc.php'))
	{
		include $racine . '/site/inc/commentaire.inc.php';
	}
	
	if (!$erreurFormulaire)
	{
		$formulaireValide = TRUE;
	}
	
	// Traitement du commentaire valide.
	if ($formulaireValide)
	{
		// Enregistrement du commentaire.
		
		$cheminConfigCommentaires = cheminConfigCommentaires($racine, $urlRacine, $url, TRUE);
		$cheminConfigAbonnementsCommentaires = cheminConfigAbonnementsCommentaires($cheminConfigCommentaires);
		
		if (!file_exists($cheminConfigCommentaires) && !@touch($cheminConfigCommentaires))
		{
			$erreurEnvoiFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Erreur: votre commentaire n'a pas pu être envoyé. Essayez un peu plus tard.") . "</li>\n";
		}
		elseif (!file_exists($cheminConfigAbonnementsCommentaires) && !@touch($cheminConfigAbonnementsCommentaires))
		{
			$erreurEnvoiFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Erreur: votre commentaire n'a pas pu être envoyé. Essayez un peu plus tard.") . "</li>\n";
		}
		
		if (file_exists($cheminConfigCommentaires) && file_exists($cheminConfigAbonnementsCommentaires))
		{
			$commentaireDejaEnregistre = formulaireDejaEnvoye($racine, $idFormulaireCommentaire);
			$idCommentaire = chaineAleatoire(16);
			$contenuConfigCommentaire = "[$idCommentaire]\n";
			$ipInternaute = ipInternaute();
			$contenuConfigCommentaire .= "ip=$ipInternaute\n";
			$date = time();
			$contenuConfigCommentaire .= "date=$date\n";
			$contenuConfigCommentaire .= "nom=$nom\n";
			$contenuConfigCommentaire .= "courriel=$courriel\n";
			$contenuConfigCommentaire .= "site=$site\n";
			$contenuConfigCommentaire .= "pieceJointe=$nomPieceJointe\n";
			$contenuConfigCommentaire .= 'notification=';
			$enregistrementConfigAbonnementsCommentaire = TRUE;
			
			if ($notification)
			{
				$contenuConfigCommentaire .= 1;
				
				if (!empty($courriel) && !$commentaireDejaEnregistre)
				{
					$listeAbonnements = super_parse_ini_file($cheminConfigAbonnementsCommentaires, TRUE);
					
					if ($listeAbonnements !== FALSE)
					{
						if (!isset($listeAbonnements[$courriel]))
						{
							$listeAbonnements[$courriel] = array ();
						}
						
						$listeAbonnements[$courriel]['nom'] = $nom;
						
						if (!isset($listeAbonnements[$courriel]['idAbonnement']))
						{
							$listeAbonnements[$courriel]['idAbonnement'] = '';
						}
						
						if (empty($listeAbonnements[$courriel]['idAbonnement']))
						{
							$idAbonnement = chaineAleatoire(16);
							$listeAbonnements[$courriel]['idAbonnement'] = $idAbonnement;
						}
						else
						{
							$idAbonnement = $listeAbonnements[$courriel]['idAbonnement'];
						}
						
						$contenuConfigAbonnementsCommentaire = '';
						
						foreach ($listeAbonnements as $courrielAbonnement => $infosAbonnement)
						{
							$contenuConfigAbonnementsCommentaire .= "[$courrielAbonnement]\n";
							$contenuConfigAbonnementsCommentaire .= 'nom=';
							
							if (!empty($infosAbonnement['nom']))
							{
								$contenuConfigAbonnementsCommentaire .= $infosAbonnement['nom'];
							}
							
							$contenuConfigAbonnementsCommentaire .= "\n";
							$contenuConfigAbonnementsCommentaire .= 'idAbonnement=';
							
							if (!empty($infosAbonnement['idAbonnement']))
							{
								$contenuConfigAbonnementsCommentaire .= $infosAbonnement['idAbonnement'];
							}
							
							$contenuConfigAbonnementsCommentaire .= "\n\n";
						}
						
						if (@file_put_contents($cheminConfigAbonnementsCommentaires, $contenuConfigAbonnementsCommentaire, LOCK_EX) === FALSE)
						{
							$enregistrementConfigAbonnementsCommentaire = FALSE;
						}
					}
					else
					{
						$enregistrementConfigAbonnementsCommentaire = FALSE;
					}
				}
			}
			else
			{
				$contenuConfigCommentaire .= 0;
			}
			
			$contenuConfigCommentaire .= "\n";
			$contenuConfigCommentaire .= 'languePage=' . eval(LANGUE) . "\n";
			$contenuConfigCommentaire .= 'enAttenteDeModeration=';
			
			if ($moderationCommentaires)
			{
				$contenuConfigCommentaire .= 1;
			}
			else
			{
				$contenuConfigCommentaire .= 0;
			}
			
			$contenuConfigCommentaire .= "\n";
			$contenuConfigCommentaire .= 'afficher=';
			
			if ($moderationCommentaires)
			{
				$contenuConfigCommentaire .= 0;
			}
			else
			{
				$contenuConfigCommentaire .= 1;
			}
			
			$contenuConfigCommentaire .= "\n";
			$messageDansConfig = messageDansConfigCommentaires($racine, $_POST['message'], $attributNofollowLiensCommentaires);
			$tableauMessageDansConfig = explode("\n", trim($messageDansConfig));
			
			foreach ($tableauMessageDansConfig as $ligneMessageDansConfig)
			{
				$contenuConfigCommentaire .= "message[]=$ligneMessageDansConfig\n";
			}
			
			$contenuConfigCommentaire .= "\n";
			
			if ($enregistrementConfigAbonnementsCommentaire && ($commentaireDejaEnregistre || @file_put_contents($cheminConfigCommentaires, $contenuConfigCommentaire, FILE_APPEND | LOCK_EX) !== FALSE))
			{
				if (!$commentaireDejaEnregistre)
				{
					majConfigFormulairesEnvoyes($racine, $idFormulaireCommentaire);
				}
				
				$commentaireEnregistre = TRUE;
				
				if ($moderationCommentaires)
				{
					$messagesScript .= '<li>' . T_("Merci. Votre commentaire est en attente d'approbation.") . "</li>\n";
				}
				elseif ($dureeCache)
				{
					$messagesScript .= '<li>' . T_("Merci. Votre commentaire a été publié. Prenez note que selon l'état du cache, il se peut qu'il n'apparaisse pas immédiatement.") . "</li>\n";
				}
				else
				{
					$messagesScript .= '<li>' . T_("Merci. Votre commentaire a été publié.") . "</li>\n";
				}
				
				// Notifications.
				
				$listeDestinataires = array ();
				
				if ($commentairesNotification)
				{
					$listeAbonnements = super_parse_ini_file($cheminConfigAbonnementsCommentaires, TRUE);
					
					if (!empty($listeAbonnements))
					{
						foreach ($listeAbonnements as $courrielAbonnement => $infosAbonnement)
						{
							if ($courrielAbonnement != $courriel && $courrielAbonnement != $commentairesDestinataireNotification && !empty($infosAbonnement['idAbonnement']))
							{
								$listeDestinataires[$courrielAbonnement] = array ();
								$listeDestinataires[$courrielAbonnement]['idAbonnement'] = $infosAbonnement['idAbonnement'];
								
								if (isset($infosAbonnement['nom']))
								{
									$listeDestinataires[$courrielAbonnement]['nom'] = $infosAbonnement['nom'];
								}
								else
								{
									$listeDestinataires[$courrielAbonnement]['nom'] = '';
								}
							}
						}
					}
				}
				
				if (!$commentaireDejaEnregistre && (!empty($listeDestinataires) || !empty($commentairesDestinataireNotification)))
				{
					$infosCourriel = array ();
					$infosCourriel['From'] = $commentairesExpediteurNotification;
					$infosCourriel['ReplyTo'] = $infosCourriel['From'];
					$infosCourriel['format'] = 'html';
					$infosCourriel['objet'] = sprintf(T_("[Commentaire] %1\$s"), $baliseTitle);
					$auteurAffiche = auteurAfficheCommentaire($nom, $site, $attributNofollowLiensCommentaires);
					$dateAffichee = date('Y-m-d', $date);
					$heureAffichee = date('H:i', $date);
					$messageDansCourriel = '<p>' . sprintf(T_("Un nouveau commentaire a été posté sur la page %1\$s par %2\$s le %3\$s à %4\$s:"), '<a href="' . $urlSansAction . '#' . $idCommentaire . '">' . $baliseTitle . '</a>', $auteurAffiche, $dateAffichee, $heureAffichee) . "</p>\n";
					$messageDansCourriel .= $messageDansConfig;
					$messageDansCourriel .= "<hr />\n";
					$urlReponse = "$urlSansAction?action[]=commentaire";
					
					if ($commentairesChampsActifs['nom'] && !empty($nom))
					{
						$urlReponse .= '&amp;action[]=message-' . encodeTexteGet("@$nom: ");
					}
					
					// Traitement personnalisé optionnel 2 de 4.
					if (file_exists($racine . '/site/inc/commentaire.inc.php'))
					{
						include $racine . '/site/inc/commentaire.inc.php';
					}
					
					if (!empty($listeDestinataires))
					{
						$codeEnAttente = array ();
						
						foreach ($listeDestinataires as $courrielDestinataire => $infosDestinataire)
						{
							$urlReponseDestinataire = $urlReponse;
							
							if (!empty($infosDestinataire['nom']))
							{
								$infosCourriel['destinataire'] = encodeInfoEnTeteCourriel($infosDestinataire['nom']) . " <$courrielDestinataire>";
								$urlReponseDestinataire .= '&amp;action[]=nom-' . encodeTexteGet($infosDestinataire['nom']);
							}
							else
							{
								$infosCourriel['destinataire'] = $courrielDestinataire;
							}
							
							$urlReponseDestinataire .= '&amp;action[]=courriel-' . encodeTexteGet($courrielDestinataire);
							$infosCourriel['message'] = $messageDansCourriel;
							
							if (!empty($nomPieceJointe) && $commentairesLienPublicPieceJointe)
							{
								$infosCourriel['message'] .= "<ul>\n";
								$infosCourriel['message'] .= '<li>' . T_("Pièce jointe: ") . "<a href=\"$urlFichiers/commentaires/$nomPieceJointe\">$nomPieceJointe</a></li>\n";
								$infosCourriel['message'] .= "</ul>\n";
								
								$infosCourriel['message'] .= "<hr />\n";
							}
							
							$infosCourriel['message'] .= '<p>' . T_("Liste d'actions:") . "</p>\n";
							$infosCourriel['message'] .= "<ul>\n";
							
							$infosCourriel['message'] .= '<li><a href="' . $urlReponseDestinataire . '#ajoutCommentaire">' . T_("Répondre") . "</a></li>\n";
							
							if (!empty($infosDestinataire['idAbonnement']))
							{
								$infosCourriel['message'] .= '<li><a href="' . $urlRacine . '/desabonnement.php?url=' . encodeTexteGet(supprimeUrlRacine($urlRacine, $urlSansAction)) . '&amp;id=' . $infosDestinataire['idAbonnement'] . '">' . T_("Se désabonner des notifications de nouveaux commentaires") . "</a></li>\n";
							}
							
							$infosCourriel['message'] .= "</ul>\n";
							
							if ($moderationCommentaires)
							{
								$codeEnAttente[$idCommentaire][] = $infosCourriel;
							}
							else
							{
								courriel($infosCourriel);
							}
						}
						
						if (!empty($codeEnAttente))
						{
							gereNotificationsEnAttente($racine, $idCommentaire, 2, $codeEnAttente);
						}
					}
					
					if (!empty($commentairesDestinataireNotification))
					{
						$urlReponseDestinataire = $urlReponse;
						
						if (!empty($commentairesNomDestinataireNotification))
						{
							$urlReponseDestinataire .= '&amp;action[]=nom-' . encodeTexteGet($commentairesNomDestinataireNotification);
						}
						
						$urlReponseDestinataire .= '&amp;action[]=courriel-' . encodeTexteGet($commentairesDestinataireNotification);
						$infosCourriel['destinataire'] = $commentairesDestinataireNotification;
						$infosCourriel['message'] = $messageDansCourriel;
						$infosSupplementaires = array ();
						$infosSupplementaires[] = sprintf(T_("Identifiant: %1\$s"), "<code>$idCommentaire</code>");
						
						if (!empty($nomPieceJointe))
						{
							$infosSupplementaires[] = sprintf(T_("Pièce jointe: %1\$s"), "<a href=\"$urlFichiers/commentaires/$nomPieceJointe\">$nomPieceJointe</a>");
						}
						
						if (!empty($courriel))
						{
							$infosSupplementaires[] = sprintf(T_("Courriel: %1\$s"), "<a href=\"mailto:$courriel\">$courriel</a>");
						}
						
						if (!empty($ipInternaute))
						{
							$infosSupplementaires[] = sprintf(T_("IP: %1\$s"), $ipInternaute);
						}
						
						if ($commentairesNotification)
						{
							if ($notification)
							{
								$notificationAffichee = 1;
							}
							else
							{
								$notificationAffichee = 0;
							}
							
							$infosSupplementaires[] = sprintf(T_("Notification: %1\$s"), $notificationAffichee);
						}
						
						$infosCourriel['message'] .= "<ul>\n";
						
						foreach ($infosSupplementaires as $infoSupplementaire)
						{
							$infosCourriel['message'] .= "<li>$infoSupplementaire</li>\n";
						}
						
						$infosCourriel['message'] .= "</ul>\n";
						$infosCourriel['message'] .= "<hr />\n";
						
						if ($moderationCommentaires)
						{
							$infosCourriel['message'] .= '<p>' . T_("Ce commentaire est en attente de modération.") . "</p>\n";
						}
						else
						{
							$infosCourriel['message'] .= '<p>' . T_("La modération n'est pas activée. Ce commentaire a donc été publié.") . "</p>\n";
						}
						
						$infosCourriel['message'] .= '<p>' . T_("Liste d'actions:") . "</p>\n";
						$infosCourriel['message'] .= "<ul>\n";
						$pageGet = encodeTexteGet(supprimeUrlRacine($urlRacine, $urlSansAction));
						
						if ($moderationCommentaires)
						{
							$infosCourriel['message'] .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=publier&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '">' . T_("Publier") . "</a></li>\n";
							
							$infosCourriel['message'] .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=cacher&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '">' . T_("Désactiver l'affichage en ligne de ce commentaire sans le supprimer") . "</a></li>\n";
						}
						else
						{
							$infosCourriel['message'] .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=cacher&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '">' . T_("Désactiver l'affichage en ligne de ce commentaire") . "</a></li>\n";
						}
						
						$infosCourriel['message'] .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?action=supprimer&amp;id=' . $idCommentaire . '&amp;page=' . $pageGet . '">' . T_("Supprimer") . "</a></li>\n";
						
						$infosCourriel['message'] .= '<li><a href="' . $urlRacineAdmin . '/commentaires.admin.php?gererType=commentaires&amp;page=' . $pageGet . '#' . $idCommentaire . '">' . T_("Modifier") . "</a></li>\n";
						
						$infosCourriel['message'] .= '<li><a href="' . $urlReponseDestinataire . '#ajoutCommentaire">' . T_("Répondre") . "</a></li>\n";
						$infosCourriel['message'] .= "</ul>\n";
						courriel($infosCourriel);
					}
				}
				
				$nom = '';
				$courriel = '';
				$site = '';
				$message = '';
				$notification = FALSE;
				$idFormulaireCommentaire = '';
			}
			else
			{
				$erreurEnvoiFormulaire = TRUE;
				$messagesScript .= '<li class="erreur">' . T_("Erreur: votre commentaire n'a pas pu être envoyé. Essayez un peu plus tard.") . "</li>\n";
			}
		}
	}
	
	// Messages de confirmation ou d'erreur.
	if (!empty($messagesScript))
	{
		$blocMessagesScript = '';
		$classesBlocMessagesScript = '';
		
		if ($erreurFormulaire || $erreurEnvoiFormulaire)
		{
			$classesBlocMessagesScript .= ' messagesErreur';
			
			if (file_exists($cheminPieceJointe))
			{
				@unlink($cheminPieceJointe);
			}
		}
		else
		{
			$classesBlocMessagesScript .= ' messagesSucces';
		}
		
		$blocMessagesScript .= '<div id="messages" class="bloc blocAvecFond blocArrondi ' . $classesBlocMessagesScript . '">' . "\n";
		
		if ($erreurFormulaire)
		{
			$blocMessagesScript .= '<p>' . T_("Le formulaire n'a pas été rempli correctement") . ':</p>' . "\n";
		}
		
		$blocMessagesScript .= "<ul>\n";
		$blocMessagesScript .= $messagesScript;
		$blocMessagesScript .= "</ul>\n";
		$blocMessagesScript .= "</div><!-- /#messages -->\n";
		$formulaireCommentaire .= $blocMessagesScript;
	}
	
	if (isset($_FILES['pieceJointe']['tmp_name']) && file_exists($_FILES['pieceJointe']['tmp_name']))
	{
		@unlink($_FILES['pieceJointe']['tmp_name']);
	}
}

// Code du formulaire.

$actionFormCommentaire = variableGet(1, url(), 'action', 'commentaire') . '#messages';
$enctypeFormCommentaire = '';

if ($formCommentairePieceJointeActivee)
{
	$enctypeFormCommentaire = ' enctype="multipart/form-data"';
	$commentairesListeTypesMimePermisPieceJointe = ' ';
	
	foreach ($commentairesTypesMimePermisPieceJointe as $extensions => $type)
	{
		$extensions = str_replace('|', ', ', $extensions);
		$commentairesListeTypesMimePermisPieceJointe .= "$extensions, ";
	}
	
	$commentairesListeTypesMimePermisPieceJointe = substr($commentairesListeTypesMimePermisPieceJointe, 0, -2);
}

$champsTousObligatoires = TRUE;

foreach ($commentairesChampsActifs as $nomChamp => $champActif)
{
	if ($champActif && !$commentairesChampsObligatoires[$nomChamp])
	{
		$champsTousObligatoires = FALSE;
		break;
	}
}

if (empty($idFormulaireCommentaire))
{
	$idFormulaireCommentaire = chaineAleatoire(16);
}

$formulaireCommentaire .= '<h2 id="ajoutCommentaire">' . T_("Ajout d'un commentaire") . "</h2>\n";

// Traitement personnalisé optionnel 3 de 4.
if (file_exists($racine . '/site/inc/commentaire.inc.php'))
{
	include $racine . '/site/inc/commentaire.inc.php';
}

ob_start();
include cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-commentaire');
$formulaireCommentaire .= ob_get_contents();
ob_end_clean();

// Traitement personnalisé optionnel 4 de 4.
if (file_exists($racine . '/site/inc/commentaire.inc.php'))
{
	include $racine . '/site/inc/commentaire.inc.php';
}
?>
