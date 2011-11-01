{include file="../shared/header.tpl"}

<div id="page-main">

	<div id="commonname-languages">
		{t}Language:{/t}
		<select name="activeLanguage" id="activeLanguage" onchange="$('#theForm').submit();">
		<option value="*"{if $activeLanguage=='*'} selected="selected"{/if}>{t}show all{/t}</option>
		<option disabled="disabled">-----------------------</option>
		{foreach name=languageloop from=$nameLanguages key=k item=v}
		<option value="{$v.id}"{if $v.id==$activeLanguage} selected="selected"{/if}>{$v.language}</option>
		{/foreach}
		</select>
	</div>

	<div>
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td class="species-name-cell">
				<a href="../species/taxon.php?id={$v.id}&cat=names">
				{if $v.label}{$v.label}{else}{$v.transliteration}{/if}
				</a>
			</td>
			<td>({$nameLanguages[$v.language_id].language})</td>
		</tr>
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	</div>
{/if}
</div>

{include file="../shared/footer.tpl"}
