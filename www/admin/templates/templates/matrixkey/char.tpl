{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
{if $characteristic.label}
{t _s1=$characteristic.label _s2=$matrix.label}Editing character "%s" for matrix "%s"{/t}
{else}
{t _s1=$matrix.label}New charcteristic for matrix "%s"{/t}
{/if}
</span>
</p>
<p>
{t}Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:{/t}
<ul>
{foreach $charTypes v k}
	<li>{t}{$v.name}{/t}: {t}{$v.info}{/t}</li>
{/foreach}
</ul>
</p>
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" id="id" name="id" value="{$characteristic.id}" />
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" id="name" value="{$characteristic.characteristic}" />
<table>
	<tr>
		<td>
		</td>
{if $languages|@count>1}
{foreach $languages v k}
{if $v.def_language=='1'}
	<td>{$v.language} *</td>
{/if}
{/foreach}
	<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
	</tr>
	<tr>
		<td>
			{t}Characteristic name:{/t}
		</td>
		<td>
			<input
				type="text" 
				id="characteristic-default" 
				onblur="matrixSaveCharacteristicLabel(allDefaultLanguage)" />
		</td>
		{if $languages|@count>1}
		<td>
			<input
				type="text" 
				id="characteristic-other" 
				onblur="matrixSaveCharacteristicLabel(allActiveLanguage)" />
		</td>
		{/if}
	</tr>
	<tr>
		<td>
			{t}Character type:{/t}
		</td>
		<td>
	<select name="type" id="type">
	{foreach $charTypes v k}
		<option value="{$v.name}" {if $characteristic.type.name==$v.name}selected="selected"{/if}>{t}{$v.name}{/t}</option>
	{/foreach}
	</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="button" onclick="matrixSaveCharacteristic()" value="{t}save{/t}" />&nbsp;
			<input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_self')" />
			{if $characteristic.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic('{$characteristic.label|@addslashes}')" />&nbsp;{/if}
		</td>
	</tr>
</table>
{if $charLib && !$characteristic.label}
<br />
<table>
	<tr>
		<td colspan="3">
			{t}Instead, you can also use an existing character from one of your other matrices. To do so, select the name below and click "use".{/t}
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<select name="existingChar" id="existingChar">
			{foreach $charLib v k}
				<option value="{$v.id}">{$v.label}</option>
			{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="button" onclick="$('#action').val('use');$('#theForm').submit();" value="{t}use{/t}" />&nbsp;
			<input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_self')" />
		</td>
	</tr>
</table>
{/if}

</form>
</p>
</div>

<script type="text/javascript">
$(document).ready(function()
{
allActiveView = 'matrixchar';

{foreach $languages v k}
allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
{/foreach}
allActiveLanguage = {if $v.language_id!=''}{$v.language_id}{else}false{/if};
allDrawLanguages();

matrixGetCharacteristicLabel(allDefaultLanguage);
matrixGetCharacteristicLabel(allActiveLanguage);

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
