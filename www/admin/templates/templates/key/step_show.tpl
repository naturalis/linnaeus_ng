{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" action="step_edit.php" id="delForm">
<input type="hidden" name="action" id="action" value="delete" />
<input type="hidden" name="id" value="{$step.id}" />
</form>
<form method="post" action="step_edit.php" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="id" id="id" value="{$step.id}" />
<input type="hidden" name="ref_choice" id="ref_choice" value="" />
<fieldset>
<legend id="key-step-title">{t}Step{/t} {$step.number}{if $step.title}: {$step.title}{/if}</legend>
{$step.content}
{if $step.image}<p><img src="{$session.admin.project.urls.project_media}{$step.image}" /><br />
<span style="color:red">
	Please note: this image is a legacay feature inherited from Linnaeus 2. It cannot be changed.
</span>
</p>{/if}
<p>
[<span onclick="$('#theForm').submit();" class="a">{t}edit{/t}</span>]
[<span onclick="keyDeleteKeyStep();" class="a">{t}delete{/t}</span>]
[<span onclick="window.open('preview.php?step={$step.id}','_self');" class="a">{t}preview{/t}</span>]
</p>
</fieldset>

</form>
<br />
<form method="post" action="choice_edit.php" id="choiceForm">
<input type="hidden" name="id" id="id2" value="" />
<input type="hidden" name="step" value="{$step.id}" />
<fieldset>
<legend id="key-step-choices">{t}Choices{/t}</legend>
<table>
	<tr>
		<th style="width:10px;text-align:right">#</th>
		<th style="width:270px;">{t}choice title{/t}</th>
		<th style="width:240px;" colspan="2">{t}choice leads to{/t}</th>
		<th style="width:90px;">{t}change order{/t}</th>
		<th style="width:30px;"><!-- span class="a" onclick="keyShowChoiceDetails(this,'all')">{t}(show all){/t}</span --></th>
		<th style="width:60px;"></th>
		{*<th style="width:130px;"></th>*}
	</tr>
{section name=i loop=$choices}
	<tr class="tr-highlight" style="vertical-align:top">
		<td class="key-choice-number">{$choices[i].marker}.</td>
		<!-- td class="key-choice-title">{$choices[i].title}</td -->
		<td class="key-choice-title">
			{$choices[i].choice_txt}
			{*
				{$choices[i].choice_txt|@substr:0:50}{if $choices[i].choice_txt|@count_characters>50}...{/if}
				<i><span class="a" onclick="keyShowChoiceDetails(this,{$smarty.section.i.index})">{t}show{/t}</span></i>
			*}
		</td>
		<td class="key-choice-leadsto">&rarr;</td>
		<td class="key-choice-target">
		{if $choices[i].res_keystep_id!=''}
			{if $choices[i].res_keystep_id!='-1'}
			<span
				onclick="$('#choice').val({$choices[i].id});$('#next').val({$choices[i].res_keystep_id});$('#nextForm').submit();" 
				class="a">
				{t}Step{/t} {if $choices[i].target_number}{$choices[i].target_number}: {/if}{$choices[i].target}
			</span>
			{else}
			<span
				onclick="$('#id').val('');$('#ref_choice').val({$choices[i].id});$('#theForm').submit();" 
				class="a">
				{$choices[i].target}
			</span>
			{/if}
			{elseif $choices[i].res_taxon_id!=''}
			{t}Taxon:{/t}
			<span
				onclick="window.open('../species/taxon.php?id={$choices[i].res_taxon_id}','_self')"
				class="a">
				{$choices[i].target}
			</span>
			{else}
			{$choices[i].target}
			</span>
			{/if}
		</td>
		<td class="key-choice-arrow">{if $smarty.section.i.index<$choices|@count-1}
			<span class="a" onclick="$('#move').val({$choices[i].id});$('#direction').val('down');$('#moveForm').submit();">&darr;</span>
			{else}
			<span class="a" onclick="$('#move').val({$choices[i].id});$('#direction').val('up');$('#moveForm').submit();">&uarr;</span>
			{/if}
		</td>
		
		<td class="key-choice-edit">[<span class="a" onclick="$('#id2').val({$choices[i].id});$('#choiceForm').submit();">{t}edit{/t}</span>]</td>
		<td class="key-choice-edit">[<span class="a" onclick="keyChoiceDelete({$choices[i].id})">{t}delete{/t}</span>]</td>
		{* <td class="key-choice-edit">[<a href="step_edit.php?insert={$choices[i].id}" title="{t}insert step between choice and target{/t}">{t}insert step{/t}</a>]</td> *}
	</tr>
	<tr id="choice-{$smarty.section.i.index}" class="key-choice-details-invisible">
		<td>&nbsp;</td>
		<td colspan="7">
			<table>
				<tr style="vertical-align:top">
				{if $choices[i].choice_img}
					<td{if !$choices[i].choice_txt} colspan="2"{/if}>
						<img
							onclick="allShowMedia('{$session.admin.project.urls.project_media}{$choices[i].choice_img}','{$choices[i].choice_img}');" 
							src="{$session.admin.project.urls.project_media}{$choices[i].choice_img}"
							class="key-choice-image-small" /></td>
				{/if}
				{if $choices[i].choice_txt}
					<td{if !$choices[i].choice_img} colspan="2"{/if} class="key-choice-details">
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
		<td colspan="8"><span class="key-no-choices">{t}(none defined){/t}</span></td>
	</tr>
{/if}
	<tr>
		<td colspan="8">&nbsp;</td>
	</tr>
	<tr>
{if $choices|@count < $maxChoicesPerKeystep}
		<td colspan="8">[<span onclick="$('#choiceForm').submit();" class="a">{t}add new choice{/t}</span>]</td>
{else}
		<td colspan="8">{t _s1=$maxChoicesPerKeystep}(you have reached the maximum of %s choices per step){/t}</td>
{/if}
	</tr>
</table>
</fieldset>
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

<form method="post" action="choice_edit.php" id="delChoiceForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id3" value="" />
<input type="hidden" name="action" value="delete" />
</form>

<div id="key-taxa-list-remain">
<fieldset>
<legend id="key-taxa-list-remain-header">{t}Possible outcomes{/t}</legend>
{if $remainingTaxa=='none'}
{t}Your key currently has no taxa attached to it.{/t}
{elseif $remainingTaxa || $choices|@count==0}
This is a list of the taxa that are a possible outcome of the key, computed from the current step ({$remainingTaxa|@count}):<br />
{section name=i loop=$remainingTaxa}
&#149;&nbsp;{$remainingTaxa[i].taxon}<br />
{/section}
{if $remainingTaxa|@count==0}{t}(none){/t}{/if}
{else}
{t _s1='<a href="process.php">' _s2='</a>'}You need to reprocess your key to see the list of possible outcomes. Go %shere%s to do so.{/t}
{/if}
</fieldset>
</div>

{include file="../shared/admin-footer.tpl"}
