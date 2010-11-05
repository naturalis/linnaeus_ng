{include file="../shared/admin-header.tpl"}


<div id="page-main">
<form method="post" action="step_edit.php" id="theForm">
<input type="hidden" name="id" id="id" value="{$step.id}" />
<input type="hidden" name="ref_choice" id="ref_choice" value="" />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td colspan="2">{t}Step number{/t} {$step.number}</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td>{$step.title}</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td>{$step.content}</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">[<span onclick="$('#theForm').submit();" class="pseudo-a">{t}edit{/t}</span>]</td>
	</tr>
</table>
</form>
<br />

<form method="post" action="step_show.php" id="nextForm">
<input type="hidden" name="id" id="next" value="" />
</form>

Choices:
<form method="post" action="choice_edit.php" id="choiceForm">
<input type="hidden" name="id" id="choice" value="" />
<table>
{section name=i loop=$choices}
	<tr>
		<td>{$choices[i].show_order}</td>
		<td>{$choices[i].title}</td>
		<td>
		
		{if $choices[i].res_keystep_id!=''}
			Step: 
			{if $choices[i].res_keystep_id!='-1'}
			<span
				onclick="$('#next').val({$choices[i].res_keystep_id});$('#nextForm').submit();" 
				class="pseudo-a">
				{$choices[i].target}
			</span>
			{else}
			<span
				onclick="$('#id').val('');$('#ref_choice').val({$choices[i].id});$('#theForm').submit();" 
				class="pseudo-a">
				{$choices[i].target}
			</span>
			{/if}
			{elseif $choices[i].res_taxon_id!=''}
			Taxon: {$choices[i].target}
			{else}
			{$choices[i].target}
			{/if}
		</td>
		<td>[<span class="pseudo-a" onclick="$('#choice').val({$choices[i].id});$('#choiceForm').submit();">{t}edit{/t}</span>]</td>
	</tr>
{/section}
{if $choices|@count==0}
	<tr style="vertical-align:top">
		<td colspan="2"><span class="key-no-choices">{t}(none defined){/t}</span></td>
	</tr>
{/if}
	<tr style="vertical-align:top">
		<td colspan="2">[<span onclick="$('#choiceForm').submit();" class="pseudo-a">{t}add new choice{/t}</span>]</td>
	</tr>
</table>
</form>
<br />
<br />
MAX ME OUT<br />
RENUMBER MY CHOICE STEPS<br />
GIVE ME SOME MORE BREADCRUMBS

</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
