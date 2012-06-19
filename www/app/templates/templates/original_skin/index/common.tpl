{include file="../shared/header.tpl"}

<div id="page-main">

	<div id="alphabet">
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	{if $hasNonAlpha}
		{assign var=l value=$letter|ord}
		{if $l < 97 || $l > 122}
		<span class="alphabet-active-letter">#</span>
		{else}
		<span class="alphabet-letter" onclick="$('#letter').val('#');$('#theForm').submit();">#</span>
		{/if}
	{/if}
	{section name=foo start=97 loop=123 step=1}
	  {assign var=l value=$smarty.section.foo.index|chr}
		{if $l==$letter}
		<span class="alphabet-active-letter">{$l|upper}</span>
		{elseif $alpha[$l]}
		<span class="alphabet-letter" onclick="$('#letter').val('{$l}');$('#theForm').submit();">{$l|upper}</span>
		{else}
		<span class="alphabet-letter-ghosted">{$l|upper}</span>
		{/if}
	{/section}
	</div>

	{if $nameLanguages|@count > 1}
	<div id="commonname-languages">
		{t}Language:{/t}
		<select id="nameLanguage" onchange="
			$('#activeLanguage').val($('#nameLanguage').val());
			$('#letter').val('');
			$('#theForm').submit();"
		>
		<option value="*"{if $activeLanguage=='*'} selected="selected"{/if}>{t}show all{/t}</option>
		<option disabled="disabled">-----------------------</option>
		{foreach name=languageloop from=$nameLanguages key=k item=v}
		<option value="{$v.id}"{if $v.id==$activeLanguage} selected="selected"{/if}>{$v.language}</option>
		{/foreach}
		</select>
		<input type="hidden" id="activeLanguage" name="activeLanguage" value="{$activeLanguage}" />
		<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
	</div>
	{/if}

	<div>
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td class="species-name-cell">
				<a href="../species/taxon.php?id={$v.id}">
				{if $v.label}{$v.label}{else}{$v.transliteration}{/if}
				</a>
			</td>
			<td>({$nameLanguages[$v.language_id].language})</td>
		</tr>
		{/foreach}
		</table>
	</div>

	{if $usePagination}
		{if $useJavascriptLinks}
			{if $prevStart!=-1}
			<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
			{/if}
		{else}
			{if $prevStart!=-1}
			<a href="?start={$prevStart}&letter={$letter}">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<a href="?start={$nextStart}&letter={$letter}">{t}next{/t} ></span>
			{/if}
		{/if}
	{else}
		{if $useJavascriptLinks}
			{if $alphaNav.prev}
			<span class="a" onclick="$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();">< {t}previous{/t}</span>
			{/if}
			{if $nextStart!=-1}
			<span class="a" onclick="$('#letter').val('{$alphaNav.next}');$('#theForm').submit();">{t}next{/t} ></span>
			{/if}
		{else}
			{if $alphaNav.prev}
			<a href="javascript:$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();">< {t}previous{/t}</span>
			{/if}
			{if $alphaNav.next}
			<a href="javascript:$('#letter').val('{$alphaNav.next}');$('#theForm').submit();">{t}next{/t} ></span>
			{/if}
		{/if}
	{/if}

</div>

{include file="../shared/footer.tpl"}
