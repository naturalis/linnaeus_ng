{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">{t _s1=$matrix.matrix}Editing matrix "%s"{/t}</span>
</p>
<table>
	<tr>
		<td>
			{t}characteristics{/t}
			<select size="100" class="matrix-list-select" id="characteristics" onchange="matrixCharacteristicsChange();" onclick="matrixGetLinks();">
			{section name=i loop=$characteristics}
			<option value="{$characteristics[i].id}" ondblclick="window.open('char.php?id={$characteristics[i].id}','_self');">{$characteristics[i].characteristic} ({$characteristics[i].type.name})</option>
			{/section}
			</select>
		</td>
		<td></td>
		<td>
			{t}taxa{/t}
			<select multiple="multiple" id="taxa" class="matrix-list-select" onclick="matrixGetLinks();">
			{section name=i loop=$taxa}
			<option value="{$taxa[i].id}">{$taxa[i].taxon}</option>
			{/section}
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
			<input type="button" class="matrix-button" value="{t}add new taxon{/t}" onclick="window.open('taxa.php','_self');" />
			<input type="button" class="matrix-button" value="{t}remove selected taxon{/t}" onclick="matrixDeleteTaxon()" />
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
			<select multiple="multiple" id="links" class="matrix-list-select">
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
			<input type="button" class="matrix-button" value="{t}add new{/t}"  onclick="matrixAddLinkClick()" />
			<input type="button" class="matrix-button" value="{t}delete selected{/t}"  onclick="matrixRemoveLink()"  />
		</td>
	</tr>
</table>





</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
