## Dépendances

Squeletml requiert un serveur Apache avec PHP 5 (et le module PHP `mbstring` activé). Aucune base de données n'est utilisée.

Certains dossiers doivent être accessibles en écriture:

- lors de l'installation, des fichiers sont créés dans le dossier racine de Squeletml, et possiblement dans des dossiers de langue (pour l'instant, seulement `en/`);

- l'administration de Squeletml peut devoir créer des fichiers dans différents sous-dossiers de `site/`, principalement les suivants:

	- `site/admin/cache/`
	- `site/cache/`
	- `site/fichiers/galeries/`
	- `site/inc/`

### Notes

- Pour utiliser la fonction de mise en maintenance du site, la réécriture d'URL doit être activée sur le serveur.

- Pour générer automatiquement des vignettes ou redimensionner des images d'une galerie, la bibliothèque GD doit être installée (par exemple, sous Ubuntu 10.10, vérifier que le paquet `php5-gd` est installé).

- Pour afficher les données Exif dans les galeries, la fonction PHP `exif_read_data()` doit être disponible.

- La rotation automatique et sans perte de qualité des images JPG peut se faire avec la commande `exiftran` sur le système, ou bien avec la commande `jpegtran` sur le système et la fonction PHP `exif_read_data()`.

- La suppression sans perte de qualité des données Exif des images JPG peut se faire avec la commande `jpegtran` sur le système.

