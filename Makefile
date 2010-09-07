########################################################################
##
## Variables.
##
########################################################################

# Chemin vers le bureau.
cheminBureau:=$(shell xdg-user-dir DESKTOP)

# Chemin vers le dossier local de définition des languages pour GtkSourceView.
cheminLanguageSpecs=~/.local/share/gtksourceview-2.0/language-specs

# Chemin vers le dossier local de scripts pour Nautilus.
cheminNautilusScripts=~/.gnome2/nautilus-scripts

# Dossier de publication.
dossierPub=squeletml

# Récupère la dernière version, représentée par la dernière étiquette.
version:=$(shell bzr tags | sort -k2n,2n | tail -n 1 | cut -d ' ' -f 1)

########################################################################
##
## Métacibles.
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant la dernière révision d'une prochaine version.
generer: messageAccueil po mo

# Crée les archives; y ajoute les fichiers qui ne sont pas versionnés, mais nécessaires; supprime les fichiers versionnés, mais inutiles. À faire après un `bzr tag ...` pour la sortie d'une nouvelle version.
publier: fichiersSurBureau

########################################################################
##
## Cibles.
##
########################################################################

annexesDoc:
	php scripts/scripts.cli.php annexesDoc doc

archives: changelog versionTxt
	bzr export -r tag:$(version) $(dossierPub)
	cp doc/ChangeLog $(dossierPub)/doc
	cp doc/version.txt $(dossierPub)/doc
	php scripts/scripts.cli.php config $(dossierPub)
	php scripts/scripts.cli.php css $(dossierPub)
	$(MAKE) moArchives
	rm -f $(dossierPub)/inc/devel.inc.php
	rm -f $(dossierPub)/Makefile
	rm -rf $(dossierPub)/scripts
	rm -rf $(dossierPub)/src
	tar -cjf squeletml.tar.bz2 $(dossierPub)
	zip -qr squeletml.zip $(dossierPub)
	rm -rf $(dossierPub)

changelog:
	# Est basé sur <http://telecom.inescporto.pt/~gjc/gnulog.py>. Ne pas oublier de mettre ce fichier dans le dossier des extensions de bazaar, par exemple `~/.bazaar/plugins/`.
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r1..tag:$(version) > doc/ChangeLog

changelogHtml: changelog
	php scripts/scripts.cli.php changelogMdtxt doc
	# PHP Markdown n'est pas utilisé, car il y a un bogue avec la conversion de longues listes (le contenu retourné est vide).
	python scripts/python-markdown2/lib/markdown2.py doc/ChangeLog.mdtxt > doc/ChangeLog.html
	rm doc/ChangeLog.mdtxt

exif:
	mkdir -p $(cheminNautilusScripts)
	cp src/exiftran-rotation/exiftran-rotation $(cheminNautilusScripts)

fichiersSurBureau: annexesDoc archives changelogHtml
	cp doc/ChangeLog $(cheminBureau)
	cp doc/version.txt $(cheminBureau)
	python scripts/python-markdown2/lib/markdown2.py doc/LISEZ-MOI.mdtxt > $(cheminBureau)/LISEZ-MOI.html
	mv doc/ChangeLog.html $(cheminBureau)
	mv doc/documentation-avec-config.html $(cheminBureau)
	mv squeletml.tar.bz2 $(cheminBureau)
	mv squeletml.zip $(cheminBureau)

ini:
	mkdir -p $(cheminLanguageSpecs)
	cp src/ini-squeletml/ini-squeletml.lang $(cheminLanguageSpecs)

menagePot:
	rm -f locale/squeletml.pot
	# À faire, sinon `xgettext -j` va planter en précisant que le fichier est introuvable.
	touch locale/squeletml.pot

messageAccueil:
	php scripts/scripts.cli.php messageAccueil

mo:
	for po in $(shell find locale/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

moArchives:
	for po in $(shell find $(dossierPub)/locale/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

po: pot poFr
	for po in $(shell find ./ -iname *.po);\
	do\
		msgmerge -o tempo $$po locale/squeletml.pot;\
		rm $$po;\
		mv tempo $$po;\
	done

poFr:
	msgen locale/squeletml.pot -o locale/fr_CA/LC_MESSAGES/tempo.po
	msgmerge -o locale/fr_CA/LC_MESSAGES/squeletml.po locale/fr_CA/LC_MESSAGES/squeletml.po.info locale/fr_CA/LC_MESSAGES/tempo.po
	rm -f locale/fr_CA/LC_MESSAGES/tempo.po

pot: menagePot
	find ./ -iname "*.php" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L PHP {} \;
	# `xgettext` n'offre pas le Javascript dans les langages à parser, donc on déclare les fichiers `.js` comme étant du Perl.
	find ./ -iname "squeletml.js" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L Perl {} \;

push:
	bzr push lp:~jpfle/+junk/squeletml

versionTxt:
	echo $(version) > doc/version.txt

