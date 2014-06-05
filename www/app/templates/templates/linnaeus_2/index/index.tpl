{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}

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
			<a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.label}</a>
			{if $v.source =='synonym' && $v.ref_taxon!=''}<span class="synonym-addition"> ({$v.ref_taxon})</span>{/if}{if $v.source =='synonym'}{t}[syn.]{/t}{/if}
		</p>
		{/foreach}

    {else if $type=='common'}

        {foreach name=taxonloop from=$list key=k item=v}
        <p>
            <a class="internal-link" href="../species/taxon.php?id={$v.taxon_id}">
            {if $v.commonname}{$v.commonname}{else}{$v.transliteration}{/if}</a>
            {if $language==''} ({$v.language}){/if}
        </p>
        {/foreach}
	
	{else}

		{foreach name=taxonloop from=$list key=k item=v}
		{if $v.is_empty!=1}
		<p>
			<a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.label}</a> {$v.author}
			{if $v.source =='synonym'} &ndash; {t}synonym{/t}{if $v.ref_taxon!=''} {t}of{/t} 
			<a class="internal-link" href="../species/taxon.php?id={$v.id}">{$v.ref_taxon}</a>{if $v.ref_author} {$v.ref_author}{/if}{/if}{/if}
		</p>
		{/if}
		{/foreach}

   {/if}

</div>
    
</div>

{include file="../shared/footer.tpl"}
