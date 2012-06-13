{include file="_search-main-no-tabs.tpl"}
<div id="page-main">
	<div id="reference">
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
