{include file="../shared/admin-header.tpl"}

<div id="page-main">


<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>



{if $variations|@count==0}
{t}No variations have been defined for this taxon.{/t}
{else}
<table>
	<tr>
		<th style="width:350px;">{t}variation{/t}</td>
		<th>delete</td>
	</tr>
	{foreach from=$variations item=v}
	<tr id="var-row-{$v.id}" class="tr-highlight">
		<td>{$v.label}</td>
			<td
			style="text-align:center;paddin:0px 2px 0px 2px" 
			class="a" 
			onclick="taxonDeleteVariation({$v.id},'{$v.label|@escape}');">
			x
		</td>
	</tr>
	{/foreach}
</table>
{/if}
<hr style="color:#eee;height:1px" />
<form method="post" action="" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
<tr><td colspan="2">{t}Add a new variation:{/t}</td><td><input type="text" name="variation" maxlength="128" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><input type="submit" value="{t}save{/t}" /></td></tr>
</table>
</form>
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('variations.php?id=%s');
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}