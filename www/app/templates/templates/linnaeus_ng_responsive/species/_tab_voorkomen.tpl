{if $content}
<div>

    <h2>{t}Voorkomen{/t}</h2>
    
    <p>
        <table>
            {if $content.presence_label}<tr><td>{t}Status{/t}</td><td>{$content.presence_label}{if $content.presence_information} 
            (<span class="link">
                {$content.presence_index_label}
                <span class="mouseover">
                    <p><b>{$content.presence_index_label|@escape} {$content.presence_information_title|@escape}</b>{$content.presence_information_one_line|@escape}</p>
                </span>
            </span>)
            {/if}</td></tr>{/if}
            {if $content.habitat_label}<tr><td style="white-space:nowrap">{t}Habitat{/t}</td><td>{$content.habitat_label}</td></tr>{/if}
            {if $content.reference_label}<tr><td style="white-space:nowrap">{t}Referentie{/t}</td><td><a href="../literature2/reference.php?id={$content.reference_id}">{$content.reference_label}</a></td></tr>{/if}
            {* if $content.presence82_label}<tr><td>{t}Status 1982{/t}</td><td>{$content.presence82_label}</td></tr>{/if *}
            {if $content.expert_name}<tr><td style="white-space:nowrap">{t}Expert{/t}</td><td>{$content.expert_name}{if $content.organisation_name} ({$content.organisation_name}){/if}</td></tr>{/if}
        </table>
    </p>

    {if $distributionMaps.data|@count>0}
    <h2>{t}Verspreiding{/t}</h2>
    {foreach from=$distributionMaps.data item=v}

    <p style="border-bottom:1px solid #ddd;padding-bottom:4px;">

    <a class="zoomimage" rel="prettyPhoto[gallery]" href="{$taxon_base_url_images_main}{$v.image}" pTitle="{if $v.meta_map_description}{$v.meta_map_description|@ucfirst|@escape}{/if}
    {if $v.meta_map_source && $v.meta_map_description}<br />{/if}{if $v.meta_map_source}{t}Bron:{/t} {$v.meta_map_source}{/if}">
        <img class="verspreidingskaartje" style="max-height:{math equation="round(a/b)" a=500 b=$distributionMaps.data|@count}px;" title="Foto {$v.photographer}" src="{$taxon_base_url_images_main}{$v.image}" />
    </a>
    {if $v.meta_map_description}<br />{$v.meta_map_description|@ucfirst}{/if}
    {if $v.meta_map_source}<br />{t}Bron{/t}: {$v.meta_map_source}{/if}
    
    </p>

    {/foreach}
    {/if}

</div>
{/if}