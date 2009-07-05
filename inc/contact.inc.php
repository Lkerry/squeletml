<?php
// Nécessaire à la traduction du module
phpGettext($racine, langue($langue));

$nom = '';
$courriel = '';
$message = '';
$copie = '';
$courrielsDecouvrir = '';

$messageEnvoye = FALSE;

include $racine . '/inc/faire-decouvrir.inc.php';

if ($decouvrir)
{
	echo '<h2 id="formulaireFaireDecouvrir">' . T_("Faire découvrir à des ami-e-s") . '</h2>';
}

// L'envoi du message est demandé
if (isset($_POST['envoyer']))
{
	$nom = securiseTexte($_POST['nom']);
	$courriel = securiseTexte($_POST['courriel']);
	$message = securiseTexte($_POST['message']);
	if (isset($_POST['copie']))
	{
		$copie = securiseTexte($_POST['copie']);
	}
	
	if ($decouvrir)
	{
		$courrielsDecouvrir = securiseTexte($_POST['courrielsDecouvrir']);
	}
	
	$msg = array ();

	if (empty($nom))
	{
		$msg['erreur'][] = T_("Vous n'avez pas inscrit de nom.");
	}

	if ($verifCourriel)
	{
		$motifCourriel = "/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i";

		if (!preg_match($motifCourriel, $courriel))
		{
			$msg['erreur'][] = T_("Votre adresse courriel ne semble pas avoir une forme valide. Veuillez vérifier.");
		}
	}
	
	if ($verifCourriel && isset($courrielsDecouvrir) && !empty($courrielsDecouvrir))
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
			$msg['erreur'][] = T_ngettext("L'adresse suivante ne semble pas avoir une forme valide; veuillez la vérifier:", "Les adresses suivantes ne semblent pas avoir une forme valide; veuillez les vérifier:", $i) . ' ' . substr($courrielsDecouvrirErreur, 0, -2);
		}
	}
	
	if (empty($message) && !$decouvrir)
	{
		$msg['erreur'][] = T_("Vous n'avez pas écrit de message.");
	}

	if ($captchaCalcul)
	{
		$ab = securiseTexte($_POST['ab']);
		$abSomme = $_POST['a'] + $_POST['d'];
		if ($abSomme != $ab)
		{
			$msg['erreur'][] = T_("Veuillez répondre correctement à la question antipourriel.");
		}
	}

	if ($captchaLiens)
	{
		if (substr_count($message, 'http') > $captchaLiensNbre)
		{
			$msg['erreur'][] = T_("Votre message a une forme qui le fait malheureusement classer comme du pourriel. Veuillez le modifier.");
		}
	}

	if (empty($msg))
	{
		// Envoi du message
		
		// Adresses
		
		$adresseFrom = $courriel;
		$adresseReplyTo = $courriel;
		
		$adresseBcc = '';
		
		if ($decouvrir && $copieCourriel && $copie == 'copie')
		{
			$adresseTo = $adresseFrom;
			$adresseBcc = $courrielsDecouvrir;
		}
		elseif ($decouvrir)
		{
			$adresseTo = $courrielsDecouvrir;
		}
		elseif (!$decouvrir && $copieCourriel && $copie == 'copie')
		{
			$adresseTo = $adresseFrom;
			$adresseBcc = $courrielContact;
		}
		elseif (!$decouvrir)
		{
			$adresseTo = $courrielContact;
		}
		
		$entete = '';
		$entete .= "From: $nom <$adresseFrom>\n";
		$entete .= "Reply-to: $adresseReplyTo\n";

		if (!empty($adresseBcc))
		{
			$entete .= "Bcc: $adresseBcc\n";
		}
		
		$entete .= "MIME-Version: 1.0\n";
		
		if ($decouvrir)
		{
			$entete .= "Content-Type: text/html; charset=\"utf-8\"\n";
		}
		else
		{
			$entete .= "Content-Type: text/plain; charset=\"utf-8\"\n";
		}
		
		if ($decouvrir)
		{
			$corps = str_replace("\r", '', $messageDecouvrir) . "\n";
		}
		else
		{
			$corps = str_replace("\r", '', $message) . "\n";
		}
		
		if (mail($adresseTo, $courrielObjetId . "Message de " . "$nom <$adresseFrom>", $corps, $entete))
		{
			$messageEnvoye = TRUE;
			$msg['envoi'][1] = T_("Votre message a bien été envoyé.");
			$nom = '';
			$courriel = '';
			$message = '';
			$copie = '';
			$courrielsDecouvrir = '';
		}
		else
		{
			$msg['envoi'][0] = T_("ERREUR: votre message n'a pas pu être envoyé. Essayez un peu plus tard.");
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
		echo '<p>' . T_("Le formulaire n'a pas été rempli correctement") . ':</p>';
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
<form id="formContact" method="post" action="<?php echo actionFormContact($decouvrir); ?>">
<div id="divContact">

<p><label><?php echo T_("Votre nom:"); ?></label><br />
<input class="champInfo" name="nom" type="text" size="30" maxlength="120" value="<?php echo $nom == T_('Votre nom') ? '' : $nom; ?>" /></p>

<p><label><?php echo T_("Votre courriel:"); ?></label><br />
<input class="champInfo" name="courriel" type="text" size="30" maxlength="120" value="<?php echo $courriel; ?>" /></p>

<?php if ($decouvrir): ?>
	<p><label><?php echo T_("Les courriels de vos ami-e-s:"); ?></label><br />
	<?php echo T_("Pour envoyer le message à plus d'une personne, veuillez séparer les adresses par une virgule."); ?><br />
	<input class="champInfo" name="courrielsDecouvrir" type="text" size="30" maxlength="120" value="<?php echo $courrielsDecouvrir; ?>" /></p>
	
	<p><?php echo T_("Modèle du message qui sera envoyé à vos ami-e-s:"); ?></p>
	<?php include $racine . '/inc/faire-decouvrir.inc.php'; ?>
	<div id="modeleMessageDecouvrir"><?php echo $messageDecouvrir; ?></div>
	
	<p><?php echo T_("Optionnellement, vous pouvez ajouter ci-dessous un petit mot personnalisé:"); ?></p>
<?php endif; ?>

<p><label><?php echo T_("Votre message:"); ?></label><br />
<textarea name="message" cols="30" rows="10" id="message"><?php echo $message; ?></textarea></p>

<?php if ($captchaCalcul): ?>
	<?php $captchaCalcul1 = rand($captchaCalculMin, $captchaCalculMax); ?>
	<?php $captchaCalcul2 = rand($captchaCalculMin, $captchaCalculMax); ?>
	<?php $captchaCalculBidon = rand($captchaCalculMin, $captchaCalculMax); ?>
	<p><label><?php echo T_("Antipourriel:"); ?></label><br />
	<?php printf(T_("Veuillez compléter: %1\$s ajouté à %2\$s vaut %3\$s"), $captchaCalcul1, $captchaCalcul2, "<input name='ab' type='text' size='4' />"); ?></p>
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
	<?php echo T_("Je souhaite recevoir une copie du message"); ?></p>
<?php endif; ?>

<p><input name="envoyer" type="submit" value="<?php echo T_('Envoyer le message'); ?>" /></p>

</div><!-- /divContact -->
</form>
