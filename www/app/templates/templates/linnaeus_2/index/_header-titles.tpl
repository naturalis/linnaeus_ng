<div id="header-titles"></div>
<div id="categories">
<ul>
<li>
    <a class="category{if $taxonType=='lower'}-active{/if} category-first" 
    href="index.php">
    {t}Species and lower taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}" 
    href="higher.php">
    {t}Higher taxa{/t}</a>
 </li>
<li>
    <a class="category{if $taxonType=='common'}-active{/if} category-last" 
    href="common.php">
    {t}Common names{/t}</a>
</li>
</ul>
</div>

