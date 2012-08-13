<form id="formCommentaire" method="post" action="<?php echo $actionFormCommentaire; ?>">
	<div id="divCommentaire">
		<p id="noteCommentaire">
			<?php if ($champsTousObligatoires): ?>
				<?php echo T_("Tous les champs sont obligatoires."); ?>
			<?php else: ?>
				<?php echo T_("Les champs obligatoires sont marqués d'un astérisque (<code>*</code>)."); ?>
			<?php endif; ?>
		</p>
		
		<?php if ($commentairesChampsActifs['nom']): ?>
			<p>
				<label for="inputNom">
					<?php if (!$champsTousObligatoires && $commentairesChampsObligatoires['nom']): ?>
						<?php echo T_("Votre nom ou pseudo<code>*</code>:"); ?>
					<?php else: ?>
						<?php echo T_("Votre nom ou pseudo:"); ?>
					<?php endif; ?>
				</label><br />
				<input id="inputNom" class="champInfo" type="text" name="nom" size="30" maxlength="120" value="<?php echo $nom; ?>" />
			</p>
		<?php endif; ?>
		
		<?php if ($commentairesChampsActifs['courriel']): ?>
			<p>
				<label for="inputCourriel">
					<?php if (!$champsTousObligatoires && $commentairesChampsObligatoires['courriel']): ?>
						<?php echo T_("Votre courriel<code>*</code>:"); ?>
					<?php else: ?>
						<?php echo T_("Votre courriel:"); ?>
					<?php endif; ?>
				</label><br />
				<span class="labelPrecision"><?php echo T_("Le courriel ne sera pas divulgué."); ?></span><br />
				<input id="inputCourriel" class="champInfo" type="text" name="courriel" size="30" maxlength="120" value="<?php echo $courriel; ?>" />
			</p>
		<?php endif; ?>
		
		<?php if ($commentairesChampsActifs['site']): ?>
			<p>
				<label for="inputSite">
					<?php if (!$champsTousObligatoires && $commentairesChampsObligatoires['site']): ?>
						<?php echo T_("Votre site Web<code>*</code>:"); ?>
					<?php else: ?>
						<?php echo T_("Votre site Web:"); ?>
					<?php endif; ?>
				</label><br />
				<input id="inputSite" class="champInfo" type="text" name="site" size="30" maxlength="120" value="<?php echo $site; ?>" />
			</p>
		<?php endif; ?>
		
		<p id="commentaireMessage"><label for="message"><?php echo T_("Votre commentaire<code>*</code>:"); ?></label><br />
		<textarea id="message" name="message" cols="30" rows="10"><?php echo $message; ?></textarea></p>
		
		<div id="commentaireAideSyntaxe">
			<p class="bDtitre"><?php echo T_("Aide sur la syntaxe"); ?></p>
			
			<ul class="bDcorps">
				<li><?php printf(T_("La <a href=\"%1\$s\">syntaxe Markdown</a> peut être utilisée."), 'http://michelf.ca/projets/php-markdown/syntaxe/'); ?></li>
				<li><?php printf(T_("Les balises HTML suivantes sont permises: %1\$s."), '<code>p</code>, <code>em</code>, <code>strong</code>, <code>strike</code>, <code>ul</code>, <code>ol</code>, <code>li</code>, <code>a</code>, <code>pre</code>, <code>code</code>, <code>q</code>, <code>blockquote</code>, <code>br</code>'); ?></li>
			</ul>
		</div>
		
		<?php if ($commentairesActiverCaptchaCalcul): ?>
			<?php echo captchaCalcul($commentairesCaptchaCalculMin, $commentairesCaptchaCalculMax, $commentairesCaptchaCalculInverse, !$champsTousObligatoires); ?>
		<?php endif; ?>
		
		<?php if ($commentairesNotification): ?>
			<p><input
				id="inputNotification"
				type="checkbox"
				name="notification"
				value="notification"
				<?php if ($notification): ?>
					checked="checked"
				<?php endif; ?>
			/>
			<label for="inputNotification" class="labelPhrase"><?php echo T_("Je souhaite être notifié par courriel des nouveaux commentaires sur cette page"); ?></label></p>
		<?php endif; ?>
		
		<input type="hidden" name="idFormulaire" value="<?php echo $idFormulaireCommentaire; ?>" />
		
		<p><input type="submit" name="envoyerCommentaire" value="<?php echo T_('Envoyer le commentaire'); ?>" /></p>
	</div><!-- /#divCommentaire -->
</form>
