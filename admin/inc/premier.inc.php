<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $langue; ?>" lang="<?php echo $langue; ?>">
<head>
<title><?php echo $baliseTitle . ' | ' . T_("Administration de Squeletml"); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow, noarchive" />
<?php
$fichiersLinkScript[] = array ("$urlRacine/admin/*" => "css:$urlRacine/admin/css/admin.css");
$fichiersLinkScript[] = array ("$urlRacine/admin/galeries.admin.php" => "javascript:$urlRacine/admin/js/squeletml.js");
$fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/js/wz_dragdrop.js");
$fichiersLinkScript[] = array ("$urlRacine/admin/porte-documents.admin.php" => "javascript:$urlRacine/admin/inc/CodeMirror/js/codemirror.js");

echo construitLinkScript($fichiersLinkScript, '', TRUE);
?>
</head>
<body>
<div id="page">

<div id="entete">
	<div id="menu">
		<?php include $racine . '/admin/inc/html.menu.inc.php'; ?>
	</div><!-- /menu -->
</div><!-- /entete -->

<div id="ancres">
	<?php include $racine . '/admin/inc/html.ancres.inc.php'; ?>
</div><!-- /ancres -->

<div id="contenu">
	<div id="interieurContenu">
