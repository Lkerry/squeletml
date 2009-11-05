<?php
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
	
	if (file_exists($racine . '/site/inc/config-admin.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/config-admin.inc.php';
	}
	
	$fichiers[] = $racine . '/admin/inc/constantes.inc.php';
	
	$fichiers[] = $racine . '/admin/inc/pclzip/pclzip.lib.php';
	
	$fichiers[] = $racine . '/admin/inc/untar/untar.class.php';
	
	$fichiers[] = $racine . '/admin/inc/UnsharpMask.inc.php';
	
	$fichiers[] = $racine . '/inc/constantes.inc.php';
	
	return $fichiers;
}

/**
Retourne la valeur d'une variable du fichier de configuration `$racine/inc/config.inc.php`. le paramètre correspond au nom de la variable, par exemple 'rss' pour la variable `$rss`.
*/
function varConf($nomVariable)
{
	include dirname(__FILE__) . '/../../init.inc.php';
	include $racine . '/inc/config.inc.php';
	if (file_exists($racine . '/site/inc/config.inc.php'))
	{
		include $racine . '/site/inc/config.inc.php';
	}
	
	return ${$nomVariable};
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
		return varConf('galerieFluxRssGlobal');
	}
	elseif ($fluxRss == 'site')
	{
		return varConf('siteFluxRssGlobal');
	}
}

