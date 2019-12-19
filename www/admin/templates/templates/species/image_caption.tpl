{include file="../shared/admin-header.tpl"}

<form method="post" action="" enctype="multipart/form-data">
<div id="page-main">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <p>
	<input name="uploadedfile" type="file" />
    </p>
    {if $languages|@count==1}
    <input type="hidden" name="language_id" value="{$languages[0].language_id}" />
    {else}
	<p>
    Caption language:
    <select name="language_id">
	{foreach from=$languages item=v}
	<option value="{$v.language_id}">{$v.language}</option>
	{/foreach}
    </select>
    </p>
    {/if}
    <p>
	<input type="submit" value="{t}upload{/t}" />
    </p>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
{t}To load image captions from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.{/t}
{t} Captions for taxon/filename combinations that do not exist will be ignored. Existing captions will be overwritten. Empty captions will cause existing captions to be deleted.{/t}
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter is a {/t}<input type="text" name="delimiter" value="," maxlength="1" style="width:10px;font-size:12px"> (comma by default)</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}Each line consists of the following fields:{/t}
		<ol>
            <li>Taxon ID <i>or</i> scientific name; interpretation is based on the value being an integer or not</li>
            <li>Filename</li>
            <li>Caption</li>
		</ol>
	</li>
</ol>
</div>
</form>

{include file="../shared/admin-footer.tpl"}
