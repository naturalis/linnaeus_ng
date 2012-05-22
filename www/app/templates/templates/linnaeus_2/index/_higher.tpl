    <div id="content">
    {foreach name=taxonloop from=$taxa key=k item=v}
        {if
            ($v.source =='synonym' && $taxonType=='lower') ||
            ($v.lower_taxon==1 && $taxonType=='lower') ||
            ($v.lower_taxon==0 && $taxonType=='higher')
        }
        <p>
        {if $useJavascriptLinks}
            <a class="internal-link" href="javascript:goTaxon({$v.id})">{$v.label}, {$v.rank}</a>
        {else}
            <a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.label}, {$v.rank}</a>
        {/if}
        {if $v.source =='synonym' && $names[$v.id].label!=''}
            <span class="synonym-addition"> ({$names[$v.id].label})</span>
        {/if}
        {if $v.is_hybrid==1}
            <span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>
        {/if}
        {if $v.source =='synonym'}{t}[syn.]{/t}{/if}
        </p>
        {/if}
     {/foreach}
    </div>