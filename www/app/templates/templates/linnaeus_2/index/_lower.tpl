    <div id="content">
    {foreach name=taxonloop from=$taxa key=k item=v}
        {if
            ($v.source =='synonym' && $taxonType=='lower') ||
            ($v.lower_taxon==1 && $taxonType=='lower') ||
            ($v.lower_taxon==0 && $taxonType=='higher')
        }
        <p>
        {if $useJavascriptLinks}
            <a class="internal-link" href="javascript:goTaxon({$v.id})">{$v.label}</a> {$v.author}
        {else}
            <a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.label}</a> {$v.author}
        {/if}
        {if $v.source =='synonym'} &ndash; {t}synonym{/t}{if $names[$v.id].label!=''} {t}of{/t} {$names[$v.id].label}{if $names[$v.id].author} {$names[$v.id].author}{/if}{else}{/if}{/if}
        {if $v.is_hybrid==1}
            <span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>
        {/if}
        </p>
        {/if}
     {/foreach}
     </div>