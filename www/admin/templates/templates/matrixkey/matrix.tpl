{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$matrix.id}" />
<input type="hidden" name="action" id="action" value="" />
<table>
	<tr>
		<td>
			{t}Matrix name:{/t}
		</td>
		<td>
			<input type="text" name="matrix" id="matrix" value="{$matrix.matrix}" maxlength="64" />
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="{t}save{/t}" />&nbsp;
			{if $matrix.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}