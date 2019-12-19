{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>
<p>

<b>Related</b><br />
{foreach from=$related.relations item=v key=k}
<a href="{if $v.ref_type=='variation'}variation.php{else}taxon.php{/if}?id={$v.relation_id}">{$v.label}</a><br />
{/foreach}
{if $related.relations|@count==0}
(none)
{/if}
</p>
{if $useVariations}
<p>
<b>Variation relations</b><br />
{foreach from=$related.variations item=v}
<i>{$v.label}:</i><br/>
{foreach from=$v.relations item=r}
&nbsp;&nbsp;<a href="{if $r.ref_type=='variation'}variations.php?var={else}taxon.php?id={/if}{$r.relation_id}">{$r.label}</a><br />
{/foreach}
{if $v.relations|@count==0}
(none)
{/if}
{/foreach}
{if $related.variations|@count==0}
(none)
{/if}
</p>
{/if}
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('related.php?id=%s');
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}