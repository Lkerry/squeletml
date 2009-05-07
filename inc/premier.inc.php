<?php
// Début des insertions
include_once dirname(__FILE__) . '/../init.inc.php';
include_once $racine . '/inc/fonctions.inc.php';
foreach (init($racine, langue($langue)) as $fichier)
{
	include_once $fichier;
}
// Fin des insertions
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo langue($langue); ?>" lang="<?php echo langue($langue); ?>">
<head>
<title><?php echo $baliseTitle .= ' | ' . baliseTitleComplement($baliseTitleComplement, $langue); ?></title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo construitMotsCles($motsCles, $description); ?>" />
<meta name="robots" content="<?php echo robots($robots); ?>" />
<?php echo construitLinkScript($fichiersLinkScript, $versionFichiersLinkScript); ?>
</head>
<body class="<?php echo construitClass(estAccueil(ACCUEIL)); ?>">
<div id="page">

<?php
if ($messageIE6)
{
	echo messageIE6($urlRacine . '/fichiers/firefox-52x52.gif', '', 52, 52);
}
?>

<div id="entete">
	<div id="titre">
		<?php echo construitNomSite(estAccueil(ACCUEIL), construitLienVersAccueil(ACCUEIL, estAccueil(ACCUEIL), titreSite($titreSite, $langue))); ?>
	</div><!-- /titre -->

	<div id="sousTitre">
		<?php include fichierSousTitre($racine, $langue); ?>
	</div><!-- /sousTitre -->
</div><!-- /entete -->

<div id="ancres">
	<?php include fichierAncres($racine, $langue); ?>
</div><!-- /ancres -->

<?php if (!$menuLanguesSousLeContenu): ?>
	<div id="menuLangues">
		<?php include fichierMenuLangues($racine, $langue); ?>
	</div><!-- /menuLangues -->
<?php endif; ?>

<?php if (!$menuSousLeContenu): ?>
	<div id="menu">
		<?php include fichierMenu($racine, $langue); ?>
	</div><!-- /menu -->
<?php endif; ?>

<div id="contenu">
	<div id="interieurContenu">
