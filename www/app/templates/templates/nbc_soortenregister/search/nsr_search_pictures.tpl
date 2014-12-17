{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		{include file="_toolbox.tpl"}

		<div id="treebranchContainer">

			{include file="_photographers.tpl"}
			
			<br />

			{include file="_validators.tpl"}

		</div>  

	</div>

	<div id="content" class="image-search">

		<div>
		
			<h1 style="color:#FA7001;font-size:30px;font-weight:normal;margin-top:4px;border-bottom:1px solid #666666;margin-bottom:5px;">
            	{if $search.header}{$search.header}{else}Foto's zoeken{/if}
			</h1>
			
			<div{if $search.display=='plain'} style="display:none;"{/if}>
				<form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
			
					<input type="hidden" id="name_id" name="name_id" value="{$search.name_id}">
					<input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">
			
					<fieldset class="block">
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
							<input type="text" size="60" class="field" value="{$search.validator}" id="validator" name="validator" autocomplete="off">
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
		
						<div class="lineBreak" style="width:510px">&nbsp;</div>
		
						<input type="submit" class="zoekknop" value="zoek">
					</fieldset>
				</form>
				</div>
		</div>
		

		<div>
			<h4><span id="resultcount-header">{$results.count}</span></h4>
			<div>
				{foreach from=$results.data item=v}
					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
							
						{if $v.common_name}		
						<h3>{$v.common_name}</h3>
						<span class="wetenschappelijkenaam"><i>{$v.name}</i></span>
						{else}
						<h3 class="wetenschappelijkenaam"><i>{$v.name}</i></h3>
						{/if}
						<dl>
							<dt>Foto</dt><dd>{$v.photographer}</dd>
							<dt>Geplaatst op</dt><dd>{$v.meta_datum_plaatsing}</dd>
						</dl>
						<div style="clear: both;"><a href="../species/nsr_taxon.php?id={$v.taxon_id}">Naar deze soort</a></div>
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
	</div>

	{include file="../shared/_right_column.tpl"}
	
	<!-- div>
		tijdelijke link t.b.v. test:<br />
		<a href="nsr_recent_pictures.php">Recente afbeeldingen</a>
	</div -->

</div>

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('title').html('Foto\'s zoeken - '+$('title').html());

	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function(){ prettyPhotoCycle() }
	 	});
	}
	bindKeys();
});
</script>


{include file="../shared/footer.tpl"}