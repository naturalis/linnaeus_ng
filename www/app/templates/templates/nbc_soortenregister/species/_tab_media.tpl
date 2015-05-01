<div>
    <div style="width:100%">
		{if $mediaOwn.count>0}
        <h4>
            Totaal aantal afbeeldingen: <span class="total-image-count"></span>
        </h4>
        {elseif $mediaCollected.species>0}
        <h4>
			Soorten/taxa met afbeelding(en): {$mediaCollected.species}
        </h4>
        {/if}
        
        <div id="images-container">
        </div>
        
        <input id="more-images-button" type="button" value="Meer afbeeldingen"  style="font-size:0.9em;width:100%;margin-top:10px;display:none;" />
    </div>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	{if $mediaOwn.count>0}
	var action='get_media_batch';
	{elseif $mediaCollected.species>0}
	var action='get_collected_batch';
	{/if}
	var page=0;
	var id={$taxon.id};
	
	function setTotalImageCount(i)
	{
		$('.total-image-count').html(i);
	}
	
	function setMoreImagesButton(total)
	{
		$('#more-images-button').toggle($('.thumbContainer').length<total);
	}
	
	function setImageBatch(images)
	{
		if (!images) return;

		var template=
			(action=='get_collected_batch' ?
				$('#template-image-cell-collected').html() :
				$('#template-image-cell').html()
			).replace('<!--','').replace('-->','');

		var buffer=Array();
		for(var i=0;i<images.length;i++)
		{
			var image=images[i];
			buffer.push(
				template
					.replace( /%image%/g, image.image ? image.image : '' )
					.replace( /%meta_data%/g, image.meta_data ? escape(image.meta_data) : '' )
					.replace( /%photographer%/g, image.photographer ? image.photographer : '' )
					.replace( /%thumb%/g, image.thumb ? image.thumb : '' )
					.replace( /%id%/g, image.taxon_id ? image.taxon_id : '' )
					.replace( /%name%/g, image.name ? image.name : '')
					.replace( /%taxon%/g, image.taxon ? image.taxon : '' )
			);
		}
		
		if (page==1)
		{
			$('#images-container').append(buffer.join("\n"));
		}
		else
		{
			$('#images-container').append("<span class='image-batch' style='display:none'>"+buffer.join("\n")+"</span>");
			$('.image-batch:hidden').show('slow');
		}
		
		nbcPrettyPhotoInit();
	}

	function getMediaBatch()
	{
		$.ajax({
			url : 'ajax_interface_nsr.php',
			type: "POST",
			data : ({
				action : action,
				id : id,
				page : page,
				time : allGetTimestamp()
			}),
			success : function (data) {
				var data=$.parseJSON(data)
				//console.dir(data);
				setTotalImageCount(data.count);
				setImageBatch(data.data);
				setMoreImagesButton(data.count);
			}
		});	
	}
	
	function getMediaNextBatch()
	{
		page++;
		getMediaBatch();
	}

	$('#more-images-button').on('click',function() { getMediaNextBatch(); } );

	getMediaNextBatch();	
		
});
</script>


<span style="display:none" id="template-image-cell">
<!--
<div class="imageInGrid3 taxon-page">
    <div class="thumbContainer">
        <a class="zoomimage" rel="prettyPhoto[gallery]" href="{$taxon_main_image_base_url}%image%" pTitle="<div style='margin-left:125px;'>%meta_data%</div>">
            <img class="speciesimage" alt="Foto %photographer%" title="Foto %photographer%" src="http://images.naturalis.nl/160x100/%thumb%" />
        </a>
    </div>
    <dl>
        <dt>Foto</dt><dd>%photographer%</dd>
    </dl>
</div>
-->
</span>
<span style="display:none" id="template-image-cell-collected">
<!--
<div class="imageInGrid3 taxon-page collected">
    <div class="thumbContainer">
        <a href="nsr_taxon.php?id=%id%&cat=media">
            <img class="speciesimage" alt="Foto %photographer%" title="Foto %photographer%" src="http://images.naturalis.nl/160x100/%thumb%" />
        </a>
    </div>
    <dl>
		<dd>%name%</dd>
        <dd><i>%taxon%</i></dd>
    </dl>
</div>
-->
</span>



{*
		<div>
		
			{if $mediaOwn.data}
				<div style="width:100%">
					<h4>
						Totaal aantal afbeeldingen: {$mediaOwn.count}
					</h4>
					<div>
	
					{foreach from=$mediaOwn.data item=v}
						{if $search.img && $search.img==$v.image}
							{$pp_popup=[{$v.image},{$v.meta_data}]}
						{/if}
						<div class="imageInGrid3 taxon-page">
							<div class="thumbContainer">
								<a class="zoomimage" rel="prettyPhoto[gallery]" href="{$taxon_main_image_base_url}{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
									<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
								</a>
							</div>
							<dl>
								<dt>Foto</dt><dd>{$v.photographer}</dd>
							</dl>
						</div>
					{/foreach}
					</div>
				</div>
			{/if}

			{if $mediaOwn.data && $mediaCollected.data}
			<p>&nbsp;</p>
			{/if}			
		
			{if $mediaCollected.data}
				<div  style="width:100%">
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

*}