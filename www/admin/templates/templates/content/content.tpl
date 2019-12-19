{include file="../shared/admin-header.tpl"}
<div id="page-main" style="height:{if $languages|@count > 1}1200px{else}670px{/if}">
<form name="theForm" id="theForm">
	<input type="button" value="{t}save{/t}" onclick="contentSaveContentAll()" style="margin-right:5px" />
	{* <input type="button" value="{t}save and preview{/t}" onclick="contentPreviewContent()" style="margin-right:5px" /> *}
	{*<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" />*}
	<input type="hidden" name="subject" id="subject" value="{$subject}" />
</form>
<form>
<div style="width:890px;height:560px;border:1px solid #aaf;margin-top:10px;">
	<div id="taxon-language-default" style="background-color:#eef;padding:5px;font-weight:bold">
		<span id="taxon-language-default-language">
{if $languages|@count>1}
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}{$languages[i].language}{/if}
{/section}	
{/if}
		</span>
	</div>
	<div style="width:100%;padding:10px">
		<textarea
			name="content-default"
			style="width:870px;height:500px;"
			id="content-default"></textarea>
	</div>
</div>

{if $languages|@count > 1}
<div style="width:890px;height:560px;border:1px solid #aaf;margin-top:10px;">
	<div id="taxon-language-default" style="background-color:#eef;padding:5px;font-weight:bold;margin-bottom:10px;">
		<div>
			<span id="project-language-tabs"></span>
		</div>	
	</div>
	<div style="width:100%;padding:10px">
		<textarea
			name="content-other"
			style="width:870px;height:500px;"
			id="content-other"></textarea>
	</div>
</div>
{/if}
</form>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'introduction';

	currentSubject = '{$subject}';
	
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();

	initTinyMce(false,false);

	//allSetAutoSaveFreq({$autoSaveFrequency});
	//contentRunAutoSave();

{literal}	
});

function onInitTinyMce() {
	contentGetDataAll();
}

</script>
{/literal}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}