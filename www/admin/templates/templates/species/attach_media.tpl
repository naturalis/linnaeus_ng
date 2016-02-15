{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
#container ul {
	list-style: none;
}
#container .buttons {
	margin-bottom: 20px;
}
#container .list li {
	width: 100%;
	border-bottom: 1px dotted #CCC;
	margin-bottom: 10px;
	padding-bottom: 10px;
}
#container .grid li {
	float: left;
	width: 20%;
	height: 50px;
	border-right: 1px dotted #CCC;
	border-bottom: 1px dotted #CCC;
	padding: 20px;
}

</style>

<div id="page-main">
    <div class="buttons">
        <button class="list">List</button>
        <button class="grid">Grid</button>
    </div>

	<form id="theForm" method="post">
    <input type="hidden" name="taxon_id" value="{$taxon.id}" />
    <input type="hidden" id="action" name="action" value="save" />
    <input type="hidden" id="subject" name="subject" value="" />

    <ul class="list">
   <!-- {foreach from=$media item=v} -->
	<li>Item 1</li>
    <li>Item 2</li>
    <li>Item 3</li>
	<li>Item 4</li>
    <li>Item 5</li>
    <li>Item 6</li>
  <!--  {/foreach} -->

    </ul>
    </form>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function() {
	$('button').on('click',function(e) {
	    if ($(this).hasClass('grid')) {
	        $('#theForm ul').removeClass('list').addClass('grid');
	    }
	    else if($(this).hasClass('list')) {
	        $('#theForm ul').removeClass('grid').addClass('list');
	    }
	});
});
</script>
{/literal}
{include file="../shared/admin-footer.tpl"}