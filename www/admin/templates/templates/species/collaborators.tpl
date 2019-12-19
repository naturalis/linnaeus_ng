{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t}You can assign parts of the taxon tree to specific collaborator. If assigned, collaborators can only edit the assigned taxon, and all taxa beneath it in the taxon tree. If a collaborator has no taxa assigned to him, he can edit no taxa.{/t}<br/>
{t}You can assign multiple taxa to the same collaborator. However, if you assign different taxa that appear in the same branch of the taxon tree, the taxa highest up the same branch takes precedent.{/t}<br /><br />

{if $taxa|@count > 0}
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="delete" id="delete" value="" />
<table>
	<tr style="vertical-align:top">
		<td>
			{t}Assign taxon{/t}
		</td>
		<td>
	<select name="taxon_id" style="width:auto">
	{foreach from=$taxa key=k item=v}
	<option value="{$v.id}">
	{section name=foo loop=$v.level}
	&nbsp;
	{/section}		
	{$v.taxon}</option>
	{/foreach}
	</select>
		</td>
		<td>
			{t}to user{/t}
		</td>
		<td>
	<select name="user_id" style="width:auto">
	{section name=i loop=$users}
	<option value="{$users[i].id}">
	{$users[i].first_name} {$users[i].last_name} ({$users[i].role})</option>
	{/section}
	</select>
		</td>
		<td>
			[<span onclick="$('#theForm').submit()" class="a">{t}save{/t}</span>]
		</td>
	</tr>
</table>

<br />
{t}Current assignments:{/t}
<br />

<table style="border-collapse:collapse">
<tr><th style="width:250px">{t}Taxon{/t}</th><th></th><th style="width:250px">{t}Collaborator{/t}</th><th></th></tr>
{section name=i loop=$usersTaxa}
<tr class="tr-highlight"><td>{$usersTaxa[i].taxon.taxon}</td>
<td>&rarr;</td>
{section name=j loop=$users}
{if $users[j].id == $usersTaxa[i].user_id}
<td>
{$users[j].first_name} {$users[j].last_name} ({$users[j].role})
</td>
{/if}
{/section}
<td>
	<span class="general-clickable-image" onclick="$('#delete').val({$usersTaxa[i].id});$('#theForm').submit()"><img src="../../media/system/icons/cross.png" /></span>
</td>
</tr>
{/section}
</table>
</form>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}