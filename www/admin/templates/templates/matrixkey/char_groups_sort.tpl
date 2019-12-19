{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
		{t}Drag and drop to alter display order of groups and non-grouped characters:{/t}
    </p>

    <div>

        <ul id="order-sort" class="sortable-drag-list">

        {foreach from=$menuorder item=v key=k}
            <li id="order-{if $v.type=='group'}group{else}char{/if}-{$v.id}" class="ui-state-default">{if $v.type=='group'}[ {/if}{if $v.short_label}{$v.short_label}{else}{$v.label}{/if}{if $v.type=='group'} ]{/if}</li>
        {/foreach}

        </ul>
	
	</div>

	<div style="clear:both;border-bottom:1px dotted #ddd;width:500px;margin-bottom:10px"></div>

    
    <form id="theForm" method="post">
    <p>
	    <input type="button" value="save" onclick="matrixSaveCharGroupOrder()"/>
    </p>
    </form>
    
    <a href="char_groups.php">{t}Organize characters in groups{/t}</a>

    </div>

</div>

{literal}
<script type="text/javascript">
$(document).ready(function()
{
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
