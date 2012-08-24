<?php
$ajoutCommentaires = FALSE;
$desactiverCache = TRUE;
$desactiverCachePartiel = TRUE;
$infosPublication = FALSE;
$licence = '';
$lienPage = FALSE;
$partageCourriel = FALSE;
$partageReseaux = FALSE;
$robots = 'noindex, nofollow, noarchive';
include 'inc/premier.inc.php';

$courrielDesabonnement = '';
$urlDesabonnement = '';
$abonnementTrouve = FALSE;
$desabonnementReussi = FALSE;

if (!empty($_GET['url']) && !empty($_GET['id']))
{
	$urlDesabonnement = $urlRacine . '/' . supprimeUrlRacine($urlRacine, decodeTexteGet($_GET['url']));
	$cheminConfigCommentaires = cheminConfigCommentaires($racine, $urlRacine, $urlDesabonnement, TRUE);
	$cheminConfigAbonnementsCommentaires = cheminConfigAbonnementsCommentaires($cheminConfigCommentaires);
	
	$listeAbonnements = super_parse_ini_file($cheminConfigAbonnementsCommentaires, TRUE);
	
	if (!empty($listeAbonnements))
	{
		$contenuConfigAbonnements = '';
		
		foreach ($listeAbonnements as $courrielAbonnement => $infosAbonnement)
		{
			if (!empty($infosAbonnement['idAbonnement']) && $infosAbonnement['idAbonnement'] == $_GET['id'])
			{
				$abonnementTrouve = TRUE;
				$courrielDesabonnement = $courrielAbonnement;
			}
			else
			{
				$contenuConfigAbonnements .= "[$courrielAbonnement]\n";
				
				if (!empty($infosAbonnement['idAbonnement']))
				{
					$contenuConfigAbonnements .= 'idAbonnement=' . $infosAbonnement['idAbonnement'] . "\n";
				}
				
				$contenuConfigAbonnements .= "\n";
			}
		}
		
		if ($abonnementTrouve && @file_put_contents($cheminConfigAbonnementsCommentaires, $contenuConfigAbonnements, LOCK_EX) !== FALSE)
		{
			$desabonnementReussi = TRUE;
		}
	}
}

if ($desabonnementReussi)
{
	echo '<p>' . sprintf(T_("Le désabonnement a été effectué pour le courriel %1\$s à l'adresse %2\$s."), '<code>' . securiseTexte($courrielDesabonnement) . '</code>', '<code>' . securiseTexte($urlDesabonnement) . '</code>') . "</p>\n";
}
else
{
	echo '<p class="erreur">' . T_("Le désabonnement n'a pu être effectué. Veuillez vérifier votre URL de désabonnement dans le courriel de notification reçu. Si le problème persiste, vous pouvez faire la demande de désabonnement par le formulaire de contact du site.") . "</p>\n";
}

include $racine . '/inc/dernier.inc.php';
?>
