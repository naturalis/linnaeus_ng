{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post">
{if $processed==true}
<p>
Data merge is complete.
</p>
<p>
<a href="../../admin/admin-index.php">Go to project index</a>
</p>
{elseif $merge}
<p>
{t _s1=$merge.title _s2=$current}You are about to merge the project "%s" into "%s".{/t}
</p>
<p>
Select the modules you wish to merge:
</p>
<p>
<table>
{foreach from=$modules.modules item=v}
{if !in_array($v.module_id,$modulesToIgnore)}
<tr class="tr-highlight" style="vertical-align:top"><td><label style="cursor:pointer"><input type="checkbox" name="modules[]" value="{$v.module_id}" checked="checked"/>{$v.module}</label></td><td><span style="color:#999">{$moduleInfo[$v.module_id]}</span></td></tr>
{/if}
{/foreach}
{foreach from=$modules.freeModules item=v}
<tr class="tr-highlight" style="vertical-align:top"><td><label style="cursor:pointer"><input type="checkbox" name="freeModules[]" value="{$v.module_id}" checked="checked"/>{$v.module}</label></td><td><span style="color:#999">{$moduleInfo.free}</span></td></tr>
{/foreach}
</table>

</p>
<p>
Other modules, like the index, are dynamically generated.
</p>

{t}Do you wish to continue?{/t}
</p>
<p>
<input type="hidden" name="action" value="merge">
<input type="hidden" name="id" value="{$merge.id}">
<input type="submit" value="{t}merge{/t}" />
<input type="button" value="{t}cancel{/t}" onclick="window.open('merge.php','_self');" />
</p>
{else}
<p>
{t _s1=$current}Select the project you wish to merge into the current project, "%s".{/t}<br/>
Please note that merging will <b>move</b> all data from the source project to the current one, not copy it. 
Afterwards, the source will remain as a (partly) empty project.
</p>
<p>
Project to merge:
<select name="id">
<option value=""></option>
{foreach from=$projects item=v}
<option value="{$v.id}">{if $v.title!=''}{$v.title}{else}[untitled]{/if} {* $v.id *}</option>
{/foreach}
</select>
</p>
<p>
<input type="submit" value="{t}select{/t}" />
</p>
{/if}
</form>
<p>
<a href="../projects/">{t}Back{/t}</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
