		<div>
		
			{if $mediaOwn.data}
				<div style="width:100%;margin-top:20px;">
					<h3>
						{if $mediaOwn.count!=1}{t}Afbeeldingen:{/t}{else}{t}Afbeelding:{/t}{/if} {$mediaOwn.count}
					</h3>
					<div>
	
					{foreach from=$mediaOwn.data item=v}
						{if $search.img && $search.img==$v.image}
							{$pp_popup=[{$v.image},{$v.meta_data}]}
						{/if}
						<div class="imageInGrid3 taxon-page">
							<div class="thumbContainer">
								<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/original/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
									<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
								</a>
							</div>
							<dl>
								<dt>{t}Foto{/t}</dt><dd>{$v.photographer}</dd>
							</dl>
						</div>
					{/foreach}
					</div>
				</div>
			{/if}

			{if $mediaOwn.data && $mediaCollected.data}
			<hr />
			{/if}			
		
			{if $mediaCollected.data}
				<div  style="width:100%">
					<h4>
						{t}Soorten/taxa met afbeelding(en):{/t} {$mediaCollected.species}
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
