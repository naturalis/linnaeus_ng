{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $names.preffered_name}
					<h1 class="main-display-name">{$names.preffered_name}</h1>
					<h2>{$names.nomen}</h2>
				{else}
					<h1 class="no-subtitle main-display-name">{$names.nomen}</h1>
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

		{if $activeCategory.tabname=='CTAB_TAXON_LIST'}

			{include file="_tab_taxon_list.tpl"}

		{elseif $activeCategory.tabname=='CTAB_CLASSIFICATION'}

			{include file="_tab_classificatie.tpl"}

		{elseif $activeCategory.tabname=='CTAB_DNA_BARCODES'}
        
			{include file="_tab_dna_barcodes.tpl"}

		{elseif $activeCategory.tabname=='CTAB_DICH_KEY_LINKS'}

			{include file="_tab_dich_key_links.tpl"}

		{elseif $activeCategory.tabname=='CTAB_LITERATURE'}
		
			{include file="_tab_literatuur.tpl"}

		{elseif $activeCategory.tabname=='CTAB_MEDIA'}

			{include file="_tab_media.tpl"}
			
		{elseif $activeCategory.tabname=='CTAB_NAMES'}
					
			{include file="_tab_naamgeving.tpl"}








		{elseif $activeCategory==$smarty.const.TAB_VERSPREIDING}

			{include file="_tab_verspreiding.tpl"}

		{elseif $activeCategory==$smarty.const.TAB_BEDREIGING_EN_BESCHERMING}
		
			{include file="_tab_bedreiging.tpl"}

		{elseif $ext_template}
		
			{include file=$ext_template}

		{elseif $external_content && $external_content->template}
        
			{include file=$external_content->template}

		{elseif $external_content}
        
			{include file='_webservice.tpl'}

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