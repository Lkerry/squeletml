<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

$fichier = securiseTexte($_GET['fichier']);
$chemin = $racine . '/' . $fichier;
$urlFichier = $urlRacine . '/' . superRawurlencode($fichier, TRUE);
$nom = superBasename($fichier);
$typeMime = typeMime($chemin);

if ($typeMime == 'application/octet-stream')
{
	$typeMime = 'application/force-download';
}

if (file_exists($chemin) && preg_match('|^' . preg_quote($racine, '|') . '(/site)?/fichiers/galeries/[^/]+/' . preg_quote($nom, '|') . '$|', $chemin) && $typeMime != 'text/plain')
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
