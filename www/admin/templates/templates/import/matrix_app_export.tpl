{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

{if $output}
Output:
<p>
<textarea id="output" style="width:100%;height:500px">{$output}</textarea>
</p>
<input type="button" value="select all (you'll have to copy manually)" onclick="$('#output').focus();$('#output').select();" />&nbsp;&nbsp;<a href="matrix_app_export.php">back</a>
{else}
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
    options:<br />
    <label><input type="checkbox" name="reduceURLs" value="y" checked="checked"/>reduce image URLs to filenames only</label><br />
    {if $dbSettings.tablePrefix!=''}
    <label><input type="checkbox" name="removePrefix" value="y" checked="checked"/>remove table prefix "{$dbSettings.tablePrefix}"</label><br />
    {else}
    <input type="hidden" name="removePrefix" value="n" />
    {/if }
    <label><input type="checkbox" name="imageList" value="y" />include list of images found in the tables</label><br />
    <label><input type="checkbox" name="separateDrop" value="y" />include separate set of drop-queries</label><br />
    <label><input type="checkbox" name="includeCode" value="y" checked="checked"/>make PhoneGap/javascript include file</label><br />
    <label><input type="checkbox" name="downloadFile" value="y" checked="checked"/>download as file (rather than display in browser)</label><br />
	db version: <input type="text" value="{$version}" name="version" style="width:30px" /><br />
    </p>
    <input type="submit" value="export" />
    </form>
    {/if}
{/if}

</div>

{include file="../shared/admin-footer.tpl"}