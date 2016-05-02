<div id="page-main">

    {if $media.total > 0}
	    {if $from == 'search'}<div class="search-results-separator"></div>{/if}
	    <div class="buttons">
	        <input type="button" class="grid" value="{t}grid{/t}" />
	        <input type="button" class="list" value="{t}list{/t}" />
	    </div>
	    {if $from == 'search'}<p>{t}A total of{/t} {$media.total} {t}media files has been found{/t}:</p>{/if}
    {/if}

	{if $module_name != '' && $item_name != ''}
		 <p><a href="{$back_url}">back to {$item_name} ({$module_name})</a></p>
	{/if}

	<form id="mediaForm" method="post">
    <input type="hidden" name="module_id" value="{$module_id}" />
    <input type="hidden" name="back_url" value="{$back_url}" />
    <input type="hidden" name="item_id" value="{$item_id}" />
    <input type="hidden" id="action" name="action" value="edit" />

	{if $from != 'search'}
    	<p>{t}A total of{/t} {$media.total} {t}media files has been uploaded for this project{/t}.</p>
	{/if}

    {if $media.total > 0}
	    <ul class="{$session.admin.user.media.display}">
	    <li class="header">
	    	<div class="list-grid-info bold">{t}file name{/t}</div>
	    	<div class="list-info">
	 			<span class="column bold">{t}title{/t}</span>
	 			<span class="column bold">{t}modified{/t}</span>
	 			<span class="column bold">{t}link (click & copy){/t}</span>
	 		</div>
	 	</li>
		{foreach from=$media.files item=v}
		<label>
	 	<li id="li_{$id_type}id_{$id}">
	 		<div class="list-grid-info">
	 			{if $input_type == 'single'}
					<input type="radio" name="media_ids" value="{$v.media_id}" id="id_{$v.media_id}" {if $v.attached==1}checked disabled{/if}>
	 			{else}
					<input type="checkbox" name="media_ids[{$v.media_id}]" id="id_{$v.media_id}" {if $v.attached==1}checked disabled{/if}>
	 			{/if}
	 			<a href="{$v.source}" rel="prettyPhoto" title="{$v.file_name}">
		 			<img class="thumbnail" src="{$v.thumbnails.medium}" />
	 			</a>
	 			<span class="file-name">{$v.file_name} (<a href="edit.php?id={$v.media_id}">{t}edit{/t}</a>)</span>
	 		</div>
	 		<div class="list-info">
	 			<span class="column">{$v.title}</span>
	 			<span class="column">{$v.modified}</span>
	 			<span class="column"><input type="text" class="link" value="{$v.source}"></span>
	 		</div>
	 	</li>
	 	</label>
	    {/foreach}
	    </ul>
		<div class="clear"></div>

		{if $module_name != '' && $item_name != ''}
			 <input type="submit" id="attach" value="{t}attach{/t}" />
		{else}
		    <input type="submit" id="delete" value="{t}delete{/t}" />
		{/if}

	    </form>

	{else}

		<p>
		{if $from != 'search'}
			{t}You must first{/t} <a href="upload.php">{t}upload images{/t}</a>.
		{else}
			{t}Nothing found (yet)!{/t}
		{/if}
		</p>

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

	$('input:submit#delete').on('click',function() {
		if (!allDoubleDeleteConfirm('the selected media files','this project')) return;
		// set action to delete
		$('input[name=action]').val('delete');
		$('#mediaForm').attr('action', 'select.php');
	});

	$('input:submit#edit').on('click',function() {
		$('#mediaForm').attr('action', 'edit.php?' +
			$('#mediaForm input:checked').attr('id').replace('id_', 'id='));
	});

	$('input:submit#attach').on('click',function() {
		$('input[name=action]').val('attach');
	});

	$('input:text.link').focus().click(function(){
		$(this).select();
	});
});
</script>
{/literal}