						<?php if ($afficherGalerie): ?>
							<?php echo $galerie; ?>
						<?php endif; ?>
						
						<?php if (!empty($idGalerie)): ?>
							</div><!-- /#galerie -->
						<?php endif; ?>
						
						<?php if ($inclureContact): ?>
							<?php echo $contact; ?>
						<?php endif; ?>
						
						<?php if (!empty($idCategorie)): ?>
							<?php echo $categorie; ?>
						<?php endif; ?>
						
						<div id="finInterieurContenu">
							<?php echo $blocs[400]; ?>
						</div><!-- /#finInterieurContenu -->
					</div><!-- /#interieurContenu -->
				</div><!-- /#contenu -->
				
				<!-- ____________________ #sousContenu ____________________ -->
				<div id="sousContenu">
					<?php echo $blocs[500]; ?>
				</div><!-- /#sousContenu -->
				
				<?php if ($inclureBasDePage): ?>
					<!-- ____________________ #basDePage ____________________ -->
					<div id="basDePage">
						<?php echo $blocs[600]; ?>
						<?php include_once $cheminBasDePage; ?>
					</div><!-- /#basDePage -->
				<?php endif; ?>
			</div><!-- /#interieurPage -->
		</div><!-- /#page -->
		
		<?php echo $linkScriptFin; ?>
	</body>
</html>
