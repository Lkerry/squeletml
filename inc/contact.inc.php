<?php
$nom = '';
$courriel = '';
$message = '';
$copie = '';

// L'envoi du message est demandé
if (isset($_POST['envoyer']))
{
	$nom = securiseTexte($_POST['nom']);
	$courriel = securiseTexte($_POST['courriel']);
	$message = securiseTexte($_POST['message']);
	$copie = securiseTexte($_POST['copie']);
	
	$msg = array ();

	if (empty($nom))
	{
		$msg['erreur'][] = "Vous n'avez pas inscrit de nom.";
	}

	if ($verifCourriel)
	{
		$motifCourriel = "/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i";

		if (!preg_match($motifCourriel, $courriel))
		{
			$msg['erreur'][] = "L'adresse courriel que vous avez saisi ne semble pas avoir une forme valide. Veuillez vérifier.";
		}
	}

	if (empty($message))
	{
		$msg['erreur'][] = "Vous n'avez pas écrit de message.";
	}

	if ($captchaCalcul)
	{
		$ab = securiseTexte($_POST['ab']);
		$abSomme = $_POST['a'] + $_POST['d'];
		if ($abSomme != $ab)
		{
			$msg['erreur'][] = "Veuillez répondre correctement à la question antipourriel.";
		}
	}

	if ($captchaLiens)
	{
		if (substr_count($message, 'http') > $captchaLiensNbre)
		{
			$msg['erreur'][] = "Votre message a une forme qui le fait malheureusement classer comme du pourriel. Veuillez le modifier.";
		}
	}

	if (empty($msg))
	{
		// Envoi du message
		$entete = "From: $nom <$courriel>\n";
		$entete .= "Reply-to: $courriel\n";

		// Si l'internaute veut une copie, on met le courriel du destinataire en copie invisible pour ne pas rendre cette adresse visible dans l'en-tête du message
		if ($copieCourriel)
		{
			$entete .= "Bcc: $courrielContact\n";
		}

		$entete .= "MIME-Version: 1.0\n";
		$entete .= "Content-Type: text/plain; charset=\"utf-8\"\n";
		$corps = "Message de $nom <$courriel>\n\n" . str_replace("\r", '', $message) . "\n";

		// Si l'internaute veut une copie, on met son adresse comme destinataire, le courriel contact se trouvant déjà en Bcc
		if ($copieCourriel)
		{
			$courrielContact = $courriel;
		}

		if (mail($courrielContact, $courrielObjetId . "Message de $courriel", $corps, $entete))
		{
			$msg['envoi'][1] = "Votre message a bien été envoyé.";
			unset($nom);
			unset($courriel);
			unset($message);
			unset($copieCourriel);
		}
		else
		{
			$msg['envoi'][0] = "ERREUR: votre message n'a pas pu être envoyé. Essayez un peu plus tard.";
		}
	}

	// Affichage des messages de confirmation ou d'erreur
	if (!empty($msg['envoi']))
	{
		if (!empty($msg['envoi'][1]))
		{
			echo '<p class="succes">' . $msg['envoi'][1] . '</p>';
		}
		elseif (!empty($msg['envoi'][0]))
		{
			echo '<p class="erreur">' . $msg['envoi'][0] . '</p>';
		}
	}
	elseif (!empty($msg['erreur']))
	{
		echo '<div class="erreur">';
		echo "<p>Le formulaire n'a pas été rempli correctement:</p>";
		echo '<ul>';
		foreach ($msg['erreur'] as $erreur)
		{
			echo "<li>$erreur</li>";
		}
		echo '</ul>';
		echo '</div>';
	}
}
?>

<!-- Affichage du formulaire -->
<form id="formContact" method="post" action="<?echo $_SERVER['PHP_SELF']; ?>">
<div id="divContact">

<p><label>Votre nom:</label><br />
<input class="champInfo" name="nom" type="text" size="30" maxlength="120" value="<?php echo $nom; ?>" /></p>

<p><label>Votre courriel:</label><br />
<input class="champInfo" name="courriel" type="text" size="30" maxlength="120" value="<?php echo $courriel; ?>" /></p>

<p><label>Votre message:</label><br />
<textarea name="message" cols="30" rows="10" id="message"><?php echo $message; ?></textarea></p>

<?php if ($captchaCalcul): ?>
	<?php $captchaCalcul1 = rand($captchaCalculMin, $captchaCalculMax); ?>
	<?php $captchaCalcul2 = rand($captchaCalculMin, $captchaCalculMax); ?>
	<?php $captchaCalculBidon = rand($captchaCalculMin, $captchaCalculMax); ?>
	<p><label>Antipourriel:</label><br />
	<?php echo "Compléter: $captchaCalcul1 ajouté à $captchaCalcul2"; ?> vaut <input name="ab" type="text" size="2" maxlength="2" /></p>
	<input name="a" type="hidden" value="<?php echo $captchaCalcul1; ?>" />
	<input name="b" type="hidden" value="<?php echo $captchaCalculBidon; ?>" />
	<?php
	// Ajout de input bidons dans le but de potentiellement mélanger les robots pourrielleurs
	$nbreInput = rand(5, 10);
	$toutesLesLettres = 'abd';
	for ($i = 0; $i < $nbreInput; $i++)
	{
		$tab = "\t";
		if ($i == 0)
		{
			$tab = '';
		}
		$lettreAuHasard = lettreAuHasard($toutesLesLettres);
		$toutesLesLettres .= $lettreAuHasard;
		echo $tab . '<input name="' . $lettreAuHasard . '" type="hidden" value="' . rand($captchaCalculMin, $captchaCalculMax) . '" />' . "\n";
	}
	?>
	<input name="d" type="hidden" value="<?php echo $captchaCalcul2; ?>" />
<?php endif; ?>

<?php if ($copieCourriel): ?>
	<p><input
		name="copie"
		type="checkbox"
		value="copie"
		<?php if ($copie == 'copie'): ?>
			checked="checked"
		<?php endif; ?>
	/>
	Je souhaite recevoir une copie du message</p>
<?php endif; ?>

<p><input name="envoyer" type="submit" value="Envoyer le message" /></p>

</div><!-- /divContact -->
</form>
