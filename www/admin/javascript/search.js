var searchReplaceValue = null;
var searchMinSearchLength = 3;

function searchSetMinSearchLength(s)
{
	searchMinSearchLength = s;
}

function searchDoSearchForm()
{
	$('#search').val($('#search').val().trim());

	if ($('#search').val()=='')
	{
		alert(_('You need to enter a search term.'));
		$('#search').focus();
		return false;
	} 
	else
	if ($('#search').val().length<searchMinSearchLength)
	{
		alert(_('The search term needs to be at least '+searchMinSearchLength+' characters.'));
		$('#search').focus();
		return false;
	} 
	else
	if ($('input[name*=modules]:checked').length==0 && $('input[name*=freeModules]:checked').length==0)
	{
		alert(_('You need to select at least one module'));
		return false;
	} 
	else
	if ($('#replaceToggle').is(':checked') && $('#replacement').val().trim()=='')
	{
		alert(_('You need to enter a replacement term.'));
		$('#replacement').focus();
		return false;
	} 
	else 
	if ($('#replaceToggle').is(':checked') && $('#optionsAll').is(':checked'))
	{
		if (!confirm(_('Are you sure? This action cannot be undone.')))
			return false;
		else
			return true;
	}
	else
	{
		return true;
	}

}
