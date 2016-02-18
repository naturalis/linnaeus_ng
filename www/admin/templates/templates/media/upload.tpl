{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
.bar {
    height: 18px;
    background: green;
}
tr td:first-child {
	text-align: right;
}
tr:last-child td {
	padding-top: 15px;
}
</style>

<div id="page-main">

	<p>Text about file types and size. The metadata provided will be used for all uploaded files.</p>
	<p>
		Maximum number of files: {$max_file_uploads}<br/>
	    Maximum size of a single file: {$upload_max_filesize}<br/>
	    Maximum total batch size: {$post_max_size}
    </p>

	{if $uploaded|@count > 0}
	<p>The following files were uploaded successfully:<ul>
	{foreach from=$uploaded item=v}
		<li style="color: green;">{$v}</li>
	{/foreach}
	</ul></p>
	{/if}

	<div id="progress">
	    <div class="bar" style="width: 0%;"></div>
	</div>

	<form id="theForm" method="post" enctype="multipart/form-data" action="upload.php">
	<input type="hidden" name="action" value="upload" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="{$session_upload_progress_name}" value="media">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$upload_max_filesize * 1000}" />
	<table>
	<tr>
		<td>{t}title{/t}:</td>
		<td><input class="large" type="text" name="title" value="" /></td>
	</tr>
	<tr>
		<td>{t}caption{/t}:</td>
		<td><input class="large" type="text" name="caption" value="" /></td>
	</tr>
	<tr>
		<td>{t}location{/t}:</td>
		<td><input class="large" type="text" name="location" value="" /></td>
	</tr>
	<tr>
		<td>{t}photographer{/t}:</td>
		<td><input class="large" type="text" name="photographer" value="" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="file" name="files[]" multiple /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="upload" value="{t}upload{/t}"></td>
	</tr>
	</table>
    </form>
</div>

{literal}
<script>

</script>
{/literal}

{include file="../shared/admin-footer.tpl"}