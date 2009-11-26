<?php
include_once 'init.inc.php';
include_once $racine . '/inc/config.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

$chemin = $racine . '/' . $_GET['fichier'];
$url = $urlRacine . '/' . $_GET['fichier'];
$nom = superBasename($_GET['fichier']);
$typeMime = mimedetect_mime(array ('filepath' => $chemin, 'filename' => $nom), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);

if ($typeMime == 'application/octet-stream')
{
	$typeMime = 'application/force-download';
}

if (file_exists($chemin) && preg_match("|^$racine/site/fichiers/galeries/[^/]+/$nom|", $chemin))
{
	header('Content-Type: ' . $typeMime);
	header('Content-Disposition: attachment; filename="' . $nom . '"');
	header('Content-Length: ' . filesize($chemin));
	readfile($url);
}
elseif (file_exists($chemin))
{
	header('HTTP/1.1 401 Unauthorized');
}
else
{
	header('HTTP/1.1 404 Not found');
}

?>
