{include file="_search-main-no-tabs.tpl"}

<div id="page-main" class="template-reference">

	{include file="_alphabet.tpl"}


	<div id="content">

		<div id="reference">
			<div id="text">{$ref.text}</div>
				
			{if $ref.taxa}
				<div id="taxa">
					
					<div class="title">
						{t}Referenced in the following taxa:{/t}
					</div>
						
					{foreach $ref.taxa v k}
						<div>
							{if $useJavascriptLinks}
								<span class="a" onclick="goTaxon({$v.taxon.id})">{$v.taxon.label}</span>
							{else}
								<a href="../species/taxon.php?id={$v.taxon.id}">{$v.taxon.label}</a>
							{/if}
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
</div>
