						<?php if ($inclureFinMilieuInterieurContenu): ?>
								<?php if ($afficherGalerie): ?>
									<?php echo $galerie; ?>
								<?php endif; ?>
								
								<?php if (!empty($idGalerie)): ?>
									</div><!-- /#galerie -->
								<?php endif; ?>
								
								<?php if ($afficherCategorie): ?>
									<?php echo $categorie; ?>
								<?php endif; ?>
							</div><!-- /#milieuInterieurContenu -->
						<?php endif; ?>
						
						<?php if ($inclureCachePartiel): ?>
							<?php include $cheminCachePartiel; ?>
						<?php endif; ?>
						
						<?php if ($inclureFinInterieurContenu): ?>
							<div id="finInterieurContenu">
								<?php if ($inclureContact): ?>
									<?php echo $contact; ?>
								<?php endif; ?>
								
								<?php if ($inclureFormulaireCommentaire): ?>
									<?php echo $formulaireCommentaire; ?>
								<?php endif; ?>
								
								<?php if (!empty($blocs[400])): ?>
									<?php echo $blocs[400]; ?>
								<?php endif; ?>
							</div><!-- /#finInterieurContenu -->
						<?php endif; ?>
					</div><!-- /#interieurContenu -->
				</div><!-- /#contenu -->
				
				<?php if (!empty($blocs[500])): ?>
					<!-- ____________________ #sousContenu ____________________ -->
					<div id="sousContenu">
						<?php echo $blocs[500]; ?>
					</div><!-- /#sousContenu -->
				<?php endif; ?>
				
				<?php if ($inclureBasDePage && $basDePageInterieurPage): ?>
					<!-- ____________________ #basDePageInterieurPage ____________________ -->
					<div id="basDePageInterieurPage">
						<?php echo $blocs[600]; ?>
						<div class="sep"></div>
						<?php include $cheminBasDePage; ?>
					</div><!-- /#basDePageInterieurPage -->
				<?php endif; ?>
			</div><!-- /#interieurPage -->
		</div><!-- /#page -->
		
		<?php if ($inclureBasDePage && !$basDePageInterieurPage): ?>
			<!-- ____________________ #basDePageHorsPage ____________________ -->
			<div id="basDePageHorsPage">
				<?php echo $blocs[600]; ?>
				<div class="sep"></div>
				<?php include $cheminBasDePage; ?>
			</div><!-- /#basDePageHorsPage -->
		<?php endif; ?>
		
		<?php echo $linkScriptFin; ?>
	</body>
</html>
