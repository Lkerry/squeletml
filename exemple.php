<?php
$baliseTitle = "Exemples d'options de Squeletml et modèle de page";
$description = "Plusieurs options de Squeletml sont présentées dans cette page modèle.";
$robots = "noindex, follow, noarchive"; // Empêche la présence du modèle dans les moteurs de recherche.
$tableDesMatieres = TRUE;
$apercu = "interne";
$boitesDeroulantes = "#testBoiteDeroulante1 .testBoiteDeroulante2";
$auteur = "Squeletml";
$dateCreation = "2010-03-01";
$dateRevision = "2010-03-02";
include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion.
?>

<h1>Modèle de page</h1>

<p>Cette page présente des exemples d'utilisation de variables, de constantes, de fonctions, etc. de Squeletml pour la création d'une page.<!-- /aperçu --> Regarder le fichier source pour bien comprendre de quelle manière les différentes options ont été utilisées, et <a href="<?php echo $urlRacineAdmin; ?>/documentation.admin.php">se référer à la documentation</a> pour plus de détails.</p>

<h2>Boîtes déroulantes</h2>

<p>Présentation de quelques boîtes déroulantes.</p>

<?php
$courriel = 'contact_a_exemple_point_qc';
$urlTestExistance = 'http://www.exemple-abcde-12345.qc/';
$urlTestEncodage = "http://www.exemple-abcde-12345.qc/chemin/vers/l'éventuelle page.php?a=1&b=2&c=3";
?>

<div class="testBoiteDeroulante2">
	<p class="bDtitre">Information sur des adresses:</p>

	<ul class="bDcorps">
		<li>l'adresse de la présente page est <code><?php echo $url; ?></code>;</li>
		
		<li>le nom de la présente page est <code><?php echo $nomPage; ?></code>;</li>
		
		<li>
			<?php if (estAccueil($accueil)): ?>
				la présente page est la page d'accueil;
			<?php else: ?>
				<a href="<?php echo ACCUEIL; ?>">lien vers la page d'accueil</a>;
			<?php endif; ?>
		</li>
		
		<li><a href="<?php echo $urlRacine . '/maintenance.php'; ?>">lien vers la page de maintenance</a>;</li>
		
		<li>
			<?php if (courrielValide($courriel)): ?>
				le courriel <code><?php echo $courriel; ?></code> est valide;
			<?php else: ?>
				le courriel <code><?php echo $courriel; ?></code> n'est pas valide;
			<?php endif; ?>
		</li>
		
		<li>
			<?php if (urlExiste($urlTestExistance)): ?>
				l'URL <code><?php echo $urlTestExistance; ?></code> existe;
			<?php else: ?>
				l'URL <code><?php echo $urlTestExistance; ?></code> n'existe pas;
			<?php endif; ?>
		</li>
		
		<li>
			<p>et prenons cette adresse:</p>
			
			<pre><?php echo $urlTestEncodage; ?></pre>
			
			<p>Affichons-la maintenant en version encodée:</p>
			
			<pre><?php echo superRawurlencode($urlTestEncodage); ?></pre>
		</li>
	</ul>
</div>

<div id="testBoiteDeroulante1">
	
</div>

<div class="testBoiteDeroulante2">
	<p class="bDtitre">Voici une autre boîte déroulante:</p>
	
	<p class="bDcorps">La langue de cette page est <em><?php echo LANGUE; ?></em>.</p>
</div>

<h2>Chaînes de caractères</h2>

<h3>Code PHP</h3>

<?php
$codePhp = "<?php
// Traductions.
\$bonjour = sprintf(T_('Bonjour %1\$s.'), \$prenom);
\$salut = sprintf(T_('Salut %1\$s.'), \$prenom);
?>";
?>

<p>Voici du code PHP coloré:</p>

<pre><?php coloreCodePhp($codePhp, FALSE, TRUE); ?></pre>

<h3>Divers traitements de chaînes</h3>

