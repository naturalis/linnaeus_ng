{include file="../shared/admin-header.tpl"}

{assign var=noKeypathEdit value=true}

{include file="_keypath.tpl"}

<div id="page-main">
{if $step.id!=''}
<span id="message-container" style="float:right;"></span><br />
<span class="key-step-title">{t}Editing keystep{/t} "<span id="default-key-title">...</span>"</span><br />
<p>
{t}Enter the title and text of this step in your key in the various languages within your project. Title and text are saved automatically after you have entered the text in the appropriate input.{/t}<br />
{t}To change the step-number from the automatically generated one, enter a new number and click 'save'. Please note that the numbers have to be unique in your key.{/t}
</p>

<form method="post" action="" id="theForm">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="id" id="id" value="{$step.id}" />
	<input type="hidden" name="is_start" value="{$step.is_start}" />

<table style="border-collapse:collapse">
	<tr style="vertical-align:top;">
		<td>
			{t}Number:{/t}
		</td>
		<td>
			<input type="text" name="number" id="number" value="{$step.number}" style="width:40px;text-align:right" /> *
		</td>
	</tr>
	<tr style="vertical-align:top;">
		<td>&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
	<td></td>
	<td {$languages[i].language_id}>
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
		<td><input 
				type="text" 
				name="titleDefault" 
				id="titleDefault" 
				onblur="keySaveStepTitle(this.value,'default')" />
		</td>
	{if $session.project.languages|@count>1}
		<td><input 
				type="text" 
				name="titleOther" 
				id="titleOther" 
				onblur="keySaveStepTitle(this.value,'other')" />
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td><textarea
				name="contentDefault" 
				id="contentDefault" 
				cols="50" 
				rows="9"
				onblur="keySaveStepText(this.value,'default')" /></textarea>
		</td>
	{if $session.project.languages|@count>1}
		<td>
			<textarea 
				name="contentOther" 
				id="contentOther" 
				cols="50" 
				rows="9"
				onblur="keySaveStepText(this.value,'other')" /></textarea>
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">
			<input type="submit" value="{t}save{/t}" />
			<input type="button" onclick="window.open('step_show.php','_self')" value="{t}back{/t}" />
		</td>
	</tr>
</table>
</form>

<form method="post" action="step_show.php" id="nextForm">
	<input type="hidden" name="id" id="next" value="" />
	<input type="hidden" name="choice" id="choice" value="" />
</form>
{/if}
</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
	taxonActiveView = 'keystepedit';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawRankLanguages();
	keyStepId = {if $step.id}{$step.id}{else}-1{/if};
	keyGetKeystepContent(allDefaultLanguage);
	keyGetKeystepContent(allActiveLanguage);
{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
