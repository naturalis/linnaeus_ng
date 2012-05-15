<!--
<div id="indexNanvigationPane">
<table><tr>
<td class="category{if $taxonType=='lower'}-active{/if}" onclick="window.open('index.php','_self')">{t}Species{/t}</td><td class="space"></td>
<td class="category{if $taxonType=='higher'}-active{/if}" onclick="window.open('higher.php','_self')">{t}Higher Taxa{/t}</td><td class="space"></td>
<td class="category{if $taxonType=='common'}-active{/if}" onclick="window.open('common.php','_self')">{t}Common Names{/t}</td><td class="space"></td>
</tr></table>
</div>
-->

<div id="categories">
<ul>
<li>
    <a class="category{if $taxonType=='lower'}-active{/if} category-first" 
    href="javascript:window.open('index.php','_self')">
    {t}Species and lower taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}" 
    href="javascript:window.open('higher.php','_self')">
    {t}Higher Taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='common'}-active{/if} category-last" 
    href="javascript:window.open('common.php','_self')">
    {t}Common names{/t}</a>
</li>
</ul>
</div>
