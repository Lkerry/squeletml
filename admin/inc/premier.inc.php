<?php
########################################################################
##
## Affectations et inclusions.
##
########################################################################

if (!isset($adminBalisesLinkScriptFinales))
{
	$adminBalisesLinkScriptFinales = array ();
}

$baliseTitle .= ' | ' . T_("Administration de Squeletml");

if (!isset($boitesDeroulantes))
{
	$boitesDeroulantes = '';
}

if (!isset($tableDesMatieres))
{
	$tableDesMatieres = FALSE;
}

if ($tableDesMatieres)
{
	$boitesDeroulantes .= ' tableDesMatieres';
}

$boitesDeroulantesTableau = boitesDeroulantes($adminBoitesDeroulantesParDefaut, $boitesDeroulantes);
$cheminAncres = adminCheminXhtml($racineAdmin, 'ancres');
$cheminRaccourcis = adminCheminXhtml($racineAdmin, 'raccourcis');
$doctype = doctype($adminXhtmlStrict);
$idBody = adminBodyId();

if (!empty($idBody))
{
	$idBody = ' id="' . $idBody . '"';
}

$locale = locale(LANGUE);

// Menu.
ob_start();
include_once adminCheminXhtml($racineAdmin, 'menu');
$menu = ob_get_contents();
ob_end_clean();
$menu = lienActif($menu, FALSE);

$nomPage = nomPage();
$url = url();
$urlDeconnexion = adminUrlDeconnexion($urlRacine);
$urlFichiers = $urlRacine . '/site/fichiers';
$urlSite = $urlRacine . '/site';

########################################################################
##
## Ajouts dans `$adminBalisesLinkScript`.
##
########################################################################

if (!adminEstIe() && $adminAideEdition != 'BUEditor')
{
	$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/wz_dragdrop/wz_dragdrop.js";
}

// Boîtes déroulantes.

if (!empty($boitesDeroulantesTableau))
{
	$adminBalisesLinkScript[] = "$url#css#$urlRacine/css/boites-deroulantes.css";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.cookie.js";
	$jsDirect = '';
	
	foreach ($boitesDeroulantesTableau as $boiteDeroulante)
	{
		$jsDirect .= "\tajouteEvenementLoad(function(){boiteDeroulante('$boiteDeroulante');});\n";
	}
	
	$adminBalisesLinkScript[] = "$url#jsDirect#$jsDirect";
}

// Aide lors de l'édition.

