{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $names.preffered_name}
					<h1>{$names.preffered_name}</h1>
					<h2>{$names.nomen}</h2>
				{else}
					<h1 class="no-subtitle">{$names.nomen}</h1>
					<h2></h2>
				{/if}
			</div>
			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="http://images.naturalis.nl/510x272/{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}Foto</span> {$overviewImage.label} 
				</div>
			</div>
			{/if}
		</div>

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

		{else}

			{if $content|@is_array}
			<ul>
				{foreach from=$content item=v key=k}
				{if $k>0}<li><a href="nsr_taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
				{/foreach}
			</ul>
			{else}
			<p>
				{$content}
			</p>
			{/if}

		{/if}

		{if $rdf}

			{include file="_rdf_data.tpl"}

		{/if}

	</div>

	{include file="../shared/_right_column.tpl"}

</div>


<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('{if $names.preffered_name}{$names.preffered_name|@strip_tags|@escape}{else}{$names.nomen|@strip_tags|@escape}{/if} - '+$('title').html());
	
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
	$.prettyPhoto.open('http://images.naturalis.nl/comping/{$pp_popup[0]}','','<div style="margin-left:125px;">{$pp_popup[1]}</div>');
	{/if}
	
} );
</script>


{include file="../shared/footer.tpl"}