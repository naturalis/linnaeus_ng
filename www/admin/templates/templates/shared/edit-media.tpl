<div id="page-main">

	<form id="theForm" enctype="multipart/form-data" method="post">
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="module_id" value="{$module_id}" />
	<input type="hidden" name="item_id" value="{$item_id}" />
	<input type="hidden" name="back_url" value="{$back_url}" />

	<p><a href="{$back_url}">{t}back{/t}</a></p>

	<p>
		{if $media_type == 'image'}
			<a href="{$source}" rel="prettyPhoto">
			<img src="{$source}" alt="{$name}" class="image-preview" />
			</a>

		{else if $media_type == 'audio' or $media_type == 'video'}
			<{$media_type} src="{$source}" alt="{$name}" class="av-preview" controls />
				<a href="{$source}">Play {$name}</a>
			</{$media_type}>

		{else}
			<a href="{$source}">
			<img src="{$thumbnail}" alt="{$name}" />
			</a>

		{/if}
		<br><strong>{$name}</strong><br>

	</p>

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
		<td><textarea name="tags" placeholder="{t}enter multiple tags separated by comma's{/t}" rows="3">{$tags}</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td style="padding: 15px 0 5px 0;">{t}Optionally, you can replace this media file with a different file.{/t}<br>
			{t}The metadata and tags will be retained.{/t}</td>
	</tr>
	<tr>
		<td>{t}replace with{/t}:</td>
		<td><input type="file" name="files[]" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="save" value="{t}save{/t}" /></td>
	</tr>
	</table>
    </form>
</div>
