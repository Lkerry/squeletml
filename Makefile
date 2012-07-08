########################################################################
##
## Variables.
##
########################################################################

# Chemin vers le bureau.
cheminBureau:=$(shell xdg-user-dir DESKTOP)

# Dossier de publication.
dossierPub=squeletml

# Récupère la dernière version, représentée par la dernière étiquette.
version:=$(shell git describe | rev | cut -d '-' -f 3- | rev)

########################################################################
##
## Cibles.
##
########################################################################

archive:
	git archive $(version) --format=tar --prefix=$(dossierPub)/ --output $(dossierPub).tar
	tar -xf $(dossierPub).tar
	rm $(dossierPub).tar
	php scripts.cli.php config $(dossierPub)
	php scripts.cli.php css $(dossierPub)
	zip -qr squeletml-$(version).zip $(dossierPub)
	mv squeletml-$(version).zip $(cheminBureau)
	rm -rf $(dossierPub)

langues: messageAccueil po mo

menagePot:
	rm -f locale/squeletml.pot
	# À faire, sinon `xgettext -j` va planter en précisant que le fichier est introuvable.
	touch locale/squeletml.pot

messageAccueil:
	php scripts.cli.php messageAccueil

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

