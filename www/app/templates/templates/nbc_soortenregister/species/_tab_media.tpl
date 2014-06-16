		<div>
		
			{if $mediaOwn.data}
				<h4>
					Afbeelding{if $mediaOwn.count!=1}en{/if}: {$mediaOwn.count}
				</h4>
				<div>

				{foreach from=$mediaOwn.data item=v}
					{if $search.img && $search.img==$v.image}
						{$pp_popup=[{$v.image},{$v.meta_data}]}
					{/if}
					<div class="imageInGrid3 taxon-page">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
						<dl>
							<dt>Foto</dt><dd>{$v.photographer}</dd>
						</dl>
					</div>
				{/foreach}
			{/if}

			{if $mediaOwn.data && $mediaCollected.data}
			<p>&nbsp;</p>
			{/if}			
		
			{if $mediaCollected.data}
				<h4>
					Soorten/taxa met afbeelding(en): {$mediaCollected.species}
				</h4>
				<div>
				{foreach from=$mediaCollected.data item=v}
					<div class="imageInGrid3 taxon-page collected">
						<div class="thumbContainer">
							<a href="nsr_taxon.php?id={$v.taxon_id}&cat=media">
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
						<dl>
							{if $v.name}<dd>{$v.name}</dd>{/if}
							<dd><i>{$v.taxon}</i></dd>
						</dl>
					</div>
				{/foreach}
				</div>

			{/if}
			
			</div>
			
			{if $mediaOwn.data && $mediaCollected.data}
			{assign var=results value=$mediaCollected}
			{else if $mediaCollected.data}
			{assign var=results value=$mediaCollected}
			{else}
			{assign var=results value=$mediaOwn}
			{/if}

			{assign var=pgnResultCount value=$results.count}
			{assign var=pgnResultsPerPage value=$results.perpage}
			{assign var=pgnCurrPage value=$search.page}
			{assign var=pgnURL value=$smarty.server.PHP_SELF}
			{assign var=pgnQuerystring value=$querystring}
			{include file="../shared/_paginator.tpl"}

			{if $showMediaUploadLink}			
			<div>
				<p>&nbsp;</p>
				<p>
					<!-- Heeft u mooie foto's van deze soort? Voeg ze dan <a href="">hier</a> toe en draag zo bij aan het Soortenregister.. -->
				</p>
			</div>
			{/if}
		
		</div>