{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
table tr td {
	vertical-align:top;
}
table {
	padding-bottom:10px;
	border-bottom:1px solid #ddd;
}
.image {
	max-width:150px;
}
.thumb {
	max-width:75px;
}
textarea {
	width:250px;
	height:50px;
	font-family:"Consolas", Courier, monospace;
	font-size:11px;
}
</style>
    
<div id="page-main">
    <p>
        <a href="media_upload.php?id={$taxon.id}">{t}upload media{/t}</a><br />
        <a href="taxon.php?id={$taxon.id}">{t}main page{/t}</a>
    </p>

	<form id="theForm" method="post">
    <input type="hidden" name="taxon_id" value="{$taxon.id}" />
    <input type="hidden" id="action" name="action" value="save" />
    <input type="hidden" id="subject" name="subject" value="" />

    {if $languages|@count>1}
    <p>
    See captions in:
    <select id="language_id" name="language_id" onchange="$('#action').val('language_change');$('#theForm').submit();">
        {foreach from=$languages item=v}
        <option value="{$v.language_id}"{if $v.language_id==$language_id} selected="selected"{/if}>{$v.language}</option>
        {/foreach}
	</select>
    </p>
    {else}
    <input type="hidden" id="language_id" name="language_id" value="{$defaultLanguage}" />
    {/if}
    
    
    <div>
    

     
    
    {foreach from=$media item=v}

        <p>
            <table>
                <tr>
                    <th colspan="3">
                        {$v.file_name} ({$v.media_type})
                    </th>
                </tr>
                <tr>
                    <td>
                        <a
                            rel="prettyPhoto[gallery]" 
                            title="{$v.description}"
                            href="{$session.admin.project.urls.project_media}{$v.file_name}">
                            
                            {if $v.media_type=='image'}
                             <img src="{$session.admin.project.urls.project_media}{$v.file_name}" class="image" />
                            {elseif $v.media_type=='video'}
                            <img src="{$baseUrl}admin/media/system/icons/video.jpg" />
                            {elseif $v.media_type=='sound'}
                            <img src="{$baseUrl}admin/media/system/icons/audio.jpg" />
                            {/if}
                        </a><br />
                        ({$v.mime_type}, {$v.file_size_hr})
                       
                    </td>
                    <td>
                        <textarea name="captions[{$v.id}]">{$v.description}</textarea><br />
                        {* original name: {$v.original_name}<br /> *}
                        {if $v.media_type=='image'}
                        <label>
                            <input type="radio" name="overview-image" value="{$v.id}" {if $v.overview_image=='1'} checked="checked"{/if} />
                            is overview image
                        </label>
                        {/if}
                        <br /><br />
                        <input type="submit" value="save" title="save all captions" />
                        <input type="button" value="&uarr;" title="move image up"
                            onclick="$('#subject').val({$v.id});$('#action').val('up');$('#theForm').submit();" />
                        <input type="button" value="&darr;" title="move image down"
                            onclick="$('#subject').val({$v.id});$('#action').val('down');$('#theForm').submit();" />
                        <input type="button" value="delete" title="delete image{if $v.thumb_name} and its thumbnail{/if}"
                            onclick="if (!confirm('{t}Are you sure?{/t}')) { return; } $('#subject').val({$v.id});$('#action').val('delete');$('#theForm').submit();" />
                    </td>
                </tr>
               {if $v.thumb_name}
                <tr>
                    <th colspan="3">
                        {$v.thumb_name} (thumbnail)<br />
                        <a
                            rel="prettyPhoto[gallery]" 
                            href="{$session.admin.project.urls.project_thumbs}{$v.thumb_name}">
                            <img src="{$session.admin.project.urls.project_thumbs}{$v.thumb_name}" class="thumb" />
                        </a>
                    </th>
	            </tr>
                    {/if}
			</table>
        </p>
    
    {/foreach}

    </div>
    </form>

</div>

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('#page-block-messages').fadeOut(1500);
	
/*
	allShowLoadingDiv();
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}

	allActiveLanguage = {$defaultLanguage};
	taxonDrawTaxonLanguages('taxonMediaChangeLanguage',true);

	allHideLoadingDiv();
//	allLookupNavigateOverrideUrl('media.php?id=%s');
*/
});
</script>
{include file="../shared/admin-footer.tpl"}