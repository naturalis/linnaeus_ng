{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
#page-main {
	overflow: auto;
	width: 100%;
}
#page-main ul {
	list-style: none;
}
#page-main .buttons {
	margin-bottom: 20px;
}
#page-main .list li {
	width: 100%;
	border-bottom: 1px dotted #CCC;
	margin-bottom: 10px;
	padding-bottom: 10px;
}
#page-main .grid li {
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
        <input type="button" class="list" value="{t}list{/t}" />
        <input type="button" class="grid" value="{t}grid{/t}" />
    </div>

	<form id="theForm" method="post">
    <input type="hidden" name="taxon_id" value="{$taxon.id}" />
    <input type="hidden" id="action" name="action" value="save" />
    <input type="hidden" id="subject" name="subject" value="" />

    <ul class="list">

	{foreach from=$media.images item=v}
 	<li><img src="{$v.thumbnails.small}" />{$v.file_name}</li>
    {/foreach}

    </ul>
    </form>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function() {
	$('input:button').on('click',function(e) {
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