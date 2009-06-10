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
