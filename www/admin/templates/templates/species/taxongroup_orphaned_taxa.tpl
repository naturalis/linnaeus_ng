{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<h3>Taxa not in any group</h3>

    {function menu level=0}
      <ul>
      {foreach $data as $entry}
            <li>
                <span id="span{$entry.id}" class="{if $entry.group_memberships|@count>0}non-zero{/if}">
                    {if $entry.commonname}
                    {$entry.commonname} (<i>{$entry.taxon}</i>; {$entry.rank})
                    {else}
                    {$entry.taxon} ({$entry.rank})
                    {/if}
                </span>
				{if $entry.group_memberships|@count==0}
                <a href="#" id="add{$entry.id}" class="edit" onclick="showGroupSelector({$entry.id});return false;">add</a>
                {/if}
                <span id="groups{$entry.id}" class="non-zero" style="font-size:0.8em">
                {foreach $entry.group_memberships as $group name=gm}
                {if $smarty.foreach.gm.iteration==1}[{/if}{if $smarty.foreach.gm.iteration>1} / {/if}{$group.sys_label}{if $smarty.foreach.gm.iteration==$entry.group_memberships|@count}]{/if}
                {/foreach}
                </span>
                {if $entry.children}{menu data=$entry.children level=$level+1}{/if}
            </li>
      {/foreach}
      </ul>
    {/function}
    <div id="all-taxa">
    {menu data=$taxa}
    </div>
    <p>
    	<a href="taxongroups.php">back</a>
    </p>

</div>

<div id="group-list" style="border:1px solid #666;padding:2px;background-color:white;width:250px;height:350px;overflow-x:hidden;overflow-y:scrollbar;display:none"></div>

<script type="text/JavaScript">
$(document).ready(function(){
    {function register level=0}
	{foreach $data as $entry}
	registerGroup( { id:{$entry.id}, sys_label:'{$entry.sys_label|@escape}',level: {$level} } );
	{if $entry.children}{register data=$entry.children level=$level+1}{/if}
	{/foreach}
    {/function}
	{register data=$groups}

	$('body').on('keyup',function(e) { if (e.keyCode==27) closeGroupSelector() } );
	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}