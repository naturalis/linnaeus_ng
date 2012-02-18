{include file="../shared/admin-header.tpl"}

<div id="alphabet">
{if $alpha|@count!=0}
{t}Click to browse:{/t}&nbsp;
{foreach name=loop from=$alpha key=k item=v}
{if $v==$letter}
<span class="alphabet-active-letter">{$v}</span>
{else}
<span class="alphabet-letter" onclick="$('#letter').val('{$v}');$('#theForm').submit();">{$v}</span>
{/if}
{/foreach}
{/if}
</div>

<div id="page-main">
	<div id="index">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td>
				<a href="../{if $taxonType=='higher'}highertaxa{else}species{/if}/edit.php?id={$v.id}">{$v.label}</a>
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.admin.project.hybrid_marker}</span>{/if}
			</td>
			<td>({$ranks[$v.rank_id].rank})</td>
		</tr>
		{/foreach}
		</table>
	</div>
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
<form name="theForm" id="theForm" method="post" action="">
<input type="hidden" id="letter" name="letter" value="{$letter}" />
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
