<?php
/**
Retourne TRUE si le type MIME passé en paramètre est permis, sinon retourne FALSE.
*/
function adminTypeMimePermis($typeMime, $filtreTypesMime, $typesMimePermis)
{
	if ($filtreTypesMime && array_search($typeMime, $typesMimePermis) === FALSE)
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}

/**
Retourne un tableau contenant les fichiers à inclure.
*/
function adminInit($racineAdmin)
{
	$racine = dirname($racineAdmin);
	
	$fichiers = array ();
	
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	
	$fichiers[] = $racine . '/inc/fonctions.inc.php';
	
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	
	$fichiers[] = $racineAdmin . '/inc/config.inc.php';
	
	if (file_exists($racine . '/site/inc/config-admin.inc.php'))
	{
		$fichiers[] = $racine . '/site/inc/config-admin.inc.php';
	}
	
	$fichiers[] = $racineAdmin . '/inc/constantes.inc.php';
	
	$fichiers[] = $racineAdmin . '/inc/pclzip/pclzip.lib.php';
	
	$fichiers[] = $racineAdmin . '/inc/tar/tar.class.php';
	
	$fichiers[] = $racineAdmin . '/inc/untar/untar.class.php';
	
	$fichiers[] = $racineAdmin . '/inc/UnsharpMask.inc.php';
	
	$fichiers[] = $racine . '/inc/constantes.inc.php';
	
	$fichiers[] = $racine . '/inc/mimedetect/file.inc.php';
	
	$fichiers[] = $racine . '/inc/mimedetect/mimedetect.inc.php';
	
	return $fichiers;
}

/**
Retourne la valeur d'une variable du fichier de configuration du site (`$racine/inc/config.inc.php` | `$racine/site/inc/config.inc.php`) si `$configAdmin` vaut FALSE, sinon retourne la valeur d'une variable du fichier de configuration de l'administration (`$racineAdmin/inc/config.inc.php` | `$racine/site/inc/config-admin.inc.php`). Si la variable demandée n'est pas définie ou vaut NULL, retourne FALSE. Le deuxième paramètre correspond au nom de la variable, par exemple 'rss' pour la variable `$rss`.
*/
function adminVarConf($racineAdmin, $nomVariable, $configAdmin = FALSE)
{
	$racine = dirname($racineAdmin);
	
	include $racine . '/init.inc.php';
	
	if ($configAdmin)
	{
		include $racineAdmin . '/inc/config.inc.php';
		if (file_exists($racine . '/site/inc/config-admin.inc.php'))
		{
			include $racine . '/site/inc/config-admin.inc.php';
		}
	}
	else
	{
		include $racine . '/inc/config.inc.php';
		if (file_exists($racine . '/site/inc/config.inc.php'))
		{
			include $racine . '/site/inc/config.inc.php';
		}
	}
	
	return isset(${$nomVariable}) ? ${$nomVariable} : FALSE;
}

/**
Retourne la valeur des variables `$galerieFluxRssGlobal` ou `$siteFluxRssGlobal`.
*/
function adminFluxRssGlobal($racineAdmin, $fluxRss)
{
	$racine = dirname($racineAdmin);
	
	include $racine . '/init.inc.php';
	include $racine . '/inc/config.inc.php';
	if (file_exists($racine . '/site/inc/config.inc.php'))
	{
		include $racine . '/site/inc/config.inc.php';
	}
	
	if ($fluxRss == 'galerie')
	{
		return adminVarConf($racineAdmin, 'galerieFluxRssGlobal');
	}
	elseif ($fluxRss == 'site')
	{
		return adminVarConf($racineAdmin, 'siteFluxRssGlobal');
	}
}

