<div id="header-titles">
    <span id="header-title"></span>
</div>

<div id="categories">
<ul>
<li>
    <a class="category{if $type=='lower'}-active{/if} category-first{if $hasNameTypes.hasSpecies==1}" href="index.php"{else} category-no-content"{/if}>
    {t}Species and lower taxa{/t}</a>
</li>
<li>
    <a class="category{if $type=='higher'}-active{/if}{if $hasNameTypes.hasHigherTaxa==1}" href="higher.php"{else} category-no-content"{/if}>
    {t}Higher taxa{/t}</a>
 </li>
<li>
    <a class="category{if $type=='common'}-active{/if} category-last{if $hasNameTypes.hasCommonNames==1}" href="common.php"{else} category-no-content"{/if}>
    {t}Common names{/t}</a>
</li>
</ul>
</div>

