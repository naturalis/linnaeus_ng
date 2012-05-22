    <div id="content">
        <!--
        <table>
        {foreach name=taxonloop from=$taxa key=k item=v}
        {if
            ($v.source =='synonym' && $taxonType=='lower') ||
            ($v.lower_taxon==1 && $taxonType=='lower') ||
            ($v.lower_taxon==0 && $taxonType=='higher')
        }
        <tr class="highlight">
        {if $useJavascriptLinks}
            <td class="species-name-cell" onclick="goTaxon({$v.id})">
                <span class="a">{$v.label}</span>
                {if $v.source =='synonym' && $names[$v.id].label!=''}<span class="synonym-addition"> ({$names[$v.id].label})</span>{/if}
                {if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
            </td>
        {else}
            <td>
                <a href="../species/taxon.php?id={$v.id}">{$v.label}</a>
                {if $v.source =='synonym' && $names[$v.id].label!=''}<span class="synonym-addition"> ({$names[$v.id].label})</span>{/if}
                {if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
            </td>
        {/if}
            <td>{if $v.source =='synonym'}{t}[syn.]{/t}{else}({$v.rank}){/if}</td>
        </tr>
        {/if}
        {/foreach}
        </table>
        -->
        
        
    
    {foreach name=taxonloop from=$taxa key=k item=v}
        {if
            ($v.source =='synonym' && $taxonType=='lower') ||
            ($v.lower_taxon==1 && $taxonType=='lower') ||
            ($v.lower_taxon==0 && $taxonType=='higher')
        }
        <p>
        {if $useJavascriptLinks}
            <a class="internal-link" href="javascript:goTaxon({$v.id})">{$v.label}</a>
        {else}
            <a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.label}</a>
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