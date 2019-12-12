<style>
.image-preview {
	max-width: 300px;
	max-height: 250px;
	border: 1px solid #ddd;
}
input[type=text] {
	width:1200px;
}
</style>

{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form enctype="multipart/form-data" id="theForm" method="post" action="state.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" id="action" value="save" />
<input type="hidden" name="repeat" id="repeat" value="0" />
<input type="hidden" name="id" id="id" value="{$state.id}" />
<input type="hidden" name="char" value="{$characteristic.id}" />
<input type="hidden" name="type" value="{$characteristic.type}" />
<p>
{t _s1=$characteristic.type _s2=$characteristic.label _s3=$matrix.label}Editing a state of the type "%s" for the character "%s" of matrix "%s".{/t}
</p>

    <table>
        <tr>
            <td>{t}Internal name{/t}:</td>
            <td><input type="text" name="sys_name" value="{$state.sys_name}" style="width:300px" maxlength="255" /></td>
        </tr>
    {foreach $languages v i}
        <tr>
            <td>{$v.language} {t}name{/t}:</td>
            <td><input type="text" name="labels[{$v.language_id}]" value="{$state.labels[$v.language_id].label}" style="width:300px" maxlength="255" /></td>
        </tr>
    {/foreach}

    <!-- tr>
        <td></td>
        {if $characteristic.type == 'media' && $state.id == ''}
        <td></td>
        {else}
        <td style="padding-top:10px">Value</td>
        {/if}
    </tr -->

	{if $characteristic.type=='text'}

	    {foreach $languages v i}
	        <tr style="vertical-align:top">
	            <td>{$v.language}:</td>
	            <td><textarea
					style="width:400px;height:150px;"
					name="texts[{$v.language_id}]"
					>{$state.texts[{$v.language_id}].text}</textarea></td>
	        </tr>
	    {/foreach}

	{elseif $characteristic.type=='media' && $state.id != ''}
    
	    {if $use_media}

		<tr style="vertical-align:top">
			<td>
			{if $state.file_name}
			{t}Current image:{/t}
			{else}
			{t}Choose a file:{/t}
			{/if}
			</td>
			<td{if $languages|@count>1} colspan="2"{/if}>
			{if $state.file_name}
				<img src="{$state.file_name}" onclick="allShowMedia('{$state.file_name}','');" class="image-preview" />
				<input type="hidden" name="existing_file" value="{$state.file_name}" /><br />
				<span class="a" onclick="matrixDeleteStateImage();">{t}detach image{/t}</span>
			{else}
				<a href="../media/upload.php?item_id={$state.id}&amp;module_id={$module_id}">{t}Upload{/t}</a> or
				<a href="../media/select.php?item_id={$state.id}&amp;module_id={$module_id}">{t}attach media{/t}</a> {t}to this state{/t}.
			{/if}
			</td>
		</tr>
	    
	    {else}
	    
	    <!-- state.file_name: {$state.file_name} -->
	    
	    {/if}

	{elseif $characteristic.type=='range'}

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

	{elseif $characteristic.type=='distribution'}

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
			{if $characteristic.type == 'media' && $state.id == ''}
				<input type="button" onclick="$('#repeat').val('2'); matrixCheckStateForm()" value="{t}save and add media{/t}" />&nbsp;
			{else}
				<input type="button" onclick="matrixCheckStateForm()" value="{t}save and return to matrix{/t}" />&nbsp;
				<input type="button" onclick="$('#repeat').val('1');matrixCheckStateForm();" value="{t _s1=$characteristic.label}save and add another state for &quot;%s&quot;{/t}" />&nbsp;
				{if $state.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_top')" />
		</td>
	</tr>
</table>

</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
