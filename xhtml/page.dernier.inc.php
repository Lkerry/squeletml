							<?php if ($afficherGalerie): ?>
								<?php echo $galerie; ?>
							<?php endif; ?>
						
							<?php if (!empty($idGalerie)): ?>
								</div><!-- /#galerie -->
							<?php endif; ?>
						
							<?php if ($afficherCategorie): ?>
								<?php echo $categorie; ?>
							<?php endif; ?>
						
							<?php if ($inclureContact): ?>
								<?php echo $contact; ?>
							<?php endif; ?>
						</div><!-- /#milieuInterieurContenu -->
						
						<?php if (!empty($blocs[400])): ?>
							<div id="finInterieurContenu">
								<?php echo $blocs[400]; ?>
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
