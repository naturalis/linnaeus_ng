{include file="../shared/admin-header.tpl"}

<style>
a.edit {
	color:#03F;
	font-size:0.8em;
	margin-left:5px;
}
</style>

<div id="page-main">

	<p>
    Groups:

    {function menu level=0}
      <ul class="level{$level}">
      {foreach $data as $entry}
        {if $entry.children}
            <li>
            	{$entry.name}
                <a class="edit" href="taxongroup_taxa.php?id={$entry.id}">edit group</a>
                <a class="edit" href="taxongroup_taxa.php?id={$entry.id}">add taxa</a>
            	<div></div>
	            {menu data=$entry.children level=$level+1}
            </li>
        {else}
            <li>
	            {$entry.name}
                <a class="edit" href="taxongroup_taxa.php?id={$entry.id}">edit group</a>
                <a class="edit" href="taxongroup_taxa.php?id={$entry.id}">add taxa</a>
            	<div></div>
            </li>
        {/if}
      {/foreach}
      </ul>
    {/function}
    
	{menu data=$groups}
	{if $groups|@count==0}(none){/if}
    </p>
    
    <p>
    	<a href="taxongroup.php">create new group</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function(){
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}