<?php echo $contenuDoctype . $ouvertureBaliseHtml; ?>
	<!-- ____________________ <head> ____________________ -->
	<head>
		<!-- Métabalises (1 de 2). -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $adminCharset; ?>" />
		
		<!-- Titre. -->
		<title><?php echo $baliseTitle; ?></title>
		
		<!-- Métabalises (2 de 2). -->
		<meta name="robots" content="<?php echo $adminRobots; ?>" />
		
		<meta name="generator" content="Squeletml" />
		
		<!-- Balises `link` et `script`. -->
		<?php echo $linkScript; ?>
	</head>
	<!-- ____________________ <body> ____________________ -->
	<body<?php echo "$idBody $classesBody"; ?>>
		<!-- ____________________ #ancres ____________________ -->
		<div id="ancres">
			<?php include $cheminAncres; ?>
		</div><!-- /#ancres -->
		
		<?php if ($siteEstEnMaintenance): ?>
			<!-- ____________________ Maintenance du site. ____________________ -->
			<?php echo $noticeMaintenance; ?>
		<?php endif; ?>
		
		<!-- ____________________ #page ____________________ -->
		<div id="page">
			<!-- ____________________ #enTete ____________________ -->
			<div id="enTete">
				<div id="menu">
					<?php echo $menu; ?>
				</div><!-- /#menu -->
				
				<div id="raccourcis">
					<?php include $cheminRaccourcis; ?>
				</div><!-- /#raccourcis -->
			</div><!-- /#enTete -->
			
			<script type="text/javascript">
			//<![CDATA[
				boiteDeroulante('#enTete', '');
			//]]>
			</script>
			
			<!-- ____________________ #contenu ____________________ -->
			<div id="contenu">
				<div id="interieurContenu">
					<div id="lienBas">
						<?php include $cheminLienBas; ?>
					</div><!-- /#lienBas -->
					
					<?php echo $h1; ?>
