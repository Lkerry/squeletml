<?php
// Début des insertions
include_once dirname(__FILE__) . '/../init.inc.php';
if (file_exists($racine . '/inc/devel.inc.php'))
{
	include_once $racine . '/inc/devel.inc.php';
}
include_once $racine . '/inc/config.inc.php';
if (file_exists($racine . '/site/inc/config.inc.php'))
{
	include_once $racine . '/site/inc/config.inc.php';
}
include_once $racine . '/inc/fonctions.inc.php';
if (!isset($idGalerie))
{
	$idGalerie = FALSE;
}
if (!isset($motsCles))
{
	$motsCles = FALSE;
}
if (isset($courrielContact) && $courrielContact == '@' && !empty($courrielContactParDefaut))
{
	$courrielContact = $courrielContactParDefaut;
}
foreach (init($racine, langue($langue), $idGalerie) as $fichier)
{
	include_once $fichier;
}
// Fin des insertions

if ($idGalerie && !isset($rss))
{
	$rss = $galerieFluxParDefaut;
}
?>

<?php echo doctype($xhtmlStrict); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo langue($langue); ?>" lang="<?php echo langue($langue); ?>">
<head>
<title><?php echo $baliseTitle .= ' | ' . baliseTitleComplement($baliseTitleComplement, $langue); ?></title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo construitMotsCles($motsCles, $description); ?>" />
<meta name="robots" content="<?php echo robots($robots); ?>" />
<?php
if ($idGalerie && $rss)
{
	$urlFlux = "$urlRacine/rss.php?chemin=" . str_replace($urlRacine . '/', '', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlFlux\" title=\"" . sprintf(T_("RSS de la galerie %1\$s"), $idGalerie) . "\" />";
}

if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
{
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlRacine/rss.php?global=galeries\" title=\"" . T_("RSS de toutes les galeries") . "\" />";
}

if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
{
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlRacine/rss.php?global=pages\" title=\"" . T_("RSS global du site") . "\" />";
}
?>
<?php echo construitLinkScript($fichiersLinkScript, $versionFichiersLinkScript, $styleSqueletmlCss); ?>
<?php if (($galerieAccueilJavascript || $galerieLienOrigJavascript) && $idGalerie): ?>
	<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $urlRacine; ?>/inc/slimbox2/js/slimbox2.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo $urlRacine; ?>/inc/slimbox2/css/slimbox2.css" media="screen" />
<?php endif; ?>
</head>
<body class="<?php echo construitClass(estAccueil(ACCUEIL), $menuSousLeContenu, $menuLanguesSousLeContenu, $menuSousLeMenuLangues, $colonneAgauche, $deuxColonnes, $idGalerie); ?>">
<div id="ancres">
	<?php include fichierAncres($racine, $langue); ?>
</div><!-- /ancres -->

<?php
if ($messageIE6)
{
	echo messageIE6($urlRacine . '/fichiers/firefox-52x52.gif', '', 52, 52);
}
?>

<div id="page">
	<div id="interieurPage">

<div id="entete">
	<?php if ($surTitre): ?>
		<div id="surTitre">
			<?php include fichierSurTitre($racine, $langue); ?>
		</div><!-- /surTitre -->
	<?php endif; ?>
	
	<div id="titre">
		<?php echo construitNomSite(estAccueil(ACCUEIL), construitLienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), titreSite($titreSite, $langue))); ?>
	</div><!-- /titre -->

	<div id="sousTitre">
		<?php include fichierSousTitre($racine, $langue); ?>
	</div><!-- /sousTitre -->
</div><!-- /entete -->

<div id="surContenu">
<?php if (!$menuSousLeMenuLangues): ?>
	<?php if (!$menuSousLeContenu): ?>
		<div id="menu">
			<?php include fichierMenu($racine, $langue); ?>
		</div><!-- /menu -->
		<script type="text/javascript">setPage();</script>
	<?php endif; ?>
<?php endif; ?>

<?php if (!$menuLanguesSousLeContenu && count($accueil) > 1): ?>
	<div id="menuLangues">
		<?php include fichierMenuLangues($racine, $langue); ?>
	</div><!-- /menuLangues -->
<?php endif; ?>

<?php if ($menuSousLeMenuLangues): ?>
	<?php if (!$menuSousLeContenu): ?>
		<div id="menu">
			<?php include fichierMenu($racine, $langue); ?>
		</div><!-- /menu -->
		<script type="text/javascript">setPage();</script>
	<?php endif; ?>
<?php endif; ?>
</div><!-- /surContenu -->

<div id="contenu">
	<div id="interieurContenu">
		
		<?php if ($idGalerie): ?>
			<div id="galerie">
		<?php endif; ?>
