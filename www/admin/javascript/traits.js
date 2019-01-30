function doSaveOrder()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="saveorder" />');

	$( "li[id^=sortable]").each(function( index ) {
		form.append('<input type="hidden" name="sortable[]" value="'+$(this).attr('id').replace('sortable','')+'" />');
	});

	form.submit();
}


var name0Focused=false;

function duplicateSysLabel()
{
	if ($('#sysname').val().length>0 && !name0Focused)
	{
		$('#name').val($('#sysname').val());
		$('#names-0').val($('#sysname').val());
	}
}

function setName0Focused()
{
	name0Focused=true;
}

function checkAndSaveForm()
{
	var buffer=Array();

	$(':input').each(function()
	{
		if ($(this).attr('mandatory')=='mandatory' && $(this).val().length<1)
		{
			var id=$(this).attr('id');
			var label=$("label[for='"+id+"']").html();
			label=label.length<1 ? id : label;
			buffer.push(label);
		}
	});

	if (buffer.length>0)
	{
		alert("Values are missing for the following mandatory field(s):\n"+buffer.join("\n"));
		return false;
	}
	else
	{
		if ($('#project_type_id option:selected').attr('sysname').indexOf('date')!==0)
		{
			$('#date_format_id').remove();
		}

		$(":input[type!='hidden']").each(function()
		{
			if ($(this).is(':hidden')) $(this).remove();

		});

		$('#theForm').submit();
	}

}

function deleteItem( message )
{
	if ( confirm( message ? message : _('Are you sure?')) )
	{
		$('#theForm').unbind('submit');
		$('#action').val('delete');
		$('#theForm').submit();
	}
}

var valuelist=[];
var languages=[]
var valuelisthash=null;
var usersave=false;
var havelabels=false;
var newcounter=-1;

function hash(a)
{
	var b='';
	for (var i=0;i<valuelist.length;i++)
	{
		b+=valuelist[i].id+valuelist[i].value+(valuelist[i].labels ? valuelist[i].labels.join("\t") : '')+"\t";
	}
	return b;
}

function setUserSave()
{
	usersave=true;
}

function setInitialValueListHash()
{
	valuelisthash=hash(valuelist);
}

function publishRemark( r )
{
	$('#remarks').toggle(true).html(r).fadeOut(1000);
}

function characterCount()
{
	$('#character-count').html(maxlength-$('#newvalue').val().length);
}

function doAddTraitValue( v )
{
	if (havelabels && !v.labels)
	{
		var labels=[];
		for (var j=0;j<languages.length;j++)
		{
			labels.push( { language:languages[j].id, label:'' } );
		}
		v.labels=labels;
	}

	valuelist.push(v);
}

function addTraitValue( checkresult )
{
	var v=$('#newvalue').val();

	if (v.length==0)
	{
		publishRemark(_("cannot add a empty value"));
		return;
	}

	if (checkresult || typeof checkTraitValue=="function")
	{
		if (checkresult)
		{
			var r=checkresult;
		}
		else
		{
			var r=checkTraitValue(v);
		}

		if (r.result!=true)
		{
			if (r.remarks.length>0)
			{
				alert("Error(s) occurred:\n"+r.remarks.join("\n"));
			}
			return;
		}
		else
		{
			if (r.remarks.length>0)
			{
				publishRemark(r.remarks.join("<br />"));
			}
		}
	}

	doAddTraitValue( { id: newcounter--, value: v } );
	$('#newvalue').val('');
	characterCount();
}

function updateValueCount()
{
	$('#value-count').html(valuelist.length);
}

function verifyTraitValueRemoval( i )
{
	if (i>0)
	{
		return confirm( sprintf( _("There are %s taxa that have been assigned this value.\nAre you sure you want to remove it?"), i) );
	}
	else
	{
		return true;
	}
}

function removeTraitValue( i )
{
	valuelist.splice(i,1);
}

