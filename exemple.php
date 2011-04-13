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
$licence = "agplv3+";
include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion.
?>

<h1>Modèle de page</h1>

<?php echo chapeau("<p><em>Note: ce résumé a été mis en forme avec la fonction <code>chapeau()</code>.</em></p>\n<p>Cette page présente des exemples d'utilisation de variables, de constantes, de fonctions, etc. de Squeletml pour la création d'une page.<!-- /aperçu --> Regarder le fichier source pour bien comprendre de quelle manière les différentes options ont été utilisées, et <a href=\"$urlRacineAdmin/documentation.admin.php\">se référer à la documentation</a> pour plus de détails.</p>\n<p>Voir aussi cet <a href=\"$urlRacine/exemple2.php\">autre exemple de page</a> beaucoup plus simple.</p>"); ?>

<h2>Boîtes déroulantes</h2>

<?php
$courriel = 'contact_a_exemple_point_qc';
$urlTestExistance1 = 'http://www.exemple-abcde-12345.qc/';
$urlTestExistance2 = URL_SQUELETML;
$urlTestEncodage = "http://www.exemple-abcde-12345.qc/chemin/vers/l'éventuelle page.php?a=1&amp;b=2&amp;c=3";
?>

<div class="testBoiteDeroulante2">
	<p class="bDtitre">Voici une boîte déroulante de classe <code>.testBoiteDeroulante2</code>:</p>

	<ul class="bDcorps">
		<li><p>La variable <code>$url</code> fournit l'adresse de la page courante, qui vaut dans ce cas-ci <code><?php echo $url; ?></code>.</p></li>
		
		<li><p>La variable <code>$nomPage</code> fournit le nom de la page courante, qui vaut dans ce cas-ci <code><?php echo $nomPage; ?></code>.</p></li>
		
		<li><p>La fonction <code>arg()</code> permet de séparer l'URL en différents arguments. Par exemple, le premier argument est donné par <code>arg(0)</code> et vaut dans ce cas-ci <code><?php echo arg(0); ?></code>.</p></li>
		
		<li><p>Voici un exemple d'utilisation de la fonction <code>estAccueil()</code> et de la constante <code>ACCUEIL</code>:</p>
			
			<?php if (estAccueil($accueil)): ?>
				<p>la page courante est la page d'accueil.</p>
			<?php else: ?>
				<p><a href="<?php echo ACCUEIL; ?>/">lien vers la page d'accueil</a>.</p>
			<?php endif; ?>
		</li>
		
		<li><p><a href="<?php echo $urlRacine . '/maintenance.php'; ?>">Squeletml fournit une page de maintenance</a>.</p></li>
		
		<li><p>Voici un exemple d'utilisation de la fonction <code>courrielValide()</code>:</p>
			
			<?php if (courrielValide($courriel)): ?>
				<p>le courriel <code><?php echo $courriel; ?></code> est valide.</p>
			<?php else: ?>
				<p>le courriel <code><?php echo $courriel; ?></code> n'est pas valide.</p>
			<?php endif; ?>
		</li>
		
		<li><p>Voici un exemple d'utilisation de la fonction <code>urlExiste()</code>:</p>
			
			<?php if (urlExiste($urlTestExistance1)): ?>
				<p>l'URL <code><?php echo $urlTestExistance1; ?></code> existe.</p>
			<?php else: ?>
				<p>l'URL <code><?php echo $urlTestExistance1; ?></code> n'existe pas.</p>
			<?php endif; ?>
		</li>
		
		<li><p>Voici un autre exemple d'utilisation de la fonction <code>urlExiste()</code>:</p>
			
			<?php if (urlExiste($urlTestExistance2)): ?>
				<p>l'URL <code><?php echo $urlTestExistance2; ?></code> existe.</p>
			<?php else: ?>
				<p>l'URL <code><?php echo $urlTestExistance2; ?></code> n'existe pas.</p>
			<?php endif; ?>
		</li>
		
		<li><p>Prenons maintenant cette adresse:</p>
			
			<pre><?php echo $urlTestEncodage; ?></pre>
			
			<p>Affichons-la en version encodée avec la fonction <code>superRawurlencode()</code>:</p>
			
			<pre><?php echo superRawurlencode($urlTestEncodage); ?></pre>
		</li>
	</ul>
</div>

<div id="testBoiteDeroulante1">
	
</div>

<div class="testBoiteDeroulante2">
	<p class="bDtitre">Voici une autre boîte déroulante de classe <code>.testBoiteDeroulante2</code>:</p>
	
	<p class="bDcorps">La constante <code>LANGUE</code> fournit la langue de la page, qui vaut dans ce cas-ci <em><?php echo LANGUE; ?></em>.</p>
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

<p>Voici du code PHP coloré avec la fonction <code>coloreCodePhp()</code>:</p>

<pre><?php coloreCodePhp($codePhp, FALSE, TRUE); ?></pre>

<h3>Divers traitements de chaînes</h3>

<?php
$chaine = <<<CHAINE
<div style="margin-left: 50px;">
<p>L'<strong>hiver</strong> arrive à grands pas!</p><!-- Fin du premier paragraphe. -->

<p>Le <strong>printemps</strong> suivra.
</div>
CHAINE;

$chaineSecurisee = securiseTexte($chaine);
$chaineSansCom = supprimeCommentairesHtml($chaine);
$chaineSansComCorrigee = corrigeHtml($chaineSansCom);
$chaineSansComCorrigeeEtSecurisee = securiseTexte($chaineSansComCorrigee);
?>

