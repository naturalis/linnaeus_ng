<script type="text/javascript">
$(document).ready(function()
{
	var urls=Array();
	
	{foreach item=v from="\n"|explode:$external_content->full_url}
    urls.push('{$v|@trim|@escape}');
    {/foreach} 
	
	$(urls).each(function(index, value)
	{
		$('#urls').append('<li>'+value+'</li>');
	});
	
	//console.dir(urls);

});
</script>


<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
    
    <ul id=urls>
    </ul>
</p>


