{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content">

		<div>

		<form method="post" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
			<h2 class="search">Zoeken naar soorten</h2>
			<fieldset class="block">
				<div class="formrow">
					<label accesskey="g" for="taxon">Soortnaam</label>
					<input type="text" size="60" class="field" id="taxon" name="taxon" autocomplete="off" value="{$search.taxon}" >
					<div id="taxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="higherTaxon">Soortgroep</label>
					<input type="text" size="60" class="field" id="higherTaxon" name="higherTaxon" autocomplete="off" value="{$search.higherTaxon}">
					<div id="higherTaxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="authorName">Auteur</label>
					<input type="text" size="60" class="field" id="authorName" name="authorName" autocomplete="off" value="{$search.authorName}">
					<div id="authorName_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
			</fieldset>

			<fieldset>
				<div class="formrow">
					<label>
						<strong>Status voorkomen</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/i000335.html" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
					</label>
					<p>
						<a id="togglePresenceStatusGevestigd" href="#gevestigd">Gevestigde soorten</a> / 
						<a id="togglePresenceStatusNietGevestigd" href="#nietgevestigd">Niet gevestigde soorten</a>
					</p>
					<ul id="presenceStatusList">
					{foreach from=$presence_statuses item=v}
						<li>
							<input type="checkbox" class="list" id="presenceStatus{$v.id}" name="presenceStatus[{$v.id}]" settled="{$v.settled_species}" {if $search.presenceStatus[$v.id]=='on'} checked="checked"{/if}>
							<label for="presenceStatus{$v.id}">
								<div class="presenceStatusCode">{$v.index_label}</div>
								<div class="presenceStatusDescription">{$v.information_short}</div>
							</label>
						</li>
					{/foreach}
					</ul>
				</div>
				
				<div class="formrow">
					<div style="width: 250px; float: left;">
						<label accesskey="g" for="">
							<strong>Alleen soorten</strong>
						</label>
						<ul id="speciesOptionList">
							<li>
								<input type="checkbox" class="list" id="images" name="images"{if $search.images=='on'} checked="checked"{/if}>
								<label for="images">met foto('s)</label>
							</li>

							<br /><i>nog niet geïmplementeerd, in afwachting externe koppeling:</i>

							<li>
								<input disabled="disabled" type="checkbox" class="list" id="externalDistribution" name="externalDistribution"{if $search.externalDistribution=='on'} checked="checked"{/if}>
								<label for="externalDistribution">met verspreidingskaart</label>
							</li>
							<li>
								<input disabled="disabled" type="checkbox" class="list" id="externalTrendChart" name="externalTrendChart"{if $search.externalTrendChart=='on'} checked="checked"{/if}>
								<label for="externalTrendChart">met trendgrafiek</label>
							</li>
						</ul>
					</div>
					<div style="width: 250px; float: left;">
						<label accesskey="d" for="">
							<strong>Soorten voor DNA barcoding</strong>&nbsp;
							<a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/dnabarcoding.html" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
						</label>
						<ul id="speciesOptionList">
							<li>
								<input type="checkbox" class="list" id="hasBarcodes" name="hasBarcodes" {if $search.hasBarcodes=='on'} checked="checked"{/if}>
								<label for="hasBarcodes">met exemplaren verzameld</label>
							</li>
							<br /><i>nog niet geïmplementeerd, onduidelijk wat precies relatie met optie hierboven:</i>
							<li>
								<input disabled type="checkbox" class="list" id="hasNoBarcodes" name="hasNoBarcodes" {if $search.hasNoBarcodes=='on'} checked="checked"{/if}>
								<label for="hasNoBarcodes">nog te verzamelen</label>
							</li>
						</ul>
					</div>
				</div>
		
				<div class="formrow">
					<label accesskey="g" for="">Resultaten sorteren op</label>
					<select name="sort">
						<option selected="selected" value="validName">Wetenschappelijk naam</option>
						<option value="preferredNameNl">Nederlandse naam</option>
					</select>
				</div>

				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
		</form>
		</div>
		
		<div>
			<h4>{$results|@count} resultaten</h4>
			{foreach from=$results item=v}
				<div style="vertical-align:top;width:500px;border-bottom:1px solid #999;padding-bottom:10px;margin-bottom:10px">
					<img src="{$v.overview_image}" style="width:140px;height:auto;float:right"/>
					
					{if $v.name!=$v.dutch_name && $v.name!=$v.taxon}
					<strong><a href="../species/taxon.php?id={$v.taxon_id}">{$v.name}</a></strong><br />
					<span style="color:#999">{$v.nametype}</span> {$v.taxon}
					{else}
					<strong><a href="../species/taxon.php?id={$v.taxon_id}">{$v.taxon}</a></strong>
					{/if}
					<br />

					{if $v.dutch_name}{$v.dutch_name}<br />{/if}
					
					Status voorkomen: {$v.presence_information_index_label} {$v.presence_information_title}
				</div>
			{/foreach}
		</div>
	</div>

	{include file="../shared/_right_column.tpl"}

</div>


	
	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	
$('#togglePresenceStatusGevestigd').bind('click',function() {
	$('input:checkbox[settled]').each(function() {
		$(this).prop('checked', ($(this).attr('settled')=='1'));
	});
	$('#formSearchFacetsSpecies').submit();
})

$('#togglePresenceStatusNietGevestigd').bind('click',function() {
	$('input:checkbox[settled]').each(function() {
		$(this).prop('checked', ($(this).attr('settled')=='0'));
	});
	$('#formSearchFacetsSpecies').submit();
})

/*
$('#hasNoBarcodes').bind('click',function() {
	$(this).prop('checked',true);
	$('#hasBarcodes').prop('checked',false);
})

$('#hasBarcodes').bind('click',function() {
	$(this).prop('checked',true);
	$('#hasNoBarcodes').prop('checked',false);
})
*/



	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}