<form id="formContact" method="post" action="<?php echo $actionFormContact; ?>">
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
			
			<div id="modeleMessageDecouvrir">
				<?php echo $messageDecouvrir; ?>
			</div><!-- /#modeleMessageDecouvrir -->
			
			<p><?php echo T_("Optionnellement, vous pouvez ajouter ci-dessous un petit mot personnalisé:"); ?></p>
		<?php endif; ?>
		
		<p><label><?php echo T_("Votre message:"); ?></label><br />
		<textarea name="message" cols="30" rows="10" id="message"><?php echo $message; ?></textarea></p>
		
		<?php if ($contactActiverCaptchaCalcul): ?>
			<?php $contactActiverCaptchaCalcul1 = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax); ?>
			<?php $contactActiverCaptchaCalcul2 = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax); ?>
			<?php $contactActiverCaptchaCalculBidon = rand($contactCaptchaCalculMin, $contactCaptchaCalculMax); ?>
			
			<p><label><?php echo T_("Antipourriel:"); ?></label><br />
			<?php printf(T_("Veuillez compléter: %1\$s ajouté à %2\$s vaut %3\$s"), $contactActiverCaptchaCalcul1, $contactActiverCaptchaCalcul2, "<input name='ab' type='text' size='4' />"); ?></p>
			
			<input name="a" type="hidden" value="<?php echo $contactActiverCaptchaCalcul1; ?>" />
			<input name="b" type="hidden" value="<?php echo $contactActiverCaptchaCalculBidon; ?>" />
			
			<?php
			// Ajout de quelques `input` bidons dans le but d'augmenter les chances de duper les robots pourrielleurs.
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
				echo $tab . '<input name="' . $lettreAuHasard . '" type="hidden" value="' . rand($contactCaptchaCalculMin, $contactCaptchaCalculMax) . '" />' . "\n";
			}
			?>
			<input name="d" type="hidden" value="<?php echo $contactActiverCaptchaCalcul2; ?>" />
		<?php endif; ?>
		
		<?php if ($contactCopieCourriel): ?>
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
	</div><!-- /#divContact -->
</form>
