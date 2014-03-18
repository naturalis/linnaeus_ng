{include file="../shared/header.tpl"}
<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="simple-search">

		<div>

		<form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">

		<input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">
		<input type="hidden" id="author_id" name="author_id" value="{$search.author_id}">

			<h2 class="search">Zoeken naar soorten</h2>
			<fieldset class="block">
				<div class="formrow">
					<label accesskey="g" for="search">Soortgroep</label>
					<input type="text" size="60" class="field" id="group" name="group" autocomplete="off" value="{$search.group}">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="author">Auteur</label>
					<input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
					<div id="author_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
			</fieldset>


			<fieldset>
				<div class="formrow">
					<label>
						<strong>Status voorkomen</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/i000335.html" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
					</label>
					<p>
						<a id="togglePresenceStatusGevestigd" href="#">Gevestigde soorten</a> / 
						<a id="togglePresenceStatusNietGevestigd" href="#">Niet gevestigde soorten</a>
					</p>
					<ul id="presenceStatusList">
					{foreach from=$presence_statuses item=v}
						<li>
							<input type="checkbox" class="list" id="established{$v.id}" name="presence[{$v.id}]" established="{$v.established}" {if $search.presence[$v.id]=='on'} checked="checked"{/if}>
							<label for="presence{$v.id}">
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
								<input disabled="disabled" type="checkbox" class="list" id="distribution" name="distribution"{if $search.distribution=='on'} checked="checked"{/if}>
								<label for="distribution">met verspreidingskaart</label>
							</li>
							<li>
								<input disabled="disabled" type="checkbox" class="list" id="trend" name="trend"{if $search.trend=='on'} checked="checked"{/if}>
								<label for="trend">met trendgrafiek</label>
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
								<input type="checkbox" class="list" id="dna" name="dna" {if $search.dna=='on'} checked="checked"{/if}>
								<label for="dna">met exemplaren verzameld</label>
							</li>
							<li>
								<input type="checkbox" class="list" id="dna_insuff" name="dna_insuff" {if $search.dna_insuff=='on'} checked="checked"{/if}>
								<label for="dna_insuff">minder dan drie exemplaren verzameld</label>
							</li>
						</ul>
					</div>
				</div>
		
				<div class="formrow">
					<label accesskey="g" for="">Resultaten sorteren op</label>
					<select name="sort">
						<option value="name-valid">Wetenschappelijk naam</option>
						<option value="name-pref-nl">Nederlandse naam</option>
					</select>
				</div>

				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
		</form>
		</div>
		
		<div id="results">
			<p>
				<h4><span id="resultcount-header">{$results.count}</span></h4>
			</p>
			{foreach from=$results.data item=v}
				<div class="result">
					{if $v.overview_image}
					<img src="http://images.ncbnaturalis.nl/120x75/{$v.overview_image}" />
					{/if}
					<strong><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></strong><br />
					{if $v.dutch_name}{$v.dutch_name}<br />{/if}
					Status voorkomen: {$v.presence_information_index_label} {$v.presence_information_title}
				</div>
			{/foreach}
		</div>

		{assign var=pgnResultCount value=$results.count}
		{assign var=pgnResultsPerPage value=$results.perpage}
		{assign var=pgnCurrPage value=$search.page}
		{assign var=pgnURL value=$smarty.server.PHP_SELF}
		{assign var=pgnQuerystring value=$querystring}
		{include file="../shared/_paginator.tpl"}
				
	</div>

	{include file="../shared/_right_column.tpl"}

</div>


	
	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	
$('#togglePresenceStatusGevestigd').bind('click',function() {
	$('input:checkbox[established]').each(function() {
		$(this).prop('checked', ($(this).attr('established')=='1'));
	});
	$('#formSearchFacetsSpecies').submit();
})

$('#togglePresenceStatusNietGevestigd').bind('click',function() {
	$('input:checkbox[established]').each(function() {
		$(this).prop('checked', ($(this).attr('established')=='0'));
	});
	$('#formSearchFacetsSpecies').submit();
})


bindKeys();

	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}