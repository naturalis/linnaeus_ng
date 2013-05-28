{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<p>
	{t}Drag and drop topics to their correct slot and click 'save order'.{/t}
	</p>
    
    <table id="drag-list" class="grid">
        <tbody>
        {foreach from=$pages key=k item=v}
            <tr class="tr-highlight" type="drag-row" drag-id="{$v.id}">
                <td>{$v.topic}</td>
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

</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("#drag-list tbody").sortable({
		helper: fixHelper
	}).disableSelection();

})
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
