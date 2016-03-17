var userTaxa=Array();

function deleteUser()
{
	if ( confirm(_('Are you sure?')) )
	{
		$('#action').val('delete');
		$('#theForm').attr('onsubmit','');
		$('#theForm').attr('action','delete.php');
		$('#theForm').submit();
	}
}

function resetPermissions()
{
	if ( confirm(_('Are you sure?')) )
	{
		$('#action').val('reset_permissions');
		$('#theForm').attr('onsubmit','');
		$('#theForm').submit();
	}	
}

function submitUserEditForm()
{
	var msg=Array();

	if ( $('#id').val().length==0 )
	{
		if ( $('#password').val().length==0 && $('#password_repeat').val().length==0 )
		{
			msg.push(_('A password is required.'));
		}
	}

	if ( $('#username').val().trim().length==0 )
	{
		msg.push(_('A username is required.'));
	}

	if ( $('#first_name').val().trim().length==0 )
	{
		msg.push(_('First name is required.'));
	}

	if ( $('#last_name').val().trim().length==0 )
	{
		msg.push(_('Last name is required.'));
	}

	if ( $('#email_address').val().trim().length==0 )
	{
		msg.push(_('Email address is required.'));
	}

	if ( $('#password').val() != $('#password_repeat').val() )
	{
		msg.push(_('Passwords not the same.'));
	}
	
	if ( msg.length>0 ) 
	{
		alert( msg.join('\n') );
		return false;
	}
	else
	{
		if ( userTaxa )
		{
			for(var i=0;i<userTaxa.length;i++)
			{
				$('#theForm').append('<input type="hidden" name="taxon[]" value="' + userTaxa[i].id +'" />');
			}
		}

		return true;
	}
}

function addTaxaToUserList( taxon )
{
	for(var i=0;i<userTaxa.length;i++)
	{
		if (userTaxa[i].id==taxon.id) return;
	}
	userTaxa.push( taxon );
}

function removeTaxonFromUserList( id )
{
	var index=-1;

	for(var i=0;i<userTaxa.length;i++)
	{
		if (userTaxa[i].id==id) index=i;
	}

	if (index > -1) userTaxa.splice(index, 1);
}

function taxonToUserList()
{
	addTaxaToUserList( { id: $('#taxon_id').val(), name: $('#taxon').html() } );
	buildTaxaUserList()
}

function buildTaxaUserList()
{
	$('#taxa').empty();	

	for(var i=0;i<userTaxa.length;i++)
	{
		$('#taxa').append( '<li data-id="' + userTaxa[i].id + '">' + userTaxa[i].name +' <a href="#" onclick="removeTaxonFromUserList(' + userTaxa[i].id + ');buildTaxaUserList();return false;">x</a></li>' );
	}
}

