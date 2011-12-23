{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true}
<p>
Data import is complete. You have been added as system administrator to the new project. In that capacity you can finish configuring the project by adding modules, creating users etc.<br />
(project id: {$projectId})
</p>
<p>
<a href="go_new_project.php">Go to project index</a>
</p>
{else}
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Map data</b><br/>
<label>Import map items?&nbsp;&nbsp;<input type="checkbox" name="map_items" checked="checked"></label>
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}