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
        <a href="../media/upload.php?item_id={$taxon.id}&amp;module_id={$module_id}">{t}upload media{/t}</a><br />
        <a href="../media/select.php?item_id={$taxon.id}&amp;module_id={$module_id}">{t}attach media{/t}</a><br />
    </p>

	<form id="theForm" method="post">
    <input type="hidden" name="taxon_id" value="{$taxon.id}" />
    <input type="hidden" id="action" name="action" value="save" />
    <input type="hidden" id="subject" name="subject" value="" />

	{assign var=total value=$media|@count-1}

    {if $languages|@count>1 && $total > 0}
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



    {foreach from=$media key=key item=v}
        <p>
            <table>
                <tr>
                    <th colspan="3">
                        {$v.name} ({$v.media_type})
                    </th>
                </tr>
                <tr>
                    <td>
                        <a
                            rel="prettyPhoto[gallery]"
                            title="{$v.caption}"
                            href="{$v.rs_original}">

                            {if $v.media_type=='image'}
                            <img src="{$v.rs_original}" class="image" />
                            {elseif $v.media_type=='video'}
                            <img src="{$baseUrl}admin/media/system/icons/video.jpg" />
                            {elseif $v.media_type=='sound'}
                            <img src="{$baseUrl}admin/media/system/icons/audio.jpg" />
                            {/if}
                        </a><br />
                        ({$v.mime_type}, {$v.file_size_hr})

                    </td>
                    <td>
                        <textarea name="captions[{$v.id}]">{$v.caption}</textarea><br />
                        {if $v.media_type=='image'}
                        <label>
                            <input type="radio" name="overview-image" value="{$v.id}" {if $v.overview_image=='1'} checked="checked"{/if} />
                            is overview image
                        </label>
                        {/if}
                        <br /><br />
                        <input type="submit" value="{t}save{/t}" title="{t}save all captions{/t}" />
                        {if $key > 0}
                        <input type="button" value="&uarr;" title="{t}move image up{/t}"
                            onclick="$('#subject').val({$v.id});$('#action').val('up');$('#theForm').submit();" />
                        {/if}
                        {if $key < $total}
                        <input type="button" value="&darr;" title="{t}move image down{/t}"
                            onclick="$('#subject').val({$v.id});$('#action').val('down');$('#theForm').submit();" />
                        {/if}
                        <input type="button" value="{t}detach{/t}" title="{t}detach media{/t}"
                            onclick="if (!confirm('{t}Are you sure?{/t}')) { return; } $('#subject').val({$v.id});$('#action').val('delete');$('#theForm').submit();" />
                    </td>
                </tr>
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

{*
	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat(
		'{$session.admin.user.id}',
		'{$session.admin.system.active_page.appName}',
		'{$session.admin.system.active_page.controllerBaseName}',
		'{$session.admin.system.active_page.viewName}',
		'{$taxon.id}'
	);
*}
	allHideLoadingDiv();
//	allLookupNavigateOverrideUrl('media.php?id=%s');
*/
});
</script>
{include file="../shared/admin-footer.tpl"}