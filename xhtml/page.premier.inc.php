<?php echo $contenuDoctype . $ouvertureBaliseHtml; ?>
	<!-- ____________________ <head> ____________________ -->
	<head>
		<!-- Métabalises (1 de 2). -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
		
		<!-- Titre. -->
		<title><?php echo $baliseTitle . $baliseTitleComplement; ?></title>
		
		<!-- Métabalises (2 de 2). -->
		<?php if (!empty($description)): ?>
			<meta name="description" content="<?php echo $description; ?>" />
		<?php endif; ?>
		
		<?php if ($inclureMotsCles && !empty($motsCles)): ?>
			<meta name="keywords" content="<?php echo $motsCles; ?>" />
		<?php endif; ?>
		
		<?php if (!empty($robots)): ?>
			<meta name="robots" content="<?php echo $robots; ?>" />
		<?php endif; ?>
		
		<meta name="generator" content="Squeletml" />
		
		<?php if (!empty($auteur)): ?>
			<meta name="author" content="<?php echo $auteur; ?>" />
		<?php endif; ?>
		
		<?php if (!empty($dateCreation)): ?>
			<meta name="date-creation-yyyymmdd" content="<?php echo $dateCreation; ?>" />
		<?php endif; ?>
		
		<?php if (!empty($dateRevision)): ?>
			<meta name="date-revision-yyyymmdd" content="<?php echo $dateRevision; ?>" />
		<?php endif; ?>
		
		<!-- Balises `link` et `script`. -->
		<?php echo $linkScript; ?>
	</head>
	<!-- ____________________ <body> ____________________ -->
	<body<?php echo $classesBody; ?>>
		<?php if ($inclureAncres): ?>
			<!-- ____________________ #ancres ____________________ -->
			<div id="ancres">
				<?php include $cheminAncres; ?>
			</div><!-- /#ancres -->
		<?php endif; ?>
		
		<?php if ($afficherMessageIe6): ?>
			<!-- ____________________ Message pour IE6. ____________________ -->
			<?php echo $messageIe6; ?>
		<?php endif; ?>
		
		<?php if ($siteEstEnMaintenance): ?>
			<!-- ____________________ Maintenance du site. ____________________ -->
			<?php echo $noticeMaintenance; ?>
		<?php endif; ?>
		
		<!-- ____________________ #page ____________________ -->
		<div id="page">
			<div id="interieurPage">
				<!-- ____________________ #enTete ____________________ -->
				<div id="enTete">
					<?php if ($inclureSurTitre): ?>
						<div id="surTitre">
							<?php include $cheminSurTitre; ?>
						</div><!-- /#surTitre -->
					<?php endif; ?>
	
					<div id="titre">
						<?php echo $nomSite; ?>
					</div><!-- /#titre -->

					<?php if ($inclureSousTitre): ?>
						<div id="sousTitre">
							<?php include $cheminSousTitre; ?>
						</div><!-- /#sousTitre -->
					<?php endif; ?>
					
					<div class="sep"></div>
					<?php echo $blocs[100]; ?>
				</div><!-- /#enTete -->
				
				<?php if (!empty($blocs[200])): ?>
					<!-- ____________________ #surContenu ____________________ -->
					<div id="surContenu">
						<?php echo $blocs[200]; ?>
					</div><!-- /#surContenu -->
				<?php endif; ?>
				
				<!-- ____________________ #contenu ____________________ -->
				<div id="contenu"<?php echo $classesContenu; ?>>
					<div id="interieurContenu">
						<?php if (!empty($blocs[300])): ?>
							<div id="debutInterieurContenu">
								<?php echo $blocs[300]; ?>
							</div><!-- /#debutInterieurContenu -->
						<?php endif; ?>
						
						<?php if ($inclureCachePartiel): ?>
							<?php include $cheminCachePartiel; ?>
						<?php endif; ?>
						
						<div id="milieuInterieurContenu">
							<?php if ($inclureApercu): ?>
								<?php echo $apercu; ?>
							<?php endif; ?>
							
							<?php if (!empty($idGalerie)): ?>
								<div id="galerie">
							<?php endif; ?>
