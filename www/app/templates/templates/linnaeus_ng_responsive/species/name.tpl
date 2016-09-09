{include file="../shared/header.tpl"}
<div id="taxonHeader">

	<div id="headerImage">
		<div class="titles">
			{if $names.preffered_name}
				<h1 class="main-display-name">{$names.preffered_name}</h1>
				<h2>{$names.nomen}</h2>
			{else}
				<h1 class="no-subtitle main-display-name">{$names.nomen}</h1>
				<h2></h2>
			{/if}
			<div id="taxonImageCredits">
				<span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}{t}Foto{/t}</span> {$overviewImage.label} 
			</div>
		</div>
	</div>
	{if $overviewImage.image}
	<div id="taxonImage">
		<img src="{$taxon_base_url_images_overview}{$overviewImage.image}" />
		<div class="imageGradient"></div>
	</div>
	{else}
		{include file="../shared/flexslider.tpl"}
	{/if}
</div>

<style>
table tr  td:first-child {
	white-space:nowrap;
	padding-right: 15px;
	line-height: 1.3;
}
</style>

<div id="dialogRidge">

	<div id="left">
    {include file="../search/_simpleSearch.tpl"}	
	</div>

	<div id="content">
		<div class="whiteBox">
			<h2>{t}Naam{/t}: {$name.name}</h2>
			<table>
	            {if $name.addition[$currentLanguageId].addition}
                    <tr><td>{t}Opmerking{/t}</td><td colspan="2">{$name.addition[$currentLanguageId].addition}</td></tr>
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
	</div>
</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('{$name.name|@strip_tags|@escape} - '+$('title').html());

});
</script>