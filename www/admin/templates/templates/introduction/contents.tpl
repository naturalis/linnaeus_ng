{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
{foreach $pages v k}
	<tr class="tr-highlight">
		<td>{if $v.hide_from_index==1}({/if}<a href="edit.php?id={$v.id}">{$v.topic}</a>{if $v.hide_from_index==1}){/if}</td>
	</tr>
{/foreach}
</table>

{if $prevStart!=-1 || $nextStart!=-1}
<div id="navigation">
	{if $prevStart!=-1}
	<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
	{/if}
	{if $nextStart!=-1}
	<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
	{/if}
</div>
{/if}

</div>
<form action="" method="post" id="theForm" action="">
<input type="hidden" name="letter" id="letter" value="" />
</form>

{include file="../shared/admin-footer.tpl"}
