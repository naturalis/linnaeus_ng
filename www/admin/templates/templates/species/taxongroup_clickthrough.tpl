{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>

	{if $groups}

    <a href="?id=">Start</a>

    <h3>Groups</h3>

      <ul class="group-list">
      {foreach $groups as $entry}
        <li>
            <a href="?id={$entry.id}">{$entry.sys_label}</a>
        </li>
      {/foreach}
      </ul>
	{/if}


	{if $group}
    
    <a href="?id=">Start</a>
    {foreach from=$path item=v}
    {if $v.id==$group.id}
    > {$v.sys_label}
    {else}
    {assign var=back_id value=$v.id}
    > <a href="?id={$v.id}&back=1">{$v.sys_label}</a>
    {/if}
    {/foreach}
    
    <h3>{$group.sys_label}</h3>

      {if $group.groups|@count>0}
      
      <h4>Groups in this group:</h4>

      <ul class="group-list">
      {foreach $group.groups as $entry}
        <li>
            <a href="?id={$entry.id}">{$entry.sys_label}</a>
        </li>
      {/foreach}
      </ul>
      
      {/if}

      <h4>Taxa in this group:</h4>

      <ul class="taxon-list">
      {foreach $group.taxa as $entry}
        <li>
            {if $entry.commonname}
            {$entry.commonname} (<i>{$entry.taxon}</i>; {$entry.rank})
            {else}
            {$entry.taxon} ({$entry.rank})
            {/if}
        </li>
      {/foreach}
      </ul>

      {if $group.taxa|@count==0}(none){/if}

	{/if}

    </p>
    
    <p>
    	<a href="?id={$back_id}&back=1">back</a>
    </p>
    <p>
    	<a href="taxongroups.php">back to index</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function(){

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}