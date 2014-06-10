{if $back}
<a class="no-text terug-naar-het-dier" href="#" onclick="toonDier( { id:{$back} } );return false;">Terug naar het dier</a>
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
        <!-- a id="lightbox905" href="{$nbc.url_image}" title="" -->
       	<a rel="prettyPhoto[gallery]" href="{$nbc.url_image_large}" title="{$v.description}">
            <img style="width:280px" title="" src="{$nbc.url_image}" alt="">
		</a>
        <!-- /a -->
    </div>
</div>
{/if}
<p>
    <span class="label">{$categoryList[855]}:</span>
    {$content[855].content}
</p>
<p>
    <span class="label">{$categoryList[856]}:</span>
    {$content[856].content}
</p>
<p>
    <span class="label">{$categoryList[857]}:</span>
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
    <span class="label">{$categoryList[860]|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[860].content}
</p>
<p>
    <span class="label">{$categoryList[861]|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[861].content}
</p>
<p>
    <span class="label">{$categoryList[862]|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[862].content}
</p>
<p>
    <span class="label">{$categoryList[863]|replace:'%s':($taxon.commonname|lower)}</span>
    {$content[863].content}
</p>

{/if}

<div>

<div class="fotos">
	<ul>
	{foreach from=$media item=v}
    	<li>
        	<a rel="prettyPhoto[gallery]" href="{$v.file_name}" title="{$v.description}">
            	<img style="width:130px" title="{$v.description}" src="{$v.file_name}" alt="">
           	</a>
		</li>
	{/foreach}
	</ul>

	<div class="clearer"></div>

</div>
{if $parent.commonname && $parent.id && $parent.hasContent}
<a class="grouplink group-container" href="#" onclick="toonDier( { id: {$parent.id}, back: {$taxon.id} } );return false;" style="">{$parent.commonname}</a>
{/if}
{if $related}
<div class="related">
        <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Lijkt op</span>
        <ul>
        {foreach from=$related item=v}
            <li class="">
                <a href="#" onclick="toonDier( { id: {$v.relation_id},type:'{if $v.ref_type=='variation'}v{else}t{/if}' } );return false;" class="resultlink">
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
        <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Soorten in deze groep</span>
        <ul>
        {foreach from=$children item=v}
            <li class="">
                <a href="#" onclick="toonDier( { id: {$v.id},type:'{if $v.ref_type=='variation'}v{else}t{/if}' } );return false;" class="resultlink">
                <img src="{$v.url_thumbnail}">
                {$v.label}                    
                </a>
            </li>
		{/foreach}
        </ul>
        <div class="clearer"></div>
</div>    
{/if}

