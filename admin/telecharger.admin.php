<?php
include 'inc/zero.inc.php';
super_set_time_limit(300);

if (!empty($adminFiltreAccesDossiers))
{
	$tableauFiltresAccesDossiers = explode('|', $adminFiltreAccesDossiers);
	$tableauFiltresAccesDossiers = adminTableauCheminsCanoniques($tableauFiltresAccesDossiers);
}
else
{
	$tableauFiltresAccesDossiers = array ();
}

if ($adminPorteDocumentsDroits['telecharger'] && adminEmplacementPermis($_GET['fichier'], $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
{
	$chemin = securiseTexte($_GET['fichier']);
	$nom = securiseTexte(superBasename($chemin));
	
	if (file_exists($chemin))
	{
		if (chdir(dirname($chemin)))
		{
			$chemin = $nom;
		
			if (is_dir($chemin))
			{
				$dossierDeSauvegarde = $racineAdmin . '/cache';
				$date = '';
				
				if (isset($_GET['action']) && $_GET['action'] == 'date')
				{
					$date = '_' . date('Y-m-d_H:i:s');
				}
				
				$nomArchive = $nom . $date . '.tar';
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
				$typeMime = typeMime($chemin, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	
				if ($typeMime == 'application/octet-stream')
				{
					$typeMime = 'application/force-download';
				}
		
				header('Content-Type: ' . $typeMime);
				header('Content-Disposition: attachment; filename="' . $nom . '"');
				header('Content-Length: ' . filesize($chemin));
				@readfile($chemin);
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
