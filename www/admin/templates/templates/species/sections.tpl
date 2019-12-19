{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t}You can define a set of sections for each category. These sections will automatically appear as paragraph headers on the corresponding category-page when you edit a taxon for the first time. A standard set of sections has been provided; these can be altered as you see fit.{/t}<br />
{t}Below, you can specify the correct label of each section in the language or languages defined in your project.{/t}<br />
{t}On the left hand side, the labels in the default language are displayed; on the right hand side, the labels in the other languages. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
{t}Text you enter is automatically saved when you leave the input field.{/t}
<br /><br />


<form name="theForm" id="theForm" action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
<tr>
	<th>{t}Category{/t}</th>
	<th>{t}Sections{/t}</th>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
{if $languages|@count>1}		
	<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
</tr>
{section name=i loop=$pages}
	{if $pages[i].sections|@count==0}
	<tr class="tr-highlight">
		<td style="background-color:#fff;">
			{$pages[i].page}
		</td>
	</tr>
	{else}
	{section name=j loop=$pages[i].sections}
	<tr class="tr-highlight">
	{if $smarty.section.j.index != 0}
		<td style="background-color:#fff;"></td>
	{else}
		<td style="background-color:#fff;">
			{$pages[i].page}
		</td>
	{/if}
		<td>{$pages[i].sections[j].section}</td>
		<td>
			<input 
				type="text" 
				maxlength="64" 
				id="default-{$pages[i].sections[j].id}"
				onblur="taxonSaveSectionTitle({$pages[i].sections[j].id},this.value,'default')" /></td>
		{if $languages|@count>1}		
		<td>
			<input 
				type="text" 
				maxlength="64" 
				id="other-{$pages[i].sections[j].id}"
				onblur="taxonSaveSectionTitle({$pages[i].sections[j].id},this.value,'other')" />
		</td>
		{/if}
		<td class="cell-page-delete" onclick="taxonSectionDelete({$pages[i].sections[j].id},'{$pages[i].sections[j].section}');"></td>
	</tr>
	{/section}
	{/if}
	<tr>
		<td></td>
		<td style="vertical-align:top">
			<input type="text" style="width:150px;" name="new[{$pages[i].id}]" /> [<span class="a" onclick="$('#theForm').submit();">{t}add{/t}</span>]
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
{/section}
</table>
</form>

</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
allActiveView = 'sections';


{section name=i loop=$pages}
{section name=j loop=$pages[i].sections}
taxonAddRankId({$pages[i].sections[j].id});
{/section}
{/section}
{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawLanguages();
taxonGetSectionLabels(allDefaultLanguage);
taxonGetSectionLabels(allActiveLanguage);

{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}