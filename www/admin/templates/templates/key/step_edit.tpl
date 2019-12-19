{include file="../shared/admin-header.tpl"}
{assign var=noKeypathEdit value=true}
{include file="_keypath.tpl"}

<div id="page-main">
{if $step.id!=''}
<form method="post" action="" id="theForm">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="id" id="id" value="{$step.id}" />
	<input type="hidden" name="is_start" value="{$step.is_start}" />

<fieldset>
<legend>{t}Editing keystep{/t} "<span id="default-key-title">...</span>"</legend>
<table style="border-collapse:collapse">
	<tr style="vertical-align:top;">
		<td>
			{t}Number:{/t}
		</td>
		<td>
			<input type="text" name="number" id="number" value="{$step.number}" style="width:40px;text-align:right" />
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
{if $session.admin.project.languages|@count>1}
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
				maxlength="64"
				onblur="keySaveStepContent('default')" />
		</td>
	{if $session.admin.project.languages|@count>1}
		<td><input 
				type="text" 
				name="titleOther" 
				id="titleOther" 
				maxlength="64"
				onblur="keySaveStepContent('other')" />
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td><textarea
				name="contentDefault" 
				id="contentDefault" 
				style="width:400px;height:200px;"
				onblur="keySaveStepContent('default')" /></textarea>
		</td>
	{if $session.admin.project.languages|@count>1}
		<td>
			<textarea 
				name="contentOther" 
				id="contentOther" 
				style="width:400px;height:200px;"
				onblur="keySaveStepContent('other')" /></textarea>
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">
			<input
				type="button"
				onclick="
					{if $session.admin.project.languages|@count>1}keySaveStepContent('other');{/if} 
					keySaveStepContent('default',$('#theForm').submit());"
					value="{t}save{/t}" />
			<input type="button" onclick="$('#backForm').submit();" value="{t}back{/t}" />&nbsp;&nbsp;
		</td>
	</tr>
</table>
</fieldset>
</form>
</div>

{include file="../shared/admin-messages.tpl"}
<div class="page-generic-div">
<p>
{t}Enter the title and text of this step in your key in the various languages within your project. Title and text are saved automatically after you have entered the text in the appropriate input.{/t}<br />
{t}To change the step-number from the automatically generated one, enter a new number and click 'save'. Please note that the numbers have to be unique in your key.{/t}
</p>

<form method="post" action="step_show.php" id="backForm">
	<input type="hidden" name="id" value="{$step.id}" />
</form>
{/if}
</div>

<script type="text/javascript">
$(document).ready(function()
{
	allActiveView = 'keystepedit';
{foreach $languages v i}
	allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
{/foreach}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	keyStepId = {if $step.id}{$step.id}{else}-1{/if};
	keyGetKeystepContent(allDefaultLanguage);
	keyGetKeystepContent(allActiveLanguage);

});
</script>

{include file="../shared/admin-footer.tpl"}
