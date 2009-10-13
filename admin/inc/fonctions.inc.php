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
	
	$fichiers[] = $racine . '/admin/inc/constantes.inc.php';
	
	$fichiers[] = $racine . '/admin/inc/pclzip/pclzip.lib.php';
	
	$fichiers[] = $racine . '/admin/inc/untar/untar.class.php';
	
	$fichiers[] = $racine . '/admin/inc/UnsharpMask.inc.php';
	
	if (file_exists($racine . '/site/inc/config-admin.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/config-admin.inc.php';
	}
	
	return $fichiers;
}

/**
Retourne la valeur des variables `$galerieFluxRssGlobal` ou `$siteFluxRssGlobal`.
*/
function adminFluxRssGlobal($fluxRss, $racine)
{
	include dirname(__FILE__) . '/../../init.inc.php';
	include $racine . '/inc/config.inc.php';
	if (file_exists($racine . '/site/inc/config.inc.php'))
	{
		include $racine . '/site/inc/config.inc.php';
	}
	
	if ($fluxRss == 'galerie')
	{
		return $galerieFluxRssGlobal;
	}
	elseif ($fluxRss == 'site')
	{
		return $siteFluxRssGlobal;
	}
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
				$lienEditer = "<a href=\"$action" . $symboleUrl . "action=editer&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">" . T_("Éditer") . "</a>";
			}
			else
			{
				$lienEditer = T_("Éditer");
			}
			
			$liste[$dossierRacine][] = "<a href=\"$action" . $symboleUrl . "action=renommer&valeur=$dossierRacine/$fichier#messagesPorteDocuments\">" . T_("Renommer/Déplacer") . "</a>
				<span class='porteDocumentsSep'>|</span> $lienEditer
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

/**
Retourne la valeur en octets des tailles paraissant dans le `php.ini`. Ex.: 2M => 2097152. Voir <http://ca.php.net/manual/fr/ini.core.php#79564>
*/
function adminPhpIniOctets($nombre)
{
	$lettre = substr($nombre, -1);
	$octets = substr($nombre, 0, -1);
	switch (strtoupper($lettre))
	{
		case 'P':
			$octets *= 1024;
		case 'T':
			$octets *= 1024;
		case 'G':
			$octets *= 1024;
		case 'M':
			$octets *= 1024;
		case 'K':
			$octets *= 1024;
			break;
	}
	
	return $octets;
}

