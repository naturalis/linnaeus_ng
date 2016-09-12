{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $projectRanks|@count==0}
{t}Currently, no ranks have been defined in your project.{/t} <a href="ranks.php">{t}Define ranks{/t}</a>.
{else}
{t}Below, you can specify the correct label of each rank in the language or languages defined in your project.{/t}<br />
{t}On the left hand side, the labels in the default language are displayed; on the right hand side, the labels in the other languages. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
{t}Text you enter is automatically saved when you leave the input field.{/t}
<br /><br />
<span id="message-container" style="float:right"></span>
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
				value="{$projectRanks[i].labels[$languages[0].language_id]}"
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

$(document).ready(function()
{
	allActiveView = 'ranklabels';
{foreach $projectRanks v}
	taxonAddRankId({$v.id});
{/foreach}
{foreach $languages v}
	allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
{/foreach}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	taxonGetRankLabels(allDefaultLanguage);
	taxonGetRankLabels(allActiveLanguage);
});

</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