function updateValueList()
{
	$('#valuelist').html('');
	if (valuelist.length==0) return;

	var b=[];
	for (var i=0;i<valuelist.length;i++)
	{
		var val=valuelist[i];
		var l=[];

		for (var j=0;j<languages.length;j++)
		{
			var lng=languages[j];
			var thisval='';

			for(var k=0;k<val.labels.length;k++)
			{
				if (val.labels[k].language==lng.id)
				{
					thisval=val.labels[k].label;
				}
			}

			l.push(
				'<span class="language-labels">'+
				lng.language+': \
				<input \
					onkeyup="addTraitValueLabel(this)" \
					class="language-labels" \
					type="text" \
					maxlength="4000" \
					value="'+thisval+'" \
					value-id="'+i+'" \
					language-id="'+lng.id+'"> \
				</span>');
		}

		b.push(
			'<li id="sortable'+val.id+'" data-id="'+i+'">'+
			val.value+' \
			<a href="traitgroup_trait_value.php?id=' + val.id + '" class="edit">'+
			_('rename')+
			'</a> \
			<a href="#" class="edit" onclick="if(!verifyTraitValueRemoval('+val.usage_count+'))return;removeTraitValue('+i+');updateValueList();updateValueCount();return false;">'+
			_('remove')+
			'</a> \
			<span class="comment" title="currently assigned to '+val.usage_count+' taxa">(' + val.usage_count + ')</span>'+
			'<br />'+l.join('')+
			'</li>'
			);
	}
	$('#valuelist').html(b.join(''));
}

function checkUnsavedValues()
{
	if (!usersave && valuelisthash && valuelisthash!=hash(valuelist))
	{
		return "There are unsaved values!\nLeave page anyway?";
	}
}

function reorderValueList()
{
	var b=[];
	$("#valuelist li").each(function( index )
	{
		b.push(valuelist[$(this).attr('data-id')]);
	});
	valuelist.empty();
	valuelist=b.splice(0);
}

function traitValuesInitialise()
{
	updateValueList();
	updateValueCount();
	setInitialValueListHash();
	$('#newvalue').focus();
}

function doAddTraitLanguage(p)
{
	languages.push(p);
}

function addTraitValueLabel(ele)
{

	var id=$(ele).attr('value-id');
	var language=$(ele).attr('language-id');
	var value=$(ele).val();

	//console.log(id,language,value);
	var add=true;
	for(var i=0;i<valuelist.length;i++)
	{
		if (i==id)
		{
			for(var j=0;j<valuelist[i].labels.length;j++)
			{
				if (valuelist[i].labels[j].language==language)
				{
					valuelist[i].labels[j].label=value;
					add=false;
				}
			}
			if (add)
			{
				valuelist[i].labels.push( { language:language,label:value });
			}
		}
	}

}

function saveValues()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="save" />');

	for(var i=0;i<valuelist.length;i++)
	{
		var value=valuelist[i];

		form.append('<input type="hidden" name="values['+value.id+']" value="'+value.value+'" />');

		if (value.labels)
		{
			for(var j=0;j<value.labels.length;j++)
			{
				var label=value.labels[j];
				form.append('<input type="hidden" name="valuelabels['+value.id+']['+label.language+']" value="'+label.label+'" />');
			}
		}
	}

	$( "li[id^=sortable]").each(function( index )
	{
		form.append('<input type="hidden" name="sortable[]" value="'+$(this).attr('id').replace('sortable','')+'" />');
	});

	form.submit();
}


function saveRawData()
{
	var form=$('<form method="post" action="data_save.php"></form>').appendTo('body');

	$('.joinrows:checked').each(function()
	{
		form.append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'" />');
	});

	form.append('<input type="hidden" name="action" value="save" />');
	form.submit();
}






var taxonTraits = {}

taxonTraits.data=true;
taxonTraits.can_select_multiple=true;
taxonTraits.can_be_null=true;
taxonTraits.can_have_range=true;
taxonTraits.date_format=null;
taxonTraits.concept=null;
taxonTraits.group=null;
taxonTraits.groupLabel=null;
taxonTraits.trait=null;
taxonTraits.traittype=null;
taxonTraits.traitname=null;
taxonTraits.description=null;
taxonTraits.currentvalues=[];
taxonTraits.currentselection=[];
taxonTraits.oldvalue=null;
taxonTraits.default_language=null;
taxonTraits.dialogheight=600;
taxonTraits.references=[];

taxonTraits.setData=function ( d )
{
	taxonTraits.data=d;
}

taxonTraits.getData=function()
{
	return taxonTraits.data;
}

taxonTraits.setDefaultLanguage=function( d )
{
	taxonTraits.default_language=d;
}

taxonTraits.getDefaultLanguage=function()
{
	return taxonTraits.default_language;
}

