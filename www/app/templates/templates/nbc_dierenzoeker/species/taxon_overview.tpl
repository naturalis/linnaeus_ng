<h1>{$headerTitles.title}</h1>
<h2>{$headerTitles.subtitle}</h2> 

{if $overviewImage}
<div class="illustratie-wrapper">
    <div class="illustratie">
        <a id="lightbox905" href="{$projectUrls.uploadedMedia}{$overviewImage.image}" title="">
            <img style="width:280px" title="" src="{$projectUrls.uploadedMedia}{$overviewImage.image}" alt="">
        </a>
    </div>
</div>
{/if}

{if $taxon.base_rank_id >= $smarty.const.SPECIES_RANK_ID}
<p>
    <span class="label">{$categoryList[855]}:</span>
    {$content[855]}
</p>
<p>
    <span class="label">{$categoryList[856]}:</span>
    {$content[856]}
</p>
<p>
    <span class="label">{$categoryList[857]}:</span>
    {$content[857]}
</p>
<p>
    <span class="label">{$content[858]}</span>
    {$content[848]}
</p>
{else}
<p>
    <span class="label">{$categoryList[860]|replace:'%s':$headerTitles.title|lower}</span>
    {$content[860]}
</p>
<p>
    <span class="label">{$categoryList[861]}:</span>
    {$content[861]}
</p>
<p>
    <span class="label">{$categoryList[862]}:</span>
    {$content[862]}
</p>
<p>
    <span class="label">{$categoryList[863]}:</span>
    {$content[863]}
</p>

{/if}

<div>

<div class="fotos">
	<ul>
	{foreach from=$content.media item=v}{if $v.overview_image!=1}
    	<li>
        	<a rel="prettyPhoto[gallery]" href="{$projectUrls.uploadedMedia}{$v.file_name}" title="{$v.description}">
            	<img style="width:130px" title="{$v.description}" src="{$projectUrls.uploadedMedia}{$v.file_name}" alt="">
           	</a>
		</li>
	{/if}{/foreach}
	</ul>

	<div class="clearer"></div>

</div>
{if $parent.commonname && $parent.id}
<a class="grouplink group-container" href="#" onclick="toonDier({$parent.id});return false;" style="">{$parent.commonname}</a>
{/if}
{if $related}
<div class="related">
        <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Lijkt op</span>
        <ul>
        {foreach from=$related item=v}
            <li class="">
                <a href="#" onclick="toonDier({$v.relation_id},'{if $v.ref_type=='variation'}v{else}t{/if}');return false;" class="resultlink">
                <img src="{$v.url_image}" style="padding-top:10px">
                {$v.label}                    
                </a>
            </li>
		{/foreach}
        </ul>
        <div class="clearer"></div>
</div>    
                    
{/if}

