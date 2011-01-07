<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAvantConfig());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

if ($activerCreationCompte && (!empty($courrielAdmin) || !empty($contactCourrielParDefaut)))
{
	$envoyerAmis = FALSE;
	$infosPublication = FALSE;
	$licence = '';
	$lienPage = FALSE;
	$partage = FALSE;
	$robots = 'noindex, nofollow, noarchive';
	include $racine . '/inc/premier.inc.php';
	
	$erreurFormulaire = FALSE;
	$messagesScript = '';
	
	if (isset($_POST['demander']))
	{
		if (empty($_POST['identifiant']))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Aucun identifiant spécifié.") . "</li>\n";
		}
		
		if (!courrielValide($_POST['courriel']))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.") . "</li>\n";
		}

		if (strlen($_POST['motDePasse']) < 8 || !preg_match('/\d+/', $_POST['motDePasse']) || !preg_match('/[A-Za-z]+/', $_POST['motDePasse']))
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Pour une question de sécurité, le mot de passe doit contenir au moins huit caractères ainsi qu'au moins un chiffre et une lettre.") . "</li>\n";
		}
		elseif ($_POST['motDePasse'] != $_POST['motDePasse2'])
		{
			$erreurFormulaire = TRUE;
			$messagesScript .= '<li class="erreur">' . T_("Veuillez confirmer correctement le mot de passe.") . "</li>\n";
		}
		
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
		
		if (!$erreurFormulaire)
		{
			$infosCourriel = array ();
			
			if (!empty($courrielExpediteurRapports))
			{
				$infosCourriel['From'] = $courrielExpediteurRapports;
			}

			$infos['ReplyTo'] = securiseTexte($_POST['courriel']);
			$infosCourriel['destinataire'] = !empty($courrielAdmin) ? $courrielAdmin : $contactCourrielParDefaut;
			$infosCourriel['objet'] = T_("Demande de création d'un compte utilisateur") . baliseTitleComplement($tableauBaliseTitleComplement, array ($langue, $langueParDefaut), FALSE);
			$infosCourriel['message'] = '';
			$infosCourriel['message'] .= sprintf(T_("Identifiant et mot de passe à ajouter dans le fichier %1\$s:"), '`.acces`') . "\n";
			
			if (stristr(PHP_OS, 'win') || $serveurFreeFr)
			{
				$ligneAcces = securiseTexte($_POST['identifiant']) . ':' . securiseTexte($_POST['motDePasse']);
			}
			else
			{
				$ligneAcces = securiseTexte($_POST['identifiant']) . ':' . chiffreMotDePasse($_POST['motDePasse']);
			}
			
			$infosCourriel['message'] .= "$ligneAcces\n\n";
			$infosCourriel['message'] .= T_("Courriel:") . "\n" . securiseTexte($_POST['courriel']) . "\n\n";
			$infosCourriel['message'] .= T_("Adresse IP:") . "\n" . ipInternaute() . "\n\n";
			$infosCourriel['message'] .= T_("Administration de l'accès:") . "\n$urlRacineAdmin/acces.admin.php\n\n";
			$infosCourriel['message'] .= sprintf(T_("Modification du fichier %1\$s:"), '`.acces`') . "\n$urlRacineAdmin/porte-documents.admin.php?action=editer&valeur=../.acces&dossierCourant=..#messages\n\n";
			
			if (courriel($infosCourriel))
			{
				$messagesScript .= '<li>' . T_("La demande de création d'un compte utilisateur a été envoyée.") . "</li>\n";
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . T_("Erreur: votre demande n'a pas pu être envoyée. Essayez un peu plus tard.") . "</li>\n";
			}
		}
	}
	
	if (!empty($messagesScript))
	{
		$blocMessagesScript = '';
		$blocMessagesScript .= '<div class="blocMessagesScript">' . "\n";
		
		$blocMessagesScript .= "<ul>\n";
		$blocMessagesScript .= $messagesScript;
		$blocMessagesScript .= "</ul>\n";
		$blocMessagesScript .= "</div><!-- /.blocMessagesScript -->\n";
		echo boiteArrondie($blocMessagesScript);
	}
	
	if (!isset($_POST['demander']) || (isset($_POST['demander']) && $erreurFormulaire))
	{
		echo '<form action="' . $url . '" method="post">' . "\n";
		echo "<div>\n";
		echo '<p><label for="inputIdentifiant">' . T_("Identifiant:") . "</label><br />\n" . '<input id="inputIdentifiant" type="text" name="identifiant" ';

		if (!empty($_POST['identifiant']))
		{
			echo 'value="' . securiseTexte($_POST['identifiant']) . '" ';
		}

		echo '/></p>' . "\n";

		echo '<p><label for="inputCourriel">' . T_("Courriel:") . "</label><br />\n" . '<input id="inputCourriel" type="text" name="courriel" ';

		if (!empty($_POST['courriel']))
		{
			echo 'value="' . securiseTexte($_POST['courriel']) . '" ';
		}
		
		echo '/></p>' . "\n";
		
		echo '<p><label for="inputMotDePasse">' . T_("Mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse" type="password" name="motDePasse" /></p>' . "\n";
		
		echo '<p><label for="inputMotDePasse2">' . T_("Confirmer le mot de passe:") . "</label><br />\n" . '<input id="inputMotDePasse2" type="password" name="motDePasse2" /></p>' . "\n";
		
		echo captchaCalcul($contactCaptchaCalculMin, $contactCaptchaCalculMax, $contactCaptchaCalculInverse);
		
		echo '<p><input type="submit" name="demander" value="' . T_("Demander la création d'un compte") . '" /></p>' . "\n";
		echo "</div>\n";
		echo "</form>\n";
	}
	
	include $racine . '/inc/dernier.inc.php';
}
else
{
	header('Location: ' . urlParente() . '/', TRUE, 301);
}
?>