taxonTraits.setConcept=function( id )
{
	taxonTraits.concept=id;
}

taxonTraits.setGroup=function( id )
{
	taxonTraits.group=id;
}

taxonTraits.setGroupLabel=function( label )
{
	taxonTraits.groupLabel=label;
}

taxonTraits.setTrait=function( id )
{
	taxonTraits.trait=id;
}

taxonTraits.setTraitType=function( type )
{
	taxonTraits.traittype=type;
}

taxonTraits.getTraitType=function()
{
	return taxonTraits.traittype;
}

taxonTraits.setTraitName=function( name )
{
	taxonTraits.traitname=name;
}

taxonTraits.setTraitDescription=function( description )
{
	taxonTraits.description = description;
}

taxonTraits.getTraitName=function()
{
	return taxonTraits.traitname;
}

taxonTraits.getTraitDescription = function()
{
	return '<p style="font-size: 12px;">' + taxonTraits.description + '</p>';
}

taxonTraits.setCanSelectMultiple=function( state )
{
	taxonTraits.can_select_multiple=state;
}

taxonTraits.getCanSelectMultiple=function()
{
	return taxonTraits.can_select_multiple;
}

taxonTraits.setCanBeNull=function( state )
{
	taxonTraits.can_be_null=state;
}

taxonTraits.getCanBeNull=function()
{
	return taxonTraits.can_be_null;
}

taxonTraits.setCanHaveRange=function( state )
{
	taxonTraits.can_have_range=state;
}

taxonTraits.getCanHaveRange=function()
{
	return taxonTraits.can_have_range;
}

taxonTraits.setSetDateFormat=function( format )
{
	taxonTraits.date_format=format;
}

taxonTraits.getSetDateFormat=function()
{
	return taxonTraits.date_format;
}

taxonTraits.setOldValue=function( value )
{
	taxonTraits.oldvalue=value;
}

taxonTraits.getOldValue=function()
{
	return taxonTraits.oldvalue;
}

taxonTraits.setDialogHeight=function( value )
{
	taxonTraits.dialogheight=value;
}

taxonTraits.getDialogHeight=function()
{
	return taxonTraits.dialogheight;
}

taxonTraits.listTraitRemove=function( caller )
{
	if (taxonTraits.currentselection.length<=1 && !taxonTraits.getCanBeNull()) return;

	var id=$( caller ).attr( 'data' );

	for(var i=0;i<taxonTraits.currentselection.length;i++)
	{
		if( taxonTraits.currentselection[i].id==id )
		{
			taxonTraits.currentselection.splice(i,1);
			break;
		}
	}

	if ( taxonTraits.getTraitType()=='stringlist' )
	{
		$( "#dialog-message-body-content" ).html( taxonTraits.__stringlist() );
	}
}

taxonTraits.listTraitAdd=function( caller )
{
	var newid=$( caller ).attr( 'data' );

	for(var i=0;i<taxonTraits.currentselection.length;i++)
	{
		if( taxonTraits.currentselection[i].id==newid ) return;
	}

	if ( taxonTraits.currentselection.length>0 && !taxonTraits.getCanSelectMultiple() )
	{
		taxonTraits.currentselection.splice(0,taxonTraits.currentselection.length);
	}

	for(var i=0;i<taxonTraits.currentvalues.length;i++)
	{
		if( taxonTraits.currentvalues[i].id==newid )
		{
			taxonTraits.currentselection.push( { id:taxonTraits.currentvalues[i].id, value:taxonTraits.currentvalues[i].value } );
		}
	}

	if ( taxonTraits.getTraitType()=='stringlist' )
	{
		$( "#dialog-message-body-content" ).html( taxonTraits.__stringlist() );
	}
}

taxonTraits.__stringlist=function()
{
	var dummy=[];
	var selected=$('<p>');
	if (taxonTraits.currentselection)
	{
		for( var i=0;i<taxonTraits.currentselection.length;i++ )
		{
			selected.append( $(templateReplace( $( "#stringlist_template_two" ).html() , taxonTraits.currentselection[i] )));
			dummy.push( taxonTraits.currentselection[i].id );
		}
	}

	var values=$('<p>');
	for( var i=0;i<taxonTraits.currentvalues.length;i++ )
	{
		if( dummy.indexOf( taxonTraits.currentvalues[i].id )!=-1) continue;
		values.append( $(templateReplace( $( "#stringlist_template_three" ).html() , taxonTraits.currentvalues[i] )));
	}

	return templateReplace( $( "#stringlist_template_one" ).html() , { SELECTED : selected.html(), VALUES : values.html() } );
}

