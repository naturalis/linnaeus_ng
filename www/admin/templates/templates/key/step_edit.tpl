{include file="../shared/admin-header.tpl"}

{assign var=noKeypathEdit value=true}

{include file="_keypath.tpl"}

<div id="page-main">

<form method="post" action="" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$step.id}" />
<input type="hidden" name="number" value="{$step.number}" />
<input type="hidden" name="is_start" value="{$step.is_start}" />
<span class="key-step-title">
{if !$data.id}{t}New step{/t}{else}{t}Editing step{/t}{/if} {if $choice}({t}follows from choice{/t} "{$choice.title}"){/if}
</span><br /><br />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td><input type="text" name="title" value="{$step.title}" /> *</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td><textarea name="content" cols="50" rows="5">{$step.content}</textarea></td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2"><input type="submit" value="{t}save{/t}" /></td>
	</tr>
</table>
</form>
<form method="post" action="step_show.php" id="nextForm">
<input type="hidden" name="id" id="next" value="" />
<input type="hidden" name="choice" id="choice" value="" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
