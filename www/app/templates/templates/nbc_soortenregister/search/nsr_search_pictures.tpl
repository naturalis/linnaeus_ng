{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content">

		<div>

		<form method="post" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
			<h2 class="search">Zoeken naar soorten</h2>
			<fieldset class="block" style="width:450px">
				<div class="formrow">
					<label accesskey="g" for="taxon">Soortnaam</label>
					<input type="text" class="field" value="" id="taxon" name="taxon" autocomplete="off">
					<div id="taxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="higherTaxon">Soortgroep</label>
					<input type="text" size="60" class="field" value="" id="higherTaxon" name="higherTaxon" autocomplete="off">
					<div id="higherTaxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="photographer">Fotograaf</label>
					<input type="text" size="60" class="field" value="" id="photographer" name="photographer" autocomplete="off">
					<div id="photographer_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="validator">Validator</label>
					<input type="text" size="60" class="field" value="" id="validator" name="validator" autocomplete="off">
					<div id="validator_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
			</fieldset>

			<fieldset>
				<div class="formrow">
					<label for="" accesskey="g">Resultaten sorteren op:</label>
					<select name="sort">
						<option value="dateModified desc" selected="selected">Datum plaatsing</option>
						<option value="validName asc">Wetenschappelijk naam</option>
						<option value="photographer asc">Fotograaf</option>
					</select>
					</div><div class="lineBreak">&nbsp;</div>
				</div>
				<div class="lineBreak">&nbsp;</div>
				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
		</form>
		</div>
	

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}