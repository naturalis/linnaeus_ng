{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

{if $output}
Output:
<p>
<textarea id="output" style="width:100%;height:500px">{$output}</textarea>
</p>
<input type="button" value="select all (you'll have to copy manually)" onclick="$('#output').focus();$('#output').select();" />
{if $fixImageNames}
<p>
{if $renameImageListCount==0}
(no images were renamed)
{else}
<a href="image_rename_script.php?p=win">download image rename script (windows)</a> / <a href="image_rename_script.php?p=lin">(linux)</a>
<span style="color:red;font-weight:bold">make sure you run this on the image file directory!</span>
{/if}
</p>
{*
<p>
<a href="delete_unused_script.php?p=win">download script for deleting unused imgaes from image folder (windows)</a>{*  / <a href="image_rename_script.php?p=lin">(linux)</a>*}
</p>
*}
{/if}
<a href="app_export.php">back</a>
{else}

    <form method="post">
    <input type="hidden" name="action" value="export" />

	<p>
	app title: <input type="text" name="appTitle" value="{$appTitle}" style="width:200px;" /><br />
	</p>

	<p>
    modules to export:<br />
	{foreach $projectModules.modules as $v}
    {if $v.controller!='highertaxa' && $v.controller!='utilities'}
		<label><input type="checkbox" name="modules[]" value="{$v.controller}" checked="checked">{$v.module}</label><br />
    {/if}
    {/foreach}
	{* foreach $projectModules.freeModules as $v}
		<label><input type="checkbox">{$v.module}</label><br />
    {/foreach *}
    </p>

	<p>
	Species-tabs to export:
	<select name="taxonTab">
    {assign var=bla value=false}
	{foreach from=$getTaxonTabs item=v}
		<option value="{$v.id}" {if $v.page|stripos:"app" && !$bla} selected="selected"{assign var=bla value=true}{/if}>{$v.page}</option>
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
    <p>
    options:<br />
    <label><input type="checkbox" name="reduceURLs" value="y" checked="checked"/>reduce image URLs to filenames only</label><br />
    <label><input type="checkbox" name="fixImageNames" value="y" checked="checked"/>remove spaces from image names</label><br />
    <label><input type="checkbox" name="keepSubURLs" value="y" checked="checked"/>when reducing image URLs, retain folder structure <i>inside</i> "{$session.admin.project.urls.project_media}" (embedded images only)</label><br />
	image root placeholder: <input type="text" name="imgRootPlaceholder" value="$IMAGE_ROOT$" style="width:100px;" /> (embedded images only)<br />
    {if $dbSettings.tablePrefix!=''}
    <label><input type="checkbox" name="removePrefix" value="y" checked="checked"/>remove table prefix "{$dbSettings.tablePrefix}"</label><br />
    {else}
    <input type="hidden" name="removePrefix" value="n" />
    {/if }
    <label><input type="checkbox" name="imageList" value="y" />include list of images found in the tables</label><br />
    <label><input type="checkbox" name="separateDrop" value="y" />include separate set of drop-queries</label><br />
    <label><input type="checkbox" name="includeCode" value="y" checked="checked"/>make encoded PhoneGap/javascript include file (unchecking gives straight sqlite queries)</label><br />
    <label><input type="checkbox" name="downloadFile" value="y" />download as file (rather than display in browser)</label><br />
    </p>
    
    <input type="submit" value="export" />
    </form>
{/if}

</div>

{include file="../shared/admin-footer.tpl"}