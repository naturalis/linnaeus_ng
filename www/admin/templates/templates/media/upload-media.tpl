<div id="media-main">
	{if $module_name != '' && $item_name != ''}
		 <p><a href="{$back_url}">back to {$item_name} ({$module_name})</a></p>
	{/if}

	{if $uploaded|@count > 0}
	<p>The following files were uploaded successfully:</p>
	<ul id="file-list">
	{foreach from=$uploaded item=v}
		<li>{$v}</li>
	{/foreach}
	</ul>
	{/if}

	<p>If you upload multiple files, the metadata
		provided will be used for all uploaded files.</p>
	<p>Tags are used to classify types of multimedia. You can enter multiple tags separated
		by commas.
	</p>
	<p>Captions are not part of the default metadata, but are dependent of the module and
		associated item (species, glossary entry, etc.). Captions can be added when editing
		media in situ.
	</p>
	</p>
	<p>
		Maximum number of files: {$max_file_uploads}<br/>
	    Maximum size of a single file: {$upload_max_filesize}<br/>
	    Maximum total batch size: {$post_max_size}
    </p>


	<div id="progress">
	    <div class="bar" style="width: 0%;"></div>
	</div>

	<form id="theForm" method="post" enctype="multipart/form-data" action="{$action}">
	<input type="hidden" name="action" value="upload" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="{$session_upload_progress_name}" value="media" />
	<input type="hidden" name="module_id" value="{$module_id}" />
	<input type="hidden" name="item_id" value="{$item_id}" />
    <input type="hidden" name="back_url" value="{$back_url}" />
    <input type="hidden" name="MAX_FILE_SIZE" value="{$form_max_file_size}" />


	<table>
    {if $languages|@count>1}
    <tr>
    <td>{t}language{/t}:</td>
    <td>
    <select id="language_id" name="language_id" onchange="$('#action').val('language_change');$('#theForm').submit();">
        {foreach from=$languages item=v}
        <option value="{$v.language_id}"{if $v.language_id==$language_id} selected="selected"{/if}>{$v.language}</option>
        {/foreach}
	</select>
    {else}
    <input type="hidden" id="language_id" name="language_id" value="{$defaultLanguage}" />
    {/if}
    </td>
	</tr>

	{foreach from=$metadata key=field item=value}
	<tr>
		<td>{t}{$field}{/t}:</td>
		<td><input type="text" name="{$field}" value="{$value}" /></td>
	</tr>
	{/foreach}

	<tr>
		<td>{t}tags{/t}:</td>
		<td><textarea name="tags" placeholder="{t}enter multiple tags separated by comma's{/t}" rows="3"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="file" name="files[]" {if $input_type != 'single'}multiple{/if} /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="upload" value="{t}upload{/t}" /></td>
	</tr>
	</table>
    </form>
</div>