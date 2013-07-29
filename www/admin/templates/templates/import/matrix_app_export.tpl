{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $matrices==false}
	The multi-entry key module has not been activated for your project.
{elseif $matrices|@count==0}
	Your project has no multi-entry key.
{else}
Select the key for which you want to create a SQLite create & insert script and click 'export':
<form method="post">
<input type="hidden" name="action" value="export" />
<p>
    {foreach from=$matrices item=v}
    {foreach from=$v.names item=n}
    <label><input type="radio" name="id" value="{$v.id}-{$n.language_id}"{if $n.language_id==$default_langauge} checked="checked"{/if} />{$n.name} ({$n.language})</label><br />
    {/foreach}
    {/foreach}
</p>
<p>
{if $dbSettings.tablePrefix!=''}
<label><input type="checkbox" name="removePrefix" value="y" />remove table prefix "{$dbSettings.tablePrefix}"</label><br />
{else}
<input type="hidden" name="removePrefix" value="n" />
{/if }
<label><input type="checkbox" name="includeCode" value="y" checked="checked"/>add PhoneGap/javascript execution code (includes table check)</label><br />
</p>
<input type="submit" value="export" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}