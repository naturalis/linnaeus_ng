{if $content.experts|@count > 0}
    <h2 id="name-header">{t}Experts{/t}</h2>
    <ul>
    {foreach $content.experts v k}
        <li>{$v.label}{if $v.company_of_name} ({$v.company_of_name}){/if}</li>
    {/foreach}
    </ul>
    <br />
{/if}

{if $content.inherited_experts|@count > 0}
    <h2>{t}Experts gekoppeld aan bovenliggende taxa{/t}</h2>
    <ul>
    {foreach $content.inherited_experts v k}
		<li>{$v.label}{if $v.company_of_name} ({$v.company_of_name}){/if} 
		(<a href="?id={$v.referencing_taxon.id}">{$v.referencing_taxon.taxon}</a>)</li>
    {/foreach}
    </ul>
{/if}
