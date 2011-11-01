{include file="../shared/header.tpl"}

<div id="page-main">
	<div>
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		{if
			($v.source =='synonym' && $taxonType=='lower') ||
			($v.lower_taxon==1 && $taxonType=='lower') ||
			($v.lower_taxon==0 && $taxonType=='higher')
		}
		<tr class="highlight">
			<td class="species-name-cell" onclick="goTaxon({$v.id})">
				<span class="a">{$v.label}</span>
				{if $v.source =='synonym' && $names[$v.id].label!=''}<span class="synonym-addition"> ({$names[$v.id].label})</span>{/if}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.project.hybrid_marker}</span>{/if}
			</td>
			<td>{if $v.source =='synonym'}{t}[syn.]{/t}{else}({$v.rank}){/if}</td>
		</tr>
		{/if}
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

{include file="../shared/footer.tpl"}
