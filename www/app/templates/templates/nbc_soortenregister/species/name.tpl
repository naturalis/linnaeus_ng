{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
        <div>
            <h2>&nbsp;</h2>
        </div>
	</div>

	<div id="content">
			<h2>{t}Naam{/t}: {$name.name}</h2>
			<table>
	            {if $name.addition[$currentLanguageId].addition}
                    <tr><td>Opmerking</td><td colspan="2">{$name.addition[$currentLanguageId].addition}</td></tr>
                {/if}
				<tr><td>{t}Is{/t} 
                
                {if !$name.language_has_preferredname && $name.alt_alt_nametype_label}
                {$name.alt_alt_nametype_label} 
                {else}
                {$name.nametype} 
                {/if}

                {t}voor{/t}</td><td colspan="2"><a href="nsr_taxon.php?id={$taxon.id}">{$taxon.taxon}</a></td></tr>
				{if $name.reference_label}<tr><td>{t}Referentie{/t}</td><td colspan="2"><a href="../literature2/reference.php?id={$name.reference_id}">{$name.reference_label}</a></td></tr>{/if}
				{if $name.expert_name}<tr><td>{t}Expert{/t}</td><td colspan="2">{$name.expert_name}</td></tr>{/if}
				{if $name.organisation_name}<tr><td>{t}Organisatie{/t}</td><td colspan="2">{$name.organisation_name}</td></tr>{/if}
			</table>
		</div>


	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('{$name.name|@strip_tags|@escape} - '+$('title').html());

});
</script>