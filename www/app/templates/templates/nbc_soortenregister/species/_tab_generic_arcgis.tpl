<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
            
    <iframe width="500" height="600" frameborder="1" scrolling="no" marginheight="0" marginwidth="0" src="{$external_content->full_url}">
    </iframe>


</p>