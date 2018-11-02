{if $content.experts|@count > 0}
    <h3 id="name-header">{t}Experts{/t}</h3>
    <ul>
    {foreach $content.experts v k}
        <li>{$v.label}{if $v.company_of_name} ({$v.company_of_name}){/if}</li>
    {/foreach}
    </ul>
    <br />
{/if}

{if $content.inherited_experts|@count > 0}
    <h3>{t}Experts linked to parent taxa{/t}</h3>
    <ul>
    {foreach $content.inherited_experts v k}
		<li>{$v.label}{if $v.company_of_name} ({$v.company_of_name}){/if}</li>
    {/foreach}
    </ul>
{/if}
