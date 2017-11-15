var currentId=null;
var parentLabel=null;
var parentId=null;
var taxa=[];
var projectLanguages=[];
var characterTypes=[];


function dialogAddGroup( data )
{
	var id=+ new Date();
	var buffer=[];
	buffer.push(
		fetchTemplate( 'labelInputTpl' )
			.replace( /%LABEL%/g, _('internal') )
			.replace( '%NAME%', 'sys_name' )
			.replace( '%VALUE%', data ? data.sys_name : "" )
			.replace( '%CLASS%', "" )
			.replace( '%ID%', "lead-" + id )
	);
	for(var i=0;i<projectLanguages.length;i++)
	{
		var value="";
		if (data && data.labels)
		{
			for(var j=0;j<data.labels.length;j++)
			{
				if (data.labels[j].language_id==projectLanguages[i].id)
				{
					value=data.labels[j].label;
				}
			}
		}

		buffer.push(
			fetchTemplate( 'labelInputTpl' )
				.replace( /%LABEL%/g, projectLanguages[i].label )
				.replace( '%NAME%', projectLanguages[i].id )
				.replace( '%VALUE%', value )
				.replace( '%CLASS%', "follow-" + id  )
				.replace( '%ID%', "" )
		);
	}

	if (data)
	{
		var cCount=data.characters ? data.characters.length : 0;
		buffer.push(
			fetchTemplate( 'groupCharacterCountTpl' )
				.replace( '%COUNT%', cCount )
		);
	}

	var content=
		fetchTemplate( 'editGroupTpl' )
			.replace( '%FORM%', buffer.join("\n").replace(/%SAVE_GROUP%/g,id))
			.replace('%HEADER%',data ? fetchTemplate( 'editGroupHeaderTpl' ) :  fetchTemplate( 'newGroupHeaderTpl' ) );

	var extraData={ savegroup: id };
	if (data)
	{
		extraData.id = data.id;
	}

	var msg = _("Are you sure?" + (cCount>0 ? "\n" + _("(grouped characters will not be deleted)") : "" ));

	saveDialog({
		title: data ? sprintf(_('Edit group: %s'),data.sys_name) : _('Add group'), 
		content: content, 
		callback: saveGroup, 
		data: extraData,
		buttons: data ?
			{ Delete : function()
				{
					if( confirm(msg) )
					{
						deleteGroup(data.id);
					}
					$( this ).dialog( "close" ); } 
			} : null
	});

	$('#lead-' + id).on('blur',function()
	{
		$('.follow-' + id).each(function()
		{
			$(this).val( $(this).val().length==0 ? $('#lead-' + id).val() : $(this).val() );
		})
	})
}

function dialogEditGroup()
{
	if (currentId==null) return;

	$.ajax({
		url : 'ext_ajax_interface.php',
		data : ({
			'action' : 'get_group',
			'id' : currentId ,
			'time' : allGetTimestamp()
		}),
		success : function( data )
		{
			data=$.parseJSON( data );
			dialogAddGroup( data );
		}
	})
}
