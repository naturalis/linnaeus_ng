{include file="../shared/admin-header.tpl"}
<div id="page-main">
<form method="post" id="theForm" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="copy" />
<input type="hidden" name="source" value="{$taxon.id}" />

{t _s1=$taxon.taxon}Choose the species you want to copy the map data of "%s" to:{/t}
<p>
	<select name="target" id="target">
	{foreach from=$taxa key=k item=v}
	{if $v.id!=$taxon.id}
		<option value="{$v.id}">{$v.taxon}{if $occurringTaxa[$v.id]} (&#149;){/if}</option>
	{/if}
	{/foreach}
	</select>
</p>
<p>
	Species marked (&#149;) currently already have map data. This data will remain unaffected when you copy data to one of these species.
</p>
<p>
<input type="button" value="{t}copy data{/t}" onclick="mapDoCopyForm('{$taxon.taxon}')" />
</p>
</form>

</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
