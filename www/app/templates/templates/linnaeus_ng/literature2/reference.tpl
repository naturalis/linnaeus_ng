{include file="../shared/header.tpl" title="Literature"}

<div id="dialogRidge">
	<div id="content" class="literature">
	    <div id="page-main">
	    	<div>
				{* if $ref.label}<div id="text">{$ref.label}</div>{/if *}
				<table>
					<tbody>
						{if $ref.publication_type}
							<tr>
								<td>{t}Type{/t}</td>
								<td>{$ref.publication_type}</td>
							</tr>
						{/if}
                        
                        
						{capture authors}
		                    {foreach from=$ref.authors item=v key=k}
		                        {$v.name}
		                        {if $ref.authors|@count>1 && $k<$ref.authors|@count-1}
		                        	{if $k==$ref.authors|@count-2} 
		                        		&
		                        	{else}
		                        		,
	                        		{/if}
	                    		{/if}
		                    {/foreach}
						{/capture}
						
                        {if $ref.author || ($smarty.capture.authors|@trim|@strlen)>0}
                        <tr>
							<td>{t}Auteur(s){/t}</td>
							<td>
								{if $ref.author}{$ref.author}{elseif ($smarty.capture.authors|@trim|@strlen)>0}{$smarty.capture.authors|@trim}{/if}
							</td>
						</tr>
                        {/if}
						{if $ref.date}
							<tr>
								<td>{t}Jaar{/t}</td>
								<td>{$ref.date}</td>
							</tr>
						{/if}
						{if $ref.label}
							<tr>
								<td>{t}Titel{/t}</td>
								<td>{$ref.label}</td>
							</tr>
						{/if}
						{if $ref.publishedin || $ref.publishedin_name}
							<tr>
								<td>{t}Gepubliceerd in{/t}</td>
								<td>{if $ref.publishedin_id}<a href="?id={$ref.publishedin_id}">{/if}
									{if $ref.publishedin}{$ref.publishedin}{else}{$ref.publishedin_name}{/if}
									{if $ref.publishedin_id}</a>{/if}
								</td>
							</tr>
						{/if}
						{if $ref.periodical || $ref.periodical_name}
							<tr>
								<td>{t}Periodiek{/t}</td>
								<td>{if $ref.periodical_id}<a href="?id={$ref.periodical_id}">{/if}
									{if $ref.periodical}{$ref.periodical}{else}{$ref.periodical_name}{/if}
									{if $ref.periodical_id}</a>{/if}
								</td>
							</tr>
						{/if}
						{if $ref.volume}
							<tr>
								<td>{t}Volume{/t}</td>
								<td>{$ref.volume}</td>
							</tr>
						{/if}
						{if $ref.pages}
							<tr>
								<td>{t}Pagina's{/t}</td>
								<td>{$ref.pages}</td>
							</tr>
						{/if}
						{if $ref.publisher}
							<tr>
								<td>{t}Uitgever{/t}</td>
								<td>{$ref.publisher}</td>
							</tr>
						{/if}
						{if $ref.external_link}
							<tr>
								<td>{t}Link{/t}</td>
								<td>
									<a href="{$ref.external_link}" target="_blank">{$ref.external_link}</a>
								</td>
							</tr>
						{/if}
					</tbody>
				</table>
	            {if $taxa}
		            <ul>
		            {foreach $taxa v k}
		            <li class="general-list"><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></li>
		            {/foreach}
		            </ul>
	            {/if}
			</div>
		</div>
	</div>
</div>
<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html('{$ref.label|@strip_tags|@escape} - '+$('title').html());

	$('#presence').remove();

	$('[id^=media-]').each(function(e){
		$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	});	
});
</script>
{include file="../shared/footer.tpl"}