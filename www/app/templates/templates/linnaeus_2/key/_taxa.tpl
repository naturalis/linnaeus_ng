<div id="panel">
<div id="categories">
<ul>
	<li><a id="rLi" href="javascript:showRemaining();" class="category-first{if $taxaState=='remaining' || $excluded|@count==0} category-active{/if}">{t}Remaining{/t}</a></li>
	<li><a id="eLi" {if $excluded|@count>0}href="javascript:showExcluded();"{/if} class="category-last{if $taxaState=='excluded'} category-active{/if}{if $excluded|@count==0} category-no-content{/if}">{t}Excluded{/t}</a></li>
</ul>
</div>

<div id="taxa">

	<div id="remaining" style="display: {if $taxaState=='remaining' || $excluded|@count==0}block{else}none{/if};">
	{if $remaining|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
	<p id="header">{t _s1=$remaining|@count _s2=$w}%s possible %s remaining:{/t}</p>
	{foreach from=$remaining key=k item=v}
	{if $useJavascriptLinks}
	    <p class="a" onclick="goTaxon({$v.id})">{$v.taxon}</p>
	{else}
	    <p><a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a></p>
	{/if}
	{/foreach}
	</div>

	<div id="excluded" style="display: {if $taxaState=='remaining' || $excluded|@count==0}none{else}block{/if};">
	{if $excluded|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
	<p id="header">{t _s1=$excluded|@count _s2=$w}%s %s excluded:{/t}</p>
	
	{foreach from=$excluded key=k item=v}
	{if $useJavascriptLinks}
	    <p class="a" onclick="goTaxon({$v.id})">{$v.taxon}</p>
	{else}
	    <p><a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a></p>
	{/if}
	{/foreach}
	</div>

</div>
</div>

