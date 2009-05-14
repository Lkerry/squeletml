<?php

$urlComplete = $_GET['url'];
$nom = basename($_GET['url']);

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
	
	default:
		$type = 'application/force-download';
		break;
}

header('Content-Type: ' . $type);
header('Content-Disposition: attachment; filename="' . $nom . '"');
readfile($urlComplete);

?>
