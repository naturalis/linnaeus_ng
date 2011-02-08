{include file="../shared/admin-header.tpl"}
<div id="page-main" style="height:{if $languages|@count > 1}1200px{else}670px{/if}">
<span id="message-container" style="float:right;"></span>
<form name="theForm" id="theForm">
<input type="button" value="{t}save{/t}" onclick="contentSaveContentAll()" style="margin-right:5px" />
<input type="button" value="{t}back{/t}" onclick="" style="" />
<input type="hidden" name="subject" id="subject" value="{$subject}" />


<div style="width:780px;height:610px;">
	<br />
	<div id="taxon-language-default">
		<span id="taxon-language-default-language">
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}{$languages[i].language}{/if}
{/section}		
		</span>
	</div>
	<textarea
		name="content-default"
		style="width:900px;height:500px;"
		id="taxon-content-default"></textarea>
	<br />
{if $languages|@count > 1}
	<div>
		<span id="project-language-tabs"></span>
	</div>	
	<div id="taxon-languages-other"></div>
	<textarea
		name="content-other"
		style="width:900px;height:500px;"
		id="taxon-content-other"></textarea>
{/if}
</div>

</form>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'introduction';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();

	contentGetDataAll();

	initTinyMce();

{literal}	
});
</script>
{/literal}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}