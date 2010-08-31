{include file="../shared/admin-header.tpl"}

<div id="admin-main">
{$data.id}{$data.version}
<table>
	<tr>
		<td>
			Internal project name:
		</td>
		<td>
			{$data.sys_name}
		</td>
	</tr>
	<tr>
		<td>
			Internal project description:
		</td>
		<td>
			{$data.sys_description}
		</td>
	</tr>
	<tr>
		<td>
			Project title:
		</td>
		<td>
			<input type="text" name="title" value="{$data.title}" />
		</td>
	</tr>
	<!-- tr>
		<td>
			Project logo:
		</td>
		<td>
		<form enctype="multipart/form-data" action="" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
			Choose a file to upload: <input name="uploadedfile" type="file" /><br />
			<input type="submit" value="Upload File" />
		</form>

			<input type="text" name="title" value="{$data.logo_path}" />
		</td>
	</tr -->
</table>





logo<br />
languages<br />
<br />
welcome text<br />
contrib text<br />
about ETI (fix)<br /><br />

</div>

{include file="../shared/admin-footer.tpl"}
