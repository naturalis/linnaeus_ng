{include file="../shared/admin-header.tpl"}

<script>

var can_select_multiple=true;
var can_be_null=true;
var concept=null;
var group=null;
var trait=null;

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

function addTaxonTrait( caller )
{
	try {
		var newdata=$.parseJSON( $( caller ).attr( 'data' ) );
		var exists=false;
		$( '.selected-values' ).each(function()
		{
			var data=$.parseJSON( $(this).attr( 'data' ) );
			if (data.value_id==newdata.id) exists=true;
		});
		
		if (!exists)
		{
			if ($( '.selected-values' ).length>0 && !can_select_multiple)
			{
				$( '.selected-values' ).each(function()
				{
					$( this ).parent().remove();
				});
			}
			$( '#selection-list' ).append($(templateReplace( $( "#stringlist_template_two" ).html() , { VALUE_START : newdata.label, ID : -1, VALUE_ID : newdata.id })));
		}
	}
	catch (err) {}
}

function removeTaxonTrait( caller )
{
	if ($( '.selected-values' ).length<=1 && !can_be_null) return;
	$( caller ).parent().remove();
}

function saveTaxonTrait()
{
	var form=$( '<form method="POST"></form>');
	form.append( '<input type="hidden" name="action" value="save" />' );
	form.append( '<input type="hidden" name="id" value="'+concept+'" />' );
	form.append( '<input type="hidden" name="group" value="'+group+'" />' );
	form.append( '<input type="hidden" name="trait" value="'+trait+'" />' );

	$( '.selected-values' ).each(function()
	{
		form.append( '<input type="hidden" name="values[]" value="'+encodeURIComponent($(this).attr( 'data' ))+'" />' );
	});

	$('body').append(form);
	form.submit();
}

function __stringlist( data )
{
	var selected=$('<p>');

	if ( data.taxon_values && data.taxon_values.values ) 
	{
		for( var i=0;i<data.taxon_values.values.length;i++ )
		{
			selected.append( $(templateReplace( $( "#stringlist_template_two" ).html() , data.taxon_values.values[i] )));
		}
	}
	
	var values=$('<p>');

	for( var i=0;i<data.trait.values.length;i++ )
	{
		values.append( $(templateReplace( $( "#stringlist_template_three" ).html() , data.trait.values[i] )));
	}
	
	return templateReplace( $( "#stringlist_template_one" ).html() , { SELECTED : selected.html(), VALUES : values.html() } );
}


function __stringfree( data )
{

	console.dir( data );

	var selected=$('<p>');

	if ( data.taxon_values && data.taxon_values.values ) 
	{
		for( var i=0;i<data.taxon_values.values.length;i++ )
		{
//			selected.append( $(templateReplace( $( "#stringfree_template" ).html() , data.taxon_values.values[i] )));
		}
	}
	
//	return templateReplace( $( "#stringlist_template_one" ).html() , { SELECTED : selected.html(), VALUES : values.html() } );
}


function taxonTraitForm( data )
{
	setTrait( data.trait.id );
	can_select_multiple=(data.trait.can_select_multiple==1);
	can_be_null=(data.trait.can_be_null==1);
	
	if ( data.trait.type_sysname=='stringlist' )
	{
		return __stringlist( data );
	}
	if ( data.trait.type_sysname=='stringfree' )
	{
		return __stringfree( data );
	}
	

	


}

function editTaxonTrait( data )
{
	data.time=allGetTimestamp();
	data.action='get_taxon_trait';

	$.ajax({
		url: "ajax_taxon.php",
		data: data,
		success : function ( data )
		{
			prettyDialog(
			{ 
				title : "Edit", 
				content : taxonTraitForm($.parseJSON(data)) , 
				buttons :
				{
					"save" : { text:'Save', click:function() { saveTaxonTrait(); } },
					"cancel" : { text:'Cancel', click:function() { $( this ).dialog( "close" ); } } }
				}
			);

			$( "#dialog-message-body-content" ).css( "font-family" , $( "body" ).css( "font-family" ) ).css( "font-size" , "0.9em" );
		}
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
              ["can_select_multiple"]=>
              ["can_include_comment"]=>
              ["can_be_null"]=>
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
<li>%VALUE_START% <a href="#" class="edit selected-values" style="padding:5px" data='{ "id": %ID% , "value_id": %VALUE_ID% }' onclick="removeTaxonTrait( this );return false;">X</a></li>
</script>

<script id="stringlist_template_three" language="text">
<li>%STRING_VALUE% <a href="#" class="edit" style="padding:5px" data='{ "id": %ID% , "label": "%STRING_VALUE%" }' onclick="addTaxonTrait( this );return false;">&rarr;</a></li>
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
				var data=JSON.parse($(this).attr('data'));
				editTaxonTrait( data );
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
