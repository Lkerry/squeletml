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
$idFormulaireContact = '';
$messageEnvoye = FALSE;
$contact = '';

// Vérification de l'état du module de partage (par courriel).
if ($partageCourriel)
{
	include $racine . '/inc/partage-courriel.inc.php';
}

if ($partageCourrielActif)
{
	$contact .= '<div id="formulairePartageCourriel">' . "\n";
	$contact .= '<h2 id="titrePartageCourriel">' . T_("Partager par courriel") . "</h2>\n";
	$formContactPieceJointeActivee = FALSE;
}

if ($formContactPieceJointeActivee)
{
	$contactTailleMaxPieceJointe = min($contactTailleMaxPieceJointe, phpIniOctets(ini_get('post_max_size')), phpIniOctets(ini_get('upload_max_filesize')));
}

// L'envoi du message est demandé.
if (isset($_POST['envoyerContact']) || ($formContactPieceJointeActivee && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > phpIniOctets(ini_get('post_max_size'))))
{
	$nom = securiseTexte(trim($_POST['nom']));
	$courriel = securiseTexte(trim($_POST['courriel']));
	$message = securiseTexte(trim($_POST['message']));
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
		$courrielsPartageCourriel = securiseTexte(trim($_POST['courrielsPartageCourriel']));
	}
	
	if (!empty($_POST['idFormulaire']))
	{
		$idFormulaireContact = securiseTexte($_POST['idFormulaire']);
	}
	
	if (empty($nom))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de nom.") . "</li>\n";
	}
	
	if (empty($courriel))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas inscrit de courriel.") . "</li>\n";
	}
	elseif (!courrielValide($courriel))
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.") . "</li>\n";
	}
	
	if (!empty($courrielsPartageCourriel))
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
	
	if (empty($message) && !$partageCourrielActif)
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Vous n'avez pas écrit de message.") . "</li>\n";
	}
	
	$nomPieceJointe = '';
	
	if ($formContactPieceJointeActivee)
	{
		if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > phpIniOctets(ini_get('post_max_size')))
		{
			// Explications: À la page <http://www.php.net/manual/fr/ini.core.php#ini.post-max-size>, on peut lire: «Dans le cas où la taille des données reçues par la méthode POST est plus grande que post_max_size , les superglobales  $_POST et $_FILES  seront vides». Je repère donc une erreur potentielle par le test ci-dessus.
			
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La pièce jointe doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($contactTailleMaxPieceJointe), $contactTailleMaxPieceJointe) . "</li>\n";
		}
		elseif (!empty($_FILES['pieceJointe']['name']))
		{
			if (file_exists($_FILES['pieceJointe']['tmp_name']) && @filesize($_FILES['pieceJointe']['tmp_name']) > $contactTailleMaxPieceJointe)
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= '<li class="erreur">' . sprintf(T_("La pièce jointe doit faire moins de %1\$s Mio (%2\$s octets)."), octetsVersMio($contactTailleMaxPieceJointe), $contactTailleMaxPieceJointe) . "</li>\n";
			}
			elseif ($_FILES['pieceJointe']['error'])
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= '<li class="erreur">' . T_("Erreur lors de l'ajout de la pièce jointe.") . "</li>\n";
			}
			else
			{
				$typeMimePieceJointe = typeMime($_FILES['pieceJointe']['tmp_name']);
				
				if (!typeMimePermis($typeMimePieceJointe, $contactFiltreTypesMimePieceJointe, $contactTypesMimePermisPieceJointe))
				{
					$erreurFormulaire = TRUE;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type de la pièce jointe %1\$s n'est pas permis."), '<code>' . securiseTexte(superBasename($_FILES['pieceJointe']['name'])) . '</code>') . "</li>\n";
				}
			}
		}
	}
	
	if ($contactActiverCaptchaCalcul)
	{
		if (!captchaCalculValide($commentairesCaptchaCalculInverse))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez répondre correctement à la question antipourriel.") . "</li>\n";
		}
	}

	if ($contactActiverLimiteNombreLiens && substr_count($message, 'http') > $contactNombreLiensMax)
	{
		$erreurFormulaire = TRUE;
		$messagesScript .= '<li class="erreur">' . T_("Votre message a une forme qui le fait malheureusement classer comme du pourriel en raison de ses liens trop nombreux. Veuillez le modifier.") . "</li>\n";
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
		$infosCourriel['From'] = encodeInfoEnTeteCourriel($nom) . " <$courriel>";
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
			$infosCourriel['message'] = $messagePartageCourriel;
		}
		else
		{
			$infosCourriel['message'] = $message;
		}
		
		if (!empty($_FILES['pieceJointe']['name']) && file_exists($_FILES['pieceJointe']['tmp_name']))
		{
			$infosCourriel['pieceJointe'] = $_FILES['pieceJointe'];
		}
		
		$formulaireContactDejaEnvoye = formulaireDejaEnvoye($racine, $idFormulaireContact);
		
		// Traitement personnalisé optionnel 2 de 4.
		if (file_exists($racine . '/site/inc/contact.inc.php'))
		{
			include $racine . '/site/inc/contact.inc.php';
		}
		
		if ($formulaireContactDejaEnvoye || courriel($infosCourriel))
		{
			$messageEnvoye = TRUE;
			$messagesScript .= '<li>' . T_("Votre message a bien été envoyé.") . "</li>\n";
			$nom = '';
			$courriel = '';
			$message = '';
			$copie = FALSE;
			$courrielsPartageCourriel = '';
			
			if (!$formulaireContactDejaEnvoye)
			{
				majConfigFormulairesEnvoyes($racine, $idFormulaireContact);
			}
			
			$idFormulaireContact = '';
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
	
	if (isset($_FILES['pieceJointe']['tmp_name']) && file_exists($_FILES['pieceJointe']['tmp_name']))
	{
		@unlink($_FILES['pieceJointe']['tmp_name']);
	}
}

// Code du formulaire.

if ($partageCourriel)
{
	include $racine . '/inc/partage-courriel.inc.php';
}

$actionFormContact = actionFormContact($partageCourrielActif);
$enctypeFormContact = '';

if ($formContactPieceJointeActivee)
{
	$enctypeFormContact = ' enctype="multipart/form-data"';
	$contactListeTypesMimePermisPieceJointe = ' ';
	
	foreach ($contactTypesMimePermisPieceJointe as $extensions => $type)
	{
		$extensions = str_replace('|', ', ', $extensions);
		$contactListeTypesMimePermisPieceJointe .= "$extensions, ";
	}
	
	$contactListeTypesMimePermisPieceJointe = substr($contactListeTypesMimePermisPieceJointe, 0, -2);
}

if ($nom == T_("VOTRE NOM"))
{
	$nom = '';
}

if (empty($idFormulaireContact))
{
	$idFormulaireContact = chaineAleatoire(16);
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
