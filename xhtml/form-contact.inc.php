<form id="formContact" method="post" action="<?php echo $actionFormContact; ?>">
	<div id="divContact">
		<p><label for="inputNom"><?php echo T_("Votre nom:"); ?></label><br />
		<input id="inputNom" class="champInfo" type="text" name="nom" size="30" maxlength="120" value="<?php echo $nom; ?>" /></p>
		
		<?php // Champs supplémentaires optionnels après le nom. ?>
		<?php if (!$partageCourrielActif && cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact-champs-apres-nom', FALSE)): ?>
			<?php include cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact-champs-apres-nom'); ?>
		<?php endif; ?>
		
		<p><label for="inputCourriel"><?php echo T_("Votre courriel:"); ?></label><br />
		<input id="inputCourriel" class="champInfo" type="text" name="courriel" size="30" maxlength="120" value="<?php echo $courriel; ?>" /></p>
		
		<?php if ($partageCourrielActif): ?>
			<p><label for="inputCourrielsPartageCourriel"><?php echo T_("Les courriels des destinataires:"); ?></label><br />
			<?php echo T_("Pour envoyer le message à plus d'une personne, veuillez séparer les adresses par une virgule."); ?><br />
			<input id="inputCourrielsPartageCourriel" class="champInfo" type="text" name="courrielsPartageCourriel" size="30" maxlength="120" value="<?php echo $courrielsPartageCourriel; ?>" /></p>
			
			<p><?php echo T_("Modèle du message qui sera envoyé aux destinataires:"); ?></p>
			
			<div id="modeleMessagePartageCourriel" class="bloc blocAvecFond">
				<?php echo $messagePartageCourriel; ?>
			</div><!-- /#modeleMessagePartageCourriel -->
			
			<p><?php echo T_("Optionnellement, vous pouvez ajouter ci-dessous un petit mot personnalisé:"); ?></p>
		<?php endif; ?>
		
		<p><label for="message"><?php echo T_("Votre message:"); ?></label><br />
		<textarea id="message" name="message" cols="30" rows="10"><?php echo $message; ?></textarea></p>
		
		<?php // Champs supplémentaires optionnels après le message. ?>
		<?php if (!$partageCourrielActif && cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact-champs-apres-message', FALSE)): ?>
			<?php include cheminXhtml($racine, array ($langue, $langueParDefaut), 'form-contact-champs-apres-message'); ?>
		<?php endif; ?>
		
		<?php if ($contactActiverCaptchaCalcul): ?>
			<?php echo captchaCalcul($contactCaptchaCalculMin, $contactCaptchaCalculMax, $contactCaptchaCalculInverse); ?>
		<?php endif; ?>
		
		<?php if ($contactCopieCourriel): ?>
			<p><input
				id="inputCopie"
				type="checkbox"
				name="copie"
				value="copie"
				<?php if ($copie): ?>
					checked="checked"
				<?php endif; ?>
			/>
			<label for="inputCopie" class="labelPhrase"><?php echo T_("Je souhaite recevoir une copie du message"); ?></label></p>
		<?php endif; ?>
		
		<p><input type="submit" name="envoyer" value="<?php echo T_('Envoyer le message'); ?>" /></p>
	</div><!-- /#divContact -->
</form>
