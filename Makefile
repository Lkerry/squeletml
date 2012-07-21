all: langues fichiersModeles

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

fichiersModeles:
	php scripts.cli.php config
	php scripts.cli.php css

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

