{include file="../shared/header.tpl"}

<div id="page-main">

	<div id="alphabet">

	<input type="hidden" id="letter" name="letter" value="{$letter}" />

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
	
	
	<div>
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		{if
			($v.source =='synonym' && $taxonType=='lower') ||
			($v.lower_taxon==1 && $taxonType=='lower') ||
			($v.lower_taxon==0 && $taxonType=='higher')
		}
		<tr class="highlight">
		{if $useJavascriptLinks}
			<td class="species-name-cell" onclick="goTaxon({$v.id})">
				<span class="a">{$v.label}</span>
				{if $v.source =='synonym' && $names[$v.id].label!=''}<span class="synonym-addition"> ({$names[$v.id].label})</span>{/if}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</td>
		{else}
			<td>
				<a href="../species/taxon.php?id={$v.id}">{$v.label}</a>
				{if $v.source =='synonym' && $names[$v.id].label!=''}<span class="synonym-addition"> ({$names[$v.id].label})</span>{/if}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</td>
		{/if}
			<td>{if $v.source =='synonym'}{t}[syn.]{/t}{else}({$v.rank}){/if}</td>
		</tr>
		{/if}
		{/foreach}
		</table>
	</div>
	{if $usePagination}
		{if $useJavascriptLinks}
			{if $prevStart!=-1}
			<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
			{/if}
		{else}
			{if $prevStart!=-1}
			<a href="?start={$prevStart}&letter={$letter}">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<a href="?start={$nextStart}&letter={$letter}">{t}next{/t} ></span>
			{/if}
		{/if}
	{/if}
</div>

{include file="../shared/footer.tpl"}
