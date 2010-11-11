{include file="../shared/admin-header.tpl"}
<div id="page-main" style="height:1200px">
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
				<input type="button" value="{t}preview{/t}" onclick="alert('working on it');" style="margin-right:5px" />
				<input type="button" value="{t}undo{/t}!?!?!" onclick="taxonGetUndo()" style="margin-right:5px" />
				<input type="button" value="{t}delete{/t}" onclick="taxonDeleteData()" style="margin-right:5px" />
				<input type="button" value="{t}taxon list{/t}" onclick="taxonClose()" style="" />
			</span>
		</td>
	</tr>
</table>
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

//	taxonPublishStates  = {if $content.publish!=''}{$content.publish}{else}0{/if};
	taxonDrawPublishBlocks();

	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat('{$session.user.id}','{$session.system.active_page.appName}','{$session.system.active_page.controllerBaseName}','{$session.system.active_page.viewName}');

	taxonActiveTaxonId = $('#taxon_id').val();
	taxonGetDataAll();

	allSetAutoSaveFreq({$autosaveFrequency});
	taxonRunAutoSave();


{literal}	
});
</script>
{/literal}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}