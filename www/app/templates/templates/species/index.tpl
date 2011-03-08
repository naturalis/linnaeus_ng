{include file="../shared/header.tpl"}

<div id="page-main">
	<div id="index">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td class="a" onclick="goTaxon({$v.id})">{$v.taxon}</td>
			<td>({$v.rank})</td>
		</tr>
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< previous</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">next ></span>
		{/if}
	</div>
{/if}
</div>

{include file="../shared/footer.tpl"}
