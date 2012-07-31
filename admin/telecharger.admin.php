<?php
include 'inc/zero.inc.php';

if (!empty($adminFiltreAccesDossiers))
{
	$tableauFiltresAccesDossiers = explode('|', $adminFiltreAccesDossiers);
	$tableauFiltresAccesDossiers = adminTableauCheminsCanoniques($tableauFiltresAccesDossiers);
}
else
{
	$tableauFiltresAccesDossiers = array ();
}

if (isset($_GET['fichier']))
{
	$chemin = decodeTexte($_GET['fichier']);
}
else
{
	$chemin = '';
}

if (file_exists($chemin) && adminEmplacementPermis($chemin, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
{
	$nom = superBasename($chemin);
	
	if (chdir(dirname($chemin)))
	{
		$chemin = $nom;
	
		if (is_dir($chemin))
		{
			$dossierDeSauvegarde = $racine . '/site/' . $dossierAdmin . '/cache';
			$nomArchive = '';
			
			if (preg_match('/^\.+$/', $nom))
			{
				$nomArchive .= '_' . $nom . '_';
			}
			else
			{
				$nomArchive .= $nom;
			}
			
			if (isset($_GET['action']) && $_GET['action'] == 'date')
			{
				if (substr($nomArchive, -1) !== '_')
				{
					$nomArchive .= '_';
				}
				
				$nomArchive .= date('Y-m-d_H:i:s');
			}
			
			$nomArchive .= '.tar';
			$cheminArchive = $dossierDeSauvegarde . '/' . $nomArchive;
			$archive = new tar($cheminArchive);
			$listeFichiers = adminListeFichiers($chemin);
	
			foreach ($listeFichiers as $fichier)
			{
				if (adminEmplacementPermis($fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
				{
					$archive->add($fichier);
				}
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
				@readfile($cheminArchive);
				@unlink($cheminArchive);
			}
	
			if (!$resultatArchive)
			{
				header('HTTP/1.1 500 Internal Server Error');
			}
		}
		else
		{
			$typeMime = typeMime($chemin);

			if ($typeMime == 'application/octet-stream')
			{
				$typeMime = 'application/force-download';
			}
	
			header('Content-Type: ' . $typeMime);
			header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', $nom) . '"');
			header('Content-Length: ' . filesize($chemin));
			@readfile($chemin);
		}
	}
	else
	{
		header('HTTP/1.1 500 Internal Server Error');
	}
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
