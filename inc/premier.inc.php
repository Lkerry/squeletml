<?php
// Début des insertions
include_once dirname(__FILE__) . '/../init.inc.php';
include_once $racine . '/inc/config.inc.php';
include_once $racine . '/inc/fonctions.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}
if (file_exists($racine . '/site/inc/fonctions.inc.php'))
{
	include_once $racine . '/site/inc/fonctions.inc.php';
}
// Fin des insertions
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo langue($lang, $langue); ?>" lang="<?php echo langue($lang, $langue); ?>">
<head>
<title><?php echo $title .= ' | ' . $titleComplement; ?></title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo construitMotsCles($keywords, $description); ?>" />
<meta name="robots" content="<?php echo robots($metaRobots, $robots); ?>" />
<?php echo construitLinkScript($fichiersLinkScript, $versionFichiersLinkScript); ?>
</head>
<body class="<?php echo construitClass(estAccueil()); ?>">
<div id="page">

<?php
if ($messageIE6)
{
	echo messageIE6($accueil . '/images/firefox-52x52.gif', '', 52, 52);
}
?>

<div id="entete">
	<div id="titre">
		<?php echo construitNomSite(estAccueil(), construitLienVersAccueil($accueil, estAccueil(), $titreSite)); ?>
	</div><!-- /titre -->

	<div id="sousTitre">
		<?php inclutSousTitre($racine); ?>
	</div><!-- /sousTitre -->
</div><!-- /entete -->

<div id="ancres">
	<?php inclutAncres($racine); ?>
</div><!-- /ancres -->

<?php if (!$menuSousLeContenu): ?>
	<?php afficheMenu($racine, $accueil); ?>
<?php endif; ?>

<div id="contenu">
	<div id="interieurContenu">
