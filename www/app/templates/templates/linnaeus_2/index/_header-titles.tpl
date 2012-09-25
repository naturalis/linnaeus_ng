<div id="header-titles"></div>
<div id="categories">
<ul>
<li>
    <a class="category{if $taxonType=='lower'}-active{/if} category-first{if $session.app.user.indexModule.hasSpecies==1}" href="index.php"{else} category-no-content"{/if}>
    {t}Species and lower taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}{if $session.app.user.indexModule.hasHigherTaxa==1}" href="higher.php"{else} category-no-content"{/if}>
    {t}Higher taxa{/t}</a>
 </li>
<li>
    <a class="category{if $taxonType=='common'}-active{/if} category-last{if $session.app.user.indexModule.hasCommonNames==1}" href="common.php"{else} category-no-content"{/if}>
    {t}Common names{/t}</a>
</li>
</ul>
</div>

