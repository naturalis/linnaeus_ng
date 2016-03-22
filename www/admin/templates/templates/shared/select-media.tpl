<div id="page-main">
	{if $module_name != '' && $item_name != ''}
		 {t}to{/t} {$item_name} {t}in{/t} {$module_name}</h3>
		 <p><a href="{$back_url}">back to {$item_name}</a></p>
	{else}
		</h3>
	{/if}

    {if $media.total > 0}
    <div class="buttons">
        <input type="button" class="list" value="{t}list{/t}" />
        <input type="button" class="grid" value="{t}grid{/t}" />
    </div>
    {/if}

	<form id="mediaForm" method="post">
    <input type="hidden" name="module_id" value="{$module_id}" />
    <input type="hidden" name="back_url" value="{$back_url}" />
    <input type="hidden" name="item_id" value="{$item_id}" />
    <input type="hidden" id="action" name="action" value="edit" />

    <p>{t}A total of{/t} {$media.total} {t}images has been uploaded for this project{/t}.

    {if $media.total > 0}
	    <ul class="list">
	    <li class="header">
	    	<div class="list-grid-info bold">file name</div>
	    	<div class="list-info">
	 			<span class="column bold">title</span>
	 			<span class="column bold">modified</span>
	 			<span class="column bold">dimension</span>
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
	 			<span class="column">{$v.title}</span>
	 			<span class="column">{$v.modified}</span>
	 			<span class="column">{$v.width} x {$v.height} px</span>
	 		</div>
	 	</li>
	 	</label>
	    {/foreach}
	    </ul>
		<div class="clear" />

		{if $module_name != '' && $item_name != ''}
			 <input type="submit" id="attach" value="{t}attach{/t}" />
		{else}
		    <input type="submit" id="edit" value="{t}edit{/t}" />
		    <input type="submit" id="delete" value="{t}delete{/t}" />
		{/if}

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
	    }
	    else if($(this).hasClass('list')) {
	        $('#mediaForm ul').removeClass('grid').addClass('list');
	    }
	});

	$('input:submit#delete').on('click',function() {
		if (!allDoubleDeleteConfirm('the selected media files','this project')) return;
		// set action to delete
		$('input[name=action]').val('delete');
	});

	$('input:submit#edit').on('click',function() {
		$('#mediaForm').attr('action', 'edit.php?' +
			$('#mediaForm input:checked').attr('id').replace('id_', 'id='));
	});

	$('input:submit#attach').on('click',function() {
		$('input[name=action]').val('attach');
	});


});
</script>
{/literal}