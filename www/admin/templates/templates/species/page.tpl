{include file="../shared/admin-header.tpl"}

<div id="page-main">

<span id="message-container" style="float:right;"></span><br />

{t _s1=$maxCategories _s2=$pages[0].page}Each taxon page consists of one or more categories, with a maximum of %s. The first category, '%s', is mandatory.{/t}<br />
{t}Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
{t}Text you enter is automatically saved when you leave the input field.{/t}
<br /><br />
<table>
<tr>
	<th style="width:150px">{t}Category{/t}</th>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
{if $languages|@count > 1}
<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
</tr>
{section name=i loop=$pages}
	<tr class="tr-highlight">
		<td>
			{$pages[i].page}
		</td>
			<td>
				<input 
					type="text" 
					id="default-{$pages[i].id}" 
					maxlength="64" 
					onblur="taxonSavePageTitle({$pages[i].id},this.value,'default')" />
			</td>
			{if $languages|@count > 1}
			<td>
				<input 
					type="text" 
					id="other-{$pages[i].id}" 
					maxlength="64" 
					onblur="taxonSavePageTitle({$pages[i].id},this.value,'other')" />
			</td>
			{/if}
			<td class="cell-page-delete" onclick="taxonPageDelete({$pages[i].id},'{$pages[i].page}');"></td>
		</tr>
{/section}
</table>
<br />
{if $languages|@count==0}
{t}You have to define at least one language in your project before you can add any categories.{/t}<br />
<a href="../projects/data.php">{t}Define languages.{/t}</a>
{else}
<form method="post" action="" id="theForm">
{if $pages|@count<$maxCategories}
Add a new category:
<input type="text" maxlength="32" id="new_page" name="new_page" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="show_order" value="{$nextShowOrder}" />
<input type="submit" value="{t}save{/t}" />
{/if}
</form>
{/if}

</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
taxonActiveView = 'page';
{section name=i loop=$pages}
taxonAddRankId({$pages[i].id});
{/section}
{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawRankLanguages();
taxonGetPageLabels(allDefaultLanguage);
taxonGetPageLabels(allActiveLanguage);

{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}