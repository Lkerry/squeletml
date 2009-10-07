<?php
########################################################################
##
## Insertions et initialisations
##
########################################################################

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

if (!isset($pageDerreur))
{
	$pageDerreur = FALSE;
}

if (!isset($robots))
{
	$robots = FALSE;
}

if (isset($courrielContact) && $courrielContact == '@' && !empty($courrielContactParDefaut))
{
	$courrielContact = $courrielContactParDefaut;
}

foreach (init($racine, $idGalerie) as $fichier)
{
	include_once $fichier;
}

if (!galerieExiste($racine, $idGalerie))
{
	$idGalerie = FALSE;
}

if ($idGalerie && !isset($rss))
{
	$rss = $galerieFluxParDefaut;
}

########################################################################
##
## Début de la structure XHTML
##
########################################################################
?>

<?php echo doctype($xhtmlStrict); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
<head>
<title><?php echo $baliseTitle .= ' | ' . baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue); ?></title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<?php if ($motsClesActives): ?>
	<meta name="keywords" content="<?php echo construitMotsCles($motsCles, $description); ?>" />
<?php endif; ?>
<meta name="robots" content="<?php echo robots($robotsParDefaut, $robots); ?>" />
<?php
if ($idGalerie && $rss)
{
	$urlFlux = "$urlRacine/rss.php?chemin=" . str_replace($urlRacine . '/', '', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlFlux\" title=\"" . sprintf(T_("RSS de la galerie %1\$s"), $idGalerie) . "\" />";
}

if ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc"))
{
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlRacine/rss.php?global=galeries&langue=" . LANGUE . "\" title=\"" . T_("RSS de toutes les galeries") . "\" />";
}

if ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))
{
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$urlRacine/rss.php?global=pages&langue=" . LANGUE . "\" title=\"" . T_("RSS global du site") . "\" />";
}
?>
<?php echo construitLinkScript($fichiersLinkScript, $versionFichiersLinkScript, $styleSqueletmlCss); ?>
<?php if (($galerieAccueilJavascript || $galerieLienOriginalJavascript) && $idGalerie): ?>
	<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $urlRacine; ?>/inc/slimbox2/js/slimbox2.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo $urlRacine; ?>/inc/slimbox2/css/slimbox2.css" media="screen" />
<?php endif; ?>
</head>
<body class="<?php echo construitClassBody(estAccueil(ACCUEIL), $idGalerie, $menuSousContenu, $menuLanguesSousContenu, $menuSousMenuLangues, $deuxColonnes, $deuxColonnesSousContenuAgauche, $uneColonneAgauche, $stylerLiensVisitesSeulementDansContenu); ?>">
<div id="ancres">
	<?php include cheminFichierIncHtml($racine, 'ancres', $langueParDefaut, $langue); ?>
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
			<?php include cheminFichierIncHtml($racine, 'sur-titre', $langueParDefaut, $langue); ?>
		</div><!-- /surTitre -->
	<?php endif; ?>
	
	<div id="titre">
		<?php echo construitNomSite(estAccueil(ACCUEIL), construitLienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), titreSite($titreSite, $langueParDefaut, $langue))); ?>
	</div><!-- /titre -->

	<div id="sousTitre">
		<?php include cheminFichierIncHtml($racine, 'sous-titre', $langueParDefaut, $langue); ?>
	</div><!-- /sousTitre -->
	
	<div class="sep"></div>
</div><!-- /entete -->

<div id="surContenu">
<?php if (!$surContenuSupplementFin && file_exists("$racine/site/inc/html." . LANGUE . ".sur-contenu-supplement.inc.php")): ?>
	<div id="surContenuSupplement">
		<?php include $racine . '/site/inc/html.' . LANGUE . '.sur-contenu-supplement.inc.php'; ?>
	</div><!-- /surContenuSupplement -->
<?php endif; ?>

<?php if (!$menuSousMenuLangues && !$menuSousContenu): ?>
	<?php include fichierPartagePremierDernier($racine, 'menu'); ?>
<?php endif; ?>

<?php if (count($accueil) > 1 && !$menuLanguesSousContenu): ?>
	<?php include fichierPartagePremierDernier($racine, 'menu-langues'); ?>
<?php endif; ?>

<?php if ($menuSousMenuLangues && !$menuSousContenu): ?>
	<?php include fichierPartagePremierDernier($racine, 'menu'); ?>
<?php endif; ?>

<?php
$decouvrir = FALSE; // Initialisation
$decouvrirInclureContact = FALSE; // Initialisation
include $racine . '/inc/faire-decouvrir.inc.php';
?>
<?php if ($faireDecouvrir && $decouvrir && !$faireDecouvrirSousContenu): ?>
	<?php include fichierPartagePremierDernier($racine, 'faire-decouvrir'); ?>
<?php endif; ?>

<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
	<?php $tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement); ?>
<?php endif; ?>
<?php if (!empty($tableauCorpsGalerie['texteIntermediaire']) && $galerieLegendeEmplacement == 'surContenu'): ?>
	<?php echo $tableauCorpsGalerie['texteIntermediaire']; ?>
<?php endif; ?>

<?php if ((($idGalerie && $rss) || ($galerieFluxGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc")) || ($siteFluxGlobal && file_exists("$racine/site/inc/rss-global-site.pc"))) && (!$rssSousContenu)): ?>
	<?php include fichierPartagePremierDernier($racine, 'flux-rss'); ?>
<?php endif; ?>

<?php if ($surContenuSupplementFin && file_exists("$racine/site/inc/html." . LANGUE . ".sur-contenu-supplement.inc.php")): ?>
	<div id="surContenuSupplement">
		<?php include $racine . '/site/inc/html.' . LANGUE . '.sur-contenu-supplement.inc.php'; ?>
	</div><!-- /surContenuSupplement -->
<?php endif; ?>
</div><!-- /surContenu -->

<div id="contenu" class="<?php if ($stylerLiensVisitesSeulementDansContenu) echo 'liensVisitesStyles'; ?>">
	<div id="interieurContenu">
		
		<?php if ($idGalerie): ?>
			<div id="galerie">
		<?php endif; ?>
