{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
	
	<div id="content">
		<div id="taxonHeader" class="hasImage">
			<div id="titles">
				<h2>
				{$ref.label}
				</h2>
			</div>
		</div>
			<table>
				{if $ref.publication_type}<tr><td>Type</td><td>{$ref.publication_type}</td></tr>{/if}

				{if $ref.actor_name}<tr><td>Auteur(s)</td><td>{$ref.actor_name}</td></tr>
				{elseif $ref.author}<tr><td>Auteur(s)</td><td>{$ref.author}</td></tr>{/if}

				{if $ref.date}<tr><td>Jaar</td><td>{$ref.date}</td></tr>{/if}
				{if $ref.label}<tr><td>Titel</td><td>{$ref.label}</td></tr>{/if}
				{if $ref.publishedin_actor_name}<tr><td>Uitgever</td><td>{$ref.publishedin_actor_name}</td></tr>{/if}

				{if $ref.volume}<tr><td>Volume</td><td>{$ref.volume}</td></tr>{/if}
				{if $ref.pages}<tr><td>Pagina's</td><td>{$ref.pages}</td></tr>{/if}
				{if $ref.presence_label}<tr><td>Status</td><td>{$ref.presence_label}</td></tr>{/if}
				{if $ref.external_link}<tr><td>Status</td><td>{$ref.external_link}</td></tr>{/if}

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