taxonTraits.__stringfree=function()
{
	taxonTraits.setOldValue( taxonTraits.currentselection[0] && taxonTraits.currentselection[0].value ? taxonTraits.currentselection[0].value : '' );
	var selected=$('<p>').append(
		$(templateReplace(
			$( "#stringfree_template_two" ).html() ,
			( taxonTraits.currentselection[0] ? taxonTraits.currentselection[0] : { value: ''} )
		))
	);
	return templateReplace( $( "#stringfree_template_one" ).html() , { SELECTED : selected.html() } );
}

taxonTraits.__datefree=function()
{
	var b='';
	var selected=$('<p>');
	var df=taxonTraits.getSetDateFormat();

	for(var i=0;i<taxonTraits.currentselection.length;i++)
	{
		var val1='';
		var val2='';
		var c=taxonTraits.currentselection[i];
		b+=c.value_start+c.value_end;

		val1=templateReplace( $( "#datefree_template_one" ).html() ,
			{ value:c.value_start?c.value_start:'', max_length:df.format_hr.length, name: 'value_start', placeholder: df.format_hr } );

		if (taxonTraits.getCanHaveRange() || (c.value_end !== null && c.value_end.length!=0))
		{
			val2=templateReplace( $( "#datefree_template_one" ).html() ,
				{ value:c.value_end?c.value_end:'', max_length:df.format_hr.length, name: 'value_end', placeholder: df.format_hr } );
		}

		selected.append(
			$( templateReplace(
				$( "#datefree_template_two" ).html() ,
				{ value_start:val1, separator:( val2.length>0 ? ' - ' : '' ), value_end:val2 }
			)
		));

	}

	if (i==0)
	{
		val1=templateReplace( $( "#datefree_template_one" ).html() ,
			{ value:'', max_length:df.format_hr.length, name: 'value_start', placeholder: df.format_hr } );

		if ( taxonTraits.getCanHaveRange() || c.value_end.length!=0 )
		{
			val2=templateReplace( $( "#datefree_template_one" ).html() ,
				{ value:'', max_length:df.format_hr.length, name: 'value_end', placeholder: df.format_hr } );
		}

		selected.append(
			$( templateReplace(
				$( "#datefree_template_two" ).html() ,
				{ value_start:val1, separator:( val2.length>0 ? ' - ' : '' ), value_end:val2 }
			)
		));
	}

	taxonTraits.setOldValue( b );

//	taxonTraits.getCanHaveRange();
//	taxonTraits.getCanSelectMultiple();
//	taxonTraits.getSetDateFormat();

	return templateReplace( $( "#datefree_template_three" ).html() , { SELECTED : selected.html() } );
}

taxonTraits.__floatfree=function()
{
	var b='';
	var selected=$('<p>');

	for(var i=0;i<taxonTraits.currentselection.length;i++)
	{
		var val1='';
		var val2='';
		var c=taxonTraits.currentselection[i];
		b+=c.value_start+c.value_end;

		val1=templateReplace( $( "#datefree_template_one" ).html() ,
			{ value:c.value_start?c.value_start:'', max_length:10, name: 'value_start', placeholder: '' } );

		if (taxonTraits.getCanHaveRange() || (c.value_end !== null && c.value_end.length!=0))
		{
			val2=templateReplace( $( "#datefree_template_one" ).html() ,
				{ value:c.value_end?c.value_end:'', max_length:10, name: 'value_end', placeholder: '' } );
		}

		selected.append(
			$( templateReplace(
				$( "#datefree_template_two" ).html() ,
				{ value_start:val1, separator:( val2.length>0 ? ' - ' : '' ), value_end:val2 }
				)
			));

	}

	if (i==0)
	{
		val1=templateReplace( $( "#datefree_template_one" ).html() ,
			{ value:'', max_length:10, name: 'value_start', placeholder: '' } );

		if ( taxonTraits.getCanHaveRange() || c.value_end.length!=0 )
		{
			val2=templateReplace( $( "#datefree_template_one" ).html() ,
				{ value:'', max_length:10, name: 'value_end', placeholder: '' } );
		}

		selected.append(
			$( templateReplace(
				$( "#datefree_template_two" ).html() ,
				{ value_start:val1, separator:( val2.length>0 ? ' - ' : '' ), value_end:val2 }
				)
			));
	}

	taxonTraits.setOldValue( b );

	return templateReplace( $( "#datefree_template_three" ).html() , { SELECTED : selected.html() } );
}

