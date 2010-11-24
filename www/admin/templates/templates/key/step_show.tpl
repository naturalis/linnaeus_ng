{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}

<div id="page-main">
<form method="post" action="step_edit.php" id="theForm">
<input type="hidden" name="id" id="id" value="{$step.id}" />
<input type="hidden" name="ref_choice" id="ref_choice" value="" />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td>
			<span id="key-step-number">{t}Step{/t} {$step.number}:</span>
			<span id="key-title">{$step.title}</span>
			<span id="key-edit-link">[<span onclick="$('#theForm').submit();" class="pseudo-a">{t}edit{/t}</span>]</span>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{$step.content}</td>
	</tr>
</table>
</form>
<br />
<form method="post" action="choice_edit.php" id="choiceForm">
<input type="hidden" name="id" id="id2" value="" />
<span  id="key-step-choices">{t}Possible choices{/t}:</span>
<table>
	<tr>
		<th>#</th>
		<th>{t}title{/t}</th>
		<th colspan="2">{t}leads to{/t}</th>
		<th>{t}change order{/t}</th>
		<th></th>
		<th>{t}details{/t} <span class="pseudo-a" onclick="keyShowChoiceDetails('all')">{t}(toggle all){/t}</span></th>
	</tr>
{section name=i loop=$choices}
	<tr class="tr-highlight">
		<td class="key-choice-number">{$choices[i].show_order}.</td>
		<td class="key-choice-title">{$choices[i].title}</td>
		<td class="key-choice-leadsto">&rarr;</td>
		<td class="key-choice-target">
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
		<td class="key-choice-arrow">{if $smarty.section.i.index<$choices|@count-1}
			<span class="pseudo-a" onclick="$('#move').val({$choices[i].id});$('#direction').val('down');$('#moveForm').submit();">&darr;</span>
			{else}
			<span class="pseudo-a" onclick="$('#move').val({$choices[i].id});$('#direction').val('up');$('#moveForm').submit();">&uarr;</span>
			{/if}
		</td>
		<td class="key-choice-edit">[<span class="pseudo-a" onclick="$('#id2').val({$choices[i].id});$('#choiceForm').submit();">{t}edit{/t}</span>]</td>
		<td title="{t}show details{/t}" class="pseudo-a" onclick="keyShowChoiceDetails({$smarty.section.i.index})">&nabla;</td>
	</tr>
	<tr id="choice-{$smarty.section.i.index}" class="key-choice-details-invisible">
		<td></td>
		<td colspan="7">
			<table>
				<tr style="vertical-align:top">
				{if $choices[i].choice_img}
					<td{if !$choices[i].choice_txt} colspan="2"{/if}><img src="{$session.project.urls.project_media}{$choices[i].choice_img}" class="key-choice-image-small" /></td>
				{/if}
				{if $choices[i].choice_txt}
					<td{if !$choices[i].choice_img} colspan="2"{/if}>
				{$choices[i].choice_txt}
					</td>
				{/if}
				</tr>
			</table>
		</td>
	</tr>
{/section}
{if $choices|@count==0}
	<tr>
		<td colspan="2"><span class="key-no-choices">{t}(none defined){/t}</span></td>
	</tr>
{/if}
{if $choices|@count < $maxChoicesPerKey}
	<tr>
		<td colspan="2">[<span onclick="$('#choiceForm').submit();" class="pseudo-a">{t}add new choice{/t}</span>]</td>
	</tr>
{/if}
</table>
</form>
</div>

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


{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
