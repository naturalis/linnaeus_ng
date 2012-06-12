{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}<div id="page-main">

	<div id="alphabet">
	{if $alpha|@count!=0}
	{t}Click to browse:{/t}&nbsp;
	{foreach name=loop from=$alpha key=k item=v}
	{if $v==$letter}
	<span class="alphabet-active-letter">{$v}</span>
	{else}
	<span class="alphabet-letter" onclick="$('#letter').val('{$v}');$('#theForm').submit();">{$v}</span>
	{/if}
	{/foreach}
	{/if}
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	</div>

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

	<div id="content">
        {foreach name=taxonloop from=$taxa key=k item=v}
        <p>
            <a class="internal-link" href="../species/taxon.php?id={$v.id}&cat=names">
            {if $v.label}{$v.label}{else}{$v.transliteration}{/if}</a>
            {if $activeLanguage=='*'} ({$nameLanguages[$v.language_id].language}){/if}
        </p>
        {/foreach}
        
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">

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
	
	</div>
{/if}
</div>

{include file="../shared/footer.tpl"}