/**
Retourne sous forme de tableau la liste des dossiers et fichiers contenus dans un emplacement fourni en paramètre. L'analyse est récursive. Les dossiers ou fichiers impossibles d'accès sont pas retournés.
*/
function adminListeFichiers($dossier)
{
	static $liste = array ();
	
	if (is_dir($dossier) && $fic = @opendir($dossier))
	{
		$liste[] = $dossier;
		
		while (($fichier = @readdir($fic)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..')
			{
				if (is_dir($dossier . '/' . $fichier))
				{
					adminListeFichiers($dossier . '/' . $fichier);
				}
				else
				{
					$liste[] = $dossier . '/' . $fichier;
				}
			}
		}
		
		closedir($fic);
	}
	
	natcasesort($liste);
	
	return $liste;
}

/**
Retourne la liste filtrée des dossiers contenus dans un emplacement fourni en paramètre. L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFiltreeDossiers($dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres)
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
				adminListeFiltreeDossiers($dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres);
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
	
	natcasesort($liste);
	
	return $liste;
}

/**
Retourne la liste filtrée des fichiers contenus dans un emplacement fourni en paramètre et prête à être affichée dans le porte-documents (contient les liens d'action comme l'édition, la suppression, etc.). L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $dossierRacine, $typeFiltreDossiers, $tableauDossiersFiltres, $action, $symboleUrl, $dossierCourant, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance, $porteDocumentsDroits)
{
	$racine = dirname($racineAdmin);
	static $liste = array ();
	
	if ($dossier = @opendir($dossierRacine))
	{
		if (!empty($dossierCourant))
		{
			$dossierCourantDansUrl = "&amp;dossierCourant=$dossierCourant";
		}
		else
		{
			$dossierCourantDansUrl = '';
		}
		
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..')
			{
				if (is_dir($dossierRacine . '/' . $fichier))
				{
					if (adminDossierEstVide($dossierRacine . '/' . $fichier))
					{
						$liste[$dossierRacine . '/' . $fichier][] = T_("Vide.");
					}
					else
					{
						adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $dossierRacine . '/' . $fichier, $typeFiltreDossiers, $tableauDossiersFiltres, $action, $symboleUrl, $dossierCourant, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance, $porteDocumentsDroits);
					}
				}
				else
				{
					$fichierMisEnForme = '';
				
					if ($porteDocumentsDroits['copier'] && $porteDocumentsDroits['deplacer'] && $porteDocumentsDroits['permissions'] && $porteDocumentsDroits['supprimer'])
					{
						$fichierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsFichiers[]\" value=\"$dossierRacine/$fichier\" />\n";
				
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($porteDocumentsDroits['telecharger'])
					{
						$fichierMisEnForme .= "<a href=\"$urlRacineAdmin/telecharger.admin.php?fichier=$dossierRacine/$fichier\"><img src=\"$urlRacineAdmin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
				
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($porteDocumentsDroits['editer'])
					{
						$fichierMisEnForme .= "<a href=\"$action" . $symboleUrl . "action=editer&amp;valeur=$dossierRacine/$fichier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacineAdmin/fichiers/editer.png\" alt=\"" . T_("Éditer") . "\" title=\"" . T_("Éditer") . "\" width=\"16\" height=\"16\" /></a>\n";
					
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($porteDocumentsDroits['renommer'])
					{
						$fichierMisEnForme .= "<a href=\"$action" . $symboleUrl . "action=renommer&amp;valeur=$dossierRacine/$fichier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacineAdmin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
				
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					$fichierMisEnForme .= adminInfobulle($racineAdmin, $urlRacineAdmin, "$dossierRacine/$fichier", $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				
					$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
				
					$fichierMisEnForme .= "<a class=\"porteDocumentsFichier\" href=\"$dossierRacine/$fichier\" title=\"" . sprintf(T_("Afficher «%1\$s»"), $fichier) . "\"><code>$fichier</code></a>\n";
			
					$liste[$dossierRacine][] = $fichierMisEnForme;
				}
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
	
	ksort($liste);
	
	return $liste;
}

/**
Retourne le code pour l'infobulle contenant les propriétés d'un fichier dans le porte-documents.
*/
function adminInfobulle($racineAdmin, $urlRacineAdmin, $cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	clearstatcache();
	
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
	
	if ($typeMime == 'image/gif' || $typeMime == 'image/jpeg' || $typeMime == 'image/png')
	{
		list ($larg, $haut, $type, $attr) = getimagesize($cheminFichier);
		$dimensionsImage = "$larg px × $haut px";
		
		// S'il n'existe pas déjà, l'aperçu est enregistré dans le dossier de cache de l'administration. On vérifie toutefois avant si on doit vider le cache (taille limite atteinte de 2 Mio).
		
		if (adminTailleCache($racineAdmin) > 2097152)
		{
			adminVideCache($racineAdmin);
		}
		
		$cheminApercuImage = $racineAdmin . '/cache/' . md5($cheminFichier) . '.' . array_pop(explode('.', $cheminFichier));;
		
		if (!file_exists($cheminApercuImage))
		{
			nouvelleImage($cheminFichier, $cheminApercuImage, array ('largeur' => 50, 'hauteur' => 50), '85', FALSE, TRUE, $typeMime);
		}
		
		if (file_exists($cheminApercuImage))
		{
			list ($larg, $haut, $type, $attr) = getimagesize($cheminApercuImage);
			$apercuImage = "<img class=\"infobulleApercuImage\" src=\"" . $urlRacineAdmin . "/cache/" . basename($cheminApercuImage) . "\" width=\"$larg\" height=\"$haut\" alt=\"" . sprintf(T_("Aperçu de l'image %1\$s"), $fichier) . "\" />";
		}
		else
		{
			$apercuImage = FALSE;
		}
	}
	else
	{
		$dimensionsImage = FALSE;
	}
	
	$infobulle .= "<a class=\"porteDocumentsProprietesFichier\" href=\"#\"><img src=\"$urlRacineAdmin/fichiers/proprietes.png\" alt=\"" . T_("Propriétés") . "\" width=\"16\" height=\"16\" /><span>";
	$infobulle .= sprintf(T_("<strong>Type MIME:</strong> %1\$s"), $typeMime) . "<br />\n";
	
	if ($stat)
	{
		$infobulle .= sprintf(T_("<strong>Taille:</strong> %1\$s Kio (%2\$s octets)"), octetsVersKio($stat['size']), $stat['size']) . "<br />\n";
		
		if ($dimensionsImage)
		{
			$infobulle .= sprintf(T_("<strong>Dimensions:</strong> %1\$s"), $dimensionsImage) . "<br />\n";
			
			if ($apercuImage)
			{
				$infobulle .= sprintf(T_("<strong>Aperçu:</strong> %1\$s"), $apercuImage) . "<br />\n";
			}
		}
		
		$infobulle .= sprintf(T_("<strong>Dernier accès:</strong> %1\$s"), date('Y-m-d H:i:s T', $stat['atime'])) . "<br />\n";
		$infobulle .= sprintf(T_("<strong>Dernière modification:</strong> %1\$s"), date('Y-m-d H:i:s T', $stat['mtime'])) . "<br />\n";
		
		if ($stat['uid'] != 0)
		{
			$infobulle .= sprintf(T_("<strong>uid:</strong> %1\$s"), $stat['uid']) . "<br />\n";
		}
		
		if ($stat['gid'] != 0)
		{
			$infobulle .= sprintf(T_("<strong>gid:</strong> %1\$s"), $stat['gid']) . "<br />\n";
		}
	}
	
	$infobulle .= sprintf(T_("<strong>Permissions:</strong> %1\$s"), adminPermissionsFichier($cheminFichier));
	$infobulle .= "</span></a>\n";
	
	return $infobulle;
}

/**
Retourne la taille en octets du dossier de cache de l'administration si le dossier est accessible, sinon retourne FALSE.
*/
function adminTailleCache($racineAdmin)
{
	$cheminCache = $racineAdmin . '/cache';
	$taille = 0;
	
	if ($dossier = @opendir($cheminCache))
	{
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if (!is_dir($cheminCache . '/' . $fichier))
			{
				$taille += filesize($cheminCache . '/' . $fichier);
			}
		}
		
		closedir($dossier);
	}
	else
	{
		return FALSE;
	}
	
	return $taille;
}

