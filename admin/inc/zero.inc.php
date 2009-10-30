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

include_once $racine . '/admin/inc/fonctions.inc.php';

foreach (adminInit($racine) as $fichier)
{
	include_once $fichier;
}

########################################################################
##
## Initialisations après inclusions
##
########################################################################

if (!isset($javascriptGettextInclus))
{
	$javascriptGettextInclus = FALSE;
}

if (!isset($jQueryInclus))
{
	$jQueryInclus = FALSE;
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = FALSE;
}

########################################################################
##
## Divers
##
########################################################################

// Nécessaire à la traduction
phpGettext('..', langue($langueParDefaut, $langueParDefaut));

?>
