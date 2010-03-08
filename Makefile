########################################################################
##
## VARIABLES
##
########################################################################

# Chemin vers le dossier local de définition des languages pour GtkSourceView
languageSpecs=~/.local/share/gtksourceview-2.0/language-specs

# Chemin vers le bureau
bureau:=$(shell xdg-user-dir DESKTOP)

# Récupère le dernier tag (qui représente la dernière version)
tag:=$(shell bzr tags | sort -k2n,2n | tail -n 1 | cut -d ' ' -f 1)

# Récupère le numéro de la dernière révision de l'avant-dernier tag
derniereRevAvantDernierTag:=$(shell bzr tags | sort -k2n,2n | tail -n 2 | head -n 1 | rev | cut -d ' ' -f 1 | rev)

# Récupère le numéro de la première révision du dernier tag
premiereRevTag:=$(shell echo $(derniereRevAvantDernierTag) | xargs expr 1 +)

########################################################################
##
## MÉTACIBLES
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant la dernière révision d'une prochaine version.
generer: message-accueil po mo

# Crée des archives .bz2 et .zip; y ajoute les fichiers qui ne sont pas versionnés, mais nécessaires; supprime les fichiers versionnés, mais inutiles; copie certains fichiers utiles (comme le ChangeLog) sur le bureau; et déplace les archives également sur le bureau. À faire après un bzr tag... pour la sortie d'une nouvelle version.
publier: archives

########################################################################
##
## CIBLES
##
########################################################################

archives: menage-archives ChangeLog version.txt
	bzr export -r tag:$(tag) $(tag)
	mv ChangeLog $(tag)/
	php ./scripts.cli.php mdtxt ChangeLog-version-actuelle
	mv ChangeLog-version-actuelle.mdtxt $(bureau)/ChangeLog-$(tag).mdtxt
	mv ChangeLog-version-actuelle $(tag)/
	mv ChangeLog-version-actuelle-fichiers $(tag)/
	cp version.txt $(tag)/
	for po in $(shell find $(tag) -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
		cp $${po%\.*}.mo $(tag)/$${po%\.*}.mo;\
	done
	rm -f $(tag)/inc/devel.inc.php
	rm -f $(tag)/Makefile
	rm -f $(tag)/scripts.cli.php
	rm -rf $(tag)/src
	tar --bzip2 -cvf squeletml.tar.bz2 $(tag) # --bzip2 = -j
	zip -rv squeletml.zip $(tag)
	rm -rf $(tag)
	mv squeletml.tar.bz2 $(bureau)/
	mv squeletml.zip $(bureau)/
	cp documentation.mdtxt $(bureau)/documentation-avec-config.mdtxt
	php ./scripts.cli.php annexes-doc $(bureau)/documentation-avec-config.mdtxt

ChangeLog: menage-ChangeLog
	# Est basé sur http://telecom.inescporto.pt/~gjc/gnulog.py
	# Ne pas oublier de mettre ce fichier dans le dossier de plugins de bzr,
	# par exemple ~/.bazaar/plugins/
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r1..tag:$(tag) > ChangeLog
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r revno:$(premiereRevTag)..tag:$(tag) > ChangeLog-version-actuelle
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr status -r revno:$(derniereRevAvantDernierTag)..tag:$(tag) > ChangeLog-version-actuelle-fichiers

ini: menage-ini
	mkdir -p $(languageSpecs)/
	cp src/ini-squeletml/ini-squeletml.lang $(languageSpecs)/

lp:
	bzr push lp:~jpfle/squeletml/trunk

menage-archives:
	rm -f squeletml.tar.bz2
	rm -f squeletml.zip

menage-ChangeLog:
	rm -f ChangeLog
	rm -f ChangeLog-version-actuelle
	rm -f ChangeLog-version-actuelle-fichiers

menage-ini:
	rm -f $(languageSpecs)/ini-squeletml.lang

menage-message-accueil:
	rm -f xhtml/message-accueil-par-defaut.inc.php

menage-pot:
	rm -f locale/squeletml.pot
	touch locale/squeletml.pot # sinon xgettext -j va planter en précisant que le fichier est introuvable

menage-version.txt:
	rm -f version.txt

message-accueil: menage-message-accueil
	php ./scripts.cli.php message-accueil

mo:
	for po in $(shell find locale/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

po: pot
	for po in $(shell find ./ -iname *.po);\
	do\
		msgmerge -o tempo $$po locale/squeletml.pot;\
		rm $$po;\
		mv tempo $$po;\
	done

pot: menage-pot
	find ./ -iname "*.php" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L PHP {} \;
	find ./ -iname "*.js" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L Perl {} \; # xgettext n'offre pas le Javascript dans les langages à parser, donc on déclare les fichiers .js comme étant du Perl

version.txt: menage-version.txt
	echo $(tag) > version.txt

