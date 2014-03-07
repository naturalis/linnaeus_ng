{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
	
	<div id="content" class="literature">

		<h2 style="margin-top: 17px; margin-bottom: 6px">{$ref.label}</h2>

			<table id="refdata" class="panel" border="0" cellpadding="0" cellspacing="0">
				<tbody id="refdata_body">
				{if $ref.publication_type}<tr ><th>Type</th><td>{$ref.publication_type}</td></tr>{/if}

				{if $ref.actor_name}<tr><th>Auteur(s)</th><td>{$ref.actor_name}</td></tr>
				{elseif $ref.author}<tr><th>Auteur(s)</th><td>{$ref.author}</td></tr>{/if}

				{if $ref.date}<tr><th>Jaar</th><td>{$ref.date}</td></tr>{/if}
				{if $ref.label}<tr><th>Titel</th><td>{$ref.label}</td></tr>{/if}
				{if $ref.publishedin_actor_name}<tr><th>Uitgever</th><td>{$ref.publishedin_actor_name}</td></tr>{/if}

				{if $ref.volume}<tr><th>Volume</th><td>{$ref.volume}</td></tr>{/if}
				{if $ref.pages}<tr><th>Pagina's</th><td>{$ref.pages}</td></tr>{/if}
				{if $ref.presence_label}<tr><th>Status</th><td>{$ref.presence_label}</td></tr>{/if}
				{if $ref.external_link}<tr><th>Status</th><td>{$ref.external_link}</td></tr>{/if}
				</tbody>
			</table>
		</div>


	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	
	$('#presence').remove();

	$('[id^=media-]').each(function(e){
		$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	});
	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}