<div {if $search.display=='plain'} style="display:none;"{/if}>
	<form method="get" action="" id="formSearchFacetsSpecies" class="filterPictures" name="formSearchFacetsSpecies">
		<input type="hidden" id="name_id" name="name_id" value="{$search.name_id}">
		<input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">
		<fieldset class="block">

			<div class="formrow">
				<label accesskey="g" for="name">
					{t}Soortnaam{/t}
                    <i class="ion-chevron-down down"></i>
                    <i class="ion-chevron-up up"></i>
				</label>
				<div class="filter">
					<div class="input">
						<input type="text" class="field" value="{$search.name}" id="name" name="name" autocomplete="off">
					</div>
				</div>
				<div id="name_suggestion" class="suggestion" match="like" class="auto_complete" style="display: none;"></div>
			</div>

			<div class="formrow">
				<label accesskey="g" for="group">
					{t}Soortgroep{/t}
                    <i class="ion-chevron-down down"></i>
                    <i class="ion-chevron-up up"></i>
				</label>
				<div class="filter">
					<div class="input">
						<input type="text" class="field" value="{$search.group}" id="group" name="group" autocomplete="off">
					</div>
				</div>
				<div id="group_suggestion" class="suggestion" match="like" class="auto_complete" style="display:none;"></div>
			</div>
            
			<div class="formrow">
				<label accesskey="g" for="photographer">
					{t}Fotograaf{/t}
                    <i class="ion-chevron-down down"></i>
                    <i class="ion-chevron-up up"></i>
				</label>
				<div class="filter">
					<div class="input">
						<input type="text" class="field" value="{$search.photographer}" id="photographer" name="photographer" autocomplete="off">	
					</div>
					<a href="nsr_photographers.php" class="completeList">{t}Bekijk volledige lijst{/t}</a>
				</div>
				<div id="photographer_suggestion" class="suggestion" match="like" class="auto_complete" style="display:none;"></div>
			</div>

			<div class="formrow">
				<label accesskey="g" for="validator">
					{t}Validator{/t}
                    <i class="ion-chevron-down down"></i>
                    <i class="ion-chevron-up up"></i>
				</label>
				<div class="filter">
					<div class="input">
						<input type="text" class="field" value="{$search.validator}" id="validator" name="validator" autocomplete="off">
					</div>
					<a href="nsr_validators.php" class="completeList">{t}Bekijk volledige lijst{/t}</a>
				</div>
				<div id="validator_suggestion" class="suggestion" match="like" class="auto_complete" style="display: none;"></div>
			</div>

		</fieldset>
	</form>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
    $('.filterPictures .filter').each(function()
	{
		$(this).toggle( ( $(this).find( 'input[type=text]' ).val().length>0 ) );
	});
});
</script>
