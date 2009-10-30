<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
	<head>
		<title><?php echo $baliseTitle . ' | ' . T_("Administration de Squeletml"); ?></title>
		
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex, nofollow, noarchive" />
		
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/admin/css/admin.css"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/admin/css/ie6.css"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/admin/css/ie7.css"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/css/extensions-proprietaires.css"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "javascript:$urlRacine/js/phpjs.js"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "javascript:$urlRacine/js/squeletml.js"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "javascript:$urlRacine/admin/js/squeletml.js"); ?>
		<?php if (!adminEstIE()): ?>
			<?php $fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/js/wz_dragdrop.js"); ?>
		<?php endif; ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/inc/CodeMirror/js/codemirror.js"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "favicon:$urlRacine/fichiers/puce.png"); ?>
		<?php echo linkScript($fichiersLinkScript, '', TRUE); ?>
		
		<?php if ($tableDesMatieres): ?>
			<!-- Table des matières -->
			<link type="text/css" rel="stylesheet" href="<?php echo $urlRacine; ?>/css/table-des-matieres.css" media="screen" />
			
			<?php echo '<!--[if lt IE 7]>' . "\n"; ?>
				<link type="text/css" rel="stylesheet" href="<?php echo $urlRacine; ?>/css/table-des-matieres-ie6.css" media="screen" />
			<?php echo '<![endif]-->'; ?>
			
			<?php if (!$javascriptGettextInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/Gettext.js"></script>
			
				<?php $locale = locale(LANGUE); ?>
				<?php if (file_exists($racine . '/locale/' . $locale)): ?>
					<link type="application/x-po" rel="gettext" href="<?php echo $urlRacine; ?>/locale/<?php echo $locale; ?>/LC_MESSAGES/squeletml.po" />
				<?php endif; ?>
			
				<script type="text/javascript">
					var gt = new Gettext({'domain': 'squeletml'});
				</script>
				<?php $javascriptGettextInclus = TRUE; ?>
			<?php endif; ?>
			
			<?php if (!$jQueryInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
				<?php $jQueryInclus = TRUE; ?>
			<?php endif; ?>
			
			<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.tableofcontents.js"></script>
			
			<script type="text/javascript">
				tableDesMatieres('interieurContenu', 'ul');
			</script>
		<?php endif; ?>
	</head>
	<body id="<?php echo adminBodyId(); ?>">
		<div id="ancres">
			<?php include $racine . '/admin/inc/html.ancres.inc.php'; ?>
		</div><!-- /ancres -->
		
		<div id="page">
			<div id="enTete">
				<div id="menu">
					<?php include $racine . '/admin/inc/html.menu.inc.php'; ?>
				</div><!-- /menu -->
				<script type="text/javascript">lienActif('menu');</script>
			</div><!-- /enTete -->
			
			<div id="contenu">
				<div id="interieurContenu">
