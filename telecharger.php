<?php
include_once 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

eval(variablesAaffecterAuDebut());

foreach (cheminsInc($racine, 'config') as $cheminFichier)
{
	include_once $cheminFichier;
}

include_once $racine . '/inc/mimedetect/file.inc.php';
include_once $racine . '/inc/mimedetect/mimedetect.inc.php';

$fichier = securiseTexte($_GET['fichier']);
$chemin = $racine . '/' . $fichier;
$urlFichier = $urlRacine . '/' . superRawurlencode($fichier, TRUE);
$nom = superBasename($fichier);
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
	@readfile($urlFichier);
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
