

{*
<div>

<style>
.speciesimage {
	max-width:150px;
}
.media-active, .media-not-active {
	width:50%;
	border-top:4px solid #666;
	padding-top:5px;
	font-weight:bold;
}
.media-not-active {
	border-top:1px solid #999;
	padding-top:8px;
	color:#666;
	font-weight:normal;
}
</style>

	{if $mediaOwn.count>0 && $mediaCollected.species>0}

	<div style="width:510px;margin-bottom:10px;text-align:center;font-family:Georgia;">
        <div class="{if $requestData.media=='collected' || $requestData.media==''}media-active{else}media-not-active{/if}">
	        {if $requestData.media=='collected' || $requestData.media==''}
	            {t}Soorten/taxa met afbeelding(en){/t} ({$mediaCollected.species})
			{else}
            	<a href="?id={$taxon.id}&cat=CTAB_MEDIA&media=collected" class="{$v.className}">
                	{t}Soorten/taxa met afbeelding(en){/t} ({$mediaCollected.species})
				</a>
			{/if}
        </div>
        <div class="{if $requestData.media=='own'}media-active{else}media-not-active{/if}">
	        {if $requestData.media=='own'}
            	{t}Afbeeldingen bij soort/taxon{/t} ({$mediaOwn.count})
			{else}
            	<a href="?id={$taxon.id}&cat=CTAB_MEDIA&media=own" class="{$v.className}">
                	{t}Afbeeldingen bij soort/taxon{/t} ({$mediaOwn.count})
				</a>
			{/if}
        </div>
    </div>
    
    {/if}

    <div style="width:100%">

		{if !($mediaOwn.count>0 && $mediaCollected.species>0)}

		{if $mediaOwn.count>0 && $requestData.media!='collected'}
        <h4>
            {t}Totaal aantal afbeeldingen:{/t} <span class="total-image-count"></span>
        </h4>
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
            style="font-size:0.9em;width:100%;margin-top:10px;display:none;"
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
        <a class="zoomimage" rel="prettyPhoto[gallery]" href="{$projectUrls['projectMedia']}%image%" pTitle="<div style='margin-left:125px;'>%meta_data%</div>">
            <img class="speciesimage" alt="Foto %photographer%" title="Foto %photographer%" src="{$projectUrls['projectMedia']}%thumb%" />
        </a>
    </div>
    <dl>
        <dt>{t}Foto{/t}</dt><dd>%photographer%</dd>
    </dl>
</div>
-->
</span>
<span style="display:none" id="template-image-cell-collected">
<!--
<div class="imageInGrid3 taxon-page collected">
    <div class="thumbContainer">
        <a href="nsr_taxon.php?id=%id%&cat=media">
            <img class="speciesimage" alt="Foto %photographer%" title="Foto %photographer%" src="{$projectUrls['projectMedia']}%thumb%" />
        </a>
    </div>
    <dl>
		<dd>%name%</dd>
        <dd><i>%taxon%</i></dd>
    </dl>
</div>
-->
</span>
*}