var userTaxa=Array();
var roleID_sysAdmin=0;
var roleID_leadExpert=0;
var roleID_editor=0;

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

function setPermissions()
{
	$('[name=can_publish][value=0]').prop('checked',true);

	$('[name^=module]').prop('checked',true).trigger('change');
	$('[name^=custom]').prop('checked',true).trigger('change');

	$('[name^=module_read]').prop('checked',true);
	$('[name^=custom_read]').prop('checked',true);
	$('[name^=module_write]').prop('checked',false);
	$('[name^=custom_write]').prop('checked',false);

	if ($('#roles :selected').val()==roleID_sysAdmin)
	{
		$('[name=can_publish][value=1]').prop('checked',true);
		$('[name^=module_write]').prop('checked',true);
		$('[name^=custom_write]').prop('checked',true);
	}
	else
	if ($('#roles :selected').val()==roleID_leadExpert)
	{
		$('[name=can_publish][value=1]').prop('checked',true);
		$('[name^=module_write]').prop('checked',true);
		$('[name^=custom_write]').prop('checked',true);
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

	if ( $('#username').length!=0 && $('#username').val().trim().length==0 )
	{
		msg.push(_('A username is required.'));
	}

	if ( $('#first_name').length!=0 && $('#first_name').val().trim().length==0 )
	{
		msg.push(_('First name is required.'));
	}

	if ( $('#last_name').length!=0 && $('#last_name').val().trim().length==0 )
	{
		msg.push(_('Last name is required.'));
	}

	if ( $('#email_address').length!=0 && $('#email_address').val().trim().length==0 )
	{
		msg.push(_('Email address is required.'));
	}

	if ( $('#password').length!=0 && $('#password').val() != $('#password_repeat').val() )
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

