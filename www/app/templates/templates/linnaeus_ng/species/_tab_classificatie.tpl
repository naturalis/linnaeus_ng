<br style="clear:all" />

<p>
<b>{t}Classification{/t}</b><br />
{foreach $content.classification v k classification}
    <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{$v.name}</a>{if $v.rank_label} [{$v.rank_label}]{/if}
    {* if $smarty.foreach.classification.last || $v.is_empty==1}
        {$v.label}
    {else}
        <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{$v.name}</a>
    {/if *}
    <br />
{/foreach}
</p>

{if $content.taxonlist|@count>0}
<p>
<b>{$taxon.label} {t}contains the following taxa{/t}:</b><br/>

{foreach $content.taxonlist v k list}
    <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{$v.taxon}</a> {if $v.rank_label}[{$v.rank_label}]{/if}
    {if $v.commonname} ({$v.commonname}){/if}
    <br />
{/foreach}
</p>
{/if}