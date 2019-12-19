{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<p>
	{t}Drag and drop topics to their correct slot and click 'save order'.{/t}
	</p>
    
    <table id="drag-list" class="grid">
        <tbody>
        {foreach $pages v k}
            <tr class="tr-highlight" type="drag-row" drag-id="{$v.id}">
                <td style="width:450px">{$v.topic}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
	<p>
        <form method="post" action="" id="theForm">
        <input type="hidden" name="rnd" value="{$rnd}" />
        <input type="button" value="save order" onclick="allSaveDragOrder()"/>
        </form>
    </p>

    <p>
    	<a href="javascript:freemodSortAlpha();">Sort topics alphabetically</a><br />
    </p>

</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function()
{
	allInitDragtable();
})
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
