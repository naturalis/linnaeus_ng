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
	margin-bottom: 3px;
	padding: 3px;
}
#page-main .grid li {
	float: left;
	width: 20%;
	height: 150px;
	border-right: 1px dotted #CCC;
	border-bottom: 1px dotted #CCC;
	padding: 20px;
}
#page-main .grid li img {
	max-width: 150px;
	max-height: 130px;
}
#page-main .list li .list-grid-info {
	display: inline-block;
	width: 300px;
}
#page-main .grid li .file-name {
	display: block;
}
#page-main .grid li .list-info, #page-main .list li .thumbnail,
	#page-main .grid li.header {
	display: none;
}
#page-main .list li .list-info {
	display: inline;
}
#page-main .list li .column {
	display: inline-block;
	width: 15%;
	padding-right: 20px;
}
.clear {
	clear:both;
}
.bold {
	font-weight: bold;
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

    {if $media.total > 0}
	    {t}Selected images will be attached{/t}.</p>

	    <ul class="list">
	    <li class="header">
	    	<div class="list-grid-info bold">file name</div>
	    	<div class="list-info">
	 			<span class="column bold">modified</span>
	 			<span class="column bold">dimension</span>
	 			<span class="bold">title</span>
	 		</div>
	 	</li>
		{foreach from=$media.images item=v}
		<label>
	 	<li id="li_rs_id_{$v.rs_id}">
	 		<div class="list-grid-info">
				<input type="checkbox" name="rs_id_{$v.rs_id}" id="rs_id_{$v.rs_id}" {if $v.attached==1}checked disabled{/if}>
	 			<img class="thumbnail" src="{$v.thumbnails.normal}" />
	 			<span class="file-name">{$v.file_name}</span>
	 		</div>
	 		<div class="list-info">
	 			<span class="column">{$v.modified}</span>
	 			<span class="column">{$v.height} x {$v.width} px</span>
	 			<span>{$v.title}</span>
	 		</div>
	 	</li>
	 	</label>
	    {/foreach}
	    </ul>
		<div class="clear" />
	    <input type="button" class="list" value="{t}attach selected{/t}" />
	    </form>

	{else}
		You must first <a href="upload.php">upload images to attach</a>.</p>
	{/if}

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