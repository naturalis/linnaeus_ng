<div id="media-main">

	{if $from != 'search'}
		<p style="margin-bottom: 25px;">type to find: <input id="type-to-find" type="text" onkeyup="searchMedia();" name="">  (in file name, tags, metadata)</p>
	{/if}

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

	{if $from != 'search'}
    	<p id="result-message">{t}A total of{/t} {$media.total} {t}media files has been uploaded for this project{/t}.</p>
	{/if}

    {if $media.total > 0}

    	<div id="media-content">

 		{$media.pager}

		<form id="mediaForm" method="post">
	    <input type="hidden" name="module_id" value="{$module_id}" />
	    <input type="hidden" name="back_url" value="{$back_url}" />
	    <input type="hidden" name="item_id" value="{$item_id}" />
	    <input type="hidden" id="action" name="action" value="edit" />

	    <ul class="{$session.admin.user.media.display}">
	    <li class="header">
	    	<div class="list-grid-info bold">{t}file name{/t}</div>
	    	<div class="list-info">
	 			<span class="column bold">{t}title{/t}</span>
	 			<span class="column bold">{t}modified{/t}</span>
	 			<span class="column bold">{t}link (click & copy){/t}</span>
	 		</div>
	 	</li>

	 	<div id="media-files">
		{foreach from=$media.files item=v}
		<label>
	 	<li>
	 		<div class="list-grid-info">
	 			{if $input_type == 'single'}
					<input type="radio" name="media_ids" value="{$v.media_id}" id="id_{$v.media_id}" {if $v.attached==1}checked disabled{/if}>
	 			{else}
					<input type="checkbox" name="media_ids[{$v.media_id}]" id="id_{$v.media_id}" {if $v.attached==1}checked disabled{/if}>
	 			{/if}
	 			{if $v.media_type=="image"}<a href="{$v.source}" rel="prettyPhoto" title="{$v.file_name}">{/if}
		 			<img class="thumbnail" src="{$v.thumbnails.medium}" alt="{$v.file_name}" />
	 			{if $v.media_type=="image"}</a>{/if}
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
	    </div>

	    </ul>
		<div class="clear"></div>

		{if $module_name != '' && $item_name != ''}
			 <input type="submit" id="attach" value="{t}attach{/t}" />
		{else}
		    <input type="submit" id="delete" value="{t}delete{/t}" />
		{/if}

	    </form>

 		{$media.pager}

 		</div>

	{else}

		<p>
		{if $from != 'search'}
			{t}You must first{/t} <a href="upload.php">{t}upload images{/t}</a>.
		{/if}
		</p>

	{/if}

</div>


<div class="inline-templates" id="media-list-tpl">
	<label>
	<li>
		<div class="list-grid-info">
			{if $input_type == 'single'}
				<input type="radio" name="media_ids" value="%MEDIA_ID%" id="id_%MEDIA_ID%" %CHECKED% >
			{else}
				{* <input type="checkbox" name="media_ids[%MEDIA_ID%]" id="id_%MEDIA_ID%" %CHECKED%> *}
				<input type="checkbox" name="media_ids[%MEDIA_ID%]" id="id_%MEDIA_ID%" %CHECKED% >
			{/if}
			%URL_START%
	 			<img class="thumbnail" src="%THUMBNAIL_SRC%" alt="%FILE_NAME%" />
			%URL_END%
			<span class="file-name">%FILE_NAME% (<a href="edit.php?id=%MEDIA_ID%">{t}edit{/t}</a>)</span>
		</div>
		<div class="list-info">
			<span class="column">%TITLE%</span>
			<span class="column">%MODIFIED%</span>
			<span class="column"><input type="text" class="link" value="%SOURCE%"></span>
		</div>
	</li>
	</label>
</div>


{literal}
<script type="text/JavaScript">

var mediaContent;
var resultMessage;
var mediaList;
var media;
var term;
var tpl;

function storeMediaContent () {
	mediaContent = $("#media-content").html();
	resultMessage = $("#result-message").html();
}

function unsetPaginator () {
	$("ul.paginator").remove();
}

function hideDeleteButton () {
	$("#delete").hide();
}

function showDeleteButton () {
	$("#delete").show();
}

function restoreMediaContent () {
	$("#media-content").html(mediaContent);
	$("#result-message").html(resultMessage);
	term = '';
	$('#type-to-find').val('');
}

function searchMedia () {
	term = $('#type-to-find').val();
	if (term.length > 0) {
		unsetPaginator();
		$.ajax({
			url: "ajax_interface.php",
			type: "POST",
			data: ({
				'action': 'type_to_find' ,
				'search': term,
			}),
			success: function (data) {
				media = $.parseJSON(data);
			}
		}).done(function() {
			displayResultMessage();
			//console.dir(media);
			displayMedia();
		});

	} else {
		restoreMediaContent();
	}
}

function displayResultMessage () {
	var message = '';
	if (media.total == 0) {
		hideDeleteButton();
		message = 'No results for term "' + term + '".';
	} else {
		showDeleteButton();
		message = "Showing " + media.total + " result" +
			(media.total == 1 ? '' : 's') + ' for term "' + term +  '".';
		if (media.total == 100) {
			message += " Enter a longer search term to narrow the results.";
		}
	}
	message += " <a href='#' onclick='showAll();'>Show all</a>.";
	$("#result-message").html(message);
}

function showAll () {
	restoreMediaContent();
}

function displayMedia () {
	var output = '';
	for (i = 0; i < media.files.length; i++) {
		var urlStart = '';
		var urlEnd = '';
		var checked = '';
		if (media.files[i].media_type == 'image') {
			urlStart = '<a href="' + media.files[i].source + '" rel="prettyPhoto" title="' +
				media.files[i].file_name + '">';
			urlEnd = '</a>';
		}
		if (media.files[i].attached == '1') {
			checked = 'checked disabled';
		}
		
		console.dir(tpl);
		
		output += tpl.replace(/\%MEDIA_ID\%/g, media.files[i].media_id)
		   .replace(/\%FILE_NAME\%/g, media.files[i].file_name)
		   .replace('%THUMBNAIL_SRC%', media.files[i].thumbnails.medium)
		   .replace('%TITLE%', media.files[i].title)
		   .replace('%MODIFIED%', media.files[i].modified)
		   .replace('%SOURCE%', media.files[i].source)
		   .replace('%URL_START%', urlStart)
		   .replace('%URL_END%', urlEnd)
		   .replace('%CHECKED%', checked);
	}
	$("#media-files").html(output);
	//console.dir(output);
}


$(document).ready(function() {
	acquireInlineTemplates();
	tpl = fetchTemplate('media-list-tpl');
	storeMediaContent();

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

	$('input:text.link').click(function(){
		$(this).select();
	});

});
</script>
{/literal}