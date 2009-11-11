<?php
include_once 'init.inc.php';

$chemin = $racine . '/' . $_GET['fichier'];
$url = $urlRacine . '/' . $_GET['fichier'];
$nom = basename($_GET['fichier']);

switch (strrchr(basename($nom), '.'))
{
	case '.png':
		$type = 'image/png';
		break;
	
	case '.gif':
		$type = 'image/gif';
		break;
	
	case '.jpeg':
		$type = 'image/jpeg';
		break;
	
	case '.jpg':
		$type = 'image/jpeg';
		break;
	
	case '.svg':
		$type = 'image/svg+xml';
		break;
	
	default:
		$type = 'application/force-download';
		break;
}

if (file_exists($chemin) && preg_match("|^$racine/site/fichiers/galeries/[^/]+/$nom|", $chemin))
{
	header('Content-Type: ' . $type);
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
