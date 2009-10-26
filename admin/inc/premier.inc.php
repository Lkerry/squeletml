<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
	<head>
		<title><?php echo $baliseTitle . ' | ' . T_("Administration de Squeletml"); ?></title>
		
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex, nofollow, noarchive" />
		
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/admin/css/admin.css"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "javascript:$urlRacine/js/squeletml.js"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "javascript:$urlRacine/admin/js/squeletml.js"); ?>
		<?php if (!adminEstIE()): ?>
			<?php $fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/js/wz_dragdrop.js"); ?>
		<?php endif; ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/inc/CodeMirror/js/codemirror.js"); ?>
		<?php $fichiersLinkScript[] = array ("$urlRacine/admin/*" => "favicon:$urlRacine/fichiers/puce.png"); ?>
		<?php echo linkScript($fichiersLinkScript, '', TRUE); ?>
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
				<script type="text/javascript">lienActif('menu', 'a');</script>
			</div><!-- /enTete -->
			
			<div id="contenu">
				<div id="interieurContenu">
