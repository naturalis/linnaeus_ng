<p>
<h3>{t}Classification{/t}</h3>
{foreach $content.classification v k classification}
    <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{if $v.name}{$v.name}{else}{$v.taxon}{/if}</a>{if $v.rank_label} [{$v.rank_label}]{/if}
    {* if $smarty.foreach.classification.last || $v.is_empty==1}
        {$v.label}
    {else}
        <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{$v.name}</a>
    {/if *}
    <br />
{/foreach}
</p>

{if $content.taxonlist|@count>0}
<p style="margin-top: 25px;">
<b>{$taxon.label} {t}contains the following taxa{/t}:</b><br/>

{foreach $content.taxonlist v k list}
    <a href="../{if $v.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$v.id}">{$v.taxon}</a> {if $v.rank_label}[{$v.rank_label}]{/if}
    {if $v.commonname} ({$v.commonname}){/if}
    <br />
{/foreach}
</p>
{/if}