<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $langue; ?>" lang="<?php echo $langue; ?>">
<head>
<title><?php echo $baliseTitle . ' | ' . T_("Administration de Squeletml"); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow, noarchive" />
<link rel="stylesheet" type="text/css" href="css/admin.css" media="screen" />
<script type="text/javascript" src="js/squeletml.js"></script>
<script type="text/javascript" src="js/wz_dragdrop.js"></script>
</head>
<body>
<div id="page">

<div id="entete">
	<div id="menu">
		<?php include $racine . '/admin/inc/html.menu.inc.php'; ?>
	</div><!-- /menu -->
</div><!-- /entete -->

<div id="ancres">
	<ul>
		<li><a href="#menu"><?php echo T_("Aller au menu"); ?></a></li>
		<li><a href="#contenu"><?php echo T_("Aller au contenu"); ?></a></li>
	</ul>
</div><!-- /ancres -->

<div id="contenu">
	<div id="interieurContenu">
