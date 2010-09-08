{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form enctype="multipart/form-data" action="" method="POST">
<table>
	<tr>
		<td>
			Internal project name:
		</td>
		<td colspan="2">
			{$data.sys_name}
		</td>
	</tr>
	<tr>
		<td>
			Internal project description:
		</td>
		<td colspan="2">
			{$data.sys_description}
		</td>
	</tr>
	<tr>
		<td>
			Project title:
		</td>
		<td colspan="2">
			<input type="text" name="title" value="{$data.title}" style="width:300px;" />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project logo:
		</td>
		<td colspan="2">
		{if $data.logo_url}
		<img src="{$data.logo_url}" width="150px" /><br />
		<label><input type="checkbox" value="1" name="deleteLogo" />Delete current logo (uploading a new logo deletes the old one as well)</label><br />
		{/if}
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		<input name="uploadedfile" type="file" /><br />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project languages:
		</td>
		<td>
			<select name="language-select" id="language-select">
			{assign var=first value=true}
			{section name=i loop=$languages}
				{if $first && $languages[i].show_order==''}				
				<option disabled="disabled" class="language-select-item-disabled">
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
				</option>{assign var=first value=false}{/if}
				<option 
					value="{$languages[i].id}"
					class="language-select-item{if $languages[i].language_project_id!=''}-active{/if}"
					>{$languages[i].language}</option>
			{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align:center">
			<img 
				src="{$rootWebUrl}admin/images/system/icons/arrow-270.png" 
				onclick="projectSaveLanguage('add',[$('#language-select :selected').val(),$('#language-select :selected').text()])" 
				class="general-clickable-image" 
				title="add language"
			/>
		</td>
	</td>
	<tr>
		<td></td>
		<td>
		<!-- u>Language(s) currently in use</u><br / -->
		<span id="language-list">	
		</span>
		</td>
	</tr>		
</table>
<input type="submit" value="save" />
</form>

<br />
The "welcome" and "contributors" texts will be added once the html-editor is in place.
</div>

{include file="../shared/admin-messages.tpl"}


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{section name=i loop=$languages}
	{if $languages[i].language_project_id!=''}
	projectAddLanguage([{$languages[i].id},'{$languages[i].language}',{$languages[i].language_project_id},{if $languages[i].is_project_default}1{else}0{/if},{if $languages[i].is_active}1{else}0{/if}])
	{/if}
{/section}
	projectUpdateLanguageBlock();
{literal}
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
