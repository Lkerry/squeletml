<?php
/**
Ce fichier construit et analyse le formulaire de contact. Après son inclusion, la variable `$contact` est prête à être utilisée. Aucun code XHTML n'est envoyé au navigateur.
*/

// Nécessaire à la traduction.
phpGettext($racine, LANGUE);

// Initialisations.
$nom = '';
$courriel = '';
$message = '';
$copie = '';
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
	$erreur = FALSE;
	
	if (isset($_POST['copie']))
	{
		$copie = $_POST['copie'];
	}
	
	if ($decouvrir)
	{
		$courrielsDecouvrir = securiseTexte($_POST['courrielsDecouvrir']);
	}
	
	if (empty($nom))
	{
		$messagesScript .= '<li>' . T_("Vous n'avez pas inscrit de nom.") . "</li>\n";
	}

	if ($contactVerifierCourriel)
	{
		$motifCourriel = "/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i";

		if (!preg_match($motifCourriel, $courriel))
		{
			$messagesScript .= '<li>' . T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.") . "</li>\n";
		}
	}
	
	if ($contactVerifierCourriel && isset($courrielsDecouvrir) && !empty($courrielsDecouvrir))
	{
		$tableauCourrielsDecouvrir = explode(',', str_replace(' ', '', $courrielsDecouvrir));
		$courrielsDecouvrirErreur = '';
		$i = 0;
		
		foreach ($tableauCourrielsDecouvrir as $courrielDecouvrir)
		{
			if (!preg_match($motifCourriel, $courrielDecouvrir))
			{
				$courrielsDecouvrirErreur .= $courrielDecouvrir . ', ';
				$i++;
			}
		}
		
		if (!empty($courrielsDecouvrirErreur))
		{
			$messagesScript .= '<li>' . sprintf(T_ngettext("L'adresse suivante ne semble pas avoir une forme valide; veuillez la vérifier: %1\$s", "Les adresses suivantes ne semblent pas avoir une forme valide; veuillez les vérifier: %1\$s", $i), substr($courrielsDecouvrirErreur, 0, -2)) . "</li>\n";
		}
	}
	
	if (empty($message) && !$decouvrir)
	{
		$messagesScript .= '<li>' . T_("Vous n'avez pas écrit de message.") . "</li>\n";
	}

	if ($contactActiverCaptchaCalcul)
	{
		$ab = securiseTexte($_POST['ab']);
		$abSomme = $_POST['a'] + $_POST['d'];
		
		if ($abSomme != $ab)
		{
			$messagesScript .= '<li>' . T_("Veuillez répondre correctement à la question antipourriel.") . "</li>\n";
		}
	}

	if ($contactActiverCaptchaLiens)
	{
		if (substr_count($message, 'http') > $contactCaptchaLiensNombre)
		{
			$messagesScript .= '<li>' . T_("Votre message a une forme qui le fait malheureusement classer comme du pourriel à cause de ses liens trop nombreux. Veuillez le modifier.") . "</li>\n";
		}
	}
	
	// Traitement personnalisé optionnel.
	if (file_exists($racine . '/site/inc/contact.inc.php'))
	{
		include_once $racine . '/site/inc/contact.inc.php';
	}
	
	// Envoi du message.
	if (empty($messagesScript))
	{
		// Adresses.
		$adresseFrom = $courriel;
		$adresseReplyTo = $courriel;
		$adresseBcc = '';
		
		if ($decouvrir && $contactCopieCourriel && $copie == 'copie')
		{
			$adresseTo = $adresseFrom;
			$adresseBcc = $courrielsDecouvrir;
		}
		elseif ($decouvrir)
		{
			$adresseTo = $courrielsDecouvrir;
		}
		elseif (!$decouvrir && $contactCopieCourriel && $copie == 'copie')
		{
			$adresseTo = $adresseFrom;
			$adresseBcc = $courrielContact;
		}
		elseif (!$decouvrir)
		{
			$adresseTo = $courrielContact;
		}
		
		$enTete = '';
		$enTete .= "From: $nom <$adresseFrom>\n";
		$enTete .= "Reply-to: $adresseReplyTo\n";

		if (!empty($adresseBcc))
		{
			$enTete .= "Bcc: $adresseBcc\n";
		}
		
		$enTete .= "MIME-Version: 1.0\n";
		
		if ($decouvrir)
		{
			$enTete .= "Content-Type: text/html; charset=\"utf-8\"\n";
		}
		else
		{
			$enTete .= "Content-Type: text/plain; charset=\"utf-8\"\n";
		}
		
		$enTete .= "X-Mailer: Squeletml\n";
		
		if ($decouvrir)
		{
			$corps = str_replace("\r", '', $messageDecouvrir) . "\n";
		}
		else
		{
			$corps = str_replace("\r", '', $message) . "\n";
		}
		
		// Traitement personnalisé optionnel.
		if (file_exists($racine . '/site/inc/contact.inc.php'))
		{
			include_once $racine . '/site/inc/contact.inc.php';
		}
		
		if (mail($adresseTo, $contactCourrielIdentifiantObjet . "Message de " . "$nom <$adresseFrom>", $corps, $enTete))
		{
			$messageEnvoye = TRUE;
			$messagesScript .= '<p class="succes">' . T_("Votre message a bien été envoyé.") . "</p>\n";
			$nom = '';
			$courriel = '';
			$message = '';
			$copie = '';
			$courrielsDecouvrir = '';
		}
		else
		{
			$messagesScript .= '<p class="erreur">' . T_("ERREUR: votre message n'a pas pu être envoyé. Essayez un peu plus tard.") . "</p>\n";
		}
	}

	// Messages de confirmation ou d'erreur.
	if (!empty($messagesScript))
	{
		$contact .= '<div class="erreur">' . "\n";
		$contact .= '<p>' . T_("Le formulaire n'a pas été rempli correctement") . ':</p>' . "\n";
		$contact .= "<ul>\n";
		$contact .= $messagesScript;
		$contact .= "</ul>\n";
		$contact .= "</div><!-- /.erreur -->\n";
	}
}

