<?php
########################################################################
##
## Inclusions
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
foreach (init($racine, $idGalerie) as $fichier)
{
	include_once $fichier;
}

########################################################################
##
## Initialisations
##
########################################################################

if (!isset($langue))
{
	$langue = FALSE;
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

if (!galerieExiste($racine, $idGalerie))
{
	$idGalerie = FALSE;
}

if ($idGalerie && !isset($rss))
{
	$rss = $galerieFluxRssParDefaut;
}

if (!isset($jQueryInclus))
{
	$jQueryInclus = FALSE;
}

if (!isset($jQueryCookieInclus))
{
	$jQueryCookieInclus = FALSE;
}

########################################################################
##
## Début de la structure XHTML
##
########################################################################

echo doctype($xhtmlStrict); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANGUE; ?>" lang="<?php echo LANGUE; ?>">
	<head>
		<!-- Titre -->
		<title><?php echo $baliseTitle .= ' | ' . baliseTitleComplement($baliseTitleComplement, $langueParDefaut, $langue); ?></title>
		
		<!-- Métabalises -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
		
		<meta name="description" content="<?php echo $description; ?>" />
		
		<?php if ($motsClesActives): ?>
			<meta name="keywords" content="<?php echo motsCles($motsCles, $description); ?>" />
		<?php endif; ?>
		
		<meta name="robots" content="<?php echo robots($robotsParDefaut, $robots); ?>" />
		
		<!-- Balises `link` et `script` -->
		<?php if ($idGalerie && $rss): ?>
			<!-- RSS -->
			<?php $urlFlux = "$urlRacine/rss.php?chemin=" . str_replace($urlRacine . '/', '', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']); ?>
			<link rel="alternate" type="application/rss+xml" href="<?php echo $urlFlux; ?>" title="<?php echo sprintf(T_('RSS de la galerie %1$s'), $idGalerie); ?>" />
		<?php endif; ?>
		
		<?php if ($galerieFluxRssGlobal && file_exists("$racine/site/inc/rss-global-galeries.pc")): ?>
			<!-- RSS -->
			<link rel="alternate" type="application/rss+xml" href="<?php echo $urlRacine . '/rss.php?global=galeries&langue=' . LANGUE; ?>" title="<?php echo T_('RSS de toutes les galeries'); ?>" />
		<?php endif; ?>

		<?php if ($siteFluxRssGlobal && file_exists("$racine/site/inc/rss-global-site.pc")): ?>
			<!-- RSS -->
			<link rel="alternate" type="application/rss+xml" href="<?php echo $urlRacine . '/rss.php?global=pages&langue=' . LANGUE; ?>" title="<?php echo T_('RSS global du site'); ?>" />
		<?php endif; ?>
		
		<!-- Ajouts réalisés par la fonction `linkScript` -->
		<?php echo linkScript($fichiersLinkScript, $versionFichiersLinkScript, $styleSqueletmlCss); ?>
		
		<?php if (($galerieAccueilJavascript || $galerieLienOriginalJavascript) && $idGalerie): ?>
			<!-- Slimbox 2 pour les galeries -->
			<?php if (!$jQueryInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
				<?php $jQueryInclus = TRUE; ?>
			<?php endif; ?>
			
			<script type="text/javascript" src="<?php echo $urlRacine; ?>/inc/slimbox2/js/slimbox2.js"></script>
			<link type="text/css" rel="stylesheet" href="<?php echo $urlRacine; ?>/inc/slimbox2/css/slimbox2.css" media="screen" />
		<?php endif; ?>
		
		<?php if (!empty($boitesDeroulantes)): ?>
			<!-- Boîtes déroulantes -->
			<?php if (!$jQueryInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
				<?php $jQueryInclus = TRUE; ?>
			<?php endif; ?>
			
			<?php if (!$jQueryCookieInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.cookie.js"></script>
				<?php $jQueryCookieInclus = TRUE; ?>
			<?php endif; ?>
			
			<?php echo '<script type="text/javascript">' . "\n"; ?>
				<?php foreach ($boitesDeroulantes as $boiteDeroulante): ?>
					<?php $boiteDeroulanteId = explode(' ', $boiteDeroulante); ?>
					<?php echo "\tajouteEvenementLoad(function(){boiteDeroulante('{$boiteDeroulanteId[0]}', '{$boiteDeroulanteId[1]}', '{$boiteDeroulanteId[2]}');});\n"; ?>
				<?php endforeach; ?>
			<?php echo "</script>\n"; ?>
		<?php endif; ?>
		
		<?php if ($messageIE6): ?>
			<!-- Message à IE6 -->
			<?php echo '<!--[if lt IE 7]>' . "\n"; ?>
			<?php if (!$jQueryInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.min.js"></script>
			<?php endif; ?>
			
			<?php if (!$jQueryCookieInclus): ?>
				<script type="text/javascript" src="<?php echo $urlRacine; ?>/js/jquery.cookie.js"></script>
			<?php endif; ?>
			
			<script type="text/javascript">
				ajouteEvenementLoad(function(){boiteDeroulante('messageIE6', 'messageIE6titre', 'messageIE6corps');});
			</script>
			<?php echo '<![endif]-->'; ?>
		<?php endif; ?>
	</head>
	<body class="<?php echo classesBody(estAccueil(ACCUEIL), $idGalerie, $deuxColonnes, $deuxColonnesSousContenuAgauche, $uneColonneAgauche, $differencierLiensVisitesSeulementDansContenu, $arrierePlanColonne, $borduresPage, $coinsArrondisBloc); ?>">
		<div id="ancres">
			<?php include cheminFichierIncHtml($racine, 'ancres', $langueParDefaut, $langue); ?>
		</div><!-- /ancres -->

		<?php if ($messageIE6): ?>
		<?php echo '<!--[if lt IE 7]>' . "\n"; ?>
			<?php echo messageIE6($urlRacine . '/fichiers/firefox-52x52.gif', '', 52, 52); ?>
		<?php echo '<![endif]-->'; ?>
		<?php endif; ?>

		<div id="page">
			<div id="interieurPage">

		<div id="entete">
			<?php if ($surTitre): ?>
				<div id="surTitre">
					<?php include cheminFichierIncHtml($racine, 'sur-titre', $langueParDefaut, $langue); ?>
				</div><!-- /surTitre -->
			<?php endif; ?>
	
			<div id="titre">
				<?php echo nomSite(estAccueil(ACCUEIL), lienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), titreSite($titreSite, $langueParDefaut, $langue))); ?>
			</div><!-- /titre -->

			<div id="sousTitre">
				<?php include cheminFichierIncHtml($racine, 'sous-titre', $langueParDefaut, $langue); ?>
			</div><!-- /sousTitre -->
	
			<div class="sep"></div>
		</div><!-- /entete -->

		<div id="surContenu">
			<?php $decouvrir = FALSE; // Initialisation ?>
			<?php $decouvrirInclureContact = FALSE; // Initialisation ?>
			<?php include $racine . '/inc/faire-decouvrir.inc.php'; ?>
	
			<?php if (isset($corpsGalerie) && !empty($corpsGalerie)): ?>
				<?php $tableauCorpsGalerie = coupeCorpsGalerie($corpsGalerie, $galerieLegendeEmplacement, $coinsArrondisBloc); ?>
			<?php endif; ?>
	
			<?php $divSurSousContenu = 'sur'; ?>
			<?php include $racine . '/inc/blocs.inc.php'; ?>
		</div><!-- /surContenu -->

		<div id="contenu" class="<?php if ($differencierLiensVisitesSeulementDansContenu) echo 'liensVisitesDifferencies'; ?>">
			<div id="interieurContenu">
		
				<?php if ($idGalerie): ?>
					<div id="galerie">
				<?php endif; ?>
