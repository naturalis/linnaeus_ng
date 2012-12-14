{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" id="action" name="action" value="save" />
<input type="hidden" id="next" name="next" value="new" />
<table>
	<tr>
		<td>
			{t}Parent taxon: {/t}
		</td>
		<td>				
			{assign var=prev value=null}
			{assign var=prevLevel value=-1}
			<select name="parent_id" id="parent-id" onchange="taxonGetRankByParent()" style="width:300px">
			{if $taxa|@count==0 || $data.parent_id==''}
			<option value="-1">{t}No parent{/t}</option>
			{/if}
			{foreach from=$taxa key=k item=v}
			{if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa)}
				<option rank_id="{$v.rank_id}" name="{$v.taxon}" value="{$v.id}" {if $data.parent_id==$v.id}selected="selected"{/if} >
				{section name=foo loop=$v.level-$taxa[0].level}
				&nbsp;
				{/section}		
				{$v.taxon_formatted}
				</option>
			{/if}
			{if $prevLevel!=$v.level}
			{assign var=prev value=$v.id}
			{/if}
			{assign var=prevLevel value=$v.level}
			{/foreach}
			</select>
		</td>
		<td>
			<span id="rank-message" class=""></span> 
		</td>
	</tr>
	<tr>
		<td>
			{t}Rank:{/t}
		</td>
		<td colspan="2">
			<select name="rank_id" id="rank-id" onchange="taxonChangeSubmitButtonLabel()">
			{foreach item=v from=$projectRanks}
				{if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa && $v.lower_taxon==1)}
				<option xxxx value="{$v.id}" ideal_parent_id="{$v.ideal_parent_id}" {if $data.rank_id==$v.id}selected="selected"{/if}>
				{$v.rank}
				</option>
				{/if }
			{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:100px">
			{t}Taxon name:{/t}
		</td>
		<td>
			<input type="text" name="taxon" id="taxon-name" onkeyup="taxonGetFormattedPreview()" onblur="taxonCheckNewTaxonName()" value="{$data.taxon}"  style="width:300px"/>
		</td>
		<td>
			<span id="taxon-message" class=""></span>
		</td>
	</tr>

	<tr>
		<td>
		Formatted example:
		</td>
		<td id="formatted-example">
		</td>
		<td>
		</td>
	</tr>
	
	
	<tr>
		<td style="width:100px">
			{t}Author:{/t}
		</td>
		<td>
			<input type="text" name="author" id="author" value="{$data.author}" style="width:300px"/>
		</td>
		<td>
			<span id="taxon-message" class=""></span>
		</td>
	</tr>

{if $session.admin.project.includes_hybrids==1}
	<tr>
		<td>
			{t}This is a hybrid:{/t}
		</td>
		<td>
			<input type="checkbox" name="is_hybrid" id="hybrid" {if $data.is_hybrid=='on' || $data.is_hybrid=='1'}checked="checked"{/if} onchange="taxonCheckHybridCheck()" />
		</td>
		<td>
			<span id="hybrid-message" class=""></span>
		</td>
	</tr>
{/if}
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="{t}save and create another{/t}" />
			<input type="button" value="{t}save and go to main taxon page{/t}" onclick="$('#next').val('main');$('#theForm').submit();" />
		</td>
	</tr>
</table>
</form>
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
allLookupNavigateOverrideUrl('edit.php?id=%s');
//taxonGetRankByParent(true);
//taxonCheckNewTaxonName();
//taxonGetRankByParent();
//taxonCheckHybridCheck();
taxonChangeSubmitButtonLabel();
{if $isHigherTaxa}
taxonHigherTaxa = true;
{/if}
taxonSubGenusRankId = {$rankIdSubgenus};
{assign var=prev value=null}			
{foreach from=$taxa key=k item=v}
{if ($v.lower_taxon==1 && $prev!==null)}
taxonStoreCopyableTaxa('{$prev}');
{/if}
{assign var=prev value=$v.id}
{/foreach}
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}