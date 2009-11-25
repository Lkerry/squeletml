<?php
########################################################################
##
## Initialisations avant inclusions
##
########################################################################

if (!isset($langue))
{
	$langue = FALSE;
}

########################################################################
##
## Inclusions
##
########################################################################

include_once dirname(__FILE__) . '/../../init.inc.php';

if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}

include_once $racine . '/inc/config.inc.php';

if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}

include_once $racineAdmin . '/inc/fonctions.inc.php';

foreach (adminInit($racineAdmin) as $fichier)
{
	include_once $fichier;
}

########################################################################
##
## Divers
##
########################################################################

// Nécessaire à la traduction
phpGettext('..', langue($adminLangueParDefaut, $adminLangueParDefaut));

?>
