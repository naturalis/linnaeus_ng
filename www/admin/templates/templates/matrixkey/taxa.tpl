{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" id="action" value="" />
<table>
	<tr>
		<td>
			{t}Taxon name:{/t}
		</td>
		<td>
	<select name="parent_id" id="parent-id" onchange="taxonGetRankByParent()">
	{foreach from=$taxa key=k item=val}
		<option value="{$taxa[i].id}">
		{section name=foo loop=$val.level-$taxa[0].level}
		&nbsp;
		{/section}		
		{$val.taxon}</option>
	{/foreach}
	</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>
<table>
	<tr>
		<td colspan="2">
			<input type="button" onclick="$('#action').val('');$('#theForm').submit();" value="{t}save and return to matrix{/t}" />&nbsp;
			<input type="button" onclick="$('#action').val('repeat');$('#theForm').submit();" value="{t _s1=$characteristic.characteristic}save and add another taxon{/t}" />
			<input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_top')" />
		</td>
	</tr>
</table>
</form>
</div>


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}