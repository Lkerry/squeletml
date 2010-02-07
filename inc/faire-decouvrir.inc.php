<?php
/*
Ce fichier crée les variables nécessaires à l'incorporation au formulaire de contact du module «Faire découvrir». Aucun code XHTML n'est envoyé au navigateur.
*/

if (!$erreur404 && !$estPageDerreur)
{
	if (!isset($messageEnvoye))
	{
		$messageEnvoye = FALSE;
	}
	
	if (isset($_GET['action']) && $_GET['action'] == 'faireDecouvrir')
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
			$petitMot = '<p>' . sprintf(T_("Aussi, %1\$s vous a écrit un petit mot personnalisé, que vous pouvez lire ci-dessous:"), '<em>' . $nom . '</em>') . '</p><div style="margin-left: 25px;">' . nl2br(securiseTexte($_POST['message'])) . "</div>\n";
		}
		else
		{
			$petitMot = '';
		}
		
		$messageDecouvrir = '';
	}

	$cheminConfigGalerie = cheminConfigGalerie($racine, $idGalerie);

	if (!empty($idGalerie) && isset($_GET['oeuvre']) && $cheminConfigGalerie)
	{
		$tableauGalerie = tableauGalerie($cheminConfigGalerie, TRUE);
		$i = 0;
	
		foreach($tableauGalerie as $oeuvre)
		{
			$id = idOeuvre($oeuvre);
		
			if ($id == sansEchappement($_GET['oeuvre']))
			{
				$decouvrir = TRUE;
			
				if (isset($_GET['action']) && $_GET['action'] == 'faireDecouvrir')
				{
					$decouvrirInclureContact = TRUE;
					$messageDecouvrirSupplement = decouvrirSupplementOeuvre($urlRacine, $idGalerie, $oeuvre, $galerieLegendeMarkdown);
					$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir l'oeuvre %3\$s, qui fait partie de la galerie %4\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . $oeuvre['intermediaireNom'] . '</em>', '<em>' . $idGalerie . '</em>') . "</p>\n" . $messageDecouvrirSupplement . $petitMot;
				}
			
				break;
			}
		}
	}
	elseif (!empty($idGalerie) && !isset($_GET['oeuvre']) && $cheminConfigGalerie)
	{
		$decouvrir = TRUE;
	
		if (isset($_GET['action']) && $_GET['action'] == 'faireDecouvrir')
		{
			$decouvrirInclureContact = TRUE;
		
			if (!isset($description))
			{
				$description = '';
			}
		
			$messageDecouvrirSupplement = decouvrirSupplementPage($description, $baliseTitle);
			$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la galerie %3\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . $idGalerie . '</em>') . "</p>\n" . $messageDecouvrirSupplement . $petitMot;
		}
	}
	elseif (empty($courrielContact))
	{
		$decouvrir = TRUE;
	
		if (isset($_GET['action']) && $_GET['action'] == 'faireDecouvrir')
		{
			$decouvrirInclureContact = TRUE;
		
			if (!isset($description))
			{
				$description = '';
			}
		
			$messageDecouvrirSupplement = decouvrirSupplementPage($description, $baliseTitle);
			$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la page %3\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . urlPageSansDecouvrir() . '</em>') . "</p>\n" . $messageDecouvrirSupplement . $petitMot;
		}
	}
}

// Traitement personnalisé optionnel.
if (file_exists($racine . '/site/inc/faire-decouvrir.inc.php'))
{
	include_once $racine . '/site/inc/faire-decouvrir.inc.php';
}
?>
