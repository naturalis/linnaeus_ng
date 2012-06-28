<div id="categories">
<ul>
<li>
    <a class="category{if $subject=='Welcome'}-active{/if} category-first" 
    {if $useJavascriptLinks}href="javascript:goContentPage('Welcome')"{else}href="../linnaeus/content.php?sub=Welcome"{/if}>
    {t}Welcome{/t}</a>
</li>
<li>
    <a class="category{if $subject=='Contributors'}-active{/if}" 
    {if $useJavascriptLinks}href="javascript:goContentPage('Contributors')"{else}href="../linnaeus/content.php?sub=Contributors"{/if}>
    {t}Contributors{/t}</a>
</li>
<li>
    <a class="category{if $subject=='About ETI'}-active{/if} category-last" 
    {if $useJavascriptLinks}href="javascript:goContentPage('Welcome')"{else}href="../linnaeus/content.php?sub=About ETI"{/if}>
    {t}About ETI{/t}</a>
</li>
</ul>
</div>
