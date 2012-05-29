{include file="../shared/_search-main.tpl"}
<div id="page-main">



	<div id="reference">

    <div id="mini-header-titles">
        <span id="mini-header-title">{$ref.author_full}, {$ref.year}{$ref.suffix}</span>
        {if $term.synonyms}
        <span id="synonyms">
({foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach})
        </span>
        {/if}
    </div>


	<div id="text">{$ref.text}</div>
	{if $ref.taxa}
		<div id="taxa">
			<div class="title">{t}Referenced in the following taxa:{/t}</div>
		{foreach from=$ref.taxa key=k item=v}
			<div>
				{if $useJavascriptLinks}
				<span class="a" onclick="goTaxon({$v.taxon.id})">{$v.taxon.taxon}</span>
				{else}
				<a href="../species/taxon.php?id={$v.taxon.id}">{$v.taxon.taxon}</a>
				{/if}
				{if $v.taxon.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</div>
		{/foreach}
		</div>
		
	{/if}

	{if $ref.synonyms}
		<div id="synonyms">
			<div class="title">{t}Referenced in the following synonyms:{/t}</div>
		{foreach from=$ref.synonyms key=k item=v}
			<div>
			{if $useJavascriptLinks}			
			<span class="a" onclick="goTaxon({$v.taxon_id},'names')">{$v.synonym}</span>			
			{else}
			<a href="../species/taxon.php?id={$v.taxon_id}&cat=names">{$v.synonym}</a>
			{/if}
			</div>

		{/foreach}
		</div>
	{/if}

	</div>

</div>
