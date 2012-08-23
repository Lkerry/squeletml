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
	$chemin = decodeTexteGet($_GET['fichier']);
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
			$typeMime = 'application/x-tar';
			$listeFichiers = adminListeFichiers($chemin);
			$listeFichiersFiltree = array ();
			
			foreach ($listeFichiers as $fichier)
			{
				if (adminEmplacementPermis($fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers) && strpos(realpath($fichier), "$racine/site/$dossierAdmin/cache/") !== 0)
				{
					$listeFichiersFiltree[] = $fichier;
				}
			}
			
			$inclureConfigGalerie = FALSE;
			
			if (!empty($_GET['galerie']))
			{
				$idGalerie = decodeTexteGet($_GET['galerie']);
				$listeGaleries = listeGaleries($racine);
				
				if (isset($listeGaleries[$idGalerie]))
				{
					$contenuConfigGalerie = '';
					
					if (isset($listeGaleries[$idGalerie]['dossier']))
					{
						$contenuConfigGalerie .= 'dossier=' . $listeGaleries[$idGalerie]['dossier'] . "\n";
					}
					
					if (isset($listeGaleries[$idGalerie]['url']))
					{
						$contenuConfigGalerie .= 'url=' . $listeGaleries[$idGalerie]['url'] . "\n";
					}
					
					if (isset($listeGaleries[$idGalerie]['rss']))
					{
						$contenuConfigGalerie .= 'rss=' . $listeGaleries[$idGalerie]['rss'] . "\n";
					}
					
					if (!empty($contenuConfigGalerie))
					{
						$contenuConfigGalerie = "[$idGalerie]\n$contenuConfigGalerie";
						$dossierTemporaire = chaineAleatoire(16);
						$parentConfigGalerieAinclure = "$racine/site/$dossierAdmin/cache/$dossierTemporaire";
						$cheminConfigGalerieAinclure = "$parentConfigGalerieAinclure/galeries.ini.txt";
						
						if ((is_dir($parentConfigGalerieAinclure) || (!file_exists($parentConfigGalerieAinclure) && @mkdir($parentConfigGalerieAinclure, octdec(755)))) && @file_put_contents($cheminConfigGalerieAinclure, $contenuConfigGalerie) !== FALSE)
						{
							$inclureConfigGalerie = TRUE;
						}
					}
				}
			}
			
			try
			{
				$archive = ezcArchive::open($cheminArchive, ezcArchive::TAR_USTAR);
				$archive->append($listeFichiersFiltree, '');
				
				if ($inclureConfigGalerie)
				{
					$archive->append($cheminConfigGalerieAinclure, dirname($cheminConfigGalerieAinclure));
					adminRmdirRecursif($parentConfigGalerieAinclure);
				}
				
				$archive->close();
				unset($archive);
			}
			catch (Exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
				echo $e->getMessage();
				@unlink($cheminArchive);
				adminRmdirRecursif($parentConfigGalerieAinclure);
				
				exit(1);
			}
			
			if (in_array('compress.zlib', stream_get_wrappers()))
			{
				if (@file_put_contents("compress.zlib://$cheminArchive.gz", file_get_contents($cheminArchive)) !== FALSE)
				{
					@unlink($cheminArchive);
					$nomArchive .= '.gz';
					$cheminArchive .= '.gz';
					$typeMime = 'application/x-gtar';
				}
				else
				{
					@unlink("$cheminArchive.gz");
				}
			}
			
			header('Content-Type: ' . $typeMime);
			header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', $nomArchive) . '"');
			header('Content-Length: ' . @filesize($cheminArchive));
			@readfile($cheminArchive);
			@unlink($cheminArchive);
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
			header('Content-Length: ' . @filesize($chemin));
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
