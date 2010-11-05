{include file="../shared/admin-header.tpl"}

{literal}
<style>
#key-path{
	margin-bottom:10px;
}
.key-step-title,.key-choice-title{
	font-weight:bold;
}
.key-no-choices {
	font-style:italic;
}
</style>
{/literal}

<div id="page-main">

<div id="key-path">
Path:
{section name=j loop=$keyPath}
{if $smarty.section.j.index!=0}
&nbsp;&rarr;&nbsp;
{/if}
{if $smarty.section.j.index!=$keyPath|@count-1}
<span class="pseudo-a" onclick="$('#id').val({$keyPath[j].id});$('#nextForm').submit()">{$keyPath[j].name}</span>
{if $keyPath[j].choiceName}: <span class="pseudo-a" onclick="$('#choice').val({$keyPath[j].choice});$('#theForm').submit();">{$keyPath[j].choiceName}</span>{/if}
{else}
{$keyPath[j].name}
{/if}
{/section}
</div>


{if !$data.id || $data.id==-1 || $edit==true}

<form method="post" action="" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<span class="key-step-title">{t}New step{/t}</span><br />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td><input type="text" name="title" value="{$data.title}" /> *</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td><textarea name="content" cols="50" rows="5">{$data.content}</textarea></td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2"><input type="submit" value="{t}save{/t}"></td>
	</tr>
</table>
</form>

{elseif $data|@count<1}

<input type="hidden" name="start" id="start" value="" />

{t}Multiple steps are available, but there is no starting step. Please choose the step which is the starting step of your key:{/t}
<table>
{section name=i loop=$data}
	<tr>
		<td>{$data[i].title}</td>
		<td>{$data[i].choiceCount}</td>
		<td>[<span class="pseudo-a" onclick="$('#start').val({$data[i].id});$('#theForm').submit();">{t}set as start step{/t}</span>]</td></tr>
{/section}
</table>

{else}

<span class="key-step-title">{t}Step{/t}</span><br />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td>{$data.title}</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td>{$data.content}</td>
	</tr>
</table>
<br />
<span class="key-choice-title">{t}Choices:{/t}</span><br />
<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<th>{t}Nr{/t}</th>
		<th>{t}Title{/t}</th>
		<th>{t}Target{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$choices}
	<tr style="vertical-align:top">
		<td>{$choices[i].show_order}</td>
		<td>{$choices[i].title}</td>
		<td>{if $choices[i].res_keystep_id!=''}
			Step: 
			{if $choices[i].res_keystep_id!='-1'}
			<span
				onclick="$('#id').val({$choices[i].res_keystep_id});$('#ref_choice').val({$choices[i].id});$('#nextForm').submit();" 
				class="pseudo-a">
				{$choices[i].target}
			</span>
			{else}
			<span
				onclick="$('#ref').val({$choices[i].id});$('#newForm').submit();" 
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
		<td>[<span class="pseudo-a" onclick="$('#choice').val({$choices[i].id});$('#theForm').submit();">{t}edit{/t}</span>]</td>
	</tr>
{/section}
	<tr style="vertical-align:top">
		<td colspan="2">{if $choices|@count==0}<span class="key-no-choices">{t}(none defined){/t}{else}&nbsp;{/if}</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">[<span onclick="$('#theForm').submit();" class="pseudo-a">{t}add new choice{/t}</span>]</td>
	</tr>
</table>
<form method="post" action="edit.php" id="newForm">
<input type="hidden" name="id" value="-1" />
<input type="hidden" id="ref" name="ref" value="" />
</form>
<form method="post" action="edit.php" id="nextForm">
<input type="hidden" id="id" name="id" value="" />
<input type="hidden" id="ref_choice" name="ref_choice" value="" />
</form>
<form method="post" action="choice.php" id="theForm">
<input type="hidden" name="step" value="{$data.id}" />
<input type="hidden" id="choice" name="choice" value="" />
</form>

{/if}
</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
