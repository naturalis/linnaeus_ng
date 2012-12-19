{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
{foreach from=$groups item=v key=k}
<tr class="tr-highlight">
	<td style="width:200px;cursor:pointer;" onclick="matrixToggleGroupChars({$v.id});">{$v.label}</td>
</tr>
<tr id="chars-{$v.id}" style="display:none;">
	<td colspan="2">
		<table>
{foreach from=$v.chars item=c key=e}
<tr>
	<td style="padding-left:10px;">{$c.label}</td>
</tr>
{/foreach}
		</table>
	</td>
</tr>
{/foreach}
</table>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
