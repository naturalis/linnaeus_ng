<div id="page-main" class="template-reference">

	{include file="_alphabet.tpl"}


	<div id="content">

    <p id="header-titles-small">
    	<span id="mini-header-title">{$ref.author_full}, {$ref.year_full}</span>
    </p>

		<div id="reference">
			<div id="text">{$ref.text}</div>
				
			{if $ref.taxa}
				<div id="taxa">
					
					<div class="title">
						{t}Referenced in the following taxa:{/t}
					</div>
						
					{foreach $ref.taxa v k}
						<div>
							<a href="../species/taxon.php?id={$v.taxon.id}">{$v.taxon.label}</a>
						</div>
					{/foreach}
				</div>
			{/if}

			{if $ref.synonyms}
				<div id="synonyms">
					
					<div class="title">
						{t}Referenced in the following synonyms:{/t}
					</div>

					{foreach $ref.synonyms v k}
						<div>
                            <a href="../species/taxon.php?id={$v.taxon_id}&cat=names">{$v.synonym}</a>
						</div>
					{/foreach}
				</div>
			{/if}

		</div>
	</div>
</div>
