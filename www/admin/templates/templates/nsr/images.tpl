{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">afbeeldingen ({$images.data|@count}):</span> {$concept.taxon}</h2>

<p>
	<table>
	    <tr>
        {foreach from=$images.data item=v key=k}

			{capture "metadata"}
            {foreach from=$v.meta item=m}
            {$m.sys_label|@replace:'beeldbank':''}:
            
            {if $m.meta_data}{$m.meta_data}{elseif $m.meta_date}{$m.meta_date}{elseif $m.meta_number}{$m.meta_number}{/if}
            
            <br />
            {/foreach}
            {/capture}

            <td style="padding-bottom:15px;width:170px">
                <a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$smarty.capture.metadata|@escape}</div>">
                    <img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
                </a><br />
                <span style="font-size:10px">
                {$v.label}<br />
                Geplaatst: {$v.meta_datum_plaatsing}<br />
                <a href="#" onclick="disconnectimage( { id:{$concept.id},image:{$v.id} } );return false;" class="edit" style="margin:0">afbeelding ontkoppelen</a><br />
                </span>
            </td>
            {if ($k+1)%5==0}</tr><tr>{/if}
        {/foreach}
        </tr>
	</table>
</p>

<p>
	<a href="javascript:alert('wordt toegevoegd als de beelduitwisselaar af is.');" class="edit"  style="margin:0">nieuwe afbeelding toevoegen</a><br />

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
{include file="../shared/admin-footer.tpl"}