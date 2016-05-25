<style>
	.ui-widget, .ui-widget input {
		font-family: Trebuchet MS,​Arial,​Helvetica,​sans-serif;
		font-size: 12px;
	}
	#media-main {
		width: 100%;
	}
</style>

<div id="media-main">

    {if $media.total > 0}
	    {if $from == 'search'}<div class="search-results-separator"></div>{/if}
	    <div class="buttons">
	        <input type="button" class="grid" value="{t}grid{/t}" />
	        <input type="button" class="list" value="{t}list{/t}" />
	    </div>
	    {if $from == 'search'}<p>{t}A total of{/t} {$media.total} {t}media files has been found{/t}:</p>{/if}
    {/if}

	<form id="mediaForm" method="post">

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
	 			{if $v.media_type=="image"}<a href="{$v.source}" rel="prettyPhoto" title="{$v.file_name}">{/if}
		 			<img class="thumbnail" src="{$v.thumbnails.medium}" alt="{$v.file_name}" />
	 			{if $v.media_type=="image"}</a>{/if}
	 			<span class="file-name">{$v.file_name}</span>
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

	$('input:text.link').click(function(){
		$(this).select();
	});
});
</script>
{/literal}