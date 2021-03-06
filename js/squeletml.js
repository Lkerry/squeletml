/***********************************************************************
**
** Général.
**
***********************************************************************/

/*
Évite l'écrasement d'événements se produisant lorsque plusieurs `window.onload` sont utilisés. Merci à <http://www.alsacreations.com/article/lire/565-JavaScript-organiser-son-code-en-modules.html>.
*/
function ajouteEvenementLoad(fonction)
{
	if (window.addEventListener)
	{
		window.addEventListener('load', fonction, false);
	}
	else if (document.addEventListener)
	{
		document.addEventListener('load', fonction, false);
	}
	else if (window.attachEvent)
	{
		window.attachEvent('onload', fonction);
	}
}

/*
Si `idAegaliser` n'est pas déjà aussi haut, ajuste sa hauteur à la valeur de la plus grande hauteur entre celle de `idDeComparaison1` et celle de `idDeComparaison2`, plus `hauteurEnPlus`.
*/
function egaliseHauteur(idAegaliser, idDeComparaison1, idDeComparaison2, hauteurEnPlus)
{
	var oIdAegaliser = document.getElementById(idAegaliser);
	var oIdDeComparaison1 = document.getElementById(idDeComparaison1);
	var oIdDeComparaison2 = document.getElementById(idDeComparaison2);
	
	if (oIdAegaliser && (oIdDeComparaison1 || oIdDeComparaison2))
	{
		oIdAegaliser.style.height = 'auto';
		var hauteurIdAegaliser = oIdAegaliser.offsetHeight;
		
		if (oIdDeComparaison1)
		{
			var hauteurIdDeComparaison1 = oIdDeComparaison1.offsetHeight;
		}
		
		if (oIdDeComparaison2)
		{
			var hauteurIdDeComparaison2 = oIdDeComparaison2.offsetHeight;
		}
		
		if (oIdDeComparaison1 && oIdDeComparaison2)
		{
			var hauteurMax = Math.max(hauteurIdDeComparaison1, hauteurIdDeComparaison2);
		}
		else if (oIdDeComparaison1)
		{
			var hauteurMax = hauteurIdDeComparaison1;
		}
		else
		{
			var hauteurMax = hauteurIdDeComparaison2;
		}
		
		hauteurMax = hauteurMax + hauteurEnPlus;
		
		if (hauteurMax > hauteurIdAegaliser)
		{
			oIdAegaliser.style.height = hauteurMax + 'px';
		}
	}
}

/*
Génère une table des matières pour la page en cours.
*/
function tableDesMatieres(idParent, baliseTable, baliseTitre, niveauDepart, niveauArret, langue, langueParDefaut)
{
	$(document).ready(function()
	{
		var oParent = document.getElementById(idParent);
		var oConteneur = document.createElement('div');
		var oTitre = document.createElement(baliseTitre);
		var oTitreTexte = '';
		var oTable = document.createElement(baliseTable);
		
		oConteneur.setAttribute('id', 'tableDesMatieresTmp');
		oTitre.setAttribute('id', 'tableDesMatieresBdTitre');
		oTitre.setAttribute('class', 'bDtitre');
		
		var oTraductionsTitre = {
			'fr': 'Table des matières',
			'en': 'Table of contents'
		};
		
		var titreTexte = oTraductionsTitre['fr'];
		
		if (oTraductionsTitre.hasOwnProperty(langue))
		{
			titreTexte = oTraductionsTitre[langue];
		}
		else if (oTraductionsTitre.hasOwnProperty(langueParDefaut))
		{
			titreTexte = oTraductionsTitre[langueParDefaut];
		}
		
		oTitreTexte = document.createTextNode(titreTexte);
		
		oTable.setAttribute('id', 'tableDesMatieresBdCorps');
		oTable.setAttribute('class', 'bDcorps');
		$(oTable).addClass('afficher');
		
		oConteneur.appendChild(oTable);
		oTitre.appendChild(oTitreTexte);
		
		oParent.insertBefore(oConteneur, oParent.firstChild);
		$('#tableDesMatieresBdCorps').tableOfContents(oParent, {startLevel: niveauDepart, depth: niveauArret});
		oConteneur.insertBefore(oTitre, oConteneur.firstChild);
		
		// S'il n'y a qu'un `li` vide (en fait, ne contenant qu'un retour à la ligne), aucun titre n'a été trouvé, on peut donc supprimer la table des matières, qui ne sert à rien. Note: pour IE6, le `li` est vraiment vide (aucun retour à la ligne).
		if ($('#tableDesMatieresBdCorps li').length == 1 && ($('#tableDesMatieresBdCorps > li').text() == "\n" || $('#tableDesMatieresBdCorps > li').text() == ''))
		{
			$('#tableDesMatieresTmp').remove();
		}
		
		var oTableDesMatieres = $(oParent).find('div#tableDesMatieres');
		var oChapeau = $(oParent).find('div.chapeau');
		var oDiC = $(oParent).find('div#debutInterieurContenu');
		var oH1 = $(oParent).find('h1');
		
		if (oTableDesMatieres.length > 0)
		{
			$(oTableDesMatieres[0]).append($('#tableDesMatieresTmp').html());
			$('#tableDesMatieresTmp').remove();
		}
		else
		{
			if (oChapeau.length > 0)
			{
				$(oChapeau[0]).after($('#tableDesMatieresTmp'));
			}
			else if (oDiC.length > 0)
			{
				$(oDiC[0]).after($('#tableDesMatieresTmp'));
			}
			else if (oH1.length > 0)
			{
				$(oH1[0]).after($('#tableDesMatieresTmp'));
			}
			
			$('#tableDesMatieresTmp').attr('id', 'tableDesMatieres');
		}
	})
}