if ($adminAideEdition == 'BUEditor')
{
	$adminBalisesLinkScript[] = "$url#css#$urlRacineAdmin/js/bueditor/bueditor.css";
	$adminBalisesLinkScript[] = "$url#js#$urlRacineAdmin/js/bueditor/bueditor.js";
	$adminBalisesLinkScript[] = "$url#js#$urlRacineAdmin/js/bueditor/library/default_buttons_functions.js";
	
	$jsDirect = '';
	$jsDirect .= "editor.path = 'js/bueditor/';\n";
	$jsDirect .= "editor.buttons = [\n";
	$jsDirect .= "['" . T_("Prévisualisation") . "', 'js: eDefPreview();', 'preview.png', 'P'],\n";
	
	$jsDirect .= "['" . T_("Emphase forte") . "', '<strong>%TEXT%</strong>', 'bold.png', 'B'],\n";
	
	$jsDirect .= "['" . T_("Emphase légère") . "', '<em>%TEXT%</em>', 'italic.png', 'I'],\n";
	
	$js = "js:
	var B = eDefBrowseButton('', 'attr_href', 'Browse', 'link');
	var form = [
		{name: 'href', title: '" . T_("Attribut «href» (adresse)") . "', suffix: B},
		{name: 'title', title: '" . T_("Attribut «title» (optionnel)") . "'}
	];
	eDefTagDialog('a', form, '" . T_("Lien") . "', '" . T_("Ajouter") . "');";
		$jsDirect .= "['" . T_("Lien") . "', \"" . str_replace("\n", ' ', $js) . "\", 'link.png', 'L'],\n";
	
	$js = "js:
	var B = eDefBrowseButton('', 'attr_src', 'Browse', 'image');
	var form = [
		{name: 'class', title: '" . T_("Classe (style)") . "', type: 'select', options: {'': '', gauche: '" . T_("Gauche") . "', centre: '" . T_("Centre") . "', droite: '" . T_("Droite") . "'}},
		{name: 'src', value: '" . $urlFichiers . "/', title: '" . T_("URL") . "', suffix: B},
		{name: 'width', title: '" . T_("Largeur × hauteur") . "', suffix: '" . T_(" × ") . "', getnext: true, attributes: {size: 3}},
		{name: 'height', attributes: {size: 3}},
		{name: 'alt', title: '" . T_("Texte alternatif") . "'}
	];
	eDefTagDialog('img', form, '" . T_("Image") . "', '" . T_("Ajouter") . "');";
	$jsDirect .= "['" . T_("Image") . "', \"" . str_replace("\n", ' ', $js) . "\", 'image.png', 'M'],\n";
	
	$jsDirect .= "['" . T_("Liste non ordonnée") . "', \"js: eDefSelProcessLines('<ul>\\\\n', '  <li>', '</li>', '\\\\n</ul>');\", 'ul.png', 'U'],\n";
	
	$jsDirect .= "['" . T_("Liste ordonnée") . "', \"js: eDefSelProcessLines('<ol>\\\\n', '  <li>', '</li>', '\\\\n</ol>');\", 'ol.png', 'O'],\n";
	
	$jsDirect .= "['" . T_("Fin de l\'aperçu") . "', '<!-- /aperçu -->', 'teaserbr.png', 'T'],\n";
	
	$jsDirect .= "['" . T_("Titre de premier niveau") . "', '<h1>%TEXT%</h1>', 'h1', ''],\n";
	
	$jsDirect .= "['" . T_("Titre de deuxième niveau") . "', '<h2>%TEXT%</h2>', 'h2', ''],\n";
	
	$jsDirect .= "['" . T_("Titre de troisième niveau") . "', '<h3>%TEXT%</h3>', 'h3', ''],\n";
	
	$jsDirect .= "['" . T_("Titre de quatrième niveau") . "', '<h4>%TEXT%</h4>', 'h4', ''],\n";
	
	$jsDirect .= "['" . T_("Titre de cinquième niveau") . "', '<h5>%TEXT%</h5>', 'h5', ''],\n";
	
	$jsDirect .= "['" . T_("Titre de sixième niveau") . "', '<h6>%TEXT%</h6>', 'h6', ''],\n";
	
	$jsDirect .= "['" . T_("Division") . "', '<div>%TEXT%</div>', 'div', ''],\n";
	
	$jsDirect .= "['" . T_("Ligne horizontale") . "', '<hr />', 'hr', ''],\n";
	
	$jsDirect .= "['" . T_("Paragraphe") . "', '<p>%TEXT%</p>', 'p', ''],\n";
	
	$js = "js:
	var balise = prompt('" . T_("Balise personnalisée") . "', '');
	var code = '<' + balise + '>' + '</' + balise + '>';
	E.replaceSelection(code);";
	$jsDirect .= "['" . T_("Balise personnalisée") . "', \"" . str_replace("\n", ' ', $js) . "\", '<?>', ''],\n";
	
	$js = "js:
	var code = prompt('" . T_("Insérer le code proposé pour intégrer la vidéo. Ce dernier sera modifié pour devenir valide XHTML 1.0 Strict. Testé avec blip.tv, Dailymotion, Google Vidéos, Metacafe, Vimeo et Youtube.") . "', '');
	
	if (code == null)
	{
		code = '';
	}
	
	var codeValide = new Array();
	
	if (code.search(/googleplayer\.swf/) != -1)
	{
		codeValide = code.match(/<embed .* src=([^ ]+) style=width:([0-9]+)px;height:([0-9]+)px/);
	}
	else
	{
		codeValide = code.match(/<embed src=\\\"(.+?)\\\".* width=\\\"(.+?)\\\".* height=\\\"(.+?)\\\"/);
	}
	
	if (codeValide == null)
	{
		codeValide = ['', '', '', ''];
	}
	
	if (codeValide[1].search(/&amp;/) == -1)
	{
		codeValide[1] = strtr(codeValide[1], {'&' : '&amp;'});
	}
	
	E.replaceSelection('<div class=\\\"video\\\"><object type=\\\"application/x-shockwave-flash\\\" data=\\\"' + codeValide[1] + '\\\" width=\\\"' + codeValide[2] + '\\\" height=\\\"' + codeValide[3] + '\\\"><param name=\\\"movie\\\" value=\\\"' + codeValide[1] + '\\\" /><param name=\\\"wmode\\\" value=\\\"transparent\\\" /></object></div>');";
	$jsDirect .= "['" . T_("Vidéo") . "', \"" . str_replace("\n", ' ', $js) . "\", '" . T_("vidéo") . "', '']\n";
	
	$jsDirect .= "];\n";
	$adminBalisesLinkScript[] = "$url#jsDirect#$jsDirect";
}
elseif ($adminAideEdition == 'CodeMirror')
{
	$adminBalisesLinkScript[] = "$urlRacineAdmin/porte-documents.admin.php*#js#$urlRacineAdmin/js/CodeMirror/js/codemirror.js";
}

// Table des matières.

if ($tableDesMatieres)
{
	$adminBalisesLinkScript[] = "$url#css#$urlRacine/css/table-des-matieres.css";
	$adminBalisesLinkScript[] = "$url#cssltIE7#$urlRacine/css/table-des-matieres-ie6.css";
	
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/Gettext/lib/Gettext.js";
	
	if (file_exists($racine . '/locale/' . $locale))
	{
		$adminBalisesLinkScript[] = "$url#po#$urlRacine/locale/$locale/LC_MESSAGES/squeletml.po";
	}
	
	$adminBalisesLinkScript[] = "$url#jsDirect#var gt = new Gettext({'domain': 'squeletml'});";
	
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery.min.js";
	$adminBalisesLinkScript[] = "$url#js#$urlRacine/js/jquery/jquery-tableofcontents/jquery.tableofcontents.js";
	$adminBalisesLinkScript[] = "$url#jsDirect#tableDesMatieres('interieurContenu', 'ul', 'h2');";
}

// Variable finale.

$linkScript = linkScript($adminBalisesLinkScript, '', TRUE);

########################################################################
##
## Traitement personnalisé optionnel.
##
########################################################################

if (file_exists("$racine/site/$dossierAdmin/inc/premier.inc.php"))
{
	include_once "$racine/site/$dossierAdmin/inc/premier.inc.php";
}

########################################################################
##
## Code XHTML 1 de 2.
##
########################################################################

include_once adminCheminXhtml($racineAdmin, 'page.premier');
?>
