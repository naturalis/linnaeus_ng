{include file="../shared/admin-header.tpl"}

<div id="page-main">

<table class="taxon-language-table">
	<tr>
{section name=i loop=$languages}
		<td class="taxon-language-cell{if $languages[i].id==$activeLanguage}-active{else}" onclick="alert({$languages[i].id}){/if}">
			{$languages[i].language}{if $languages[i].def_language=='1'} *{/if}
		</td>
{/section}
	</tr>
</table>
<form name="theForm" id="theForm">
<input type="hidden" name="taxon_id" id="taxon_id" value="" />  
Taxon name:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="" />
<span style="float:right">
<span id="save-message">
<input type="button" value="save" onclick="taxonSaveData()" style="float:right" />
</span>
</span>
<textarea name="content" style="width:880px;height:600px;" id="taxon-content">
</textarea>
</form>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	activeLanguage = {/literal}{$activeLanguage}{literal};
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
