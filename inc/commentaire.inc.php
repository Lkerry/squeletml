<?php
/*
Ce fichier construit et analyse le formulaire d'ajout d'un commentaire. Après son inclusion, la variable `$formulaireCommentaire` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Affectations.
$nom = '';
$courriel = '';
$site = '';
$message = '';
$notification = FALSE;
$idFormulaireCommentaire = '';
$commentaireEnregistre = FALSE;
$formulaireCommentaire = '';

// L'envoi du commentaire est demandé.
if (isset($_POST['envoyerCommentaire']))
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
	
	if (isset($_POST['notification']))
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
			$contenuConfigCommentaire .= "languePage=$langue\n";
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
					$heureAffichee = date('H:i T', $date);
					$messageDansCourriel = '<p>' . sprintf(T_("Un nouveau commentaire a été posté sur la page %1\$s par %2\$s le %3\$s à %4\$s:"), '<a href="' . $urlSansAction . '#' . $idCommentaire . '">' . $baliseTitle . '</a>', $auteurAffiche, $dateAffichee, $heureAffichee) . "</p>\n";
					$messageDansCourriel .= $messageDansConfig;
					$messageDansCourriel .= "<hr />\n";
					$infosCourriel['message'] = $messageDansCourriel;
					
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
							if (!empty($infosDestinataire['nom']))
							{
								$infosCourriel['destinataire'] = encodeInfoEnTeteCourriel($infosDestinataire['nom']) . " <$courrielDestinataire>";
							}
							else
							{
								$infosCourriel['destinataire'] = $courrielDestinataire;
							}
							
							$infosCourriel['message'] = $messageDansCourriel;
							
							if (!empty($infosDestinataire['idAbonnement']))
							{
								$infosCourriel['message'] .= '<p><a href="' . $urlRacine . '/desabonnement.php?url=' . encodeTexteGet(supprimeUrlRacine($urlRacine, $urlSansAction)) . '&amp;id=' . $infosDestinataire['idAbonnement'] . '">' . T_("Se désabonner des notifications de nouveaux commentaires.") . "</a></p>\n";
							}
							
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
						$infosCourriel['destinataire'] = $commentairesDestinataireNotification;
						$infosCourriel['message'] = $messageDansCourriel;
						$infosSupplementaires = array ();
						$infosSupplementaires[] = sprintf(T_("Identifiant: %1\$s"), "<code>$idCommentaire</code>");
						
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
}

// Code du formulaire.

$actionFormCommentaire = url() . '#messages';
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