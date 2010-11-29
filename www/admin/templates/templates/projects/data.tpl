{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form enctype="multipart/form-data" id="theForm" action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="deleteLogo" id="deleteLogo" value="0" />
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
		{if $data.logo}
		<img src="{$session.project.urls.project_media}{$data.logo}" width="150px" />
		<span class="pseudo-a" onclick="$('#deleteLogo').val(1);$('#theForm').submit();">{t}Delete logo{/t}</span><br />
		{else}
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		<input name="uploadedfile" type="file" /><br />
		{/if}
		</td>
	</tr>
	<tr>
		<td>
			CSS url:
		</td>
		<td colspan="2">
			<input type="text" name="css_url" value="{$data.css_url}" style="width:300px;" />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project languages:
		</td>
		<td>
		<!-- u>Language(s) currently in use</u><br / -->
		<span id="language-list"></span>
		</td>
	</tr>
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
				class="pseudo-a" 
				onclick="projectSaveLanguage('add',[$('#language-select :selected').val(),$('#language-select :selected').text()])">
				add language
			</span>
		</td>
	</tr>
	<tr>
		<td>
			This project includes hybrid taxa:
		</td>
		<td>
			<label>
				<input type="radio" name="includes_hybrids" value="1" {if $data.includes_hybrids=='1'} checked="checked"{/if}/> yes
			</label>
			<label>
				<input type="radio" name="includes_hybrids" value="0" {if $data.includes_hybrids!='1'} checked="checked"{/if}/> no
			</label>
		</td>
	</tr>		
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>		
	<tr>
		<td colspan="2">
			<input type="submit" value="save" />
			<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>		
</table>
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
	projectAddLanguage([{$languages[i].id},'{$languages[i].language}',{$languages[i].language_project_id},{if $languages[i].is_project_default}1{else}0{/if},{if $languages[i].is_active}1{else}0{/if},{$languages[i].tranlation_status}])
	{/if}
{/section}
	projectUpdateLanguageBlock();
{literal}
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
