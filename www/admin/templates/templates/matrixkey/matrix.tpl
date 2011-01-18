{include file="../shared/admin-header.tpl"}
{literal}
<style>
.matrix-list-select {
	border:1px solid black;
	width:420px;
	height:220px;
}
.matrix-button {
	font-size:11px;
}
</style>
{/literal}
<div id="page-main">
<table>
	<tr>
		<td>
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
		<td></td>
		<td>
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
	</tr>
	<tr>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="add new characteristic"  onclick="maxtrixCharacteristicAdd()" />
			<input type="button" class="matrix-button" value="delete selected characteristic" />
		</td>
		<td></td>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="add new taxon" onclick="maxtrixTaxonAdd();" />
			<input type="button" class="matrix-button" value="delete selected taxon" />
		</td>
	</tr>
	<tr>
		<td colspan="3"  style="height:10px">
	</tr>		
	<tr>
		<td>
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
		<td></td>
		<td>
			<select multiple="multiple" class="matrix-list-select">
			</select>
		</td>
	</tr>		
	<tr>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="add new state" />
			<input type="button" class="matrix-button" value="delete selected state" />
		</td>
		<td></td>
		<td style="text-align:center">
			<input type="button" class="matrix-button" value="add new link" />
			<input type="button" class="matrix-button" value="delete selected link" />
		</td>
	</tr>
</table>





</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
