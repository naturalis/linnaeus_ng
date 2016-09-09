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
			<h1 class="main-display-name desktop">{$names.preffered_name} <span class="cursive">{$names.nomen}</span></h1>
			<h2 class="sideMenuTitle">&nbsp;</h2>
			
			{if $activeCategory==$smarty.const.TAB_BEELD_EN_GELUID || $activeCategory==$smarty.const.CTAB_MEDIA}
				{include file="_tab_media.tpl"}
			{elseif $activeCategory==$smarty.const.CTAB_DNA_BARCODES}
				{include file="_tab_dna_barcodes.tpl"}
			{elseif $activeCategory==$smarty.const.TAB_VERSPREIDING}
				{include file="_tab_verspreiding.tpl"}
			{elseif $activeCategory==$smarty.const.CTAB_NAMES || $activeCategory==$smarty.const.TAB_NAAMGEVING}
				{include file="_tab_naamgeving.tpl"}
			{elseif $activeCategory==$smarty.const.TAB_BEDREIGING_EN_BESCHERMING}
				{include file="_tab_bedreiging.tpl"}
			{elseif $ext_template}
				{include file=$ext_template}
			{else}
				{if $content|@is_array}
				<ul>
					{foreach from=$content item=v key=k}
					{if $k>0}<li><a href="nsr_taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
					{/foreach}
				</ul>
				{else}
					{$content}
				{/if}
			{/if}

			{if $rdf}
				{include file="_rdf_data.tpl"}
			{/if}
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