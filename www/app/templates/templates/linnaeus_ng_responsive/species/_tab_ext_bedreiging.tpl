<div>

{foreach $external_content->content_json_decoded val key}
{$wetten[$val->wetenschappelijke_naam]['wetten'][$val->wet][]=['categorie'=>$val->categorie,'publicatie'=>$val->publicatie|@strip_tags|@trim]}
{$wetten[$val->wetenschappelijke_naam]['url']='http://minez.nederlandsesoorten.nl/node/%s'|sprintf:$val->soort_id}
{/foreach}

{if $wetten}

    <h2>{t}Beschermingsstatus{/t}</h2>

    {foreach from=$wetten item=soort key=naam}
    <p>
        {* if $naam!=$names.nomen_no_tags}
        <h3><i>{$naam}</i></h3><br />
        {/if *}
        {if $wetten|@count>1}
        <h3><i>{$naam}</i></h3><br />
        {/if}
        
        <ul>
            {foreach from=$soort.wetten item=v key=wet}
            <li>
                <b>{$wet}</b>
                <ul>
                    {foreach from=$v item=w}
                    <li>
                        {$w.categorie}<br />
                        {$w.publicatie}
                    </li>
                    {/foreach}
                </ul>
            </li>
            {/foreach}
        </ul>
        <br />
        
        {* Zie ook: <a href="{$soort.url}">EL&I wettelijke bescherming, beleid en signalering</a><br /><br /> *}
        {t}Bron:{/t} <a href="{$soort.url}">{if $wetten|@count>1}{$naam}{else}{t}soortgegevens{/t}{/if}</a> uit Beschermde natuur van Nederland: soorten in wetgeving en beleid (Ministerie van Economische Zaken)

    </p>
    {/foreach}
    
{/if}

{if $content}
<h2>{t}Bedreiging en bescherming{/t}</h2>
<p>
    {$content}
</p>
{/if}

</div>