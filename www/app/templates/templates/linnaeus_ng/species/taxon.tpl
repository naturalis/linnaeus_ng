{include file="../shared/header.tpl"}

<div id="dialogRidge">

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
		</div>

        <div id="categories">
        
        <ul>
            {foreach $categories v k}
                {if !$v.is_empty}
                <li id="ctb-{$v.id}">
                    <a {if $v.is_empty==0}href="../{if $taxon.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$taxon.id}&cat={$v.id}"{/if}
                    {if $activeCategory==$v.id}
                    class="category-active"
                    {/if}                
                    >{$v.title}</a>
                </li>
                {if $activeCategory==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
                {/if}
            {/foreach}
        </ul>
        
        </div>
        
        <div style="margin-left:160px;padding-left:25px;">

        {if $overviewImage.image && !($activeCategory==$smarty.const.TAB_BEELD_EN_GELUID || $activeCategory==$smarty.const.CTAB_MEDIA)}
        <div id="taxonImage" style="float:right">
            <img src="{$projectUrls['projectMedia']}{$overviewImage.image}" />
            <div id="taxonImageCredits">
                <span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}{t}Foto{/t}</span> {$overviewImage.label} 
            </div>
        </div>
        {/if}


		{if $activeCategory==$smarty.const.TAB_BEELD_EN_GELUID || $activeCategory==$smarty.const.CTAB_MEDIA}
        

			{include file="_tab_media.tpl"}
			
		{elseif $activeCategory==$smarty.const.CTAB_NAMES || $activeCategory==$smarty.const.TAB_NAAMGEVING}
					
			{include file="_tab_naamgeving.tpl"}

		{elseif $activeCategory==$smarty.const.CTAB_LITERATURE}
		
			{include file="_tab_literatuur.tpl"}

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

	</div>

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
	
} );
</script>


{include file="../shared/footer.tpl"}