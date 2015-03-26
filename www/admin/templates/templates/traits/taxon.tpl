{include file="../shared/admin-header.tpl"}

<script>

var data=true;
var can_select_multiple=true;
var can_be_null=true;
var can_have_range=true;
var date_format=null;
var concept=null;
var group=null;
var trait=null;
var traittype=null;
var traitname=null;
var currentvalues=[];
var currentselection=[];
var oldvalue=null;

function setData( d )
{
	data=d;
}

function getData()
{
	return data;
}

function setConcept( id )
{
	concept=id;
}

function setGroup( id )
{
	group=id;
}

function setTrait( id )
{
	trait=id;
}

function setTraitType( type )
{
	traittype=type;
}

function getTraitType()
{
	return traittype;
}

function setTraitName( name )
{
	traitname=name;
}

function getTraitName()
{
	return traitname;
}

function setCanSelectMultiple( state )
{
	can_select_multiple=state;
}

function getCanSelectMultiple()
{
	return can_select_multiple;
}

function setCanBeNull( state )
{
	can_be_null=state;
}

function getCanBeNull()
{
	return can_be_null;
}

function setCanHaveRange( state )
{
	can_have_range=state;
}

function getCanHaveRange()
{
	return can_have_range;
}

function setSetDateFormat( format )
{
	date_format=format;
}

function getSetDateFormat()
{
	return date_format;
}

function setOldValue( value )
{
	oldvalue=value;
}

function getOldValue()
{
	return oldvalue;
}


function listTraitRemove( caller )
{
	if (currentselection.length<=1 && !getCanBeNull()) return;

	var id=$( caller ).attr( 'data' );

	for(var i=0;i<currentselection.length;i++)
	{
		if( currentselection[i].id==id )
		{
			currentselection.splice(i,1);
			break;
		}
	}

	if ( getTraitType()=='stringlist' )
	{
		$( "#dialog-message-body-content" ).html( __stringlist() );
	}
}

function listTraitAdd( caller )
{
	var newid=$( caller ).attr( 'data' );

	for(var i=0;i<currentselection.length;i++)
	{
		if( currentselection[i].id==newid ) return;
	}
	
	if ( currentselection.length>0 && !getCanSelectMultiple() )
	{
		currentselection.splice(0,currentselection.length);
	}

	for(var i=0;i<currentvalues.length;i++)
	{
		if( currentvalues[i].id==newid )
		{
			currentselection.push( { id:currentvalues[i].id, value:currentvalues[i].value } );
		}
	}

	if ( getTraitType()=='stringlist' )
	{
		$( "#dialog-message-body-content" ).html( __stringlist() );
	}
}

function __stringlist()
{
	var dummy=[];
	var selected=$('<p>');
	if (currentselection)
	{
		for( var i=0;i<currentselection.length;i++ )
		{
			selected.append( $(templateReplace( $( "#stringlist_template_two" ).html() , currentselection[i] )));
			dummy.push( currentselection[i].id );
		}
	}
	
	var values=$('<p>');
	for( var i=0;i<currentvalues.length;i++ )
	{
		if( dummy.indexOf( currentvalues[i].id )!=-1) continue;
		values.append( $(templateReplace( $( "#stringlist_template_three" ).html() , currentvalues[i] )));
	}
	
	return templateReplace( $( "#stringlist_template_one" ).html() , { SELECTED : selected.html(), VALUES : values.html() } );
}

function __stringfree()
{
	setOldValue( currentselection[0] && currentselection[0].value ? currentselection[0].value : '' );
	var selected=$('<p>').append( $(templateReplace( $( "#stringfree_template_two" ).html() , ( currentselection[0] ? currentselection[0] : { value: ''} ))));
	return templateReplace( $( "#stringfree_template_one" ).html() , { SELECTED : selected.html() } );
}

function __datefree()
{
	var b='';
	var selected=$('<p>');
	
	var df=getSetDateFormat();

	for(var i=0;i<currentselection.length;i++)
	{
		var val1='';
		var val2='';
		var c=currentselection[i];
		b+=c.value_start+c.value_end;
		
		if (getCanHaveRange() || c.value_end.length!=0)
		{
			val2=templateReplace( $( "#datefree_template_one" ).html() ,
				{ value:c.value_end?c.value_end:'', max_length:df.format_hr.length, name: 'value_end', placeholder: df.format_hr } );
		}

		val1=templateReplace( $( "#datefree_template_one" ).html() ,
			{ value:c.value_start, max_length:df.format_hr.length, name: 'value_start', placeholder: df.format_hr } );

		selected.append( $(templateReplace( $( "#datefree_template_two" ).html() , { value_start:val1, separator:( val2.length>0 ? ' - ' : '' ), value_end:val2 } )));
		
	}
	setOldValue( b );

//	getCanHaveRange();
//	getCanSelectMultiple();
//	getSetDateFormat();
	
	console.dir( currentselection );
	

	return templateReplace( $( "#datefree_template_three" ).html() , { SELECTED : selected.html() } );
}

