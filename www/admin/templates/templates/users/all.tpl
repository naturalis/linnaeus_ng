{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
<tr>

	<th class="th-userlist-left-align" style="text-align:left">first name</th>
	<th class="th-userlist-left-align" style="text-align:left">last name</th>
	<th class="th-userlist-left-align" style="text-align:left">username</th>
	<th class="th-userlist-left-align" style="text-align:left">e-mail</th>
	<th class="th-userlist-left-align" style="text-align:right;">in<br />projects</th>
	{if $session.admin.project.id}
	<th class="th-userlist-left-align" style="text-align:left;">current<br />project</th>
	{/if}	
	<!-- th class="th-userlist-left-align" style="text-align:left">last login</th -->
	<th class="th-userlist-left-align" style="text-align:left">detail</th>
</tr>
{foreach item=v from=$users}
<tr class="tr-highlight">
	<td class="cell-userlist-left-align">{$v.first_name}</td>
	<td class="cell-userlist-left-align">{$v.last_name}</td>
	<td class="cell-userlist-left-align">{$v.username}</td>
	<td class="cell-userlist-left-align">{$v.email_address}</td>
	<td class="cell-userlist-right-align" style="padding-right:20px">{if $userProjectCount[$v.id].total}{$userProjectCount[$v.id].total}{else}0{/if}</td>
	{if $session.admin.project.id}
	{if $currentProjectUsers[$v.id]==''}
	<td>[<span class="a" onclick="userAddToProject({$v.id});">{t}add{/t}</span>]</td>
	{else}
	<td>[<span class="a" onclick="userRemoveFromProject({$v.id});">{t}remove{/t}</span>]</td>
	{/if}
	{/if}
	<!-- td class="cell-userlist-left-align">{$v.last_login}</td-->
	<td>[<a href="view.php?id={$v.id}">{t}view{/t}</a>]</td>
	{* <td>[<a href="edit.php?id={$v.id}">{t}edit{/t}</a>]</td> *}
</tr>
{/foreach}
</table>

{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<a href="?start={$prevStart}">< previous</span>&nbsp;&nbsp;
		{/if}
		{if $nextStart!=-1}
		<a href="?start={$nextStart}">next ></span>
		{/if}
	</div>
{/if}

</div>

<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>

<form method="post" action="edit.php" name="deleteForm" id="deleteForm">
<input name="id" id="id" value="-1" type="hidden" />
<input name="delete" id="delete" value="1" type="hidden" />
</form>

{include file="../shared/admin-footer.tpl"}
