<div id="taxa">
{if $remaining|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
<p id="header">{t _s1=$remaining|@count _s2=$w}%s possible %s remaining:{/t}</p>

{foreach from=$remaining key=k item=v}
{if $useJavascriptLinks}
    <p class="a" onclick="goTaxon({$v})">
    	{$taxa[$v].label}
    	{if $taxa[$v].is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
    </p>
{else}
    <p><a href="../species/taxon.php?id={$v}" >
    	{$taxa[$v].label}
    	{if $taxa[$v].is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
    </a></p>
{/if}
{/foreach}
</div>