						<?php if ($afficherGalerie): ?>
							<?php echo $galerie; ?>
						<?php endif; ?>
						
						<?php if ($inclureContact): ?>
							<?php echo $contact; ?>
						<?php endif; ?>
						
						<?php if ($idGalerie): ?>
							</div><!-- /#galerie -->
						<?php endif; ?>
					</div><!-- /#interieurContenu -->
				</div><!-- /#contenu -->
				
				<!-- ____________________ #sousContenu ____________________ -->
				<div id="sousContenu">
					<?php echo $blocs; ?>
				</div><!-- /#sousContenu -->
				
				<?php if ($inclureBasDePage): ?>
					<!-- ____________________ #basDePage ____________________ -->
					<div id="basDePage">
						<?php include_once $cheminBasDePage; ?>
					</div><!-- /#basDePage -->
				<?php endif; ?>
			</div><!-- /#interieurPage -->
		</div><!-- /#page -->
		
		<?php echo $linkScriptFin; ?>
	</body>
</html>
