{include file="../shared/header.tpl"}
{if $overviewImage.image}
<div id="taxonHeader">
	<div id="headerImage">
		<div class="titles">

			<h1 class="main-display-name">{$names.preffered_name} <span>{$names.nomen}</span></h1>
			
			{if $overviewImage.photographer}
			<div id="taxonImageCredits">
				{t}Foto:{/t} {$overviewImage.photographer} 
			</div>
			{/if}
		</div>
	</div>
	<div id="taxonImage" style="background-image: url('{$taxon_base_url_images_overview}{$overviewImage.image}');">
		<div class="imageGradient"></div>
	</div>
</div>
{else}
	<div class="whiteBox no-header-image">
		<h1 class="main-display-name">{$names.preffered_name} <span>{$names.nomen}</span></h1>
	</div>
{/if}
<div id="dialogRidge">
	<!-- <div class="whiteBox responsive-title species-title">
		<h1 class="main-display-name mobile">{$names.preffered_name} <span>{$names.nomen}</span></h1>
	</div> -->
	{include file="_left_column.tpl"}
	<div id="content" class="taxon-detail">
		<!-- <div class="whiteBox desktop-title species-title">
			<h1>{$names.preffered_name} <span>{$names.nomen}</span></h1>
		</div> -->
		<div class="whiteBox">
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

	{if $pp_popup}
	$.prettyPhoto.open('{$taxon_base_url_images_main}{$pp_popup[0]}','','<div style="margin-left:125px;">{$pp_popup[1]}</div>');
	{/if}
	
} );
</script>


{include file="../shared/footer.tpl"}