/**
Met à jour un fichier de configuration de galerie.
Retourne TRUE s'il n'y a aucune erreur, sinon retourne FALSE.
*/
function adminMajConfGalerie($racine, $id, $listeAjouts)
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
	$fichierConfigChemin = $racine . '/site/fichiers/galeries/' . $id . '/config.pc';
	if (!empty($listeAjouts))
	{
		$listeExistant = file_get_contents($fichierConfigChemin);
		if ($listeExistant === FALSE)
		{
			return FALSE;
		}
		
		if (file_put_contents($fichierConfigChemin, $listeAjouts . $listeExistant) === FALSE)
		{
			return FALSE;
		}
	}
	
	$galerie = tableauGalerie($fichierConfigChemin);
	$galerieTemp = array ();
	$i = 0;

	foreach ($galerie as $oeuvre)
	{
		foreach ($oeuvre as $cle => $valeur)
		{
			if ($cle == 'intermediaireNom')
			{
				if (!empty($valeur) && file_exists($cheminGalerie . '/' . $valeur) && !in_array_multi($valeur, $galerieTemp))
				{
					$galerieTemp[$i][$cle] = $valeur;
				}
				else
				{
					continue; // On sort de cette oeuvre sans la prendre en note, car elle n'existe plus
				}
			}
			elseif ($cle == 'vignetteNom')
			{
				if (!empty($valeur) && file_exists($cheminGalerie . '/' . $valeur))
				{
					$galerieTemp[$i][$cle] = $valeur;
				}
			}
			elseif ($cle == 'originalNom')
			{
				if (!empty($valeur) && file_exists($cheminGalerie . '/' . $valeur))
				{
					$galerieTemp[$i][$cle] = $valeur;
				}
			}
			else
			{
				if (!empty($valeur))
				{
					$galerieTemp[$i][$cle] = $valeur;
				}
			}
		}
		
		$i++;
	}
	
	$fic = opendir($cheminGalerie);
	if ($fic === FALSE)
	{
		return FALSE;
	}
	
	$listeNouveauxFichiers = array ();
	while($fichier = @readdir($fic))
	{
		if(!is_dir($cheminGalerie . '/' . $fichier) && $fichier != '.' && $fichier != '..')
		{
			if (!preg_match('/-vignette\.[[:alpha:]]{3,4}$/', $fichier) &&
				!preg_match('/-original\.[[:alpha:]]{3,4}$/', $fichier) &&
				preg_match('/\.(gif|png|jpeg|jpg)$/i', $fichier) &&
				!in_array_multi($fichier, $galerieTemp) &&
				!in_array_multi($fichier, $listeNouveauxFichiers))
			{
				$listeNouveauxFichiers[] = $fichier;
			}
		}
	}
	closedir($fic);
	
	rsort($listeNouveauxFichiers);
	foreach ($listeNouveauxFichiers as $valeur)
	{
		array_unshift($galerieTemp, array ('intermediaireNom' => $valeur));
	}
	unset($listeNouveauxFichiers);
	
	$contenuConfig = '';
	foreach ($galerieTemp as $oeuvre)
	{
		foreach ($oeuvre as $cle => $valeur)
		{
			$contenuConfig .= "$cle=$valeur\n";
		}
		$contenuConfig .= "#IMG\n";
	}
	
	$contenuConfig = rtrim($contenuConfig);
	
	if (file_put_contents($fichierConfigChemin, $contenuConfig) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/**
Retourne l'id de `body`.
*/
function adminBodyId()
{
	return str_replace('.', '-', basename($_SERVER['SCRIPT_NAME']));
}

/**
Retourne TRUE si le navigateur de l'internaute est Internet Explorer, sinon retourne FALSE.
*/
function adminEstIE()
{
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/**
Retourne l'IP de l'internaute si elle a été trouvée, sinon retourne FALSE.
*/
function ipInternaute()
{
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	else
	{
		$ip = FALSE;
	}
	
	return $ip;
}

/**
Si `$retourneMessage` vaut TRUE, retourne un message informant si la réécriture d'URL est activée ou non, sinon retourne un caractère.
*/
function reecritureDurl($retourneMessage)
{
	if (function_exists('apache_get_modules'))
	{
		if (in_array("mod_rewrite", apache_get_modules()))
		{
			$caractere = 'o';
			$message = T_("La réécriture d'URL est activée.");
		}
		else
		{
			$caractere = 'n';
			$message = T_("La réécriture d'URL n'est pas activée.");
		}
	}
	else
	{
		$caractere = '?';
		$message = T_("Impossible de savoir si la réécriture d'URL est activée.");
	}
	
	if ($retourneMessage)
	{
		return $message;
	}
	else
	{
		return $caractere;
	}
}

/**
Retourne TRUE si le site est en maintenance, sinon retourne FALSE.
*/
function adminSiteEnMaintenance($cheminHtaccess)
{
	if ($fic = fopen($cheminHtaccess, 'r'))
	{
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			if (preg_match('/^# Ajout automatique de Squeletml \(maintenance\). Ne pas modifier./', $ligne))
			{
				return TRUE;
			}
		}
		fclose($fic);
	}
	
	return FALSE;
}

/**
Retourne l'IP ayant accès au site en maintenance, si elle existe, sinon retourne FALSE.
*/
function adminSiteEnMaintenanceIp($cheminHtaccess)
{
	if ($fic = fopen($cheminHtaccess, 'r'))
	{
		while (!feof($fic))
		{
			$ligne = rtrim(fgets($fic));
			if (preg_match('/^\tRewriteCond %{REMOTE_ADDR} !\^(([0-9]{1,4}\\\.){3}[0-9]{1,4})/', $ligne, $resultat))
			{
				return str_replace('\\', '', $resultat[1]);
			}
		}
		fclose($fic);
	}
	
	return FALSE;
}

?>
