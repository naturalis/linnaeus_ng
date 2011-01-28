{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
{if $characteristic.id}
{t _s1=$characteristic.characteristic _s2=$matrix.matrix}Editing charcteristic "%s" for matrix "%s"{/t}
{else}
{t _s1=$matrix.matrix}New charcteristic for matrix "%s"{/t}
{/if}
</span>
</p>
<p>
{t}Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:{/t}
<ul>
{section name=i loop=$charTypes}
<li>{t}{$charTypes[i].name}{/t}: {t}{$charTypes[i].info}{/t}</li>
{/section}
</ul>
{t}After you have saved the name and type, you can specify the name in the different languages within your project.{/t}
</p>
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$characteristic.id}" />
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" id="name" value="{$characteristic.characteristic}" />
<table>
	<tr>
		<td>
			{t}Characteristic name:{/t}
		</td>
		<td>
			<input
				type="text" 
				name="characteristic" 
				id="characteristic" 
				value="{$characteristic.characteristic}" />
		</td>
	</tr>
	<tr>
		<td>
			{t}Characteristic type:{/t}
		</td>
		<td>
	<select name="type" id="type">
	{section name=i loop=$charTypes}
		<option value="{$charTypes[i].name}" {if $characteristic.type.name==$charTypes[i].name}selected="selected"{/if}>{t}{$charTypes[i].name}{/t}</option>
	{/section}
	</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="{t}save{/t}" />&nbsp;
			{if $characteristic.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
{if $charLib && !$characteristic.id}
<br />
<table>
	<tr>
		<td colspan="3">
			{t}Instead, you can also use an existing characteristic from one of your other matrices. To do so, select the name below and click "use".{/t}
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<select name="existingChar" id="existingChar">
			{section name=i loop=$charLib}
				<option value="{$charLib[i].id}">{$charLib[i].characteristic}</option>
			{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="button" onclick="$('#action').val('use');$('#theForm').submit();" value="{t}use{/t}" />&nbsp;
			<input type="button" value="{t}back{/t}" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
{/if}

</form>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}