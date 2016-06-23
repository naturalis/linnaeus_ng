{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $names.preffered_name}
					<h1 class="main-display-name">{$names.preffered_name}</h1>
					<h2>{$names.nomen}</h2>
				{elseif $names.nomen}
					<h1 class="no-subtitle main-display-name">{$names.nomen}</h1>
					<h2></h2>
				{else}
					<h1 class="no-subtitle main-display-name">{$names.scientific_name}</h1>
					<h2></h2>
				{/if}
			</div>

			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="{$taxon_base_url_images_overview}{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}{t}Foto{/t}</span> {$overviewImage.label} 
				</div>
			</div>
			{/if}
		</div>

		{include file="_tabs.tpl"}

	</div>

	{include file="../shared/_right_column.tpl"}

</div>


<script type="text/JavaScript">
$(document).ready(function()
{
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

	if (typeof acquireInlineTemplates == 'function') acquireInlineTemplates();
	
} );
</script>


{include file="../shared/footer.tpl"}