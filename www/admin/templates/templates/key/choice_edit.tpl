{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}

<div id="page-main">
<form method="post" action="" id="theForm" enctype="multipart/form-data">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$data.id}" />
<input type="hidden" name="action" id="action" value="" />

<span class="key-step-title">
<span id="message-container" style="float:right;"></span><br />
{t}Editing choice{/t} "<span id="default-choice-title">...</span>"
</span><br /><br />

<table style="border-collapse:collapse">

	<tr style="vertical-align:top">
		<td></td>
		<td {$languages[i].language_id} colspan="2">
			{$defaultLanguage.language}
		</td>
	{if $session.project.languages|@count>1}
		<td id="project-language-tabs">
			(languages)
		</td>
{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td colspan="2">
			<input 
				type="text" 
				name="titleDefault" 
				id="titleDefault" 
				onblur="keySaveChoiceTitle(this.value,'default')" />
		</td>
	{if $session.project.languages|@count>1}
		<td>
			<input 
				type="text" 
				name="titleOther" 
				id="titleOther" 
				onblur="keySaveChoiceTitle(this.value,'other')" />
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td colspan="2">
			<textarea
				name="contentDefault" 
				id="contentDefault" 
				cols="50" 
				rows="9"
				onblur="keySaveChoiceText(this.value,'default')" /></textarea>
		</td>
	{if $session.project.languages|@count>1}
		<td>
			<textarea
				name="contentOther" 
				id="contentOther" 
				cols="50" 
				rows="9"
				onblur="keySaveChoiceText(this.value,'other')" /></textarea>
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Image:{/t}</td>
		<td colspan="2">
		{if $data.choice_img}
			<img src="{$session.project.urls.project_media}{$data.choice_img}" style=" width:200px;border:1px solid #ddd;padding:2px;" /><br />
			<span class="pseudo-a" onclick="keyDeleteImage();">{t}delete image{/t}</span>
		{else}
			<input type="file" name="image" />
		{/if}
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Target:{/t}</td>
		<td>step:</td>
		<td colspan="2">
			<select name="res_keystep_id" id="res_keystep_id" onchange="keyCheckTargetIntegrity(this)">
				<option value="-1">{t}new step{/t}</option>
				<option value="0"{if $data.res_taxon_id!=null} selected="selected"{/if}>{t}(none){/t}</option>
{if $steps|@count>0}
				<option value="-1" disabled="disabled">&nbsp</option>{/if}
{section name=i loop=$steps}<option value="{$steps[i].id}"{if $steps[i].id==$data.res_keystep_id} selected="selected"{/if}>{$steps[i].number}. {$steps[i].title}</option>
{/section}
			</select>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>&nbsp;</td>
		<td colspan="2">{t}or{/t}</td>
	</tr>
	<tr style="vertical-align:top">
		<td>&nbsp;</td>
		<td>taxon:</td>
		<td>
		
		
			<select name="res_taxon_id" id="res_taxon_id" onchange="keyCheckTargetIntegrity(this)">
				<option value="0">{t}(none){/t}</option>
				<option disabled="disabled">&nbsp;</option>
{assign var=first value=true}
{section name=i loop=$taxa}
{if $first}{assign var=minBuffer value=$taxa[i].level}{/if}
{assign var=x value=$taxa[i].id}
{if $taxa[i].keypath_endpoint==1}
<option value="{$taxa[i].id}"{if $taxa[i].id==$data.res_taxon_id} selected="selected"{/if} class="key-taxa-list{if $remainingTaxa[$x]==true}-remain{/if}">
{section name=foo loop=$taxa[i].level-$minBuffer}&nbsp;&nbsp;{/section}
{$taxa[i].taxon}{if $taxa[i].is_hybrid==1}&nbsp;x{/if}
{assign var=first value=false}{/if}
</option>
{/section}
			</select>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="3">
			<input type="submit" value="{t}save{/t}" />
			<input type="button" onclick="keyChoiceDelete()" value="{t}delete{/t}" />
			<input type="button" onclick="$('#backForm').submit();" value="{t}back{/t}" />
		</td>
	</tr>
</table>
</form>

<form method="post" action="step_show.php" id="backForm">
<input type="hidden" name="id" value="{$data.keystep_id}" />
</form>
</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
	taxonActiveView = 'choiceedit';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawRankLanguages();
	keyChoiceId = {if $data.id}{$data.id}{else}-1{/if};
	keyGetChoiceContent(allDefaultLanguage);
	keyGetChoiceContent(allActiveLanguage);
{literal}
});
{/literal}
</script>


{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}