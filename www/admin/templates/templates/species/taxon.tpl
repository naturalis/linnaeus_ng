{include file="../shared/admin-header.tpl"}

{if $taxon.id!=-1}
<div id="page-main" style="height:{if $languages|@count > 1}1200px{else}670px{/if}">
{else}
<div id="page-main">
{/if}

	<span id="debug-message"></span>
	{if $taxon.id!=-1}
	{* $taxon.taxon *}
	<form name="theForm" id="theForm" method="post">
		<input type="hidden" name="taxon_id" id="taxon_id" value="{$taxon.id}" />  
		<input type="hidden" name="taxon_name" id="taxon-name" value="{$taxon.taxon}" />  
		<input type="hidden" name="activeLanguage" value="{$activeLanguage}" />  
		<input type="hidden" name="activePage" value="{$activePage}" />  
	<div style="border-bottom:1px dotted #ddd;padding-bottom:10px">
		{* yes i know, but for some reason the buttons refused to "see" the style sheet *}
		<input type="button" value="{t}save{/t}" onclick="taxonSaveDataManual()" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}save and preview{/t}" onclick="taxonDoPreview()" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}undo (auto)save{/t}" onclick="taxonGetUndo()" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}delete taxon{/t}" onclick="taxonDeleteData()" style="padding-right:25px;width:150px;" />
		<span id="message-container" style="margin-right:10px">&nbsp;</span>
	</div>
	<div style="padding:10px 0px 10px 0px">
		<input type="button" value="{t}name and parent{/t}" onclick="window.open('edit.php?id={$taxon.id}','_self')" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}media{/t}" onclick="window.open('media.php?id={$taxon.id}','_self')" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}literature{/t}" onclick="window.open('literature.php?id={$taxon.id}','_self')" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}synonyms{/t}" onclick="window.open('synonyms.php?id={$taxon.id}','_self')" style="padding-right:25px;width:150px;" />
		<input type="button" value="{t}common names{/t}" onclick="window.open('common.php?id={$taxon.id}','_self')" style="padding-right:25px;width:150px;" />
	
		<!-- input type="button" value="{t}taxon list{/t}" onclick="taxonClose()" style="" / -->
	</div>


	<div id="taxon-pages-table-div"></div>

	<div style="width:780px;height:610px;">
	
		<div id="taxon-language-default">
			<span id="taxon-language-default-language"></span>
			<span id="taxon-language-default-publish"></span>
		</div>
		
		<textarea name="content-default" style="width:900px;height:500px;" id="taxon-content-default"></textarea>
		<br />
		{if $languages|@count > 1}
		<div id="taxon-language-other">
			<span id="taxon-language-other-language"></span>
			<span id="taxon-language-other-publish"></span>
		</div>	
		<div id="taxon-languages-other"></div>
		<textarea name="content-other" style="width:900px;height:500px;" id="taxon-content-active"></textarea>
		{/if}

	</div>

</form>
{/if}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'taxon';
	taxonHigherTaxa = {if $isHigherTaxa}true{else}false{/if};
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	taxonDrawTaxonLanguages();

{section name=i loop=$pages}
	var pagenames = new Array();
	pagenames[-1] = '{$pages[i].page|addslashes}';
	{section name=j loop=$languages}{assign var=n value=$languages[j].language_id}pagenames[{$n}] = '{$pages[i].titles[$n].title|addslashes}';
{/section}
	taxonAddPage([{$pages[i].id},pagenames,{if $pages[i].def_page=='1'}1{else}0{/if}]);
{/section}
	taxonActivePage = {$activePage};
	taxonUpdatePageBlock();

//	taxonPublishStates  = {if $content.publish!=''}{$content.publish}{else}0{/if};
	taxonDrawPublishBlocks();

	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat(
		'{$session.user.id}',
		'{$session.system.active_page.appName}',
		'{$session.system.active_page.controllerBaseName}',
		'{$session.system.active_page.viewName}',
		'{$taxon.id}'
	);

	taxonActiveTaxonId = $('#taxon_id').val();

	allSetAutoSaveFreq({$autosaveFrequency});
	taxonRunAutoSave();

	initTinyMce('{$literature}','{$media}');
	allLookupNavigateOverrideUrl('taxon.php?id=%s&cat=');

{literal}	
});

function onInitTinyMce() {
	taxonGetDataAll();
}

</script>
{/literal}
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}