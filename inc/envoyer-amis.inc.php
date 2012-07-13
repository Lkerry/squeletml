<?php
/*
Ce fichier crée les variables nécessaires à l'incorporation au formulaire de contact du module «Envoyer à des amis». Aucun code XHTML n'est envoyé au navigateur.
*/

if (!$erreur404 && !$estPageDerreur)
{
	if (!isset($messageEnvoye))
	{
		$messageEnvoye = FALSE;
	}
	
	if (isset($_GET['action']) && $_GET['action'] == 'envoyerAmis')
	{
		if (!empty($_POST['nom']) && !$messageEnvoye)
		{
			$nom = securiseTexte($_POST['nom']);
		}
		else
		{
			$nom = T_("VOTRE NOM");
		}

		if (!empty($_POST['message']) && !$messageEnvoye)
		{
			$petitMot = '<p>' . sprintf(T_("Aussi, %1\$s vous a écrit un petit mot personnalisé, que vous pouvez lire ci-dessous:"), $nom) . '</p><blockquote>' . nl2br(securiseTexte($_POST['message'])) . "</blockquote>\n";
		}
		else
		{
			$petitMot = '';
		}
		
		$messageEnvoyerAmis = '';
	}
	
	$cheminConfigGalerie = cheminConfigGalerie($racine, $idGalerieDossier);
	
	if (!empty($idGalerie) && isset($_GET['image']) && $cheminConfigGalerie)
	{
		$tableauGalerie = tableauGalerie($cheminConfigGalerie, TRUE);
		$i = 0;
		
		foreach($tableauGalerie as $image)
		{
			$id = idImage($racine, $image);
			
			if (filtreChaine($racine, $id) == sansEchappement($_GET['image']))
			{
				$envoyerAmisEstActif = TRUE;
				
				if (isset($_GET['action']) && $_GET['action'] == 'envoyerAmis')
				{
					$envoyerAmisInclureContact = TRUE;
					$messageEnvoyerAmisSupplement = envoyerAmisSupplementImage($urlRacine, $idGalerieDossier, $image, $galerieLegendeMarkdown);
					$titreImage = titreImage($image);
					$messageEnvoyerAmis = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir l'image %3\$s, qui fait partie de la galerie %4\$s:"), $nom, '<code>' . ACCUEIL . '</code>', '<em>' . $titreImage . '</em>', '<em>' . $idGalerie . '</em>') . "</p>\n" . $messageEnvoyerAmisSupplement . $petitMot;
				}
				
				break;
			}
		}
	}
	elseif (!empty($idGalerie) && !isset($_GET['image']) && $cheminConfigGalerie)
	{
		$envoyerAmisEstActif = TRUE;
		
		if (isset($_GET['action']) && $_GET['action'] == 'envoyerAmis')
		{
			$envoyerAmisInclureContact = TRUE;
			$corpsMinivignettes = "<div>\n";
			$corpsMinivignettes .= '<ul style="list-style-type: none; margin: 0px; padding: 0px;">' . "\n";
			$tableauGalerie = tableauGalerie($cheminConfigGalerie, TRUE);
			$i = 0;
			
			foreach($tableauGalerie as $image)
			{
				if (!empty($image['vignetteNom']))
				{
					$vignetteNom = $image['vignetteNom'];
				}
				else
				{
					$vignetteNom = nomSuffixe($image['intermediaireNom'], '-vignette');
				}
				
				$cheminImg = $racine;
				$urlImg = $urlRacine;
				
				if ($idGalerieDossier != 'demo')
				{
					$cheminImg .= '/site';
					$urlImg .= '/site';
				}
				
				$cheminImg .= '/fichiers/galeries/' . $idGalerieDossier . '/' . $vignetteNom;
				$urlImg .= '/fichiers/galeries/' . rawurlencode($idGalerieDossier) . '/' . rawurlencode($vignetteNom);
				list ($imgWidth, $imgHeight) = getimagesize($cheminImg);
				$imgAlt = envoyerAmisSupplementImageAlt($image);
				$corpsMinivignettes .= "<li><img style=\"float: left; border: 1px solid #cccccc; margin: 2px;\" src=\"$urlImg\" width=\"$imgWidth\" height=\"$imgHeight\" alt=\"$imgAlt\" /></li>\n";
				$i++;
				
				if ($i >= 5)
				{
					break;
				}
			}
			
			$corpsMinivignettes .= "</div>\n";
			
			$corpsMinivignettes .= '<div style="clear: both; margin-bottom: 20px;"></div>' . "\n";
			
			$messageEnvoyerAmisSupplement = envoyerAmisSupplementPage('', '', $corpsMinivignettes);
			$messageEnvoyerAmis = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la galerie %3\$s:"), $nom, '<code>' . ACCUEIL . '</code>', '<em>' . $idGalerie . '</em>') . "</p>\n" . $messageEnvoyerAmisSupplement . $petitMot;
		}
	}
	elseif (empty($courrielContact))
	{
		$envoyerAmisEstActif = TRUE;
		
		if (isset($_GET['action']) && $_GET['action'] == 'envoyerAmis')
		{
			$envoyerAmisInclureContact = TRUE;
			
			if (!empty($idCategorie))
			{
				$messageEnvoyerAmisSupplement = publicationsRecentes($racine, $urlRacine, $langueParDefaut, $langue, 'categorie', $idCategorie, 5, FALSE, FALSE, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $dureeCache);
				$messageEnvoyerAmisSupplement = envoyerAmisSupplementPage('', '', $messageEnvoyerAmisSupplement);
				$messageEnvoyerAmis = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la catégorie %3\$s:"), $nom, '<code>' . ACCUEIL . '</code>', '<em>' . $idCategorie . '</em>') . "</p>\n" . $messageEnvoyerAmisSupplement . $petitMot;
			}
			else
			{
				$messageEnvoyerAmisSupplement = envoyerAmisSupplementPage($description, $baliseTitle . $baliseTitleComplement);
				$messageEnvoyerAmis = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la page suivante:"), $nom, '<code>' . ACCUEIL . '</code>') . "</p>\n" . $messageEnvoyerAmisSupplement . $petitMot;
			}
		}
	}
}

// Empêcher le contenu dupliqué dans les moteurs de recherche.
if ($envoyerAmisEstActif && $envoyerAmisInclureContact)
{
	$robots = 'noindex, follow, noarchive';
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/envoyer-amis.inc.php'))
{
	include $racine . '/site/inc/envoyer-amis.inc.php';
}
?>
