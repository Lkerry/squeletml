########################################################################
##
## Variables.
##
########################################################################

# Chemin vers le dossier local de définition des languages pour GtkSourceView.
cheminLanguageSpecs=~/.local/share/gtksourceview-2.0/language-specs

# Chemin vers le dossier local de scripts pour Nautilus.
cheminNautilusScripts=~/.gnome2/nautilus-scripts

# Chemin vers le bureau.
bureau:=$(shell xdg-user-dir DESKTOP)

# Récupère la dernière version, représentée par la dernière étiquette.
version:=$(shell bzr tags | sort -k2n,2n | tail -n 1 | cut -d ' ' -f 1)

########################################################################
##
## Métacibles.
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant la dernière révision d'une prochaine version.
generer: message-accueil po mo

# Crée les archives; y ajoute les fichiers qui ne sont pas versionnés, mais nécessaires; supprime les fichiers versionnés, mais inutiles. À faire après un `bzr tag ...` pour la sortie d'une nouvelle version.
publier: archives

########################################################################
##
## Cibles.
##
########################################################################

archives: menage-archives ChangeLog version.txt
	bzr export -r tag:$(version) squeletml
	mv ChangeLog squeletml
	cp doc/version.txt squeletml/doc
	php ./scripts.cli.php config squeletml
	php ./scripts.cli.php css squeletml
	$(MAKE) mo-archives
	rm -f squeletml/inc/devel.inc.php
	rm -f squeletml/Makefile
	rm -f squeletml)/scripts.cli.php
	rm -rf squeletml/src
	tar --bzip2 -cvf squeletml.tar.bz2 squeletml # `--bzip2` équivaut à `-j`.
	zip -rv squeletml.zip squeletml
	rm -rf squeletml
	mv squeletml.tar.bz2 $(bureau)
	mv squeletml.zip $(bureau)
	php ./scripts.cli.php annexes-doc $(bureau)/documentation-avec-config.html

ChangeLog: menage-ChangeLog
	# Est basé sur <http://telecom.inescporto.pt/~gjc/gnulog.py>. Ne pas oublier de mettre ce fichier dans le dossier des extensions de bazaar, par exemple `~/.bazaar/plugins/`.
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r1..tag:$(version) > ChangeLog

exif: menage-exif
	mkdir -p $(cheminNautilusScripts)
	cp src/exiftran-rotation/exiftran-rotation $(cheminNautilusScripts)

ini: menage-ini
	mkdir -p $(cheminLanguageSpecs)
	cp src/ini-squeletml/ini-squeletml.lang $(cheminLanguageSpecs)

menage-archives:
	rm -f squeletml.tar.bz2
	rm -f squeletml.zip

menage-ChangeLog:
	rm -f ChangeLog

menage-exif:
	rm -f $(cheminNautilusScripts)/exiftran-rotation

menage-ini:
	rm -f $(cheminLanguageSpecs)/ini-squeletml.lang

menage-message-accueil:
	rm -f xhtml/message-accueil-par-defaut.inc.php

menage-pot:
	rm -f locale/squeletml.pot
	# À faire, sinon `xgettext -j` va planter en précisant que le fichier est introuvable.
	touch locale/squeletml.pot

menage-version.txt:
	rm -f doc/version.txt

message-accueil: menage-message-accueil
	php ./scripts.cli.php message-accueil

mo:
	for po in $(shell find locale/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

mo-archives:
	for po in $(shell find squeletml/locale/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

po: pot pofr
	for po in $(shell find ./ -iname *.po);\
	do\
		msgmerge -o tempo $$po locale/squeletml.pot;\
		rm $$po;\
		mv tempo $$po;\
	done

pofr:
	msgen locale/squeletml.pot -o locale/fr_CA/LC_MESSAGES/tempo.po
	msgmerge -o locale/fr_CA/LC_MESSAGES/squeletml.po locale/fr_CA/LC_MESSAGES/squeletml.po.info locale/fr_CA/LC_MESSAGES/tempo.po
	rm -f locale/fr_CA/LC_MESSAGES/tempo.po

pot: menage-pot
	find ./ -iname "*.php" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L PHP {} \;
	# `xgettext` n'offre pas le Javascript dans les langages à parser, donc on déclare les fichiers `.js` comme étant du Perl.
	find ./ -iname "squeletml.js" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L Perl {} \;

push:
	bzr push lp:~jpfle/+junk/squeletml

version.txt: menage-version.txt
	echo $(version) > doc/version.txt

