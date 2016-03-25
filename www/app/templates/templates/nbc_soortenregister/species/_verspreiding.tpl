<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
            
	<iframe width="500" height="300" scrolling="no" frameborder="no" src="{$external_content->full_url}"></iframe>

</p>