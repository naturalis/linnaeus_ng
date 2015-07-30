{include file="../shared/admin-header.tpl"}

<script>
var area='<textarea style="font-family:inherit;font-size:inherit;width:100%;height:100%;" data-id="%ID%" data-old="%OLD%">%OLD%</textarea><input type="button" value="save" onclick="saveInfo(%ID%);">';

var inpt='<input type="text" data-id="%ID%" data-old="%OLD%" value="%OLD%" style="width:130px;"><input type="button" value="save" onclick="saveValue(%ID%);">';


function updateField(ele,html,field)
{
	unbindDblClick();
	$(ele).html( html.replace(/%ID%/g,$(ele).attr( 'id' ).replace(field+"-","" )).replace(/%OLD%/g,$(ele).html()) );
}

function updateInfo(ele)
{
	updateField(ele,area,"info");
}

function updateValue(ele)
{
	updateField(ele,inpt,"value");
}

function saveField( id,ele,field )
{
	var a=ele;
	var b="#"+field+"-"+id;
	
	if ( a.val() != a.attr( 'data-old' ) )
	{
		$.ajax({
			url : 'ajax_interface.php',
			type: 'POST',
			data : ({
				action : "update_"+field ,
				id : id,
				value : a.val(),
				time : allGetTimestamp(),
			}),
			success : function ( data )
			{
				$( b ).html( a.val() );
				$( b ).html( $( b ).html(  ) + '<span style="color:black;margin-left:3px;" class="message">'+data+'</span>' );
				$( '.message' ).fadeOut(1500,null,function() { $( '.message' ).remove(); } );
				bindDblClick();
			}
		});

	}
	else
	{
		$( b ).html( a.attr( 'data-old' ) );
	}
}

function saveInfo( id )
{
	var a=$( "textarea[data-id="+id+"]" );
	saveField( id, a, "info" );
}

function saveValue( id )
{
	var a=$( "input[data-id="+id+"]" );
	saveField( id, a,"value" );
}

function bindDblClick()
{
	 $('td.setting-info').on('dblclick', function() { updateInfo(this); } );
	 $('td.setting-default').on('dblclick', function() { updateValue(this); } );
}

function unbindDblClick()
{
	 $('td.setting-info').unbind('dblclick');
	 $('td.setting-default').unbind('dblclick');
}

</script>

<style>
table tr {
	vertical-align:top;
}
table td {
	border-bottom:1px dotted #bbb;
}
table th {
	background-color:#eee;
}
table tr td.setting-name, table tr th.setting-name {
	text-align:right;
	font-weight:bold;
}
table tr td.setting-info {
	color:#666;
}
input[type=text] {
	width:200px;
}
</style>


<div id="page-main">

<h3>module settings for "{$module.module}"</h3>

<form id="theForm" method="post">
<input type="hidden" id="action" name="action" value="save" />
<input type="hidden" name="id" value="{$module.id}" />
<input type="hidden" id="setting_id" name="setting_id" value="" />

<table>
	<tr class="tr-highlight" style="vertical-align:bottom">
    	<th class="setting-name">setting</th>
    	<th class="setting-info">info</th>
    	<th class="setting-default">default</th>
    	<th class="setting-values"></th>
    	<!-- th class="setting-allow">allow&nbspfree?</th -->
    	<th class="setting-delete"></th>
	</tr>
{foreach $settings v}
	<tr class="tr-highlight">
    	<td class="setting-name">{$v.setting}</td>
    	<td class="setting-info" data-id="{$v.id}" id="info-{$v.id}">{$v.info}</td>
    	<td class="setting-default" data-id="{$v.id}" id="value-{$v.id}">{$v.default_value}</td>
    	<td class="setting-delete"><a href="#" onclick="
        	if (confirm('are you sure?'))
            {	
            	$('#action').val('delete');
            	$('#setting_id').val( {$v.id} );
	            $('#theForm').submit();
			}
		">delete</a></td>
	</tr>
{/foreach}
{if $settings|@count==0}
	<tr class="tr-highlight">
    	<td class="setting-name">(none)</td>
	</tr>
{/if}
</table>

<p>
    new setting:<br />
    <input type="text" value="" name="new_setting" placeholder="name" /><br />
    <textarea name="new_info" style="font-family:inherit;font-size:inherit;width:300px;height:150px;margin:3px 0 3px 0;" placeholder="description (optional)" ></textarea><br />
    <input type="text" value="" name="new_default_value" placeholder="default value (optional)" />
</p>
<p>
	<input type="submit" value="save" />
</p>

</form>

<a href="values.php?id={$module.id}">values</a> | <a href="index.php">index</a>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
	bindDblClick();
});
</script>

{include file="../shared/admin-footer.tpl"}
