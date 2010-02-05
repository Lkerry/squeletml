<form id="formContact" method="post" action="<?php echo $actionFormContact; ?>#messagesContact">
	<div id="divContact">
		<p><label><?php echo T_("Votre nom:"); ?></label><br />
		<input class="champInfo" name="nom" type="text" size="30" maxlength="120" value="<?php echo $nom; ?>" /></p>
		
		<p><label><?php echo T_("Votre courriel:"); ?></label><br />
		<input class="champInfo" name="courriel" type="text" size="30" maxlength="120" value="<?php echo $courriel; ?>" /></p>
		
		<?php if ($decouvrir): ?>
			<p><label><?php echo T_("Les courriels de vos ami-e-s:"); ?></label><br />
			<?php echo T_("Pour envoyer le message à plus d'une personne, veuillez séparer les adresses par une virgule."); ?><br />
			<input class="champInfo" name="courrielsDecouvrir" type="text" size="30" maxlength="120" value="<?php echo $courrielsDecouvrir; ?>" /></p>
			
			<p><?php echo T_("Modèle du message qui sera envoyé à vos ami-e-s:"); ?></p>
			
			<div id="modeleMessageDecouvrir">
				<?php echo $messageDecouvrir; ?>
			</div><!-- /#modeleMessageDecouvrir -->
			
			<p><?php echo T_("Optionnellement, vous pouvez ajouter ci-dessous un petit mot personnalisé:"); ?></p>
		<?php endif; ?>
		
		<p><label><?php echo T_("Votre message:"); ?></label><br />
		<textarea name="message" cols="30" rows="10" id="message"><?php echo $message; ?></textarea></p>
		
		<?php if ($contactActiverCaptchaCalcul): ?>
			<p><label><?php echo T_("Antipourriel:"); ?></label><br />
				
				<?php if ($contactCaptchaCalculInverse): ?>
					<?php printf(T_("Veuillez indiquer deux nombres qui, une fois additionnés, donnent %1\$s (plusieurs réponses possibles):"), $contactCaptchaCalculUn); ?>
					<?php printf(T_("%1\$s et %2\$s"), "<input name='r' type='text' size='4' />", "<input name='s' type='text' size='4' />"); ?>
				<?php else: ?>
					<?php printf(T_("Veuillez compléter: %1\$s ajouté à %2\$s vaut %3\$s"), $contactCaptchaCalculUn, $contactCaptchaCalculDeux, "<input name='r' type='text' size='4' />"); ?>
				<?php endif; ?>
			</p>
			
			<?php echo $inputHidden; ?>
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
