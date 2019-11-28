<p>
    {if $content.literature|@count > 0}
        <h3 id="name-header">{t}Literature{/t}</h3>
        <ul>
        {foreach $content.literature v k}
            <li>{$v.formatted}</li>
        {/foreach}
        </ul>
        <br />
    {/if}
    
    {if $content.inherited_literature|@count > 0}
        <h3>{t}Literature linked to parent taxa{/t}</h3>
        <ul>
        {foreach $content.inherited_literature v k}
        <li>
            {$v.formatted} (<a href="?id={$v.referencing_taxon.id}">{$v.referencing_taxon.taxon}</a>)
        </li>
        {/foreach}
        </ul>
    {/if}
</p>

