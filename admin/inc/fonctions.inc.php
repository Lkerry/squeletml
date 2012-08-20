<?php
/*
Retourne un tableau associatif de deux éléments:

- `cheminFichier`: le chemin du fichier à créer dans le porte-documents;
- `messagesScript`: le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminCheminFichierAcreerPorteDocuments($adminDossierRacinePorteDocuments)
{
	$cheminFichier = '';
	$messagesScript = '';
	
	if (isset($_POST['porteDocumentsCreationNom']) && isset($_POST['porteDocumentsCreationChemin']))
	{
		$cheminFichier = decodeTexte($_POST['porteDocumentsCreationNom']);
		
		if (isset($_POST['filtrerNom']) && in_array('filtrer', $_POST['filtrerNom']))
		{
			$ancienCheminFichier = $cheminFichier;
			$casse = '';
			
			if (in_array('min', $_POST['filtrerNom']))
			{
				$casse = 'min';
			}
			
			$cheminFichier = filtreChaine($cheminFichier, $casse);
			
			if ($cheminFichier != $ancienCheminFichier)
			{
				$messagesScript .= '<li>' . sprintf(T_("Filtrage de la chaîne de caractères %1\$s en %2\$s effectué."), '<code>' . securiseTexte($ancienCheminFichier) . '</code>', '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
			}
		}
		
		$cheminFichier = decodeTexte($_POST['porteDocumentsCreationChemin']) . '/' . $cheminFichier;
		
		if (!preg_match('#^' . preg_quote($adminDossierRacinePorteDocuments, '#') . '/#', $cheminFichier))
		{
			$cheminFichier = "$adminDossierRacinePorteDocuments/$cheminFichier";
		}
	}
	
	return array ('cheminFichier' => $cheminFichier, 'messagesScript' => $messagesScript);
}

/*
Retourne le chemin du fichier relatif à la racine du porte-documents.
*/
function adminCheminFichierRelatifRacinePorteDocuments($racine, $adminDossierRacinePorteDocuments, $cheminFichier)
{
	if (strpos($cheminFichier, $racine . '/') !== 0)
	{
		$cheminFichier = $racine . '/' . $cheminFichier;
	}
	
	$cheminFichier = preg_replace('#^' . preg_quote(realpath($adminDossierRacinePorteDocuments), '#') . '/?#', '', $cheminFichier);
	$cheminFichier = $adminDossierRacinePorteDocuments . '/' . $cheminFichier;
	
	return $cheminFichier;
}

/*
Retourne un tableau dont chaque élément contient un chemin vers le fichier `(site/)basename($racineAdmin)/inc/$nom.inc.php` demandé.
*/
function adminCheminsInc($racineAdmin, $nom)
{
	$racine = dirname($racineAdmin);
	$dossierAdmin = superBasename($racineAdmin);
	$fichiers = array ();
	$fichiers[] = "$racineAdmin/inc/$nom.inc.php";
	
	if (file_exists("$racine/site/$dossierAdmin/inc/$nom.inc.php"))
	{
		$fichiers[] = "$racine/site/$dossierAdmin/inc/$nom.inc.php";
	}
	
	return $fichiers;
}

/*
Retourne le chemin vers le fichier `(site/)basename($racineAdmin)/xhtml/(LANGUE/)$nom.inc.php` demandé. Si aucun fichier n'a été trouvé, retourne une chaîne vide.
*/
function adminCheminXhtml($racineAdmin, $langues, $nom)
{
	$racine = dirname($racineAdmin);
	$dossierAdmin = superBasename($racineAdmin);
	
	foreach ($langues as $langue)
	{
		if (file_exists("$racine/site/$dossierAdmin/xhtml/$langue/$nom.inc.php"))
		{
			return "$racine/site/$dossierAdmin/xhtml/$langue/$nom.inc.php";
		}
		elseif (file_exists("$racine/site/$dossierAdmin/xhtml/$nom.inc.php"))
		{
			return "$racine/site/$dossierAdmin/xhtml/$nom.inc.php";
		}
	}
	
	foreach ($langues as $langue)
	{
		if (file_exists("$racineAdmin/xhtml/$langue/$nom.inc.php"))
		{
			return "$racineAdmin/xhtml/$langue/$nom.inc.php";
		}
		elseif (file_exists("$racineAdmin/xhtml/$nom.inc.php"))
		{
			return "$racineAdmin/xhtml/$nom.inc.php";
		}
	}
	
	return '';
}

/*
Simule `chmod()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminChmod($fichier, $permissions)
{
	$anciennesPermissions = adminPermissionsFichier($fichier);
	
	if ($permissions != octdec($anciennesPermissions))
	{
		if (@chmod($fichier, $permissions))
		{
			return '<li>' . sprintf(T_("Modification des permissions de %1\$s effectuée (de %2\$s vers %3\$s)."), '<code>' . securiseTexte($fichier) . '</code>', "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
		}
		else
		{
			return '<li class="erreur">' . sprintf(T_("Modification des permissions de %1\$s impossible (de %2\$s vers %3\$s)."), '<code>' . securiseTexte($fichier) . '</code>', "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
		}
	}
	else
	{
		return '<li>' . sprintf(T_("Modification des permissions de %1\$s non nécessaire (demande de %2\$s vers %3\$s)."), '<code>' . securiseTexte($fichier) . '</code>', "<code>$anciennesPermissions</code>", "<code>" . decoct($permissions) . "</code>") . "</li>\n";
	}
}

/*
Modifie les permissions d'un dossier ainsi que son contenu et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminChmodRecursif($dossierAmodifier, $permissions)
{
	$messagesScript = '';
	
	if (superBasename($dossierAmodifier) != '.' && superBasename($dossierAmodifier) != '..')
	{
		if (adminDossierEstVide($dossierAmodifier))
		{
			$messagesScript .= adminChmod($dossierAmodifier, $permissions);
		}
		else
		{
			if ($dossier = @opendir($dossierAmodifier))
			{
				while (($fichier = @readdir($dossier)) !== FALSE)
				{
					if (!is_dir("$dossierAmodifier/$fichier"))
					{
						$messagesScript .= adminChmod("$dossierAmodifier/$fichier", $permissions);
					}
					else
					{
						$messagesScript .= adminChmodRecursif("$dossierAmodifier/$fichier", $permissions);
					}
				}
				
				closedir($dossier);
			}
			else
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), '<code>' . securiseTexte($dossierAmodifier) . '</code>') . "</li>\n";
			}
		}
	}
	
	return $messagesScript;
}

/*
Retourne une liste de classes pour `body`.
*/
function adminClassesBody($tableDesMatieresAvecFond, $tableDesMatieresArrondie)
{
	$classesBody = '';
	
	if ($tableDesMatieresAvecFond)
	{
		$classesBody .= 'tableDesMatieresAvecFond ';
	}
	
	if ($tableDesMatieresArrondie)
	{
		$classesBody .= 'tableDesMatieresArrondie ';
	}
	
	return $classesBody;
}

