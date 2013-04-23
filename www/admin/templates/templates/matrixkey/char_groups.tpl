{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
        Drag characters to the appropriate group and place within the group, and press 'save' to store.<br />
        Characters that should appear by themselves can be placed under 'Not in any group'.
    </p>
    
    <div>

    {foreach from=$groups item=v key=k}
    <ul id="sortable{$k+1}" class="sortable-drag-list connectedSortable">
        <li id="group-{$v.id}" class="ui-state-disabled">{$v.label} <span onclick="matrixDeleteGroup({$v.id})" title="{t}delete group{/t}" style="margin-left:5px;color:red;cursor:pointer">x</span></li>
        {foreach from=$v.chars item=c key=e}
            <li id="char-{$c.id}" class="ui-state-default">{$c.short_label}</li>
        {/foreach}
    </ul>
    {/foreach}

	<div style="clear:both;"></div>

    <ul id="sortable0" class="sortable-drag-list connectedSortable">
        <li id="group-0" class="ui-state-disabled">{t}Not in any group{/t}</li>
        {foreach from=$characteristics item=c key=e}
            <li id="char-{$c.id}" class="ui-state-default">{$c.short_label}</li>
        {/foreach}
    </ul>

	<div style="clear:both;border-bottom:1px dotted #ddd;width:500px;margin-bottom:10px"></div>
    
    <form id="theForm" method="post">
	Create new group: <input type="text" name="new" />
    <p>
	    <input type="button" value="save" onclick="matrixSaveCharGroupOrder()"/>
    </p>
    </form>

    <a href="char_groups_sort.php">Change the display order of groups</a>
    
    </div>


</div>

{literal}
<script type="text/javascript">
$(document).ready(function(){

	$("[id^=sortable]").sortable({
		opacity: 0.6, 
		cursor: 'move',
		items: "li:not(.ui-state-disabled)",
		connectWith: ".connectedSortable"
	}).disableSelection();

  $( "#order-sort" ).sortable().disableSelection();
	
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
