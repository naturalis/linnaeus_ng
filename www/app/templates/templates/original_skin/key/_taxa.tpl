<div id="taxa" style="overflow-y:scroll;">
{if $taxa|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
<span id="header">{t _s1=$taxa|@count _s2=$w}%s possible %s remaining:{/t}</span><br/>
{foreach from=$taxa key=k item=v}
{if $useJavascriptLinks}
<span class="a" style="padding-left:3px" onclick="goTaxon({$v.id})">
	{$v.label}
	{if $v.is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
</span>
{else}
<a href="../species/taxon.php?id={$v.id}"  style="padding-left:3px">
	{$v.label}
	{if $v.is_hybrid==1}{$session.app.project.hybrid_marker}{/if}
</a>

{/if}
<br />
{/foreach}
</div>