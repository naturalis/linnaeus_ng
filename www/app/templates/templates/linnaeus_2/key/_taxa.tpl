<div id="taxa">
{if $taxa|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
<p id="header">{t _s1=$taxa|@count _s2=$w}%s possible %s remaining:{/t}</p>
{foreach from=$taxa key=k item=v}
{if $useJavascriptLinks}
    <p class="a" onclick="goTaxon({$v.id})">
    	{$v.taxon}
    	{if $v.is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
    </p>
{else}
    <p><a href="../species/taxon.php?id={$v.id}" >
    	{$v.taxon}
    	{if $v.is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
    </a></p>
{/if}
{/foreach}
</div>