// Code du formulaire.

include $racine . '/inc/faire-decouvrir.inc.php';
$actionFormContact = actionFormContact($decouvrir);

if ($nom == T_('Votre nom'))
{
	$nom = '';
}

if ($contactActiverCaptchaCalcul)
{
	$contactActiverCaptchaCalcul1 = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax);
	$contactActiverCaptchaCalcul2 = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax);
	$contactActiverCaptchaCalculBidon = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax);
	$inputHidden = '';
	$inputHidden .= '<input name="a" type="hidden" value="' . $contactActiverCaptchaCalcul1 . '" />' . "\n";
	$inputHidden .= '<input name="b" type="hidden" value="' . $contactActiverCaptchaCalculBidon . '" />' . "\n";
	
	// Ajout de quelques `input` bidons pour augmenter les chances de duper les robots pourrielleurs.
	$nbreInput = rand(5, 10);
	$toutesLesLettres = 'abd';
	
	for ($i = 0; $i < $nbreInput; $i++)
	{
		$lettreAuHasard = lettreAuHasard($toutesLesLettres);
		$toutesLesLettres .= $lettreAuHasard;
		$inputHidden .= '<input name="' . $lettreAuHasard . '" type="hidden" value="' . rand($contactCaptchaCalculMin, $contactCaptchaCalculMax) . '" />' . "\n";
	}
	
	$inputHidden .= '<input name="d" type="hidden" value="' . $contactActiverCaptchaCalcul2 . '" />' . "\n";
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/contact.inc.php'))
{
	include_once $racine . '/site/inc/contact.inc.php';
}

ob_start();
include_once cheminXhtml($racine, 'form-contact');
$contact .= ob_get_contents();
ob_end_clean();
?>
