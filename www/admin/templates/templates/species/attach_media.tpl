{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
#page-main {
	overflow: auto;
	width: 100%;
}
#page-main ul {
	list-style: none;
	margin: 30px 0;
	padding: 0;
	overflow: auto;
}
#page-main .buttons {
	margin-bottom: 20px;
}
#page-main .list li {
	width: auto;
	border-bottom: 1px dotted #CCC;
	margin-bottom: 10px;
	padding: 10px;
}
#page-main .grid li {
	float: left;
	width: 20%;
	height: 120px;
	border-right: 1px dotted #CCC;
	border-bottom: 1px dotted #CCC;
	padding: 20px;
}
#page-main .list li img {
	max-height: 40px;
}
#page-main .grid li img {
	max-width: 150px;
	max-height: 100px;
}
#page-main .list li .thumb {
	display: inline-block;
	width: 300px;
}
#page-main .grid li .file-name {
	display: block;
}
#page-main .grid li .list-info {
	display: none;
}
.clear {
	clear:both;
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

    <p>{t}A total of{/t} {$media.total} {t}images has been uploaded for this project{/t}.
    {t}Selected images will be attached, deselected images detached{/t}.</p>

    <ul class="list">

	{foreach from=$media.files item=v}
	<label>
 	<li id="li_rs_id_{$v.rs_id}">

 		<div class="thumb">
			<input type="checkbox" name="rs_id_{$v.rs_id}" id="rs_id_{$v.rs_id}" {if $v.attached==1}checked{/if}>
 			<img src="{$v.thumbnails.normal}" />
 			<span class="file-name">{$v.file_name}</span>
 		</div>
 		<span class="list-info">size: {$v.height} x {$v.width}; title: {$v.title}</span>

 	</li>
 	</label>
    {/foreach}

    </ul>
	<div class="clear" />
    <input type="button" class="list" value="{t}attach selected{/t}" />
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