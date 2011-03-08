{include file="../shared/header.tpl"}

{if $alpha}
<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<span class="letter" onclick="goAlpha('{$v}','index.php')">{$v}</span>
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
	<div id="reference">
		<div id="author">
			<span id="name">
				{$ref.author_full}
			</span>
			<span id="year">{$ref.year}</span>
		</div>
		<div id="text">{$ref.text}</div>

	{if $ref.taxa}
		<div id="taxa">
			<div class="title">{t}Referenced in the following taxa:{/t}</div>
		{foreach from=$ref.taxa key=k item=v}
			<div><span class="a" onclick="goTaxon({$v.taxon_id})">{$v.taxon}</span></div>
		{/foreach}
		</div>
	{/if}

	{if $ref.synonyms}
		<div id="synonyms">
			<div class="title">{t}Referenced in the following synonyms:{/t}</div>
		{foreach from=$ref.synonyms key=k item=v}
			<div><span class="a" onclick="goTaxon({$v.taxon_id},'names')">{$v.synonym}</span></div>
		{/foreach}
		</div>
	{/if}

	</div>

	<div id="navigation">
		<span id="back" onclick="goAlpha('{$ref.author_first|@substr:0:1}','index.php')">{t}back to index{/t}</span>
	</div>
</div>

{include file="../shared/footer.tpl"}
