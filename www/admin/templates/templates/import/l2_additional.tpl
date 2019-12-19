{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true || $modules|@count==0}
<p>
Data import is complete. You have been added as system administrator to the new project. In that capacity you can finish configuring the project by adding modules, creating users etc.<br />
(project id: {$projectId})<br /><br />
<a href="l2_additional.php?action=errorlog">Download import error log</a>
</p>
<p>
<a href="go_new_project.php">Go to project index</a>
</p>
{else}
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<table>
<tr><td colspan="2"><b>Custom modules</b></td></tr>
{foreach from=$modules item=v}
<tr>
	<td><label><input type="checkbox" name="modules[{$v}]" checked="checked">&nbsp;import module "{$v}"</label></td>
	<td>internal name: <input type="text" value="{$v}" name="modules-name[{$v}]" /></td>
</tr>
{/foreach}
</table>
"internal name" is the name as used in internal links; it is case-sensitive, so please make sure (by checking in the XML, for instance)!
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}

