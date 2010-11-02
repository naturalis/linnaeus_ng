{include file="../shared/admin-header.tpl"}
<div id="page-main" style="height:850px">
<span id="debug-message"></span>

{if $taxon.id!=-1}
<form name="theForm" id="theForm">
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$taxon.id}" />  
	<input type="hidden" name="taxon_name" id="taxon-name" value="{$taxon.taxon}" />  

<div id="taxon-navigation-table-div">
<table id="taxon-navigation-table">
	<tr>
		<td id="taxon-navigation-cell">
			<span style="float:right">
				<span id="message-container" style="margin-right:10px">&nbsp;</span>
				<input type="button" value="{t}save{/t}" onclick="taxonSaveDataManual()" style="margin-right:5px" />
				<input type="button" value="{t}preview{/t}" onclick="taxonOpenContentPreview('manual')" style="margin-right:5px" />
				<input type="button" value="{t}undo{/t}" onclick="taxonGetUndo()" style="margin-right:5px" />
				<input type="button" value="{t}delete{/t}" onclick="taxonDeleteData()" style="margin-right:5px" />
				<input type="button" value="{t}taxon list{/t}" onclick="taxonClose()" style="" />
			</span>
		</td>
	</tr>
</table>
</div>

<div id="taxon-pages-table-div"></div>

<div id="taxon-publish-table-div" style="position:absolute;top:280px"></div>


<!-- div style="position:absolute;top:320px;">Page title:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="{$content.title}" /></div -->

<div id="taxon-language-div-default" style="position:absolute;left:10px;top:320px;"></div>

<div id="taxon-language-div" style="position:absolute;left:770px;top:320px;"></div>

<div style="position:absolute;left:10px;top:345px;width:780px;height:610px;">
<textarea name="content-default" style="width:440px;height:600px;" id="taxon-content-default"></textarea>
</div>
<div style="position:absolute;left:770px;top:345px;width:780px;height:610px;">
<textarea name="content-other" style="width:440px;height:600px;" id="taxon-content-other"></textarea>
</div>

</form>
{/if}

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	taxonActiveView = 'taxon';
{section name=i loop=$languages}
	taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	taxonActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
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

	taxonPublishState  = {if $content.publish!=''}{$content.publish}{else}0{/if};
	taxonDrawPublishBlock();

	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat('{$session.user.id}','{$session.system.active_page.appName}','{$session.system.active_page.controllerBaseName}','{$session.system.active_page.viewName}');

	allShowLoadingDiv();
	taxonGetData(taxonDefaultLanguage,taxonActivePage,true);
	taxonGetData(taxonActiveLanguage,taxonActivePage);

	allSetAutoSaveFreq({$autosaveFrequency});
	taxonRunAutoSave();

{literal}
	$(window).unload(
		function () { 
			taxonConfirmSaveOnUnload();
		} 
	);
	
});
</script>
{/literal}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}