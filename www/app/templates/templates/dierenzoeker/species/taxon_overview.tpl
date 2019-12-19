<style>
div.pp_default .pp_description {
    font-weight:normal;
}
</style>

{if $back}
<a class="no-text terug-naar-het-dier" href="#" onclick="drnzkr_toon_dier( { id:{$back} } );return false;">Terug naar het dier</a>
{/if}

<h1>{$taxon.commonname}</h1>
<h2>{$taxon.taxon}</h2> 


{foreach $categories.categories v}
{if $v.page=='Appearance'}{assign catUiterlijk $v.id}{/if}
{if $v.page=='Behaviour'}{assign catGedrag $v.id}{/if}
{if $v.page=='Where and when'}{assign catWaar $v.id}{/if}
{if $v.page=='DescriptionTitle'}{assign catDescriptionTitle $v.id}{/if}
{if $v.page=='Description'}{assign catDescription $v.id}{/if}

{if $v.page=='_howMany'}{assign catHowMany $v.id}{/if}
{if $v.page=='_whatAre'}{assign catWhatAre $v.id}{/if}
{if $v.page=='_whatDo'}{assign catWhatDo $v.id}{/if}
{if $v.page=='_whereAre'}{assign catWhereAre $v.id}{/if}
{/foreach}

{if $taxon.base_rank_id >= $smarty.const.SPECIES_RANK_ID}

<script>
	$("#dier-header").html('Dier');
</script>

{foreach $media v}
{if $v.overview_image==1}
    {assign overview_image $v}
{/if}
{/foreach}


{function tidy_string}
    {$data|regex_replace:"/(^<p>|<\/p>$)/":""}
{/function}

{function make_thumb}
    {assign var=foo value="/"|explode:$data} 
    {assign var=foo value=$foo[count($foo)-1]} 
    {$projectUrls.uploadedMedia}{$foo|@replace:'.jpg':'_thumb.jpg'}
{/function}

{if $overview_image}

{capture caption}
{if $overview_image.meta_data.beeldbankOmschrijving.meta_data}<b>{$overview_image.meta_data.beeldbankOmschrijving.meta_data}</b><br />{/if}
{if $overview_image.meta_data.beeldbankFotograaf.meta_data}Gemaakt door: {$overview_image.meta_data.beeldbankFotograaf.meta_data}<br />{/if}
{if $overview_image.meta_data.beeldbankLicentie.meta_data}Licentie: {$overview_image.meta_data.beeldbankLicentie.meta_data}{/if}
{/capture}

<div class="illustratie-wrapper">
    <div class="illustratie">
       	<a data-fancybox="gallery" data-caption="{$smarty.capture.caption}" href="{$base_url_images_main}{$overview_image.file_name}" title="{$v.meta_data.beeldbankOmschrijving.meta_data}" id="overview-picture">
            <img style="width:280px" title="{$overview_image.description}" src="{$base_url_images_main}{$overview_image.file_name}" alt="">
		</a>
    </div>
</div>
{/if}

<p>
    <span class="label">Uiterlijk</span>
    {tidy_string data=$content[$catUiterlijk].content}
</p>

<p>
    <span class="label">Gedrag</span>
    {tidy_string data=$content[$catGedrag].content}
</p>

<p>
    <span class="label">Waar en wanneer</span>
    {tidy_string data=$content[$catWaar].content}
</p>
<p>
    <span class="label">{tidy_string data=$content[$catDescriptionTitle].content}</span>
    {tidy_string data=$content[$catDescription].content}
</p>
{else}
<script>
	$("#dier-header").html('Diergroep');
</script>

<p>
    <span class="label">{'Wat zijn %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {tidy_string data=$content[$catWhatAre].content}
</p>
<p>
    <span class="label">{'Waar zitten %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {tidy_string data=$content[$catWhereAre].content}
</p>
<p>
    <span class="label">{'Wat doen %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {tidy_string data=$content[$catWhatDo].content}
</p>
<p>
    <span class="label">{'Hoeveel %s zijn er in Nederland?'|replace:'%s':($taxon.commonname|lower)}</span>
    {tidy_string data=$content[$catHowMany].content}
</p>

{/if}

<div>

<div class="fotos">

	<div class="clearer"></div>

</div>

<style type="text/css">
.nsr-image-link {
    display: block;
    text-decoration: none;
    background-color: black;
    background-image: url(../../media/system/skins/dierenzoeker/diergroep-ext-link.png);
    background-repeat: no-repeat;
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    width: 348px;
    height: 39px;
    padding-left: 37px;
    padding-top: 7px;
}    
</style>


{if $parent.commonname && $parent.id && $parent.hasContent}
<a class="grouplink group-container" href="#" onclick="drnzkr_toon_dier( { id: {$parent.id}, back: {$taxon.id} } );return false;" style="">{$parent.commonname}</a>
{/if}

<a class="group-header-no-text nsr-image-link" style="display:none" href="" target="_new">
    Nederlands Soortenregister<br />
    <span style="font-size: 18px;display: block;padding-top:1px;">Bekijk foto's</span>
</a>

{if $related}
<div class="related">
    <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Lijkt op</span>
    <ul>
    {foreach $related v}
        <li class="">
            <a href="#" onclick="drnzkr_toon_dier( { id: {$v.relation_id},type:'{if $v.ref_type=='variation'}v{else}t{/if}' } );return false;" class="resultlink">
            <img src="{make_thumb data=$v.url_image}">
            {$v.label}                    
            </a>
        </li>
    {/foreach}
    </ul>
    <div class="clearer"></div>
</div>    
{/if}

{if $children}
<div class="related">
        <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Dieren in deze groep</span>
        <ul>
        {foreach $children v}
            <li class="">
                <a href="#" onclick="drnzkr_toon_dier( { id: {$v.id},type:'{if $v.ref_type=='variation'}v{else}t{/if}' } );return false;" class="resultlink">
                <img src="{make_thumb data=$v.url_image}">
                {$v.commonname}
                </a>
            </li>
		{/foreach}
        </ul>
        <div class="clearer"></div>
</div>    
{/if}

<script type="text/JavaScript">
$(document).ready(function()
{
    $.ajax({
        url : "/linnaeus_ng/external.php",
        type: 'GET',
        cache: false,
        data: {
            set: 'dierenzoeker_nsr_image_link',
            taxon_id: {$taxon.id},
        },
        success: function (data)
        {
            if (data.length>0)
            {
                $('.nsr-image-link')
                    .attr('href', 'http://www.nederlandsesoorten.nl/linnaeus_ng/rewrite.php?p=1&u=nsr/concept/'+data+'/images')
                    .toggle(true);
            }
        }
    })

    $('[data-fancybox]').fancybox({
        arrows : false,
        infobar : true,
        animationEffect : false
    });
});
</script>
