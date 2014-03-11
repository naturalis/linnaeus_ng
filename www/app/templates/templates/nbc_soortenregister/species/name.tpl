{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
        <div>
            <h2>&nbsp;</h2>
        </div>
	</div>

	<div id="content">
			<h2>Naam: {$name.name}</h2>
			<table>
				<tr><td>Is {$name.nametype} voor</td><td colspan="2">soort: <a href="nsr_taxon.php?id={$taxon.id}">{$taxon.taxon}</a></td></tr>
				{if $name.reference_label}<tr><td>Referentie</td><td colspan="2"><a href="../literature2/reference.php?id={$name.reference_id}">{$name.reference_label}</a></td></tr>{/if}
				{if $name.expert_name}<tr><td>Expert</td><td colspan="2">{$name.expert_name}</td></tr>{/if}
				{if $name.organisation_name}<tr><td>Organisatie</td><td colspan="2">{$name.organisation_name}</td></tr>{/if}
			</table>
		</div>


	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}