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

<div id="taxon-pages-table-div">
<table id="taxon-pages-table">
	<tr>
{section name=i loop=$languages}
		<td class="taxon-language-cell{if $languages[i].language_id==$activeLanguage}-active{else}" onclick="taxonSwitchLanguage({$languages[i].language_id}){/if}">
			{$languages[i].language}{if $languages[i].def_language=='1'} *{/if}
		</td>
{/section}
	</tr>
</table>
</div>

<div id="taxon-language-table-div">
<table id="taxon-language-table" class="taxon-language-table">
	<tr>
{section name=i loop=$languages}
		<td class="taxon-language-cell{if $languages[i].language_id==$activeLanguage}-active{else}" onclick="taxonSwitchLanguage({$languages[i].language_id}){/if}">
			{$languages[i].language}{if $languages[i].def_language=='1'} *{/if}
		</td>
{/section}
	</tr>
</table>
</div>

<p>
ADD MORE PAGES
</p>

Taxon name:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="{$content[$activeLanguage].content_name}" />
<textarea name="content" style="width:880px;height:600px;" id="taxon-content">{$content[$activeLanguage].content}</textarea>
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
{literal}
});
</script>
{/literal}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	activeLanguage = {/literal}{$activeLanguage}{literal};
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
