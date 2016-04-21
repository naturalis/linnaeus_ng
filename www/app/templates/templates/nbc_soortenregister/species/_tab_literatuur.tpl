<p>
    <h2 id="name-header">{t}Literatuur{/t}</h2>

        <ul>
        {foreach $content.literature v k}
        <li><a href="../literature2/reference.php?id={$v.id}">{if $v.author_name}{$v.author_name}, {/if}{$v.label}{if $v.date} ({$v.date}){/if}</a></li>
        {/foreach}
        </ul>
        
        {if $content.inherited_literature|@count > 0}
        <p>
            <h2>{t}Literatuur over bovenliggende taxa{/t}</h2>
            <ul>
            {foreach $content.inherited_literature v k}
            <li>
            	<a href="../literature2/reference.php?id={$v.id}">{if $v.author_name}{$v.author_name}, {/if}{$v.label}{if $v.date} ({$v.date}){/if}</a>
	            ({$v.referencing_taxon.taxon})
			</li>
            {/foreach}
            </ul>
        </p>
        {/if}
</p>

