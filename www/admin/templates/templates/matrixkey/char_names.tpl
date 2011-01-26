{include file="../shared/admin-header.tpl"}

when save set language

<div id="page-main">
<p>
{t}Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:{/t}
<ul>
{section name=i loop=$charTypes}
<li>{t}{$charTypes[i].name}{/t}: {t}{$charTypes[i].info}{/t}</li>
{/section}
</ul>
</p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr>
		<td></td>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
{if $languages|@count>1}		
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
				name="characteristic" 
				id="characteristic" 
				value="{$characteristic.characteristic}" 
				onblur="matrixSaveCharacteristic('{$characteristic.id}',this.value,'default')" />
		</td>
		<td>
			<input 
				type="text" 
				name="characteristic" 
				id="characteristic" 
				value="{$characteristic.characteristic}" 
				onblur="matrixSaveCharacteristic('{$characteristic.id}',this.value,'other')" />
		</td>
	</tr>
	<tr>
		<td>
			{t}Characteristic type:{/t}
		</td>
		<td>
	<select name="type" id="type">
	{section name=i loop=$charTypes}
		<option value="{$charTypes[i].name}" {if $characteristic.type==$charTypes[i].name}selected="selected"{/if}>{t}{$charTypes[i].name}{/t}</option>
	{/section}
	</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="save" />&nbsp;
			<input type="button" value="{t}back{/t}" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>
</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
allActiveView = 'matrixcharacteristics';

{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawLanguages();

{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}