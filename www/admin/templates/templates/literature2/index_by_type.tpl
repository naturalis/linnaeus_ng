{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
        <h2>{$publicationType}</h2>
        {$references|@count} referenties
    </p>
    <p>
  
        <ul>
        {foreach $references v}
            <li>
            {capture item}
            {if $v.label}{$v.label}, {/if}
            {if $v.authors|@count>0}
                {foreach $v.authors a}{$a.name}, {/foreach}
            {else}
                {if $v.author}{$v.author}, {/if}
            {/if}
            {if $v.external_link}{$v.external_link}{/if}
            {/capture}
            <a href="edit.php?id={$v.id}">{$smarty.capture.item|rtrim|rtrim:","}</a>
            </li>
        {/foreach}
        </ul>
    
    </p>
            
	<p>
		<a href="publication_types.php">publicatievormen</a>
	</p>

	<p>
		<div id="lit2-result-list"></div>
	</p>

</div>

{include file="../shared/admin-footer.tpl"}
