{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" action="step_edit.php" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="id" id="id" value="{$step.id}" />
<input type="hidden" name="ref_choice" id="ref_choice" value="" />
<fieldset>
<legend id="key-step-title">{t}Step{/t} {$step.number}{if $step.title}: {$step.title}{/if}</legend>
{$step.content}
{if $step.image}<p><img src="{$session.admin.project.urls.project_media}{$step.image}" /><br />
<span style="color:red">
	Please note: this image is a legacy feature inherited from Linnaeus 2. It cannot be changed.</span><br />
	<span class="a" onclick="keyDeleteImage();">{t}delete image{/t}</span> | 
	<span class="a" onclick="keyDeleteAllImages();">{t}delete all images{/t}</span>
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
		<th style="text-align:right">#</th>
		<th style="width:450px;">{t}choice title{/t}</th>
		<th style="width:90px;">{t}image{/t}</th>
		<th style="width:100px;">{t}choice leads to{/t}</th>
		<th style="width:80px;" class="key-choice-arrow">{t}move{/t}</th>
		<th style="width:30px;"><!-- span class="a" onclick="keyShowChoiceDetails(this,'all')">{t}(show all){/t}</span --></th>
		<th style="width:30px;"></th>
	</tr>
{section name=i loop=$choices}
	<tr class="tr-highlight" style="vertical-align:top">

		<td class="key-choice-number">{$choices[i].marker}.</td>

		<td class="key-choice-title">
			{$choices[i].choice_txt|@strip_tags}
		</td>
		
		<td>
			{if $choices[i].choice_img}
			<img
				onclick="allShowMedia('{$session.admin.project.urls.project_media}{$choices[i].choice_img}','{$choices[i].choice_img}');" 
				src="{$session.admin.project.urls.project_media}{$choices[i].choice_img}"
				class="key-choice-image-small" />
			{/if}
		</td>
		
		<td class="key-choice-target">
		&rarr;
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
<table>
	<tr style="vertical-align:top">
		<td>

	{if $didKeyTaxaChange}
		<a href="store.php?step={$step.id}">{t}You need to process and store your key tree to see the list of possible outcomes.{/t}</a>
	{else}
		<fieldset style="width:300px">
		<legend id="key-taxa-list-remain-header">{t}Remaining taxa{/t}</legend>
		{foreach from=$taxonDivision.remaining item=v}
		&#149;&nbsp;{$v.taxon}<br />
		{/foreach}
		</fieldset>
				</td>
				<td>
		<fieldset style="width:300px">
		<legend id="key-taxa-list-remain-header">{t}Excluded taxa{/t}</legend>
		{foreach from=$taxonDivision.excluded item=v}
		&#149;&nbsp;{$v.taxon}<br />
		{/foreach}
		</fieldset>
	{/if}
		</td>
	</tr>
</table>

</div>

{include file="../shared/admin-footer.tpl"}
