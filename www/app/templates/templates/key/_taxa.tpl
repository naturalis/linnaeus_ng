<div id="taxa" style="overflow-y:scroll;">
{if $taxa|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
<span id="header">{t _s1=$taxa|@count _s2=$w}%s possible %s remaining:{/t}</span><br/>
{foreach from=$taxa key=k item=v}
<span class="a" style="padding-left:3px" onclick="goTaxon({$v.id})">
	{$v.taxon}
	{if $v.is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
</span><br />
{/foreach}
</div>