function taxonTraitFormInit( data )
{
	setData( data );
	setTrait( data.trait.id );
	setTraitType( data.trait.type_sysname );
	setTraitName( data.trait.sysname );
	setCanSelectMultiple( data.trait.can_select_multiple==1 );
	setCanBeNull( data.trait.can_be_null==1 );
	setCanHaveRange( data.trait.can_have_range==1 );
	setSetDateFormat( { format:data.trait.date_format_format,format_hr:data.trait.date_format_format_hr } );
}

function taxonTraitForm()
{
	var d=getData();

	currentselection.splice(0,currentselection.length);
	currentvalues.splice(0,currentvalues.length);
	
	if ( getTraitType()=='stringlist' || getTraitType()=='stringfree' )
	{
		if ( d.taxon_values && d.taxon_values.values )
		{
			for(var i=0;i<d.taxon_values.values.length;i++)
			{
				currentselection.push({
					id:d.taxon_values.values[i].value_id,
					value:d.taxon_values.values[i].value_start
				});
			}
		}

		if ( d.trait.values )
		{
			for(var i=0;i<d.trait.values.length;i++)
			{
				currentvalues.push({
					id:d.trait.values[i].id,
					value:d.trait.values[i].string_value
				});
			}
		}
	}

	if ( getTraitType()=='datefree' )
	{
		if ( d.taxon_values && d.taxon_values.values )
		{
			for(var i=0;i<d.taxon_values.values.length;i++)
			{
				currentselection.push({
					id:d.taxon_values.values[i].value_id,
					value_start:d.taxon_values.values[i].value_start,
					value_end:d.taxon_values.values[i].value_end
				});
			}
		}
	}

	if ( getTraitType()=='stringlist' )
	{
		return __stringlist();
	}
	else
	if ( getTraitType()=='stringfree' )
	{
		return __stringfree();
	}
	else
	if ( getTraitType()=='datefree' )
	{
		return __datefree();
	}
}

function hasChanged()
{
	if ( getTraitType()=='stringfree' )
	{
		var newvalue='';
		$('textarea[name*=values]').each(function()
		{
			newvalue+=$(this).val();
		});
		
		return getOldValue()!=newvalue;
	}	
	
	return true;
}


function saveTaxonTrait()
{
	if (!hasChanged())
	{
		$( "#dialog-message" ).dialog( "close" );
		return;
	}

	var form=$( '<form method="POST"></form>' );
	form.append( '<input type="hidden" name="action" value="save" />' );

//<input type="hidden" name="rnd" value="{$rnd}" />

	form.append( '<input type="hidden" name="id" value="'+concept+'" />' );
	form.append( '<input type="hidden" name="group" value="'+group+'" />' );
	form.append( '<input type="hidden" name="trait" value="'+trait+'" />' );

	if ( getTraitType()=='stringfree' )
	{
		$('textarea[name*=values]').each(function()
		{
			form.append( '<input type="hidden" name="values[]" value="'+ $(this).val() +'" />' );
		});
	}	
	else
	{
		for(var i=0;i<currentselection.length;i++)
		{
			form.append( '<input type="hidden" name="values[]" value="'+ currentselection[i].id +'" />' );
		}
	}
	
	$('body').append(form);
	form.submit();
}

