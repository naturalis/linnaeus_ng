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

{if $taxon.base_rank_id >= $smarty.const.SPECIES_RANK_ID}
<script>
	$("#dier-header").html('Dier');
</script>
{if $nbc.url_image}
<div class="illustratie-wrapper">
    <div class="illustratie">
       	<a rel="prettyPhoto[gallery]" href="{$nbc.url_image_large}" title="{$v.description}" id="overview-picture">
            <img style="width:280px" title="" src="{$nbc.url_image}" alt="">
		</a>
    </div>
</div>
{/if}
<p>
    <span class="label">Uiterlijk:</span>
    {$content[855].content}
</p>
<p>
    <span class="label">Gedrag:</span>
    {$content[856].content}
</p>
<p>
    <span class="label">Waar en wanneer:</span>
    {$content[857].content}
</p>
<p>
    <span class="label">{$content[858].content}</span>
    {$content[848].content}
</p>
{else}
<script>
	$("#dier-header").html('Diergroep');
</script>

<p>
    <span class="label">{'Wat zijn %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[860].content}
</p>
<p>
    <span class="label">{'Waar zitten %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[861].content}
</p>
<p>
    <span class="label">{'Wat doen %s?'|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[862].content}
</p>
<p>
    <span class="label">{'Hoeveel %s zijn er in Nederland?'|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[863].content}
</p>

{/if}

<div>

<div class="fotos">
	<ul>
	
	{foreach $media v}
    	<li>
        	<a data-fancybox="gallery" data-caption="{$v.description}" href="{$v.file_name}" title="{$v.description}" id="img-{$v.id}">
            	<img style="width:130px" title="{$v.description}" src="{$v.file_name|@replace:'w800':'160x100'}" alt="">
           	</a>
		</li>
	{/foreach}
	</ul>

	<div class="clearer"></div>

</div>
{if $parent.commonname && $parent.id && $parent.hasContent}
<a class="grouplink group-container" href="#" onclick="drnzkr_toon_dier( { id: {$parent.id}, back: {$taxon.id} } );return false;" style="">{$parent.commonname}</a>
{/if}
{if $related}
<div class="related">
        <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Lijkt op</span>
        <ul>
        {foreach $related v}
            <li class="">
                <a href="#" onclick="drnzkr_toon_dier( { id: {$v.relation_id},type:'{if $v.ref_type=='variation'}v{else}t{/if}' } );return false;" class="resultlink">
                <img src="{$v.url_thumbnail}">
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
                <img src="{$v.url_thumbnail}">
                {$v.label}
                </a>
            </li>
		{/foreach}
        </ul>
        <div class="clearer"></div>
</div>    
{/if}


<script type="text/JavaScript">
$(document).ready(function() {
function getremotemetadata(p)
{
	$.ajax({
		url : '../../static/dierenzoeker/getremotemetadata.php',
		type: 'GET',
		data : ({
			image_id :p.name
		}),
		success : function (data)
		{
			if (data)
			{
				var data=$.parseJSON(data);
				$('#'+p.id).attr('title',
					(data.description? '"'+data.description+'" ' : '')+(data.copyright ? '&copy; '+data.copyright : '') +
					(data.copyright && data.maker ? ' - ' : '') +(data.maker ? 'Maker: '+data.maker : '')
				);
			}
		}
	});	
}

{if $nbc.url_image}
	var url = '{$nbc.url_image}';
	getremotemetadata( { id: 'overview-picture' , name: url.substring(url.lastIndexOf('/')+1).replace('.jpg','') } );
{/if}
{foreach $media v}
	var url = '{$v.file_name}';
	getremotemetadata( { id: 'img-'+ {$v.id} , name: url.substring(url.lastIndexOf('/')+1).replace('.jpg','') } );
{/foreach}

});
</script>