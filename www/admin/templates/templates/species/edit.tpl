{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $allowed}
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" id="action" name="action" value="save" />
<input name="id" id="id" type="hidden" value="{$data.id}"  />
<input name="org_parent_id" id="org_parent_id" type="hidden" value="{$data.parent_id}"  />
{if $data.id}<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$data.id}','_top')" />{/if}
<table>
	<tr>
		<td>
			{t}Parent taxon: {/t}
		</td>
		<td>		
	<select name="parent_id" id="parent-id" onchange="taxonGetRankByParent()">
	{if $taxa|@count==0 || $data.parent_id==''}
	<option value="-1">{t}No parent{/t}</option>
	{/if}
	{foreach from=$taxa key=k item=v}
	{if ($isHigherTaxa && $v.lower_taxon==0) || (!$isHigherTaxa)}
		<option value="{$v.id}" {if $data.parent_id==$v.id}selected="selected"{/if}>
		{section name=foo loop=$v.level-$taxa[0].level}
		&nbsp;
		{/section}		
		{$v.taxon}</option>
	{/if}
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
			<select name="rank_id" id="rank-id">
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
			<input type="text" name="taxon" id="taxon-name" onkeyup="taxonRegisterManualInput()" value="{$data.taxon}"  style="width:300px"/>
		</td>
		<td>
			<span id="taxon-message" class=""></span>
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
	
	{if $session.admin.project.includes_hybrids==1}	<tr>
		<td>
			{t}This is a hybrid:{/t}
		</td>
		<td>
			<input type="checkbox" name="is_hybrid" id="hybrid" {if $data.is_hybrid=='on' || $data.is_hybrid=='1'}checked="checked"{/if} onchange="taxonCheckHybridCheck()" style="width:300px" />
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
			<input type="submit" value="{t}save{/t}" />
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
{foreach item=v from=$projectRanks}
{if $v.can_hybrid}
taxonCanHaveHybrid[taxonCanHaveHybrid.length]={$v.id};
{/if}
{/foreach}

allSetHeartbeatFreq({$heartbeatFrequency});
taxonSetHeartbeat(
	'{$session.admin.user.id}',
	'{$session.admin.system.active_page.appName}',
	'{$session.admin.system.active_page.controllerBaseName}',
	'{$session.admin.system.active_page.viewName}',
	'{$taxon.id}'
);

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


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}