function editTaxonTrait( d )
{
	d.time=allGetTimestamp();
	d.action='get_taxon_trait';

	$.ajax({
		url: "ajax_taxon.php",
		data: d,
		success : function ( d )
		{
			taxonTraitFormInit( $.parseJSON( d ) );
			prettyDialog(
			{ 
				title : getTraitName() , 
				content : taxonTraitForm() , 
				buttons :
				{
					"save" : { text:'Save', click:function() { saveTaxonTrait(); } },
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

</script>


<div id="page-main">

{if !$concept || !$group}
    <p>
        {if !$concept}No concept selected.{/if}
    </p>
    <p>
	    {if !$group}No trait group selected.{/if}
    </p>
{else}

    <h2>
        <span style="font-size:12px;font-style:normal">{$group.sysname}:</span>
        {$concept.taxon}
    </h2>
    <p>
	    {if $group.parent}(parent: <a href="taxon.php?id={$concept.id}&group={$group.parent.id}">{$group.parent.sysname}</a>)<br />{/if}
    </p>
    {if $group.groups|@count>0}
    <p>
        subgroups:
        <ul>
        {foreach from=$group.groups item=v}
            <li><a href="taxon.php?id={$concept.id}&group={$v.id}">{$v.sysname}</a></li>
        {/foreach}
        </ul>
    </p>
    {/if}
	
            <!--
            
              ["max_length"]=>
              ["can_include_comment"]=>
              ["can_have_range"]=>
              ["date_format_format_hr"]=>
            
              ["type_sysname"]=>
              ["type_allow_values"]=>
              ["type_allow_select_multiple"]=>
              ["type_allow_max_length"]=>
              ["type_allow_unit"]=>
              ["value_count"]=>
              
            -->

    <p>
        <table>
            {foreach from=$traits item=v}
            <tr class="tr-highlight">
                <th style="width:200px">{$v.sysname}:</th>
                <td>
                {foreach from=$values item=t}
                {if $t.trait.id==$v.id}
                    {foreach from=$t.values item=i}
                    {$i.value_start}{if $i.value_end} - {$i.value_end}{/if}<br />
                    {/foreach}
                {/if}
                {/foreach}
                </td>
                <td>
					<a class="edit" data='{ "trait":{$v.id},"taxon":{$concept.id},"group":{$group.id} }'>edit</a>
                </td>
            </tr>
            {/foreach}
        </table>
        
        <p>
        	references:
        	<ul>
                {foreach from=$references item=v}
                <li>{$v.label}</li>
                {/foreach}
			</ul>        
        </p>
        
        
    </p>

{/if}
</div>

{include file="../shared/admin-messages.tpl"}
<script id="stringlist_template_one" language="text">
<table style="width:100%">
	<tr>
		<td style="width:50%;padding-left:5px;border-right:1px dotted #666;">available values</td>
		<td style="width:50%;padding-left:5px;">selected values</td>
	</tr>
	<tr>
		<td colspan=2 style="border-bottom:1px solid #666"></td>
	</tr>
	<tr>
		<td style="width:50%;border-right:1px dotted #666"><ul style="list-style-type:none;padding:0;margin:0 0 0 5px;" id="value-list">%VALUES%</ul></td>
		<td style="width:50%;"><ul style="list-style-type:none;padding:0;margin:0 0 0 5px;" id="selection-list">%SELECTED%</ul></td>
	</tr>
</table>
</script>

<script id="stringlist_template_two" language="text">
<li>%VALUE% <a href="#" class="edit selected-values" style="padding:5px" data="%ID%" onclick="listTraitRemove( this );return false;">X</a></li>
</script>

<script id="stringlist_template_three" language="text">
<li>%VALUE% <a href="#" class="edit" style="padding:5px" data="%ID%" onclick="listTraitAdd( this );return false;">&rarr;</a></li>
</script>


<script id="stringfree_template_one" language="text">
<table style="width:100%"><tr><td>%SELECTED%</td></tr></table>
</script>

<script id="stringfree_template_two" language="text">
<textarea class="__stringfree" style="width:100%;height:350px" name="values[]">%VALUE%</textarea>
</script>

<script id="datefree_template_one" language="text">
<input type="text" maxlength="%MAX_LENGTH%" name="%NAME%[]" value="%VALUE%" placeholder="%PLACEHOLDER%" style="width:50px;text-align:right">
</script>

<script id="datefree_template_two" language="text">
<tr><td>%VALUE_START%</td><td>%SEPARATOR%</td><td>%VALUE_END%</td></tr>
</script>

<script id="datefree_template_three" language="text">
<table style="" class="datefree_table">%SELECTED%</table>
</script>


<script>
$(document).ready(function()
{
	setConcept( {$concept.id} );
	setGroup( {$group.id} );
	
	$('a.edit').each(function()
	{ 
		$(this).attr('href','#').on('click',function(e)
		{
			try
			{
				var d=JSON.parse($(this).attr('data'));
				editTaxonTrait( d );
			}
			catch (err) {
				console.log( err );
			}
			e.preventDefault();
		});
	});
	$('#page-block-messages').fadeOut(3000);

});
</script>

{include file="../shared/admin-footer.tpl"}
