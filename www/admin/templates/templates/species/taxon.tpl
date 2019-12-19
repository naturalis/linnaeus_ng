{include file="../shared/admin-header.tpl"}

{if $taxon}

<div id="page-main">

	<span id="debug-message"></span>

	<form name="theForm" id="theForm" method="post">
		<input type="hidden" name="action" id="action" value="" />  
		<input type="hidden" name="taxon_id" id="taxon_id" value="{$taxon.id}" />  
		<input type="hidden" name="taxon_name" id="taxon-name" value="{$taxon.taxon}" />  
		<input type="hidden" name="activeLanguage" value="{$activeLanguage}" />  
		<input type="hidden" name="activePage" value="{$activePage}" /> 
	<div style="border-bottom:1px dotted #ddd;padding-bottom:10px">
		<input type="button" value="{t}save{/t}" onclick="taxonSaveDataManual()" class="localButton" disabledOnLoad="1" disabled="disabled"/>
		<input type="button" value="{t}save and preview{/t}" onclick="taxonDoPreview()" class="localButton" disabledOnLoad="1" disabled="disabled"/>
		<input type="button" value="{t}delete taxon{/t}" onclick="taxonDeleteData()" class="localButton" disabledOnLoad="1" disabled="disabled"/>
		<input type="button" value="{t}new taxon{/t}" onclick="window.open('new.php','_self')" class="localButton" disabledOnLoad="1" disabled="disabled"/>
	</div>
	<div style="padding:10px 0px 10px 0px">
		<input type="button" value="{t}name and parent{/t}" onclick="window.open('edit.php?id={$taxon.id}','_self')" class="localButton" />
		<input type="button" value="{t}media{/t}" onclick="window.open('media.php?id={$taxon.id}','_self')" class="localButton" />
		<input type="button" value="{t}literature{/t}" onclick="window.open('literature.php?id={$taxon.id}','_self')" class="localButton" />
		<input type="button" value="{t}synonyms{/t}" onclick="window.open('synonyms.php?id={$taxon.id}','_self')" class="localButton" />
		<input type="button" value="{t}common names{/t}" onclick="window.open('common.php?id={$taxon.id}','_self')" class="localButton" />
	</div>

	{if $useVariations || $useRelated || $useNBCExtras}
	<div style="padding:10px 0px 10px 0px">
		{if $useVariations}<input type="button" value="{t}variations{/t}" onclick="window.open('variations.php?id={$taxon.id}','_self')" class="localButton" />{/if}
		{if $useRelated}<input type="button" value="{t}related{/t}" onclick="window.open('related.php?id={$taxon.id}','_self')" class="localButton" />{/if}
		{if $useNBCExtras}<input type="button" value="{t}NBC extras{/t}" onclick="window.open('nbc_extras.php?id={$taxon.id}','_self')" class="localButton" />{/if}
	</div>
	{/if}


	<div id="taxon-pages-table-div"></div>

	<div>
	
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
</div>
{else}
<div id="page-main">
</div>
{/if}



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

{* // taxonAddPage([id,[names],default?]);
   // names[-1] contains the system label, names[x] the name in language x 
*}
{section name=i loop=$pages}
	var pagenames = new Array();
	pagenames[-1] = '{$pages[i].page|addslashes}';
	{section name=j loop=$languages}
		{assign var=n value=$languages[j].language_id}
		pagenames[{$n}] = '{$pages[i].titles[$n].title|addslashes}';
	{/section}
	taxonAddPage([{$pages[i].id},pagenames,{if $pages[i].def_page=='1'}1{else}0{/if}]);
{/section}

	{if $taxon.id!=-1}
		taxonActivePage = {$activePage};
		taxonUpdatePageBlock();
	//	taxonPublishStates  = {if $content.publish!=''}{$content.publish}{else}0{/if};
		taxonDrawPublishBlocks();
		taxonActiveTaxonId = $('#taxon_id').val();

		//allSetAutoSaveFreq({$autoSaveFrequency});
		//taxonRunAutoSave();

		initTinyMce('{$literature}','{$media}');
	{/if}


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