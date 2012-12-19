{include file="../shared/admin-header.tpl"}

<div id="page-main">
<span id="message-container" style="float:right;"></span>
<form enctype="multipart/form-data" id="theForm" method="post" action="state.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" id="action" value="save" />
<input type="hidden" name="id" id="id" value="{$state.id}" />
<input type="hidden" name="char" value="{$characteristic.id}" />
<input type="hidden" name="type" value="{$characteristic.type.name}" />
<p>
{t _s1=$characteristic.type.name _s2=$characteristic.label _s3=$matrix.matrix}Editing a state of the type "%s" for the character "%s" of matrix "%s".{/t}
</p>
<table>
	<tr>
		<td>
		</td>
{if $languages|@count>1}
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
	<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
	</tr>
	<tr>
		<td>{t}Name:{/t}</td>
		<td>
			<input
				type="text" 
				name="label" 
				id="label-default"
				onblur="matrixSaveStateLabel(allDefaultLanguage)" />
		</td>
{if $languages|@count>1}
		<td>
			<input
				type="text" 
				id="label-other"
				onblur="matrixSaveStateLabel(allActiveLanguage)" />
		</td>
{/if}
	</tr>
{if $characteristic.type.name=='text'}
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td>
			<textarea
				style="width:400px;height:300px;" 
				name="text"
				id="text-default"
				onblur="matrixSaveStateText(allDefaultLanguage)"
				></textarea>
		</td>
{if $languages|@count>1}
		<td>
			<textarea
				style="width:400px;height:300px;" 
				id="text-other"
				onblur="matrixSaveStateText(allActiveLanguage)"
				></textarea>
		</td>
{/if}
	</tr>
{elseif $characteristic.type.name=='media'}
	<tr style="vertical-align:top">
		<td>
		{if $state.file_name}
		{t}Current image:{/t}
		{else}
		{t}Choose a file to upload:{/t}
		{/if}
		</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
		{if $state.file_name}
			<img src="{$session.admin.project.urls.project_media}{$state.file_name}" onclick="allShowMedia('{$session.admin.project.urls.project_media}{$state.file_name}','{$state.file_name}');" 
				style="width:250px;border:1px solid black;margin:5px 0px 5px 0px;cursor:pointer" />
			<input type="hidden" name="existing_file" value="{$state.file_name}" /><br />
			<span class="a" onclick="matrixDeleteStateImage();">{t}delete image{/t}</span>
		{else}
			<input name="uploadedfile" id="uploadedfile" type="file" /><br />
		{/if}
		</td>
	</tr>
	{if !$state.file_name}
	<tr>
		<td>&nbsp;</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
			{t}Allowed formats:{/t}<ul>
			{section name=i loop=$allowedFormats}
			<li>
				{$allowedFormats[i].mime}
				({t _s1=$allowedFormats[i].media_name}%s{/t}; {t}max.{/t} {math equation="x/y" x=$allowedFormats[i].maxSize y=1000000 format="%.0fM"} {t}per file{/t}{if $allowedFormats[i].media_type=='archive'}; {t}see below for information on uploading archives{/t}{/if})</li>
			{/section}
			</ul>
		</td>
	</tr>
	{/if}
{elseif $characteristic.type.name=='range'}
	<tr>
		<td>{t}Lower limit (inclusive):{/t}</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
			<input type="text" name="lower" id="lower" autocomplete="off" style="text-align:right;width:75px;" value="{$state.lower}" />
		</td>
	</tr>
	<tr>
		<td>{t}Upper limit (inclusive):{/t}</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
			<input type="text" name="upper" id="upper" autocomplete="off" style="text-align:right;width:75px;" value="{$state.upper}" />
		</td>
	</tr>
{elseif $characteristic.type.name=='distribution'}
	<tr>
		<td>{t}Mean:{/t}</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
			<input type="text" name="mean" id="mean" autocomplete="off" style="text-align:right;width:50px;" value="{$state.mean}" />
		</td>
	</tr>
	<tr>
		<td>{t}Standard deviation:{/t}</td>
		<td{if $languages|@count>1} colspan="2"{/if}>
			<input type="text" name="sd" id="sd" autocomplete="off" style="text-align:right;width:50px;" value="{$state.sd}" />
		</td>
	</tr>
{/if}
</table>
<table>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" onclick="matrixCheckStateForm()" value="{t}save and return to matrix{/t}" />&nbsp;
			<input type="button" onclick="$('#action').val('repeat');matrixCheckStateForm();" value="{t _s1=$characteristic.label}save and add another state for &quot;%s&quot;{/t}" />&nbsp;
			{if $state.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_top')" />
		</td>
	</tr>
</table>
</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'matrixstate';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();

	matrixGetStateLabel(allDefaultLanguage);
	matrixGetStateLabel(allActiveLanguage);
{if $characteristic.type.name=='text'}
	matrixGetStateText(allDefaultLanguage);
	matrixGetStateText(allActiveLanguage);
{/if}
{literal}	
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}