########################################################################
##
## VARIABLES
##
########################################################################

# Récupère le dernier tag (qui représente la dernière version)
tag=`bzr tags | tail -n 1 | cut -d ' ' -f 1`

########################################################################
##
## MÉTACIBLES
##
########################################################################

# Met à jour les fichiers qui sont versionnés, mais pas créés ni gérés à la main. À faire par exemple avant le dernier commit d'une prochaine version.
generer: po

# Crée une archive .bz2, y ajoute les fichiers qui ne sont pas versionnés mais nécessaires, supprime les fichiers versionnés mais inutiles. À faire après un bzr tag... quand une nouvelle version est sortie.
publier: bz2

########################################################################
##
## CIBLES
##
########################################################################

bz2: menage-bz2 ChangeLog
	bzr export -r tag:$(tag) $(tag)
	mv ChangeLog $(tag)/
	for po in $(find $(tag)/ -iname *.po);\
	do\
		msgfmt -o $${po%\.*}.mo $$po;\
	done
	rm $(tag)/Makefile
	tar -jcvf $(tag).tar.bz2 $(tag)
	rm -rf $(tag)

ChangeLog: menage-ChangeLog
	#Est basé sur http://telecom.inescporto.pt/~gjc/gnulog.py
	#Ne pas oublier de mettre ce fichier dans le dossier de plugins de bzr,
	#par exemple ~/.bazaar/plugins/
	BZR_GNULOG_SPLIT_ON_BLANK_LINES=0 bzr log -v --log-format 'gnu' -r1..tag:$(tag) > ChangeLog

menage-bz2:
	rm -f $(tag).tar.bz2

menage-ChangeLog:
	rm -f ChangeLog

menage-pot:
	rm -f locale/squeletml.pot
	touch locale/squeletml.pot # sinon xgettext -j va planter en précisant que le fichier est introuvable

po: pot
	for po in `find ./ -iname *.po`;\
	do\
		msgmerge -o tempo $$po locale/squeletml.pot;\
		rm $$po;\
		mv tempo $$po;\
	done

pot: menage-pot
	find ./ -iname "*.php" -exec xgettext -j -o locale/squeletml.pot --from-code=UTF-8 -kT_ngettext:1,2 -kT_ -L PHP {} \;

