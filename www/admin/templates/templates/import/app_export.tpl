{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

{if $output}
Output:
<p>
<textarea id="output" style="width:100%;height:500px">{$output}</textarea>
</p>
<input type="button" value="select all (you'll have to copy manually)" onclick="$('#output').focus();$('#output').select();" />&nbsp;&nbsp;<a href="app_export.php">back</a>
{else}
    Select the key for which you want to create a SQLite create & insert script and click 'export':
    <form method="post">
    <input type="hidden" name="action" value="export" />
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
    </p>
	<p>
	Species-tabs to export:
	<select name="taxonTab">
	{foreach from=$getTaxonTabs item=v}
		<option value="{$v.id}">{$v.page}</option>
	{/foreach}
	</select>
	</p>	
	<p>
	Language to export:
	<select name="projectLanguage">
	{foreach from=$getProjectLanguages item=v}
		<option value="{$v.language_id}">{$v.language}</option>
	{/foreach}
	</select>
	
	
	
	</p>
    <input type="submit" value="export" />
    </form>
{/if}

</div>

{include file="../shared/admin-footer.tpl"}