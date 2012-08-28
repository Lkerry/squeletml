<?php
/*
Ce fichier crée les variables nécessaires à l'incorporation au formulaire de contact du module de partage (par courriel). Aucun code XHTML n'est envoyé au navigateur.
*/

if (!$erreur404 && !$estPageDerreur)
{
	if (!isset($messageEnvoye))
	{
		$messageEnvoye = FALSE;
	}
	
	if (isset($_GET['action']) && $_GET['action'] == 'partageCourriel')
	{
		if (isset($_POST['nom']))
		{
			$nom = securiseTexte(trim($_POST['nom']));
		}
		
		if (empty($nom))
		{
			$nom = T_("VOTRE NOM");
		}
		
		$message = '';
		
		if (isset($_POST['message']))
		{
			$message = securiseTexte(trim($_POST['message']));
		}
		
		if (!empty($message) && !$messageEnvoye)
		{
			$petitMot = '<p>' . sprintf(T_("Aussi, %1\$s vous a écrit un petit mot personnalisé, que vous pouvez lire ci-dessous:"), $nom) . '</p><blockquote>' . nl2br($message) . "</blockquote>\n";
		}
		else
		{
			$petitMot = '';
		}
		
		$messagePartageCourriel = '';
	}
	
	$cheminConfigGalerie = cheminConfigGalerie($racine, $idGalerieDossier);
	
	if (!empty($idGalerie) && isset($_GET['image']) && $cheminConfigGalerie)
	{
		$tableauGalerie = tableauGalerie($cheminConfigGalerie, TRUE);
		$i = 0;
		
		foreach($tableauGalerie as $image)
		{
			if (idImage($image) == $_GET['image'])
			{
				$partageCourrielActif = TRUE;
				
				if (isset($_GET['action']) && $_GET['action'] == 'partageCourriel')
				{
					$partageCourrielInclureContact = TRUE;
					$messagePartageCourrielSupplement = partageCourrielSupplementImage($urlRacine, $idGalerieDossier, $image, $galerieLegendeMarkdown);
					$titreImage = titreImage($image);
					$messagePartageCourriel = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir l'image %3\$s, qui fait partie de la galerie %4\$s:"), $nom, '<code>' . securiseTexte(ACCUEIL) . '</code>', '<em>' . securiseTexte($titreImage) . '</em>', '<em>' . securiseTexte($idGalerie) . '</em>') . "</p>\n" . $messagePartageCourrielSupplement . $petitMot;
				}
				
				break;
			}
		}
	}
	elseif (!empty($idGalerie) && !isset($_GET['image']) && $cheminConfigGalerie)
	{
		$partageCourrielActif = TRUE;
		
		if (isset($_GET['action']) && $_GET['action'] == 'partageCourriel')
		{
			$partageCourrielInclureContact = TRUE;
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
				$urlImg .= '/fichiers/galeries/' . encodeTexte("$idGalerieDossier/$vignetteNom");
				list ($imgWidth, $imgHeight) = @getimagesize($cheminImg);
				$imgAlt = partageCourrielSupplementImageAlt($image);
				$corpsMinivignettes .= "<li><img style=\"float: left; border: 1px solid #cccccc; margin: 2px;\" src=\"$urlImg\" width=\"$imgWidth\" height=\"$imgHeight\" alt=\"$imgAlt\" /></li>\n";
				$i++;
				
				if ($i >= 5)
				{
					break;
				}
			}
			
			$corpsMinivignettes .= "</ul>\n";
			$corpsMinivignettes .= "</div>\n";
			
			$corpsMinivignettes .= '<div style="clear: both; margin-bottom: 20px;"></div>' . "\n";
			
			$messagePartageCourrielSupplement = partageCourrielSupplementPage('', '', $corpsMinivignettes);
			$messagePartageCourriel = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la galerie %3\$s:"), $nom, '<code>' . securiseTexte(ACCUEIL) . '</code>', '<em>' . securiseTexte($idGalerie) . '</em>') . "</p>\n" . $messagePartageCourrielSupplement . $petitMot;
		}
	}
	elseif (empty($courrielContact))
	{
		$partageCourrielActif = TRUE;
		
		if (isset($_GET['action']) && $_GET['action'] == 'partageCourriel')
		{
			$partageCourrielInclureContact = TRUE;
			
			if (!empty($idCategorie))
			{
				$messagePartageCourrielSupplement = publicationsRecentes($racine, $urlRacine, $langue, 'categorie', $idCategorie, 5, FALSE, FALSE, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLienOriginalTelecharger, $dureeCache);
				$messagePartageCourrielSupplement = partageCourrielSupplementPage('', '', $messagePartageCourrielSupplement);
				$messagePartageCourriel = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la catégorie %3\$s:"), $nom, '<code>' . securiseTexte(ACCUEIL) . '</code>', '<em>' . securiseTexte($idCategorie) . '</em>') . "</p>\n" . $messagePartageCourrielSupplement . $petitMot;
			}
			else
			{
				$messagePartageCourrielSupplement = partageCourrielSupplementPage($description, $baliseTitle . $baliseTitleComplement);
				$messagePartageCourriel = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la page suivante:"), $nom, '<code>' . securiseTexte(ACCUEIL) . '</code>') . "</p>\n" . $messagePartageCourrielSupplement . $petitMot;
			}
		}
	}
}

// Empêcher le contenu dupliqué dans les moteurs de recherche.
if ($partageCourrielActif && $partageCourrielInclureContact && $premierOuDernier == 'premier')
{
	$robots = 'noindex, follow, noarchive';
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/partage-courriel.inc.php'))
{
	include $racine . '/site/inc/partage-courriel.inc.php';
}
?>
