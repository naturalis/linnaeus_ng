{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<div id="page-container-div">

<div id="page-main">

<style>
.regular_image, .overview_image {
	margin-bottom:15px;
	border:1px solid #999;
}
.overview_image {
	border-color:red;
}
</style>

<h2><span style="font-size:12px;font-style:normal">afbeeldingen ({$images.data|@count}):</span> {$concept.taxon}</h2>

<p>
	<table style="border-collapse:collapse">
	    <tr>
        {foreach from=$images.data item=v key=k}

			{capture "metadata"}
                {foreach from=$v.meta item=m}
                {$m.sys_label|@replace:'beeldbank':''}:
                {if $m.meta_data}{$m.meta_data}{elseif $m.meta_date}{$m.meta_date}{elseif $m.meta_number}{$m.meta_number}{/if}
                <br />
                {/foreach}
            {/capture}

            <td style="width:160px">
            	<div class="{if $v.overview_image==1}overview_image{else}regular_image{/if}">
                    <a class="zoomimage"
                        rel="prettyPhoto[gallery]"
                        href="{$taxon_main_image_base_url}{$v.image}"
                        pTitle="<div style='margin-left:125px;'>{$smarty.capture.metadata|@escape}</div>"
                    >
                        <img style="max-width:160px;height:auto;" alt="Foto {$v.photographer}" title="Foto {$v.photographer}{if $v.overview_image==1}; banner-afbeelding{/if}" src="{$taxon_thumb_image_base_url}{$v.thumb}" />
                    </a>



                    <div style="font-size:10px;padding:0 2px 1px 2px">
                        {$v.label}<br />
                        Geplaatst: {$v.meta_datum_plaatsing}<br />
                        <a href="#" onclick="disconnectimage( { id:{$concept.id},image:{$v.id} } );return false;" class="edit" style="margin:0">afbeelding ontkoppelen</a> |
                        <a href="image_data.php?id={$v.id}" class="edit" style="margin:0">edit data</a>
					</div>
                </div>
            </td>
            {if ($k+1)%5==0}</tr><tr>{/if}
        {/foreach}
        </tr>
	</table>
</p>

<p>
	<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat=media&epi={$session.admin.project.id}" class="edit"  style="margin:0" target="nsr">afbeeldingen bekijken in het Soortenregister (nieuw venster)</a><br />

</p>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

<script type="text/JavaScript">
$(document).ready(function() {

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

} );
</script>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}