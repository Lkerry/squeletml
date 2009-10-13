<?php
include 'inc/zero.inc.php';
$baliseTitle = T_("Flux RSS globaux");
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
		################################################################
		#
		# Pages des galeries
		#
		################################################################
		
		$cheminFichier = "$racine/site/inc/rss-global-galeries.pc";
		if (file_exists($cheminFichier))
		{
			if ($fic = fopen($cheminFichier, 'r'))
			{
				echo "<form action='$action#messages' method='post'><div>\n";
				
				$galeries = tableauAssociatif($cheminFichier);
				$listeGaleries = '';
				if (!empty($galeries))
				{
					foreach ($galeries as $codeLangueIdGalerie => $urlRelativeGalerie)
					{
						list ($codeLangue, $idGalerie) = explode(':', $codeLangueIdGalerie, 2);
						$listeGaleries .= '<input type="text" name="langue[]" value="' . $codeLangue . '" />:<input type="text" name="id[]" value="' . $idGalerie . '" />=<input type="text" name="url[]" value="' . $urlRelativeGalerie . '" /><br />' . "\n";
					}
				}
				
				fclose($fic);
				
				echo '<div class="boite2">' . "\n";
				echo '<h3>' . T_("Liste des pages des galeries") . '</h3>' . "\n";
				echo '<p>' . sprintf(T_("Chaque ligne est sous la forme <code>code de la langue:identifiant de la galerie=URL relative de la galerie</code>. Par exemple, %1\$s fait référence à une galerie en français dont l'identifiant est %2\$s et dont l'URL est %3\$s."), "<code>fr:chiens=animaux/chiens.php</code>", "<code>chiens</code>", "<code>$urlRacine/animaux/chiens.php</code>") . '</p>';
	
				if (!empty($listeGaleries))
				{
					echo $listeGaleries;
				}
				else
				{
					echo '<p>' . T_("Le fichier est vide. Aucune galerie n'y est listée.") . "</p>\n";
				}
				
				echo '<p><strong>' . T_("Ajouter une galerie:") . '</p></strong>';
				echo '<input type="text" name="langueAjout" value="" />:<input type="text" name="idAjout" value="" />=<input type="text" name="urlAjout" value="" /><br />' . "\n";
				
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
			echo '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), $cheminFichier, 'porte-documents.admin.php?action=editer&valeur=../site/inc/rss-global-galeries.pc#messagesPorteDocuments');
		}
	}
	
	elseif (isset($_POST['global']) && $_POST['global'] == 'site')
	{
		################################################################
		#
		# Autres pages
		#
		################################################################
		
		$cheminFichier = "$racine/site/inc/rss-global-site.pc";
		if (file_exists($cheminFichier))
		{
			if (is_array($pages = file($cheminFichier)))
			{
				echo "<form action='$action#messages' method='post'><div>\n";
				
				if (!empty($pages))
				{
					$listePages = '';
					foreach ($pages as $page)
					{
						if (strpos($page, ':') !== FALSE)
						{
							list ($codeLangue, $page) = explode(':', $page, 2);
						}
						$page = rtrim($page);
						if (!empty($codeLangue) && !empty($page))
						{
							$listePages .= '<input type="text" name="langue[]" value="' . $codeLangue . '" />:<input type="text" name="url[]" value="' . $page . '" /><br />' . "\n";
						}
					}
				}
				
				echo '<div class="boite2">' . "\n";
				echo '<h3>' . T_("Liste des pages autres que les galeries") . '</h3>' . "\n";
				echo '<p>' . sprintf(T_("Chaque ligne est sous la forme <code>code de la langue:URL relative de la page</code>. Par exemple, %1\$s fait référence à une page en français dont l'URL est %2\$s."), "<code>fr:animaux/chiens.php</code>", "<code>$urlRacine/animaux/chiens.php</code>") . '</p>';
	
				if (!empty($listePages))
				{
					echo $listePages;
				}
				else
				{
					echo '<p>' . T_("Le fichier est vide. Aucune page n'y est listée.") . "</p>\n";
				}
				
				echo '<p><strong>' . T_("Ajouter une page:") . '</p></strong>';
				echo '<input type="text" name="langueAjout" value="" />:<input type="text" name="urlAjout" value="" /><br />' . "\n";
				
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
			echo '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), $cheminFichier, 'porte-documents.admin.php?action=editer&valeur=../site/inc/rss-global-site.pc#messagesPorteDocuments');
		}
	}
}