taxonTraits.taxonTraitFormInit=function( data )
{
	taxonTraits.setData( data );
	taxonTraits.setDefaultLanguage( data.default_project_language );
	if ( data.trait )
	{
		taxonTraits.setTrait( data.trait.id );
		taxonTraits.setTraitType( data.trait.type_sysname );
		taxonTraits.setTraitName( data.trait.sysname );
		taxonTraits.setTraitDescription( data.trait.description );
		taxonTraits.setCanSelectMultiple( data.trait.can_select_multiple==1 );
		taxonTraits.setCanBeNull( data.trait.can_be_null==1 );
		taxonTraits.setCanHaveRange( data.trait.can_have_range==1 );
		taxonTraits.setSetDateFormat( { format:data.trait.date_format_format,format_hr:data.trait.date_format_format_hr } );
	}
}

taxonTraits.taxonTraitForm=function()
{
	var d=taxonTraits.getData();

	taxonTraits.currentselection.splice(0,taxonTraits.currentselection.length);
	taxonTraits.currentvalues.splice(0,taxonTraits.currentvalues.length);

	taxonTraits.setDialogHeight( 400 );

	if ( taxonTraits.getTraitType()=='stringlist' || taxonTraits.getTraitType()=='stringfree' )
	{
		if ( d.taxon_values && d.taxon_values.values )
		{
			for(var i=0;i<d.taxon_values.values.length;i++)
			{
				taxonTraits.currentselection.push({
					id:d.taxon_values.values[i].value_id,
					value:d.taxon_values.values[i].value_start
				});
			}
		}

		if ( d.trait.values )
		{
			for(var i=0;i<d.trait.values.length;i++)
			{
				if (d.trait.values[i].language_labels && d.trait.values[i].language_labels[taxonTraits.getDefaultLanguage()])
					var label=d.trait.values[i].language_labels[taxonTraits.getDefaultLanguage()];
				else
					var label=d.trait.values[i].string_value;

				taxonTraits.currentvalues.push({
					id:d.trait.values[i].id,
					value:label
				});
			}
		}
	}

	if ( taxonTraits.getTraitType()=='datefree' || taxonTraits.getTraitType()=='floatfree' )
	{
		if ( d.taxon_values && d.taxon_values.values )
		{
			for(var i=0;i<d.taxon_values.values.length;i++)
			{
				taxonTraits.currentselection.push({
					id:d.taxon_values.values[i].value_id,
					value_start:d.taxon_values.values[i].value_start,
					value_end:d.taxon_values.values[i].value_end
				});
			}
		}
	}

	if ( taxonTraits.getTraitType()=='stringlist' )
	{
		return taxonTraits.__stringlist();
	}
	else
	if ( taxonTraits.getTraitType()=='stringfree' )
	{
		return taxonTraits.__stringfree();
	}
	else
	if ( taxonTraits.getTraitType()=='datefree' )
	{
		taxonTraits.setDialogHeight( 200 );
		return taxonTraits.__datefree();
	}
	else
	if ( taxonTraits.getTraitType()=='floatfree' )
	{
		taxonTraits.setDialogHeight( 200 );
		return taxonTraits.__floatfree();
	}
}

taxonTraits.hasChanged=function()
{
	if ( taxonTraits.getTraitType()=='stringfree' )
	{
		var newvalue='';
		$('textarea[name*=values]').each(function()
		{
			newvalue+=$(this).val();
		});

		return taxonTraits.getOldValue()!=newvalue;
	}

	return true;
}

