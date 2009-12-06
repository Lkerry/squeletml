<?php
/**
Retourne un tableau contenant les fichiers à inclure au début du script.
*/
function adminAinclureDebut($racineAdmin)
{
	$racine = dirname($racineAdmin);
	
	$fichiers = array ();
	$fichiers[] = $racine . '/inc/mimedetect/file.inc.php';
	$fichiers[] = $racine . '/inc/mimedetect/mimedetect.inc.php';
	$fichiers[] = $racine . '/inc/php-markdown/markdown.php';
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc';
	$fichiers[] = $racineAdmin . '/inc/pclzip/pclzip.lib.php';
	$fichiers[] = $racineAdmin . '/inc/tar/tar.class.php';
	$fichiers[] = $racineAdmin . '/inc/untar/untar.class.php';
	
	foreach (cheminsInc($racine, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	foreach (cheminsInc($racine, 'constantes') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/**
Retourne l'`id` de `body`.
*/
function adminBodyId()
{
	return str_replace('.', '-', page());
}

/**
Retourne un tableau dont chaque élément contient un chemin vers le fichier `(site/)basename($racineAdmin)/inc/$nom.inc.php` demandé.
*/
function adminCheminsInc($racineAdmin, $nom)
{
	$racine = dirname($racineAdmin);
	$dossierAdmin = basename($racineAdmin);
	$fichiers = array ();
	$fichiers[] = "$racineAdmin/inc/$nom.inc.php";
	
	if (file_exists("$racine/site/$dossierAdmin/inc/$nom.inc.php"))
	{
		$fichiers[] = "$racine/site/$dossierAdmin/inc/$nom.inc.php";
	}
	
	return $fichiers;
}

/**
Retourne le chemin vers le fichier `(site/)basename($racineAdmin)/xhtml/$nom.inc.php` demandé. Si aucun fichier n'a été trouvé, retourne une chaîne vide.
*/
function adminCheminXhtml($racineAdmin, $nom)
{
	$racine = dirname($racineAdmin);
	$dossierAdmin = basename($racineAdmin);
	
	if (file_exists("$racine/site/$dossierAdmin/xhtml/$nom.inc.php"))
	{
		return "$racine/site/$dossierAdmin/xhtml/$nom.inc.php";
	}
	elseif (file_exists("$racineAdmin/xhtml/$nom.inc.php"))
	{
		return "$racineAdmin/xhtml/$nom.inc.php";
	}
	
	return '';
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
Modifie les permissions d'un dossier ainsi que son contenu et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminChmodRecursif($dossierAmodifier, $permissions)
{
	$messagesScriptChaine = '';
	
	if (superBasename($dossierAmodifier) != '.' && superBasename($dossierAmodifier) != '..')
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
Retourne TRUE s'il est permis de modifier l'emplacement du fichier passé en paramètre, sinon retourne FALSE.
*/
function adminEmplacementModifiable($cheminFichier, $adminDossierRacinePorteDocuments)
{
	$adminDossierRacinePorteDocuments = realpath($adminDossierRacinePorteDocuments);
	$cheminFichier = realpath($cheminFichier);
	
	if (is_dir($cheminFichier) && ($cheminFichier == $adminDossierRacinePorteDocuments || $cheminFichier == '.' || $cheminFichier == '..' || preg_match('|/\.{1,2}$|', $cheminFichier) || !preg_match("|^$adminDossierRacinePorteDocuments(/.+)?$|", $cheminFichier)))
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}

/**
Retourne TRUE s'il est permis de gérer l'emplacement du fichier passé en paramètre, sinon retourne FALSE.
*/
function adminEmplacementPermis($cheminFichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers)
{
	$adminDossierRacinePorteDocuments = realpath($adminDossierRacinePorteDocuments);
	$cheminTestFichier = $cheminFichier;
	
	do
	{
		$chemin = realpath($cheminTestFichier);
	} while ($chemin === FALSE && $cheminTestFichier = dirname($cheminTestFichier));
	
	$cheminFichier = $chemin;
	
	if (is_dir($cheminFichier))
	{
		$emplacement = $cheminFichier;
	}
	else
	{
		$emplacement = dirname($cheminFichier);
	}
	
	if ($emplacement == $adminDossierRacinePorteDocuments || empty($adminTypeFiltreDossiers))
	{
		return TRUE;
	}
	elseif ($adminTypeFiltreDossiers == 'dossiersInclus')
	{
		foreach ($tableauFiltresDossiers as $dossierFiltre)
		{
			if (preg_match("|^$dossierFiltre(/.+)?$|", $emplacement))
			{
				return TRUE;
			}
		}
	}
	elseif ($adminTypeFiltreDossiers == 'dossiersExclus')
	{
		$aAjouter = TRUE;
		
		foreach ($tableauFiltresDossiers as $dossierFiltre)
		{
			if (preg_match("|^$dossierFiltre(/.+)?$|", $emplacement) || !preg_match("|^$adminDossierRacinePorteDocuments(/.+)?$|", $emplacement))
			{
				$aAjouter = FALSE;
				break;
			}
		}
		
		if ($aAjouter)
		{
			return TRUE;
		}
	}
	
	return FALSE;
}

/**
Retourne le tableau d'emplacements vidé des emplacements non modifiables.
*/
function adminEmplacementsModifiables($tableauFichiers, $adminDossierRacinePorteDocuments)
{
	$tableauFichiersFiltre = array ();
	
	foreach ($tableauFichiers as $cheminFichier)
	{
		if (adminEmplacementModifiable($cheminFichier, $adminDossierRacinePorteDocuments))
		{
			$tableauFichiersFiltre[] = $cheminFichier;
		}
	}
	
	return $tableauFichiersFiltre;
}

/**
Retourne le tableau d'emplacements vidé des emplacements non gérables.
*/
function adminEmplacementsPermis($tableauFichiers, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers)
{
	$tableauFichiersFiltre = array ();
	
	foreach ($tableauFichiers as $cheminFichier)
	{
		if (adminEmplacementPermis($cheminFichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
		{
			$tableauFichiersFiltre[] = $cheminFichier;
		}
	}
	
	return $tableauFichiersFiltre;
}

/**
Retourne TRUE si le navigateur de l'internaute est Internet Explorer, sinon retourne FALSE.
*/
function adminEstIe()
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
Compresse (gzip) un fichier et retourne le chemin vers le fichier compressé. Si une erreur survient, retourne FALSE. Merci à <http://ca.php.net/manual/fr/function.gzwrite.php#34955>.
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
Retourne le code pour l'infobulle contenant les propriétés d'un fichier dans le porte-documents.
*/
function adminInfobulle($racineAdmin, $urlRacineAdmin, $cheminFichier, $apercu, $adminTailleCache, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	clearstatcache();
	
	$infobulle = '';
	$fichier = superBasename($cheminFichier);
	
	if (is_dir($cheminFichier))
	{
		$typeMime = T_("dossier");
	}
	else
	{
		$typeMime = typeMime($cheminFichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
	}
	
	$stat = stat($cheminFichier);
	
	if (@getimagesize($cheminFichier) !== FALSE)
	{
		list ($larg, $haut, $type, $attr) = @getimagesize($cheminFichier);
		$dimensionsImage = "$larg px × $haut px";
	}
	else
	{
		$dimensionsImage = FALSE;
	}
	
	if ($apercu && ($typeMime == 'image/gif' || $typeMime == 'image/jpeg' || $typeMime == 'image/png'))
	{
		// S'il n'existe pas déjà, l'aperçu est enregistré dans le dossier de cache de l'administration. On vérifie toutefois avant si on doit vider le cache (taille limite dépassée).
		
		if (adminTailleCache($racineAdmin) > $adminTailleCache)
		{
			adminVideCache($racineAdmin);
		}
		
		$cheminApercuImage = $racineAdmin . '/cache/' . md5($cheminFichier) . '.' . array_pop(explode('.', $cheminFichier));;
		
		if (!file_exists($cheminApercuImage))
		{
			nouvelleImage($cheminFichier, $cheminApercuImage, $typeMime, array ('largeur' => 50, 'hauteur' => 50), TRUE, '85', FALSE);
		}
		
		if (file_exists($cheminApercuImage))
		{
			list ($larg, $haut, $type, $attr) = getimagesize($cheminApercuImage);
			$apercu = "<img class=\"infobulleApercuImage\" src=\"" . $urlRacineAdmin . "/cache/" . superBasename($cheminApercuImage) . "\" width=\"$larg\" height=\"$haut\" alt=\"" . sprintf(T_("Aperçu de l'image %1\$s"), $fichier) . "\" />";
		}
	}
	
	$infobulle .= "<a class=\"porteDocumentsProprietesFichier\" href=\"#\"><img src=\"$urlRacineAdmin/fichiers/proprietes.png\" alt=\"" . T_("Propriétés") . "\" width=\"16\" height=\"16\" /><span>";
	$infobulle .= sprintf(T_("<strong>Type MIME:</strong> %1\$s"), $typeMime) . "<br />\n";
	
	if ($stat)
	{
		$infobulle .= sprintf(T_("<strong>Taille:</strong> %1\$s Kio (%2\$s octets)"), octetsVersKio($stat['size']), $stat['size']) . "<br />\n";
		
		if ($dimensionsImage)
		{
			$infobulle .= sprintf(T_("<strong>Dimensions:</strong> %1\$s"), $dimensionsImage) . "<br />\n";
		}
		
		if ($apercu)
		{
			$infobulle .= sprintf(T_("<strong>Aperçu:</strong> %1\$s"), $apercu) . "<br />\n";
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
Retourne sous forme de tableau la liste des dossiers et fichiers contenus dans un emplacement fourni en paramètre. L'analyse est récursive. Les dossiers ou fichiers dont l'accès a échoué ne sont pas retournés.
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
function adminListeFiltreeDossiers($dossierAlister, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers)
{
	static $liste = array ();
	
	if (adminEmplacementPermis($dossierAlister, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
	{
		if (!in_array($dossierAlister, $liste))
		{
			$liste[] = $dossierAlister;
		}
		
		if ($dossier = @opendir($dossierAlister))
		{
			while (($fichier = @readdir($dossier)) !== FALSE)
			{
				if ($fichier != '.' && $fichier != '..' && is_dir($dossierAlister . '/' . $fichier))
				{
					if (!in_array($dossierAlister . '/' . $fichier, $liste) && adminEmplacementPermis($dossierAlister . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
					{
						$liste[] = $dossierAlister . '/' . $fichier;
					}
					
					adminListeFiltreeDossiers($dossierAlister . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers);
				}
			}
		
			closedir($dossier);
		}
	}
	
	if (!empty($liste))
	{
		natcasesort($liste);
	}
	
	return $liste;
}

/**
Retourne la liste filtrée des fichiers contenus dans un emplacement fourni en paramètre et prête à être affichée dans le porte-documents (contient s'il ya lieu les liens d'action comme l'édition, la suppression, etc.). L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierAparcourir, $adminTypeFiltreDossiers, $tableauFiltresDossiers, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminPorteDocumentsDroits, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	$racine = dirname($racineAdmin);
	static $liste = array ();
	
	if (adminEmplacementPermis($dossierAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers) && $dossier = @opendir($dossierAparcourir))
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
			if ($fichier != '.' && $fichier != '..' && adminEmplacementPermis($dossierAparcourir . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreDossiers, $tableauFiltresDossiers))
			{
				if (is_dir($dossierAparcourir . '/' . $fichier))
				{
					if (adminDossierEstVide($dossierAparcourir . '/' . $fichier))
					{
						$liste[$dossierAparcourir . '/' . $fichier][] = T_("Vide.");
					}
					else
					{
						adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierAparcourir . '/' . $fichier, $adminTypeFiltreDossiers, $tableauFiltresDossiers, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminPorteDocumentsDroits, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					}
				}
				else
				{
					$fichierMisEnForme = '';
				
					if ($adminPorteDocumentsDroits['copier'] && $adminPorteDocumentsDroits['deplacer'] && $adminPorteDocumentsDroits['permissions'] && $adminPorteDocumentsDroits['supprimer'])
					{
						$fichierMisEnForme .= "<input type=\"checkbox\" name=\"porteDocumentsFichiers[]\" value=\"$dossierAparcourir/$fichier\" />\n";
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($adminPorteDocumentsDroits['telecharger'])
					{
						$fichierMisEnForme .= "<a href=\"$urlRacineAdmin/telecharger.admin.php?fichier=$dossierAparcourir/$fichier\"><img src=\"$urlRacineAdmin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($adminPorteDocumentsDroits['editer'])
					{
						$fichierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . "action=editer&amp;valeur=$dossierAparcourir/$fichier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacineAdmin/fichiers/editer.png\" alt=\"" . T_("Éditer") . "\" title=\"" . T_("Éditer") . "\" width=\"16\" height=\"16\" /></a>\n";
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					if ($adminPorteDocumentsDroits['renommer'])
					{
						$fichierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . "action=renommer&amp;valeur=$dossierAparcourir/$fichier$dossierCourantDansUrl#messagesPorteDocuments\"><img src=\"$urlRacineAdmin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
						$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					}
				
					$fichierMisEnForme .= adminInfobulle($racineAdmin, $urlRacineAdmin, "$dossierAparcourir/$fichier", TRUE, $adminTailleCache, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
					$fichierMisEnForme .= "<span class='porteDocumentsSep'>|</span>\n";
					$fichierMisEnForme .= "<a class=\"porteDocumentsFichier\" href=\"$dossierAparcourir/$fichier\" title=\"" . sprintf(T_("Afficher «%1\$s»"), $fichier) . "\"><code>$fichier</code></a>\n";
					$liste[$dossierAparcourir][] = $fichierMisEnForme;
				}
			}
		}
		
		closedir($dossier);
	}
	
	if (!empty($liste))
	{
		ksort($liste);
	}
	
	return $liste;
}

/**
Retourne un tableau dont chaque élément contient le nom d'une galerie. Si le paramètre `$avecConfigSeulement` vaut TRUE, retourne seulement les galeries ayant un fichier de configuration, sinon retourne le nom de tous les dossiers de `$racine/site/fichiers/galeries/`. Si une erreur survient, retourne FALSE.
*/
function adminListeGaleries($racine, $avecConfigSeulement = TRUE)
{
	if ($fic = @opendir($racine . '/site/fichiers/galeries'))
	{
		$galeries = array ();
		
		while($fichier = @readdir($fic))
		{
			if(is_dir($racine . '/site/fichiers/galeries/' . $fichier) && $fichier != '.' && $fichier != '..')
			{
				if (($avecConfigSeulement && cheminConfigGalerie($racine, $fichier)) || !$avecConfigSeulement)
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
Met à jour le fichier de configuration d'une galerie. Retourne FALSE si une erreur survient, sinon retourne TRUE.
*/
function adminMajConfigGalerie($racine, $id, $listeAjouts, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance)
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $id;
	$cheminConfigGalerie = cheminConfigGalerie($racine, $id);
	
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
			$cheminConfigGalerie = cheminConfigGalerie($racine, $id, TRUE);
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
						continue; // On sort de cette oeuvre sans la prendre en note, car elle n'existe plus.
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
				$typeMime = typeMime($cheminGalerie . '/' . $fichier, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
				$versionImage = adminVersionImage($racine, $cheminGalerie . '/' . $fichier, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime);
				
				if (adminImageValide($typeMime) && $versionImage != 'vignette' && $versionImage != 'original' && !adminImageEstDeclaree($fichier, $galerieTemp, 'intermediaire') && !adminImageEstDeclaree($fichier, $listeNouveauxFichiers, 'intermediaire'))
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
		$cheminConfigGalerie = cheminConfigGalerie($racine, $id, TRUE);
	}
	
	if (@file_put_contents($cheminConfigGalerie, $contenuConfig) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/**
Retourne la transcription en texte d'une erreur `$_FILES['fichier']['error']` sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminMessageFilesError($erreur)
{
	$messageErreur = '';
	
	switch ($erreur)
	{
		// Merci à <http://www.php.net/manual/fr/features.file-upload.errors.php> pour les messages.
		
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
		$messagesScriptChaine .= "</div><!-- /.sousBoite -->\n";
	}
	
	return $messagesScriptChaine;
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
Retourne la valeur en octets des tailles déclarées dans le `php.ini`. Ex.:

	2M => 2097152

Merci à <http://ca.php.net/manual/fr/ini.core.php#79564>.
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
Retourne un message informant si la réécriture d'URL est activée. Si `$retourneMessage` vaut TRUE, retourne une phrase complète, sinon retourne un seul caractère (`o` pour *oui*, `n` pour *non* ou `?` pour *impossible de le déterminer*).
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
		$message = T_("Impossible de déterminer si la réécriture d'URL est activée.");
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
Supprime un dossier ainsi que son contenu et retourne le résultat sous forme de message correspondant à un élément de `$messagesScript`.
*/
function adminRmdirRecursif($dossierAsupprimer)
{
	$messagesScriptChaine = '';
	
	if (superBasename($dossierAsupprimer) != '.' && superBasename($dossierAsupprimer) != '..')
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
Retourne le tableau d'emplacements trié en ordre décroissant selon la profondeur (nombre de dossiers parents) du fichier et, pour une même profondeur, en ordre décroissant selon le nom. Par exemple, la liste suivante:

	../site/fichiers/documents
	../site/fichiers/galeries/galerie
	../site/fichiers/images
	../css
	../js

sera retournée dans cet ordre:

	../site/fichiers/galeries/galerie
	../site/fichiers/images
	../site/fichiers/documents
	../js
	../css
*/
function adminTriParProfondeur($tableauFichiers)
{
	$tableauFichiersTemp = array ();
	
	foreach ($tableauFichiers as $cheminFichier)
	{
		$profondeur = substr_count($cheminFichier, '/');
		$tableauFichiersTemp[$profondeur][] = $cheminFichier;
	}
	
	krsort($tableauFichiersTemp);
	
	$tableauFichiers = array ();
	
	foreach ($tableauFichiersTemp as $profondeur)
	{
		natcasesort($profondeur);
		$profondeur = array_reverse($profondeur);
		
		foreach ($profondeur as $cheminFichier)
		{
			$tableauFichiers[] = $cheminFichier;
		}
	}
	
	return $tableauFichiers;
}

/**
Retourne TRUE si le type MIME passé en paramètre est permis, sinon retourne FALSE.
*/
function adminTypeMimePermis($typeMime, $adminFiltreTypesMime, $adminTypesMimePermis)
{
	if ($adminFiltreTypesMime && array_search($typeMime, $adminTypesMimePermis) === FALSE)
	{
		return FALSE;
	}
	else
	{
		return TRUE;
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
Retourne la version de l'image (intermediaire|vignette|original|inconnu).
*/
function adminVersionImage($racine, $image, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $typeMime)
{
	$nomImage = superBasename($image);
	
	if ($analyserConfig)
	{
		$cheminConfigGalerie = cheminConfigGalerie($racine, superBasename(dirname($image)));
		
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
Retourne la version de Squeletml.
*/
function adminVersionSqueletml($racine)
{
	$fic = @fopen($racine . '/version.txt', 'r');
	$tag = fgets($fic, 20); // Exemple: logiciel-1.8
	fclose($fic);
	$version = explode('-', $tag);
	
	return trim($version[1]);
}

/**
Vide le cache. Seuls les fichiers à la racine du dossier sont supprimés, ce qui constitue tout ce qui est mis en cache par Squeletml. Retourne FALSE si une erreur survient, sinon retourne TRUE.
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
?>
