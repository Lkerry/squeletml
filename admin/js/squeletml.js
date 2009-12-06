/**
Sélectionne le texte de l'élément fourni.
*/
function adminSelectionneTexte(sId)
{
	var myDiv = document.getElementById(sId);
	
	if (window.getSelection)
	{
		var selection = window.getSelection();
		
		/* Safari. */
		if (selection.setBaseAndExtent)
		{
			selection.setBaseAndExtent(myDiv, 0, myDiv, 1);
		}
		/* Firefox, Opera. */
		else
		{
			var range = document.createRange();
			
			range.selectNodeContents(myDiv);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}
	/* IE. */
	else
	{
		var range = document.body.createTextRange();
		
		range.moveToElementText(myDiv);
		range.select();
	}
}
