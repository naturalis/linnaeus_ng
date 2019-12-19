{include file="../shared/admin-header.tpl"}

<div id="page-main">


<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>

{if $data}
<i>{$taxon.taxon}</i>
<table>
{foreach from=$data item=v}
<tr><td>&nbsp;{$v.name}</td><td>{$v.value}</td></tr>
{/foreach}
</table>
{/if}

{foreach from=$varData item=v}
<i>{$v.label}</i>
<table>
{foreach from=$v.data item=r}
<tr><td>&nbsp;{$r.name}</td><td>
{if $r.name|@strpos:'url_'===0}
<a href="{$r.value}" target="_new">{$r.value}</a>
{else}
{$r.value}
{/if}
</td></tr>
{/foreach}
</table><br />
{/foreach}
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('nbc_extras.php?id=%s');
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}