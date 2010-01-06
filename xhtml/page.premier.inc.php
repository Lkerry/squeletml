<?php echo $doctype; ?><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
	<!-- ____________________ <head> ____________________ -->
	<head>
		<!-- Titre. -->
		<title><?php echo $baliseTitle; ?></title>
		
		<!-- Métabalises. -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
		
		<?php if (!empty($description)): ?>
			<meta name="description" content="<?php echo $description; ?>" />
		<?php endif; ?>
		
		<?php if ($inclureMotsCles): ?>
			<meta name="keywords" content="<?php echo $motsCles; ?>" />
		<?php endif; ?>
		
		<meta name="robots" content="<?php echo $robots; ?>" />
		
		<meta name="generator" content="Squeletml" />
		
		<!-- Balises `link` et `script`. -->
		<?php echo $linkScript; ?>
	</head>
	<?php flush(); // Si possible, envoi immédiat de l'en-tête au navigateur. ?>
	<!-- ____________________ <body> ____________________ -->
	<body<?php echo $classesBody; ?>>
		<!-- ____________________ #ancres ____________________ -->
		<div id="ancres">
			<?php include_once $cheminAncres; ?>
		</div><!-- /#ancres -->

		<?php if ($afficherMessageIe6): ?>
			<!-- ____________________ Message pour IE6. ____________________ -->
			<?php echo $messageIe6; ?>
		<?php endif; ?>
		
		<!-- ____________________ #page ____________________ -->
		<div id="page">
			<div id="interieurPage">
				<!-- ____________________ #enTete ____________________ -->
				<div id="enTete" class="sep">
					<?php if ($inclureSurTitre): ?>
						<div id="surTitre">
							<?php include_once $cheminSurTitre; ?>
						</div><!-- /#surTitre -->
					<?php endif; ?>
	
					<div id="titre">
						<?php echo $nomSite; ?>
					</div><!-- /#titre -->

					<div id="sousTitre">
						<?php include_once $cheminSousTitre; ?>
					</div><!-- /#sousTitre -->
					
					<?php echo $blocs[100]; ?>
				</div><!-- /#enTete -->
		
				<!-- ____________________ #surContenu ____________________ -->
				<div id="surContenu">
					<?php echo $blocs[200]; ?>
				</div><!-- /#surContenu -->
		
				<!-- ____________________ #contenu ____________________ -->
				<div id="contenu"<?php echo $classesContenu; ?>>
					<div id="interieurContenu">
						<?php if ($inclureApercu): ?>
							<?php echo $apercu; ?>
						<?php endif; ?>
						
						<div id="debutInterieurContenu">
							<?php echo $blocs[300]; ?>
						</div><!-- /#debutInterieurContenu -->
						
						<?php if ($idGalerie): ?>
							<div id="galerie" class="sep">
						<?php endif; ?>
