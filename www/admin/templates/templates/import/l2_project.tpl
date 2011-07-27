{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" id="theForm">
<input type="hidden" name="clear" id="clear" value="">
{if $project.id}
Target roject: <b>{$project.title}</b> (<span class="pseudo-a" onclick="$('#clear').val('project');$('#theForm').submit()">change</span>)<br/>
<p>
<input type="button" value="analyze data" onclick="window.open('l2_analyze.php','_self');" />
</p>
{else}
Create all new project or use existing?
<p>
<select name="project">
<option value="-1">create new project</option>
{foreach from=$projects item=v}
<option value="{$v.id}">use "{$v.title}"</option>
{/foreach}
</select>
</p>
<p>
<input type="submit" value="go" />
</p>
{/if}
</form>
<p>
<a href="linnaeus2.php">back</a>
</p>
</div>

{include file="../shared/admin-footer.tpl"}