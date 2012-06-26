{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}

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


	<div id="commonname-languages">
		{t}Language:{/t}
		<select id="nameLanguage" onchange="
			$('#activeLanguage').val($('#nameLanguage').val());
			$('#letter').val('');
			$('#theForm').submit();"
		>
		<option value="*"{if $activeLanguage=='*'} selected="selected"{/if}>{t}Show all{/t}</option>
		<option disabled="disabled">-----------------------</option>
		{foreach name=languageloop from=$nameLanguages key=k item=v}
		<option value="{$v.id}"{if $v.id==$activeLanguage} selected="selected"{/if}>{$v.language}</option>
		{/foreach}
		</select>
		<input type="hidden" id="activeLanguage" name="activeLanguage" value="{$activeLanguage}" />
		<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
	</div>



	</div>



<div id="page-main">



	<div id="content">
        {foreach name=taxonloop from=$taxa key=k item=v}
        <p>
            <a class="internal-link" href="../species/taxon.php?id={$v.id}">
            {if $v.label}{$v.label}{else}{$v.transliteration}{/if}</a>
            {if $activeLanguage=='*'} ({$nameLanguages[$v.language_id].language}){/if}
        </p>
        {/foreach}
        
	</div>
</div>

{include file="../shared/footer.tpl"}
