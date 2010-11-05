{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form method="post" action="" id="theForm" enctype="multipart/form-data">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$data.id}" />
<input type="hidden" name="action" id="action" value="" />

<span class="key-step-title">
{if $data.id}{t}Editing choice{/t}{else}{t}Add a new choice{/t}{/if}
</span><br /><br />

<table style="border-collapse:collapse">
	<tr style="vertical-align:top">
		<td>{t}Title:{/t}</td>
		<td colspan="2"><input type="text" name="title" value="{$data.title}" /> *</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td colspan="2"><textarea name="choice_txt" cols="50" rows="5">{$data.choice_txt}</textarea></td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Image:{/t}</td>
		<td colspan="2">
		{if $data.choice_img}
			<img src="{$session.project.urls.project_media}{$data.choice_img}" style=" width:200px;" /><br />
			<span class="pseudo-a" onclick="keyDeleteImage();">{t}delete image{/t}</span>
		{else}
			<input type="file" name="image" />
		{/if}
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Target:{/t}</td>
		<td>step:</td>
		<td>
			<select name="res_keystep_id" id="res_keystep_id" onchange="keyCheckTargetIntegrity(this)">
				<option value="-1">{t}new step{/t}</option>
				<option value="0"{if $data.res_taxon_id!=null} selected="selected"{/if}>{t}(none){/t}</option>
{if $steps|@count>0}
				<option value="-1" disabled="disabled">&nbsp</option>{/if}
{section name=i loop=$steps}<option value="{$steps[i].id}"{if $steps[i].id==$data.res_keystep_id} selected="selected"{/if}>{$steps[i].title}</option>
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
				<option disabled="disabled">&nbsp</option>
{section name=i loop=$taxa}<option value="{$taxa[i].id}"{if $taxa[i].id==$data.res_taxon_id} selected="selected"{/if}>{section name=foo loop=$taxa[i].level-$taxa[0].level}&nbsp;{/section}{$taxa[i].taxon}</option>
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

<form method="post" action="step_show.php" id="backForm"></form>
</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}