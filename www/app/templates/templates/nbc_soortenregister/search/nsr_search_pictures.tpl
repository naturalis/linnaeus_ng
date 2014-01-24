{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="toolboxContainer">
            <h2>Toolbox</h2>
		</div>  

		<div id="treebranchContainer">

			<div class="top5">
				<h2>Top 5 fotografen</h2>
				<h4>Fotograaf (foto’s/soorten)</h4>
				<ul>
				{foreach from=$photographers item=v}
					{assign var=photograhper_name value=", "|explode:$v.meta_data} 
					<li>
						<a href="nsr_search_pictures.php?photographer={$v.meta_data}">{$photograhper_name[1]} {$photograhper_name[0]} ({$v.total} / {$v.taxon_count})</a>
					</li>
				{/foreach}
				</ul>
				<p>
					<a href=""><i>Bekijk volledige lijst</i></a>
				</p>
			</div>
			
			<br />

			<div class="top5">
				<h2>Top 5 validatoren</h2>
				<h4>Validator (foto’s/soorten)</h4>
				<i>nog niet weten te exporteren uit de beeldbankdump</i>
			</div>

		</div>  

	</div>

	<div id="content">

		<div>

		<form method="post" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
			<h2 class="search">Zoeken naar soorten</h2>
			<fieldset class="block" style="width:450px">
				<div class="formrow">
					<label accesskey="g" for="taxon">Soortnaam</label>
					<input type="text" class="field" value="{$search.taxon}" id="taxon" name="taxon" autocomplete="off">
					<div id="taxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="higherTaxon">Soortgroep</label>
					<input type="text" size="60" class="field" value="{$search.higherTaxon}" id="higherTaxon" name="higherTaxon" autocomplete="off">
					<div id="higherTaxon_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="photographer">Fotograaf</label>
					<input type="text" size="60" class="field" value="{$search.photographer}" id="photographer" name="photographer" autocomplete="off">
					<div id="photographer_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="validator">Validator</label>
					<input disabled="disabled" type="text" size="60" class="field" value="nog niet weten te exporteren uit de beeldbankdump" "{$search.validator}" id="validator" name="validator" autocomplete="off">
					<div id="validator_auto_complete" class="auto_complete" style="display: none;"></div>
				</div>
			</fieldset>

			<fieldset>
				<div class="formrow">
					<label for="" accesskey="g">Resultaten sorteren op:</label>
					<select name="sort">
						<!-- option value="dateModified desc" selected="selected">Datum plaatsing</option -->
						<option value="validName"{if $search.sort=='validName'} selected="selected"{/if}>Wetenschappelijk naam</option>
						<option value="photographer"{if $search.sort=='photographer'} selected="selected"{/if}>Fotograaf</option>
					</select>
				</div>

				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
		</form>
		</div>
		
		<div>
			<h4>{$results|@count} resultaten</h4>
			<div>
				{foreach from=$results item=v}
					{assign var=photograhper_name value=", "|explode:$v.photographer_name} 
					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="{$v.file_name}" pTitle="foto {$photograhper_name[1]} {$photograhper_name[0]}">
								<img class="speciesimage" alt="Foto {$photograhper_name[1]} {$photograhper_name[0]}" title="Foto {$photograhper_name[1]} {$photograhper_name[0]}" src="{$v.thumb_name}" />
							</a>
						</div>
						<h3><i>{$v.taxon}</i></h3>
						<dl>
							<dt>Foto</dt><dd>{$photograhper_name[1]} {$photograhper_name[0]}</dd>
							<dt>Geplaatst op</dt><dd>...</dd>
						</dl>
						<div style="clear: both;">
							<a href="../species/taxon.php?id={$v.taxon_id}">Naar deze soort</a>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}
</script>
{/literal}

{include file="../shared/footer.tpl"}