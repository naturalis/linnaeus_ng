<h1>{$headerTitles.title}</h1>
<h2>{$headerTitles.subtitle}</h2> 




{foreach from=$content.media item=v}{if $v.overview_image==1}
<div class="illustratie-wrapper">
    <div class="illustratie">
        <a id="lightbox905" href="{$session.app.project.urls.uploadedMedia}{$v.file_name}" title="{$v.description}">
            <img style="width:280px" title="{$v.description}" src="{$session.app.project.urls.uploadedMedia}{$v.file_name}" alt="">
        </a>
    </div>
</div>
{/if}{/foreach}

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
        	<a rel="prettyPhoto[gallery]" href="{$session.app.project.urls.uploadedMedia}{$v.file_name}" title="{$v.description}">
            	<img style="width:130px" title="{$v.description}" src="{$session.app.project.urls.uploadedMedia}{$v.file_name}" alt="">
           	</a>
		</li>
	{/if}{/foreach}
	</ul>

	<div class="clearer"></div>

</div>

<a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhLzIwYWIwZjllLTNlYTUtNDNiOS04MzA1LWVmODEwM2Q4MDJiZg==/group" class="grouplink group-container">Vogels</a>   

<div class="related">
    <span style="font-weight:bold;padding-left:40px;font-size:14px;position:relative;top:10px;">Lijkt op</span>
    <ul>
        <li>
        <a href="/index.php/show/aHR0cDovL3d3dy5ybmFwcm9qZWN0Lm9yZy9kYXRhL2FkNWUyN2FiLTE1ZmItNDQ3Ni04MmQ3LTZjYzBhNGM3NDgxYQ==" class="resultlink">
            <img src="http://images.ncbnaturalis.nl/80x80/194873.jpg">
            Gierzwaluw                     
        </a>
        </li>
    </ul>
    <div class="clearer"></div>
</div>

</div>
