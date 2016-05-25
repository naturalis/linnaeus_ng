{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p style="width: 700px; margin-bottom: 25px;">
        {$references|@count} references that could not be parsed properly after a conversion from
        the original Literature module. The complete text of these references
        has been stored in the author field.
     	<br><br>
     	Please note that when a reference is edited and saved, the reference will disappear from
     	this list, even when it may not have been broken up in the appropriate fields yet!
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
		<div id="lit2-result-list"></div>
	</p>

</div>

{include file="../shared/admin-footer.tpl"}
