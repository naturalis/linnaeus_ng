{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" name="theForm">
{if $projects}
<p>
{t}Select the project you wish to delete.{/t}
</p>
<p>
<select name="p">
<option value=""></option>
{foreach from=$projects item=v}
<option value="{$v.id}">{if $v.title!=''}{$v.title}{else}[untitled]{/if}</option>
{/foreach}
</select>
</p>
<p>
<input type="submit" value="{t}select{/t}" />
<input type="button" value="{t}back{/t}" onclick="window.open('../users/choose_project.php','_self')" />
</p>
{elseif $project}
<p>
You have selected the project "{$project.title}" for deletion.<br />
<span class="message-error">Deletion cannot be undone. All data will be lost.</span><br />
Are you sure you wish to continue?
</p>
<p>
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="{$project.id}" />
<input type="button" value="Cancel delete" onclick="window.open('../users/choose_project.php','_self')" />
<input type="submit" value="Delete ''{$project.title}''"/>
</p>
{else}

<a href="../users/choose_project.php">Back</a>

{/if}
</form>
</div>
{include file="../shared/admin-footer.tpl"}
