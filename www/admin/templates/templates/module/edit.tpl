{include file="../shared/admin-header.tpl"}
<style>
.image-preview {
	max-width: 300px;
	max-height: 250px;
	border: 1px solid #ddd;
}
</style>

<div id="page-main">
<form name="theForm" id="theForm" method="post" action="" >
<input type="button" value="{t}save{/t}" onclick="freemodSaveContentAll()" style="margin-right:5px" />
{* <input type="button" value="{t}save and preview{/t}" onclick="freemodDoPreview()" style="margin-right:5px" /> *}
{*<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" style="margin-right:15px" />*}
<input type="button" value="{t}delete{/t}" onclick="freemodDeletePage()" />
<input type="hidden" name="id" id="id" value="{$id}" />
<input type="hidden" name="action" id="action" value="" />
<div style="width:890px;height:610px;border:1px solid #aaf;margin-top:10px;">
	<div id="taxon-language-default" style="background-color:#eef;padding:5px;font-weight:bold">
{if $languages|@count > 1}
		<span id="taxon-language-default-language">
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}{$languages[i].language}{/if}
{/section}
		</span>
{/if}
	</div>
	<div style="width:100%;padding:10px">
		{t}Topic:{/t} <input type="text" id="topic-default" style="margin-bottom:10px" onblur="freemodSaveContentDefault()" />
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
		{t}Topic:{/t} <input type="text" id="topic-other" style="margin-bottom:10px" onblur="freemodSaveContentActive()" />
		<textarea
			name="content-other"
			style="width:870px;height:500px;"
			onblur="freemodSaveContentActive()"
			id="content-other"></textarea>
	</div>
</div>
{/if}

<p>
{if $page.image}
	<input type="hidden" name="media_id" id="media_id" value="{$page.image.id}" />
	{t}current image for this page:{/t}<br />
	<a href="{$page.image.rs_original}" title="{$page.image.name}" rel="prettyPhoto">
		<img src="{$page.image.rs_original}" alt="{$page.image.caption}" class="image-preview" />
	</a><br />
	<span class="a" onclick="freemodDeletePageImage('{$page.image.id}')">{t}(click to detach image){/t}</span>
{else}
	<a href="../media/upload.php?item_id={$id}&amp;module_id={$module_id}">{t}Upload{/t}</a> or
	<a href="../media/select.php?item_id={$id}&amp;module_id={$module_id}">{t}attach media{/t}</a> to this page.
{/if}
</p>

</form>
<form action="media_upload.php" method="post" id="imgForm">
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'freemodule';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();

	initTinyMce(false,false);

	//allSetAutoSaveFreq({$autoSaveFrequency});
	//freemodRunAutoSave();

{literal}
});

function onInitTinyMce() {
	freemodGetDataAll();
}

</script>
{/literal}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
