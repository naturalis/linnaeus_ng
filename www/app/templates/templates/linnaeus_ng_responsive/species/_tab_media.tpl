{if $mediaOwn.count>0 && $mediaCollected.species>0}

				<div class="mediaTabs">
			        <div class="mediaTab {if $requestData.media=='collected' || $requestData.media==''}media-active{else}media-not-active{/if}">
				        {if $requestData.media=='collected' || $requestData.media==''}
				        	<span>
				            	{t}Soorten/taxa met afbeelding(en){/t} ({$mediaCollected.species})
			            	</span>
						{else}
			            	<a href="?id={$taxon.id}&cat=media&media=collected" class="{$v.className}">
			                	{t}Soorten/taxa met afbeelding(en){/t} ({$mediaCollected.species})
							</a>
						{/if}
			        </div>
			        <div class="mediaTab {if $requestData.media=='own'}media-active{else}media-not-active{/if}">
				        {if $requestData.media=='own'}
				        	<span>
			            		{t}Afbeeldingen bij soort/taxon{/t} ({$mediaOwn.count})
		            		</span>
						{else}
			            	<a href="?id={$taxon.id}&cat=media&media=own" class="{$v.className}">
			                	{t}Afbeeldingen bij soort/taxon{/t} ({$mediaOwn.count})
							</a>
						{/if}
			        </div>
			    </div>
			    
			{/if}

<div>
    <div>

		{if !($mediaOwn.count>0 && $mediaCollected.species>0)}

		{if $mediaOwn.count>0 && $requestData.media!='collected'}
        <h2>
            {t}Totaal aantal foto's:{/t} <span class="total-image-count"></span>
        </h2>
        {elseif $mediaCollected.species>0 &&  $requestData.media!='own'}
        <h4>
			{t}Soorten/taxa met afbeelding(en):{/t} {$mediaCollected.species}
        </h4>
        {/if}
        
        {/if}
        
        <div id="images-container">
        </div>
        
        <input 
        	id="more-images-button" 
            type="button" 
            value="{t}Meer afbeeldingen{/t}"
		/>
    </div>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	{if $mediaCollected.species>0 &&  $requestData.media!='own'}
	var action='get_collected_batch';
	{elseif $mediaOwn.count>0 && $requestData.media!='collected'}
	var action='get_media_batch';
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
			$('#images-container').append(buffer.join("\n"));
			// $('.image-batch:hidden').show('slow');
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
    <a class="fancybox" data-fancybox-group="gallery" href="{$taxon_base_url_images_main}%image%" ptitle="<div>%meta_data%</div>">
    	<div class="imageGradient"></div>
      <img class="speciesimage" alt="Foto %photographer%" title="Foto %photographer%" src="{$taxon_base_url_images_thumb}%thumb%" />
    </a>
  </div>
  <ul>
    <li>{t}Foto{/t}: %photographer%</li>
  </ul>
</div>
-->
</span>
<span style="display:none" id="template-image-cell-collected">
<!--
<div class="imageInGrid3 taxon-page collected">
  <div class="thumbContainer">
    <a href="nsr_taxon.php?id=%id%&cat=media">
    	<div class="imageGradient"></div>
      <img class="speciesimage" alt="Foto %photographer%" ptitle="Foto %photographer%" src="{$taxon_base_url_images_thumb}%thumb%" />
    </a>
  </div>
  <ul>
	<li>%name%</li>
    <li><i>%taxon%</i></li>
  </ul>
</div>
-->
</span>
