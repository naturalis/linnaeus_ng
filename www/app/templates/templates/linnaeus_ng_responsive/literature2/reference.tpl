{include file="../shared/header.tpl"}

<div id="dialogRidge">	
	<div id="content" class="literature">
		<div class="whiteBox">
			<h2>{t}Literatuurreferentie{/t}</h2>

				<table class="literateTable">
					{if $ref.label}<tr><td>{t}Titel{/t}</td><td>{$ref.label}</td></tr>{/if}
					{if $ref.publication_type}<tr><td>{t}Type{/t}</td><td>{$ref.publication_type}</td></tr>{/if}

					{capture authors}
	                    {foreach from=$ref.authors item=v key=k}
	                        {$v.name}{if $ref.authors|@count>1 && $k<$ref.authors|@count-1}{if $k==$ref.authors|@count-2} &{else},{/if}{/if}
	                    {/foreach}
					{/capture}

					<tr><td>{t}Auteur(s){/t}</td><td>{if ($smarty.capture.authors|@trim|@strlen)>0}{$smarty.capture.authors|@trim}{else}{$ref.author}{/if}</td></tr>



					{if $ref.date}<tr><td>{t}Jaar{/t}</td><td>{$ref.date}</td></tr>{/if}

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
					{if $ref.volume}<tr><td>{t}Volume{/t}</td><td>{$ref.volume}</td></tr>{/if}
					{if $ref.pages}<tr><td>{t}Pagina's{/t}</td><td>{$ref.pages}</td></tr>{/if}
					{if $ref.publisher}<tr><td>{t}Uitgever{/t}</td><td>{$ref.publisher}</td></tr>{/if}
					{if $ref.external_link}<tr><td>{t}Link{/t}</td><td><a href="{$ref.external_link}" style="overflow:hidden" target="_blank">{t}ga naar brondocument{/t}{*$ref.external_link|replace:'.':'.<wbr>&#8203;'*}</a></td></tr>{/if}
					</tbody>
				</table>

                {if $taxa}
                <p>
                    <h2>{t}Als referentie opgenomen bij{/t}</h2>
                    <ul>
                    {foreach $taxa v k}
                    <li class="general-list"><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></li>
                    {/foreach}
                    </ul>
                </p>
                {/if}

			</div>
		</div>
	</div>
	{include file="../shared/footer.tpl"}
</div>


<script type="text/JavaScript">
$(document).ready(function(){

	$('title').html('{$ref.label|@strip_tags|@escape} - '+$('title').html());

	$('#presence').remove();

	$('[id^=media-]').each(function(e){
		$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	});
	
});
</script>




