{include file="../shared/admin-header.tpl"}

<style>
h3 {
    font-style: normal;
	margin:1px 0 10px 0;
}
</style>

<div id="page-main">

{if !$taxa || $taxa|@count==0}
    <p>
		no values
    </p>
{else}
    <p>
    
    	<h3>
        	{if $group.name}{$group.name}{else}{$group.sysname}{/if}:
        	{if $taxa[0].trait_name}{$taxa[0].trait_name}{else}{$taxa[0].trait_sysname}{/if}
		</h3>
   
        <ul>
        {foreach $taxa v}
            <li><a href="taxon.php?id={$v.taxon_id}&group={$v.trait_group_id}">{$v.taxon}</a>: 
                {$v.value_start}
                {if $v.value_end}-{$v.value_end}{/if}
                {$v._date_value}
                {if $v._date_value_end}-{$v._date_value_end}{/if}
			</li>
            
            
        {/foreach}
        </ul>
    </p>
{/if}


    <p>
    	<a href="traitgroup_trait.php?id={$taxa[0].trait_id}">back</a>&nbsp;&nbsp;
    	<a href="index.php">index</a><br />
    </p>


</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


