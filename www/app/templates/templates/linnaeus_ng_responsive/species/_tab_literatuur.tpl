{if $content.literature|@count > 0}
    <h2 id="name-header">{t}Literatuur{/t}</h2>
    <ul>
    {foreach $content.literature v k}
    
        {capture authors}
            {foreach from=$v.authors a u}
                {$a.name}{if $v.authors|@count>1 && $u<$v.authors|@count-1}{if $u==$v.authors|@count-2} &{else},{/if}{/if}
            {/foreach}
        {/capture}
    
        <li class="general-list"><a href="../literature2/reference.php?id={$v.id}">
            {if $v.author}
            {$v.author}
            {else}
            {if ($smarty.capture.authors|@trim|@strlen)>0}
            {$smarty.capture.authors|@trim}
            {/if}
            {/if}
            {if $v.date} {$v.date}{/if}{if $v.author_name || $v.date}.</a> {/if}{$v.label}
        </li>
    {/foreach}
    </ul>
    <br />
{/if}

{if $content.inherited_literature|@count > 0}
    <h2>{t}Literatuur gekoppeld aan bovenliggende taxa{/t}</h2>
    <ul>
    {foreach $content.inherited_literature v k}

        {capture authors}
            {foreach from=$v.authors a u}
                {$a.name}{if $v.authors|@count>1 && $u<$v.authors|@count-1}{if $u==$v.authors|@count-2} &{else},{/if}{/if}
            {/foreach}
        {/capture}

        <li class="general-list">
            <a href="../literature2/reference.php?id={$v.id}">
            {if $v.author}
            {$v.author}
            {else}
            {if ($smarty.capture.authors|@trim|@strlen)>0}
            {$smarty.capture.authors|@trim}
            {/if}
            {/if}
            {if $v.date} {$v.date}{/if}{if $v.author_name || $v.date}.{/if}
            </a>
            {$v.label}
            (<a href="?id={$v.referencing_taxon.id}">{$v.referencing_taxon.taxon}</a>)
        </li>
    {/foreach}
    </ul>
{/if}
