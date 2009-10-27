////////////////////////////////////////////////////////////////////////
//
// Fonctions pour le lien actif dans le menu. Merci à <http://www.richnetapps.com/automatically_highlight_current_page_in/>
//
////////////////////////////////////////////////////////////////////////

function extraitUrlPage(valeurHref)
{
	var tableauAdresse = valeurHref.split('/');
	return (tableauAdresse.length < 2) ? valeurHref : tableauAdresse[tableauAdresse.length - 2].toLowerCase() + tableauAdresse[tableauAdresse.length - 1].toLowerCase();
}

function lienActifAjouteClasse(tableauA, urlPageCourante)
{
	for (var i = 0; i < tableauA.length; i++)
	{
		if(extraitUrlPage(tableauA[i].href) == urlPageCourante)
		{
			if (tableauA[i].parentNode.tagName != 'div')
			{
				tableauA[i].className = 'actif';
				tableauA[i].parentNode.className = 'actif';
			}
		}
	}
}

function lienActif(idConteneur)
{
	valeurHref = document.location.href ? document.location.href : document.location;

	if (document.getElementById(idConteneur) != null)
	lienActifAjouteClasse(document.getElementById(idConteneur).getElementsByTagName('a'), extraitUrlPage(valeurHref));
}

////////////////////////////////////////////////////////////////////////
//
// Fonctions pour les boîtes déroulantes. Inspiré en partie de <http://forum.alsacreations.com/topic-4-33864-1-AfficherMasquer-un-bloc-dans-une-page-web-par-CSS.html>
//
////////////////////////////////////////////////////////////////////////

function boiteDeroulante(conteneur, titre, corps)
{
	var oConteneur = document.getElementById(conteneur);
	var oTitre = document.getElementById(titre);
	var oCorps = document.getElementById(corps);
	
	var oA = document.createElement('a');
	var oSpan1 = document.createElement('span');
	var oSpan2 = document.createElement('span');
	var oTxtSpan1 = '';
	var oTxtSpan2 = document.createTextNode($('#' + titre).html());
	var symbole = '';
	var temoinBoiteDeroulante = $.cookie('squeletmlBoiteDeroulante' + ucfirst(conteneur));
	
	switch(temoinBoiteDeroulante)
	{
		case 'masquer':
			oCorps.className = 'masquer';
			symbole = '+';
			break;
			
		case 'afficher':
			oCorps.className = 'afficher';
			symbole = '-';
			break;
			
		default:
			if(oCorps.className != 'masquer')
			{
				oCorps.className = 'afficher';
				symbole = '-';
			}
			else
			{
				symbole = '+';
			}
	}
	
	oA.href= '#';
	oA.setAttribute('class', 'boiteDeroulanteLien');
	oSpan2.appendChild(oTxtSpan2);
	oA.appendChild(oSpan2);
	$('#' + titre).html(oA);
	oTxtSpan1 = document.createTextNode('[' + symbole + ']&nbsp;');
	oSpan1.appendChild(oTxtSpan1);
	oSpan1.setAttribute('class', 'boiteDeroulanteSymbole');
	$('#' + titre + '>a span:first').before(oSpan1);
	$('#' + titre + '>a').html(html_entity_decode($('#' + titre + '>a').html()));
	oA.onclick = function()
	{
		boiteDeroulanteChangementDetat(conteneur, corps);
		
		if (oCorps.className == 'masquer')
		{
			symbole = '+';
		}
		else
		{
			symbole = '-';
		}
		
		$('#' + titre + '>a span:first').html(html_entity_decode('[' + symbole + ']&nbsp;'));
		
		return false;
	};
}

function boiteDeroulanteChangementDetat(conteneur, corps)
{
	var oCorps = document.getElementById(corps);
	var symbole = '';
	
	if (oCorps.className == 'masquer')
	{
		oCorps.className = 'afficher';
		$.cookie('squeletmlBoiteDeroulante' + ucfirst(conteneur), 'afficher', { expires: 30, path: '/' });
	}
	else
	{
		oCorps.className = 'masquer';
		$.cookie('squeletmlBoiteDeroulante' + ucfirst(conteneur), 'masquer', { expires: 30, path: '/' });
	}
}

////////////////////////////////////////////////////////////////////////
//
// Divers
//
////////////////////////////////////////////////////////////////////////

/**
Permet d'éviter l'écrasement d'événements se produisant lorsque plusieurs `window.onload` sont utilisés. Merci à <http://www.alsacreations.com/article/lire/565-JavaScript-organiser-son-code-en-modules.html>.
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

/**
Ajustement de la hauteur de `idAegaliser` pour la plus grande entre celle de `idDeComparaison1` et celle de `idDeComparaison2` si `idAegaliser` n'est pas déjà aussi haut.
*/
function egaliseHauteur(idAegaliser, idDeComparaison1, idDeComparaison2)
{
	if (document.getElementById(idAegaliser) && document.getElementById(idDeComparaison1) && document.getElementById(idDeComparaison2))
	{
		var hauteurIdAegaliser = document.getElementById(idAegaliser).offsetHeight;
		var hauteurIdDeComparaison1 = document.getElementById(idDeComparaison1).offsetHeight + 80;
		var hauteurIdDeComparaison2 = document.getElementById(idDeComparaison2).offsetHeight + 80;
	
		var hauteurMax = Math.max(hauteurIdDeComparaison1, hauteurIdDeComparaison2);
		if (hauteurMax > hauteurIdAegaliser)
		{
			document.getElementById(idAegaliser).style.height = hauteurMax + "px";
		}
	}
}

/**
Génère une table des matières pour la page en cours.
*/
function tableDesMatieres(idParent, baliseTable)
{
	$(document).ready(function()
	{
		var oPage = document.getElementById(idParent);
		var oDiv = document.createElement('div');
		var oUl = document.createElement(baliseTable);
		
		oDiv.setAttribute('id', 'tableDesMatieres');
		oUl.setAttribute('id', 'tableDesMatieresLiens');
		
		oDiv.appendChild(oUl);
		oPage.insertBefore(oDiv, oPage.firstChild);
		$("#tableDesMatieresLiens").tableOfContents($("#" + idParent), {startLevel: 2, depth: 6});
	})
}

