<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Gestion des flux RSS globaux");
include 'inc/premier.inc.php';

include '../init.inc.php';
?>

<h1><?php echo T_("Gestion des flux RSS globaux"); ?></h1>

<div id="boiteMessages" class="boite">
<h2 id="messages"><?php echo T_("Messages d'avancement, de confirmation ou d'erreur"); ?></h2>

<?php
if (isset($_POST['lister']))
{
	if (isset($_POST['global']) && $_POST['global'] == 'galeries')
	{
		$cheminFichier = "$racine/site/inc/rss-global-galeries.txt";
		if (file_exists($cheminFichier))
		{
			if ($fic = fopen($cheminFichier, 'r'))
			{
				echo "<form action='$action#messages' method='post'><div>\n";
				
				$galeries = tableauAssociatif($cheminFichier);
				$listeGaleries = '';
				$i = 0;
				if (!empty($galeries))
				{
					foreach ($galeries as $idGalerie => $urlRelativeGalerie)
					{
						$listeGaleries .= "<input type='text' name='id$i' value='$idGalerie' />=<input type='text' name='url$i' value='$urlRelativeGalerie' /><br />\n";
						$i++;
					}
				}
				
				fclose($fic);
				
				echo '<div class="boite2">' . "\n";
				echo '<h3>' . T_("Liste des pages des galeries") . '</h3>' . "\n";
				echo '<p>' . sprintf(T_("Chaque ligne est sous la forme <code>id de la galerie=URL relative de la galerie</code>. Par exemple, %1\$s fait référence à une galerie dont l'id est %2\$s et dont l'URL est %3\$s."), "<code>chiens=animaux/chiens.php</code>", "<code>chiens</code>", "<code>$urlRacine/animaux/chiens.php</code>") . '</p>';
	
				if (!empty($listeGaleries))
				{
					echo $listeGaleries;
				}
				else
				{
					echo '<p>' . T_("Le fichier est vide. Aucune galerie n'y est listée.") . "</p>\n";
				}
				
				echo '<p><strong>' . T_("Ajouter une galerie:") . '</p></strong>';
				echo "<input type='text' name='id$i' value='' />=<input type='text' name='url$i' value='' /><br />\n";
				$i++;
				
				echo "<p><input type='submit' name='modifsGaleries' value='" . T_('Enregistrer mes modifications') . "' /></p>\n";
				echo "</div></form>\n";
				
				echo "</div><!-- /boite2 -->\n";
			}
			else
			{
				echo '<p class="erreur">' . sprintf(T_("Impossible d'ouvrir le fichier %1\$s."), '<code>' . $cheminFichier . '</code>') . '</p>';
			}
		}
		else
		{
			echo '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux global des galeries puisque le fichier %1\$s n'existe pas. <a href='%2\$s'>Vous pouvez créer ce fichier</a>."), $cheminFichier, '<a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/rss-global-galeries.txt#messagesPorteDocuments">');
		}
	}
	
	elseif (isset($_POST['global']) && $_POST['global'] == 'site')
	{
		$cheminFichier = "$racine/site/inc/rss-global-site.txt";
		if (file_exists($cheminFichier))
		{
			if ($pages = file($cheminFichier))
			{
				echo "<form action='$action#messages' method='post'><div>\n";
				
				if (!empty($pages))
				{
					$listePages = '';
					$i = 0;
					foreach ($pages as $page)
					{
						$page = rtrim($page);
						$listePages .= "<input type='text' name='url$i' value='$page' /><br />\n";
						
						$i++;
					}
				}
				
				echo '<div class="boite2">' . "\n";
				echo '<h3>' . T_("Liste des pages autres que les galeries") . '</h3>' . "\n";
				echo '<p>' . sprintf(T_("Chaque ligne est sous la forme <code>URL relative de la page</code>. Par exemple, %1\$s fait référence à une page dont l'URL est %2\$s."), "<code>animaux/chiens.php</code>", "<code>$urlRacine/animaux/chiens.php</code>") . '</p>';
	
				if (!empty($listePages))
				{
					echo $listePages;
				}
				else
				{
					echo '<p>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</p>\n";
				}
				
				echo '<p><strong>' . T_("Ajouter une page:") . '</p></strong>';
				echo "<input type='text' name='url$i' value='' /><br />\n";
				$i++;
				
				echo "<p><input type='submit' name='modifsSite' value='" . T_('Enregistrer mes modifications') . "' /></p>\n";
				echo "</div></form>\n";
				
				echo "</div><!-- /boite2 -->\n";
			}
			else
			{
				echo '<p class="erreur">' . sprintf(T_("Impossible d'ouvrir le fichier %1\$s."), '<code>' . $cheminFichier . '</code>') . '</p>';
			}
		}
		else
		{
			echo '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux global du site puisque le fichier %1\$s n'existe pas. <a href='%2\$s'>Vous pouvez créer ce fichier</a>."), $cheminFichier, '<a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/rss-global-site.txt#messagesPorteDocuments">');
		}
	}
}

