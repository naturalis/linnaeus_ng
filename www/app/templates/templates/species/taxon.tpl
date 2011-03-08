{include file="../shared/header.tpl"}

<div id="categories">
<table>
	<tr>
	{foreach from=$categories key=k item=v}
		<td {if $activeCategory==$v.id}class="category-active"{else}class="category" onclick="goTaxon({$taxon.id},{$v.id})"{/if}>{$v.title}</td><td class="space"></td>
		{/foreach}
{if $contentCount.media>0}
		<td {if $activeCategory=='media'}class="category-active"{else}class="category" onclick="goTaxon({$taxon.id},'media')"{/if}>{t}Media{/t}</td><td class="space"></td>
{/if}
		<td {if $activeCategory=='classification'}class="category-active"{else}class="category" onclick="goTaxon({$taxon.id},'classification')"{/if}>{t}Classification{/t}</td><td class="space"></td>
{if $contentCount.literature>0}
		<td {if $activeCategory=='literature'}class="category-active"{else}class="category" onclick="goTaxon({$taxon.id},'literature')"{/if}>{t}Literature{/t}</td><td class="space"></td>
{/if}
{if $contentCount.names>0}
		<td {if $activeCategory=='names'}class="category-active"{else}class="category" onclick="goTaxon({$taxon.id},'names')"{/if}>{t}Synonyms{/t}</td>
{/if}
	</tr>
</table>
</div>

<div id="page-main">
{if $activeCategory=='classification'}
<div id="classification">
	<table>
	{foreach from=$content key=k item=v name=classification}
		<tr>
			<td {if $smarty.foreach.classification.index==$content|@count-1}class="current-taxon"{else}class="a" onclick="{if $v.lower_taxon==1}goTaxon{else}goHigherTaxon{/if}({$v.id})"{/if}>{$v.taxon}</td>
			<td>({$v.rank})</td>
		</tr>
	{/foreach}
	</table>
</div>
{elseif $activeCategory=='literature' && $contentCount.literature>0}
<div id="literature">
	{foreach from=$content key=k item=v}
	<div class="author">
		<span class="name">
			{$v.author_full}
		</span>
		<span class="year">{$v.year}</span>
	</div>
	<div class="text">{$v.text}</div>
	{/foreach}
</div>
{elseif $activeCategory=='names' && $contentCount.names>0}
{if $content.synonyms}
<div id="synonyms">
	<div class="title">{t}Synonyms{/t}</div>
	<table>
	{foreach from=$content.synonyms key=k item=v}
		<tr class="highlight">
			<td>{$v.synonym}</td>
			<td>{if $v.reference}<span onclick="goLiterature({$v.reference.id});" class="a">{$v.reference.author_full}</span>{/if}</td>
		</tr>
		{* $v.remark *}
	{/foreach}
	</table>
</div>
{/if}
{if $content.common}
<div id="common">
	<div class="title">{t}Common Names{/t}</div>
	<table>
	<thead>
		<tr class="highlight">
			<th>{t}Common name{/t}</th>
			<th>{t}Transliteration{/t}</th>
			<th>{t}Language{/t}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$content.common key=k item=v}
		<tr class="highlight">
			<td>{$v.commonname}</td>
			<td>{$v.transliteration}</td>
			<td>{$v.language_name}</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
</div>
{/if}
{elseif $activeCategory=='media' && $contentCount.media>0}
<div id="media">
	<table>
	{foreach from=$content key=k item=v}
	<tr>
		<td>{$v.mime_type}</td>
		<td>{$v.description}</td>
	</tr>
	{/foreach}
	</table>
</div>
{else}
<div id="content">
{$content}
</div>
{/if}

	<div id="navigation">
		<span id="back" onclick="window.open('{if $taxon.lower_taxon==1}../species/{else}../highertaxa/{/if}','_self')">{t}back to index{/t}</span>
	</div>
</div>

{include file="../shared/footer.tpl"}
