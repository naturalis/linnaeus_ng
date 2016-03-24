<style></style>

<div id="page-main">
	<h3>{t}Browse media on ResourceSpace server{/t}</h3>
	<p>Info about credentials to log in?</p>

    {if $media.total > 0}
    <div class="buttons">
       <input type="button" class="grid" value="{t}grid{/t}" />
       <input type="button" class="list" value="{t}list{/t}" />
    </div>
    {/if}

	<form id="mediaForm" method="post">
    <input type="hidden" name="module_id" value="{$module_id}" />
    <input type="hidden" name="item_id" value="{$item_id}" />
    <input type="hidden" id="action" name="action" value="edit" />

    <p>{t}A total of{/t} {$media.total} {t}media item(s) is stored for this project{/t}.

    {if $media.total > 0}
	    <ul class="{$session.admin.user.media.display}">
	    <li class="header">
	    	<div class="list-grid-info bold">file name</div>
	    	<div class="list-info">
	 			<span class="column bold">modified</span>
	 			<span class="column bold">dimension</span>
	 			<span class="bold">title</span>
	 		</div>
	 	</li>
		{foreach from=$media.files item=v}
		<label>
	 	<li id="li_{$id_type}id_{$id}">
	 		<div class="list-grid-info">
				<input type="checkbox" name="media_ids[{$v.media_id}]" id="id_{$v.media_id}" {if $v.attached==1}checked disabled{/if}>
	 			<a href="{$v.source}" rel="prettyPhoto" title="{$v.file_name}">
	 			<img class="thumbnail" src="{$v.thumbnails.medium}" />
	 			</a>
	 			<span class="file-name">{$v.file_name}</span>
	 		</div>
	 		<div class="list-info">
	 			<span class="column">{$v.modified}</span>
	 			<span class="column">{$v.width} x {$v.height} px</span>
	 			<span>{$v.title}</span>
	 		</div>
	 	</li>
	 	</label>
	    {/foreach}
	    </ul>
		<div class="clear" />

	    </form>

	{else}
		{t}You must first{/t} <a href="upload.php">{t}upload images{/t}</a>.</p>
	{/if}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function() {
	$('input:button').on('click',function(e) {
	    if ($(this).hasClass('grid')) {
	        $('#mediaForm ul').removeClass('list').addClass('grid');
	        saveDisplayPreference('grid');
	    }
	    else if($(this).hasClass('list')) {
	        $('#mediaForm ul').removeClass('grid').addClass('list');
	        saveDisplayPreference('list');
	    }
	});
});
</script>
{/literal}