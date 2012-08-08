<?php
include 'init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';

if (isset($_GET['fichier']))
{
	$chemin = $racine . '/' . decodeTexte($_GET['fichier']);
	$nom = superBasename($chemin);
	$typeMime = typeMime($chemin);
	
	if ($typeMime == 'application/octet-stream')
	{
		$typeMime = 'application/force-download';
	}
}
else
{
	$chemin = '';
}

if (file_exists($chemin) && preg_match('|^' . preg_quote($racine, '|') . '(/site)?/fichiers/galeries/[^/]+/' . preg_quote($nom, '|') . '$|', $chemin) && $typeMime != 'text/plain')
{
	header('Content-Type: ' . $typeMime);
	header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', $nom) . '"');
	header('Content-Length: ' . @filesize($chemin));
	@readfile($chemin);
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
