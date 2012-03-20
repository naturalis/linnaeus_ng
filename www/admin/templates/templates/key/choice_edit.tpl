{include file="../shared/admin-header.tpl"}

{include file="_keypath.tpl"}

<div id="page-main">
<form method="post" action="" id="theForm" enctype="multipart/form-data">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$data.id}" />
<input type="hidden" name="action" id="action" value="" />

<span class="key-step-title">
<span id="message-container" style="float:right;"></span><br />

<fieldset>
<legend>{t}Editing choice{/t} "<span id="default-choice-title">...</span>"</legend>
<table style="border-collapse:collapse">

	<tr style="vertical-align:top">
		<td></td>
		<td {$languages[i].language_id} colspan="2">
			{$defaultLanguage.language}
		</td>
	{if $session.admin.project.languages|@count>1}
		<td id="project-language-tabs">
			(languages)
		</td>
{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Text:{/t}</td>
		<td colspan="2">
			<textarea
				name="contentDefault" 
				id="contentDefault" 
				style="width:400px;height:200px;"
				onblur="keySaveChoiceContent('default')" /></textarea>
		</td>
	{if $session.admin.project.languages|@count>1}
		<td>
			<textarea
				name="contentOther" 
				id="contentOther" 
				style="width:400px;height:200px;"
				onblur="keySaveChoiceContent('other')" /></textarea>
		</td>
	{/if}
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Image:{/t}</td>
		<td colspan="2">
		{if $data.choice_img}
			<img
				onclick="allShowMedia('{$session.admin.project.urls.project_media}{$data.choice_img}','{$data.choice_img}');"
				src="{$session.admin.project.urls.project_media}{$data.choice_img}" 
				class="key-choice-image-normal" /><br />
			<span class="a" onclick="keyDeleteImage();">{t}delete image{/t}</span>
			{if $data.choice_image_params!=''}
				<br />
				<span style="color:red">
					Please note: this image has specific attributes for size and positioning,<br/>
					which were inherited from Linnaeus 2. These cannot be changed, and will be<br/>
					erased if you delete the image.
				</span>{/if}
		{else}
			<input type="file" name="image" />
		{/if}
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Target:{/t}</td>
		<td>{t}step:{/t}</td>
		<td colspan="2">
			<select name="res_keystep_id" id="res_keystep_id" onchange="keyCheckTargetIntegrity(this)">
				<option value="-1">{t}new step{/t}</option>
				<option value="0"{if $data.res_taxon_id!=null} selected="selected"{/if}>{t}(none){/t}</option>
			{if $steps|@count>0}
				<option value="-1" disabled="disabled">
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
				</option>
			{/if}
				{section name=i loop=$steps}
				<option value="{$steps[i].id}"{if $steps[i].id==$data.res_keystep_id} selected="selected"{/if}>{$steps[i].number}. {$steps[i].title}</option>
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
		<td>{t}taxon:{/t}</td>
		<td>
			<select name="res_taxon_id" id="res_taxon_id" onchange="keyCheckTargetIntegrity(this)">
				<option value="0">{t}(none){/t}</option>
				<option disabled="disabled">
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
				</option>
				{foreach from=$taxa key=k item=v}
				{assign var=x value=$v.id}
				{if $v.keypath_endpoint==1}
				<option value="{$v.id}"{if $v.id==$data.res_taxon_id} selected="selected"{/if} class="key-taxa-list{if $remainingTaxa[$x]==true}-remain{/if}">
					{$v.taxon}{if $v.is_hybrid==1}&nbsp;x{/if}
					{assign var=first value=false}{/if}
					 ({$ranks[$v.rank_id].rank})
				</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="3">
			<input type="button" onclick="keyChoiceSave();" value="{t}save{/t}" />
			<!-- input type="button" onclick="keyChoiceDelete()" value="{t}delete{/t}" / -->
			<input type="button" onclick="$('#backForm').submit();" value="{t}back{/t}" />&nbsp;&nbsp;
			<input type="button" onclick="keyChoiceUndo();"  value="{t}undo last save{/t}" />
		</td>
	</tr>
</table>
</fieldset>
</form>

{include file="../shared/admin-messages.tpl"}
<div class="page-generic-div">
<p>
{t}Enter the title, text, an optional image and the target of this choice. Title and text are saved automatically after you have entered the text in the appropriate input.{/t}<br />
{t}To change the step-number from the automatically generated one, enter a new number and click 'save'. Please note that the numbers have to be unique in your key.{/t}
</p>


<form method="post" action="step_show.php" id="backForm">
<input type="hidden" name="id" value="{$data.keystep_id}" />
</form>
</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
	allActiveView = 'choiceedit';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	keyChoiceId = {if $data.id}{$data.id}{else}-1{/if};
	keyGetChoiceContent(allDefaultLanguage);
	keyGetChoiceContent(allActiveLanguage);
	{if $data.res_keystep_id!=null}keyCurrentTargetStep = {$data.res_keystep_id};{/if}
	{if $data.res_taxon_id!=null}keyCurrentTargetTaxon = {$data.res_taxon_id};{/if}
	allPrevValSetUp('res_keystep_id');
	allPrevValSetUp('res_taxon_id');
{literal}
});
{/literal}
</script>

{include file="../shared/admin-footer.tpl"}