{$_page_start_tpl__filter_content_placeholder='Search taxa'}

{include file="../shared/header.tpl"}

<div id="page-main">
	<div id="index">
		<table>
		{foreach $taxa v k}
		{if $v.do_display}
		<tr class="highlight">
			<td><a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a></td>
			<td>({$v.rank})</td>
		</tr>
		{/if}
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
	{if $prevStart!=-1}
		<a href="?start={$prevStart}">< {t}previous{/t}</span>
	{/if}
	{if $nextStart!=-1}
		<a href="?start={$nextStart}">{t}next{/t} ></span>
	{/if}
	</div>
{/if}
</div>

{include file="../shared/footer.tpl"}
