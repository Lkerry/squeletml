<?php
include_once 'init.inc.php';

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

$chemin = $racine . '/' . $_GET['fichier'];
$url = $urlRacine . '/' . $_GET['fichier'];
$nom = superBasename($_GET['fichier']);
$typeMime = typeMime($chemin, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);

if ($typeMime == 'application/octet-stream')
{
	$typeMime = 'application/force-download';
}

if (file_exists($chemin) && preg_match("|^$racine/site/fichiers/galeries/[^/]+/$nom$|", $chemin) && $typeMime != 'text/plain')
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
