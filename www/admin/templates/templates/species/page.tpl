{include file="../shared/admin-header.tpl"}

<div id="page-main">

<span id="message-container" style="float:right;"></span>

Each taxon page consists of one or more categories, with a maximum of {$maxCategories}. The first category, '{$pages[0].page}', is mandatory.<br />
Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.<br />
Text you enter is automatically saved when you leave the input field.
<br /><br />
<table>
<tr>
	<td></td>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
<td colspan="2" id="language-tabs">(languages)</td>
</tr>
{section name=i loop=$pages}
	<tr>
		<td>
			{$pages[i].page}
		</td>
			<td>
				<input 
					type="text" 
					id="default-{$pages[i].id}" 
					maxlength="64" 
					onblur="taxonSavePageTitle({$pages[i].id},this.value,'default')" /></td>
			<td>
				<input 
					type="text" 
					id="other-{$pages[i].id}" 
					maxlength="64" 
					onblur="taxonSavePageTitle({$pages[i].id},this.value,'other')" />
			</td>
			<td class="cell-page-delete" onclick="taxonPageDelete({$pages[i].id},'{$pages[i].page}');"></td>
		</tr>
{/section}
</table>

<br />
{if $languages|@count==0}
You have to define at least one language in your project before you can add any categories.<br />
<a href="../projects/data.php">Define languages.</a>
{else}
<form method="post" action="" id="theForm">
{if $pages|@count<$maxCategories}
Add a new category:
<input type="text" maxlength="32" id="new_page" name="new_page" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="show_order" value="{$nextShowOrder}" />
<input type="submit" value="save" />
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
taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
taxonActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
taxonDrawRankLanguages();
taxonGetPageLabels(taxonDefaultLanguage);
taxonGetPageLabels(taxonActiveLanguage);

{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}