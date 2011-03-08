{include file="../shared/header.tpl"}

<div id="page-main">
<p>
{t}Choose a matrix to use:{/t}
</p>
	<table>
	{foreach from=$matrices key=k item=v}
	<tr class="highlight">
		<td class="a" style="width:200px" onclick="goMatrix({$v.id})">{t _s1=$v.name}Use "%s"{/t}</td>
	</tr>
	{/foreach}
	</table>
</div>

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
