<h4>{t}Child taxa{/t}</h4>
<p>
    <ul>
    {foreach $content v k}
        <li class-"general-list"><a href="../species/nsr_taxon.php?id={$v.id}">{$v.name}</a>{if $v.rank_label} [{$v.rank_label}]{/if}</li>
    {/foreach}
    </ul>
</p>    
