function selectionneTexte(sId)
{
	var myDiv = document.getElementById(sId);
	if (window.getSelection)
	{
		var selection = window.getSelection();
		if (selection.setBaseAndExtent)
		{ /* for Safari */
			selection.setBaseAndExtent(myDiv, 0, myDiv, 1);
		}
		else
		{ /* for FF, Opera */
			var range = document.createRange();
			range.selectNodeContents(myDiv);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}
	else
	{ /* for IE */
		var range = document.body.createTextRange();
		range.moveToElementText(myDiv);
		range.select();
	}
}