<p>Voici maintenant une chaîne de caractères sécurisée avec la fonction <code>securiseTexte()</code>:</p>

<p><?php echo $chaineSecurisee ?></p>

<p>Affichons un peu mieux la chaîne, c'est-à-dire dans une balise <code>pre</code>:</p>

<pre><?php echo $chaineSecurisee ?></pre>

<p>Et si on ne voulait pas de commentaire HTML? Utilisons alors la fonction <code>supprimeCommentairesHtml()</code>:</p>

<pre><?php echo securiseTexte($chaineSansCom); ?></pre>

<p>Oups! On remarque qu'un parapraphe n'a pas été fermé. Voici le code HTML corrigé avec la fonction <code>corrigeHtml()</code>:</p>

<pre><?php echo $chaineSansComCorrigeeEtSecurisee; ?></pre>

<p>Pour terminer, désécurisons avec la fonction <code>desecuriseTexte()</code> la chaîne précédemment sécurisée:</p>

<?php echo desecuriseTexte($chaineSansComCorrigeeEtSecurisee); ?>

<?php
$phrase = "L'hiver arrive, le vent se lève";
$phraseFiltree = filtreChaine($racine, $phrase);
?>

<p>Prenons maintenant cette phrase: <em><?php echo $phrase; ?></em>. Nous pourrions en faire un nom de fichier ou de page web en utilisant la fonction <code>filtreChaine()</code>: <em><?php echo $phraseFiltree; ?></em>.</p>

<h3>Syntaxe Markdown</h3>

<?php
$markdown = <<<MARKDOWN
----

Une *emphase* en HTML:

	<em>Bonjour.</em>

Une **emphase forte** en HTML:

	<strong>Bonjour.</strong>

Fin de l'exemple en Markdown.

----
MARKDOWN;
?>

<p>Voici un texte écrit avec la syntaxe Markdown:</p>

<pre><?php echo securiseTexte($markdown); ?></pre>

<p>Ce qui donne, une fois traité avec la fonction <code>mdtxtChaine()</code>:</p>

<?php echo mdtxtChaine($markdown); ?>

<h2>Fichiers et styles CSS généraux</h2>

<?php
$cheminImage = "$racine/fichiers/squeletml-logo.png";
$urlImage = "$urlRacine/fichiers/squeletml-logo.png";
$nomImage = superBasename($cheminImage);
$vignetteNom = nomSuffixe($nomImage, '-vignette');
$extensionImage = extension($cheminImage);
$nomImageSansExtension = extension($cheminImage, TRUE);
$typeMime = typeMime($cheminImage, $typeMimeFile, $typeMimeCheminFile, $typeMimeCorrespondance);
?>

<p><img class="imgGauche" src="<?php echo $urlImage; ?>" alt="Squeletml" width="75" height="75" /> La présente image est le logo de Squeletml et a été positionnée avec la classe <code>imgGauche</code>. La fonction <code>superBasename()</code> fournit le nom de l'image, qui vaut dans ce cas-ci <code><?php echo $nomImage; ?></code>. Son extension peut être obtenue avec la fonction <code>extension()</code>; le résultat est <code><?php echo $extensionImage; ?></code> (ce qui donne <code><?php echo $nomImageSansExtension; ?></code> sans l'extension). Le type MIME, obtenu avec la fonction <code>typeMime()</code>, est <code><?php echo $typeMime; ?></code>. La vignette de cette image pourrait s'appeler par exemple <code><?php echo $vignetteNom; ?></code> (résultat obtenu avec <code>nomSuffixe($nomImage, '-vignette')</code>).</p>

<div class="sep"></div>

<p>Voici la bannière de Squeletml, centrée avec la classe <code>imgCentre</code>:</p>

<img class="imgCentre" src="<?php echo $urlRacine; ?>/fichiers/banniere-squeletml-80x15.png" alt="Squeletml" width="80" height="15" />

<p><img class="imgDroite" src="<?php echo $urlRacine; ?>/fichiers/Deer_Park_Globe.png" alt="Firefox" width="52" height="52" /> Voici maintenant un petit logo positionné à droite du paragraphe grâce à la classe <code>imgDroite</code>. Lorem ipsum dolor sit amet, consec tetier adipis cing elit. In sapien ante; dictum id, phare tra ut, males uada et, magna. Class aptent taci ti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam. Fusce eros leo, feugiat nec, viverra eu, tristique pellentesque, nunc.</p>

<div class="sep"></div>

<p class="gauche">Tiens, alignons du texte à gauche avec la classe <code>gauche</code>.</p>

<p class="centre">Et au centre, avec la classe <code>centre</code>.</p>

<p class="droite">À droite maintenant, avec la classe <code>droite</code>.</p>

<?php echo boiteArrondie("Note: ce texte a été mis en forme avec la fonction <code>boiteArrondie()</code>. Quisque sit amet mi sit amet magna faucibus luctus. Ut pellentesque sodales arcu. Phasellus a elit. Maecenas rhoncus lorem id quam. Sed sed arcu et quam fermentum ultrices. Aenean pulvinar molestie magna. Vestibulum bibendum? Nullam libero arcu, ultrices a; aliquet quis, adipiscing sit amet, neque."); ?>

<?php include $racine . '/inc/dernier.inc.php'; ?>
