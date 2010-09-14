{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form name="theForm" id="theForm">
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$taxon.id}" />  

<div id="taxon-navigation-table-div">
<table id="taxon-navigation-table">
	<tr>
		<td id="taxon-navigation-cell">
			<span style="float:right">
				<span id="message-container" style="margin-right:10px">&nbsp;</span>
				<input type="button" value="save" onclick="taxonSaveData()" style="margin-right:5px" />
				<input type="button" value="undo" onclick="allSetMessage('coming soon')" style="margin-right:5px" />
				<input type="button" value="delete" onclick="taxonDeleteData(taxonActiveLanguage)" style="margin-right:5px" />
				<input type="button" value="taxon list" onclick="taxonClose()" style="" />
			</span>
		</td>
	</tr>
</table>
</div>

<div id="taxon-pages-table-div"></div>

<div id="taxon-language-table-div"></div>

Page title:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="{$content.title}" />
<textarea name="content" style="width:880px;height:600px;" id="taxon-content">{$content.content}</textarea>
</form>



{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{section name=i loop=$languages}
	taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	taxonActiveLanguage = {$activeLanguage};
	taxonUpdateLanguageBlock();


{section name=i loop=$pages}
	var pagenames = new Array();
	pagenames[-1] = '{$pages[i].page|addslashes}';
	{section name=j loop=$languages}{assign var=n value=$languages[j].language_id}pagenames[{$n}] = '{$pages[i].titles[$n].title|addslashes}';
{/section}
	taxonAddPage([{$pages[i].id},pagenames,{if $pages[i].def_page=='1'}1{else}0{/if}]);
{/section}
	taxonActivePage = {$activePage};
	taxonUpdatePageBlock();


{literal}
	$(window).unload(
		function () { 
			taxonConfirmSaveOnUnload() ;
		} 
	);
});
</script>
{/literal}

</div>

{include file="../shared/admin-footer.tpl"}
