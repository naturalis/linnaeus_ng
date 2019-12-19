{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $allowed}
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" id="action" name="action" value="save" />
<input name="id" id="id" type="hidden" value="{$data.id}"  />
<input name="org_parent_id" id="org_parent_id" type="hidden" value="{$data.parent_id}"  />

{if $projectRanks[$data.rank_id].lower_taxon==1}
{assign var=isHigherTaxa value=false}
{else}
{assign var=isHigherTaxa value=true}
{/if}

{function branch level=0}
    {foreach $data as $k => $v}
    {* if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa) *}
	<option rank_id="{$v.rank_id}" root_rank_id="{$v.root_rank_id}" name="{$v.taxon}" value="{$v.id}" {if $current==$v.id}selected="selected"{/if} >
	{'&nbsp;&nbsp;&nbsp;'|str_repeat:$level}{$v.taxon_formatted}
    </option>
	{if $v.children}
    {branch data=$v.children level=$level+1 current=$current}
	{/if}
    {* /if *}
    {/foreach}
{/function}


{if $data.id}<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$data.id}','_top')" />{/if}
<table>
	<tr>
		<td>
			{t}Parent taxon: {/t}
		</td>
		<td>
            <select name="parent_id" id="parent-id" style="width:300px">
                <option value="-1">{t}No parent{/t}</option>
                <option disabled="disabled"></option>
                {branch data=$taxa current=$data.parent_id}
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
			<select name="rank_id" id="rank-id" onchange="taxonGetFormattedPreview()">
			{foreach item=v from=$projectRanks}
				{if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa && $v.lower_taxon==1)}
				<option value="{$v.id}" {if $data.rank_id==$v.id}selected="selected"{/if}>{$v.rank}</option>
				{/if }
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
    	<td>Formatted example:</td><td id="formatted-example"></td><td></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="{t}save{/t}" />
			<input type="button" value="{t}delete{/t}" onclick="$('#theForm').attr('action','delete.php').submit();" />
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
taxonGetFormattedPreview();
{foreach item=v from=$projectRanks}
{if $v.can_hybrid}
taxonCanHaveHybrid[taxonCanHaveHybrid.length]={$v.id};
{/if}
{/foreach}

allLookupNavigateOverrideUrl('edit.php?id=%s');


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

{* literal}
<script>
function bla() {

	var b = $('#parent-id :selected').text();
	b = b.trim();
	
	$('#parent-id > option').each(function(i){
		var s = $(this).text();
		if (s.trim()==b) $(this).text(s+' <-----------------------------');
	});
	
}
</script>
{/literal *}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}