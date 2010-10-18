{include file="../shared/admin-header.tpl"}

<div id="page-main">

<span id="message-container" style="float:right;"></span>
<br />

<table>
<tr>
	<td></td>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td {$languages[i].language_id}>{$languages[i].language} *</td>
{/if}
{/section}
<td id="language-tabs">(languages)</td>
</tr>
{section name=i loop=$projectRanks}
<tr>
	<td>{$projectRanks[i].rank}</td>
	<td><input type="text" id="default-{$projectRanks[i].id}" maxlength="64" onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'default')" /></td>
	<td><input type="text" id="other-{$projectRanks[i].id}" maxlength="64" onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'other')" /></td>
</tr>
{/section}
</table>
</div>




<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
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


