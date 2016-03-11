<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
            
	<iframe style="width:510px;height:500px;" src="{$external_content->full_url}"></iframe>

</p>