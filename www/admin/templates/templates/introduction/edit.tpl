<style>
.image-preview {
	max-width: 300px;
	max-height: 250px;
	border: 1px solid #ddd;
}
</style>


{include file="../shared/admin-header.tpl"}
<div id="page-main">
<form name="theForm" id="theForm" method="post" action="edit.php" >
{if $CRUDstates.can_update}
<input type="button" value="{t}save{/t}" onclick="freemodSaveContentAll()" style="margin-right:5px" />
{* <input type="button" value="{t}save and preview{/t}" onclick="freemodDoPreview()" style="margin-right:5px" /> *}
{/if}
{if $CRUDstates.can_delete}
<input type="button" value="{t}delete{/t}" onclick="freemodDeletePage()" />
{/if}
<input type="hidden" name="id" id="id" value="{$id}" />
<input type="hidden" name="action" id="action" value="" />
<div style="width:890px;border:1px solid #aaf;margin-top:10px;">
{if $languages|@count>1}
	<div id="taxon-language-default" style="background-color:#eef;padding:5px;font-weight:bold">
		<span id="taxon-language-default-language">
        {foreach $languages v k}
        {if $v.def_language=='1'}{$v.language}{/if}
        {/foreach}
		</span>
	</div>
{/if}
	<div style="width:100%;padding:10px">
		{t}Topic:{/t} <input type="text" id="topic-default" name="topic-default" style="margin-bottom:10px" onblur="freemodSaveContentDefault()" /><br />
        <span style="font-size:0.9em;"><label><input type="checkbox" id="hide_from_index"{if $page.hide_from_index==1} checked="checked"{/if}/>{t}hide page from the public introduction index{/t}</label></span>
		<textarea
			name="content-default"
			style="width:870px;height:500px;"
			onblur="freemodSaveContentDefault()"
			id="content-default"></textarea>
	</div>
</div>

{if $languages|@count > 1}
<div style="width:890px;height:610px;border:1px solid #aaf;margin-top:10px;">
	<div id="taxon-language-default" style="background-color:#eef;padding:5px;font-weight:bold;margin-bottom:10px;">
		<div>
			<span id="project-language-tabs"></span>
		</div>
	</div>
	<div style="width:100%;padding:10px">
		{t}Topic:{/t} <input type="text" id="topic-other" name="topic-other" style="margin-bottom:10px" onblur="freemodSaveContentActive()" />
		<textarea
			name="content-other"
			style="width:870px;height:500px;"
			onblur="freemodSaveContentActive()"
			id="content-other"></textarea>
	</div>
</div>
{/if}

{if $use_media}
<p>
{if $page.image}
	<input type="hidden" name="media_id" id="media_id" value="{$page.image.id}" />
	{t}current image for this page:{/t}<br />
	<a href="{$page.image.rs_original}" title="{$page.image.name}" rel="prettyPhoto">
		<img src="{$page.image.rs_original}" alt="{$page.image.caption}" class="image-preview" />
	</a><br />
{if $CRUDstates.can_update}
	<span class="a" onclick="freemodDeletePageImage('{$page.image.id}')">{t}(click to detach image){/t}</span>
{/if}
{else}
	<a href="../media/upload.php?item_id={$id}&amp;module_id={$module_id}">{t}Upload{/t}</a> or
	<a href="../media/select.php?item_id={$id}&amp;module_id={$module_id}">{t}attach media{/t}</a> to this page.
{/if}
</p>
{/if}

</form>
<form action="media_upload.php" method="post" id="imgForm">
<input type="hidden" name="id" id="id" value="{$id}" />
</form>



<script type="text/JavaScript">
$(document).ready(function()
{
	allActiveView = 'freemodule';
{foreach $languages v k}
	allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
{/foreach}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();

	initTinyMce(false,false);

	//allSetAutoSaveFreq({$autoSaveFrequency});
	//freemodRunAutoSave();
});

function onInitTinyMce()
{
	freemodGetDataAll();
}

</script>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
