<?php
// Module «Faire découvrir»
$decouvrir = FALSE; // Initialisation
if (isset($_GET['action']) && $_GET['action'] == 'faireDecouvrir' && $faireDecouvrir)
{
	if (isset($_POST['nom']) && !empty($_POST['nom']) && !$messageEnvoye)
	{
		$nom = securiseTexte($_POST['nom']);
	}
	else
	{
		$nom = T_("Votre nom");
	}
	
	if (isset($_POST['message']) && !empty($_POST['message']) && !$messageEnvoye)
	{
		$petitMot = '<p>' . sprintf(T_("Aussi, %1\$s vous a écrit un petit mot personnalisé, que vous pouvez lire ci-dessous:"), '<em>' . $nom . '</em>') . '</p><div style="margin-left: 25px;">' . nl2br(securiseTexte($_POST['message'])) . '</div>';
	}
	else
	{
		$petitMot = '';
	}
	
	$messageDecouvrir = '';
	
	if ($idGalerie && isset($_GET['oeuvre']))
	{
		if (file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/')
		&& file_exists($racine . '/site/inc/galerie-' . $idGalerie . '.txt'))
		{
			$galerie = construitTableauGalerie($racine . '/site/inc/galerie-' . $idGalerie . '.txt');
			$i = 0;
			foreach($galerie as $oeuvre)
			{
				$id = idOeuvre($oeuvre);
				if ($id == $_GET['oeuvre'])
				{
					$messageDecouvrirSupplement = decouvrirSupplementOeuvre($urlRacine, $idGalerie, $oeuvre);
					
					$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir l'oeuvre %3\$s, qui fait partie de la galerie %4\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . $oeuvre['grandeNom'] . '</em>', '<em>' . $idGalerie . '</em>') . '</p>' . $messageDecouvrirSupplement . $petitMot;
					$decouvrir = TRUE;
					break;
				}
			}
		}
	}
	elseif ($idGalerie && !isset($_GET['oeuvre']))
	{
		if (file_exists($racine . '/site/fichiers/galeries/' . $idGalerie . '/')
		&& file_exists($racine . '/site/inc/galerie-' . $idGalerie . '.txt'))
		{
			$messageDecouvrirSupplement = decouvrirSupplementPage($baliseDescription, $baliseTitle);
			
			$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la galerie %3\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . $idGalerie . '</em>') . '</p>' . $messageDecouvrirSupplement . $petitMot;
			$decouvrir = TRUE;
		}
	}
	else
	{
		$messageDecouvrirSupplement = decouvrirSupplementPage($baliseDescription, $baliseTitle);
		
		$messageDecouvrir = '<p>' . sprintf(T_("%1\$s vous a envoyé un message à partir du site %2\$s pour vous faire découvrir la page %3\$s."), '<em>' . $nom . '</em>', '<a href="' . ACCUEIL . '">' . ACCUEIL . '</a>', '<em>' . urlPageSansDecouvrir() . '</em>') . '</p>' . $messageDecouvrirSupplement . $petitMot;
		$decouvrir = TRUE;
	}
}

?>
