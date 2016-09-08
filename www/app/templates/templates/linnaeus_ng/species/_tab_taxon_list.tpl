<br />
<p>
    <ul>
    {foreach $content v k}
        <li><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a>{if $v.rank_label} [{$v.rank_label}]{/if}</li>
    {/foreach}
    </ul>
</p>    
