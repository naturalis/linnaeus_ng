{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<style>
table {
	border-collapse: collapse;
}
table tr td {
	vertical-align: top;
}
td.media-header {
	vertical-align: bottom;
	font-weight: bold;
	padding: 10px 0 5px 0;
}
td.media-cell {
	border-bottom: 1px solid #ddd;
	padding-bottom: 10px;
	padding-right: 10px;
}
.image-preview, .av-preview {
	max-width: 300px;
	max-height: 250px;
}
.image-preview {
	border: 1px solid #ddd;
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
        <a href="../media/upload.php?item_id={$item_id}&amp;module_id={$module_id}">{t}upload media{/t}</a><br />
        <a href="../media/select.php?item_id={$item_id}&amp;module_id={$module_id}">{t}attach media{/t}</a><br />
    </p>

	<form id="theForm" method="post">
    <input type="hidden" name="id" value="{$item_id}" />
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


    <table>
    {foreach from=$media key=key item=v}
         <tr>
            <td class="media-header">
                {$v.name} ({$v.media_type})
            </td>
            <td class="media-header">
            	{t}Caption{/t}
            </td>
        </tr>
        <tr>
            <td class="media-cell">

                {if $v.media_type=='image'}
                    <a
                        rel="prettyPhoto[gallery]"
                        title="{$v.caption}"
                        href="{$v.rs_original}">
						<img src="{$v.rs_original}" class="image-preview" />
                    </a>

				{else if $v.media_type == 'audio' or $v.media_type == 'video'}
					<{$v.media_type} src="{$v.rs_original}" alt="{$v.caption}" class="av-preview" controls />
						<a href="{$v.rs_original}">Play {$v.caption}</a>
					</{$v.media_type}><br>

				{else}
					<a href="{$v.rs_original}">
						<img src="{$v.rs_thumb_medium}" alt="{$v.caption}" /><br>
					</a>

				{/if}


                <br />
                ({$v.file_size_hr})

            </td>
            <td class="media-cell">
                <textarea name="captions[{$v.id}]">{$v.caption}</textarea><br />
                <br />
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

             	<table style="margin-top: 15px;">
            	<tr>
            		<td class="media-header">{t}Metadata{/t}</td>
            		<td class="media-header"><a href="../media/edit.php?id={$v.id}&amp;language_id={$language_id}">{t}edit{/t}</a></td>
            	</tr>
				{foreach from=$v.metadata key=label item=val}
					<tr><td>{$label}:</td><td>{if $val != ''}{$val}{else}-{/if}</td></tr>
				{/foreach}
				</table>

            </td>
        </tr>

	{/foreach}
	</table>



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