{include file="../shared/header.tpl"}
<style>
#categories {
	width:75%;
 }
</style>

<div id="header-titles">
	<span id="header-title">{t}Index{/t}</span>
</div>

<div id="categories">
	<ul>
		<li>
			<a class="category{if $type=='lower'}-active{/if} category-first{if $hasSpecies==1}" href="index.php"{else} category-no-content"{/if}>
			{t}Species and lower taxa{/t}</a>
		</li>
		<li>
			<a class="category{if $type=='higher'}-active{/if}{if $hasHigherTaxa==1}" href="higher.php"{else} category-no-content"{/if}>
			{t}Higher taxa{/t}</a>
		</li>
		<li>
			<a class="category{if $type=='common'}-active{/if} category-last{if $hasCommonNames==1}" href="common.php"{else} category-no-content"{/if}>
			{t}Common names{/t}</a>
		</li>
	</ul>

    {if $type=='common'}
	<div style="float:right;">
		{t}Language:{/t}
		<select id="nameLanguage" onchange="window.open('index.php?type=common&language='+$('#nameLanguage').val()+'&letter=','_self');">
		<option value=""{if $activeLanguage==''} selected="selected"{/if}>{t}Show all{/t}</option>
		<option disabled="disabled">-----------------------</option>
		{foreach name=languageloop from=$nameLanguages key=k item=v}
		<option value="{$v.id}"{if $v.id==$language} selected="selected"{/if}>{$v.language}</option>
		{/foreach}
		</select>
		<input type="hidden" id="activeLanguage" name="activeLanguage" value="{$activeLanguage}" />
		<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
	</div>
	{/if}
</div>

<div id="alphabet" class="test">
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	{if $alpha.hasNonAlpha}
		{assign var=l value=$letter|ord}
		{if $l < 97 || $l > 122}
			<span class="alphabet-active-letter">#</span>
		{else}
			<a href="index.php?{$querystring}&letter=#">#</a>
		{/if}
	{/if}

	{section name=foo start=97 loop=123 step=1}
		{assign var=l value=$smarty.section.foo.index|chr}
		{if $l==$letter}
			<span class="alphabet-active-letter">{$l|upper}</span>
		{elseif $alpha.alphabet[$l]}
			<a class="alphabet-letter" href="index.php?{$querystring}&letter={$l}">{$l|upper}</a>
		{else}
			<span class="alphabet-letter-ghosted">{$l|upper}</span>
		{/if}
	{/section}


</div>

<div id="page-main">

    <div id="content">

    {if $type=='higher'}

		{foreach name=taxonloop from=$list key=k item=v}
		<p>
			<a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">{$v.name}</a>
			{if $v.nametype!='isValidNameOf' && $v.ref_taxon!=''}<span class="synonym-addition"> ({$v.ref_taxon})</span>{/if}{if $v.source =='synonym'}{t}[syn.]{/t}{/if}
		</p>
		{/foreach}

    {else if $type=='common'}

        {foreach name=taxonloop from=$list key=k item=v}
        <p>
            <a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">
            {if $v.commonname}{$v.commonname}{else}{$v.transliteration}{/if}</a>
            {if $language==''} ({$v.language}){/if}
        </p>
        {/foreach}

	{else}

		{foreach name=taxonloop from=$list key=k item=v}
		<p>
			<a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">{$v.name}</a> 
			{if $v.nametype!='isValidNameOf'} &ndash; {t}synonym{/t}{if $v.ref_taxon!=''} {t}of{/t}
			<a class="internal-link" href="../species/taxon.php?id={$v.taxon_id}">{$v.ref_taxon}</a> 
			{$v.authorship}{/if}{/if}
		</p>
		{/foreach}

   {/if}

</div>

</div>

{include file="../shared/footer.tpl"}
