{include file="../shared/admin-header.tpl"}
<div id="page-main">
<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>
{if $commonnames|@count==0}
{t}No common names have been defined for this taxon.{/t}
{else}
<table>
	<tr>
		<th style="width:150px;">{t}common name{/t}</th>
		<th style="width:150px;">{t}transliteration{/t}</th>
		<th style="width:100px;">{t}language{/t}</th>
		<td  style="width:100px;">{$session.admin.project.languageList[$session.admin.project.default_language_id].language}</td>
		{if $languages|@count>1}
		<td  style="width:200px;" id="project-language-tabs">(languages)</td>
		{/if}
		<th style="width:65px;">{t}move up{/t} /</th>
		<th style="width:40px;">{t}down{/t}</th>
		<th>delete</th>
	</tr>
	{section name=i loop=$commonnames}
	<tr class="tr-highlight" style="vertical-align:bottom">
		<td>{$commonnames[i].commonname}</td>
		<td>{$commonnames[i].transliteration}</td>
		<td>{$commonnames[i].language_name}</td>
		<td>
			<input
				type="text" 
				id="default-{$smarty.section.i.index}"
				onblur="taxonSaveLanguageLabel({$commonnames[i].language_id},this.value,'default')"
				style="width:100px" 
				value="" />
			<script>
				taxonCommonnameLanguages[{$smarty.section.i.index}] = {$commonnames[i].language_id};
			</script>
		</td>
		{if $languages|@count>1}
		<td>
			<input
				type="text" 
				id="other-{$smarty.section.i.index}" 
				onblur="taxonSaveLanguageLabel({$commonnames[i].language_id},this.value,'other')"
				style="width:100px" 
				value="" />
		</td>
		{/if}
		{if $smarty.section.i.first}
		<td></td>
		{else}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonCommonNameAction({$commonnames[i].id},'up');">
			&uarr;
		</td>
		{/if}
		{if $smarty.section.i.last}
		<td></td>
		{else}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonCommonNameAction({$commonnames[i].id},'down');">
			&darr;
		</td>
		{/if}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonCommonNameAction({$commonnames[i].id},'delete');">
			x
		</td>
	</tr>
	{/section}
</table>
{/if}

<hr style="color:#eee;height:1px" />
<form method="post" action="" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="commonname_id" id="commonname_id" value="" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr><td colspan="2">{t}Add a new common name:{/t}</td></tr>
	<tr><td style="width:125px">{t}common name:{/t}</td><td><input type="text" name="commonname" id="commonname" maxlength="64" /></td></tr>
	<tr><td>{t}transliteration:{/t}</td><td><input type="text" name="transliteration" id="transliteration" maxlength="64" /></td></tr>
	<tr><td>{t}language:{/t}</td><td>
		<select name="language_id" id="language">
		{section name=i loop=$allLanguages}
			{if $allLanguages[i].language!=''}<option value="{$allLanguages[i].id}"{if $allLanguages[i].id==$lastLanguage} selected="selected"{/if}>{$allLanguages[i].language}</option>{/if}
		{/section}
		</select>
	</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><input type="button" value="{t}save{/t}" onclick="taxonCommonNameSubmit();"/></td></tr>
</table>
</form>
<br />
{t}After you have added a new common name, you will be allowed to provide the name of its language in the various interface languages that your project uses.{/t}
</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
allActiveView = 'commonnames';

{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawLanguages();
taxonGetCommonnameLabels(allDefaultLanguage);
taxonGetCommonnameLabels(allActiveLanguage);

allLookupNavigateOverrideUrl('common.php?id=%s');

{literal}
});
{/literal}
</script>

{include file="../shared/admin-footer.tpl"}