/**
Retourne la liste filtrée des dossiers contenus dans un emplacement fourni en paramètre. L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres)
{
	static $liste = array ();
	
	if ($dossier = @opendir($dossierRacine))
	{
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..' && is_dir($dossierRacine . '/' . $fichier))
			{
				if (!in_array($dossierRacine . '/' . $fichier, $liste))
				{
					$liste[] = $dossierRacine . '/' . $fichier;
				}
				adminListeDossiers($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres);
			}
		}
		
		closedir($dossier);
	}
	
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
Retourne la liste filtrée des fichiers contenus dans un emplacement fourni en paramètre et prête à être affichée dans le porte-documents (contient les liens d'action comme l'édition, la suppression, etc.). L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFichiersFormatee($urlRacine, $dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres, $action, $symboleUrl, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	static $liste = array ();
	
	if ($dossier = @opendir($dossierRacine))
	{
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..' && is_dir($dossierRacine . '/' . $fichier))
			{
				adminListeFichiersFormatee($urlRacine, $dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres, $action, $symboleUrl, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
			}
			elseif ($fichier != '.' && $fichier != '..')
			{
				$lienEditer = "<a href=\"$action" . $symboleUrl . "action=editer&amp;valeur=$dossierRacine/$fichier#messagesPorteDocuments\"><img src=\"$urlRacine/admin/fichiers/editer.png\" alt=\"" . T_("Éditer") . "\" title=\"" . T_("Éditer") . "\" width=\"16\" height=\"16\" /></a>";
			
				$fichierMisEnForme = '';
				$fichierMisEnForme .= "<a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$dossierRacine/$fichier#messagesPorteDocuments\"><img src=\"$urlRacine/admin/fichiers/copier.png\" alt=\"" . T_("Renommer/Déplacer") . "\" title=\"" . T_("Renommer/Déplacer") . "\" width=\"16\" height=\"16\" /></a>\n";
				$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
				$fichierMisEnForme .= "$lienEditer\n";
				$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
				$fichierMisEnForme .= "<img src=\"$urlRacine/admin/fichiers/supprimer.png\" alt=\"" . T_("Supprimer") . "\" title=\"" . T_("Supprimer") . "\" width=\"16\" height=\"16\" /> <input type=\"checkbox\" name=\"telechargerSuppr[]\" value=\"$dossierRacine/$fichier\" />\n";
				$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
				$fichierMisEnForme .= adminInfobulle($urlRacine, "$dossierRacine/$fichier", $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
				$fichierMisEnForme .= "<a class=\"porteDocumentsFichier\" href=\"$dossierRacine/$fichier\" title=\"" . sprintf(T_("Afficher «%1\$s»"), $fichier) . "\"><code>$fichier</code></a>\n";
			
				$liste[$dossierRacine][] = $fichierMisEnForme;
			}
		}
		
		closedir($dossier);
	}
	
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
Retourne le code pour l'infobulle contenant les propriétés d'un fichier dans le porte-documents.
*/
function adminInfobulle($urlRacine, $cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	$infobulle = '';
	$fichier = basename($cheminFichier);
	
	if (is_dir($cheminFichier))
	{
		$typeMime = T_("dossier");
	}
	else
	{
		$typeMime = mimedetect_mime(array ('filepath' => $cheminFichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	}
	
	$stat = stat($cheminFichier);
	
	if (list($larg, $haut, $type, $attr) = @getimagesize($cheminFichier))
	{
		$dim = "$larg px × $haut px";
	}
	else
	{
		$dim = '';
	}
	
	$infobulle .= "<a class=\"porteDocumentsProprietesFichier\" href=\"#\"><img src=\"$urlRacine/admin/fichiers/proprietes.png\" alt=\"" . T_("Propriétés") . "\" width=\"16\" height=\"16\" /><span>";
	$infobulle .= T_("<strong>Type MIME:</strong>") . ' ' . $typeMime . "<br />\n";
	
	if ($stat)
	{
		$infobulle .= sprintf(T_("<strong>Taille:</strong> %1\$s Kio (%2\$s octets)"), octetsVersKio($stat['size']), $stat['size']) . "<br />\n";
		
		if (!empty($dim))
		{
			$infobulle .= T_("<strong>Dimensions:</strong>") . ' ' . $dim . "<br />\n";
		}
		
		$infobulle .= T_("<strong>Dernier accès:</strong>") . ' ' . date('Y-m-d H:i:s T', $stat['atime']) . "<br />\n";
		$infobulle .= T_("<strong>Dernière modification:</strong>") . ' ' . date('Y-m-d H:i:s T', $stat['mtime']) . "<br />\n";
		
		if ($stat['uid'] != 0)
		{
			$infobulle .= T_("<strong>uid:</strong>") . ' ' . $stat['uid'] . "<br />\n";
		}
		
		if ($stat['gid'] != 0)
		{
			$infobulle .= T_("<strong>gid:</strong>") . ' ' . $stat['gid'] . "<br />\n";
		}
	}
	
	$infobulle .= T_("<strong>Permissions:</strong>") . ' ' . substr(sprintf('%o', fileperms($cheminFichier)), -4);
	$infobulle .= "</span></a>\n";
	
	return $infobulle;
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
	
	$fic = @opendir($cheminGalerie);
	if ($fic === FALSE)
	{
		return FALSE;
	}
	
	$listeNouveauxFichiers = array ();
	while($fichier = @readdir($fic))
	{
		if(!is_dir($cheminGalerie . '/' . $fichier))
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
Si une erreur survient, retourne FALSE, sinon retourne un tableau dont chaque élément contient le nom d'une galerie. Si le paramètre `$strictementAvecConfig` vaut TRUE, retourne seulement les galeries ayant un fichier de configuration `config.pc`, sinon retourne le nom de tous les dossiers de `$racine/site/fichiers/galeries/`.
*/
function adminListeGaleries($racine, $strictementAvecConfig = TRUE)
{
	if ($fic = @opendir($racine . '/site/fichiers/galeries'))
	{
		$galeries = array ();
		
		while($fichier = @readdir($fic))
		{
			if(is_dir($racine . '/site/fichiers/galeries/' . $fichier) && $fichier != '.' && $fichier != '..')
			{
				if (($strictementAvecConfig && file_exists($racine . '/site/fichiers/galeries/' . $fichier . '/config.pc')) || !$strictementAvecConfig)
				{
					$galeries[] = sansEchappement($fichier);
				}
			}
		}
		
		closedir($fic);
	}
	
	if (isset($galeries))
	{
		return $galeries;
	}
	else
	{
		return FALSE;
	}
}

/**
Retourne l'id de `body`.
*/
function adminBodyId()
{
	return str_replace('.', '-', page());
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
		$ip = securiseTexte($_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	elseif (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = securiseTexte($_SERVER['HTTP_CLIENT_IP']);
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
		$ip = securiseTexte($_SERVER['REMOTE_ADDR']);
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

/**
Retourne TRUE si le dossier est vide, sinon retourne FALSE.
*/
function dossierEstVide($cheminDossier)
{
	$dossierEstVide = FALSE;
	$i = 0;
	
	if (is_dir($cheminDossier) && $fic = @opendir($cheminDossier))
	{
		while ($fichier = @readdir($fic))
		{
			if ($fichier != '.' && $fichier != '..')
			{
				$i++;
				break;
			}
		}
		
		closedir($fic);
		
		if ($i == 0)
		{
			$dossierEstVide = TRUE;
		}
	}
	
	return $dossierEstVide;
}

?>
