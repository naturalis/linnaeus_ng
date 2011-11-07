<div id="page-main">
	<div id="reference">
		<div id="author">
			<span id="name">
				{$ref.author_full}
			</span>
			<span id="year">{$ref.year}{$ref.suffix}</span>
		</div>
		<div id="text">{$ref.text}</div>

	{if $ref.taxa}
		<div id="taxa">
			<div class="title">{t}Referenced in the following taxa:{/t}</div>
		{foreach from=$ref.taxa key=k item=v}
			<div>
				<span class="a" onclick="goTaxon({$v.taxon.id})">{$v.taxon.taxon}</span>
				{if $v.taxon.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.project.hybrid_marker}</span>{/if}
			</div>
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

</div>
