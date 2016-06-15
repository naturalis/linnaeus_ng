<b>{t}Classification{/t}</b>
<p>
    <ul>
    {foreach $content.classification v k}
	{if $v.name}
        <li><a href="../species/nsr_taxon.php?id={$v.id}">{$v.name}</a>{if $v.rank_label} [{$v.rank_label}]{/if}</li>
	{/if}
    {/foreach}
    </ul>
</p>    

{if $content.taxonlist|@count>0}

<b>{$taxon.label} {t}contains the following taxa{/t}:</b>
<p>
    <ul>
    {foreach $content.taxonlist v k}
        <li><a href="../species/nsr_taxon.php?id={$v.id}">{$v.name}</a>{if $v.rank_label} [{$v.rank_label}]{/if}</li>
    {/foreach}
    </ul>
</p>    

{/if}