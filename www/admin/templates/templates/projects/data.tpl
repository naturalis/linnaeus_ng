{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form enctype="multipart/form-data" id="theForm" action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="deleteLogo" id="deleteLogo" value="0" />
<table>
	<tr>
		<td>
			{t}Project title:{/t}
		</td>
		<td colspan="2">
			<input type="text" name="title" value="{$data.title}" style="width:300px;" />
		</td>
	</tr>
	<tr>
		<td>
			{t}Internal project name:{/t}
		</td>
		<td colspan="2">
        {if $isSysAdmin}
        	<input type="text" name="sys_name" value="{$data.sys_name}" style="width:300px;" />
        {else}
			{$data.sys_name}
		{/if}
		</td>
	</tr>
	<tr>
		<td>
			{t}Internal project description:{/t}
		</td>
		<td colspan="2">
        {if $isSysAdmin}
        	<textarea name="sys_description" style="width:300px;font-family:inherit;font-size:0.9em">{$data.sys_description}</textarea>
        {else}
			{$data.sys_description}
		{/if}

		</td>
	</tr>
	<tr>
		<td>
			{t}Project ID:{/t}
		</td>
		<td colspan="2">
			{$data.id}
		</td>
	</tr>
	<tr>
		<td>
			{t}Shortname for URL ("slug"):{/t}
		</td>
		<td colspan="2">
			<input type="text" name="short_name" value="{$data.short_name}" style="width:300px;" />
		</td>
	</tr>
	<tr>
		<td>
			{t}Group:{/t}
		</td>
		<td colspan="2">
			<input type="text" name="group" value="{$data.group}" style="width:300px;" />
		</td>
	</tr>

	{*<tr style="vertical-align:top">
		<td>
			{t}Project logo:{/t}
		</td>
		<td colspan="2">
		{if $data.logo}
		<img src="{$session.admin.project.urls.project_media}{$data.logo}" width="150px" />
		<span class="a" onclick="$('#deleteLogo').val(1);$('#theForm').submit();">{t}Delete logo{/t}</span><br />
		{else}
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		<input name="uploadedfile" type="file" /><br />
		{/if}
		</td>
	</tr>*}
	<tr style="vertical-align:top">
		<td>
			{t}Description (for html meta-tag):{/t}
		</td>
		<td colspan="2">
			<textarea name="description" style="width:300px;height:200px;font-size:11px" />{$data.description}</textarea>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			{t}Keywords (for html meta-tag; separate with spaces):{/t}
		</td>
		<td colspan="2">
			<textarea name="keywords" style="width:300px;height:100px;font-size:11px" />{$data.keywords}</textarea>
		</td>
	</tr>
	{* <tr>
		<td>
			{t}CSS url:{/t}
		</td>
		<td colspan="2">
			<input type="text" name="css_url" value="{$data.css_url}" style="width:300px;" />
		</td>
	</tr> *}
	<tr style="vertical-align:top">
		<td>
			{t}Project languages:{/t}
		</td>
		<td>
		<span id="language-list"></span>
		</td>
	</tr>
    {if $CRUDstates.can_update}
	<tr>
		<td></td>
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
			<span
				class="a"
				onclick="projectSaveLanguage('add',[$('#language-select :selected').val(),$('#language-select :selected').text()])">
				{t}add language{/t}
			</span>
		</td>
	</tr>
    {/if}
	<tr>
		<td>
			{t}Publish project:{/t}
		</td>
		<td>
			<label>
				<input type="radio" name="published" value="1" {if $data.published=='1'} checked="checked"{/if}/> {t}yes{/t}
			</label>
			<label>
				<input type="radio" name="published" value="0" {if $data.published!='1'} checked="checked"{/if}/> {t}no{/t}
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="save" />
			<input type="button" value="back" onclick="window.open('{$session.admin.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>
</div>

{include file="../shared/admin-messages.tpl"}

<!-- database: {$database.database} -->

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{section name=i loop=$languages}
	{if $languages[i].language_project_id!=''}
	projectAddLanguage([{$languages[i].id},'{$languages[i].language}',{$languages[i].language_project_id},{if $languages[i].is_project_default}1{else}0{/if},{if $languages[i].is_active}1{else}0{/if},{$languages[i].tranlation_status}])
	{/if}
{/section}
	projectUpdateLanguageBlock();
{literal}
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
