<div id="content">
{foreach name=taxonloop from=$taxa key=k item=v}
    {if
        ($v.source =='synonym' && $taxonType=='lower') ||
        ($v.lower_taxon==1 && $taxonType=='lower') ||
        ($v.lower_taxon==0 && $taxonType=='higher')
    }
    <p>
    <a class="internal-link" href="{if $useJavascriptLinks}javascript:goTaxon({$v.id}){else}../species/taxon.php?id={$v.id}{/if}">{$v.label}</a> {$v.author}
    {if $v.source =='synonym'} &ndash; {t}synonym{/t}{if $names[$v.id].label!=''} {t}of{/t} 
    
    <a class="internal-link" href="{if $useJavascriptLinks}javascript:goTaxon({$v.id}){else}../species/taxon.php?id={$v.id}{/if}">{$names[$v.id].label}</a>{if $names[$v.id].author} {$names[$v.id].author}{/if}{else}{/if}{/if}
    {if $v.is_hybrid==1}
        <span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>
    {/if}
    </p>
    {/if}
 {/foreach}
 </div>