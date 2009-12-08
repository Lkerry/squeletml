/**
Sélectionne le texte de l'élément fourni.
*/
function adminSelectionneTexte(conteneur)
{
	var oConteneur = document.getElementById(conteneur);
	
	if (window.getSelection)
	{
		var selection = window.getSelection();
		
		/* Safari. */
		if (selection.setBaseAndExtent)
		{
			selection.setBaseAndExtent(oConteneur, 0, oConteneur, 1);
		}
		/* Firefox, Opera. */
		else
		{
			var plage = document.createRange();
			
			plage.selectNodeContents(oConteneur);
			selection.removeAllRanges();
			selection.addRange(plage);
		}
	}
	/* IE. */
	else
	{
		var plage = document.body.createTextRange();
		
		plage.moveToElementText(oConteneur);
		plage.select();
	}
}
