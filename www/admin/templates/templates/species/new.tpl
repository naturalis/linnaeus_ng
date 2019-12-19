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

            {function branch level=0}
                {foreach $data as $k => $v}
                {* if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa) *}
                <option rank_id="{$v.rank_id}" root_rank_id="{$v.root_rank_id}" name="{$v.taxon}" value="{$v.id}" {if $current==$v.id}selected="selected"{/if} >
                {'&nbsp;&nbsp;&nbsp;'|str_repeat:$level}{$v.taxon_formatted}
                </option>
                {if $v.children}
                {branch data=$v.children level=$level+1}
                {/if}
                {* /if *}
                {/foreach}
            {/function}

			<select name="parent_id" id="parent-id" onchange="taxonGetRankByParent();taxonBlankOutRanks();" style="width:300px">
                <option value="-1">{t}No parent{/t}</option>
                <option disabled="disabled"></option>
                {branch data=$taxa}
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
			<select name="rank_id" id="rank-id" onchange="taxonChangeSubmitButtonLabel();taxonGetFormattedPreview();">
			{assign var=firstLower value=true}
			{foreach item=v from=$projectRanks}
				<option value="{$v.id}" root_rank_id="{$v.rank_id}" ideal_parent_id="{$v.ideal_parent_id}" {if $data.rank_id==$v.id}selected="selected"{/if}>{$v.rank}</option>
			{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>
			{t}Taxon name:{/t}
		</td>
		<td>
			<input type="text" name="taxon" id="taxon-name" onkeyup="taxonGetFormattedPreview()" value="{$data.taxon}"  style="width:300px"/>
		</td>
		<td>
			<span id="taxon-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>
			{t}Hybrid:{/t}
		</td>
		<td colspan="2">
			<select name="is_hybrid" id="is-hybrid" onchange="taxonGetFormattedPreview()">
				<option value="0" {if $data.is_hybrid==0}selected="selected"{/if}>{t}no hybrid{/t}</option>
				<option value="1" {if $data.is_hybrid==1}selected="selected"{/if}>{t}interspecific hybrid{/t}</option>
				<option value="2" {if $data.is_hybrid==2}selected="selected"{/if}>{t}intergeneric hybrid{/t}</option>
			</select>
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
		<td>
			{t}Author:{/t}
		</td>
		<td>
			<input type="text" name="author" id="author" value="{$data.author}" style="width:300px"/>
		</td>
		<td>
			<span id="taxon-message" class=""></span>
		</td>
	</tr>

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
//taxonGetRankByParent();
//taxonCheckHybridCheck();
taxonChangeSubmitButtonLabel();
{if $isHigherTaxa}
taxonHigherTaxa = true;
{/if}
{if $rankIdSubgenus}
taxonSubGenusRankId = {$rankIdSubgenus};
{/if}
{assign var=prev value=null}			
{foreach from=$taxa key=k item=v}
{if ($v.lower_taxon==1 && $prev!==null)}
taxonStoreCopyableTaxa('{$prev}');
{/if}
{assign var=prev value=$v.id}
{/foreach}
//taxonGetRankByParent();
//taxonBlankOutRanks();


{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}