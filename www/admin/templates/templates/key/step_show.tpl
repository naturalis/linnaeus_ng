{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}

<div id="page-main">
<form method="post" action="step_edit.php" id="theForm">
<input type="hidden" name="id" id="id" value="{$step.id}" />
<input type="hidden" name="ref_choice" id="ref_choice" value="" />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td colspan="2" id="key-step-number">{t}Step number{/t} {$step.number}</td>
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
<input type="hidden" name="choice" id="choice" value="" />
</form>

<form method="post" action="" id="moveForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$step.id}" />
<input type="hidden" name="move" id="move" value="" />
<input type="hidden" name="direction" id="direction" value="" />
</form>

<span  id="key-step-choices">{t}Choices{/t}</span>
<form method="post" action="choice_edit.php" id="choiceForm">
<input type="hidden" name="id" id="id2" value="" />
<table>
{section name=i loop=$choices}
	<tr class="tr-highlight">
		<td style="width:12px;text-align:right">{$choices[i].show_order}.</td>
		<td style="width:200px">{$choices[i].title}</td>
		<td>&rarr;</td>
		<td style="width:250px">
		
		{if $choices[i].res_keystep_id!=''}
			
			{if $choices[i].res_keystep_id!='-1'}
			<span
				onclick="$('#choice').val({$choices[i].id});$('#next').val({$choices[i].res_keystep_id});$('#nextForm').submit();" 
				class="pseudo-a">
				{t}Step{/t} {if $choices[i].target_number}{$choices[i].target_number}: {/if}{$choices[i].target}
			</span>
			{else}
			<span
				onclick="$('#id').val('');$('#ref_choice').val({$choices[i].id});$('#theForm').submit();" 
				class="pseudo-a">
				{$choices[i].target}
			</span>
			{/if}
			{elseif $choices[i].res_taxon_id!=''}
			{t}Taxon:{/t}
			<span
				onclick="window.open('../species/taxon.php?id={$choices[i].res_taxon_id}','_self')"
				class="pseudo-a">
				{$choices[i].target}
			</span>
			{else}
			{$choices[i].target}
			</span>
			{/if}
		</td>
		<td>[<span class="pseudo-a" onclick="$('#id2').val({$choices[i].id});$('#choiceForm').submit();">{t}edit{/t}</span>]</td>
		<td>{if $smarty.section.i.index<$choices|@count-1}
			<span class="pseudo-a" onclick="$('#move').val({$choices[i].id});$('#direction').val('down');$('#moveForm').submit();">&darr;</span>
			{else}
			<span class="pseudo-a" onclick="$('#move').val({$choices[i].id});$('#direction').val('up');$('#moveForm').submit();">&uarr;</span>
			{/if}
		</td>
	</tr>
{/section}
{if $choices|@count==0}
	<tr style="vertical-align:top">
		<td colspan="2"><span class="key-no-choices">{t}(none defined){/t}</span></td>
	</tr>
{/if}
{if $choices|@count < $maxChoicesPerKey}
	<tr style="vertical-align:top">
		<td colspan="2">[<span onclick="$('#choiceForm').submit();" class="pseudo-a">{t}add new choice{/t}</span>]</td>
	</tr>
{/if}
</table>
</form>
</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