/*
Simule `copy()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminCopy($fichierSource, $fichierDeDestination)
{
	if (@copy($fichierSource, $fichierDeDestination))
	{
		return '<li>' . sprintf(T_("Copie de %1\$s vers %2\$s effectuée."), '<code>' . securiseTexte($fichierSource) . '</code>', '<code>' . securiseTexte($fichierDeDestination) . '</code>') . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Copie de %1\$s vers %2\$s impossible."), '<code>' . securiseTexte($fichierSource) . '</code>', '<code>' . securiseTexte($fichierDeDestination) . '</code>') . "</li>\n";
	}
}

/*
Copie un dossier dans un autre et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminCopyDossier($dossierSource, $dossierDeDestination)
{
	$messagesScript = '';
	
	if (!file_exists($dossierDeDestination))
	{
		$messagesScript .= adminMkdir($dossierDeDestination, octdec(adminPermissionsFichier($dossierSource)), TRUE);
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
						$messagesScript .= adminCopyDossier($dossierSource . '/' . $fichier, $dossierDeDestination . '/' . $fichier);
					}
					else
					{
						$messagesScript .= adminCopy($dossierSource . '/' . $fichier, $dossierDeDestination . '/' . $fichier);
					}
				}
			}
		
			closedir($dossier);
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), '<code>' . securiseTexte($dossierSource) . '</code>') . "</li>\n";
		}
	}
	
	return $messagesScript;
}

/*
Vérifie si le fichier d'index Sitemap est déclaré dans le fichier `robots.txt`, et le déclare au besoin. Retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminDeclareSitemapDansRobots($racine, $urlRacine)
{
	$messagesScript = '';
	$cheminFichierRobots = $racine . '/robots.txt';
	
	if (!file_exists($cheminFichierRobots) && !@touch($cheminFichierRobots))
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Le fichier Sitemap ne peut être déclaré puisque %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichierRobots) . '</code>') . "</li>\n";
	}
	
	if (file_exists($cheminFichierRobots))
	{
		$contenuRobots = @file_get_contents($cheminFichierRobots);
		
		if ($contenuRobots === FALSE)
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichierRobots) . '</code>') . "</li>\n";
		}
		else
		{
			$contenuRobots = trim($contenuRobots);
			$declaration = "Sitemap: $urlRacine/sitemap.xml";
			
			if (preg_match('/^' . preg_quote($declaration, '/') . '$/m', $contenuRobots))
			{
				$messagesScript .= '<li>' . sprintf(T_("Le fichier Sitemap est déjà déclaré dans le fichier %1\$s."), '<code>' . securiseTexte($cheminFichierRobots) . '</code>') . "</li>\n";
			}
			else
			{
				$contenuRobots .= "\n$declaration";
				$contenuRobots = preg_replace("/\n{2,}/", "\n", $contenuRobots);
				$messagesScript .= '<li class="contenuFichierPourSauvegarde">';

				if (@file_put_contents($cheminFichierRobots, $contenuRobots) !== FALSE)
				{
					$messagesScript .= '<p>' . sprintf(T_("Déclaration du fichier Sitemap dans le fichier %1\$s effectuée."), '<code>' . securiseTexte($cheminFichierRobots) . '</code>') . "</p>\n";


					$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui a été enregistré dans le fichier:") . "</p>\n";
				}
				else
				{
					$messagesScript .= '<p class="erreur">' . sprintf(T_("Déclaration du fichier Sitemap dans le fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichierRobots) . '</code>') . "</p>\n";

					$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
				}

				$messagesScript .= "<div class=\"bDcorps\">\n";
				$messagesScript .= '<pre id="contenuFichierRobots">' . securiseTexte($contenuRobots) . "</pre>\n";
	
				$messagesScript .= "<ul>\n";
				$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierRobots');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
				$messagesScript .= "</ul>\n";
				$messagesScript .= "</div><!-- /.bDcorps -->\n";
				$messagesScript .= "</li>\n";
			}
		}
	}
	
	return $messagesScript;
}

/*
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

/*
Retourne TRUE si le dossier fourni est affichable, sinon retourne FALSE.
*/
function adminEmplacementAffichable($dossierAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreAffichage, $tableauFiltresAffichage)
{
	$adminDossierRacinePorteDocuments = realpath($adminDossierRacinePorteDocuments);
	
	do
	{
		$emplacement = realpath($dossierAparcourir);
	} while ($emplacement === FALSE && $dossierAparcourir = dirname($dossierAparcourir));
	
	if ($emplacement == $adminDossierRacinePorteDocuments || empty($adminTypeFiltreAffichage))
	{
		return TRUE;
	}
	elseif ($adminTypeFiltreAffichage == 'dossiersAffiches')
	{
		foreach ($tableauFiltresAffichage as $dossierFiltre)
		{
			if (preg_match('#^' . preg_quote($dossierFiltre, '#') . '(/.+)?$#', $emplacement))
			{
				return TRUE;
			}
		}
	}
	elseif ($adminTypeFiltreAffichage == 'dossiersNonAffiches')
	{
		$aAjouter = TRUE;
		
		foreach ($tableauFiltresAffichage as $dossierFiltre)
		{
			if (preg_match('#^' . preg_quote($dossierFiltre, '#') . '(/.+)?$#', $emplacement) || !preg_match('#^' . preg_quote($adminDossierRacinePorteDocuments, '#') . '(/.+)?$#', $emplacement))
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

/*
Retourne TRUE s'il est permis de modifier l'emplacement du fichier passé en paramètre, sinon retourne FALSE.
*/
function adminEmplacementModifiable($cheminFichier, $adminDossierRacinePorteDocuments)
{
	$adminDossierRacinePorteDocuments = realpath($adminDossierRacinePorteDocuments);
	$cheminFichier = realpath($cheminFichier);
	
	if (is_dir($cheminFichier) && ($cheminFichier == $adminDossierRacinePorteDocuments || $cheminFichier == '.' || $cheminFichier == '..' || preg_match('#/\.{1,2}$#', $cheminFichier) || !preg_match('#^' . preg_quote($adminDossierRacinePorteDocuments, '#') . '(/.+)?$#', $cheminFichier)))
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}

/*
Retourne TRUE s'il est permis de gérer l'emplacement du fichier passé en paramètre, sinon retourne FALSE.
*/
function adminEmplacementPermis($cheminFichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers)
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
	
	if ($emplacement == $adminDossierRacinePorteDocuments || empty($adminTypeFiltreAccesDossiers))
	{
		return TRUE;
	}
	elseif ($adminTypeFiltreAccesDossiers == 'dossiersInclus')
	{
		foreach ($tableauFiltresAccesDossiers as $dossierFiltre)
		{
			if (preg_match('#^' . preg_quote($dossierFiltre, '#') . '(/.+)?$#', $emplacement))
			{
				return TRUE;
			}
		}
	}
	elseif ($adminTypeFiltreAccesDossiers == 'dossiersExclus')
	{
		$aAjouter = TRUE;
		
		foreach ($tableauFiltresAccesDossiers as $dossierFiltre)
		{
			if (preg_match('#^' . preg_quote($dossierFiltre, '#') . '(/.+)?$#', $emplacement) || !preg_match('#^' . preg_quote($adminDossierRacinePorteDocuments, '#') . '(/.+)?$#', $emplacement))
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

/*
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

/*
Retourne le tableau d'emplacements vidé des emplacements non gérables.
*/
function adminEmplacementsPermis($tableauFichiers, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers)
{
	$tableauFichiersFiltre = array ();
	
	foreach ($tableauFichiers as $cheminFichier)
	{
		if (adminEmplacementPermis($cheminFichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
		{
			$tableauFichiersFiltre[] = $cheminFichier;
		}
	}
	
	return $tableauFichiersFiltre;
}

/*
Si `$enregistrerCommentaires` vaut `TRUE`, enregistre un fichier de configuration des commentaires, sinon enregistre un fichier de configuration des abonnements aux notifications des nouveaux commentaires. Retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminEnregistreConfigCommentaires($racine, $cheminFichier, $contenuFichier, $enregistrerCommentaires = TRUE)
{
	$messagesScript = '';
	
	if (!file_exists($cheminFichier) && !@touch($cheminFichier))
	{
		if ($enregistrerCommentaires)
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucun commentaire ne peut être enregistré puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucun abonnement ne peut être enregistré puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
	}
	
	$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
	
	if (file_exists($cheminFichier))
	{
		if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
		{
			$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";
			
			$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
		}
		else
		{
			$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
			
			$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
		}
	}
	else
	{
		$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
	}

	$messagesScript .= "<div class=\"bDcorps\">\n";
	$messagesScript .= '<pre id="contenuFichier">' . securiseTexte($contenuFichier) . "</pre>\n";
	
	$messagesScript .= "<ul>\n";
	$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
	$messagesScript .= "</ul>\n";
	$messagesScript .= "</div><!-- /.bDcorps -->\n";
	$messagesScript .= "</li>\n";
	
	return $messagesScript;
}

/*
Enregistre la configuration du flux RSS des dernières publications et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminEnregistreConfigFluxRssGlobalSite($racine, $contenuFichier)
{
	$messagesScript = '';
	$cheminFichier = cheminConfigFluxRssGlobalSite($racine);
	
	if (!$cheminFichier)
	{
		$cheminFichier = cheminConfigFluxRssGlobalSite($racine, TRUE);
		
		if (!@touch($cheminFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS des dernières publications puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
	}
	
	$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
	
	if (file_exists($cheminFichier))
	{
		if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
		{
			$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

			$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
		}
		else
		{
			$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
			
			$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
		}
	}
	else
	{
		$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
	}

	$messagesScript .= "<div class=\"bDcorps\">\n";
	$messagesScript .= '<pre id="contenuFichier">' . securiseTexte($contenuFichier) . "</pre>\n";
	
	$messagesScript .= "<ul>\n";
	$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
	$messagesScript .= "</ul>\n";
	$messagesScript .= "</div><!-- /.bDcorps -->\n";
	$messagesScript .= "</li>\n";
	
	return $messagesScript;
}

/*
Enregistre le contenu fourni dans le fichier Sitemap et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminEnregistreSitemap($racine, $contenuSitemap)
{
	$messagesScript = '';
	$cheminFichierSitemap = $racine . '/sitemap.xml';
	
	if (!file_exists($cheminFichierSitemap))
	{
		if (!@touch($cheminFichierSitemap))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du fichier Sitemap puisque %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichierSitemap) . '</code>') . "</li>\n";
		}
	}

	$messagesScript .= '<li class="contenuFichierPourSauvegarde">';

	if (file_exists($cheminFichierSitemap))
	{
		if (@file_put_contents($cheminFichierSitemap, $contenuSitemap) !== FALSE)
		{
			$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

			$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminFichierSitemap) . '</code>') . "</p>\n";
		}
		else
		{
			$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichierSitemap) . '</code>') . "</p>\n";
		
			$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
		}
	}
	else
	{
		$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
	}

	$messagesScript .= "<div class=\"bDcorps\">\n";
	$messagesScript .= '<pre id="contenuFichierSitemap">' . securiseTexte($contenuSitemap) . "</pre>\n";

	$messagesScript .= "<ul>\n";
	$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierSitemap');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
	$messagesScript .= "</ul>\n";
	$messagesScript .= "</div><!-- /.bDcorps -->\n";
	$messagesScript .= "</li>\n";
	
	return $messagesScript;
}

/*
Retourne `TRUE` si le fichier est éditable dans le porte-documents, sinon retourne `FALSE`.
*/
function adminEstEditable($cheminFichier)
{
	$typeMime = typeMime($cheminFichier);
	
	if (strpos($typeMime, 'text/') === 0 || strpos($typeMime, 'xml') !== FALSE || $typeMime == 'application/x-empty')
	{
		return TRUE;
	}
	
	return FALSE;
}

/*
Retourne TRUE si le navigateur de l'internaute est Internet Explorer, sinon retourne FALSE.
*/
function adminEstIe()
{
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
Extrait l'archive (`.tar`, `.tar.bz2`, `.tar.gz` ou `.zip`) et retourne un tableau associatif de deux éléments:

- `fichiersExtraits`: un tableau listant les fichiers extraits;
- `messagesScript`: le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminExtraitArchive($cheminArchive, $cheminExtraction, $adminFiltreTypesMime, $adminTypesMimePermis, $filtrerNom = FALSE, $filtreCasse = '')
{
	$fichiersExtraits = array ();
	$messagesScript = '';
	$erreur = FALSE;
	
	try
	{
		$archive = ezcArchive::open($cheminArchive);
	}
	catch (Exception $e)
	{
		$erreur = TRUE;
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de l'extraction de l'archive %1\$s: «%2\$s»."), '<code>' . securiseTexte($cheminArchive) . '</code>', $e->getMessage()) . "</li>\n";
	}
	
	if (!$erreur)
	{
		$messagesScript .= '<li>' . sprintf(T_("Début de l'extraction de l'archive %1\$s."), '<code>' . securiseTexte($cheminArchive) . '</code>') . "</li>\n";
		
		while ($archive->valid())
		{
			$fichier = $archive->current();
			$cheminFichier = $cheminExtraction . '/' . $fichier->getPath();
			
			if (file_exists($cheminFichier))
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà. Il n'y a donc pas eu extraction."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
			}
			else
			{
				if ($filtrerNom)
				{
					$cheminFichierFiltre = filtreChaine($fichier->getPath(), $filtreCasse, FALSE);
				}
				else
				{
					$cheminFichierFiltre = $fichier->getPath();
				}
				
				$messagesScriptFiltrage = '';
				
				if ($cheminFichierFiltre != $fichier->getPath())
				{
					$messagesScriptFiltrage = '<li>' . sprintf(T_("Filtrage de la chaîne de caractères %1\$s en %2\$s effectué."), '<code>' . securiseTexte($fichier->getPath()) . '</code>', '<code>' . securiseTexte($cheminFichierFiltre) . '</code>') . "</li>\n";
				}
				
				$cheminFichierFiltre = $cheminExtraction . '/' . $cheminFichierFiltre;
				
				if (file_exists($cheminFichierFiltre))
				{
					$messagesScript .= $messagesScriptFiltrage;
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Un fichier %1\$s existe déjà. Il n'y a donc pas eu extraction."), '<code>' . securiseTexte($cheminFichierFiltre) . '</code>') . "</li>\n";
				}
				else
				{
					try
					{
						$extractionReussie = $archive->extractCurrent($cheminExtraction, TRUE);
					}
					catch (Exception $e)
					{
						$messagesScript .= '<li class="erreur">' . sprintf(T_("Erreur lors de l'extraction du fichier %1\$s: «%2\$s»."), '<code>' . securiseTexte($cheminFichier) . '</code>', $e->getMessage()) . "</li>\n";
						@unlink($cheminFichier);
					}
					
					if ($extractionReussie)
					{
						$typeMimeFichier = typeMime($cheminFichier);
						
						if (!adminTypeMimePermis($typeMimeFichier, $adminFiltreTypesMime, $adminTypesMimePermis))
						{
							$messagesScript .= '<li class="erreur">' . sprintf(T_("Le type MIME reconnu pour le fichier %1\$s est %2\$s, mais il n'est pas permis d'ajouter un tel type de fichier. Le transfert du fichier n'est donc pas possible."), '<code>' . securiseTexte($cheminFichier) . '</code>', '<code>' . $typeMimeFichier . '</code>') . "</li>\n";
							@unlink($cheminFichier);
						}
						else
						{
							$messagesScript .= '<li>' . sprintf(T_("Ajout de %1\$s effectué."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
						
							if (!empty($messagesScriptFiltrage))
							{
								$messagesScript .= $messagesScriptFiltrage;
								$messagesScript .= adminRename($cheminFichier, $cheminFichierFiltre);
							}
							
							if (file_exists($cheminFichierFiltre))
							{
								$fichiersExtraits[] = $cheminFichierFiltre;
							}
							else
							{
								$fichiersExtraits[] = $cheminFichier;
							}
						}
					}
				}
			}
			
			$archive->next();
		}
		
		$archive->close();
		unset($archive);
		$messagesScript .= '<li>' . sprintf(T_("Extraction de l'archive %1\$s effectuée."), '<code>' . securiseTexte($cheminArchive) . '</code>') . "</li>\n";
	}
	
	return array ('fichiersExtraits' => $fichiersExtraits, 'messagesScript' => $messagesScript);
}

/*
Génère et retourne le contenu du fichier Sitemap.
*/
function adminGenereContenuSitemap($tableauUrl)
{
	$contenuSitemap = adminPlanSitemapXml(FALSE);
	
	foreach ($tableauUrl as $urlAjout => $infosUrlAjout)
	{
		$contenuSitemap .= "\t<url>\n";
		$contenuSitemap .= "\t\t<loc>" . $urlAjout . "</loc>\n";
	
		foreach ($infosUrlAjout as $balise => $valeur)
		{
			if (empty($valeur))
			{
				continue;
			}
		
			if ($balise == 'image')
			{
				foreach ($valeur as $imageAjout => $infosImageAjout)
				{
					$contenuSitemap .= "\t\t<image:image xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";
					$contenuSitemap .= "\t\t\t<image:loc>$imageAjout</image:loc>\n";
				
					foreach ($infosImageAjout as $baliseImage => $valeurImage)
					{
						if (!empty($valeurImage))
						{
							$contenuSitemap .= "\t\t\t<image:$baliseImage>$valeurImage</image:$baliseImage>\n";
						}
					}
				
					$contenuSitemap .= "\t\t</image:image>\n";
				}
			}
			elseif ($balise == 'link')
			{
				foreach ($valeur as $href => $hreflang)
				{
					$contenuSitemap .= "\t\t<xhtml:link rel=\"alternate\" hreflang=\"$hreflang\" href=\"$href\" />\n";
				}
			}
			elseif ($balise != 'cache' && $balise != 'cacheEnTete')
			{
				$contenuSitemap .= "\t\t<$balise>$valeur</$balise>\n";
			}
		}
		
		$contenuSitemap .= "\t</url>\n";
	}
	
	$contenuSitemap .= '</urlset>';
	
	return $contenuSitemap;
}

/*
Retourne l'`id` de `body`.
*/
function adminIdBody()
{
	return str_replace('.', '-', nomPage());
}

/*
Retourne TRUE si l'image est déclarée dans le fichier de configuration, sinon retourne FALSE.
*/
function adminImageEstDeclaree($fichier, $tableauGalerie, $versionAchercher = FALSE)
{
	if ($tableauGalerie)
	{
		foreach ($tableauGalerie as $image)
		{
			if ((!$versionAchercher || $versionAchercher = 'intermediaire') && (isset($image['intermediaireNom']) && $image['intermediaireNom'] == $fichier))
			{
				return TRUE;
			}
			elseif ((!$versionAchercher || $versionAchercher = 'vignette') && (isset($image['vignetteNom']) && $image['vignetteNom'] == $fichier))
			{
				return TRUE;
			}
			elseif ((!$versionAchercher || $versionAchercher = 'original') && (isset($image['originalNom']) && $image['originalNom'] == $fichier))
			{
				return TRUE;
			}
		}
	}
	
	return FALSE;
}

/*
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

/*
Retourne un tableau contenant les fichiers à inclure au début du script.
*/
function adminInclureAuDebut($racineAdmin)
{
	$racine = dirname($racineAdmin);
	
	$fichiers = array ();
	
	foreach (cheminsInc($racine, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'config') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/*
Retourne un tableau contenant les fichiers à inclure une seule fois au début du script.
*/
function adminInclureUneFoisAuDebut($racineAdmin)
{
	$racine = dirname($racineAdmin);
	
	$fichiers = array ();
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/common.inc.php';
	$fichiers[] = $racine . '/inc/filter_htmlcorrector/filter.inc.php';
	$fichiers[] = $racine . '/inc/php-markdown/markdown.inc.php';
	$fichiers[] = $racine . '/inc/php-gettext/gettext.inc.php';
	$fichiers[] = $racine . '/inc/simplehtmldom/simple_html_dom.inc.php';
	$fichiers[] = $racineAdmin . '/inc/ezcomponents/Base/src/ezc_bootstrap.php';
	
	if (nomPage() == 'galeries.admin.php')
	{
		$fichiers[] = $racineAdmin . '/inc/UnsharpMask/UnsharpMask.inc.php';
	}
	
	foreach (cheminsInc($racine, 'constantes') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	foreach (adminCheminsInc($racineAdmin, 'constantes') as $fichier)
	{
		$fichiers[] = $fichier;
	}
	
	return $fichiers;
}

/*
Retourne le code pour l'infobulle contenant les propriétés d'un fichier dans le porte-documents.
*/
function adminInfobulle($racineAdmin, $urlRacineAdmin, $cheminFichier, $apercu, $adminTailleCache, $galerieQualiteJpg, $galerieCouleurAlloueeImage)
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
		$typeMime = typeMime($cheminFichier);
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
	
	if ($apercu && (!gdEstInstallee() || ($typeMime != 'image/gif' && $typeMime != 'image/jpeg' && $typeMime != 'image/png')))
	{
		$apercu = FALSE;
	}
	
	if ($apercu)
	{
		// S'il n'existe pas déjà, l'aperçu est enregistré dans le dossier de cache de l'administration. On vérifie toutefois avant si on doit vider le cache (taille limite dépassée).
		
		if (adminTailleCache($racineAdmin) > $adminTailleCache)
		{
			adminVideCache($racineAdmin, 'admin');
		}
		
		$racine = dirname($racineAdmin);
		$dossierAdmin = superBasename($racineAdmin);
		$nomFichierSansExtension = extension(superBasename($cheminFichier), TRUE);
		$extension = extension($cheminFichier);
		$cheminApercuImage = "$racine/site/$dossierAdmin/cache/" . filtreChaine($nomFichierSansExtension . '-' . dechex(crc32($cheminFichier)) . ".cache.$extension");
		
		if (!file_exists($cheminApercuImage))
		{
			nouvelleImage($cheminFichier, $cheminApercuImage, $typeMime, array ('largeur' => 50, 'hauteur' => 50), TRUE, $galerieQualiteJpg, $galerieCouleurAlloueeImage, array ('nettete' => FALSE));
		}
		
		if (file_exists($cheminApercuImage))
		{
			list ($larg, $haut, $type, $attr) = @getimagesize($cheminApercuImage);
			$apercu = "<img class=\"infobulleApercuImage\" src=\"" . dirname($urlRacineAdmin) . "/site/$dossierAdmin/cache/" . encodeTexte(superBasename($cheminApercuImage)) . "\" width=\"$larg\" height=\"$haut\" alt=\"" . sprintf(T_("Aperçu de l'image %1\$s"), $fichier) . "\" />";
		}
	}
	
	$infobulle .= "<a class=\"lienInfobulle\" href=\"#\"><img src=\"$urlRacineAdmin/fichiers/proprietes.png\" alt=\"" . T_("Propriétés") . "\" width=\"16\" height=\"16\" /><span>";
	$infobulle .= sprintf(T_("<strong>Type MIME:</strong> %1\$s"), $typeMime) . "<br />\n";
	
	if ($stat)
	{
		$infobulle .= sprintf(T_ngettext("<strong>Taille:</strong> %1\$s Kio (%2\$s octet)", "<strong>Taille:</strong> %1\$s Kio (%2\$s octets)", $stat['size']), octetsVersKio($stat['size']), $stat['size']) . "<br />\n";
		
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

/*
Retourne le code de langue du flux RSS des dernières publications dans laquelle la page fournie est classée. Si aucune langue n'est trouvée, retourne une chaîne vide.
*/
function adminLangueFluxRssPage($racine, $urlRacine, $urlPage)
{
	$langueFluxRssPage = '';
	$urlRelativePage = supprimeUrlRacine($urlRacine, $urlPage);
	$cheminFichierRss = cheminConfigFluxRssGlobalSite($racine, TRUE);
	
	if (file_exists($cheminFichierRss))
	{
		$rssPages = super_parse_ini_file($cheminFichierRss, TRUE);
		
		if (!empty($rssPages))
		{
			foreach ($rssPages as $codeLangue => $langueInfos)
			{
				if (!empty($langueInfos['pages']))
				{
					foreach ($langueInfos['pages'] as $page)
					{
						if ($page == $urlRelativePage)
						{
							$langueFluxRssPage = $codeLangue;
							break 2;
						}
					}
				}
			}
		}
	}
	
	return $langueFluxRssPage;
}

/*
Retourne sous forme de tableau la liste des dossiers et fichiers contenus dans un emplacement fourni en paramètre. L'analyse est récursive. Les dossiers ou fichiers dont l'accès a échoué ne sont pas retournés.
*/
function adminListeFichiers($dossier, $liste = array ())
{
	if (is_dir($dossier) && $fic = @opendir($dossier))
	{
		$liste[] = $dossier;
		
		while (($fichier = @readdir($fic)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..')
			{
				if (is_dir($dossier . '/' . $fichier))
				{
					$liste = adminListeFichiers($dossier . '/' . $fichier, $liste);
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

/*
Retourne la liste filtrée des dossiers contenus dans un emplacement fourni en paramètre. L'analyse est potentiellement récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFiltreeDossiers($dossierAlister, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, $adminListerSousDossiers, $liste = array ())
{
	if (adminEmplacementPermis($dossierAlister, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers) && adminEmplacementAffichable($dossierAlister, $adminDossierRacinePorteDocuments, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe))
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
					if (!in_array($dossierAlister . '/' . $fichier, $liste) && adminEmplacementPermis($dossierAlister . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers) && adminEmplacementAffichable($dossierAlister . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe))
					{
						$liste[] = $dossierAlister . '/' . $fichier;
					}
					
					if ($adminListerSousDossiers)
					{
						$liste = adminListeFiltreeDossiers($dossierAlister . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminTypeFiltreAffichageDansListe, $tableauFiltresAffichageDansListe, $adminListerSousDossiers, $liste);
					}
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

/*
Retourne la liste filtrée des fichiers contenus dans un emplacement fourni en paramètre et prête à être affichée dans le porte-documents (contient les liens d'action comme l'édition, la suppression, etc.). L'analyse est récursive. Voir le fichier de configuration de l'administration pour plus de détails au sujet du filtre.
*/
function adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierDeDepartAparcourir, $dossierAparcourir, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminAfficherSousDossiersDansContenu, $adminTypeFiltreAffichageDansContenu, $tableauFiltresAffichageDansContenu, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminActiverInfobulle, $galerieQualiteJpg, $galerieCouleurAlloueeImage, $liste = array ())
{
	$racine = dirname($racineAdmin);
	
	if (adminEmplacementPermis($dossierAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers) && $dossier = @opendir($dossierAparcourir))
	{
		if (!empty($dossierCourant))
		{
			$dossierCourantDansUrl = '&amp;dossierCourant=' . encodeTexteGet($dossierCourant);
		}
		else
		{
			$dossierCourantDansUrl = '';
		}
		
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if ($fichier != '.' && $fichier != '..' && adminEmplacementPermis($dossierAparcourir . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers))
			{
				if (is_dir($dossierAparcourir . '/' . $fichier))
				{
					if (adminDossierEstVide($dossierAparcourir . '/' . $fichier) || (!$adminAfficherSousDossiersDansContenu || (!adminEmplacementAffichable($dossierAparcourir . '/' . $fichier, $adminDossierRacinePorteDocuments, $adminTypeFiltreAffichageDansContenu, $tableauFiltresAffichageDansContenu) && adminEmplacementAffichable($dossierDeDepartAparcourir, $adminDossierRacinePorteDocuments, $adminTypeFiltreAffichageDansContenu, $tableauFiltresAffichageDansContenu))))
					{
						$liste[$dossierAparcourir . '/' . $fichier] = array ();
					}
					else
					{
						$liste = adminListeFormateeFichiers($racineAdmin, $urlRacineAdmin, $adminDossierRacinePorteDocuments, $dossierDeDepartAparcourir, $dossierAparcourir . '/' . $fichier, $adminTypeFiltreAccesDossiers, $tableauFiltresAccesDossiers, $adminAfficherSousDossiersDansContenu, $adminTypeFiltreAffichageDansContenu, $tableauFiltresAffichageDansContenu, $adminAction, $adminSymboleUrl, $dossierCourant, $adminTailleCache, $adminActiverInfobulle, $galerieQualiteJpg, $galerieCouleurAlloueeImage, $liste);
					}
				}
				else
				{
					$fichierMisEnForme = '';
					
					$fichierMisEnForme .= '<input type="checkbox" name="porteDocumentsFichiers[]" value="' . encodeTexte("$dossierAparcourir/$fichier") . '" />';
					$fichierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
					
					$fichierMisEnForme .= "<a href=\"$urlRacineAdmin/telecharger.admin.php?fichier=" . encodeTexteGet("$dossierAparcourir/$fichier") . "\"><img src=\"$urlRacineAdmin/fichiers/telecharger.png\" alt=\"" . T_("Télécharger") . "\" title=\"" . T_("Télécharger") . "\" width=\"16\" height=\"16\" /></a>\n";
					$fichierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
					
					$fichierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . 'action=renommer&amp;valeur=' . encodeTexteGet("$dossierAparcourir/$fichier") . "$dossierCourantDansUrl#messages\"><img src=\"$urlRacineAdmin/fichiers/renommer.png\" alt=\"" . T_("Renommer") . "\" title=\"" . T_("Renommer") . "\" width=\"16\" height=\"16\" /></a>\n";
					$fichierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
					
					if (adminEstEditable("$dossierAparcourir/$fichier"))
					{
						$fichierMisEnForme .= "<a href=\"$adminAction" . $adminSymboleUrl . 'action=editer&amp;valeur=' . encodeTexteGet("$dossierAparcourir/$fichier") . "$dossierCourantDansUrl#messages\"><img src=\"$urlRacineAdmin/fichiers/editer.png\" alt=\"" . T_("Éditer") . "\" title=\"" . T_("Éditer") . "\" width=\"16\" height=\"16\" /></a>\n";
					}
					else
					{
						$fichierMisEnForme .= "<img src=\"$urlRacineAdmin/fichiers/editer-desactive.png\" alt=\"" . T_("Éditer") . "\" title=\"" . T_("Éditer") . "\" width=\"16\" height=\"16\" />\n";
					}
					
					$fichierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
					
					if ($adminActiverInfobulle['contenuDossier'])
					{
						$fichierMisEnForme .= adminInfobulle($racineAdmin, $urlRacineAdmin, "$dossierAparcourir/$fichier", TRUE, $adminTailleCache, $galerieQualiteJpg, $galerieCouleurAlloueeImage);
						$fichierMisEnForme .= "<span class=\"porteDocumentsSep\">|</span>\n";
					}
					
					$fichierMisEnForme .= '<a class="lienSurCode" href="' . encodeTexte("$dossierAparcourir/$fichier") . '" title="' . sprintf(T_("Afficher «%1\$s»"), securiseTexte($fichier)) . '"><code>' . securiseTexte($fichier) . "</code></a>\n";
					$liste[$dossierAparcourir][] = $fichierMisEnForme;
				}
			}
		}
		
		closedir($dossier);
	}
	
	if (!empty($liste))
	{
		uksort($liste, 'strnatcasecmp');
	}
	
	return $liste;
}

/*
Retourne un tableau listant les pages ayant au moins un commentaire.
*/
function adminListePagesAvecCommentaires($racine)
{
	$listePages = array ();
	$racineCommentaires = "$racine/site/inc/commentaires";
	$listeFichiers = adminListeFichiers($racineCommentaires);
	
	foreach ($listeFichiers as $listeFichier)
	{
		if (!is_dir($listeFichier) && preg_match('#^' . preg_quote($racineCommentaires, '#') . '/([^/]+)\.ini\.txt$#', $listeFichier, $resultat) && file_exists(cheminConfigAbonnementsCommentaires($listeFichier)))
		{
			$configFichier = super_parse_ini_file($listeFichier, TRUE);
			
			if (!empty($configFichier))
			{
				$listePages[] = decodeTexte($resultat[1]);
			}
		}
	}
	
	uksort($listePages, 'strnatcasecmp');
	
	return $listePages;
}

/*
Génère la liste des URL que Squeletml connaît à propos du site et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminListeUrl($racine, $urlRacine, $accueil, $activerCategoriesGlobales, $nombreArticlesParPageCategorie, $nombreItemsFluxRss, $activerFluxRssGlobalSite, $galerieActiverFluxRssGlobal, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $galerieVignettesParPage, $activerGalerieDemo)
{
	$tableauUrl = array ();
	$langues = array ();
	
	foreach ($accueil as $codeLangue => $urlAccueilLangue)
	{
		$langues[] = $codeLangue;
	}
	
	foreach ($langues as $codeLangue)
	{
		// URL d'accueil.
		$url = $accueil[$codeLangue] . '/';
		$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
		$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
		$tableauUrl[$url]['cache'] = $cheminFichierCache;
		$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
		
		if (count($accueil) > 1)
		{
			foreach ($langues as $code)
			{
				$urlLink = $accueil[$code] . '/';
				$tableauUrl[$url]['link'][$urlLink] = $code;
			}
		}
	}
	
	$listeGaleries = listeGaleries($racine);
	
	// Galerie démo.
	if ($activerGalerieDemo)
	{
		$listeGaleries = array_merge(array ('démo' => array ('dossier' => 'demo', 'url' => 'galerie.php?id=demo&amp;langue={LANGUE}')), $listeGaleries);
	}
	
	foreach ($listeGaleries as $idGalerie => $infosGalerie)
	{
		if (!empty($infosGalerie['rss']) && $infosGalerie['rss'] == 1)
		{
			foreach ($langues as $codeLangue)
			{
				// Flux RSS individuel d'une galerie pour une langue donnée.
				$url = $urlRacine . '/rss.php?type=galerie&amp;id=' . filtreChaine($idGalerie) . "&amp;langue=$codeLangue";
				$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url, FALSE);
				$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
				$tableauUrl[$url]['cache'] = $cheminFichierCache;
				$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
				
				if (count($accueil) > 1)
				{
					foreach ($langues as $code)
					{
						$urlLink = variableGet(1, $url, 'langue', $code);
						$tableauUrl[$url]['link'][$urlLink] = $code;
					}
				}
			}
		}
		
		// Galerie.
		if (!empty($infosGalerie['dossier']) && cheminConfigGalerie($racine, $infosGalerie['dossier']))
		{
			$tableauGalerie = tableauGalerie(cheminConfigGalerie($racine, $infosGalerie['dossier']), TRUE);
			
			if ($galerieVignettesParPage)
			{
				$nombreDimages = count($tableauGalerie);
				$nombreDePages = ceil($nombreDimages / $galerieVignettesParPage);
			}
			else
			{
				$nombreDePages = 1;
			}
			
			foreach ($langues as $codeLangue)
			{
				if (!empty($infosGalerie['url']))
				{
					$url = urlGalerie(1, '', $urlRacine, $infosGalerie['url'], $codeLangue);
				}
				else
				{
					$url = urlGalerie(0, $racine, $urlRacine, $idGalerie, $codeLangue);
				}
				
				$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
				$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
				$tableauUrl[$url]['cache'] = $cheminFichierCache;
				$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
				
				if (count($accueil) > 1)
				{
					foreach ($langues as $code)
					{
						$urlLink = variableGet(1, $url, 'langue', $code);
						$tableauUrl[$url]['link'][$urlLink] = $code;
					}
				}
				
				if ($nombreDePages > 1)
				{
					for ($i = 2; $i <= $nombreDePages; $i++)
					{
						$urlPage = variableGet(2, $url, 'page', $i);
						$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlPage);
						$cheminFichierCacheEnTete = cheminFichierCacheEnTete($nomFichierCache);
						$tableauUrl[$urlPage]['cache'] = $cheminFichierCache;
						$tableauUrl[$urlPage]['cacheEnTete'] = $cheminFichierCacheEnTete;
						
						if (count($accueil) > 1)
						{
							foreach ($langues as $code)
							{
								$urlPageLink = variableGet(1, $urlPage, 'langue', $code);
								$tableauUrl[$urlPage]['link'][$urlPageLink] = $code;
							}
						}
					}
				}
				
				foreach ($tableauGalerie as $image)
				{
					$urlImage = variableGet(2, $url, 'image', idImage($image));
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlImage);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$urlImage]['cache'] = $cheminFichierCache;
					$tableauUrl[$urlImage]['cacheEnTete'] = $cheminFichierCacheEnTete;
					
					if (count($accueil) > 1)
					{
						foreach ($langues as $code)
						{
							$urlImageLink = variableGet(1, $urlImage, 'langue', $code);
							$tableauUrl[$urlImage]['link'][$urlImageLink] = $code;
						}
					}
					
					$tableauUrl[$urlImage]['image'] = array ();
					$urlImageIntermediaire = $urlRacine . '/site/fichiers/galeries/' . encodeTexte($infosGalerie['dossier'] . '/' . $image['intermediaireNom']);
					$tableauUrl[$urlImage]['image'][$urlImageIntermediaire] = array ();
					
					if (!empty($image['intermediaireLegende']))
					{
						$tableauUrl[$urlImage]['image'][$urlImageIntermediaire]['caption'] = securiseTexte($image['intermediaireLegende']);
					}
					
					if (!empty($image['titre']))
					{
						$tableauUrl[$urlImage]['image'][$urlImageIntermediaire]['title'] = securiseTexte($image['titre']);
					}
					
					if (!empty($image['licence']))
					{
						$tableauLicence = explode(' ', $image['licence'], 2);
						$codeLicence = licence($urlRacine, $tableauLicence[0]);
						preg_match('/href="([^"]+)"/', $codeLicence, $resultat);
						
						if (!empty($resultat[1]))
						{
							$tableauUrl[$urlImage]['image'][$urlImageIntermediaire]['license'] = $resultat[1];
						}
					}
				}
			}
		}
	}
	
	// Flux RSS global des galeries.
	if ($galerieActiverFluxRssGlobal)
	{
		$itemsFluxRssGaleries = fluxRssGaleriesTableauBrut($racine, $urlRacine, $langues[0], $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, FALSE);
		
		if (!empty($itemsFluxRssGaleries))
		{
			foreach ($langues as $codeLangue)
			{
				$url = "$urlRacine/rss.php?type=galeries&amp;langue=$codeLangue";
				$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url, FALSE);
				$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
				$tableauUrl[$url]['cache'] = $cheminFichierCache;
				$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
				
				if (count($accueil) > 1)
				{
					foreach ($langues as $code)
					{
						$urlLink = variableGet(1, $url, 'langue', $code);
						$tableauUrl[$url]['link'][$urlLink] = $code;
					}
				}
			}
		}
	}
	
	// Catégorie spéciale: derniers ajouts aux galeries pour chaque langue.
	if ($activerCategoriesGlobales['galeries'])
	{
		foreach ($langues as $codeLangue)
		{
			$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array ('galeries'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
		
			if (!empty($categorie))
			{
				$listeUrl = cronUrlCategorie($racine, $urlRacine, $categorie['galeries'], 'galeries', $nombreArticlesParPageCategorie);
			
				foreach ($listeUrl as $infosUrl)
				{
					$url = $infosUrl['url'];
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$url]['cache'] = $cheminFichierCache;
					$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
					
					if (count($accueil) > 1)
					{
						foreach ($langues as $code)
						{
							$urlLink = variableGet(1, $url, 'langue', $code);
							$tableauUrl[$url]['link'][$urlLink] = $code;
						}
					}
				}
			}
		}
	}
	
	// Catégories.
	if (cheminConfigCategories($racine))
	{
		$categories = super_parse_ini_file(cheminConfigCategories($racine), TRUE);
		
		if (!empty($categories))
		{
			foreach ($categories as $categorie => $categorieInfos)
			{
				$listeUrl = cronUrlCategorie($racine, $urlRacine, $categorieInfos, $categorie, $nombreArticlesParPageCategorie);
				
				foreach ($listeUrl as $infosUrl)
				{
					// Catégorie.
					$url = $infosUrl['url'];
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$url]['cache'] = $cheminFichierCache;
					$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
				}
				
				if (!empty($categorieInfos['pages']))
				{
					foreach ($categorieInfos['pages'] as $page)
					{
						// Pages faisant partie de la catégorie.
						$urlPage = $urlRacine . '/' . $page;
						$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlPage);
						$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
						$tableauUrl[$urlPage]['cache'] = $cheminFichierCache;
						$tableauUrl[$urlPage]['cacheEnTete'] = $cheminFichierCacheEnTete;
					}
				}
				
				if (!empty($categorieInfos['rss']) && $categorieInfos['rss'] == 1)
				{
					// Flux RSS de la catégorie.
					$urlRss = $urlRacine . '/rss.php?type=categorie&amp;id=' . filtreChaine($categorie);
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlRss, FALSE);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$urlRss]['cache'] = $cheminFichierCache;
					$tableauUrl[$urlRss]['cacheEnTete'] = $cheminFichierCacheEnTete;
				}
			}
		}
	}
	
	// Flux RSS global du site.
	if ($activerFluxRssGlobalSite && cheminConfigFluxRssGlobalSite($racine))
	{
		$pages = super_parse_ini_file(cheminConfigFluxRssGlobalSite($racine), TRUE);
		
		if (!empty($pages))
		{
			foreach ($pages as $codeLangue => $langueInfos)
			{
				if (!empty($langueInfos['pages']))
				{
					$url = "$urlRacine/rss.php?type=site&amp;langue=$codeLangue";
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url, FALSE);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$url]['cache'] = $cheminFichierCache;
					$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
					
					foreach ($langueInfos['pages'] as $page)
					{
						// Page faisant partie du flux RSS.
						$urlPage = $urlRacine . '/' . $page;
						$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $urlPage, FALSE);
						$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
						$tableauUrl[$urlPage]['cache'] = $cheminFichierCache;
						$tableauUrl[$urlPage]['cacheEnTete'] = $cheminFichierCacheEnTete;
					}
				}
			}
		}
	}
	
	// Catégorie spéciale: dernières publications pour chaque langue.
	if ($activerCategoriesGlobales['site'])
	{
		foreach ($langues as $codeLangue)
		{
			$categorie = ajouteCategoriesSpeciales($racine, $urlRacine, $codeLangue, array (), array ('site'), $nombreItemsFluxRss, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger);
			
			if (!empty($categorie))
			{
				$listeUrl = cronUrlCategorie($racine, $urlRacine, $categorie['site'], 'site', $nombreArticlesParPageCategorie);
				
				foreach ($listeUrl as $infosUrl)
				{
					$url = $infosUrl['url'];
					$cheminFichierCache = cheminFichierCache($racine, $urlRacine, $url);
					$cheminFichierCacheEnTete = cheminFichierCacheEnTete($cheminFichierCache);
					$tableauUrl[$url]['cache'] = $cheminFichierCache;
					$tableauUrl[$url]['cacheEnTete'] = $cheminFichierCacheEnTete;
				}
			}
		}
	}
	
	return $tableauUrl;
}

/*
Met à jour le fichier de configuration des catégories et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminMajConfigCategories($racine, $contenuFichierTableau)
{
	$messagesScript = '';
	$contenuFichier = '';
	
	foreach ($contenuFichierTableau as $categorie => $categorieInfos)
	{
		if (!empty($categorieInfos['infos']) || !empty($categorieInfos['pages']))
		{
			$contenuFichier .= "[$categorie]\n";
			
			if (!empty($categorieInfos['infos']))
			{
				foreach ($categorieInfos['infos'] as $ligne)
				{
					$contenuFichier .= $ligne;
				}
			}
			
			if (!empty($categorieInfos['pages']))
			{
				foreach ($categorieInfos['pages'] as $ligne)
				{
					$contenuFichier .= $ligne;
				}
			}
			
			$contenuFichier .= "\n";
		}
	}
	
	$cheminFichier = cheminConfigCategories($racine);
	
	if (!$cheminFichier)
	{
		$cheminFichier = cheminConfigCategories($racine, TRUE);
		
		if (!@touch($cheminFichier))
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("La gestion des catégories est impossible puisque le fichier %1\$s n'existe pas, et sa création automatique a échoué. Veuillez créer ce fichier manuellement."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</li>\n";
		}
	}
	
	$messagesScript .= '<li class="contenuFichierPourSauvegarde">';
	
	if (file_exists($cheminFichier))
	{
		if (@file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
		{
			$messagesScript .= '<p>' . T_("Les modifications ont été enregistrées.") . "</p>\n";

			$messagesScript .= '<p class="bDtitre">' . sprintf(T_("Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
		}
		else
		{
			$messagesScript .= '<p class="erreur">' . sprintf(T_("Ouverture du fichier %1\$s impossible."), '<code>' . securiseTexte($cheminFichier) . '</code>') . "</p>\n";
			
			$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
		}
	}
	else
	{
		$messagesScript .= '<p class="bDtitre">' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . "</p>\n";
	}
	
	$messagesScript .= "<div class=\"bDcorps\">\n";
	$messagesScript .= '<pre id="contenuFichierCategories">' . securiseTexte($contenuFichier) . "</pre>\n";
	
	$messagesScript .= "<ul>\n";
	$messagesScript .= "<li><a href=\"javascript:adminSelectionneTexte('contenuFichierCategories');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
	$messagesScript .= "</ul>\n";
	$messagesScript .= "</div><!-- /.bDcorps -->\n";
	$messagesScript .= "</li>\n";
	
	return $messagesScript;
}

/*
Met à jour le fichier de configuration d'une galerie. Retourne FALSE si une erreur survient, sinon retourne TRUE.
*/
function adminMajConfigGalerie($racine, $idDossier, $listeAjouts, $analyserConfig, $exclureMotifsCommeIntermediaires, $analyserSeulementConfig, $parametresNouvellesImages = array ())
{
	$cheminGalerie = $racine . '/site/fichiers/galeries/' . $idDossier;
	$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier);
	
	if (!empty($listeAjouts))
	{
		if ($cheminConfigGalerie)
		{
			$listeExistant = @file_get_contents($cheminConfigGalerie);
			
			if ($listeExistant === FALSE)
			{
				return FALSE;
			}
		}
		else
		{
			$listeExistant = '';
			$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier, TRUE);
		}
		
		if (@file_put_contents($cheminConfigGalerie, $listeAjouts . $listeExistant) === FALSE)
		{
			return FALSE;
		}
	}
	
	$galerieTemp = array ();
	
	if ($cheminConfigGalerie)
	{
		$tableauGalerie = tableauGalerie($cheminConfigGalerie);
		$i = 0;

		foreach ($tableauGalerie as $image)
		{
			// On prend en compte l'image seulement si elle existe encore.
			if (!empty($image['intermediaireNom']) && file_exists($cheminGalerie . '/' . $image['intermediaireNom']) && !adminImageEstDeclaree($image['intermediaireNom'], $galerieTemp, 'intermediaire'))
			{
				$galerieTemp[$i]['intermediaireNom'] = $image['intermediaireNom'];
				
				foreach ($image as $cle => $valeur)
				{
					if ($cle == 'vignetteNom')
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
			}
			
			$i++;
		}
	}
	
	$listeNouveauxFichiers = array ();
	
	if ($fic = @opendir($cheminGalerie))
	{
		while ($fichier = @readdir($fic))
		{
			if (!is_dir($cheminGalerie . '/' . $fichier))
			{
				$typeMime = typeMime($cheminGalerie . '/' . $fichier);
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
	
	$dateAjout = date('Y-m-d H:i');
	
	foreach ($listeNouveauxFichiers as $nouveauFichier)
	{
		$parametres = array ();
		$parametres['intermediaireNom'] = $nouveauFichier;
		
		foreach ($parametresNouvellesImages as $parametre => $valeur)
		{
			$parametres[$parametre] = $valeur;
		}
		
		if (!array_key_exists('dateAjout', $parametres))
		{
			$parametres['dateAjout'] = $dateAjout;
		}
		
		array_unshift($galerieTemp, $parametres);
	}
	
	unset($listeNouveauxFichiers);
	
	$contenuConfig = '';
	
	foreach ($galerieTemp as $image)
	{
		$contenuConfigTemp = '';
		
		foreach ($image as $cle => $valeur)
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
		$cheminConfigGalerie = cheminConfigGalerie($racine, $idDossier, TRUE);
	}
	
	if (@file_put_contents($cheminConfigGalerie, $contenuConfig) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/*
Met à jour le fichier de configuration des galeries. Retourne FALSE si une erreur survient, sinon retourne TRUE.
*/
function adminMajConfigGaleries($racine, $listeModifs)
{
	$listeGaleries = listeGaleries($racine);
	
	foreach ($listeModifs as $idGalerie => $infosGalerie)
	{
		if (isset($listeGaleries[$idGalerie]))
		{
			if (empty($infosGalerie))
			{
				unset($listeGaleries[$idGalerie]);
			}
			else
			{
				$listeGaleries[$idGalerie] = $infosGalerie;
			}
		}
		elseif (!empty($infosGalerie))
		{
			$listeGaleries[$idGalerie] = $infosGalerie;
		}
	}
	
	$cheminConfigGaleries = cheminConfigGaleries($racine, TRUE);
	
	if (!file_exists($cheminConfigGaleries) && !@touch($cheminConfigGaleries))
	{
		return FALSE;
	}
	
	$contenuConfig = '';
	
	foreach ($listeGaleries as $idGalerie => $infosGalerie)
	{
		$contenuConfig .= "[$idGalerie]\n";
		
		foreach ($infosGalerie as $cle => $valeur)
		{
			$contenuConfig .= "$cle=$valeur\n";
		}
		
		$contenuConfig .= "\n";
	}
	
	if (@file_put_contents($cheminConfigGaleries, $contenuConfig) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/*
Retourne la transcription en texte d'une erreur `$_FILES['fichier']['error']` sous forme de message concaténable dans `$messagesScript`.
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
			$messageErreur = T_("Le fichier téléchargé excède la taille de <code>upload_max_filesize</code>, configurée dans le <code>php.ini</code>.");
			break;
			
		case 2:
			$messageErreur = T_("Le fichier téléchargé excède la taille de <code>MAX_FILE_SIZE</code>, qui a été spécifiée dans le formulaire HTML.");
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

/*
Retourne les messages à afficher dans une chaîne formatée. Si le titre est vide, ne retourne qu'une liste de messages, sinon retourne une division de classe `sousBoite` contenant un titre de troisième niveau et la liste des messages.
*/
function adminMessagesScript($messagesScript, $titre = '')
{
	$messagesScriptFinaux = '';
	
	if (!empty($titre))
	{
		$messagesScriptFinaux .= '<div class="sousBoite">' . "\n";
		$messagesScriptFinaux .= "<h3>$titre</h3>\n";
	}
	
	if (!empty($messagesScript))
	{
		$messagesScriptFinaux .= "<ul>\n";
		$messagesScriptFinaux .= $messagesScript;
		$messagesScriptFinaux .= "</ul>\n";
	}
	
	if (!empty($titre))
	{
		$messagesScriptFinaux .= "</div><!-- /.sousBoite -->\n";
	}
	
	return $messagesScriptFinaux;
}

/*
Simule `mkdir()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminMkdir($fichier, $permissions, $recursivite = FALSE)
{
	if (@mkdir($fichier, $permissions, $recursivite))
	{
		return '<li>' . sprintf(T_("Création du dossier %1\$s effectuée."), '<code>' . securiseTexte($fichier) . '</code>') . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Création du dossier %1\$s impossible."), '<code>' . securiseTexte($fichier) . '</code>') . "</li>\n";
	}
}

/*
Retourne `TRUE` s'il faut afficher les options de catégories et de flux RSS pour le fichier édité dans le porte-documents, sinon retourne `FALSE`.
*/
function adminOptionsFichierPorteDocuments($urlRacineAdmin, $urlFichierEdite)
{
	return preg_match('/(?<!\.inc)\.php$/', $urlFichierEdite) && strpos($urlFichierEdite, $urlRacineAdmin) !== 0;
}

/*
Retourne un tableau de deux éléments tableau dont chaque élément contient le nom d'un paramètre d'une image de galerie. Le premier tableau contient les paramètres les plus utilisés; le second, ceux qui le sont moins.
*/
function adminParametresImage()
{
	return array (
		array (
			'titre',
			'intermediaireLegende',
			'exclure',
		),
		array (
			'id',
			'licence',
			'originalNom',
			'vignetteNom',
			'vignetteLargeur',
			'vignetteHauteur',
			'vignetteAlt',
			'vignetteAttributTitle',
			'intermediaireLargeur',
			'intermediaireHauteur',
			'intermediaireAlt',
			'intermediaireAttributTitle',
			'pageIntermediaireBaliseTitle',
			'pageIntermediaireDescription',
			'pageIntermediaireMotsCles',
			'auteurAjout',
			'dateAjout',
			'commentaire',
		),
	);
}

/*
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

/*
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

/*
Retourne un plan modèle de fichier Sitemap (du site ou des galeries) au format XML. Si `$fermeUrlset` vaut FALSE, la balise fermante de `urlset` ne sera pas incluse dans le modèle retourné.
*/
function adminPlanSitemapXml($fermeUrlset = TRUE)
{
	$plan = '';
	$plan .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$plan .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
	
	if ($fermeUrlset)
	{
		$plan .= '</urlset>';
	}
	
	return $plan;
}

/*
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

/*
Simule `rename()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`. Si `$messageDeplacement` vaut TRUE, le message retourné présente l'action effectuée comme étant un déplacement, sinon présente l'action comme étant un renommage.
*/
function adminRename($ancienNom, $nouveauNom, $messageDeplacement = FALSE)
{
	if (@rename($ancienNom, $nouveauNom))
	{
		if ($messageDeplacement)
		{
			return '<li>' . sprintf(T_("Déplacement de %1\$s vers %2\$s effectué."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
		}
		else
		{
			return '<li>' . sprintf(T_("Renommage de %1\$s en %2\$s effectué."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
		}
	}
	else
	{
		if ($messageDeplacement)
		{
			return '<li class="erreur">' . sprintf(T_("Déplacement de %1\$s vers %2\$s impossible."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
		}
		else
		{
			return '<li class="erreur">' . sprintf(T_("Renommage de %1\$s en %2\$s impossible."), '<code>' . securiseTexte($ancienNom) . '</code>', '<code>' . securiseTexte($nouveauNom) . '</code>') . "</li>\n";
		}
	}
}

/*
Simule `rmdir()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminRmdir($dossier)
{
	if (@rmdir($dossier))
	{
		return '<li>' . sprintf(T_("Suppression de %1\$s effectuée."), '<code>' . securiseTexte($dossier) . '</code>') . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Suppression de %1\$s impossible."), '<code>' . securiseTexte($dossier) . '</code>') . "</li>\n";
	}
}

/*
Supprime un dossier ainsi que son contenu et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminRmdirRecursif($dossierAsupprimer)
{
	$messagesScript = '';
	
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
						$messagesScript .= adminUnlink("$dossierAsupprimer/$fichier");
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
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Accès au dossier %1\$s impossible."), '<code>' . securiseTexte($dossierAtraiter) . '</code>') . "</li>\n";
			}
		}
		
		if (adminDossierEstVide($dossierAsupprimer))
		{
			$messagesScript .= adminRmdir($dossierAsupprimer);
		}
	}
	
	return $messagesScript;
}

/*
Si nécessaire, effectue une rotation automatique et sans perte de qualité d'une image JPG. La rotation à effectuer est trouvée à partir de l'orientation déclarée dans les données Exif, si cette information existe. Si `$supprimerExif` vaut TRUE, tente de supprimer les données Exif. Retourne le résultat de l'opération sous forme de message concaténable dans `$messagesScript`.

La vérification du type MIME de l'image n'est pas effectuée, donc la fonction suppose que l'image dont le chemin est passé en paramètre est de type MIME `image/jpeg`. Aussi, pour que la rotation puisse avoir lieu, une des deux configurations suivantes doit être vérifiée:

- accès à l'exécutable `exiftran`, dont le chemin est passé en paramètre dans la variable `$cheminExiftran`;
- accès à l'exécutable `jpegtran`, dont le chemin est passé en paramètre dans la variable `$cheminJpegtran`, ainsi qu'à la fonction PHP `exif_read_data()`.

Si `exiftran` est exécutable, il sera utilisé en priorité.

Fonction inspirée au départ par `acidfree_rotate_image()`, fonction présente dans le fichier `image_manip.inc` du module Acidfree Albums pour Drupal (<http://drupal.org/project/acidfree>).
*/
function adminRotationJpegSansPerte($cheminImage, $cheminExiftran, $cheminJpegtran, $supprimerExif)
{
	$messagesScript = '';
	$cheminEchapeImage = adminSuperEscapeshellarg($cheminImage);
	$suppressionExifGeree = FALSE;
	
	if (function_exists('exif_read_data'))
	{
		$exif = @exif_read_data($cheminImage);
		
		if (!empty($exif['IFD0']['Orientation']))
		{
			$orientation = $exif['IFD0']['Orientation'];
		}
		elseif (!empty($exif['Orientation']))
		{
			$orientation = $exif['Orientation'];
		}
	}
	
	if (isset($orientation) && $orientation == 1)
	{
		$messagesScript .= '<li>' . sprintf(T_("Aucune rotation automatique à effectuer pour l'image %1\$s."), '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
	}
	elseif (is_executable($cheminExiftran))
	{
		exec("$cheminExiftran -aip $cheminEchapeImage", $sortie, $ret);
		
		if (!$ret)
		{
			$messagesScript .= '<li>' . sprintf(T_("Rotation automatique et sans perte de qualité effectuée par %1\$s pour l'image %2\$s."), '<code>exiftran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Rotation automatique et sans perte de qualité par %1\$s impossible pour l'image %2\$s. Vérifier l'état de l'image sur le serveur."), '<code>exiftran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
		}
	}
	elseif (is_executable($cheminJpegtran) && isset($orientation))
	{
		$parametresJpegtran = '';
		
		// Merci à <http://ca.php.net/manual/fr/function.exif-read-data.php#76964> pour l'analyse de l'orientation.
		switch($orientation)
		{
			case 2:
				$parametresJpegtran = '-flip horizontal';
				break;
			
			case 3:
				$parametresJpegtran = '-rotate 180';
				break;
			
			case 4:
				$parametresJpegtran = '-flip vertical';
				break;
			
			case 5:
				$parametresJpegtran = '-flip vertical -rotate 90';
				break;
			
			case 6:
				$parametresJpegtran = '-rotate 90';
				break;
			
			case 7:
				$parametresJpegtran = '-flip horizontal -rotate 90';
				break;
			
			case 8:
				$parametresJpegtran = '-rotate 270';
				break;
		}
		
		if ($supprimerExif)
		{
			$valeurParametreCopy = 'none';
		}
		else
		{
			$valeurParametreCopy = 'all';
		}
		
		$cheminImageTmp = tempnam(dirname($cheminImage), 'jpg');
		$cheminEchapeImageTmp = adminSuperEscapeshellarg($cheminImageTmp);
		exec("$cheminJpegtran -copy $valeurParametreCopy $parametresJpegtran -outfile $cheminEchapeImageTmp $cheminEchapeImage", $sortie, $ret);
		$suppressionExifGeree = TRUE;
		
		if (!$ret && @copy($cheminImageTmp, $cheminImage))
		{
			$messagesScript .= '<li>' . sprintf(T_("Rotation automatique et sans perte de qualité effectuée par %1\$s pour l'image %2\$s."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
			
			if ($supprimerExif)
			{
				$messagesScript .= '<li>' . sprintf(T_("Suppression sans perte de qualité des données Exif effectuée par %1\$s pour l'image %2\$s."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
			}
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Rotation automatique et sans perte de qualité par %1\$s impossible pour l'image %2\$s. Vérifier l'état de l'image sur le serveur."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
			
			if ($supprimerExif)
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Suppression sans perte de qualité des données Exif par %1\$s impossible pour l'image %2\$s. Vérifier l'état de l'image sur le serveur."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
			}
		}
		
		@unlink($cheminImageTmp);
	}
	else
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Votre environnement ne permet pas d'effectuer une rotation automatique et sans perte de qualité. La configuration nécessaire est soit un accès à l'exécutable %1\$s, soit un accès à l'exécutable %2\$s ainsi qu'à la fonction PHP %3\$s."), '<code>exiftran</code>', '<code>jpegtran</code>', '<code>exif_read_data()</code>') . "</li>\n";
	}
	
	if ($supprimerExif && !$suppressionExifGeree)
	{
		$messagesScript .= adminSupprimeExif($cheminImage, $cheminJpegtran);
	}
	
	return $messagesScript;
}

/*
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

/*
Reproduit la fonction `escapeshellarg()`, mais sans dépendre de la locale. Par exemple, les caractères non supportés par la locale ne sont pas supprimés.
*/
function adminSuperEscapeshellarg($arg)
{
	return "'" . str_replace("'", "'\''", $arg) . "'";
}

/*
Supprime les données Exif d'une image JPG. L'opération est sans perte de qualité. Retourne le résultat de l'opération sous forme de message concaténable dans `$messagesScript`.

La vérification du type MIME de l'image n'est pas effectuée, donc la fonction suppose que l'image dont le chemin est passé en paramètre est de type MIME `image/jpeg`. Aussi, pour que la suppression puisse avoir lieu, l'exécutable `jpegtran` (dont le chemin est passé en paramètre dans la variable `$cheminJpegtran`) doit être accessible.
*/
function adminSupprimeExif($cheminImage, $cheminJpegtran)
{
	$messagesScript = '';
	
	if (is_executable($cheminJpegtran))
	{
		$cheminEchapeImage = adminSuperEscapeshellarg($cheminImage);
		$cheminImageTmp = tempnam(dirname($cheminImage), 'jpg');
		$cheminEchapeImageTmp = adminSuperEscapeshellarg($cheminImageTmp);
		exec("$cheminJpegtran -copy none -outfile $cheminEchapeImageTmp $cheminEchapeImage", $sortie, $ret);
		
		if (!$ret && @copy($cheminImageTmp, $cheminImage))
		{
			$messagesScript .= '<li>' . sprintf(T_("Suppression sans perte de qualité des données Exif effectuée par %1\$s pour l'image %2\$s."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
		}
		else
		{
			$messagesScript .= '<li class="erreur">' . sprintf(T_("Suppression sans perte de qualité des données Exif par %1\$s impossible pour l'image %2\$s. Vérifier l'état de l'image sur le serveur."), '<code>jpegtran</code>', '<code>' . securiseTexte($cheminImage) . '</code>') . "</li>\n";
		}
		
		@unlink($cheminImageTmp);
	}
	else
	{
		$messagesScript .= '<li class="erreur">' . sprintf(T_("Votre environnement ne permet pas d'effectuer une suppression sans perte de qualité des données Exif. La configuration nécessaire est un accès à l'exécutable %1\$s."), '<code>jpegtran</code>') . "</li>\n";
	}
	
	return $messagesScript;
}

/*
Transforme un tableau de chemins en tableau de chemins canoniques, et retourne le tableau résultant.
*/
function adminTableauCheminsCanoniques($tableauChemins)
{
	$tableauCheminsCanoniques = array ();
	
	foreach ($tableauChemins as $chemin)
	{
		$chemin = realpath($chemin);
		
		if ($chemin !== FALSE)
		{
			$tableauCheminsCanoniques[] = $chemin;
		}
	}
	
	return $tableauCheminsCanoniques;
}

/*
Convertit un tableau représentant la configuration des commentaires d'une page vers un contenu texte enregistrable dans le fichier de configuration en question. Retourne un tableau de deux éléments:

	array ('config' => $contenuFichier, 'messagesScript' => $messagesScript)
*/
function adminTableauConfigCommentairesVersTexte($racine, $commentairesChampsObligatoires, $moderationCommentaires, $tableauConfig)
{
	$messagesScript = '';
	$contenuFichier = '';
	
	foreach ($tableauConfig as $idCommentaire => $infosCommentaire)
	{
		$message = '';
		
		if (!empty($infosCommentaire['message']) && is_array($infosCommentaire['message']))
		{
			foreach ($infosCommentaire['message'] as $ligneMessage)
			{
				$message .= "message[]=$ligneMessage\n";
			}
		}
		
		if (!empty($message))
		{
			$contenuFichier .= "[$idCommentaire]\n";
			
			// IP.
			
			$contenuFichier .= 'ip=';
			
			if (!empty($infosCommentaire['ip']))
			{
				// Le motif exact pour les adresses IP est beaucoup plus complexe, mais on ne vérifie ici que la forme générale.
				if (!preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $infosCommentaire['ip']))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: l'adresse IP %1\$s ne semble pas avoir une forme valide."), '<code>' . $infosCommentaire['ip'] . '</code>') . "</li>\n";
				}
				
				$contenuFichier .= $infosCommentaire['ip'];
			}
			
			$contenuFichier .= "\n";
			
			// Date.
			
			$contenuFichier .= 'date=';
			
			if (!empty($infosCommentaire['date']))
			{
				if (!preg_match('/^\d+$/', $infosCommentaire['date']))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: la date %1\$s ne semble pas avoir une forme valide."), '<code>' . $infosCommentaire['date'] . '</code>') . "</li>\n";
				}
				
				$contenuFichier .= $infosCommentaire['date'];
			}
			
			$contenuFichier .= "\n";
			
			// Nom.
			
			$contenuFichier .= 'nom=';
			
			if (!empty($infosCommentaire['nom']))
			{
				$contenuFichier .= $infosCommentaire['nom'];
			}
			elseif ($commentairesChampsObligatoires['nom'])
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: selon la configuration des commentaires, le nom est obligatoire, mais celui associé au commentaire %1\$s est vide."), '<code>' . $idCommentaire . '</code>') . "</li>\n";
			}
			
			$contenuFichier .= "\n";
			
			// Courriel.
			
			$contenuFichier .= 'courriel=';
			
			if (!empty($infosCommentaire['courriel']))
			{
				if (!courrielValide($infosCommentaire['courriel']))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: le courriel %1\$s ne semble pas avoir une forme valide."), '<code>' . $infosCommentaire['courriel'] . '</code>') . "</li>\n";
				}
				
				$contenuFichier .= $infosCommentaire['courriel'];
			}
			elseif ($commentairesChampsObligatoires['courriel'])
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: selon la configuration des commentaires, le courriel est obligatoire, mais celui associé au commentaire %1\$s est vide."), '<code>' . $idCommentaire . '</code>') . "</li>\n";
			}
			
			$contenuFichier .= "\n";
			
			// Site.
			
			$contenuFichier .= 'site=';
			
			if (!empty($infosCommentaire['site']))
			{
				if (!siteWebValide($infosCommentaire['site']))
				{
					$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: le site Web %1\$s ne semble pas avoir une forme valide."), '<code>' . $infosCommentaire['site'] . '</code>') . "</li>\n";
				}
				
				$contenuFichier .= $infosCommentaire['site'];
			}
			elseif ($commentairesChampsObligatoires['site'])
			{
				$messagesScript .= '<li class="erreur">' . sprintf(T_("Avertissement: selon la configuration des commentaires, le site est obligatoire, mais celui associé au commentaire %1\$s est vide."), '<code>' . $idCommentaire . '</code>') . "</li>\n";
			}
			
			$contenuFichier .= "\n";
			
			// Notification.
			
			$contenuFichier .= 'notification=';
			
			if (isset($infosCommentaire['notification']) && $infosCommentaire['notification'] == 1)
			{
				$contenuFichier .= 1;
			}
			else
			{
				$contenuFichier .= 0;
			}
			
			$contenuFichier .= "\n";
			
			// Langue de la page.
			
			$contenuFichier .= 'languePage=';
			
			if (!empty($infosCommentaire['languePage']))
			{
				$contenuFichier .= $infosCommentaire['languePage'];
			}
			
			$contenuFichier .= "\n";
			
			// En attente de modération.
			
			$contenuFichier .= 'enAttenteDeModeration=';
			
			if (!isset($infosCommentaire['enAttenteDeModeration']))
			{
				if ($moderationCommentaires)
				{
					$infosCommentaire['enAttenteDeModeration'] = 1;
				}
				else
				{
					$infosCommentaire['enAttenteDeModeration'] = 0;
				}
			}
			
			if ($infosCommentaire['enAttenteDeModeration'] == 1)
			{
				$contenuFichier .= 1;
			}
			else
			{
				$contenuFichier .= 0;
			}
			
			$contenuFichier .= "\n";
			
			// Afficher.
			
			$contenuFichier .= 'afficher=';
			
			if (!isset($infosCommentaire['afficher']))
			{
				if ($moderationCommentaires)
				{
					$infosCommentaire['afficher'] = 0;
				}
				else
				{
					$infosCommentaire['afficher'] = 1;
				}
			}
			
			if ($infosCommentaire['afficher'] == 1)
			{
				$contenuFichier .= 1;
			}
			else
			{
				$contenuFichier .= 0;
			}
			
			$contenuFichier .= "\n";
			
			// Message.
			$contenuFichier .= $message;
			
			$contenuFichier .= "\n";
		}
	}
	
	return array ('config' => $contenuFichier, 'messagesScript' => $messagesScript);
}

/*
Retourne la taille en octets du dossier de cache de l'administration si le dossier est accessible, sinon retourne FALSE.
*/
function adminTailleCache($racineAdmin)
{
	$racine = dirname($racineAdmin);
	$dossierAdmin = superBasename($racineAdmin);
	$cheminCache = $racine . '/site/' . $dossierAdmin . '/cache';
	$taille = 0;
	
	if ($dossier = @opendir($cheminCache))
	{
		while (($fichier = @readdir($dossier)) !== FALSE)
		{
			if (!is_dir($cheminCache . '/' . $fichier))
			{
				$taille += @filesize($cheminCache . '/' . $fichier);
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

/*
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

/*
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

/*
Simule `unlink()` et retourne le résultat sous forme de message concaténable dans `$messagesScript`.
*/
function adminUnlink($fichier)
{
	if (@unlink($fichier))
	{
		return '<li>' . sprintf(T_("Suppression de %1\$s effectuée."), '<code>' . securiseTexte($fichier) . '</code>') . "</li>\n";
	}
	else
	{
		return '<li class="erreur">' . sprintf(T_("Suppression de %1\$s impossible."), '<code>' . securiseTexte($fichier) . '</code>') . "</li>\n";
	}
}

/*
Retourne l'URL de déconnexion de la section d'administration.
*/
function adminUrlDeconnexion($urlRacine)
{
	list ($protocole, $url) = explode('://', $urlRacine, 2);
	
	return "$protocole://deconnexion@$url/deconnexion.php";
}

/*
Retourne l'URL du fichier édité dans le porte-documents.
*/
function adminUrlFichierEditePorteDocuments($racine, $urlRacine, $cheminFichier)
{
	$urlFichierEdite = realpath($cheminFichier);
	$urlFichierEdite = preg_replace('#^' . preg_quote($racine, '#') . '/?#', '', $urlFichierEdite);
	$urlFichierEdite = $urlRacine . '/' . encodeTexte($urlFichierEdite);
	
	return $urlFichierEdite;
}

/*
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
			$tableauGalerie = tableauGalerie($cheminConfigGalerie);
			
			foreach ($tableauGalerie as $image)
			{
				if ($image['intermediaireNom'] == $nomImage)
				{
					return 'intermediaire';
				}
				elseif (isset($image['vignetteNom']) && $image['vignetteNom'] == $nomImage)
				{
					return 'vignette';
				}
				elseif (isset($image['originalNom']) && $image['originalNom'] == $nomImage)
				{
					return 'original';
				}
				elseif (preg_match('/(.+)-vignette(\.[^\.]+)$/', $nomImage, $nomConstruit))
				{
					$nomConstruitIntermediaire = $nomConstruit[1] . $nomConstruit[2];
					
					if ($image['intermediaireNom'] == $nomConstruitIntermediaire)
					{
						return 'vignette';
					}
				}
				elseif (preg_match('/(.+)-original(\.[^\.]+)$/', $nomImage, $nomConstruit))
				{
					$nomConstruitIntermediaire = $nomConstruit[1] . $nomConstruit[2];
					
					if ($image['intermediaireNom'] == $nomConstruitIntermediaire)
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

/*
Retourne la version de Squeletml. Si aucune version n'est trouvée, retourne une chaîne vide.
*/
function adminVersionSqueletml($cheminFichierVersionTxt)
{
	$version = '';
	$contenuFichierVersionTxt = @file_get_contents($cheminFichierVersionTxt);
	
	if (preg_match('/^(.+) \(\d{4}(-\d{2}){2}\)$/', $contenuFichierVersionTxt, $resultat))
	{
		$version = $resultat[1];
	}
	elseif (preg_match('/^.+\+$/', $contenuFichierVersionTxt, $resultat))
	{
		$version = $resultat[0];
	}
	
	return $version;
}

/*
Vide le cache de l'administration (si `$type` vaut `admin`) ou du site (si `$type` vaut `site`). Seuls les fichiers à la racine du dossier sont supprimés, ce qui constitue tout ce qui est mis en cache par Squeletml. Retourne FALSE si une erreur survient, sinon retourne TRUE.
*/
function adminVideCache($racineAdmin, $type)
{
	$sansErreur = TRUE;
	
	if ($type == 'admin')
	{
		$racine = dirname($racineAdmin);
		$dossierAdmin = superBasename($racineAdmin);
		$cheminCache = $racine . '/site/' . $dossierAdmin . '/cache';
	}
	elseif ($type == 'site')
	{
		$cheminCache = dirname($racineAdmin) . '/site/cache';
	}
	else
	{
		$sansErreur = FALSE;
	}
	
	if ($sansErreur)
	{
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
	}
	
	return $sansErreur;
}
?>
