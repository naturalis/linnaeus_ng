{include file="../shared/header.tpl"}

<div id="page-main">

	<div id="alphabet">
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	{if $hasNonAlpha}
		{assign var=l value=$letter|ord}
		{if $l < 97 || $l > 122}
		<span class="alphabet-active-letter">#</span>
		{else}
		<span class="alphabet-letter" onclick="$('#letter').val('#');$('#theForm').submit();">#</span>
		{/if}
	{/if}

	{section name=foo start=97 loop=123 step=1}
	  {assign var=l value=$smarty.section.foo.index|chr}
		{if $l==$letter}
		<span class="alphabet-active-letter">{$l|upper}</span>
		{elseif $alpha[$l]}
		<span class="alphabet-letter" onclick="$('#letter').val('{$l}');$('#theForm').submit();">{$l|upper}</span>
		{else}
		<span class="alphabet-letter-ghosted">{$l|upper}</span>
		{/if}
	{/section}
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
				<span class="a{if $v.source =='synonym'} italics{/if}">{$v.label}</span>
				{if $v.source =='synonym' && $names[$v.id].label!=''}[syn. <span class="synonym-addition">{$names[$v.id].label}</span>]{/if}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</td>
		{else}
			<td>
				<a {if $v.source =='synonym'} class="italics"{/if} href="../species/taxon.php?id={$v.id}">{$v.label}</a>
				{if $v.source =='synonym' && $names[$v.id].label!=''}[syn. <span class="synonym-addition">{$names[$v.id].label}</span>]{/if}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</td>
		{/if}
			<!-- td>{if $v.source !='synonym'}({$v.rank}){/if}</td-->
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
	{else}
		{if $useJavascriptLinks}
			{if $alphaNav.prev}
			<span class="a" onclick="$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<span class="a" onclick="$('#letter').val('{$alphaNav.next}');$('#theForm').submit();">{t}next{/t} ></span>
			{/if}
		{else}
			{if $alphaNav.prev}
			<a href="javascript:$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();">< {t}previous{/t}</span>
			{/if}
			{if $alphaNav.next}
			<a href="javascript:$('#letter').val('{$alphaNav.next}');$('#theForm').submit();">{t}next{/t} ></span>
			{/if}
		{/if}
	{/if}
</div>

{include file="../shared/footer.tpl"}
