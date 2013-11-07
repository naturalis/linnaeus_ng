{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content">
	

		<div>

		<form method="post" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
			<h2 class="search">Zoeken naar soorten</h2>
			<fieldset class="block">
				<div class="formrow">
					<label accesskey="g" for="higherTaxon">Soortgroep</label>
					<input type="text" size="60" class="field" value="" id="higherTaxon" name="higherTaxon" autocomplete="off">
					<div id="higherTaxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="authorName">Auteur</label>
					<input type="text" size="60" class="field" value="" id="authorName" name="authorName" autocomplete="off">
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
					
						{foreach from=$presences item=v key=k}
							<li>
								<input type="checkbox" class="list" value="0" id="presenceStatus{$v.id}" name="presenceStatus">
								<label for="presenceStatus0">
									<div class="presenceStatusCode">{$k}</div>
									<div class="presenceStatusDescription">{$v.label}</div>
								</label>
							</li>
						{/foreach}
					</ul>
					
					
					<!-- ul id="presenceStatusList">
						<li>
							<input type="checkbox" class="list" value="0" id="presenceStatus0" name="presenceStatus">
							<label for="presenceStatus0">
								<div class="presenceStatusCode">0</div>
								<div class="presenceStatusDescription">Gemeld. Nog niet beoordeeld.</div>
							</label>
						</li>
						<li>
							<input type="checkbox" class="list" value="0a" id="presenceStatus0a" name="presenceStatus">
							<label for="presenceStatus0a">
								<div class="presenceStatusCode">0a</div>
								<div class="presenceStatusDescription">In Nederland. Precieze status nog niet bepaald.</div>
							</label>
						</li>
						<li>
							<input type="checkbox" class="list" value="1" id="presenceStatus1" name="presenceStatus">
							<label for="presenceStatus1">
								<div class="presenceStatusCode">1</div>
								<div class="presenceStatusDescription">Oorspronkelijk. Precieze status nog niet bepaald.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="1a" id="presenceStatus1a" name="presenceStatus">
							<label for="presenceStatus1a">
								<div class="presenceStatusCode">1a</div>
								<div class="presenceStatusDescription">Oorspronkelijk. Minimaal 10 jaar achtereen voortplanting.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="1b" id="presenceStatus1b" name="presenceStatus">
							<label for="presenceStatus1b">
								<div class="presenceStatusCode">1b</div>
								<div class="presenceStatusDescription">Incidenteel/Periodiek. Minder dan 10 jaar achtereen voortplanting en toevallige gasten.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="2" id="presenceStatus2" name="presenceStatus">
							<label for="presenceStatus2">
								<div class="presenceStatusCode">2</div>
								<div class="presenceStatusDescription">Exoot. Precieze status nog niet bepaald.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="2a" id="presenceStatus2a" name="presenceStatus">
							<label for="presenceStatus2a">
								<div class="presenceStatusCode">2a</div>
								<div class="presenceStatusDescription">Exoot. Minimaal 100 jaar zelfstandige handhaving.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="2b" id="presenceStatus2b" name="presenceStatus">
							<label for="presenceStatus2b">
								<div class="presenceStatusCode">2b</div>
								<div class="presenceStatusDescription">Exoot. Tussen 10 en 100 jaar zelfstandige handhaving.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="2c" id="presenceStatus2c" name="presenceStatus">
							<label for="presenceStatus2c">
								<div class="presenceStatusCode">2c</div>
								<div class="presenceStatusDescription">Exoot. Minder dan 10 jaar zelfstandige handhaving.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="2d" id="presenceStatus2d" name="presenceStatus">
							<label for="presenceStatus2d">
								<div class="presenceStatusCode">2d</div>
								<div class="presenceStatusDescription">Exoot. Incidentele import, geen voortplanting.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="3a" id="presenceStatus3a" name="presenceStatus">
							<label for="presenceStatus3a">
								<div class="presenceStatusCode">3a</div>
								<div class="presenceStatusDescription">Gemeld. Onvoldoende gegevens voor beoordeling.</div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="3b" id="presenceStatus3b" name="presenceStatus">
							<label for="presenceStatus3b">
								<div class="presenceStatusCode">3b</div>
								<div class="presenceStatusDescription">Onterecht gemeld. </div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="3c" id="presenceStatus3c" name="presenceStatus">
							<label for="presenceStatus3c">
								<div class="presenceStatusCode">3c</div>
								<div class="presenceStatusDescription">Verwacht. </div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="3d" id="presenceStatus3d" name="presenceStatus">
							<label for="presenceStatus3d">
								<div class="presenceStatusCode">3d</div>
								<div class="presenceStatusDescription">Onterecht gebruikte naam (auct.). </div>
							</label>
							</li>
							<li>
							<input type="checkbox" class="list" value="4" id="presenceStatus4" name="presenceStatus">
							<label for="presenceStatus4">
								<div class="presenceStatusCode">4</div>
								<div class="presenceStatusDescription">Overig. </div>
							</label>
						</li>
					</ul -->
				</div>
				
				<div class="formrow">
					<div style="width: 250px; float: left;">
						<label accesskey="g" for="">
							<strong>Alleen soorten met</strong>
						</label>
						<ul id="speciesOptionList">
							<li>
								<input type="checkbox" class="list" value="true" id="images" name="images">
								<label for="images">met foto('s)</label>
							</li>
							<li>
								<input type="checkbox" class="list" value="true" id="externalDistribution" name="externalDistribution">
								<label for="externalDistribution">met verspreidingskaart</label>
							</li>
							<li>
								<input type="checkbox" class="list" value="true" id="externalTrendChart" name="externalTrendChart">
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
								<input type="checkbox" class="list" value="completed" id="dnaBarcodingComplete" name="barcodeSpecimen">
								<label for="images">met exemplaren verzameld</label>
							</li>
							<li>
								<input type="checkbox" class="list" value="incomplete" id="dnaBarcodingIncomplete" name="barcodeSpecimen">
								<label for="externalDistribution">nog te verzamelen</label>
							</li>
						</ul>
					</div>
				</div>
		
				<div class="formrow">
					<label accesskey="g" for="">Resultaten sorteren op</label>
					<select name="sort">
						<option selected="selected" value="validName asc">Wetenschappelijk naam</option>
						<option value="preferredNameNl asc">Nederlandse naam</option>
					</select>
				</div>
				<div class="lineBreak">&nbsp;</div>
				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
		</form>
		</div>
		

	

	</div>

	{include file="../shared/_right_column.tpl"}

</div>


	
	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	
	$('#presence').remove();
	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}