{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $allowed}
<form id="theForm" method="post" action="">
<input name="id" id="id" type="hidden" value="{$data.id}"  />
<table>
	<tr>
		<td>
			Taxon name:
		</td>
		<td>
			<input type="text" name="taxon" id="taxon-name" onblur="taxonCheckNewTaxonName()" value="{$data.taxon}" />
		</td>
		<td>
			<span id="taxon-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>
			This is a hybrid:
		</td>
		<td>
			<input type="checkbox" name="is_hybrid" id="hybrid" {if $data.is_hybrid=='on' || $data.is_hybrid=='1'}checked="checked"{/if} onchange="taxonCheckHybridCheck()" />
		</td>
		<td>
			<span id="hybrid-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>
			Parent taxon: 
		</td>
		<td>
	<select name="parent_id" id="parent-id" onchange="taxonGetRankByParent()">
	{if $taxa|@count==0}
	<option value="-1">No parent</option>
	{/if}
	{section name=i loop=$taxa}
	<option value="{$taxa[i].id}" {if $data.parent_id==$taxa[i].id}selected="selected"{/if}>
	{section name=foo loop=$taxa[i].level-$taxa[0].level}
	&nbsp;
	{/section}		
	{$taxa[i].taxon}</option>
	{/section}
	</select>
		</td>
		<td>
			<span id="rank-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>
			Rank: 
		</td>
		<td colspan="2">
			<select name="rank_id" id="rank-id">
{section name=i loop=$projectRanks}
<option value="{$projectRanks[i].id}" {if $data.rank_id==$projectRanks[i].id}selected="selected"{/if}>{$projectRanks[i].rank}</option>
{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="save" />&nbsp;<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>
{/if}
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{section name=i loop=$projectRanks}
{if $projectRanks[i].can_hybrid}
taxonCanHaveHybrid[taxonCanHaveHybrid.length]={$projectRanks[i].id};
{/if}
{/section}
taxonGetRankByParent(true);
//taxonCheckNewTaxonName();
//taxonGetRankByParent();
//taxonCheckHybridCheck();
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}