- La récupération de contenu externe se fait par `file_get_contents()`. À ce sujet, il semble que **Free.fr** a modifié au début 2010 la configuration de ses serveurs, et une requête HTTP retourne systématiquement un code d'erreur 403. Par exemple, `file_get_contents('http://www.site.ext/');` retourne ceci:

		HTTP/1.0 403 Forbidden

	Selon la [FAQ du site «Les Pages Perso Chez Free»](http://les.pages.perso.chez.free.fr/index.php?post/2010/01/22/FAQ), il faut demander sur le forum d'aide de Free.fr de mettre sur la liste blanche les URL que l'on souhaite atteindre.

## Installation

La procédure d'installation est très simple. Il faut commencer par [télécharger l'archive de la dernière version](https://github.com/jpfleury/squeletml/archive/master.zip), extraire cette archive et placer les fichiers sur votre serveur, par exemple par ftp.

### Mise en situation

On télécharge et extrait l'archive, ce qui crée le dossier `projets-divers-squeletml`.

- Pour avoir un site situé dans un répertoire, copier le dossier `projets-divers-squeletml` sur le serveur et le renommer comme désiré, ce qui nous donne l'adresse `http://www.nomDeDomaine.ext/monSiteSqueletml/`.

- Pour avoir un site situé à la racine d'un nom de domaine, transférer sur le serveur le contenu du dossier `projets-divers-squeletml`, ce qui nous donne l'adresse `http://www.nomDeDomaine.ext/`.

	**Important:** à la racine du dossier créé par l'extraction de l'archive, il y a au moins un fichier dont le nom débute par un point. De tels fichiers sont dits «cachés» (à tout le moins sur les systèmes de type Unix), et pour les voir, il faut généralement que le gestionnaire de fichiers soit configuré en conséquence ou que la ligne de commande comporte un argument conséquent. Par exemple, dans Nautilus, on peut afficher les fichiers cachés en allant dans *Affichage* et en cochant *Afficher les fichiers cachés*. En ligne de commande, `ls` accepte l'argument `-a`:

		ls -a

On termine l'installation de façon automatisée ou à la main.

### Installation automatisée

Visiter la page d'accueil de votre nouveau site et suivre les quelques indications.

Si tout se passe bien, Squeletml créera automatiquement les fichiers nécessaires et la structure par défaut de votre site sera accessible.

#### Mise en situation

On visite dans notre navigateur la page d'accueil du nouveau site, ce qui nous donne `http://www.nomDeDomaine.ext/monSiteSqueletml/` ou `http://www.nomDeDomaine.ext/` selon le choix effectué à l'étape 1, et on suit les indications affichées.

### Installation à la main

Par défaut, l'installation se fait en passant par une page web. Cependant, il est possible d'installer Squeletml à la main.

Seulement quatre fichiers doivent obligatoirement être créés et configurés, bien que vous voudrez probablement personnaliser ensuite le reste du site.

Tant que l'installation n'est pas terminée, la page d'accueil de votre site Squeletml dirigera vers la procédure d'installation par défaut passant par une page web.

#### Le fichier `init.inc.php`

Avec la méthode de votre choix (par exemple en vous connectant par ftp au serveur hébergeant votre site), trouver le fichier `modeles/init.inc.php.modele`, le copier et le coller sous le nom `init.inc.php` (il faut donc enlever le `.modele` final du nom) à la racine du site.

Ce fichier contient deux variables obligatoires à renseigner:

- `$urlRacine`: il s'agit de l'adresse URL vers votre site Squeletml. Par exemple, `http://www.nomDeDomaine.ext/monSiteSqueletml` ou `http://www.nomDeDomaine.ext`.

- `$accueil`: il s'agit d'un tableau contenant l'adresse URL de l'accueil pour chaque langue de votre site.

	Vous pouvez ajouter autant de langues que vous le désirez, dans la mesure où cette langue est offerte par défaut avec Squeletml ou que vous en effectuiez la traduction. Le français est la langue par défaut. Ainsi, l'interface de Squeletml sera toujours entièrement disponible dans cette langue. Pour l'instant, une traduction partielle en anglais accompagne Squeletml.

	Vous pouvez également commenter les langues dont vous ne vous servez pas. Seules les langues existant dans le tableau `$accueil` apparaissent dans le menu des langues généré automatiquement par Squeletml.

	Exemple d'un site n'utilisant que le français:

		$accueil['fr'] = $urlRacine;
		#$accueil['en'] = $urlRacine . '/en';

	Exemple d'un site utilisant le français et l'anglais:

		$accueil['fr'] = $urlRacine;
		$accueil['en'] = $urlRacine . '/en';

	Si une langue n'est pas utilisée, il est conseillé de supprimer son dossier d'accueil ou de créer dans ce dossier un fichier interdisant son accès. Par exemple, si le site n'utilise pas l'anglais, nous pourrions supprimer le dossier d'accueil par défaut de cette langue, c'est-à-dire `$urlRacine/en`, ou y mettre un fichier `.htaccess` (donc `$urlRacine/en/.htaccess`) contenant ceci:

		Deny from all

	D'ailleurs, un tel fichier `.htaccess` est ajouté par l'installateur automatisé dans chaque dossier d'accueil d'une langue inactive.

**Note: il est possible de renommer le dossier d'administration pour plus de sécurité, qui est par défaut `admin`. La variable à modifier, `$adminDossierAdmin`, se trouve dans le fichier `init.inc.php`. Le cas échéant, modifier le chemin vers l'administration dans les exemples de cette documentation. Par exemple, modifier ceci:**

	http://www.nomDeDomaine.ext/admin/acces.admin.php

pour:

	http://www.nomDeDomaine.ext/$dossierAdmin/acces.admin.php

##### Cas des serveurs de Free.fr

Pour une installation de Squeletml sur un serveur de Free.fr, il faut effectuer en plus l'action suivante:

- trouver la variable `$serveurFreeFr` et modifier sa valeur pour `TRUE`.

#### Protection de l'administration (fichiers `.htaccess` et `.acces`)

Trouver le fichier `modeles/.htaccess.modele`, le copier et le coller sous le nom `.htaccess` (il faut donc enlever le `.modele` final du nom) à la racine du site. Ensuite, visiter la page suivante:

	http://www.nomDeDomaine.ext/admin/acces.admin.php

et ajouter un utilisateur. Un fichier `.acces` sera créé à la racine de votre site Squeletml. Ce fichier contient la liste des utilisateurs.

##### Cas des serveurs de Free.fr

Si Squeletml est installé sur un serveur de Free.fr, le fichier à trouver est `modeles/.htaccess.free.fr.modele`. La suite est la même (copie et collage sous le nom `.htaccess` à la racine du site).

##### Chemin des pages d'erreur

Le fichier `.htaccess` contient entre autres l'adresse URL vers la page d'erreur 404 (toute personne tentant de visiter une page qui n'existe pas ou qui n'existe plus sur votre site sera redirigée vers cette page explicative). Si en visitant une page inexistante, vous ne voyez pas le contenu de la page d'erreur, ouvrez le fichier `.htaccess`, trouvez la ligne:

	ErrorDocument 404 /404.php

et modifiez `/404.php` par la bonne adresse URL, par exemple `http://www.nomDeDomaine.ext/404.php`.

D'autres pages d'erreur sont déclarées dans le `.htaccess`, comme celle pour l'erreur 401. Au besoin, modifier dans le `.htaccess` le chemin de toutes les pages d'erreur.

#### Déclaration de l'installation terminée

Créer un fichier vide `squeletml-est-installe.txt` dans le dossier `site/inc`, ce qui donne:

	site/inc/squeletml-est-installe.txt

L'installation par défaut passant par une page web sera ainsi désactivée.

#### Le fichier `robots.txt`

Vous pouvez trouver le fichier `modeles/robots.txt.modele`, le copier et le coller sous le nom `robots.txt` (il faut donc enlever le `.modele` final du nom) à la racine du site.

Vous pouvez personnaliser ce fichier ou le laisser tel quel.

## Architecture de Squeletml

### Dossiers

L'arborescence de Squeletml est la suivante:

- `admin` (le cas échéant, modifier le nom du dossier d'administration)
- `css`
- `doc`
- `en`
- `fichiers`
- `inc`
- `js`
- `locale`
- `modeles`
- **`site`**
- `xhtml`

Tous les dossiers, à part `site`, font partie de la structure officielle par défaut de Squeletml, et les fichiers y étant contenus ne doivent pas être modifiés, sous peine de perdre les modifications lors d'une mise à jour de Squeletml.

**Toute modification apportée à la configuration par défaut doit donc être effectuée dans le dossier `site`.**

Par exemple, pour modifier l'apparence du site, **ne modifiez pas** le fichier `css/style-general.css`, mais créez un fichier dans `site/css`, par exemple `site/css/style.css`. Il faudra ensuite ajouter ce fichier dans les styles à inclure. Nous verrons comment faire un peu plus loin.

Pour ajouter des images, mettez vos fichiers dans `site/fichiers`. Pour ajouter des fichiers à inclure, créez des fichiers dans `site/inc`. Vous aurez compris, le dossier `site` recrée la structure par défaut de Squeletml, ce qui permet de pouvoir facilement personnaliser le site sans toucher aux fichiers officiels de Squeletml. La mise à jour du logiciel en sera grandement facilitée.

#### Le dossier `inc`

Le dossier `inc` contient tous les scripts déclarant et affectant les variables qui seront utilisées pour construire la structure XHTML. Aucun fichier de ce dossier ne génère un affichage vers le navigateur. Il y a donc une séparation dans Squeletml entre les fichiers d'analyse et ceux contenant le contenu envoyé au navigateur.

Les fichiers du dossier `inc` peuvent être personnalisés en créant un fichier de même nom dans `site/inc`, et Squeletml reconnaîtra automatiquement le fichier personnel:

- `blocs.inc.php`: construit le code XHTML des blocs (menu des langues, menu principal, liens vers les flux RSS, etc.) pour une région spécifique. Après son inclusion, la variable `$blocs` est prête à être utilisée. Vous pouvez modifier cette variable en créant le fichier `site/inc/blocs.inc.php`, qui sera inséré à la fin de celui par défaut.

- `cache-partiel.inc.php`: vérifie si le cache partiel doit être généré, affiché ou enregistré.

- `categorie.inc.php`: construit et analyse la liste des articles classés dans la catégorie demandée. Après son inclusion, la variable `$categorie` est prête à être utilisée. Vous pouvez modifier cette variable en créant le fichier `site/inc/categorie.inc.php`, qui sera inséré à la fin de celui par défaut.

- `commentaire.inc.php`: construit et analyse le formulaire d'ajout d'un commentaire. Après son inclusion, la variable `$formulaireCommentaire` est prête à être utilisée. Il est possible d'interagir avec ce script en créant le fichier `site/inc/commentaire.inc.php`, qui sera inséré à différentes étapes du script.

- `config.inc.php`: contient presque toute la configuration du site, autant pour le formulaire de contact, les galeries photo, le titre du site, etc. Pour modifier une variable de ce fichier, créer le fichier `site/inc/config.inc.php` et y réaffecter les variables dont vous voulez changer la valeur. S'il existe, ce fichier sera inséré après celui par défaut.

	Quelques variables peuvent être utilisées dans le fichier de configuration personnalisé pour aider à construire des liens:

  - `$urlFichiers`
  - `$urlRacineAdmin`
  - `$urlSite`

	Voir la section «Variables et constantes utiles» pour une description de ces variables.

- `constantes.inc.php`: contient les constantes PHP utilisées dans Squeletml. Vous pouvez ajouter vos propres constantes en créant le fichier `site/inc/constantes.inc.php`. S'il existe, ce fichier sera inséré après celui par défaut.

- `contact.inc.php`: construit et analyse le formulaire de contact. Après son inclusion, la variable `$contact` est prête à être utilisée et contient les messages (d'erreur ou de confirmation) affichés à l'internaute et le formulaire en tant que tel. Il est possible d'interagir avec ce script en créant le fichier `site/inc/contact.inc.php`, qui sera inséré à différentes étapes du script:

  - juste après l'analyse des champs par défaut du formulaire, ce qui permet entre autres d'avoir des champs personnalisés et d'utiliser son propre script d'analyse de ces champs. Les variables utiles lors d'une analyse personnalisée sont `$erreurFormulaire`, qui annule l'envoi du message si elle vaut `TRUE`, et `$messagesScript`, qui contient les explications affichées après le traitement du formulaire;

  - juste avant l'envoi du message, ce qui permet de modifier le corps du message, l'objet, etc.;

  - à la fin du fichier par défaut.

- `dernier.inc.php`: gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML suivant le contenu ajouté directement dans une page du site. Deux traitements personnalisés peuvent être effectués dans ce fichier:

  - vous pouvez créer le fichier `site/inc/dernier-pre.inc.php`, qui sera inséré au début de `inc/dernier.inc.php`;
  - vous pouvez également créer le fichier `site/inc/dernier.inc.php`, qui sera inséré à la fin de celui par défaut.

- `fonctions.inc.php`: contient les fonctions utilisées dans Squeletml. Vous pouvez créer vos propres fonctions dans le fichier `site/inc/fonctions.inc.php`, qui sera inséré après celui par défaut.

- `galerie.inc.php`: génère les variables nécessaires à l'affiche d'une galerie ou d'une page individuelle d'une image. Vous pouvez modifier ces variables en créant le fichier `site/inc/galerie.inc.php`, qui sera inséré à la fin de celui par défaut.

- `partage-courriel.inc.php`: crée les variables nécessaires à l'incorporation au formulaire de contact du module de partage (par courriel). Vous pouvez modifier ces variables en créant le fichier `site/inc/partage-courriel.inc.php`, qui sera inséré à la fin de celui par défaut.

- `premier.inc.php`: gère l'inclusion des fichiers et l'affectation des variables nécessaires à la construction de la structure XHTML précédant le contenu ajouté directement dans une page du site. Deux traitements personnalisés peuvent être effectués dans ce fichier:

  - vous pouvez créer le fichier `site/inc/premier-pre.inc.php`, qui sera inséré dans `inc/premier.inc.php` après la deuxième série d'inclusions, donc juste avant les affectations en masse;
  - vous pouvez également créer le fichier `site/inc/premier.inc.php`, qui sera inséré à la fin de celui par défaut.

**Note: le principe de personnalisation des fichiers du dossier `inc` est applicable également à la section d'administration. La structure répliquée à l'intérieur du dossier `site` doit être contenue dans un dossier de même nom que celui de l'administration, qui est `admin` par défaut. Par exemple, pour modifier des variables du fichier de configuration `admin/inc/config.inc.php`, créer le fichier `site/admin/inc/config.inc.php`.**

#### Le dossier `modeles`

Le dossier `modeles` contient des modèles qui sont utilisés par l'installateur automatisé ou utilisable lors d'une installation manuelle, mais contient aussi des structures de fichier CSS et de fichiers de configuration (configuration du site et de l'administration).

#### Le dossier `xhtml`

Le dossier `xhtml` contient tous les fichiers contenant la structure XHTML envoyée au navigateur. Autrement dit, les variables nécessaires à la construction d'une page ont été déclarées et affectées par les scripts du dossier `inc`, et elles seront utilisées dans les fichiers du dossier `xhtml`.

Les fichiers du dossier `xhtml` peuvent être personnalisés en créant un fichier de même nom dans `site/xhtml`, et Squeletml reconnaîtra automatiquement le fichier personnel. Par exemple, pour modifier le menu en français, créer un fichier `site/xhtml/fr/menu.inc.php`, qui sera inséré à la place du fichier par défaut.

Il est également possible d'utiliser le même fichier pour toutes les langues du site. Pour ce faire, placer le fichier à la racine du dossier `site/xhtml/`. Par exemple, un menu commun à toutes les langues se trouvera dans `site/xhtml/menu.inc.php`.

##### Liste des fichiers

Pour commencer, il y a les fichiers de la structure XHTML générale de la page:

- `form-contact.inc.php`: modèle du formulaire de contact. Vous pouvez utiliser votre propre modèle en créant le fichier `site/xhtml/(LANGUE/)form-contact.inc.php`, qui sera utilisé à la place du modèle par défaut. Vous pouvez également ajouter des champs supplémentaires au modèle par défaut. Ainsi, si le fichier `site/xhtml/(LANGUE/)form-contact.inc.php` n'existe pas, des champs supplémentaires sous le champ `nom` peuvent être inclus dans le modèle par défaut grâce au fichier `site/xhtml/(LANGUE/)form-contact-champs-apres-nom.inc.php`, et des champs supplémentaires sous le message peuvent être inclus grâce au fichier `site/xhtml/(LANGUE/)form-contact-champs-apres-message.inc.php`.

	**Mise en situation:** nous voulons afficher sous le champ `nom` un nouveau champ de saisie pour le code postal, et ce pour les formulaires en français.

	Il faut commencer par ajouter le code du nouveau champ de saisie dans le fichier `site/xhtml/fr/form-contact-champs-apres-nom.inc.php`, ce qui donne par exemple:

		<p><label for="inputCodePostal">Votre code postal:</label><br />
		<input id="inputCodePostal" class="champInfo" type="text" name="codePostal" size="7" maxlength="7" value="<?php echo $codePostal; ?>" /></p>

	Ensuite, il faut ajouter le traitement de ce nouveau champ dans le fichier `site/inc/contact.inc.php`. Le traitement doit inclure la validation de la saisie ainsi que son utilisation lorsque toutes les données sont valides et que le courriel peut être envoyé. Cela peut donner ce qui suit:

		<?php
		$codePostal = '';

		// L'envoi du message est demandé
		if (isset($_POST['envoyer']))
		{
			$codePostal = securiseTexte($_POST['codePostal']);
			$codePostal = str_replace(' ', '', $codePostal);
			$codePostal = strtoupper($codePostal);
	
			if (empty($codePostal))
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= "<li class=\"erreur\">Vous n'avez pas inscrit de code postal.</li>\n";
			}
			elseif (!preg_match('/([A-Z]\d){3}/', $codePostal))
			{
				$erreurFormulaire = TRUE;
				$messagesScript .= "<li class=\"erreur\">Le code postal ne semble pas avoir une forme valide. Veuillez l'inscrire sous la forme <em>A1A1A1</em>.</li>\n";
			}
	
			// Envoi du message
			if ($formulaireValide)
			{
				if (!empty($infosCourriel['message']))
				{
					$infosCourriel['message'] = rtrim($infosCourriel['message']) . "\n\n=========================\n\n";
				}
		
				$infosCourriel['message'] .= "Code postal: $codePostal";
			}
		}
		?>

- `page.dernier.inc.php`: modèle de page suivant le contenu ajouté directement par l'utilisateur. Vous pouvez utiliser votre propre modèle en créant le fichier `site/xhtml/(LANGUE/)page.dernier.inc.php`, qui sera utilisé à la place du modèle par défaut.

- `page.premier.inc.php`: modèle de page précédant le contenu ajouté directement par l'utilisateur. Vous pouvez utiliser votre propre modèle en créant le fichier `site/xhtml/(LANGUE/)page.premier.inc.php`, qui sera utilisé à la place du modèle par défaut.

Ensuite, il y a les fichiers de division XHTML, qui sont, pour le français:

- `fr/ancres.inc.php`
- `fr/bas-de-page.inc.php`
- `fr/menu.inc.php`
- `fr/sous-titre.inc.php`
- `fr/sur-titre.inc.php`

	Pour chaque autre langue, le `fr` est remplacé par le code approprié, par exemple `en/menu.inc.php`.

Il y a enfin les fichiers des pages par défaut:

  - `fr/page.401.inc.php`
  - `fr/page.404.inc.php`
  - `fr/page.contact.inc.php`
  - `fr/page.index.inc.php`

	Pour chaque autre langue, le `fr` est remplacé par le code approprié, par exemple `en/page.index.inc.php`.

	**Il s'agit des pages livrées par défaut avec Squeletml.** C'est pour cette raison que leur contenu se trouve dans un fichier à inclure, sinon la mise à jour du logiciel risquerait de supprimer les modifications effectuées. **Toute autre page créée dans le site n'aura pas à utiliser ce système d'inclusion.** Pour personnaliser une page livrée par défaut, par exemple la page d'accueil, créer le fichier `site/xhtml/fr/page.index.inc.php`, qui sera inclus à la place du fichier par défaut.

	La seule page que vous voudrez sans aucun doute personnaliser est la page d'accueil. Le système d'inclusion expliqué ci-dessus est donc tout indiqué. Cependant, pour les autres pages, il ne s'agit pas d'une nécessité absolue. En effet, les pages d'erreur 401 et 404 affichent un message standard et propose un lien vers l'accueil, et pour sa part, la page de contact peut utiliser un courriel par défaut si vous renseignez la variable `$contactCourrielParDefaut` dans le fichier de configuration.

###### Fichier `piwik.inc.php`

Piwik est un logiciel libre de statistiques web. Lorsqu'un site est ajouté dans Piwik, un code est généré et doit être inséré dans chaque page du site en question. Squeletml permet d'ajouter rapidement ce code. En effet, si un fichier `piwik.inc.php` existe dans le dossier `site/xhtml`, il sera automatiquement inclus comme bloc dans les pages, par défaut à la fin du bas de page. La région peut être modifiée dans le fichier de configuration comme tout autre bloc de contenu (voir la section «Nombre de colonnes et blocs de contenu»).

#### Dossiers inutiles pour une configuration donnée

Le dossier `src` ainsi que le dossier d'une langue inactivée peuvent être supprimés, tout comme les pages livrées par défaut et qui ne sont pas utilisées. Par exemple, si votre site est seulement en français, le dossier `en` peut être supprimé. C'est également le cas des pages `exemple.php` et `exemple2.php` à la racine du site si ces dernières ne sont pas utilisées.

Lors d'une mise à jour, il est également inutile de copier ces dossiers ou fichiers.

### Structure XHTML par défaut

Voici un modèle simplifié d'une page de Squeletml **par défaut**:

	Doctype XHTML 1.0 Strict..
	<html ...><!-- Langue de la page en cours. -->
		<!-- ____________________ <head> ____________________ -->
		<head>
			<!-- Titre. -->
			<title>...</title>
			
			<!-- Métabalises. -->
			Encodage UTF-8.
			Description.
			Robots.
			
			<!-- Balises `link` et `script`. -->
			Feuilles de style et scripts Javascript.
		</head>
		<!-- ____________________ <body> ____________________ -->
		<body class="...">
			<!-- ____________________ #ancres ____________________ -->
			<div id="ancres">
				...
			</div><!-- /#ancres -->
			
			<!-- ____________________ Message pour IE6 et IE7. ____________________ -->
			<!--[if lte IE 7]>
				<div id="messageIE">
					...
				</div><!-- /#messageIE -->
			<![endif]-->
			
			<!-- ____________________ #page ____________________ -->
			<div id="page">
			  <div id="interieurPage">
			  	<!-- ____________________ #enTete ____________________ -->
					<div id="enTete">
						<div id="titre">
							Titre du site dans un `h1` s'il s'agit de la page d'accueil, sinon dans un `p`.
						</div><!-- /#titre -->
						
						<div id="sousTitre">
							...
						</div><!-- /#sousTitre -->
					</div><!-- /#enTete -->
					
					<!-- ____________________ #surContenu ____________________ -->
					<div id="surContenu">
						Vide par défaut, donc `div` pas incluse.
					</div><!-- /#surContenu -->
					
					<!-- ____________________ #contenu ____________________ -->
					<div id="contenu" class="...">
						<div id="interieurContenu">
							<div id="debutInterieurContenu">
								Vide par défaut, donc `div` pas incluse.
							</div><!-- /#debutInterieurContenu -->
							
							<div id="milieuInterieurContenu">
								Contenu entré directement par l'utilisateur.
							</div><!-- /#milieuInterieurContenu -->
							
							<div id="finInterieurContenu">
								Vide par défaut, donc `div` pas incluse.
							</div><!-- /#finInterieurContenu -->
						</div><!-- /#interieurContenu -->
					</div><!-- /#contenu -->
					
					<!-- ____________________ #sousContenu ____________________ -->
					<div id="sousContenu">
						<div id="menuLangues" class="bloc">
							...
						</div><!-- /#menuLangues -->
						
						<div id="menu" class="bloc">
							...
						</div><!-- /#menu -->
						
						<div id="fluxRss" class="bloc">
							...
						</div><!-- /#fluxRss -->
						
						<div id="partage" class="bloc">
							...
						</div><!-- /#partage -->
					</div><!-- /#sousContenu -->
				</div><!-- /#interieurPage -->
			</div><!-- /#page -->
			
			<!-- ____________________ #basDePageHorsPage ____________________ -->
			<div id="basDePageHorsPage">
				...
			</div><!-- /#basDePage -->
			
			Balises `script` finales.
		</body>
	</html>

D'autres `div` peuvent apparaître à la suite de `<div id="interieurContenu">` selon le module en cours d'utilisation, par exemple `<div id="galerie">` lorsque nous visitons la page d'une galerie. Aussi, certaines `div` peuvent être positionnées ailleurs selon les choix effectués dans le fichier de configuration, par exemple le menu. Enfin, il est possible d'utiliser son propre modèle de page, comme expliqué à la section «Le dossier `xhtml`».

#### Nombre de colonnes et blocs de contenu

Les sections «Style CSS» et «Contenu et ordre du flux HTML» du fichier de configuration contiennent beaucoup commentaires explicatifs. Tout ne sera pas repris ici, mais en résumé, il est possible de choisir le nombre de colonnes ainsi que l'ordre de leur contenu. Le principe est le suivant: plusieurs blocs de contenu existent par défaut, comme le menu des langues, le menu principal, les liens vers les flux RSS, etc. Il est possible également d'ajouter ses propres blocs.

Chaque bloc peut être positionné dans une région spécifique de la page (et dans l'ordre voulu à l'intérieur d'une même région): `enTete`, `surContenu`, `debutInterieurContenu`, `finInterieurContenu`, `sousContenu` ou `basDePage`. Chaque nom de région correspond à une `div` du modèle de page.

Selon le style affecté (voir la section «Style CSS» du fichier de configuration), les `div` `surContenu` et `sousContenu` vont être positionnées dans la page pour remplir la ou les colonnes, ou bien, s'il n'y a pas de colonne, le dessus ou le dessous du contenu. Les possibilités sont donc:

- aucune colonne, les blocs étant positionnés au-dessus ou au-dessous du contenu selon leur configuration;
- une seule colonne à gauche;
- une seule colonne à droite;
- deux colonnes dont celle de gauche est remplie par les blocs de `surContenu` et celle de droite par les blocs de `sousContenu`;
- deux colonnes dont celle de gauche est remplie par les blocs de `sousContenu` et celle de droite par les blocs de `surContenu`.

Il est bon de rappeler qu'un modèle de page personnalisé peut être utilisé (voir la section «Le dossier `xhtml`»), et que les blocs peuvent alors être affichés ailleurs selon le modèle utilisé.

## Style de Squeletml

Le style d'un site réalisé avec Squeletml peut être modifié comme n'importe quel autre site à l'aide de feuilles de style CSS. Il est même possible de ne pas inclure les feuilles par défaut et partir de zéro. Cependant, le fichier de configuration contient une section «Style CSS», offrant la possibilité de modifier quelques aspects du site sans devoir bidouiller dans une feuille de style ou modifier le modèle de page (voir la section «Dossiers»). L'utilisation du fichier de configuration pour modifier le style n'est pas du tout une obligation, mais une aide supplémentaire si besoin il y a.

Voici quelques exemples:

- ajout d'une couleur de fond et de coins arrondis aux blocs de contenu;
- nombre de colonnes;
- emplacement d'une colonne unique;
- arrière-plan d'une colonne;
- contenu affichable ou masquable par un clic sur le titre;
- ajout d'une classe `actif` aux liens pointant vers la page en cours dans les blocs de contenu;
- limite de la profondeur d'une liste dans les blocs de contenu (classe `masquer` ajoutée aux sous-listes inactives);
- etc.

## Traduction de Squeletml

Il est possible de traduire Squeletml dans la langue désirée. Le principal fichier est `locale/squeletml.pot`. Il contient la plupart des phrases à traduire. Les autres fichiers sont:

- `admin/versions-solo.admin.php` (le cas échéant, modifier le nom du dossier d'administration)
- `doc/documentation.md`
- `js/squeletml.js` (fonction `tableDesMatieres()`)
- `xhtml/fr/ancres.inc.php`
- `xhtml/fr/bas-de-page.inc.php`
- `xhtml/fr/menu.inc.php`
- `xhtml/fr/sous-titre.inc.php`
- `xhtml/fr/sur-titre.inc.php`
- `xhtml/fr/page.401.inc.php`
- `xhtml/fr/page.404.inc.php`
- `xhtml/fr/page.contact.inc.php`
- `xhtml/fr/page.galerie.inc.php`
- `xhtml/fr/page.index.inc.php`
- `maintenance.php`
- `README.md`

## Mise à jour de Squeletml

**Note: lisez toute cette section avant d'effectuer une mise à jour.**

Pour mettre à jour Squeletml:

- Visitez la page `http://www.nomDeDomaine.ext/admin/acces.admin.php` et mettez votre site en maintenance (hors ligne). Vous pouvez ajouter votre adresse IP dans le champ prévu à cet effet pour avoir encore accès à votre site durant la maintenance.

	La page de maintenance n'a pas de dépendance à des fichiers du site, à l'exception du fichier `.htaccess` de la racine, ce qui veut dire qu'en mode maintenance, vous pouvez supprimer ou déplacer tous les fichiers voulus, à l'exception de `maintenance.php` et `.htaccess`.

	Il est possible de personnaliser la page de maintenance en créant le fichier `maintenance.inc.php` à la racine du site. Si un tel fichier existe, il sera utilisé à la place de la page de maintenance par défaut.

	**Note: la réécriture d'URL doit être activée sur votre serveur pour utiliser cette fonctionnalité. Si tel n'est pas le cas, ignorez le mode maintenance et passez à l'étape suivante.**

- Téléchargez l'archive de la dernière version et extrayez son contenu. Vous allez obtenir un dossier dont le nom est `projets-divers-squeletml`.

- Ensuite, sélectionnez et copiez tout le contenu de ce dossier, et collez la sélection dans l'emplacement de votre site, et ce en acceptant de fusionner les dossiers et d'écraser les fichiers déjà existants.

	**Important**: ne pas oublier d'afficher les fichiers cachés pour bien les sélectionner. Voir la section «Installation» pour plus de détails.

Il s'agit probablement de la méthode la plus simple. Cependant, de vieux fichiers supprimés entre deux versions de Squeletml peuvent encore être présents sur votre site. Pour une mise à jour totalement propre, vous pouvez supprimer les fichiers de Squeletml de votre site avant d'y coller votre précédente sélection. Prenez garde cependant à **ne pas supprimer** les quelques fichiers de configuration à la racine (`.acces`, `.htaccess`, `init.inc.php`, `robots.txt` et `sitemap.xml`), le dossier `site`, qui contient votre configuration personnalisée, éventuellement les dossiers des différentes langues si votre site est multilingue (par exemple, si vous avez une section en anglais, vous avez fort probablement créé des pages personnalisées dans le dossier `en`) ainsi que les pages que vous avez vous-même ajoutées.

**Notes:**

- si vous avez modifié le nom du dossier d'administration, ne pas oublier de supprimer l'ancien dossier et de renommer le nouveau;
- il est bon de vérifier si les fichiers de configuration à la racine par défaut ont été modifiés dans la nouvelle version de Squeletml et, si tel est le cas, les éditer à la main pour y appliquer les changements;
- la lecture de la section «Dossiers inutiles pour une configuration donnée» est suggérée en complément à cette explication sur la mise à jour de Squeletml.

## Création de pages

Il y a plusieurs manières de créer une page:

1. Créer un fichier vide et reproduire la structure d'une page.
2. Copier le fichier `exemple.php` et le coller avec le nom désiré.
3. Dans le porte-documents de la section d'administration, créer un nouveau fichier de type «Page web modèle» ou «Page web modèle avec fichier Markdown».

Voici l'anatomie d'une page:

1. variables PHP
2. inclusion du premier fichier PHP
3. contenu de la page
4. inclusion du dernier fichier PHP

### Variables PHP

Voici les différentes variables optionnelles avant l'inclusion du premier fichier PHP:

- `$ajoutCommentaires`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut activer ou non l'ajout de commentaires pour la page courante, si la valeur est différente de `$ajoutCommentairesParDefaut` du fichier de configuration du site.

- `$apercu`: aperçu de la page en cours, si la valeur est différente de `$apercuParDefaut` du fichier de configuration du site. Le contenu de `$apercu` est inséré en tant que commentaire HTML au début de la `div` `milieuInterieurContenu` s'il n'est pas vide et si `$inclureApercu` du fichier de configuration du site vaut TRUE.

	Si `$apercu` vaut exactement `interne` (`$apercu = "interne";`), le commentaire HTML inséré sera donc `<!-- APERÇU: interne -->`, ce qui signifiera à Squeletml d'utiliser comme aperçu tout le texte situé entre l'ouverture de la `div` `milieuInterieurContenu` et le commentaire `<!-- /aperçu -->`. S'il y a lieu, des balises HTML seront fermées pour rendre le code de l'aperçu valide. Exemple:

		<div id="milieuInterieurContenu">
			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada<!-- /aperçu --> et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent tempus; odio ac sagittis vehicula; mauris pede tincidunt lacus, in euismod orci mauris a quam. Sed justo. Nunc diam.</p>

	Les espaces dans le commentaire `<!-- /aperçu -->` sont optionnelles, et le «c» peut s'écrire sans cédille. Voici des exemples de commentaire valide:

	- `<!-- /aperçu -->`
	- `<!--/aperçu-->`
	- `<!-- /apercu -->`
	- `<!--/apercu-->`

	Sinon si `$apercu` vaut exactement `description` (`$apercu = "description";`), le commentaire HTML inséré sera donc `<!-- APERÇU: description -->`, ce qui signifiera à Squeletml d'utiliser comme aperçu le contenu de la métabalise `description`.

	Sinon si `$apercu` vaut exactement `automatique` (`$apercu = "automatique";`), le commentaire HTML inséré sera donc `<!-- APERÇU: automatique -->`, ce qui signifiera à Squeletml d'utiliser comme aperçu les premiers caractères de la `div` `milieuInterieurContenu`. Le nombre de caractères correspond à la valeur de la variable `$tailleApercuAutomatique` du fichier de configuration du site.

- `$auteur`: nom ou noms à inclure dans la métabalise `author`, si la valeur est différente de `$auteurParDefaut` du fichier de configuration du site. Si elle existe, cette variable sera utilisée dans le listage des articles classés dans une catégorie ainsi que dans le bloc des informations de publication.

- `$baliseH1`: contenu de la balise `h1`. Il s'agit donc du titre de premier niveau pour la page courante. Exemple:

		$baliseH1 = "Titre de premier niveau de la page courante";

	Ce titre peut très bien être ajouté directement dans le corps de la page en HTML sans passer par la variable `$baliseH1`. Le but d'utiliser cette variable est:

	- d'éviter de saisir deux fois la même information dans le cas où nous voulons que la balise `title` ait le même contenu que le titre de premier niveau. En effet, si la variable `$baliseTitle` n'est pas renseignée, mais que `$baliseH1` l'est, alors la variable `$baliseTitle` aura le même contenu que le titre `h1`;
	
	- de pouvoir manipuler le titre de premier niveau comme un bloc de contenu (`balise-h1`), et ainsi pouvoir choisir son emplacement parmi les diverses régions possibles. Par exemple, le bloc de contenu sur les informations de publication s'affiche par défaut sous le contenu dans la région `finInterieurContenu`. En créant un bloc `balise-h1`, il est ainsi possible de faire apparaître le bloc des informations de publication dans le haut de la page, entre le titre de premier niveau et le contenu.

- `$baliseTitle`: contenu de la balise `title`. Si cette variable est vide, elle se verra assigner la valeur de la variable `$baliseH1` si cette dernière n'est pas vide, sinon l'URL de la page en cours.

- `$boitesDeroulantes`: permet d'activer les boîte déroulantes pour du contenu présent dans la page en cours. Voir les commentaires de la variable `$boitesDeroulantesParDefaut` dans le fichier de configuration du site pour une description détaillée de la syntaxe à utiliser.

- `$boitesDeroulantesAlaMain`: prend la valeur `TRUE` ou `FALSE`, si la valeur est différente de `$boitesDeroulantesAlaMainParDefaut` du fichier de configuration du site. Si vaut `TRUE`, les fichiers nécessaires à la gestion d'une boîte déroulante (Javascript et CSS) seront inclus, mais l'appel à la fonction Javascript `boiteDeroulante()` ne se fera pas de manière automatique, mais à la main par l'utilisateur, qui devra insérer la fonction à l'endroit désiré dans le code de la page.

- `$classesBody`: permet d'ajouter des classes à la balise `body`. Voici un exemple:

		$classesBody = 'maClasse1 maClasse2';

	Prendre note que la balise `body` contient déjà par défaut plusieurs classes, dont la plupart ont un nom explicite. Voici cependant quelques classes qui méritent d'être définies:

  - `accueil`: ajoutée aux pages d'accueil de chaque langue du site.
  - `article`: ajoutée aux pages étant classées dans au moins une catégorie.
  - `categorie`: ajoutée aux pages listant les articles appartenant à une catégorie.
  - `galerie`: ajoutée aux pages d'une galerie, que ce soit la page d'accueil de la galerie ou une page individuelle d'une image.
  - `galerieAccueil`: ajoutée aux pages d'accueil de chaque galerie.
  - `galeriePageImage`: ajoutée à chaque page individuelle d'une image dans une galerie.
  - `pageStandard`: ajoutée aux pages n'étant ni un formulaire de contact, ni une galerie, ni une page listant les articles appartenant à une catégorie.
  - `pageStandardSansCategorie`: ajoutée aux pages n'étant ni un formulaire de contact, ni une galerie, ni une page listant les articles appartenant à une catégorie, ni une page étant classée dans au moins une catégorie.

- `$classesContenu`: permet d'ajouter des classes à la `div` `contenu`. Voir `$classesBody` pour un exemple d'utilisation.

- `$courrielContact`: adresse courriel qui va recevoir les messages du formulaire de contact. Si cette variable existe et n'est pas vide, un formulaire de contact sera automatiquement inclus dans la page. Il est donc facile de créer autant de formulaires que désiré en créant pour chacun une page contenant une varibale `$courrielContact`.

	Note: dans le formulaire de contact livré par défaut avec Squeletml, cette variable vaut simplement `@`. Puisqu'elle n'est pas vide, un formulaire de contact s'affiche. Cependant, le formulaire par défaut n'est pas utilisable en ce sens que la valeur de `$courrielContact` n'est pas une adresse réelle. Toutefois, si la variable `$contactCourrielParDefaut` est renseignée dans le fichier de configuration du site, toutes les variables `$courrielContact` valant exactement `@` prendront la valeur de `$contactCourrielParDefaut`, ce qui évite de devoir créer une page de contact personnalisée simplement parce que le formulaire par défaut n'a pas une adresse valide.

- `$dateCreation`: date de création de la page, sous la forme *AAAA-MM-JJ*, incluse dans la métabalise `date-creation-yyyymmdd`. Si elle existe, cette variable sera utilisée dans le listage des articles classés dans une catégorie, dans le classement des items des flux RSS et dans le bloc des informations de publication.

- `$dateRevision`: date de dernière révision de la page, sous la forme *AAAA-MM-JJ*, incluse dans la métabalise `date-revision-yyyymmdd`. Si elle existe, cette variable sera utilisée dans le bloc des informations de publication, dans le listage des articles classés dans une catégorie et, si `$dateCreation` n'existe pas, dans le classement des items des flux RSS.

- `$desactiverCache`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut désactiver ou non le cache complet pour la page courante (dans le cas où le cache est activé avec la variable `$dureeCache` du fichier de configuration du site).

- `$desactiverCachePartiel`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut désactiver ou non le cache partiel pour la page courante (dans le cas où le cache complet est désactivé et que le cache partiel est activé avec la variable `$dureeCachePartiel` du fichier de configuration du site).

- `$description`: contenu de la métabalise `description`. Si cette variable est vide, la métabalise `description` ne sera pas incluse dans l'en-tête de la page.

- `$idCategorie`: un nombre, un mot ou une phrase identifiant la catégorie à chercher pour afficher la liste des articles y étant classés. Voir la section «Catégories».

- `$idGalerie`: un nombre, un mot ou une phrase identifiant la galerie. Le nom du dossier de la galerie dans `site/fichiers/galeries` correspond à l'identifiant filtré. Par défaut, cette variable est vide, mais si elle n'est pas vide, et si le fichier de configuration existe pour cet `id` (voir la section «Galeries»), une galerie sera insérée dans la page.

	Par exemple, si nous avons `$idGalerie = "Promenade en forêt";`, Squeletml va rechercher le dossier `site/fichiers/galeries/Promenade-en-foret`. Si ce dossier ou le fichier de configuration n'existent pas, un message d'erreur informe de l'inexistence de la galerie.

- `$inclureCodeFenetreJavascript`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut inclure ou non le code permettant de passer d'une image à une autre dans une fenêtre Javascript. Le script utilisé est Slimbox 2. À noter que ce code est inclus automatiquement dans les galeries (si ce type de navigation a été choisi dans le fichier de configuration). Cette variable permet tout simplement d'utiliser le code dans des pages personnalisées.

- `$infosPublication`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut afficher ou non les informations de publication (auteur, date de création, date de dernière révision) pour la page courante, si la valeur est différente de `$afficherInfosPublicationParDefaut` du fichier de configuration du site.

- `$langue`: langue de la page courante, si la valeur est différente de `$langueParDefaut` du fichier de configuration du site.

- `$licence`: licence de la page courante, si la valeur est différente de `$licenceParDefaut` du fichier de configuration du site. Plusieurs licences peuvent être déclarées, chacune devant être séparée par une espace. Voici un exemple:

		$licence = 'art-libre cc-by-sa';

	Voir la fonction `licence()` pour connaître tous les choix possibles.

- `$lienPage`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut afficher ou non une suggestion de code pour un lien vers la page courante, si la valeur est différente de `$afficherLienPageParDefaut` du fichier de configuration du site.

- `$motsCles`: contenu de la métabalise `keywords`. Si cette variable est vide ou inexistante, elle sera générée automatiquement à partir du contenu de la variable `$description`. Prenez note que si `$inclureMotsCles` vaut `FALSE` dans le fichier de configuration du site, les mots-clés ne seront pas ajoutés à l'en-tête de la page, même si `$motsCles` n'est pas vide.

- `$partageCourriel`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut activer ou non cette option pour la page courante, si la valeur est différente de `$activerPartageCourrielParDefaut` du fichier de configuration du site.

- `$partageReseaux`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut activer ou non cette option pour la page courante, si la valeur est différente de `$activerPartageReseauxParDefaut` du fichier de configuration du site.

- `$robots`: contenu de la métabalise `robots`, si la valeur est différente de `$robotsParDefaut` du fichier de configuration du site.

- `$tableDesMatieres`: prend la valeur `TRUE` ou `FALSE`, selon qu'on veut générer ou non une table des matières pour la page courante, si la valeur est différente de `$afficherTableDesMatieresParDefaut` du fichier de configuration du site. La table des matières est générée du côté client par Javascript si le cache (global et partiel) est désactivé, sinon elle est générée du côté serveur par PHP.

	Le contenu analysé pour la génération de la table des matières est la `div` `milieuInterieurContenu` pour les pages du site et la `div` `interieurContenu` pour les pages de l'administration. La table est ajoutée au début de la `div` analysée, ou bien à l'endroit où se trouve une `div` d'`id` `tableDesMatieres`, par exemple:

		Contenu
		<div id="tableDesMatieres"></div>
		Contenu

	Par défaut, tous les titres de niveaux 2 à 6 présents à l'intérieur de la `div` analysée constituent la table des matières. Il est possible de configurer la table des matières dans le fichier de configuration du site.

### Inclusion du premier fichier PHP

Il suffit d'inclure le fichier `inc/premier.inc.php`.

#### Cas de l'installation par défaut de Squeletml

Ne pas oublier de vérifier le chemin d'inclusion. Par exemple, pour une page à la racine du site, ça donnera:

	include 'inc/premier.inc.php';

Pour une page dans un dossier:

	include '../inc/premier.inc.php';

Pour une page dans un sous-dossier (dossier d'un dossier):

	include '../../inc/premier.inc.php';

**Note: si la page est créée à partir du porte-documents dans la section d'administration, le bon chemin d'inclusion est automatiquement inséré.**

#### Cas avec insertion automatique du fichier `init.inc.php`

**Note: je n'ai pas testé cette possibilité, et ce n'est pas supporté officiellement dans Squeletml.**

Le fichier `init.inc.php` contient entre autres la variable `$racine`. En faisant insérer automatiquement ce fichier dans toutes les pages, il est donc possible d'utiliser la variable `$racine` pour inclure le fichier `inc/premier.inc.php`, ce qui nous dispense de devoir modifier le chemin d'inclusion selon le dossier dans lequel la page est située.

Pour ce faire, ouvrir le fichier `.htaccess`, trouver la ligne contenant la directive `auto_prepend_file`, et la décommenter, ce qui va donner:

	<FilesMatch "\.(php)$"> 
		php_value auto_prepend_file "/var/www/serveur_local/squeletml/init.inc.php"
	</FilesMatch>

Ne pas oublier de modifier le chemin d'inclusion du fichier `init.inc.php`.

### Contenu

Mettre tout ce que vous désirez. Du texte, du code HTML, du code PHP, etc.

#### Utilisation de la syntaxe Markdown

Il est possible d'utiliser la syntaxe Markdown Extra:

- [syntaxe Markdown officielle](http://michelf.com/projets/php-markdown/syntaxe/);
- [options supplémentaires apportées par la version Extra](http://michelf.com/projets/php-markdown/extra/).

Pour ce faire, deux fonctions sont mises à disposition:

- `md()`, permettant de convertir en HTML un fichier écrit en Markdown;
- `mdChaine()`, permettant de convertir en HTML une chaîne de caractères écrite en Markdown.

##### Fonction `md()`

Écrire le contenu en Markdown dans un fichier. Ensuite, faire appel à cette fonction dans la page du site. Exemple:

1. Création du fichier Markdown, par exemple `ma-page.php.md`.

2. Création de la page PHP, par exemple `ma-page.php`, comme n'importe quelle autre page du site. On suppose dans cet exemple que le fichier `ma-page.php.md` et la page `ma-page.php` sont dans le même dossier (ce n'est pas obligatoire).

3. À l'intérieur de `ma-page.php`, à l'endroit où on insère habituellement le contenu, faire appel à la fonction suivante:

		<?php echo md('ma-page.php.md'); ?>

**Note: si la page a été créée dans le porte-documents de la section d'administration avec le type «Fichier modèle HTML de page web avec syntaxe Markdown», tout ceci est effectué automatiquement.**

##### Fonction `mdChaine()`

On peut passer directement une chaîne écrite en Markdown à la fonction `mdChaine()`. Exemple:

1. Création d'une page pour le site, par exemple `une-page.php`.

2. À l'endoit où on insère habituellement le contenu, utiliser la fonction suivante:

		<?php echo mdChaine("Du texte écrit en *Markdown*."); ?>

	Même exemple, mais avec une variable:

		<?php
		$chaine = "Du texte écrit en *Markdown*.";
		echo mdChaine($chaine);
		?>

	Autre exemple avec une variable dont le contenu est plus long:

		<?php
		$chaine = <<<TEXTE
		Du texte écrit en *Markdown*.
		
		Liste:
		
		- Item 1
		- Item 2
		
		Paragraphe.
		TEXTE;
		
		echo mdChaine($chaine);
		?>

##### Syntaxe Markdown avec imbrication de code PHP

Il est possible d'imbriquer du code PHP dans un fichier ou une chaîne utilisant la syntaxe Markdown. Le code PHP sera évalué avant la conversion du Markdown en HTML.

**Pour insérer du PHP dans un fichier écrit en Markdown:**

1. Créer un fichier Markdown, par exemple `ma-page.php.md`, et y insérer du code PHP entre les balises `[php]` et `[/php]`. Exemple:

		Les **licences présentées dans cet article** sont les suivantes:
		
		- [php]echo licence($urlRacine, 'art-libre');[/php]
		- [php]echo licence($urlRacine, 'cc-by-sa');[/php]
		
		Les autres licences le seront dans un prochain article.

2. Créer la page PHP, par exemple `ma-page.php`, comme n'importe quelle autre page du site.

3. À l'intérieur de `ma-page.php`, à l'endroit où on insère habituellement le contenu, insérer le code suivant:

		<?php
		$cheminMdPhp = $racine . '/chemin/vers/ma-page.php.md';
		eval(MD_PHP);
		?>

	Ne pas oublier de modifier la valeur de la variable `$cheminMdPhp` pour préciser le bon chemin vers le fichier Markdown.

**Maintenant, pour insérer du PHP dans une chaîne écrite en Markdown:**

1. Créer une page pour le site, par exemple `une-page.php`.

2. À l'endoit où on insère habituellement le contenu, insérer le code suivant:

		<?php
		$chaineMdPhp = 'Voici une liste de **1 à 5**:
		
		[php]
		for ($i = 1; $i <= 5; $i++)
		{
			echo "- $i\n";
		}
		[/php]';
		eval(MD_PHP);
		?>

	Modifier la valeur de la variable `$chaineMdPhp` pour correspondre au contenu voulu.

**Note: si les variables `$chaineMdPhp` et `$cheminMdPhp` existent toutes les deux, la variable `$chaineMdPhp` a préséance.**

#### Fonctions diverses

Voir la page `exemple.php` située à la racine de votre site Squeletml pour voir plusieurs exemples d'utilisation de fonctions aidant à la rédaction du contenu.

#### Variables et constantes utiles

Quelques variables et constantes PHP peuvent être utilisées dans la rédaction du contenu pour faciliter l'élaboration des chemins vers certains fichiers:

##### Variables

- `$accueil`: tableau permettant d'utiliser un lien vers l'accueil de n'importe quelle langue. Par exemple, dans une page en français:

		<a href="<?php echo $accueil['en']; ?>/gallery.php">lien vers une galerie dans la section en anglais</a>

- `$racine`: contient le chemin sur le serveur vers le dossier racine de Squeletml. Exemple d'inclusion d'un fichier situé à la racine de votre site Squeletml:

		<?php include $racine . '/fichier.inc.php'; ?>

- `$urlRacine`: contient l'URL vers le dossier racine de Squeletml. Exemple:

		<a href="<?php echo $urlRacine; ?>/cron.php">lien vers la page de cron</a>

- `$urlRacineAdmin`: contient l'URL vers le dossier d'administration de Squeletml. Exemple:

		<a href="<?php echo $urlRacineAdmin; ?>">lien vers la section d'administration</a>

- `$urlSite`: contient l'URL vers le dossier `site`. Exemple de lien vers une image:

		<a href="<?php echo $urlSite; ?>/fichiers/image.jpg">lien vers une image</a>

- `$urlFichiers`: contient l'URL vers le dossier `site/fichiers`. Exemple de lien vers la même image:

		<a href="<?php echo $urlFichiers; ?>/image.jpg">lien vers une image</a>

- variables relatives à l'URL de la page en cours (à moins d'indication contraire, dans les exemples qui suivent, l'URL de référence est `http://www.NomDeDomaine.ext/fichier.php?a=1&b=2`):

  - `$nomPage`: contient le nom de la page en cours. Exemple:

			fichier.php

  - `$url`: contient l'URL de la page en cours. Exemple:

			http://www.NomDeDomaine.ext/fichier.php?a=1&b=2

  - `$urlSansGet`: contient l'URL de la page en cours sans les variables `GET`. Exemple:

			http://www.NomDeDomaine.ext/fichier.php

  - `$urlAvecIndexSansGet`: contient l'URL de la page en cours sans les variables `GET` et avec le fichier d'index, s'il y a lieu. Pour l'URL `http://www.NomDeDomaine.ext/actualite/?a=1&b=2`, la valeur serait par exemple:

			http://www.NomDeDomaine.ext/actualite/index.php

##### Constantes

- `ACCUEIL`: contient l'URL pointant vers l'accueil de la langue de la page.

  - Exemple d'utilisation dans une page dont la langue est le français:

			<a href="<?php echo eval(ACCUEIL); ?>/contact.php">lien vers la page contact de la section en français</a>

  - Exemple d'utilisation dans une page dont la langue est l'anglais:

			<a href="<?php echo eval(ACCUEIL); ?>/contact.php">lien vers la page contact de la section en anglais</a>

- `LANGUE`: contient la langue de la page en cours (par exemple `fr`). Exemple d'utilisation:

		Langue de la page en cours: <?php echo eval(LANGUE); ?>

#### Liste des dernières publications

La fonction `publicationsRecentes()` permet d'obtenir la liste des dernières publications pour un type de publication donné: une catégorie, une galerie, toutes les galeries ou tout le site. Voici un exemple:

	$dernieresImages = publicationsRecentes($racine, $urlRacine, eval(LANGUE), 'galerie', 'chiens', 5, TRUE, TRUE, $triParDateFluxRss, $datePublicationVautDateRevision, $galerieFluxRssAuteurEstAuteurParDefaut, $auteurParDefaut, $galerieLegendeMarkdown, $galerieLienOriginalTelecharger, $marqueTroncatureApercu, $dureeCache, $estPageCron, $mettreAjourCacheSeulementParCron);

Dans l'exemple ci-dessus, la variable `$dernieresImages` contient les 5 dernières images ajoutées à la galerie `chiens`, et ce sous la forme d'une liste (formée de balises `li` dans un `ul`) dont chaque item contient la vignette d'une image et un lien vers la page individuelle de l'image. Aussi, le septième argument vaut `TRUE`, donc un lien est ajouté sur chaque vignette vers la page Web de l'image en question. Le huitième argument vaut également `TRUE`, donc un lien «Voir plus d'images» est ajouté à la fin de la liste vers l'accueil de la galerie.

Pour les types de publication `categorie` et `site`, la fonction renvoie une liste de titres (pointant vers la page en question si le huitième argument vaut `TRUE`).

Voir les explications de la fonction `publicationsRecentes()` dans le fichier `inc/fonctions.inc.php` pour plus de détails.

#### Coloration de code PHP

Il est possible d'utiliser une version personnalisée des fonctions `highlight_string()` et `highlight_file()` de PHP. En effet, `coloreCodePhp()` et `coloreFichierPhp()` remplacent les espaces insécables par des espaces normales et modifient les couleurs par défaut (entre autres pour améliorer le contraste des commentaires).

Les deux premiers paramètres sont les mêmes que ceux des fonctions natives de PHP. De plus, un paramètre supplémentaire permet d'afficher les commentaires en noir.

Exemples:

	<?php coloreFichierPhp($cheminFichier); ?>
	<?php $texte = coloreFichierPhp($cheminFichier, TRUE); ?>
	<?php coloreCodePhp($code, FALSE, TRUE); ?>

Voir la déclaration de la fonction dans le fichier `inc/fonctions.inc.php` pour plus de détails.

### Inclusion du dernier fichier PHP

Il suffit d'inclure le fichier `inc/dernier.inc.php`. Cette fois-ci, il n'est pas nécessaire de faire attention au chemin d'inclusion. Nous pouvons utiliser la variable `$racine`. Exemple:

	<?php include $racine . '/inc/dernier.inc.php'; ?>

### Exemple complet

Voici un exemple minimal:

	<?php
	$baliseTitle = "Titre (contenu de la balise `title`)";
	$description = "Description de la page.";
	include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion
	?>
	
	<h1>Titre de la page</h1>
	
	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
	
	<?php include $racine . '/inc/dernier.inc.php'; ?>

## Catégories

Squeletml propose un moyen de regrouper des pages: les catégories. Il ne faut au minimum qu'un fichier de configuration pour utiliser cette fonctionnalité. Le fichier de configuration `site/inc/categories.ini.txt` ou `site/inc/categories.ini` peut être créé à la main ou à l'aide du script de gestion des catégories dans la section d'administration de Squeletml. Ce fichier doit contenir la liste des pages pour chaque catégorie, et ce sous la forme suivante:

	[id de la catégorie]
	pages[]=URL relative de la page

L'URL relative est le chemin de la page à partir de l'URL racine du site. Exemple:

	[Chiens]
	pages[]=animaux/chiens/husky.php
	pages[]=animaux/chiens/malamute.php

Les deux pages en question sont accessibles respectivement à l'adresse:

	$urlRacine/animaux/chiens/husky.php

et:

	$urlRacine/animaux/chiens/malamute.php

Optionnellement, la langue à laquelle appartient chaque catégorie peut être précisée avec le paramètre `langue`:

	langue=fr

Si aucune langue n'est précisée, la langue par défaut du site est utilisée.

Aussi, un lien vers la page d'accueil de chaque catégorie peut optionnellement être précisé grâce au paramètre `url`:

	url=l'URL relative de la page d'accueil de la catégorie

Exemple:

	[Chiens]
	langue=fr
	url=animaux/chiens/
	pages[]=animaux/chiens/husky.php
	pages[]=animaux/chiens/malamute.php

La page d'accueil d'une catégorie peut être située n'importe où sur le site. L'important est d'insérer dans la page l'identifiant de la catégorie que vous voulez afficher, et ce grâce à la variable `$idCategorie`. Disons que pour notre exemple la page est `$urlRacine/animaux/chiens/index.php` et qu'elle contient ceci:

	<?php
	$baliseTitle = "Articles sur les chiens";
	$idCategorie = "Chiens";
	include '../../inc/premier.inc.php';
	?>
	
	<?php include $racine . '/inc/dernier.inc.php'; ?>

En visitant cette page, un aperçu pour chaque page listée dans le fichier de configuration pour la catégorie donnée sera généré. Un titre de premier niveau (`h1`) sera également généré par défaut (voir la variable `$genererTitrePageCategories` dans le fichier de configuration du site).

Si la page d'accueil d'une catégorie n'est pas précisée à l'aide du paramètre `url`, l'URL sera générée automatiquement, et ce sous la forme `$urlRacine/categorie.php?id=idCategorieFiltre` (`idCategorieFiltre` représente la variable `$idCategorie` filtrée). Dans ce cas, il n'est pas nécessaire de créer la page d'accueil manuellement puisque `categorie.php` est une page livrée par défaut avec Squeletml et gérant l'affichage des articles d'une catégorie. Pour éviter le contenu dupliqué dans les moteurs de recherche, seules les catégories pour lesquelles aucune valeur n'a été donnée au paramètre `url` sont accessibles sur la page `$urlRacine/categorie.php`.

Voici un exemple d'aperçu qui pourrait être généré pour la page sur le husky:

	Article sur le husky
	
	Publié par pseudo le 2010-01-04. Dernière révision le 2010-01-05.
	
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In sapien ante; dictum id, pharetra ut, malesuada et, magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. […]
	
	Lire la suite de Article sur le husky 

La teneur exacte de l'aperçu de la page sur le husky dépend de la configuration de `$urlRacine/animaux/chiens/husky.php`. Dans notre exemple, les variables `$dateCreation` et `$dateRevision` ont été affectées. Pour sa part, le lien «Lire la suite de...» est ajouté lorsque le texte de la page n'apparaît pas au complet, et ce grâce à l'utilisation de la variable `$apercu`. Voir la section «Variables PHP» pour plus de détails au sujet de ces variables.

Si une page fait partie d'une catégorie au moins, les informations de publication au sujet de cette page feront référence à sa ou ses catégories. Exemple:

	Publié par pseudo le 2010-01-04. Dernière révision le 2010-01-05. Catégories: Animaux, Chiens

### Catégories spéciales

Les catégories spéciales sont les dernières publications (`site`) et les derniers ajouts aux galeries (`galeries`). Par défaut, l'URL est `$urlRacine/categorie.php?id=(site|galeries)&amp;langue=$langue`. Il n'y a pas de flux RSS associé à ces catégories puisque les flux RSS globaux remplissent déjà cette tâche.

Une catégorie spéciale apparaît seulement si elle est activée dans le tableau `$activerCategoriesGlobales` du fichier de configuration du site.

### Bloc de menu des catégories

Les catégories peuvent être listées dans un menu qui leur est propre. Deux méthodes existent pour obtenir un tel menu:

- créer le fichier `site/xhtml/(LANGUE/)menu-categories.inc.php`;

- utiliser le menu généré automatiquement en affectant la variable `$genererMenuCategories` à TRUE dans le fichier de configuration du site. Il est possible de préciser la catégorie parente d'une catégorie, et ainsi obtenir un menu structuré en conséquence. Exemple:

		[Chiens]
		url=animaux/chiens/
		parent=Animaux
		pages[]=animaux/chiens/husky.php
		pages[]=animaux/chiens/malamute.php

Dans l'un ou l'autre des cas, les catégories actives seront marquées comme telles, si cette option est activée dans le fichier de configuration du site.

## Galeries

Chaque galerie possède son propre titre, aussi appelé «identifiant unique» (ou *id*), qui peut être un nombre ou une chaîne de caractères que vous choisissez lors de sa création, et qui est utilisé par Squeletml pour différencier les galeries. Pour l'explication qui suit, disons que l'identifiant vaut `Ma première galerie`.

Les points suivants doivent être satisfaits pour qu'une galerie soit accessible (**toutes ces étapes peuvent être réalisées facilement dans la section d'administration**):

- un dossier contenant les images, présent dans `site/fichiers/galeries/`. Le nom du dossier importe peu. Pour notre exemple, disons que le dossier est `site/fichiers/galeries/galerie-1/`;

- un fichier de configuration listant les images à afficher et éventuellement diverses informations optionnelles, et situé dans `site/fichiers/galeries/galerie-1/`. Il y a deux noms possibles pour le fichier de configuration: `config.ini.txt` ou `config.ini`. Le premier fichier existant trouvé est utilisé, `config.ini.txt` étant cherché en premier. Cela donne pour notre exemple `site/fichiers/galeries/galerie-1/config.ini.txt` ou `site/fichiers/galeries/galerie-1/config.ini`;

- une page à visiter sur le site pour afficher la galerie. Il y a deux possibilités:

  - la page globale des galeries, `galerie.php?id=identifiant-filtré&amp;langue={LANGUE}`, ce qui donne pour notre exemple `galerie.php?id=Ma-premiere-galerie&amp;langue={LANGUE}`;

  - une page personnalisée, par exemple `page-de-ma-premiere-galerie.php`. Cette page peut être située n'importe où sur le site. L'important est d'insérer dans la page l'identifiant de la galerie (variable `$idGalerie`) que vous voulez afficher.

- un fichier de configuration des galeries, `site/inc/galeries.ini` ou `site/inc/galeries.ini.txt`, listant la galerie en question;

Concrètement, pour créer une galerie à partir de l'administration de Squeletml, voici les étapes à suivre:

1. Visiter la page `admin/galeries.admin.php` et se rendre à la section «Ajouter des images».

2. Dans le champ «Identifiant de la galerie», inscrire l'identifiant voulu, par exemple `Ma première galerie`.

3. Le champ «Si nouvelle galerie, nom du dossier» peut être rempli avec la valeur désirée ou laissé vide pour génération automatique à partir de l'identifiant.

4. Le champ «Si nouvelle galerie, URL de la page Web» peut être rempli avec la valeur désirée ou laissé vide pour utilisation de la page globale des galeries (`galerie.php`).

5. Dans le champ «Fichier», préciser un fichier image ou une archive (`.tar`, `.tar.bz2`, `.tar.gz` ou `.zip`).

6. Choisir d'autres options selon les besoins.

7. Cliquer sur «Ajouter des images».

Pour créer manuellement une galerie, donc sans passer par l'administration de Squeletml, les étapes sont les suivantes:

1. Créer un dossier dans `site/fichiers/galeries/`, par exemple `site/fichiers/galeries/galerie-1/`, et y mettre les images.

2. Créer un fichier de configuration `site/fichiers/galeries/galerie-1/config.ini.txt` ou `site/fichiers/galeries/galerie-1/config.ini`. Cette étape est développée dans la prochaine section.

3. Ajouter la nouvelle galerie dans le fichier de configuration des galeries (`site/inc/galeries.ini` ou `site/inc/galeries.ini.txt`), avec la syntaxe suivante:

		[identifiant de la galerie]
		dossier=dossier de la galerie
		url=URL de la galerie

	Exemple:

		[Ma première galerie]
		dossier=site/fichiers/galeries/galerie-1
		url=page-de-ma-premiere-galerie.php

	L'URL peut être la page globale des galeries (`galerie.php?id=identifiant-filtré&amp;langue={LANGUE}`, ce qui donne pour notre exemple `galerie.php?id=Ma-premiere-galerie&amp;langue={LANGUE}`; Squeletml va s'occuper automatiquement de remplacer `{LANGUE}` par la bonne valeur lors de l'affichage de la galerie) ou une page personnalisée.

4. Si l'URL de la galerie est une page personnalisée, ajouter une variable `$idGalerie` au début de la page en question (par exemple, `page-de-ma-premiere-galerie.php`), et l'assigner avec l'identifiant voulu (par exemple, `$idGalerie = "Ma première galerie";`).

### Fichier de configuration d'une galerie

Chaque image d'une galerie est déclinée en au moins deux versions: vignette et intermédiaire. Une troisième version peut être offerte en téléchargement: le format original. Chacune des images peut se voir assigner différentes informations optionnelles, par exemple la valeur de chaque attribut (`width`, `height`, `alt`, `src`) de la balise `img`. En fait, **une seule information est obligatoire dans le fichier de configuration: le nom du fichier de l'image en version intermédiaire.**

Cette information doit se retrouver dans le fichier de configuration `site/fichiers/galeries/idGalerieDossier/config.ini.txt` ou `site/fichiers/galeries/idGalerieDossier/config.ini`. Vous pouvez générer et mettre à jour automatiquement le contenu de ce fichier à partir de l'administration de Squeletml. Vous pouvez également le créer et le modifier à la main à l'aide d'un simple éditeur de texte.

Chaque image de la galerie possède sa propre section dans le fichier de configuration. La section commence par un titre entre crochets représentant le nom du fichier de l'image en version intermédiaire (et se termine par le début d'une autre section ou par la fin du fichier):

	`[intermediaireNom]`

Voici un exemple:

	[fichier1.jpg]
	
	[fichier2.jpg]

Il s'agit d'une configuration minimale pour une galerie de deux images. Il est cependant possible d'ajouter beaucoup plus d'information pour chaque image. Voici la liste complète des paramètres possibles:

- `titre`: utilisé pour nommer l'image dans les textes générés par le script. Exemple:

		titre=Le fleuve Saint-Laurent en hiver

- `intermediaireLegende`: commentaire qui sera affiché sous l'image en version intermédiaire. Exemple:

		intermediaireLegende=Cette photographie a été prise le... à... avec un appareil de marque... et représente...

- `exclure`: informe si l'image doit être exclue de la galerie. Par défaut, l'image n'est pas exclue, mais elle l'est seulement si `exclure` vaut `1` ou `oui`. Exemple:

		exclure=1

- `id`: utilisé dans l'adresse URL pour identifier l'image, au lieu de l'indice de la position de l'image dans le tableau de la galerie. Ceci permet de facilement déplacer des images dans la galerie sans modifier leur URL. Si l'`id` n'est pas renseigné, il sera généré automatiquement à partir du titre de l'image ou du nom du fichier image. L'`id` doit être seulement composé des caractères suivants: `-A-Za-z0-9._+`. Exemple:

		id=paysage-hiver

- `licence`: licence ou licences de l'image. Exemple:

		licence=art-libre

	Voir les explications de la variable `$licence` dans la documentation pour plus de détails.

- `originalNom`: nom de l'image au format original (en tout cas normalement de taille plus importante que la version intermédiaire, ou un fichier source). Si l'information est renseignée, un lien de téléchargement vers ce fichier sera ajouté, selon la configuration, dans le bas de l'image en version intermédiaire, directement sur l'image ou sur une petite icône sous l'image (l'icône par défaut est `fichiers/agrandir.png`; pour utiliser sa propre icône, créer le fichier `site/fichiers/agrandir.png`). Si l'information n'est pas renseignée, un nom sera construit à partir du nom de l'image en version intermédiaire, c'est-à-dire `intermediaireNom(sans extension)-original.extension`, un test sera effectué pour savoir si ce fichier existe, et s'il existe, un lien sera ajouté. Exemple:

		originalNom=fichier1Original.jpg

- `vignetteNom`: nom de l'image en version vignette. Si l'information n'est pas renseignée, le nom de la vignette sera déduit à partir du nom de l'image en version intermédiaire, c'est-à-dire `intermediaireNom(sans extension)-vignette.extension`. Exemple:

		vignetteNom=fichier1Petit.jpg

- `vignetteLargeur`: largeur de la vignette. Si `vignetteLargeur` ou `vignetteHauteur` sont renseignées, seulement la ou les informations renseignées seront affichées dans la balise `img`. Si les deux sont vides, les attributs `width` et `height` seront calculés automatiquement. Exemple:

		vignetteLargeur=100

- `vignetteHauteur`: hauteur de la vignette. Voir `vignetteLargeur` pour plus de détails. Exemple:

		vignetteHauteur=150

- `vignetteAlt`: texte alternatif (contenu de l'attribut `alt`) de la vignette. Si vide, le contenu sera généré automatiquement. Exemple:

		vignetteAlt=Chien assis sur un divan

- `vignetteAttributTitle`: texte affiché dans une infobulle au survol de l'image par la souris. Exemple:

		vignetteAttributTitle=Chien assis sur un divan

- `intermediaireLargeur`: largeur de l'image en version intermédiaire. Voir `vignetteLargeur` pour plus de détails. Exemple:

		intermediaireLargeur=500

- `intermediaireHauteur`: hauteur de l'image en version intermédiaire. Voir `vignetteLargeur` pour plus de détails. Exemple:

		intermediaireHauteur=750

- `intermediaireAlt`: texte alternatif (contenu de l'attribut `alt`) de l'image en version intermédiaire. Si vide, le contenu sera généré automatiquement. Exemple:

		intermediaireAlt=Chien dormant sur un divan

- `intermediaireAttributTitle`: texte affiché dans une infobulle au survol de l'image par la souris. Exemple:

		intermediaireAttributTitle=Chien dormant sur un divan

- `pageIntermediaireBaliseTitle`: contenu de la balise `title` de la page présentant l'image en version intermédiaire. Laisser vide pour une génération automatique. Exemple:

		pageIntermediaireBaliseTitle=Bla bla bla

- `pageIntermediaireDescription`: contenu de la métabalise `description` de la page présentant l'image en version intermédiaire. Laisser vide pour une génération automatique. Exemple:

		pageIntermediaireDescription=Bla bla bla

- `pageIntermediaireMotsCles`: contenu de la métabalise `keywords` de la page présentant l'image en version intermédiaire. Laisser vide pour une génération automatique. Exemple:

		pageIntermediaireMotsCles=photographie, chien, bla bla bla, divan, photo

- `auteurAjout`: auteur de l'ajout de l'image dans la galerie. Exemple:

		auteurAjout=Moi

	S'il existe et n'est pas vide, ce champ est utilisé dans les flux RSS.

- `dateAjout`: date d'ajout de l'image dans la galerie, sous la forme `Y-m-d H:i` ([voir la fonction PHP `date()`](http://php.net/manual/function.date.php)). Exemple:

		dateAjout=2010-01-17 20:05

	La partie après le jour est optionnelle. Il est donc possible de ne pas préciser l'heure ni les minutes, ou de ne préciser que l'heure.

	La date d'ajout est insérée automatiquement par Squeletml lorsque les galeries sont gérées par l'interface d'administration. Cette information est utilisée dans les flux RSS pour classer les images. Si `dateAjout` n'existe pas, la date utilisée sera celle du fichier image sur le serveur retournée par la [fonction PHP `filemtime()`](http://php.net/manual/function.filemtime.php).

- `commentaire`: n'est pas utilisé par Squeletml pour l'affichage de l'image. Il s'agit d'un paramètre utile pour ajouter des notes personnelles quand on modifie le fichier de configuration dans un éditeur de texte sans passer par la section d'administration de Squeletml. Exemple:

		commentaire=Photographie à 50% de sa taille originale.

Voici un exemple pour une galerie de deux images:

	[fichier1.jpg]
	id=1
	titre=Lorem ipsum
	intermediaireLegende=Lorem ipsum dolor sit amet.
	
	[fichier2.jpg]
	intermediaireLegende=Praesent tempus; odio ac sagittis vehicula.

Voici une entrée vide, qu'il est possible de copier/coller:

	[intermediaireNom]
	titre=
	intermediaireLegende=
	exclure=
	id=
	licence=
	vignetteNom=
	vignetteLargeur=
	vignetteHauteur=
	vignetteAlt=
	intermediaireLargeur=
	intermediaireHauteur=
	intermediaireAlt=
	pageIntermediaireBaliseTitle=
	pageIntermediaireDescription=
	pageIntermediaireMotsCles=
	originalNom=
	auteurAjout=
	dateAjout=
	commentaire=

### Navigation entre les images

Il y a six méthodes possibles pour naviguer entre les images. La méthode à utiliser est paramétrable dans le fichier de configuration du site.

#### Fenêtre Javascript

Il s'agit de la seule méthode de navigation sans passer par le rechargement de la page pour consulter les images. Plus précisément, en choisissant cette option, le script Slimbox 2 est utilisé pour passer d'une image à une autre sur la page d'accueil de la galerie au lieu de naviguer d'une image à une autre en rechargeant toute la page.

Si une légende est précisée pour une image, elle s'affiche sous cette dernière dans la fenêtre Javascript.

#### Flèches

Il est possible de choisir l'emplacement des flèches (haut ou bas).

Les flèches par défaut sont `fichiers/precedent.png` et `fichiers/suivant.png`. Pour utiliser ses propres images, créer les fichiers `site/fichiers/precedent.png` et `site/fichiers/suivant.png`.

*Note: de façon générale, toute pagination sur le site effectuée à l'aide de flèches utilise les images personnalisées si ces dernières existent, sinon les flèches par défaut sont utilisées.*

#### Vignettes

Il est possible de choisir l'emplacement des vignettes (haut ou bas).

##### Vignettes seules

Les vignettes utilisées sont celles de l'image vers laquelle le lien pointe:

- soit la vignette précisée dans le fichier de configuration (paramètre `vignetteNom`);

- soit la vignette déduite automatiquement à partir du nom du fichier de l'image en version intermédiaire (`intermediaireNom(sans extension)-vignette.extension`);

- soit la vignette générée automatiquement par le script de galerie.

##### Vignettes tatouées d'une flèche

Dans le cas où l'option `$galerieNavigationTatouerVignettes` est activée dans le fichier de configuration, une vignette personnalisée est générée par le script (à partir de la vignette de l'image) sur laquelle une flèche est superposée au centre.

Les fichiers par défaut pour les flèches superposées sont `fichiers/precedent-tatouage.png` et `fichiers/suivant-tatouage.png`. Pour utiliser ses propres images, créer les fichiers `site/fichiers/precedent-tatouage.png` et `site/fichiers/suivant-tatouage.png`.

La vignette résultante est sauvegardée dans `site/fichiers/galeries/id/tatouage/vignetteNom-sens.extension`. Il y a donc deux vignettes tatouées par image (une pour le sens «précédent» et l'autre pour le sens «suivant»).

##### Vignettes seules accompagnées d'une flèche

Dans le cas où les vignettes seules sont utilisées (sans tatouage), il est possible d'ajouter une flèche à côté de la vignette.

Les fichiers par défaut pour les flèches accompagnant les vignettes sont `fichiers/precedent-accompagnee.png` et `fichiers/suivant-accompagnee.png`. Pour utiliser ses propres images, créer les fichiers `site/fichiers/precedent-accompagnee.png` et `site/fichiers/suivant-accompagnee.png`.

#### Minivignettes

Il est possible d'ajouter au système de flèches ou de vignettes un aperçu de la galerie, composé de minivignettes des images de la galerie. Chaque minivignette est cliquable. Par défaut, la hauteur d'une minivignette est de 35 pixels. Ceci peut être modifié par CSS.

Il est possible de choisir l'emplacement des minivignettes (haut ou bas).

### Bloc de menu des galeries

Les galeries peuvent être listées dans un menu qui leur est propre. Deux méthodes existent pour obtenir un tel menu:

- créer le fichier `site/xhtml/(LANGUE/)menu-galeries.inc.php`;

- utiliser le menu généré automatiquement en affectant la variable `$genererMenuGaleries` à TRUE dans le fichier de configuration du site.

Dans l'un ou l'autre des cas, les galeries actives seront marquées comme telles, si cette option est activée dans le fichier de configuration du site.

### Plusieurs galeries sur une même page

Il est possible de lister plusieurs galeries sur une même page. Pour ce faire, utiliser la fonction `galeries()`. Exemple:

	<?php
	$baliseH1 = "Plusieurs galeries sur une même page";
	$inclureCodeFenetreJavascript = TRUE;
	include 'inc/premier.inc.php'; // Le cas échéant, modifier le chemin d'inclusion.
	
	echo galeries($racine, $urlRacine, eval(LANGUE), array ('identifiant galerie 1', 'identifiant galerie 2', 'identifiant galerie 3'), $galerieAncreDeNavigation);
	
	include $racine . '/inc/dernier.inc.php';
	?>

Ne pas oublier de définir la variable `$inclureCodeFenetreJavascript` à `TRUE` au début du fichier si la navigation par fenêtre Javascript est souhaitée.

Par défaut, la navigation par fenêtre Javascript est activée et le niveau des titres de galerie est 2. Les deux derniers paramètres (optionnels) de la fonction permettent de modifier ces valeurs par défaut. Exemple:

	echo galeries($racine, $urlRacine, eval(LANGUE), array ('identifiant galerie 1', 'identifiant galerie 2', 'identifiant galerie 3'), $galerieAncreDeNavigation, FALSE, 3);

## Partage par courriel

Une option de partage par courriel est activée par défaut. Concrètement, un lien est inséré dans le menu de partage pour offrir la possibilité à l'internaute d'envoyer un message à une ou plusieurs personnes pour faire connaître la page visitée. En cliquant sur ce lien, un formulaire de contact est ajouté dans le bas de la page. Le modèle du message qui sera envoyé est présenté à l'internaute, et ce dernier peut ajouter un petit mot personnalisé.

Si la page visitée est la page individuelle d'une image dans une galerie, le modèle de message contiendra une vignette de l'image et, si possible, une description, qui est formée par une de ces informations (en ordre de priorité):

- `intermediaireLegende`
- `intermediaireAlt`
- `vignetteAlt`
- `pageIntermediaireDescription`
- `pageIntermediaireBaliseTitle`

Si la page visitée est autre chose qu'une page individuelle d'image, le modèle de message contiendra un lien vers la page et, si possible une description, qui est formée par une de ces informations (en ordre de priorité):

- `$baliseDescription`
- `$baliseTitle`

## Commentaires

Lorsqu'un commentaire est ajouté, il est enregistré dans un fichier de configuration des commentaires propre à la page concernée et créé dans le dossier `site/inc/commentaires/`. Ce fichier de configuration liste tous les commentaires de la page en question.

À ce fichier de configuration s'ajoute un fichier de configuration des abonnements aux notifications par courriel des nouveaux commentaires, créé dans le même dossier. Chaque page possède également son propre fichier.

## Syndication de contenu (flux RSS)

### Syndication individuelle

#### Syndication par catégorie

Chaque catégorie possède son propre flux RSS, généré automatiquement, si son paramètre `rss` vaut `1` dans le fichier de configuration des catégories.

#### Syndication par galerie

Chaque galerie possède son propre flux RSS, généré automatiquement, si son paramètre `rss` vaut `1` dans le fichier de configuration des galeries.

### Syndication globale

*Note: chaque langue du site a sa propre syndication globale. Le code de la langue dans les explications qui suivent précise la langue de la syndication globale. Par exemple, `fr` signifie que la page en question sera incluse dans le flux RSS global de la section du site en français. Le code de la langue doit correspondre aux indices du tableau `$accueil`, déclaré dans le fichier `init.inc.php`, situé à la racine du site.*

#### Syndication globale du site

Pour activer le flux RSS global du site, ou flux RSS des dernières publications, il faut:

- que la variable `$activerFluxRssGlobalSite` dans le fichier de configuration du site vaille TRUE;

- qu'un fichier `site/inc/rss-site.ini.txt` ou `site/inc/rss-site.ini` soit créé à la main ou à l'aide du script de gestion des flux globaux dans la section d'administration de Squeletml. Ce fichier doit contenir la liste des pages à inclure dans le flux RSS, et ce sous la forme suivante:

		[code de la langue]
		pages[]=URL relative de la page

	Le code de la langue précise la langue de la syndication globale. L'URL relative est le chemin de la page à partir de l'URL racine du site. Exemple:

		[fr]
		pages[]=dossier1/dossier2/page.php
		pages[]=autrePage.php

	Les deux pages en question sont accessibles respectivement à l'adresse:

		$urlRacine/dossier1/dossier2/page.php

	et:

		$urlRacine/autrePage.php
		
#### Syndication globale des galeries

Pour activer le flux RSS global des galeries, ou flux RSS des derniers ajouts aux galeries, il faut:

- que la variable `$galerieActiverFluxRssGlobal` dans le fichier de configuration du site vaille TRUE;

- qu'aun moins une galerie dans le fichier de configuration des galeries ait un paramètre `rss` avec une valeur de `1`. La valeur de la clé `langue` va indiquer la langue de la syndication globale.

## Cache

Le cache permet d'enregistrer tout le contenu HTML (ou XML) d'une page (cache global) ou une partie (cache partiel) pour un accès ultérieur. Tant que le fichier de cache n'a pas expiré, c'est ce contenu qui est récupéré par Squeletml pour être retourné aux internautes. Le contenu de la page n'est donc pas généré (ou complètement généré) dynamiquement à chaque visite, ce qui accélère l'affichage et réduit l'utilisation des ressources du serveur.

Lorsqu'un internaute visite une page dont le cache n'est plus valide, Squeletml regénère le contenu, le remet en cache et retourne le résultat HTML (ou XML). Cependant, il est possible d'empêcher Squeletml de mettre à jour le cache (ce qui peut prendre un certain temps) lors de la visite d'un internaute pour réserver plutôt cette action au cron. Pour ce faire, il faut donner la valeur TRUE à la variable `$mettreAjourCacheSeulementParCron` du fichier de configuration du site. Le fichier de cache (même s'il n'est plus valide) sera dans ce cas toujours utilisé par Squeletml pour afficher la page demandée par un internaute, la mise à jour du cache s'effectuant lors du lancement du cron (voir la section «Cron» pour plus de détails).

### Cache global

Il est possible d'activer le cache global en modifiant la variable `$dureeCache` du fichier de configuration du site. Tout le contenu d'une page sera alors enregistré dans un fichier HTML (ou XML pour une page de flux RSS). La page ne sera donc pas générée à chaque visite. Squeletml s'occupera également d'envoyer les en-têtes HTTP relatives au cache (`Expires`, `Cache-Control`, `Last-Modified` et `ETag`) et analysera les requêtes conditionnelles reçues (`If-Modified-Since` et `If-None-Match`) pour retourner seulement un code d'en-tête 304 dans le cas où le contenu n'a pas changé (au lieu de retourner le contenu lui-même).

À noter que si la variable `$desactiverCache` est déclarée dans une page et qu'elle vaut `TRUE`, le cache sera désactivé même si `$dureeCache` ne vaut pas `0`.

Le cache pour les autres types de fichiers (images, Javascript, etc.) est paramétré dans le fichier `.htaccess`. À ce sujet, la variable `$versionParDefautLinkScript` du fichier de configuration du site peut être pratique. En effet, le cache des fichiers CSS et Javascript peut être paramétré pour une durée relativement longue (par exemple un mois ou plus), mais si un fichier a été modifié, il est possible de forcer son retéléchargement en modifiant l'URL déclarée par Squeletml. Par exemple, l'URL d'un fichier CSS sera `http://url/vers/fichier.css?$versionParDefautLinkScript['css']`.

### Cache partiel

Il est possible d'activer le cache partiel en modifiant la variable `$dureeCachePartiel` du fichier de configuration du site. Le cache partiel sera alors activé seulement si le cache global ne l'est pas. À noter que si la variable `$desactiverCachePartiel` est déclarée dans une page et qu'elle vaut `TRUE`, le cache sera désactivé même si `$dureeCachePartiel` ne vaut pas `0`.

Autrement dit, le cache partiel sera activé si l'expression suivante est vraie: `(!$dureeCache || $desactiverCache) && $dureeCachePartiel && !$desactiverCachePartiel`.

Aucune en-tête HTTP relative au cache ne sera envoyée, et aucune requête conditionnelle ne sera analysée. Squeletml va continuer à calculer et à générer une partie de la page. Le contenu HTML ou XML enregistré en cache correspondra au contenu de `<div id="milieuInterieurContenu">...</div>`.

À noter que le cache partiel se comportera exactement comme le cache global pour les flux RSS.

### Fusion des feuilles de style CSS et des scripts Javascript

Si la variable `$fusionnerCssJs` dans le fichier de configuration du site vaut `TRUE`, les feuilles de style seront fusionnées dans un seul fichier, tout comme les scripts Javascript, ce qui permet de réduire le nombre de requêtes HTTP lors de la visite d'une page.

Les fichiers uniques sont enregistrés dans le dossier de cache (`site/cache/`). Selon la configuration du site, il peut y avoir plusieurs fichiers uniques. En effet, un fichier unique est créé pour chaque ensemble différent de fichiers. Par exemple, si le site inclut normalement les feuilles de style A, B, C et D, un fichier unique sera créé pour contenir le code CSS de toutes ces feuilles et sera inclus à la place de ces dernières. Maintenant, si une section particulière du site nécessite en plus la feuille de style E, un autre fichier unique sera créé pour cette section et contiendra le code CSS des feuilles A, B, C, D et E. Le bon fichier unique sera inclus selon la page visitée. Le même principe s'applique aux scripts Javascript.

La fusion prend également en compte les fichiers inclus par [commentaires conditionnels](http://www.alsacreations.com/astuce/lire/48-commentaires-conditionnels.html) pour le navigateur Internet Explorer. Ainsi, un fichier unique est créé pour le code visant tous les navigateurs; un autre pour Internet Explorer 6; un autre pour Internet Explorer 7; et un autre pour Internet Explorer 8.

Une fois qu'un fichier unique a été créé pour un ensemble différent de fichiers, il ne sera pas recréé, même si les fichiers CSS ou Javascript originaux ont été modifiés. Pour forcer la regénération des fichiers uniques, supprimer simplement les fichiers CSS et Javascript dans le dossier de cache. Pour cette raison, il est conseillé d'activer cette option seulement pour les sites en production et non pour ceux en développement.

À noter que la section d'administration possède également une option similaire (variable `$adminFusionnerCssJs` dans le fichier de configuration de l'administration). Les fichiers uniques sont alors enregistrés dans le dossier de cache de l'administration.

## Cron

Le cron permet d'effectuer certaines tâches de maintenance:

- génération ou mise à jour du cache. Voir la section «Cache» pour plus de détails;

- génération ou mise à jour du fichier Sitemap. Si ce fichier n'existe pas, il sera créé, et s'il n'est pas indiqué dans le fichier `robots.txt`, il y sera déclaré.

### Lancement du cron

Le cron peut être lancé manuellement dans l'administration (section «Accès»). Il peut aussi être lancé automatiquement dans la mesure où `$activerPageCron` dans le fichier de configuration du site vaut `TRUE` et que la visite de la page `cron.php` est planifiée par un script (pour plus de sécurité, une clé d'accès peut être précisée; voir la variable `$cleCron` dans le fichier de configuration du site). Par exemple, sous GNU/Linux, la commande `crontab -e` permet d'ajouter une commande différée, comme celle-ci:

	0 4 * * * lynx -source $urlRacine/cron.php

Grâce à la commande ci-dessus, la page `cron.php` est visitée une fois par jour à 4h00. Bien sûr, ne pas oublier de remplacer `$urlRacine` par la bonne valeur, par exemple:

	0 4 * * * lynx -source http://www.nomDeDomaine.ext/cron.php

Lynx est un navigateur en mode texte. S'il n'est pas installé sur votre système, vous pouvez utiliser GNU Wget:

	0 4 * * * wget -O - -q -t 1 http://www.nomDeDomaine.ext/cron.php

ou encore cURL:

	0 4 * * * curl --silent --compressed http://www.nomDeDomaine.ext/cron.php

Certains scripts permettent aussi d'émuler cron en PHP. Voir par exemple [pseudo-cron](http://www.bitfolge.de/index.php?option=com_content&view=article&id=61%3Aphp&catid=38%3Aeigene&Itemid=59&limitstart=3) (non testé).

### Rapport par courriel

Il est possible de recevoir un rapport par courriel au format HTML après l'exécution (ou la tentative d'exécution) du cron. Pour ce faire, la variable `$envoyerRapportCron` du fichier de configuration du site doit valoir `1` (envoyer un rapport seulement si le cron n'a pas pu être lancé) ou `2` (toujours envoyer un rapport), et un courriel d'administration doit avoir été précisé (au moins une des deux variables `$courrielAdmin` ou `$contactCourrielParDefaut` doit être renseignée).

## Fichier Sitemap

Un [fichier Sitemap au format XML](http://www.sitemaps.org/fr/) peut être généré manuellement dans la section d'administration ou automatiquement par le cron si `$ajouterPagesParCronDansSitemap` du fichier de configuration du site vaut `TRUE`. Toutes les pages connues par Squeletml (URL d'accueil, galeries, flux RSS, catégories, etc.) seront ajoutées dans le fichier Sitemap.

Ce fichier, nommé `sitemap.xml`, se trouve à la racine du site, et supporte pour chaque page listée les balises d'information au sujet des images importantes apparaissant dans la page en question. Voir [Sitemaps pour images](http://support.google.com/webmasters/bin/answer.py?hl=fr&answer=178636). Il supporte également la déclaration de pages équivalentes en d'autres langues. Voir [Sitemaps&amp;: rel="alternate" hreflang="x"](http://support.google.com/webmasters/bin/answer.py?hl=fr&answer=2620865).

## Développement

### Fichier `Makefile`

Un fichier `Makefile.modele` se trouve dans le dossier `modeles`. Pour l'utiliser, le copier à la racine du site en le renommant `Makefile`. Les principales commandes sont:

- `make fichiersModeles`: génère les modèles dans le dossier `modeles`;

- `make langues`: met à jour les fichiers de langues.

La commande `make all` permet d'effectuer les deux.

### Fichier `.gitignore`

Un fichier `.gitignore.modele` se trouve dans le dossier `modeles`. Pour l'utiliser, le copier à la racine du site en le renommant `.gitignore`.