/**
Vide le cache. Seuls les fichiers à la racine du dossier sont supprimés, ce qui constitue normalement tout ce qui est mis en cache par Squeletml. Retourne FALSE si une erreur survient, sinon retourne TRUE.
*/
function adminVideCache($racineAdmin)
{
	$cheminCache = $racineAdmin . '/cache';
	$sansErreur = TRUE;
	
	if ($dossier = @opendir($cheminCache))
	{
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if (!is_dir($cheminCache . '/' . $fichier))
			{
				if (!@unlink($cheminCache . '/' . $fichier))
				{
					$sansErreur = FALSE;
				}
			}
		}
		
		closedir($dossier);
	}
	else
	{
		$sansErreur = FALSE;
	}
	
	return $sansErreur;
}

/**
Retourne les permissions d'un fichier. La valeur retournée est en notation octale sur trois chiffres.
*/
function adminPermissionsFichier($cheminFichier)
{
	clearstatcache();
	
	$permissions = substr(decoct(fileperms($cheminFichier)), 2);
	
	if (strlen($permissions) > 3)
	{
		$permissions = substr($permissions, -3, 3);
	}
	
	return $permissions;
}

/**
Retourne la version de l'installation
*/
function adminVersionLogiciel($racine)
{
	$fic = @fopen($racine . '/version.txt', 'r');
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
Retourne le chemin vers le fichier de configuration du flux RSS global des galeries. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function adminCheminConfigFluxRssGlobalGaleries($racine, $retourneCheminParDefaut = FALSE)
{
	if (file_exists($racine . '/site/inc/rss-global-galeries.ini.txt'))
	{
		return $racine . '/site/inc/rss-global-galeries.ini.txt';
	}
	elseif (file_exists($racine . '/site/inc/rss-global-galeries.ini'))
	{
		return $racine . '/site/inc/rss-global-galeries.ini';
	}
	elseif ($retourneCheminParDefaut)
	{
		return $racine . '/site/inc/rss-global-galeries.ini.txt';
	}
	else
	{
		return FALSE;
	}
}

/**
Retourne le chemin vers le fichier de configuration du flux RSS global des pages autres que les galeries. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function adminCheminConfigFluxRssGlobalSite($racine, $retourneCheminParDefaut = FALSE)
{
	if (file_exists($racine . '/site/inc/rss-global-site.ini.txt'))
	{
		return $racine . '/site/inc/rss-global-site.ini.txt';
	}
	elseif (file_exists($racine . '/site/inc/rss-global-site.ini'))
	{
		return $racine . '/site/inc/rss-global-site.ini';
	}
	elseif ($retourneCheminParDefaut)
	{
		return $racine . '/site/inc/rss-global-site.ini.txt';
	}
	else
	{
		return FALSE;
	}
}

/**
Retourne le chemin vers le fichier de configuration d'une galerie. Si aucun fichier de configuration n'a été trouvé, retourne FALSE si `$retourneCheminParDefaut` vaut FALSE, sinon retourne le chemin par défaut du fichier de configuration.
*/
function adminCheminConfigGalerie($racine, $idGalerie, $retourneCheminParDefaut = FALSE)
{
	if ($idGalerie && file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt';
	}
	elseif ($idGalerie && file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini'))
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini';
	}
	elseif ($retourneCheminParDefaut)
	{
		return $racine . '/site/fichiers/galeries/' . $idGalerie . '/config.ini.txt';
	}
	else
	{
		return FALSE;
	}
}

/**
Met à jour un fichier de configuration de galerie. Retourne TRUE s'il n'y a aucune erreur, sinon retourne FALSE.
*/
function adminMajConfigGalerie($racine, $id, $listeAjouts, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
	$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id);
	
	if (!empty($listeAjouts))
	{
		if ($cheminConfigGalerie)
		{
			$listeExistant = file_get_contents($cheminConfigGalerie);
			
			if ($listeExistant === FALSE)
			{
				return FALSE;
			}
		}
		else
		{
			$listeExistant = '';
			$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id, TRUE);
		}
		
		if (@file_put_contents($cheminConfigGalerie, $listeAjouts . $listeExistant) === FALSE)
		{
			return FALSE;
		}
	}
	
	$galerieTemp = array ();
	
	if ($cheminConfigGalerie)
	{
		$galerie = tableauGalerie($cheminConfigGalerie);
		$i = 0;

		foreach ($galerie as $oeuvre)
		{
			foreach ($oeuvre as $cle => $valeur)
			{
				if ($cle == 'intermediaireNom')
				{
					if (!empty($valeur) && file_exists($cheminGalerie . '/' . $valeur) && !adminImageEstDeclaree($valeur, $galerieTemp, 'intermediaire'))
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
				elseif (!empty($valeur))
				{
					$galerieTemp[$i][$cle] = $valeur;
				}
			}
		
			$i++;
		}
	}
	
	$listeNouveauxFichiers = array ();
	
	if ($fic = @opendir($cheminGalerie))
	{
		while($fichier = @readdir($fic))
		{
			if(!is_dir($cheminGalerie . '/' . $fichier))
			{
				$typeMime = mimedetect_mime(array ('filepath' => $cheminGalerie . '/' . $fichier, 'filename' => $fichier), $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				
				$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime);
				
				if (
					adminImageValide($typeMime) &&
					$versionImage != 'vignette' &&
					$versionImage != 'original' &&
					!adminImageEstDeclaree($fichier, $galerieTemp, 'intermediaire') &&
					!adminImageEstDeclaree($fichier, $listeNouveauxFichiers, 'intermediaire')
				)
				{
					$listeNouveauxFichiers[] = $fichier;
				}
			}
		}
		
		closedir($fic);
	}
	else
	{
		return FALSE;
	}
	
	natcasesort($listeNouveauxFichiers);
	$listeNouveauxFichiers = array_reverse($listeNouveauxFichiers);
	foreach ($listeNouveauxFichiers as $valeur)
	{
		array_unshift($galerieTemp, array ('intermediaireNom' => $valeur));
	}
	unset($listeNouveauxFichiers);
	
	$contenuConfig = '';
	foreach ($galerieTemp as $oeuvre)
	{
		$contenuConfigTemp = '';
		foreach ($oeuvre as $cle => $valeur)
		{
			if ($cle == 'intermediaireNom')
			{
				$contenuConfig .= "[$valeur]\n";
			}
			else
			{
				$contenuConfigTemp .= "$cle=$valeur\n";
			}
		}
		$contenuConfig .= "$contenuConfigTemp\n";
	}
	
	$contenuConfig = rtrim($contenuConfig);
	
	if (!$cheminConfigGalerie)
	{
		$cheminConfigGalerie = adminCheminConfigGalerie($racine, $id, TRUE);
	}
	
	if (@file_put_contents($cheminConfigGalerie, $contenuConfig) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/**
Retourne TRUE si l'image est affichable par une galerie de Squeletml, sinon retourne FALSE.
*/
function adminImageValide($typeMime)
{
	if ($typeMime == 'image/gif' || $typeMime == 'image/jpeg' || $typeMime == 'image/png')
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/**
Retourne TRUE si l'image est déclarée dans le fichier de configuration, sinon retourne FALSE.
*/
function adminImageEstDeclaree($fichier, $galerie, $versionAchercher = FALSE)
{
	if ($galerie)
	{
		foreach ($galerie as $oeuvre)
		{
			if ((!$versionAchercher || $versionAchercher = 'intermediaire') && (isset($oeuvre['intermediaireNom']) && $oeuvre['intermediaireNom'] == $fichier))
			{
				return TRUE;
			}
			elseif ((!$versionAchercher || $versionAchercher = 'vignette') && (isset($oeuvre['vignetteNom']) && $oeuvre['vignetteNom'] == $fichier))
			{
				return TRUE;
			}
			elseif ((!$versionAchercher || $versionAchercher = 'original') && (isset($oeuvre['originalNom']) && $oeuvre['originalNom'] == $fichier))
			{
				return TRUE;
			}
		}
	}
	
	return FALSE;
}

/**
Retourne la version de l'image (intermediaire|vignette|original|inconnu).
*/
function adminVersionImage($racine, $image, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime)
{
	$nomImage = basename($image);
	
	if ($analyserConfig)
	{
		$cheminConfigGalerie = adminCheminConfigGalerie($racine, basename(dirname($image)));
		
		if ($cheminConfigGalerie)
		{
			$galerie = tableauGalerie($cheminConfigGalerie);
			
			foreach ($galerie as $oeuvre)
			{
				if ($oeuvre['intermediaireNom'] == $nomImage)
				{
					return 'intermediaire';
				}
				elseif (isset($oeuvre['vignetteNom']) && $oeuvre['vignetteNom'] == $nomImage)
				{
					return 'vignette';
				}
				elseif (isset($oeuvre['originalNom']) && $oeuvre['originalNom'] == $nomImage)
				{
					return 'original';
				}
				elseif (preg_match('/(.+)-vignette(\.[^\.]+)$/', $nomImage, $nomConstruit))
				{
					$nomConstruitIntermediaire = $nomConstruit[1] . $nomConstruit[2];
					if ($oeuvre['intermediaireNom'] == $nomConstruitIntermediaire)
					{
						return 'vignette';
					}
				}
				elseif (preg_match('/(.+)-original(\.[^\.]+)$/', $nomImage, $nomConstruit))
				{
					$nomConstruitIntermediaire = $nomConstruit[1] . $nomConstruit[2];
					if ($oeuvre['intermediaireNom'] == $nomConstruitIntermediaire)
					{
						return 'original';
					}
				}
			}
		}
	}
	
	if ($analyserConfig && $analyserSeulementConfig)
	{
		return 'inconnu';
	}
	elseif (preg_match('/-vignette\.[^\.]+$/', $nomImage) && adminImageValide($typeMime))
	{
		if ($exclureMotifsCommeIntermediaires)
		{
			return 'vignette';
		}
		else
		{
			return 'intermediaire';
		}
	}
	elseif (preg_match('/-original\.[^\.]+$/', $nomImage))
	{
		if ($exclureMotifsCommeIntermediaires)
		{
			return 'original';
		}
		elseif (adminImageValide($typeMime))
		{
			return 'intermediaire';
		}
		else
		{
			return 'inconnu';
		}
	}
	elseif (adminImageValide($typeMime))
	{
		return 'intermediaire';
	}
	else
	{
		return 'inconnu';
	}
}

/**
Si une erreur survient, retourne FALSE, sinon retourne un tableau dont chaque élément contient le nom d'une galerie. Si le paramètre `$strictementAvecConfig` vaut TRUE, retourne seulement les galeries ayant un fichier de configuration, sinon retourne le nom de tous les dossiers de `$racine/site/fichiers/galeries/`.
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
				if (($strictementAvecConfig && adminCheminConfigGalerie($racine, $fichier)) || !$strictementAvecConfig)
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
Retourne les messages à afficher dans une chaîne formatée. Si le titre est vide, ne retourne qu'une liste de messages, sinon retourne une division de classe `sousBoite` contenant un titre de troisième niveau et la liste des messages.
*/
function adminMessagesScript($messagesScript, $titre = '')
{
	$messagesScriptChaine = '';
	
	if (!empty($titre))
	{
		$messagesScriptChaine .= '<div class="sousBoite">' . "\n";
		$messagesScriptChaine .= "<h3>$titre</h3>\n";
	}
	
	if (!empty($messagesScript))
	{
		$messagesScriptChaine .= "<ul>\n";
		
		foreach ($messagesScript as $messageScript)
		{
			$messagesScriptChaine .= $messageScript;
		}
		
		$messagesScriptChaine .= "</ul>\n";
	}
	
	if (!empty($titre))
	{
		$messagesScriptChaine .= "</div><!-- /class=sousBoite -->\n";
	}
	
	return $messagesScriptChaine;
}

/**
Copie un dossier dans un autre et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminCopyDossier($dossierSource, $dossierDeDestination)
{
	$messagesScriptChaine = '';
	
	if (!file_exists($dossierDeDestination))
	{
		$messagesScriptChaine .= adminMkdir($dossierDeDestination, octdec(adminPermissionsFichier($dossierSource)), TRUE);
	}
	
	if (file_exists($dossierDeDestination))
	{
		if ($dossier = @opendir($dossierSource))
		{
			while (($fichier = @readdir($dossier)) !== FALSE)
			{
				if ($fichier != '.' && $fichier != '..')
				{
					if (is_dir($dossierSource . '/' . $fichier))
					{
						$messagesScriptChaine .= adminCopyDossier($dossierSource . '/' . $fichier, $dossierDeDestination . '/' . $fichier);
					}
					else
					{
						$messagesScriptChaine .= adminCopy($dossierSource . '/' . $fichier, $dossierDeDestination . '/' . $fichier);
					}
				}
			}
		
			closedir($dossier);
		}
		else
		{
			$messagesScriptChaine .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), "<code>$dossierSource</code>") . "</li>\n";
		}
	}
	
	return $messagesScriptChaine;
}

/**
Supprime un dossier ainsi que son contenu et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminRmdirRecursif($dossierAsupprimer)
{
	$messagesScriptChaine = '';
	
	if (basename($dossierAsupprimer) != '.' && basename($dossierAsupprimer) != '..')
	{
		if (!adminDossierEstVide($dossierAsupprimer))
		{
			if ($dossier = @opendir($dossierAsupprimer))
			{
				while (($fichier = @readdir($dossier)) !== FALSE)
				{
					if (!is_dir("$dossierAsupprimer/$fichier"))
					{
						$messagesScriptChaine .= adminUnlink("$dossierAsupprimer/$fichier");
					}
					else
					{
						adminRmdirRecursif("$dossierAsupprimer/$fichier");
					}
				}
				
				closedir($dossier);
			}
			else
			{
				$messagesScriptChaine .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), "<code>$dossierAtraiter</code>") . "</li>\n";
			}
		}
		
		if (adminDossierEstVide($dossierAsupprimer))
		{
			$messagesScriptChaine .= adminRmdir($dossierAsupprimer);
		}
	}
	
	return $messagesScriptChaine;
}

/**
Modifie les permissions d'un dossier ainsi que son contenu et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminChmodRecursif($dossierAmodifier, $permissions)
{
	$messagesScriptChaine = '';
	
	if (basename($dossierAmodifier) != '.' && basename($dossierAmodifier) != '..')
	{
		if (adminDossierEstVide($dossierAmodifier))
		{
			$messagesScriptChaine .= adminChmod($dossierAmodifier, $permissions);
		}
		else
		{
			if ($dossier = @opendir($dossierAmodifier))
			{
				while (($fichier = @readdir($dossier)) !== FALSE)
				{
					if (!is_dir("$dossierAmodifier/$fichier"))
					{
						$messagesScriptChaine .= adminChmod("$dossierAmodifier/$fichier", $permissions);
					}
					else
					{
						$messagesScriptChaine .= adminChmodRecursif("$dossierAmodifier/$fichier", $permissions);
					}
				}
				
				closedir($dossier);
			}
			else
			{
				$messagesScriptChaine .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), "<code>$dossierAmodifier</code>") . "</li>\n";
			}
		}
	}
	
	return $messagesScriptChaine;
}

/**
Simule `copy()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminCopy($fichierSource, $fichierDeDestination)
{
	if (@copy($fichierSource, $fichierDeDestination))
	{
		return '<li>' . sprintf(T_("Copie de %1\$s vers %2\$s effectuée."), "<code>$fichierSource</code>", "<code>$fichierDeDestination</code>") . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible."), "<code>$fichierSource</code>", "<code>$fichierDeDestination</code>") . "</li>\n";
	}
}

/**
Simule `unlink()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminUnlink($fichier)
{
	if (@unlink($fichier))
	{
		return '<li>' . sprintf(T_("Suppression de %1\$s effectuée."), "<code>$fichier</code>") . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Suppression de %1\$s impossible."), "<code>$fichier</code>") . "</li>\n";
	}
}

/**
Simule `rmdir()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminRmdir($dossier)
{
	if (@rmdir($dossier))
	{
		return '<li>' . sprintf(T_("Suppression de %1\$s effectuée."), "<code>$dossier</code>") . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Suppression de %1\$s impossible."), "<code>$dossier</code>") . "</li>\n";
	}
}

/**
Simule `chmod()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminChmod($fichier, $permissions)
{
	$anciennesPermissions = adminPermissionsFichier($fichier);
	
	if ($permissions != octdec($anciennesPermissions))
	{
		if (@chmod($fichier, $permissions))
		{
			return '<li>' . sprintf(T_("Modification des permissions de %1\$s effectuée (de %2\$s vers %3\$s)."), "<code>$fichier</code>", "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
		}
		else
		{
			return '<li class="erreur">' . sprintf(T_("Modification des permissions de %1\$s impossible (de %2\$s vers %3\$s)."), "<code>$fichier</code>", "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
		}
	}
	else
	{
		return '<li>' . sprintf(T_("Modification des permissions de %1\$s non nécessaire (demande de %2\$s vers %3\$s)."), "<code>$fichier</code>", "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
	}
}

/**
Simule `rename()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`. Si `$messageDeplacement` vaut TRUE, le message retourné présente l'action effectuée comme étant un déplacement, sinon présente l'action comme étant un renommage.
*/
function adminRename($ancienNom, $nouveauNom, $messageDeplacement = FALSE)
{
	if (@rename($ancienNom, $nouveauNom))
	{
		if ($messageDeplacement)
		{
			return '<li>' . sprintf(T_("Déplacement de %1\$s vers %2\$s effectué."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
		}
		else
		{
			return '<li>' . sprintf(T_("Renommage de %1\$s en %2\$s effectué."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
		}
	}
	else
	{
		if ($messageDeplacement)
		{
			return '<li class="erreur">' . sprintf(T_("Déplacement de %1\$s vers %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
		}
		else
		{
			return '<li class="erreur">' . sprintf(T_("Renommage de %1\$s en %2\$s impossible."), "<code>$ancienNom</code>", "<code>$nouveauNom</code>") . "</li>\n";
		}
	}
}

/**
Simule `mkdir()` et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminMkdir($fichier, $permissions, $recursivite = FALSE)
{
	if (@mkdir($fichier, $permissions, $recursivite))
	{
		return '<li>' . sprintf(T_("Création du dossier %1\$s effectuée."), "<code>$fichier</code>") . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Création du dossier %1\$s impossible."), "<code>$fichier</code>") . "</li>\n";
	}
}

/**
Retourne la transcription en texte d'une erreur `$_FILES['fichier']['error']` sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminMessageFilesError($erreur)
{
	$messageErreur = '';
	
	// Voir <http://www.php.net/manual/fr/features.file-upload.errors.php>
	switch ($erreur)
	{
		case 0:
			$messageErreur = T_("Aucune erreur, le téléchargement est correct.");
			break;
			
		case 1:
			$messageErreur = T_("Le fichier téléchargé excède la taille de upload_max_filesize, configurée dans le php.ini.");
			break;
			
		case 2:
			$messageErreur = T_("Le fichier téléchargé excède la taille de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML.");
			break;
			
		case 3:
			$messageErreur = T_("Le fichier n'a été que partiellement téléchargé.");
			break;
			
		case 4:
			$messageErreur = T_("Aucun fichier n'a été téléchargé.");
			break;
			
		case 6:
			$messageErreur = T_("Un dossier temporaire est manquant.");
			break;
			
		case 7:
			$messageErreur = T_("Échec de l'écriture du fichier sur le disque.");
			break;
			
		case 8:
			$messageErreur = T_("L'envoi de fichier est arrêté par l'extension.");
			break;
	}
	
	if ($erreur)
	{
		return '<li class="erreur">' . $messageErreur . "</li>\n";
	}
	else
	{
		return '<li>' . $messageErreur . "</li>\n";
	}
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
function adminIpInternaute()
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
function adminReecritureDurl($retourneMessage)
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
	if ($fic = @fopen($cheminHtaccess, 'r'))
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
	if ($fic = @fopen($cheminHtaccess, 'r'))
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
function adminDossierEstVide($cheminDossier)
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

/**
Compresse (gzip) un fichier. Merci à <http://ca.php.net/manual/fr/function.gzwrite.php#34955>.
*/
function adminGz($fichierSource)
{
	$fichierCompresse = $fichierSource . '.gz';
	$erreur = FALSE;
	
	if ($ficDest = gzopen($fichierCompresse, 'wb9'))
	{
		if ($ficSource = fopen($fichierSource, 'rb'))
		{
			while (!feof($ficSource))
			{
				gzwrite($ficDest, fread($ficSource, 1024 * 512));
			}
			
			fclose($ficSource);
		}
		else
		{
			$erreur = TRUE;
		}
		
		gzclose($ficDest);
	}
	else
	{
		$erreur = TRUE;
	}
	
	if ($erreur)
	{
		return FALSE;
	}
	else
	{
		return $fichierCompresse;
	}
}
?>
