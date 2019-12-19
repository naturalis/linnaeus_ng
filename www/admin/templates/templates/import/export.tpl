{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post">
<input type="hidden" name="action" value="export" />
<input type="hidden" name="rnd" value="{$rnd}" />

{t}Export project data to XML-file.{/t}
<table>
{foreach from=$modules.modules item=v}
{if $v.controller!='utilities' && $v.controller!='index' && $v.controller!='highertaxa'}
<tr>
	<td><input type="checkbox" name="modules[]" value="{$v.controller}" id="{$v.controller}" checked="checked" /></td>
	<td><label for="{$v.controller}">{$v.module}</label></td>
</tr>
{/if}
{/foreach}

{foreach from=$modules.freeModules item=v}
<tr><td><input type="checkbox" name="freeModules[]" value="{$v.id}" id="fm{$v.id}" checked="checked" /></td><td><label for="fm{$v.id}">{$v.module}</label></td></tr>
{/foreach}
</table>
<input type="submit" value="{t}export{/t}" />

</form>
<p>
{t}Images and other media files should be copied by hand, and are referenced in the export file by filename only. They can be found in the server folder:<br />{/t}
{$session.admin.project.paths.project_media}
</p>
</div>

{include file="../shared/admin-footer.tpl"}