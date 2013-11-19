{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
	{t _s1=$matrix.matrix}Editing matrix "%s"{/t}
	(<a href="preview.php">{t}preview{/t}</a>) (<a href="matrices.php">{t}select another matrix{/t}</a>)<br />
	{if $matrices|@count> 1}
		{if $matrix.default==1}
			(this is the default matrix)
		{else}
			(this is not currently the default matrix; <a href="?action=def&id={$matrix.id}">make this matrix the default matrix</a>)
		{/if}
		<br />
	{/if}
	<a href="matrices.php?imgdim={$matrix.id}">{t}Get and save state image dimensions (for newly imported matrices){/t}</a>



</span>
</p>
<table>
	<tr>
		<td colspan="2">
			{t}characters{/t} (<a href="char_sort.php">{t}sort characters{/t}</a>{if $useCharacterGroups}; <a href="char_groups.php">{t}edit character groups{/t}</a>{/if})
			<select size="100" class="matrix-list-select" id="characteristics" onchange="matrixCharacteristicsChange();" onclick="matrixGetLinks();">
			{section name=i loop=$characteristics}
			<option value="{$characteristics[i].id}" id="char-{$characteristics[i].id}" ondblclick="window.open('char.php?id={$characteristics[i].id}','_self');">{$characteristics[i].label} ({$characteristics[i].type.name})</option>
			{/section}
			</select>
		</td>
		<td>
		</td>
		<td colspan="2">
			{t}taxa{/t}  (<a href="links.php">{t}display current links per taxon{/t}{if $matrices} {t}& other matrices{/t}{/if}</a>)
			<select size="100" id="taxa" class="matrix-list-select" onclick="matrixGetLinks();">
			{foreach from=$taxa item=v}
			<option id="tax-{$v.id}" value="{$v.id}">{$v.taxon}</option>
			{/foreach}

			{if $useVariations && $variations}
			<option disabled="disabled">----------------------------------------------------------------------------------------------------</option>
			{foreach from=$variations item=v}
			<option id="var-{$v.id}" value="var-{$v.id}">{$v.label}</option>
			{/foreach}
			{/if}
			
			{if $matrices}
			<option disabled="disabled">----------------------------------------------------------------------------------------------------</option>
			{foreach from=$matrices item=v}
			<option id="mx-{$v.id}" value="mx-{$v.id}">{$v.default_name}</option>
			{/foreach}
			{/if}
			</select>
		</td>
	</tr>
	<tr>{* buttons are div's as normal buttons cannot hold more than one line of text *}
		<td style="text-align:center"><script> allCreateButton('{t}add new{/t}','window.open(\'char.php\',\'_self\');');</script></td>
		<td style="text-align:center"><script> allCreateButton('{t}edit/delete selected{/t}','window.open(\'char.php?id=\'+$(\'#characteristics\').val(),\'_self\');');</script></td>
		<td></td>
		<td style="text-align:center"><script> allCreateButton('{t}add new taxon{/t}','window.open(\'taxa.php\',\'_self\');');</script></td>
		<td style="text-align:center"><script> allCreateButton('{t}remove selected taxon{/t}','matrixDeleteTaxon();');</script></td>
	</tr>
	<tr>
		<td colspan="3"  style="height:10px">
	</tr>		
	<tr>
		<td colspan="2">
			{t}states{/t} (<span class="a" onclick="matrixShowSortStates();">{t}sort states{/t}</span>)
			<select size="100" id="states" class="matrix-list-select">
			</select>
		</td>
		<td></td>
		<td colspan="2">
			{t}links{/t}
			<select size="100" id="links" class="matrix-list-select">
			</select>
		</td>
	</tr>		
	<tr>
		<td style="text-align:center"><script> allCreateButton('{t}add new{/t}','matrixAddStateClick()','newStateButton');</script></td>
		<td style="text-align:center"><script> allCreateButton('{t}edit/delete selected{/t}','matrixEditStateClick()');</script></td>
		<td></td>
		<td style="text-align:center"><script> allCreateButton('{t}add new{/t}','matrixAddLinkClick()');</script></td>
		<td style="text-align:center"><script> allCreateButton('{t}delete selected{/t}','matrixRemoveLink()');</script></td>
	</tr>
</table>
	
</div>

{if $activeCharacteristic}
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
matrixSetActiveState({$activeCharacteristic});
matrixCharacteristicsChange();
{literal}
});
</script>
{/literal}
{/if}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
