////////////////////////////////////////////////////////////////////////
//
// Fonctions pour le lien actif dans le menu. Merci à <http://www.richnetapps.com/automatically_highlight_current_page_in/>
//
////////////////////////////////////////////////////////////////////////

function extractPageName(hrefString)
{
	var arr = hrefString.split('/');
	return  (arr.length < 2) ? hrefString : arr[arr.length - 2].toLowerCase() + arr[arr.length - 1].toLowerCase();
}

function setActiveMenu(arr, crtPage)
{
	for (var i = 0; i < arr.length; i++)
	{
		if(extractPageName(arr[i].href) == crtPage)
		{
			if (arr[i].parentNode.tagName != "DIV")
			{
				arr[i].className = "actif";
				arr[i].parentNode.className = "actif";
			}
		}
	}
}

function setPage()
{
	hrefString = document.location.href ? document.location.href : document.location;

	if (document.getElementById("menu") != null)
	setActiveMenu(document.getElementById("menu").getElementsByTagName("a"), extractPageName(hrefString));
}

////////////////////////////////////////////////////////////////////////
//
// Fonctions pour les boîtes déroulantes. Inspiré en partie de <http://forum.alsacreations.com/topic-4-33864-1-AfficherMasquer-un-bloc-dans-une-page-web-par-CSS.html>
//
////////////////////////////////////////////////////////////////////////

function boiteDeroulanteParDefaut(conteneur, titre, corps)
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