<?php
$chaine = <<<CHAINE
<p>L'<strong>hiver</strong> arrive à grands pas!</p><!-- Fin du premier paragraphe. -->

<p>Le <strong>printemps</strong> suivra.
CHAINE;

$codeSansCommentaire = supprimeCommentairesHtml($chaine);
?>

<p>Voici maintenant une chaîne de caractères sécurisée:</p>

<p><?php echo securiseTexte($chaine) ?></p>

<p>Affichons un peu mieux la chaîne:</p>

<pre><?php echo securiseTexte($chaine) ?></pre>

<p>Et si on ne voulait pas de commentaire HTML?</p>

<pre><?php echo securiseTexte($codeSansCommentaire); ?></pre>

<p>Oups! On remarque qu'un parapraphe n'a pas été fermé. Voici le code HTML corrigé:</p>

<pre><?php echo securiseTexte(corrigeHtml($codeSansCommentaire)); ?></pre>

<?php
$phrase = "L'hiver arrive, le vent se lève";
$phraseFiltree = filtreChaine($racine, $phrase);
?>

<p>Prenons maintenant cette phrase: <em><?php echo $phrase; ?></em>. Nous pourrions en faire un nom de fichier ou de page web: <em><?php echo $phraseFiltree; ?></em>.</p>

<h3>Syntaxe Markdown</h3>

<?php
$markdown = <<<MARKDOWN
Une *emphase* en HTML:

	<em>Bonjour.</em>

Une **emphase forte** en HTML:

	<strong>Bonjour.</strong>
MARKDOWN;
?>

<p>Voici un texte écrit avec la syntaxe Markdown:</p>

<pre><?php echo $markdown; ?></pre>

<p>Ce qui donne:</p>

<?php echo mdtxtChaine($markdown); ?>

<h2>Fichiers et styles CSS généraux</h2>

<?php
$cheminImage = "$racine/fichiers/squeletml-logo.png";
$urlImage = "$urlRacine/fichiers/squeletml-logo.png";
$nomImage = superBasename($cheminImage);
$vignetteNom = nomSuffixe($nomImage, '-vignette');
$extensionImage = extension($cheminImage);
$typeMime = typeMime($cheminImage, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
?>

<p><img class="imgGauche" src="<?php echo $urlImage; ?>" alt="Squeletml" width="75" height="75" /> Le nom de cette image est <code><?php echo $nomImage; ?></code>, son extension est <code><?php echo $extensionImage; ?></code> et son type MIME est <code><?php echo $typeMime; ?></code>. La vignette de cette image pourrait s'appeler par exemple <code><?php echo $vignetteNom; ?></code>. Il s'agit du logo de Squeletml.</p>

<div class="sep"></div>

<p>Voici la bannière de Squeletml:</p>

<img class="imgCentre" src="<?php echo $urlRacine; ?>/fichiers/banniere-squeletml-80x15.png" alt="Squeletml" width="80" height="15" />

<p><img class="imgDroite" src="<?php echo $urlRacine; ?>/fichiers/firefox-52x52.png" alt="Firefox" width="52" height="52" /> Voici maintenant un petit logo à droite du paragraphe. Lorem ipsum dolor sit amet, consec tetier adipis cing elit. In sapien ante; dictum id, phare tra ut, males uada et, magna. Class aptent taci ti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.</p>

<div class="sep"></div>

<p class="gauche">Tiens, alignons du texte à droite.</p>

<p class="centre">Et au centre.</p>

<p class="droite">À gauche maintenant.</p>

<p>Quisque sit amet mi sit amet magna faucibus luctus. Ut pellentesque sodales arcu. Phasellus a elit. Maecenas rhoncus lorem id quam. Sed sed arcu et quam fermentum ultrices. Aenean pulvinar molestie magna. Vestibulum bibendum? Nullam libero arcu, ultrices a; aliquet quis, adipiscing sit amet, neque.</p>

<?php include $racine . '/inc/dernier.inc.php'; ?>