if (isset($_POST['modifsGaleries']))
{
	echo '<div class="boite2">' . "\n";
	echo '<h3>' . T_("Enregistrement des modifications pour les galeries") .'</h3>' ."\n" ;
	
	$contenuFichierTableau = array ();
	if (isset($_POST['langue']))
	{
		foreach ($_POST['langue'] as $cle => $postLangueValeur)
		{
			if (isset($postLangueValeur) && !empty($postLangueValeur) && isset($_POST['id'][$cle]) && !empty($_POST['id'][$cle]) && isset($_POST['url'][$cle]) && !empty($_POST['url'][$cle]))
			{
				$contenuFichierTableau[] = $postLangueValeur . ':' . sansEchappement($_POST['id'][$cle]) . '=' . sansEchappement($_POST['url'][$cle]) . "\n";
			}
		}
	}
	
	if (isset($_POST['langueAjout']) && !empty($_POST['langueAjout']) && isset($_POST['idAjout']) && !empty($_POST['idAjout']) && isset($_POST['urlAjout']) && !empty($_POST['urlAjout']))
	{
		array_unshift($contenuFichierTableau, $_POST['langueAjout'] . ':' . sansEchappement($_POST['idAjout']) . '=' . sansEchappement($_POST['urlAjout']) . "\n");
	}
	
	$contenuFichier = implode('', $contenuFichierTableau);
	
	$cheminFichier = "$racine/site/inc/rss-global-galeries.pc";
	if (file_exists($cheminFichier))
	{
		if (file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
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
		echo '<p class="erreur">' . sprintf(T_("Aucune galerie ne peut faire partie du flux RSS global des galeries puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), $cheminFichier, 'porte-documents.admin.php?action=editer&valeur=../site/inc/rss-global-galeries.pc#messagesPorteDocuments');
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
	
	$contenuFichierTableau = array ();
	if (isset($_POST['langue']))
	{
		foreach ($_POST['langue'] as $cle => $postLangueValeur)
		{
			if (isset($postLangueValeur) && !empty($postLangueValeur) && isset($_POST['url'][$cle]) && !empty($_POST['url'][$cle]))
			{
				$contenuFichierTableau[] = $postLangueValeur . ':' . sansEchappement($_POST['url'][$cle]) . "\n";
			}
		}
	}
	
	if (isset($_POST['langueAjout']) && !empty($_POST['langueAjout']) && isset($_POST['urlAjout']) && !empty($_POST['urlAjout']))
	{
		array_unshift($contenuFichierTableau, $_POST['langueAjout'] . ':' . sansEchappement($_POST['urlAjout']) . "\n");
	}
	
	$contenuFichier = implode('', $contenuFichierTableau);
	
	$cheminFichier = "$racine/site/inc/rss-global-site.pc";
	if (file_exists($cheminFichier))
	{
		if (file_put_contents($cheminFichier, $contenuFichier) !== FALSE)
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
		echo '<p class="erreur">' . sprintf(T_("Aucune page ne peut faire partie du flux RSS global du site puisque le fichier %1\$s n'existe pas. <a href=\"%2\$s\">Vous pouvez créer ce fichier</a>."), $cheminFichier, 'porte-documents.admin.php?action=editer&valeur=../site/inc/rss-global-site.pc#messagesPorteDocuments');
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
if (adminFluxRssGlobal('galerie', $racine))
{
	echo '<li>' . T_("Le flux RSS global des galeries est activé") . ' (<code>$galerieFluxRssGlobal = TRUE;</code>).</li>';
}
else
{
	echo '<li>' . T_("Le flux RSS global des galeries n'est pas activé") . ' (<code>$galerieFluxRssGlobal = FALSE;</code>).</li>';
}

if (adminFluxRssGlobal('site', $racine))
{
	echo '<li>' . T_("Le flux RSS global du site est activé") . ' (<code>$siteFluxRssGlobal = TRUE;</code>).</li>';
}
else
{
	echo '<li>' . T_("Le flux RSS global du site n'est pas activé") . ' (<code>$siteFluxRssGlobal = FALSE;</code>).</li>';
}
echo '</ul>';

echo '<p><a href="porte-documents.admin.php?action=editer&valeur=../site/inc/config.inc.php#messagesPorteDocuments">' . T_("Modifier cette configuration.") . '</a></p>';
?>
</div><!-- /boite -->

<div class="boite">
<h2><?php echo T_("Pages ajoutées aux flux RSS globaux"); ?></h2>

<form action="<?php echo $action; ?>#messages" method="post">
<div>
<p><input type="radio" name="global" value="galeries" /> <?php echo T_("Pages des galeries"); ?><br />
<input type="radio" name="global" value="site" checked="checked" /> <?php echo T_("Pages autres que les galeries"); ?></p>

<p><input type="submit" name="lister" value="<?php echo T_('Lister les pages'); ?>" /></p>
</div>
</form>
</div><!-- /boite -->

<?php include $racine . '/admin/inc/dernier.inc.php'; ?>
