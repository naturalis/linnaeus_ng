{include file="../shared/admin-header.tpl"}
{literal}
<script>
function taxonCheckNewTaxonName() {

	if ($('#taxon-name').val().length==0) {
		$('#taxon-message').html('')
		return;
	}

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'check_taxon_name' ,
			'taxon_name' : $('#taxon-name').val() ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				$('#taxon-message').removeClass().addClass('message-no-error');
				$('#taxon-message').html('Ok')
			} else {
				$('#taxon-message').removeClass().addClass('message-error');
				$('#taxon-message').html(data)
			}
		}
	});




}
</script>
{/literal}
<div id="page-main">
<form id="theForm" method="post" action="">
<p>
Taxon name: <input type="text" name="taxon" id="taxon-name" onblur="taxonCheckNewTaxonName()" />&nbsp;<span id="taxon-message" class=""></span>
</p>
<p>
Parent taxon: 
<select name="parent_id">
<option value="-1">No parent</option>
<option value="-1" disabled="disabled"></option>
{section name=i loop=$taxa}
<option value="{$taxa[i].id}">{$taxa[i].list_padding|replace:'<%pad%>':'..'} {$taxa[i].taxon}</option>
{/section}
</select>
</p>
<input type="submit" value="save" />&nbsp;<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}