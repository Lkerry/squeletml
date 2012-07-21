<?php
/*
Ce fichier construit et analyse le formulaire de contact. Après son inclusion, la variable `$contact` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Affectations.
$nom = '';
$courriel = '';
$message = '';
$copie = FALSE;
$courrielsPartageCourriel = '';
$messageEnvoye = FALSE;
$contact = '';

// Vérification de l'état du module de partage (par courriel).
include $racine . '/inc/partage-courriel.inc.php';

if ($partageCourrielActif)
{
	$contact .= '<div id="formulairePartageCourriel">' . "\n";
	$contact .= '<h2 id="titrePartageCourriel">' . T_("Partager par courriel") . "</h2>\n";
}

// L'envoi du message est demandé.
if (isset($_POST['envoyer']))
{
	$desactiverCache = TRUE;
	$nom = securiseTexte($_POST['nom']);
	$courriel = securiseTexte($_POST['courriel']);
	$message = securiseTexte($_POST['message']);
	$messagesScript = '';
	$erreurFormulaire = FALSE;
	$erreurEnvoiFormulaire = FALSE;
	$formulaireValide = FALSE;
	
	if (isset($_POST['copie']))
	{
		$copie = TRUE;
	}
	
	if ($partageCourrielActif)
	{
		$courrielsPartageCourriel = securiseTexte($_POST['courrielsPartageCourriel']);
	}
	
	if (empty($nom) && $contactChampsObligatoires['nom'])
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de nom.") . "</li>\n";
	}

	if ($contactVerifierCourriel)
	{
		if (!courrielValide($courriel))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.") . "</li>\n";
		}
	}
	elseif (empty($courriel))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de courriel.") . "</li>\n";
	}
	
	if ($contactVerifierCourriel && !empty($courrielsPartageCourriel))
	{
		$tableauCourrielsPartageCourriel = explode(',', str_replace(' ', '', $courrielsPartageCourriel));
		$courrielsPartageCourrielErreur = '';
		$i = 0;
		
		foreach ($tableauCourrielsPartageCourriel as $courrielPartageCourriel)
		{
			if (!courrielValide($courrielPartageCourriel))
			{
				$courrielsPartageCourrielErreur .= $courrielPartageCourriel . ', ';
				$i++;
			}
		}
		
		if (!empty($courrielsPartageCourrielErreur))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_ngettext("L'adresse suivante ne semble pas avoir une forme valide; veuillez la vérifier: %1\$s", "Les adresses suivantes ne semblent pas avoir une forme valide; veuillez les vérifier: %1\$s", $i), substr($courrielsPartageCourrielErreur, 0, -2)) . "</li>\n";
		}
	}
	
	if (empty($message) && !$partageCourrielActif && $contactChampsObligatoires['message'])
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas écrit de message.") . "</li>\n";
	}

	if ($contactActiverCaptchaCalcul)
	{
		if ($contactCaptchaCalculInverse)
		{
			$resultat = $_POST['u'];
			$sommeUnDeux = $_POST['r'] + $_POST['s'];
		}
		else
		{
			$resultat = $_POST['r'];
			$sommeUnDeux = $_POST['u'] + $_POST['d'];
		}
		
		if ($sommeUnDeux != $resultat)
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez répondre correctement à la question antipourriel.") . "</li>\n";
		}
	}

	if ($contactActiverLimiteNombreLiens)
	{
		if (substr_count($message, 'http') > $contactNombreLiensMax)
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Votre message a une forme qui le fait malheureusement classer comme du pourriel en raison de ses liens trop nombreux. Veuillez le modifier.") . "</li>\n";
		}
	}
	
	// Traitement personnalisé optionnel 1 de 4.
	if (file_exists($racine . '/site/inc/contact.inc.php'))
	{
		include $racine . '/site/inc/contact.inc.php';
	}
	
	if (!$erreurFormulaire)
	{
		$formulaireValide = TRUE;
	}
	
	// Envoi du message.
	if ($formulaireValide)
	{
		$infosCourriel = array ();
		$infosCourriel['From'] = "$nom <$courriel>";
		$infosCourriel['ReplyTo'] = $infosCourriel['From'];
		
		if ($partageCourrielActif && $contactCopieCourriel && $copie)
		{
			$infosCourriel['destinataire'] = $infosCourriel['From'];
			$infosCourriel['Bcc'] = $courrielsPartageCourriel;
		}
		elseif ($partageCourrielActif)
		{
			$infosCourriel['destinataire'] = $courrielsPartageCourriel;
		}
		elseif (!$partageCourrielActif && $contactCopieCourriel && $copie)
		{
			$infosCourriel['destinataire'] = $infosCourriel['From'];
			$infosCourriel['Bcc'] = $courrielContact;
		}
		else
		{
			$infosCourriel['destinataire'] = $courrielContact;
		}
		
		if ($partageCourrielActif)
		{
			$infosCourriel['format'] = 'html';
		}
		else
		{
			$infosCourriel['format'] = 'texte';
		}
		
		$infosCourriel['objet'] = $contactCourrielIdentifiantObjet . "Message de $nom <$courriel>";
		
		if ($partageCourrielActif)
		{
			$infosCourriel['message'] = str_replace(array ("\r\n", "\r"), "\n", $messagePartageCourriel) . "\n";
		}
		else
		{
			$infosCourriel['message'] = str_replace(array ("\r\n", "\r"), "\n", $message) . "\n";
		}
		
		// Traitement personnalisé optionnel 2 de 4.
		if (file_exists($racine . '/site/inc/contact.inc.php'))
		{
			include $racine . '/site/inc/contact.inc.php';
		}
		
		if (courriel($infosCourriel))
		{
			$messageEnvoye = TRUE;
			$messagesScript .= '<li>' . T_("Votre message a bien été envoyé.") . "</li>\n";
			$nom = '';
			$courriel = '';
			$message = '';
			$copie = FALSE;
			$courrielsPartageCourriel = '';
		}
		else
		{
			$erreurEnvoiFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Erreur: votre message n'a pas pu être envoyé. Essayez un peu plus tard.") . "</li>\n";
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
		$contact .= $blocMessagesScript;
	}
}

// Code du formulaire.

include $racine . '/inc/partage-courriel.inc.php';
$actionFormContact = actionFormContact($partageCourrielActif);

if ($nom == T_("VOTRE NOM"))
{
	$nom = '';
}

// Traitement personnalisé optionnel 3 de 4.
if (file_exists($racine . '/site/inc/contact.inc.php'))
{
	include $racine . '/site/inc/contact.inc.php';
}

ob_start();
include cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact');
$contact .= ob_get_contents();
ob_end_clean();

if ($partageCourrielActif)
{
	$contact .= '</div><!-- /#formulairePartageCourriel -->' . "\n";
}

// Traitement personnalisé optionnel 4 de 4.
if (file_exists($racine . '/site/inc/contact.inc.php'))
{
	include $racine . '/site/inc/contact.inc.php';
}
?>