taxonTraits.saveTaxonTrait=function()
{
	if (!taxonTraits.hasChanged())
	{
		$( "#dialog-message" ).dialog( "close" );
		return;
	}

	var form=$( '<form method="POST"></form>' );
	form.append( '<input type="hidden" name="action" value="save" />' );
	form.append( '<input type="hidden" name="id" value="'+taxonTraits.concept+'" />' );
	form.append( '<input type="hidden" name="group" value="'+taxonTraits.group+'" />' );
	form.append( '<input type="hidden" name="trait" value="'+taxonTraits.trait+'" />' );

	if ( taxonTraits.getTraitType()=='stringfree' )
	{
		$('textarea[name*=values]').each(function()
		{
			form.append( '<input type="hidden" name="values[]" value="'+ $(this).val() +'" />' );
		});
	}
	else
	if ( taxonTraits.getTraitType()=='datefree' || taxonTraits.getTraitType()=='floatfree')
	{
		$('input.__datefree[type=text]').each(function()
		{
			form.append( '<input type="hidden" name="'+ $(this).attr( 'name' ) +'" value="'+ $(this).val() +'" />' );
		});
	}
	else
	{
		for(var i=0;i<taxonTraits.currentselection.length;i++)
		{
			form.append( '<input type="hidden" name="values[]" value="'+ taxonTraits.currentselection[i].id +'" />' );
		}
	}

	$('body').append(form);
	form.submit();
}

taxonTraits.editTaxonTrait = function( d )
{
	d.time=allGetTimestamp();
	d.action='get_taxon_trait';

	$.ajax({
		url: "ajax_taxon.php",
		data: d,
		success : function ( d )
		{
			taxonTraits.taxonTraitFormInit( $.parseJSON( d ) );

			prettyDialog(
			{
				title : taxonTraits.getTraitName() ,
				content : taxonTraits.getTraitDescription() + taxonTraits.taxonTraitForm() ,
				width: 600,
				height: taxonTraits.getDialogHeight(),
				buttons :
				{
					"save" : { text:'Save', click:function() { taxonTraits.saveTaxonTrait(); } },
					"cancel" : { text:'Cancel', click:function() { $( this ).dialog( "close" ); } } }
				}
			);
		}
	}).done(function()
	{
		$( "#dialog-message-body-content" ).css( "font-family" , $( "body" ).css( "font-family" ) ).css( "font-size" , "0.9em" );
		$( ".__stringfree" ).css( "font-family" , $( "body" ).css( "font-family" ) ).css( "font-size" , "0.9em" );
	});
}

taxonTraits.setReference=function( ref )
{
	for(var i=0;i<taxonTraits.references.length;i++)
	{
		if (taxonTraits.references[i].literature_id==ref.literature_id) return;
	}
	taxonTraits.references.push( ref );
}

taxonTraits.removeReference=function( i )
{
	taxonTraits.references.splice(i,1);
}

taxonTraits.printReferences=function()
{
	var buffer=[]
	for(var i=0;i<taxonTraits.references.length;i++)
	{
		buffer.push(
			'<li>'+
				'<a href="../literature2/edit.php?id='+taxonTraits.references[i].literature_id+'">' +
				taxonTraits.references[i].label+
				'</a>'+
				' <a href="#" onclick="taxonTraits.removeReference('+i+');taxonTraits.printReferences();return false;">x</a></li>'
		 );
	}
	$( '#references' ).html( buffer.join('') );
}

taxonTraits.saveReferences=function()
{
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="savereferences" />');

	for(var i=0;i<taxonTraits.references.length;i++)
	{
		form.append(
			'<input type="hidden" name="references[]" value="'+
				(taxonTraits.references[i].id ? taxonTraits.references[i].id : -1) +','+ taxonTraits.references[i].literature_id +
			'" />');
	};

	form.submit();

}

taxonTraits.deleteTraitsReferencesByGroup=function(group)
{
	if (!taxonTraits.concept || !taxonTraits.group) return;
	
	if (!confirm(sprintf(_('Delete all traits and references for trait group %s?'),taxonTraits.groupLabel))) return;

	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="deletegroup" />');
	form.append( '<input type="hidden" name="id" value="'+taxonTraits.concept+'" />' );
	form.append( '<input type="hidden" name="group" value="'+taxonTraits.group+'" />' );
	form.submit();
}

taxonTraits.deleteTraitsReferences=function()
{
	if (!confirm(_('Delete all traits and references?'))) return;

	var form=$('<form method="post"></form>').appendTo('body');
	form.append( '<input type="hidden" name="id" value="'+taxonTraits.concept+'" />' );
	form.append('<input type="hidden" name="action" value="deleteall" />');
	form.submit();
}