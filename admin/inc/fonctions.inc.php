<?php
/**
Traite une chaîne pour l'afficher sécuritairement à l'écran.
*/
function formateTexte($texte)
{
	return stripslashes($texte);
}

/**
Retourne un tableau contenant les fichiers à inclure.
*/
function init()
{
	$fichiers = array ();
	
	$fichiers[] = 'inc/config.inc.php';
	
	if (file_exists('../site/inc/config-admin.inc.php'))
	{
		$fichiers[] = '../site/inc/config-admin.inc.php';
	}
	
	return $fichiers;
}

/**
Conversion des octets en Mo.
*/
function octetsVersMo($octets)
{
	return $octets / 1000000;
}

/**

*/
function parcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres)
{
	static $liste = array ();
	$dossier = opendir($dossierRacine);
	while (($fichier = readdir($dossier)) !== FALSE)
	{
		if ($fichier != '.' && $fichier != '..' && is_dir($dossierRacine . '/' . $fichier))
		{
			if (!in_array($dossierRacine . '/' . $fichier, $liste))
			{
				$liste[] = $dossierRacine . '/' . $fichier;
			}
			parcourirDossiers($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres);
		}
	}

	closedir($dossier);

	if (!in_array($dossierRacine, $liste))
	{
		$liste[] = $dossierRacine;
	}

	if (!empty($tableauDossiersFiltres))
	{
		if ($typeFiltreDossiers == 'dossiersPermis')
		{
			$liste = array_intersect($liste, $tableauDossiersFiltres);
		}

		elseif ($typeFiltreDossiers == 'dossiersExclus')
		{
			$liste = array_diff($liste, $tableauDossiersFiltres);
		}
	}

	return $liste;
}

/**

*/
function parcourirTout($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl)
{
	static $liste = array ();
	$dossier = opendir($dossierRacine);
	while (($fichier = readdir($dossier)) !== FALSE)
	{
		if ($fichier != '.' && $fichier != '..' && is_dir($dossierRacine . '/' . $fichier))
		{
			parcourirTout($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
		}

		elseif ($fichier != '.' && $fichier != '..')
		{
			if ($afficheDimensionsImages)
			{
				if (list($larg, $haut, $type, $attr) = @getimagesize("$dossierRacine/$fichier"))
				{
					$dim = "(haut.: $haut, larg.: $larg)\n";
				}
				else
				{
					$dim = '';
				}
			}
			else
			{
				$dim = '';
			}

			$liste[$dossierRacine][] = "<a href=\"$action" . $symboleUrl . "action=modifier&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">Modifier</a> <span class='porteDocumentsSep'>|</span> Supprimer <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$dossierRacine/$fichier\" />
			<a href=\"$action" . $symboleUrl . "action=renommer&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">Renommer</a> <span class='porteDocumentsSep'>|</span>
			<span class='porteDocumentsSep'>|</span> <a href=\"$dossierRacine/$fichier\"><span class='porteDocumentsNom'>$fichier</span></a> $dim";
		}
	}

	closedir($dossier);

	if (!empty($tableauDossiersFiltres))
	{
		if ($typeFiltreDossiers == 'dossiersExclus')
		{
			foreach ($tableauDossiersFiltres as $valeur)
			{
				unset($liste[$valeur]);
			}
		}
	}

	return $liste;
}

?>
