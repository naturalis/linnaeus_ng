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

{foreach from=$content item=v key=k}{if $categoryList[$k]}
<p>
{if $categoryList[$k]!='Description'}
    <span class="label">{$categoryList[$k]}:</span>
{/if}
    {$v}
</p>
{/if}{/foreach}

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
{if $freePageContent}
<a href="#" onclick="openDiergroep({$freePageContent.page_id},{$taxon.id},'{$type}');return false;" class="grouplink group-container">{$freePageContent.topic|@ucfirst}</a>   
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

