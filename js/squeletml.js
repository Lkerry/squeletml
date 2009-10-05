// DÉBUT du code pour le lien actif dans le menu. Merci à <http://www.richnetapps.com/automatically_highlight_current_page_in/>

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

// FIN du code pour le lien actif dans le menu.

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

