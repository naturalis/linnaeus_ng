{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="content" class="taxon-detail">

    <p id="header-titles-small">
        <span id="header-title" class="set-as-title">
            {$taxon.label}
        </span>
    </p>

    <div id="categories">

        <ul>
            {foreach $categories v k}
            <li id="ctb-{$v.id}">
                <!-- a {if $v.is_empty==0}href="../species/nsr_taxon.php?id={$taxon.id}&cat={$v.id}"{/if} -->
 				<!-- a {if $v.is_empty==0}href="../{if $taxon.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$taxon.id}&cat={$v.tabname}"{/if} -->
 				<a {if $v.is_empty==0}href="../species/nsr_taxon.php?id={$taxon.id}&cat={$v.tabname}"{/if}
                {if $activeCategory.id==$v.id}
                class="category-active"
                {/if}
                >{$v.label}</a>
            </li>
            {if $activeCategory.id==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
            {/foreach}
        </ul>

    </div>
    
	<br style="clear:all" />

    <div style="margin-left:200px;">

        {if $is_nsr && $overviewImage && !($activeCategory.id==$smarty.const.TAB_BEELD_EN_GELUID || $activeCategory.id==$smarty.const.CTAB_MEDIA)}
            <div id="taxonImage" style="float:right">
                <img src="{$projectUrls['projectMedia']}{$overviewImage.image}" />
                <div id="taxonImageCredits">
                    <span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}{t}Foto{/t}</span> {$overviewImage.label}
                </div>
            </div>
        {elseif $overviewImage && $activeCategory.id.show_overview_image}
           <div id="overview-image" style="background: url('{$overviewImage}');"></div>
        {/if}

 		{if $activeCategory.tabname=='CTAB_MEDIA'}

			{if $is_nsr !== false}
				{include file="_tab_media_nsr.tpl"}
			{else}
				{include file="_tab_media.tpl"}
			{/if}

		{elseif $activeCategory.tabname=='CTAB_NAMES' || $activeCategory.tabname=='TAB_NAAMGEVING'}

			{include file="_tab_naamgeving.tpl"}

		{elseif $activeCategory.tabname=='CTAB_LITERATURE'}

			{include file="_tab_literatuur.tpl"}

		{elseif $activeCategory.tabname=='CTAB_CLASSIFICATION'}

			{include file="../species/_tab_classificatie.tpl"}

		{elseif $activeCategory.tabname=='CTAB_DICH_KEY_LINKS'}

			{include file="../species/_tab_dich_key_links.tpl"}

		{else}
        
	        <br style="clear:all" />

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

	$( 'title' ).html( $('<p>' + $('.set-as-title').html() + '</p>').text() + ' - ' + $( 'title' ).html() );

	{if $is_nsr}
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
	{/if}

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

<div class="inline-templates" id="lookupDialogItem">
	<p id="allLookupListCell-%COUNTER%" class="row%ROW-CLASS%" lookupId="%ID%" onclick="%ONCLICK%">
    	<span class="italics" style="cursor:pointer">%LABEL%<span class="allLookupListSource" style="%SOURCE-STYLE%"> (%SOURCE%)</span></span>
	</p>
</div>

{include file="../shared/footer.tpl"}