<ul class="bDcorps">
	<li><a href="<?php echo $urlRacineAdmin; ?>/porte-documents.admin.php#ajouter"><?php echo T_("Ajouter un fichier"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/porte-documents.admin.php#creer"><?php echo T_("Créer un fichier"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/porte-documents.admin.php?action=editer&valeur=../site/xhtml/<?php echo $adminLangueParDefaut; ?>/menu.inc.php&dossierCourant=../site/xhtml/<?php echo $adminLangueParDefaut; ?>#messages"><?php printf(T_("Modifier le menu «%1\$s»"), $adminLangueParDefaut); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/rss.admin.php?global=site#messages"><?php echo T_("Modifier les flux RSS globaux des pages"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/porte-documents.admin.php?action=editer&valeur=../site/inc/config.inc.php&dossierCourant=../site/inc#messages"><?php echo T_("Modifier la configuration"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/galeries.admin.php#ajouter"><?php echo T_("Ajouter des images à une galerie"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/galeries.admin.php#pageWeb"><?php echo T_("Créer une page web de galerie"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/acces.admin.php#sauvegarde"><?php echo T_("Sauvegarder le site"); ?></a> | </li>
	
	<li><a href="<?php echo $urlRacineAdmin; ?>/acces.admin.php#maintenance"><?php echo T_("Mettre le site hors ligne"); ?></a></li>
</ul>
