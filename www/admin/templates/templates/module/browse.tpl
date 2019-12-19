{include file="../shared/admin-header.tpl"}

<div id="alphabet">
{t}Click to browse:{/t}

{foreach from=$alpha key=k item=v}
{if $v==$letter}
<span class="alphabet-active-letter">{$v}</span>
{else}
<span class="alphabet-letter" onclick="$('#letter').val('{$v}');$('#theForm').submit();">{$v}</span>
{/if}
{/foreach}
<form name="theForm" id="theForm" method="post" action="">
<input type="hidden" name="letter" id="letter" value="{$letter}"  />
</form>
</div>

<div id="page-main">
<table>
	<tr>
		<th style="width:500px">{t}topic{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$refs}
	<tr class="tr-highlight">
		<td>{$refs[i].topic}</td>
		<td>[<a href="edit.php?id={$refs[i].id}">{t}edit{/t}</a>]</td>
	</tr>
{/section}
</table>
<p>
[<a href="edit.php">{t}add new page{/t}</a>]
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
