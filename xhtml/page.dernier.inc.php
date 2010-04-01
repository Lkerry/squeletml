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
				
				<?php if ($inclureBasDePage): ?>
					<!-- ____________________ #basDePage ____________________ -->
					<div id="basDePage">
						<?php echo $blocs[600]; ?>
						<div class="sep"></div>
						<?php include_once $cheminBasDePage; ?>
					</div><!-- /#basDePage -->
				<?php endif; ?>
			</div><!-- /#interieurPage -->
		</div><!-- /#page -->
		
		<?php echo $linkScriptFin; ?>
	</body>
</html>