if (isset($_POST['modifsGaleries']))
{
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Enregistrement des modifications pour les galeries") .'</h3>' ."\n" ;
	
	$contenuFichier = '';
	$i = 0;
	while (isset($_POST['id' . $i]) && isset($_POST['url' . $i]))
	{
		if (!empty($_POST['id' . $i]) && !empty($_POST['url' . $i]))
		{
			$contenuFichier .= $_POST['id' . $i] . '=' . $_POST['url' . $i] . "\n";
		}
		
		$i++;
	}
	
	$cheminFichier = "$racine/site/inc/rss-global-galeries.txt";
	if (file_exists($cheminFichier))
	{
		if (file_put_contents($cheminFichier, $contenuFichier))
		{
			echo '<p class="succes">' . sprintf(T_("Les modifications ont été enregistrées. Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . $cheminFichier . '</code>') . '</p>';
			echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
			echo "<ul>\n";
			echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			echo "</ul>\n";
		}
		else
		{
			echo '<p class="erreur">' . sprintf(T_("Impossible d'ouvrir le fichier %1\$s."), '<code>' . $cheminFichier . '</code>') . '</p>';
			echo '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . '</p>';
			echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
			echo "<ul>\n";
			echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			echo "</ul>\n";
		}
	}
	else
	{
		echo '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux global des galeries puisque le fichier %1\$s n'existe pas. <a href='%2\$s'>Vous pouvez créer ce fichier</a>."), $cheminFichier, '<a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/rss-global-galeries.txt#messagesPorteDocuments">');
		echo '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . '</p>';
		echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
		echo "<ul>\n";
		echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
		echo "</ul>\n";
	}
	
	echo "</div><!-- /boite2 -->\n";
}

elseif (isset($_POST['modifsSite']))
{
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Enregistrement des modifications pour les pages autres que les galeries") .'</h3>' ."\n" ;
	
	$contenuFichier = '';
	$i = 0;
	while (isset($_POST['url' . $i]))
	{
		if (!empty($_POST['url' . $i]))
		{
			$contenuFichier .= $_POST['url' . $i] . "\n";
		}
		
		$i++;
	}
	
	$cheminFichier = "$racine/site/inc/rss-global-site.txt";
	if (file_exists($cheminFichier))
	{
		if (file_put_contents($cheminFichier, $contenuFichier))
		{
			echo '<p class="succes">' . sprintf(T_("Les modifications ont été enregistrées. Voici le contenu qui a été enregistré dans le fichier %1\$s:"), '<code>' . $cheminFichier . '</code>') . '</p>';
			echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
			echo "<ul>\n";
			echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			echo "</ul>\n";
		}
		else
		{
			echo '<p class="erreur">' . sprintf(T_("Impossible d'ouvrir le fichier %1\$s."), '<code>' . $cheminFichier . '</code>') . '</p>';
			echo '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . '</p>';
			echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
			echo "<ul>\n";
			echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
			echo "</ul>\n";
		}
	}
	else
	{
		echo '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux global du site puisque le fichier %1\$s n'existe pas. <a href='%2\$s'>Vous pouvez créer ce fichier</a>."), $cheminFichier, '<a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/rss-global-site.txt#messagesPorteDocuments">');
		echo '<p>' . T_("Voici le contenu qui aurait été enregistré dans le fichier:") . '</p>';
		echo '<pre id="contenuFichier">' . $contenuFichier . '</pre>' . "\n";
		echo "<ul>\n";
		echo "<li><a href=\"javascript:selectionneTexte('contenuFichier');\">" . T_("Sélectionner le résultat.") . "</a></li>\n";
		echo "</ul>\n";
	}
	
	echo "</div><!-- /boite2 -->\n";
}
?>

</div><!-- /boiteMessages -->

<div class="boite">
<h2><?php echo T_("Configuration actuelle"); ?></h2>

<?php
echo '<ul>';
if (adminFluxGlobal('galerie', $racine))
{
	echo '<li>' . T_("Le flux RSS global des galeries est activé") . ' (<code>$galerieFluxGlobal = TRUE;</code>).</li>';
}
else
{
	echo '<li>' . T_("Le flux RSS global des galeries n'est pas activé") . ' (<code>$galerieFluxGlobal = FALSE;</code>).</li>';
}

if (adminFluxGlobal('site', $racine))
{
	echo '<li>' . T_("Le flux RSS global du site est activé") . ' (<code>$siteFluxGlobal = TRUE;</code>).</li>';
}
else
{
	echo '<li>' . T_("Le flux RSS global du site n'est pas activé") . ' (<code>$siteFluxGlobal = FALSE;</code>).</li>';
}
echo '</ul>';

echo '<p><a href="porte-documents.admin.php?action=modifier&valeur=../site/inc/config.inc.php#messagesPorteDocuments">' . T_("Modifier cette configuration.") . '</a></p>';
?>
</div><!-- /boite -->

<div class="boite">
<h2><?php echo T_("Pages ajoutées aux flux globaux"); ?></h2>

<form action="<?php echo $action; ?>#messages" method="post">
<div>
<p><input type="radio" name="global" value="galeries" /> <?php echo T_("Pages des galeries"); ?><br />
<input type="radio" name="global" value="site" /> <?php echo T_("Autres pages"); ?></p>

<p><input type="submit" name="lister" value="<?php echo T_('Lister les pages'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
