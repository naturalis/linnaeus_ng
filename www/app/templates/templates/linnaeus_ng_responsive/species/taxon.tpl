{include file="../shared/header.tpl"}

<div id="taxonHeader">

	<div id="headerImage">
		<div class="titles">
			{if $overviewImage.photographer}
			<div id="taxonImageCredits">
				<span class="photographer-title">{t}Foto:{/t}</span> {$overviewImage.photographer} 
			</div>
			{/if}
		</div>
	</div>
	{if $overviewImage.image}
	<div id="taxonImage">
		<img src="{$taxon_base_url_images_overview}{$overviewImage.image}" />
		<div class="imageGradient"></div>
	</div>
	{else}
		{include file="../shared/flexslider.tpl"}
	{/if}
</div>
<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">
		<div class="whiteBox">
			<h1 class="main-display-name desktop">{$names.preffered_name} <span>{$names.nomen}</span></h1>
			<!-- h2 class="sideMenuTitle">&nbsp;</h2 -->
			
			{include file="_tabs.tpl"}

		</div>

	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function() {
	$('h2.sideMenuTitle').html($('#left #categories .active').html());
	$( 'title' ).html( $('<p>' + $('.main-display-name').html() + '</p>').text() + ' - ' + $( 'title' ).html() );
	
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto( { 
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function() { prettyPhotoCycle(); }
	 	} );
	}
	
	$('img[class=intern]').each(function() { $(this).remove(); } )

	{if $taxon.NsrId!=''}
	$('#name-header').on( 'click' , function(event) { 
	
		if ($('#nsr-id-row').html()==undefined)
		{
			if (event.altKey!==true) return;
			$('#names-table').append('<tr id="nsr-id-row"><td>NSR ID</td><td>{$taxon.NsrId}</td></tr>');
		}
		else
		{
			$('#nsr-id-row').toggle();
		}
	});
	{/if}
	
	{if $pp_popup}
	$.prettyPhoto.open('{$taxon_base_url_images_main}{$pp_popup[0]}','','<div style="margin-left:125px;">{$pp_popup[1]}</div>');
	{/if}
	
} );
</script>


{include file="../shared/footer.tpl"}