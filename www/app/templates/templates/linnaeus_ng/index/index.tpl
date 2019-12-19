{include file="../shared/header.tpl" title="Index"}
<div id="page-main">
	<div class="glossary-filter">
		<div id="categories">
			<ul>
				<li class="{if !$hasSpecies}category-no-content{/if}">
					<a class="category-first category{if $type=='lower'}-active{/if}" {if $hasSpecies}href="index.php"{/if}>
					{t}Species and lower taxa{/t}</a>
				</li>
				<li class="{if !$hasHigherTaxa}category-no-content{/if}">
					<a class="category{if $type=='higher'}-active{/if}" {if $hasHigherTaxa}href="higher.php"{/if}>
					{t}Higher taxa{/t}</a>
				</li>
				<li class="{if !$hasCommonNames}category-no-content{/if}">
					<a class="category-last category{if $type=='common'}-active{/if}" {if $hasCommonNames}href="common.php"{/if}>
					{t}Common names{/t}</a>
				</li>
			</ul>
			{if $type=='common'}
				<ul>
					{foreach name=languageloop from=$nameLanguages key=k item=v}
			        	<li>
			        		<a href="index.php?type=common&language={$v.language_id}" class="{if $v.language_id==$language}category-active{/if}">
			        			<!-- value="{$v.language_id}"{if $v.language_id==$language} selected="selected"{/if} -->
			        			{$v.language}
			        		</a>
		        		</li>
					{/foreach}
				</ul>
			{/if}
		</div>
		<div class="alphabet">
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
		<div id="content">
		    {if $type=='higher'}
				{foreach name=taxonloop from=$list key=k item=v}
				<p>
					<!--<a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">{$v.name}</a> {if $v.rank_label}[{$v.rank_label}]{/if}-->
					<a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">{$v.label}</a>
					{if $v.nametype!='isValidNameOf' && $v.ref_taxon!=''}<span class="synonym-addition">  &ndash; {$v.nametype_translated} {if $v.ref_taxon!=''}{t}of{/t} {$v.ref_taxon}{/if}</span>{/if}
				</p>
				{/foreach}
		    {else if $type=='common'}
		        {foreach name=taxonloop from=$list key=k item=v}
		        <p>
		            <a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">
		            {$v.name}</a>
		            {if $language==''} ({$v.language}){/if}
		        </p>
		        {/foreach}
			{else}
				{foreach name=taxonloop from=$list key=k item=v}
				<p>
					<a class="internal-link index-entry" href="../species/taxon.php?id={$v.taxon_id}">{$v.label}</a>
					{if $v.nametype!='isValidNameOf'} &ndash; {$v.nametype_translated}{if $v.ref_taxon!=''} {t}of{/t}
					<a class="internal-link" href="../species/taxon.php?id={$v.taxon_id}">{$v.ref_taxon}</a>{/if}{/if}
				</p>
				{/foreach}
		   {/if}
		</div>
	</div>
    
</div>
{include file="../shared/footer.tpl"}