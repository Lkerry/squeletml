<?php
/*
Ce fichier construit et analyse le formulaire de contact. Après son inclusion, la variable `$contact` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Affectations.
$nom = '';
$courriel = '';
$message = '';
$copie = FALSE;
$courrielsDecouvrir = '';
$messageEnvoye = FALSE;
$contact = '';

// Vérification de l'état du module «Faire découvrir».
include $racine . '/inc/faire-decouvrir.inc.php';

if ($decouvrir)
{
	$contact .= '<h2 id="formulaireFaireDecouvrir">' . T_("Faire découvrir à des ami-e-s") . "</h2>\n";
}

// L'envoi du message est demandé.
if (isset($_POST['envoyer']))
{
	$nom = securiseTexte($_POST['nom']);
	$courriel = securiseTexte($_POST['courriel']);
	$message = securiseTexte($_POST['message']);
	$messagesScript = '';
	$erreurFormulaire = FALSE;
	$formulaireValide = FALSE;
	
	if (isset($_POST['copie']))
	{
		$copie = TRUE;
	}
	
	if ($decouvrir)
	{
		$courrielsDecouvrir = securiseTexte($_POST['courrielsDecouvrir']);
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
	
	if ($contactVerifierCourriel && !empty($courrielsDecouvrir))
	{
		$tableauCourrielsDecouvrir = explode(',', str_replace(' ', '', $courrielsDecouvrir));
		$courrielsDecouvrirErreur = '';
		$i = 0;
		
		foreach ($tableauCourrielsDecouvrir as $courrielDecouvrir)
		{
			if (!courrielValide($courrielDecouvrir))
			{
				$courrielsDecouvrirErreur .= $courrielDecouvrir . ', ';
				$i++;
			}
		}
		
		if (!empty($courrielsDecouvrirErreur))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_ngettext("L'adresse suivante ne semble pas avoir une forme valide; veuillez la vérifier: %1\$s", "Les adresses suivantes ne semblent pas avoir une forme valide; veuillez les vérifier: %1\$s", $i), substr($courrielsDecouvrirErreur, 0, -2)) . "</li>\n";
		}
	}
	
	if (empty($message) && !$decouvrir && $contactChampsObligatoires['message'])
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
		
		if ($decouvrir && $contactCopieCourriel && $copie)
		{
			$infosCourriel['destinataire'] = $infosCourriel['From'];
			$infosCourriel['Bcc'] = $courrielsDecouvrir;
		}
		elseif ($decouvrir)
		{
			$infosCourriel['destinataire'] = $courrielsDecouvrir;
		}
		elseif (!$decouvrir && $contactCopieCourriel && $copie)
		{
			$infosCourriel['destinataire'] = $infosCourriel['From'];
			$infosCourriel['Bcc'] = $courrielContact;
		}
		else
		{
			$infosCourriel['destinataire'] = $courrielContact;
		}
		
		if ($decouvrir)
		{
			$infosCourriel['format'] = 'html';
		}
		else
		{
			$infosCourriel['format'] = 'texte';
		}
		
		$infosCourriel['objet'] = $contactCourrielIdentifiantObjet . "Message de $nom <$courriel>";
		
		if ($decouvrir)
		{
			$infosCourriel['message'] = str_replace(array("\r\n", "\r"), "\n", $messageDecouvrir) . "\n";
		}
		else
		{
			$infosCourriel['message'] = str_replace(array("\r\n", "\r"), "\n", $message) . "\n";
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
			$courrielsDecouvrir = '';
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . T_("Erreur: votre message n'a pas pu être envoyé. Essayez un peu plus tard.") . "</li>\n";
		}
	}

	// Messages de confirmation ou d'erreur.
	if (!empty($messagesScript))
	{
		$blocMessagesScript = '';
		$blocMessagesScript .= '<div id="messages" class="blocMessagesScript">' . "\n";
		
		if ($erreurFormulaire)
		{
			$blocMessagesScript .= '<p>' . T_("Le formulaire n'a pas été rempli correctement") . ':</p>' . "\n";
		}
		
		$blocMessagesScript .= "<ul>\n";
		$blocMessagesScript .= $messagesScript;
		$blocMessagesScript .= "</ul>\n";
		$blocMessagesScript .= "</div><!-- /#messages -->\n";
		$contact .= boiteArrondie($blocMessagesScript);
	}
}

// Code du formulaire.

include $racine . '/inc/faire-decouvrir.inc.php';
$actionFormContact = actionFormContact($decouvrir);

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
include_once cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact');
$contact .= ob_get_contents();
ob_end_clean();

// Traitement personnalisé optionnel 4 de 4.
if (file_exists($racine . '/site/inc/contact.inc.php'))
{
	include $racine . '/site/inc/contact.inc.php';
}
?>
