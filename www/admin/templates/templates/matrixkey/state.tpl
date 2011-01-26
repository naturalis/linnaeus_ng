{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form enctype="multipart/form-data" id="theForm" method="post" action="state.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="id" value="{$state.id}" />
<input type="hidden" name="char" value="{$characteristic.id}" />
<input type="hidden" name="type" value="{$characteristic.type.name}" />
<p>
{t _s1=$characteristic.characteristic _s2=$characteristic.type.name}You are defining a state for the characteristic "%s", which is of the type "%s".{/t}
</p>
<table>
	<tr>
		<td>{t}Name:{/t}</td>
		<td>
			<input type="text" name="label" id="label" value="{$state.label}" />
		</td>
	</tr>
{if $characteristic.type.name=='media'}
	<tr style="vertical-align:top">
		<td>
		{if $state.file_name}
		{t}Current image:{/t}
		{else}
		{t}Choose a file to upload:{/t}
		{/if}
		</td>
		<td>
		{if $state.file_name}
			<img src="{$session.project.urls.project_media}{$state.file_name}" style="width:150px;border:1px solid black;margin-bottom:5px" />
		{else}
			<input name="uploadedfile" id="uploadedfile" type="file" /><br />
		{/if}
		</td>
	</tr>
	{if !$state.file_name}
	<tr>
		<td>&nbsp;</td>
		<td>
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
		<td>
			<input type="text" name="lower" id="lower" style="text-align:right;width:75px;" value="{$state.lower}" />
		</td>
	</tr>
	<tr>
		<td>{t}Upper limit (inclusive):{/t}</td>
		<td>
			<input type="text" name="upper" id="upper" style="text-align:right;width:75px;" value="{$state.upper}" />
		</td>
	</tr>
{elseif $characteristic.type.name=='distribution'}
	<tr>
		<td>{t}Mean:{/t}</td>
		<td>
			<input type="text" name="mean" id="mean" style="text-align:right;width:50px;" value="{$state.mean}" />
		</td>
	</tr>
	<tr>
		<td>{t}Distance from mean of one standard deviation:{/t}</td>
		<td>
			<input type="text" name="sd1" id="sd1" style="text-align:right;width:50px;" value="{$state.sd1}" />
		</td>
	</tr>
	<tr>
		<td>{t}Distance from mean of two standard deviation:{/t}</td>
		<td>
			<input type="text" name="sd2" id="sd2" style="text-align:right;width:50px;" value="{$state.sd2}" />
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
			<input type="button" onclick="$('#action').val('');matrixCheckStateForm()" value="{t}save and return to matrix{/t}" />&nbsp;
			<input type="button" onclick="$('#action').val('repeat');matrixCheckStateForm();" value="{t _s1=$characteristic.characteristic}save and add another state for &quot;%s&quot;{/t}" />&nbsp;
			{if $state.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('matrix.php','_top')" />
		</td>
	</tr>
</table>
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}