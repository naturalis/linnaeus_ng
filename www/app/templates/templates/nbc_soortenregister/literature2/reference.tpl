{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
        <div>
            <h2>&nbsp;</h2>
        </div>
	</div>
	
	<div id="content" class="literature">

		<h2 style="margin-top: 17px; margin-bottom: 6px">{$ref.label}</h2>

			<table>
				{if $ref.publication_type}<tr><td>Type</td><td>{$ref.publication_type}</td></tr>{/if}

				{capture authors}
                    {foreach from=$ref.authors item=v key=k}
                        {$v.name}{if $ref.authors|@count>1 && $k<$ref.authors|@count-1}{if $k==$ref.authors|@count-2} &{else},{/if}{/if}
                    {/foreach}
				{/capture}

				<tr><td>Auteur(s)</td><td>{if ($smarty.capture.authors|@trim|@strlen)>0}{$smarty.capture.authors|@trim}{else}{$ref.author}{/if}</td></tr>



				{if $ref.date}<tr><td>Jaar</td><td>{$ref.date}</td></tr>{/if}
				{if $ref.label}<tr><td>Titel</td><td>{$ref.label}</td></tr>{/if}

				{if $ref.publishedin || $ref.publishedin_name}
				<tr>
					<td>Gepubliceerd in</td>
					<td>{if $ref.publishedin_id}<a href="?id={$ref.publishedin_id}">{/if}
						{if $ref.publishedin}{$ref.publishedin}{else}{$ref.publishedin_name}{/if}
						{if $ref.publishedin_id}</a>{/if}
					</td>
				</tr>
				{/if}

				{if $ref.periodical || $ref.periodical_name}
				<tr>
					<td>Periodiek</td>
					<td>{if $ref.periodical_id}<a href="?id={$ref.periodical_id}">{/if}
						{if $ref.periodical}{$ref.periodical}{else}{$ref.periodical_name}{/if}
						{if $ref.periodical_id}</a>{/if}
					</td>
				</tr>
				{/if}
				{if $ref.volume}<tr><td>Volume</td><td>{$ref.volume}</td></tr>{/if}
				{if $ref.pages}<tr><td>Pagina's</td><td>{$ref.pages}</td></tr>{/if}
				{if $ref.publisher}<tr><td>Uitgever</td><td>{$ref.publisher}</td></tr>{/if}
				{if $ref.external_link}<tr><td>Link</td><td><a href="{$ref.external_link}" target="_blank">{$ref.external_link}</a></td></tr>{/if}
				</tbody>
			</table>
		</div>
	</div>

	{include file="../shared/_right_column.tpl"}

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


{include file="../shared/footer.tpl"}

