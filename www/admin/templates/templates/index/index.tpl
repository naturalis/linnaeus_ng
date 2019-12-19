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
				<a href="../species/taxon.php?id={$v.id}">{$v.label}</a>
			</td>
			<td>({$ranks[$v.rank_id].rank})</td>
		</tr>
		{/foreach}
		</table>
	</div>

</div>
<form name="theForm" id="theForm" method="get" action="">
<input type="hidden" id="letter" name="letter" value="{$letter}" />
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
