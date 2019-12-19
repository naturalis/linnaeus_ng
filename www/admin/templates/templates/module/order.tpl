{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
	<tr>
		<th style="width:300px">{t}topic{/t}</th>
		<th colspan="3">{t}change order{/t}</th>
	</tr>
{foreach from=$pages key=k item=v}
	<tr class="tr-highlight">
		<td><a href="edit.php?id={$v.id}">{$v.topic}</a></td>
		<td class="a" style="text-align:center;width:15px" {if $k!=0}onclick="$('#id').val({$v.id});$('#dir').val('up');$('#theForm').submit();"{/if}>{if $k!=0}&uarr;{/if}</td>
		<td class="a" style="text-align:center;width:15px" {if $k!=$pages|@count-1}onclick="$('#id').val({$v.id});$('#dir').val('down');$('#theForm').submit();"{/if}>{if $k!=$pages|@count-1}&darr;{/if}</td>
		<td></td>
	</tr>
{/foreach}
</table>
</div>
<form method="post" id="theForm">
<input type="hidden" id="id" name="id" value="" />
<input type="hidden" id="dir" name="dir" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
