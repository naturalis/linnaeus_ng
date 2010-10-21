{include file="../shared/admin-header.tpl"}

<div id="page-main">
You can assign parts of the taxon tree to specific collaborator. If assigned, collaborators can only edit the assigned taxon, and all taxa beneath it in the taxon tree. If a collaborator has no taxa assigned to him, he can edit no taxa.<br/>
You can assign multiple taxa to the same collaborator. However, if you assign differente taxa that appear in the same branch of the taxon tree, the taxa highest up the same branch takes precedent.<br /><br />

<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="delete" id="delete" value="" />
<table>
	<tr style="vertical-align:top">
		<td>
			Assign taxon
		</td>
		<td>
	<select name="taxon_id" style="width:200px">
	{section name=i loop=$taxa}
	<option value="{$taxa[i].id}">
	{section name=foo loop=$taxa[i].level}
	&nbsp;
	{/section}		
	{$taxa[i].taxon}</option>
	{/section}
	</select>
		</td>
		<td>
			to user
		</td>
		<td>
	<select name="user_id" style="width:200px">
	{section name=i loop=$users}
	<option value="{$users[i].id}">
	{$users[i].first_name} {$users[i].last_name} ({$users[i].role})</option>
	{/section}
	</select>
		</td>
		<td>
			[<span onclick="$('#theForm').submit()" class="pseudo-a">save</span>]
		</td>
	</tr>
</table>

<br />
Current assignments:
<br />

<table style="border-collapse:collapse">
<tr><th style="width:250px">Taxon</th><th></th><th style="width:250px">Collaborator</th><th></th></tr>
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
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}