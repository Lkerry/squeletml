<?php
include 'inc/zero.inc.php';

if ($porteDocumentsDroits['telecharger'])
{
	$chemin = $_GET['fichier'];
	$nom = basename($chemin);

	if (file_exists($chemin))
	{
		if (chdir(dirname($chemin)))
		{
			$chemin = $nom;
		
			if (is_dir($chemin))
			{
				$dossierDeSauvegarde = $racineAdmin . '/cache';
				$nomArchive = $nom . '.tar';
				$cheminArchive = $dossierDeSauvegarde . '/' . $nomArchive;
		
				$archive = new tar($cheminArchive);
		
				$listeFichiers = adminListeFichiers($chemin);
		
				foreach ($listeFichiers as $fichier)
				{
					$archive->add($fichier);
				}
		
				$resultatArchive = $archive->write();
		
				if ($resultatArchive)
				{
					$contentType = 'application/x-tar';
					
					if (function_exists('gzopen') && adminGz($dossierDeSauvegarde . '/' . $nomArchive) !== FALSE)
					{
						@unlink($cheminArchive);
						$nomArchive = $nomArchive . '.gz';
						$cheminArchive = $dossierDeSauvegarde . '/' . $nomArchive;
						$contentType = 'application/x-gtar';
					}
					
					header('Content-Type: ' . $contentType);
					header('Content-Disposition: attachment; filename="' . $nomArchive . '"');
					header('Content-Length: ' . filesize($cheminArchive));
					readfile($cheminArchive);
					@unlink($cheminArchive);
				}
		
				if (!$resultatArchive)
				{
					header('HTTP/1.1 500 Internal Server Error');
				}
			}
			else
			{
				$typeMime = mimedetect_mime(array ('filepath' => $chemin, 'filename' => $nom), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	
				if ($typeMime == 'application/octet-stream')
				{
					$typeMime = 'application/force-download';
				}
		
				header('Content-Type: ' . $typeMime);
				header('Content-Disposition: attachment; filename="' . $nom . '"');
				header('Content-Length: ' . filesize($chemin));
				readfile($chemin);
			}
		}
		else
		{
			header('HTTP/1.1 500 Internal Server Error');
		}
	}
	else
	{
		header('HTTP/1.1 404 Not found');
	}
}
else
{
	header('HTTP/1.1 401 Unauthorized');
}
?>
