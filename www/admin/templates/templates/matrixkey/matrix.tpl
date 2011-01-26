{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
	<tr>
		<td>
			{t}characteristics{/t}
			<select size="100" class="matrix-list-select" id="characteristics" onchange="matrixCharacteristicsChange()">
			{section name=i loop=$characteristics}
			<option value="{$characteristics[i].id}" ondblclick="window.open('char.php?id={$characteristics[i].id}','_self');">{$characteristics[i].characteristic} ({$characteristics[i].type})</option>
			{/section}
			</select>
		</td>
		<td></td>
		<td>
			{t}taxa{/t}
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
	</tr>
	<tr>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="{t}add new{/t}" onclick="window.open('char.php','_self');" />
			<input 
				type="button" 
				class="matrix-button" 
				value="{t}edit/delete selected{/t}"  
				onclick="window.open('char.php?id='+$('#characteristics').val(),'_self');" />
		</td>
		<td></td>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="{t}add new taxon{/t}" onclick="maxtrixTaxonAddClick();" />
			<input type="button" class="matrix-button" value="{t}delete selected taxon{/t}" />
		</td>
	</tr>
	<tr>
		<td colspan="3"  style="height:10px">
	</tr>		
	<tr>
		<td>
			{t}states{/t}
				<select multiple="multiple" id="states" class="matrix-list-select">
			</select>
		</td>
		<td></td>
		<td>
			{t}links{/t}
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
	</tr>		
	<tr>
		<td style="text-align:center">
			<input type="button" class="matrix-button" id="newStateButton" value="{t}add new{/t}" onclick="matrixAddStateClick()" />
			<input type="button" class="matrix-button" value="{t}edit/delete selected{/t}" onclick="window.open('state.php?id='+$('#states').val(),'_self');" />

		</td>
		<td></td>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="{t}add new{/t}" />
			<input type="button" class="matrix-button" value="{t}delete selected{/t}" />
		</td>
	</tr>
</table>





</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
