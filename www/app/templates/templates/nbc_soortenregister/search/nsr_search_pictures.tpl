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

		<form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">

		<input type="hidden" id="name_id" name="name_id" value="{$search.name_id}">
		<input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">

			<h2 class="search">Zoeken naar afbeeldingen</h2>
			<fieldset class="block" style="width:450px">
				<div class="formrow">
					<label accesskey="g" for="name">Soortnaam</label>
					<input type="text" class="field" value="{$search.name}" id="name" name="name" autocomplete="off">
					<div id="name_suggestion" match="start" class="auto_complete" style="display: none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="group">Soortgroep</label>
					<input type="text" size="60" class="field" value="{$search.group}" id="group" name="group" autocomplete="off">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="photographer">Fotograaf</label>
					<input type="text" size="60" class="field" value="{$search.photographer}" id="photographer" name="photographer" autocomplete="off">
					<div id="photographer_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="validator">Validator</label>
					<input disabled="disabled" type="text" size="60" class="field" value="nog niet weten te exporteren uit de beeldbankdump" "{$search.validator}" id="validator" name="validator" autocomplete="off">
					<div id="validator_suggestion" match="start" class="auto_complete" style="display: none;"></div>
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
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.ncbnaturalis.nl/comping/{$v.file_name}" pTitle="foto {$photograhper_name[1]} {$photograhper_name[0]}">
								<img class="speciesimage" alt="Foto {$photograhper_name[1]} {$photograhper_name[0]}" title="Foto {$photograhper_name[1]} {$photograhper_name[0]}" src="http://images.ncbnaturalis.nl/160x100/{$v.thumb_name}" />
							</a>
						</div>
						
						<h3>{$v.dutch_name}</h3>
						<span class="wetenschappelijkenaam"><i>{$v.taxon}</i></span>
						<dl>
							<dt>Foto</dt><dd>{$v.photographer}</dd>
							<dt>Geplaatst op</dt><dd>{$v.meta_datum}</dd>
						</dl>
						<div style="clear: both;"><a href="../species/taxon.php?id={$v.taxon_id}">Naar deze soort</a></div>
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
	bindKeys();
});
</script>
{/literal}

{include file="../shared/footer.tpl"}