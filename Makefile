########################################################################
##
## Variables.
##
########################################################################

# Chemin vers le bureau.
cheminBureau:=$(shell xdg-user-dir DESKTOP)

# Chemin vers le dossier local de définition des languages pour GtkSourceView.
cheminLanguageSpecs=~/.local/share/gtksourceview-3.0/language-specs

# Dossier de publication.
dossierPub=squeletml

# Récupère la dernière version, représentée par la dernière étiquette.
version:=$(shell git describe | rev | cut -d '-' -f 3- | rev)

########################################################################
##
## Métacibles.
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant la dernière révision d'une prochaine version.
generer: messageAccueil po mo

# Crée les archives; y ajoute les fichiers qui ne sont pas versionnés, mais nécessaires; supprime les fichiers versionnés, mais inutiles. À faire après un `git tag -a ...` pour la sortie d'une nouvelle version.
publier: fichiersSurBureau

########################################################################
##
## Cibles.
##
########################################################################

annexesDoc:
	php scripts/scripts.cli.php annexesDoc doc

archives:
	git archive $(version) --format=tar --prefix=$(dossierPub)/ --output $(dossierPub).tar
	tar -xf $(dossierPub).tar
	rm $(dossierPub).tar
	php scripts/scripts.cli.php config $(dossierPub)
	php scripts/scripts.cli.php css $(dossierPub)
	tar -cjf squeletml.tar.bz2 $(dossierPub)
	zip -qr squeletml.zip $(dossierPub)
	rm -rf $(dossierPub)

fichiersSurBureau: annexesDoc archives
	cp doc/INCOMPATIBILITES.mkd $(cheminBureau)
	echo "$(version)" > $(cheminBureau)/version.txt
	python scripts/python-markdown2/lib/markdown2.py doc/LISEZ-MOI.mkd > $(cheminBureau)/LISEZ-MOI.html
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
	for po in $(shell find locale/ -name *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

po: pot poFr
	for po in $(shell find ./ -name *.po);\
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
	find ./ -name "*.php" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L PHP {} \;
	# `xgettext` n'offre pas le Javascript dans les langages à parser, donc on déclare les fichiers `.js` comme étant du Perl.
	find ./ -name "squeletml.js" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L Perl {} \;

push:
	git push origin --tags :

