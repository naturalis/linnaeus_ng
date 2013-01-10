{include file="../shared/header.tpl"}

<div id="page-main">
	<div id="index">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		{if $v.do_display}
		<tr class="highlight">
			{if $useJavascriptLinks}			
			<td class="a" onclick="goTaxon({$v.id})">{$v.taxon}</td>
			{else}
			<td><a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a></td>
			{/if}
			<td>({$v.rank})</td>
		</tr>
		{/if}
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
	{if $useJavascriptLinks}
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	{else}
		{if $prevStart!=-1}
		<a href="?start={$prevStart}">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<a href="?start={$nextStart}">{t}next{/t} ></span>
		{/if}
	{/if}
	</div>
{/if}
</div>

{include file="../shared/footer.tpl"}
