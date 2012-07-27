<?php
########################################################################
##
## Traitement personnalisé optionnel 1 de 2.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/dernier-pre.inc.php"))
{
	include "$racine/site/$dossierAdmin/inc/dernier-pre.inc.php";
}

########################################################################
##
## Affectations.
##
########################################################################

$cheminBasDePage = adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'bas-de-page');

########################################################################
##
## Ajouts dans `$adminBalisesLinkScriptFinales`.
##
########################################################################

// Variable finale.
if (!empty($adminBalisesLinkScriptFinales))
{
	$linkScriptFin = linkScript($racine, $urlRacine, $adminFusionnerCssJs, $dossierAdmin, $adminBalisesLinkScriptFinales);
}

########################################################################
##
## Traitement personnalisé optionnel 2 de 2.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/dernier.inc.php"))
{
	include "$racine/site/$dossierAdmin/inc/dernier.inc.php";
}

########################################################################
##
## Code XHTML 2 de 2.
##
########################################################################

include adminCheminXhtml($racineAdmin, array ($langue, $adminLangueParDefaut), 'page.dernier');
?>
