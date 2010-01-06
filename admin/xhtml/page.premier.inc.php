<?php echo $doctype; ?><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
	<!-- ____________________ <head> ____________________ -->
	<head>
		<!-- Titre. -->
		<title><?php echo $baliseTitle; ?></title>
		
		<!-- Métabalises. -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $adminCharset; ?>" />
		
		<meta name="robots" content="<?php echo $adminRobots; ?>" />
		
		<meta name="generator" content="Squeletml" />
		
		<!-- Balises `link` et `script`. -->
		<?php echo $linkScript; ?>
	</head>
	<?php flush(); // Si possible, envoi immédiat de l'en-tête au navigateur. ?>
	<!-- ____________________ <body> ____________________ -->
	<body<?php echo $idBody; ?>>
		<!-- ____________________ #ancres ____________________ -->
		<div id="ancres">
			<?php include_once $cheminAncres; ?>
		</div><!-- /#ancres -->
		
		<!-- ____________________ #page ____________________ -->
		<div id="page">
			<!-- ____________________ #enTete ____________________ -->
			<div id="enTete">
				<div id="menu">
					<?php echo $menu; ?>
				</div><!-- /#menu -->
				
				<div id="raccourcis">
					<?php include_once $cheminRaccourcis; ?>
				</div><!-- /#raccourcis -->
			</div><!-- /#enTete -->
			
			<!-- ____________________ #contenu ____________________ -->
			<div id="contenu">
				<div id="interieurContenu">
