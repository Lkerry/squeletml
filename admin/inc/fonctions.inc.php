<?php
/**
Traite une chaîne pour l'afficher sécuritairement à l'écran.
*/
function adminFormateTexte($texte)
{
	return stripslashes($texte);
}

/**
Retourne un tableau contenant les fichiers à inclure.
*/
function adminInit($racine)
{
	$fichiers = array ();
	
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	
	$fichiers[] = $racine . '/inc/fonctions.inc.php';
	
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	
	$fichiers[] = $racine . '/admin/inc/config.inc.php';
	
	$fichiers[] = $racine . '/admin/inc/pclzip/pclzip.lib.php';
	
	$fichiers[] = $racine . '/admin/inc/pcltar/lib/pclerror.lib.php3';
	$fichiers[] = $racine . '/admin/inc/pcltar/lib/pcltrace.lib.php3';
	$fichiers[] = $racine . '/admin/inc/pcltar/lib/pcltar.lib.php3';
	
	$fichiers[] = $racine . '/admin/inc/UnsharpMask.inc.php';
	
	if (file_exists($racine . '/site/inc/config-admin.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/config-admin.inc.php';
	}
	
	return $fichiers;
}

/**

*/
function adminParcourirDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres)
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
			adminParcourirDossiers($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres);
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
function adminParcourirTout($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl)
{
	static $liste = array ();
	$dossier = opendir($dossierRacine);
	while (($fichier = readdir($dossier)) !== FALSE)
	{
		if ($fichier != '.' && $fichier != '..' && is_dir($dossierRacine . '/' . $fichier))
		{
			adminParcourirTout($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres, $afficheDimensionsImages, $action, $symboleUrl);
		}

		elseif ($fichier != '.' && $fichier != '..')
		{
			if ($afficheDimensionsImages)
			{
				if (list($larg, $haut, $type, $attr) = @getimagesize("$dossierRacine/$fichier"))
				{
					$dim = " (${larg}&nbsp;x&nbsp;$haut)\n";
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
			
			if (empty($dim))
			{
				$lienModifier = "<a href=\"$action" . $symboleUrl . "action=modifier&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">" . T_("Modifier") . "</a>";
			}
			else
			{
				$lienModifier = 'Modifier';
			}
			
			$liste[$dossierRacine][] = "$lienModifier
				<span class='porteDocumentsSep'>|</span> <a href=\"$action" . $symboleUrl . "action=renommer&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">" . T_("Renommer") . "</a>
				<span class='porteDocumentsSep'>|</span> " . T_("Supprimer") . " <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$dossierRacine/$fichier\" />
				<span class='porteDocumentsSep'>|</span> <a href=\"$dossierRacine/$fichier\"><span class='porteDocumentsNom'>$fichier</span></a>$dim";
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

/**
Retourne la version de l'installation
*/
function adminVersionLogiciel($racine)
{
	$fic = fopen($racine . '/version.txt', 'r');
	$tag = fgets($fic, 20); // exemple: logiciel-1.4
	fclose($fic);
	$version = explode('-', $tag);
	
	return trim($version[1]);
}

/**
Retourne le nom de la page en cours
*/
function adminNomPageEnCours($url)
{
	return basename($url);
}
?>