/*
Gère une boîte déroulante. Si la variable `aExecuterApresClic` n'est pas vide, son contenu est exécuté par `eval()` après un clic sur une boîte déroulante.

Inspiré en partie de <http://forum.alsacreations.com/topic-4-33864-1-AfficherMasquer-un-bloc-dans-une-page-web-par-CSS.html>.
*/
function boiteDeroulante(conteneur, aExecuterApresClic)
{
	$(conteneur).each(function()
	{
		if (conteneur == '#tableDesMatieres')
		{
			var cheminTitre = '#tableDesMatieresBdTitre';
			var cheminCorps = '#tableDesMatieresBdCorps';
		}
		else
		{
			var cheminTitre = '.bDtitre';
			var cheminCorps = '.bDcorps';
		}
	
		var oTitre = $(this).find(cheminTitre).get(0);
		var oCorps = $(this).find(cheminCorps).get(0);
		var oA = document.createElement('a');
		var oSpan1 = document.createElement('span');
		var oSpan2 = document.createElement('span');
		var oSpan3 = document.createElement('span');
		var oTexteSpan1 = '';
		var oTexteSpan1fin = '';
		var oTexteSpan2 = '';
		var oTexteSpan3 = document.createTextNode($(oTitre).html());
		var symbole = '';
		var nomTemoin = 'squeletmlBoiteDeroulante';
		
		if (conteneur.substr(0, 1) == '.')
		{
			nomTemoin += 'Class';
		}
		else if (conteneur.substr(0, 1) == '#')
		{
			nomTemoin += 'Id';
		}
		
		nomTemoin += ucfirst(conteneur.substr(1));
		
		var temoinBoiteDeroulante = $.cookie(nomTemoin);
		
		switch(temoinBoiteDeroulante)
		{
			case 'masquer':
				$(oCorps).removeClass('afficher').addClass('masquer');
				symbole = '+';
				break;
			
			case 'afficher':
				$(oCorps).removeClass('masquer').addClass('afficher');
				symbole = '-';
				break;
			
			default:
				if (!$(oCorps).hasClass('afficher'))
				{
					$(oCorps).addClass('masquer');
					symbole = '+';
				}
				else
				{
					symbole = '-';
				}
		}
		
		oA.href= '#';
		oA.setAttribute('class', 'boiteDeroulanteLien');
		oSpan3.appendChild(oTexteSpan3);
		oA.appendChild(oSpan3);
		$(oTitre).html(oA);
		oTexteSpan1 = document.createTextNode('[');
		oTexteSpan1fin = document.createTextNode(']&nbsp;');
		oTexteSpan2 = document.createTextNode(symbole);
		oSpan2.appendChild(oTexteSpan2);
		oSpan2.setAttribute('class', 'boiteDeroulanteVisuelSymbole');
		oSpan1.appendChild(oTexteSpan1);
		oSpan1.appendChild(oSpan2);
		oSpan1.appendChild(oTexteSpan1fin);
		oSpan1.setAttribute('class', 'boiteDeroulanteVisuel');
		$(oTitre).find('>a span:first').before(oSpan1);
		$(oTitre).find('>a').html(html_entity_decode($(oTitre).find('>a').html()));
		oA.onclick = function()
		{
			if ($(oCorps).hasClass('masquer'))
			{
				$(oCorps).removeClass('masquer').addClass('afficher');
				symbole = '-';
				$.cookie(nomTemoin, 'afficher', { expires: 365, path: '/' });
			}
			else
			{
				$(oCorps).removeClass('afficher').addClass('masquer');
				symbole = '+';
				$.cookie(nomTemoin, 'masquer', { expires: 365, path: '/' });
			}
			
			$(oTitre).find('>a span.boiteDeroulanteVisuelSymbole').html(symbole);
			
			if (aExecuterApresClic.length)
			{
				eval(aExecuterApresClic);
			}
			
			return false;
		};
	});
}
