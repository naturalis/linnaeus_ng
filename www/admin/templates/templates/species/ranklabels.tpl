{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $projectRanks|@count==0}
Currently, no ranks have been defined in your project. Go <a href="ranks.php">here</a> to define ranks.
{else}

<span id="message-container" style="float:right;"></span>
Below, you can specify the correct label of each rank in the language or languages defined in your project.<br />On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.<br />
Text you enter is automatically saved when you leave the input field.
<br /><br />
<table>
<tr>
	<th style="width:150px">Rank</th>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td {$languages[i].language_id}>{$languages[i].language} *</td>
{/if}
{/section}
{if $languages|@count>1}		
<td id="language-tabs">(languages)</td>
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
taxonActiveView = 'ranklabels';
{section name=i loop=$projectRanks}
taxonAddRankId({$projectRanks[i].id});
{/section}
{section name=i loop=$languages}
taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
taxonActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
taxonDrawRankLanguages();
taxonGetRankLabels(taxonDefaultLanguage);
taxonGetRankLabels(taxonActiveLanguage);

{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


