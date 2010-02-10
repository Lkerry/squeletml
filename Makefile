########################################################################
##
## VARIABLES
##
########################################################################

# Chemin vers le dossier local de définition des languages pour GtkSourceView
languageSpecs=~/.local/share/gtksourceview-2.0/language-specs

# Chemin vers le bureau
bureau=`xdg-user-dir DESKTOP`

# Récupère le dernier tag (qui représente la dernière version)
tag=`bzr tags | sort -k2n,2n | tail -n 1 | cut -d ' ' -f 1`

# Récupère le numéro de la première révision du dernier tag
premiereRevTag=`bzr tags | sort -k2n,2n | tail -n 2 | head -n 1 | rev | cut -d ' ' -f 1 | rev | xargs expr 1 + `

########################################################################
##
## MÉTACIBLES
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant la dernière révision d'une prochaine version.
generer: message-accueil po

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
	mv ChangeLog-version-actuelle.mdtxt $(bureau)/
	mv ChangeLog-version-actuelle $(tag)/
	cp ChangeLog-version-actuelle-fichiers $(bureau)/
	mv ChangeLog-version-actuelle-fichiers $(tag)/
	cp version.txt $(tag)/
	cd $(tag) # Palliatif au fait que je n'ai pas trouvé comment insérer
	          # une variable de Makefile dans une commande shell $(...).
	          # Par exemple, ceci ne fonctionne pas:
	          # for po in $(find $(tag) -iname *.po);\
	for po in `find . -iname *.po`;\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done
	cd ../
	rm -f $(tag)/inc/devel.inc.php
	rm -f $(tag)/Makefile
	rm -f $(tag)/scripts.cli.php
	rm -rf $(tag)/src
	tar --bzip2 -cvf $(tag).tar.bz2 $(tag) # --bzip2 = -j
	zip -rv $(tag).zip $(tag)
	rm -rf $(tag)
	mv $(tag).tar.bz2 $(tag).tbz2 # Drupal bogue avec l'ajout de fichiers .tar.bz2
	mv $(tag).tbz2 $(bureau)/
	mv $(tag).zip $(bureau)/
	cp LISEZ-MOI.mdtxt $(bureau)/LISEZ-MOI-$(tag).mdtxt
	cp documentation.mdtxt $(bureau)/documentation-$(tag).mdtxt
	cp version.txt $(bureau)/derniere-version-squeletml.txt
	php ./scripts.cli.php annexes-doc $(bureau)/documentation-$(tag).mdtxt

ChangeLog: menage-ChangeLog
	# Est basé sur http://telecom.inescporto.pt/~gjc/gnulog.py
	# Ne pas oublier de mettre ce fichier dans le dossier de plugins de bzr,
	# par exemple ~/.bazaar/plugins/
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r1..tag:$(tag) > ChangeLog
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r revno:$(premiereRevTag)..tag:$(tag) > ChangeLog-version-actuelle
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr status -r revno:$(premiereRevTag)..tag:$(tag) > ChangeLog-version-actuelle-fichiers

ini: menage-ini
	mkdir -p $(languageSpecs)/
	cp src/ini-squeletml/ini-squeletml.lang $(languageSpecs)/

lp:
	bzr push lp:~jpfle/squeletml/trunk

menage-archives:
	rm -f $(tag).tbz2
	rm -f $(tag).zip

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
	for po in `find locale/ -iname *.po`;\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done

po: pot
	for po in `find ./ -iname *.po`;\
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

