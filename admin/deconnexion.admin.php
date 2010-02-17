<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Déconnexion de la section d'administration");
$boitesDeroulantes = "#explications";
include $racineAdmin . '/inc/premier.inc.php';
?>

<h1><?php echo T_("Déconnexion de la section d'administration"); ?></h1>

<div id="explications">
	<h2 class="bDtitre"><?php echo T_("Explications"); ?></h2>
	
	<div class="bDcorps">
		<p><?php echo T_("Avant de se déconnecter de la section d'administration, il est important de bien comprendre la manière dont l'accès est géré. Squeletml utilise l'authentification basique <acronym lang=\"en\" title=\"HyperText Transfer Protocol\">HTTP</acronym> offerte par Apache. Les informations de connexion sont gardées en mémoire par le navigateur, qui va automatiquement les envoyer au serveur lors de chaque visite d'une page à accès restreint."); ?></p>

		<p><?php echo T_("L'identifiant et le mot de passe sont donc gérés au niveau du client, et non au niveau du serveur. Il n'y a rien de prévu dans le protocole <acronym lang=\"en\">HTTP</acronym>/1.1 pour permettre au serveur de forcer un navigateur à cesser une connexion déjà établie."); ?></p>

		<p><?php echo T_("Ainsi, les seules méthodes de déconnexion tout à fait fiables sont:"); ?></p>

		<ul>
			<li><?php echo T_("fermer toutes les instances ouvertes du navigateur;"); ?></li>
			<li><?php echo T_("utiliser une option de suppression des données de connexion en mémoire offerte par le navigateur, si une telle option existe. Par exemple, <span lang=\"en\">Firefox</span> 3.5 permet de le faire facilement en allant dans <em>Outils > Supprimer l'historique récent...</em> et en cochant <em>Connexions actives</em> dans la section <em>Détails</em> avant de supprimer l'historique."); ?></li>
		</ul>

		<p><?php echo T_("Cependant, il y a une astuce qui consiste à forcer une nouvelle connexion avec un identifiant bidon. Ainsi, les nouvelles informations de connexion en mémoire ne conviendront plus pour la section d'administration. Pour ce faire, cliquer sur le lien de déconnexion (si une fenêtre apparaît, saisir <code>deconnexion</code> comme utilisateur et laisser vide le champ pour le mot de passe). Après avoir effectué cette manoeuvre, il est important de vérifier que la connexion à l'administration a bien été désactivée (en visitant une page de l'administration, une fenêtre de connexion devrait vous inviter à vous identifier à nouveau)."); ?></p>
		
		<h3><?php echo T_("Liens utiles"); ?></h3>
		
		<ul>
			<li><?php printf(T_("Section <a href=\"%1\$s\">«Accréditifs d'authentification et clients inactifs»</a> de la traduction en français de la <acronym lang=\"en\" title=\"Request for Comments\">RFC</acronym> 2616 (<em>Protocole de transfert Hypertexte -- <acronym lang=\"en\">HTTP</acronym>/1.1</em>)."), "http://abcdrfc.free.fr/rfc-vf/rfc2616.htm#_Toc163190671"); ?>
			<ul>
				<li><?php printf(T_("Le texte original de la même section de la <acronym lang=\"en\">RFC</acronym> 2616: <a href=\"%1\$s\" lang=\"en\">«Authentication Credentials and Idle Clients»</a>."), "http://www.w3.org/Protocols/rfc2616/rfc2616-sec15.html#sec15.6"); ?></li>
			</ul></li>
			<li><?php printf(T_("<a href=\"%1\$s\">Section «<span lang=\"en\">Frequently asked questions about basic auth</span>»</a> du document <em lang=\"en\">Authentication, Authorization, and Access Control</em> (documentation Apache)."), "http://httpd.apache.org/docs/1.3/howto/auth.html#basicfaq"); ?></li>
			<li><?php printf(T_("<a href=\"%1\$s\">Authentification, autorisation et contrôle d'accès</a> (documentation Apache)."), "http://httpd.apache.org/docs/trunk/fr/howto/auth.html"); ?></li>
		</ul>
	</div><!-- /.bDcorps -->
</div><!-- /#explications -->

<h2><?php echo T_("Se déconnecter"); ?></h2>

<p><a id="lienDeconnexion" href="<?php echo $urlDeconnexion; ?>"><?php echo T_("Lien de déconnexion"); ?></a></p>

<?php include $racineAdmin . '/inc/dernier.inc.php'; ?>
