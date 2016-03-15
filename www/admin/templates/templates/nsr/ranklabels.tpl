{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $projectRanks|@count==0}
{t}Currently, no ranks have been defined in your project.{/t} <a href="ranks.php">{t}Define ranks{/t}</a>.
{else}
{t}Below, you can specify the correct label of each rank in the language or languages defined in your project.{/t}<br />
{t}On the left hand side, the labels in the default language are displayed; on the right hand side, the labels in the other languages. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
{t}Text you enter is automatically saved when you leave the input field.{/t}
<br /><br />
<table>
<tr>
	<th style="width:150px">{t}Rank{/t}</th>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td {$languages[i].language_id}>{$languages[i].language} *</td>
{/if}
{/section}
{if $languages|@count>1}
<td id="project-language-tabs">(languages)</td>
{/if}
</tr>
{section name=i loop=$projectRanks}
	<tr class="tr-highlight">
		<td>{$projectRanks[i].rank}</td>
		<td>
			<input
				type="text" 
				id="default-{$projectRanks[i].id}" 
				maxlength="64" 
				onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'default')"
				direction="{$languages[0].direction}" />
		</td>
	{if $languages|@count>1}		
		<td>
			<input 
				type="text" 
				id="other-{$projectRanks[i].id}" 
				maxlength="64" 
				onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'other')" />
		</td>
	{/if}
	</tr>
{/section}
</table>
{/if}
</div>


<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
	allActiveView = 'ranklabels';
{section name=i loop=$projectRanks}
	taxonAddRankId({$projectRanks[i].id});
{/section}
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	taxonGetRankLabels(allDefaultLanguage);
	taxonGetRankLabels(allActiveLanguage);
{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


