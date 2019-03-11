{include file="../shared/header.tpl"}

<body> 
    <div class="main-wrapper">
        
        <div class="header">
            <div class="header-inner">
            
	            {include file="../shared/top-banners.tpl"}

                <div class="clearer"></div>
                
                <ul class="wat-weet-je-list">      
                    <li class="wat-weet-je-arrow no-text">Wat weet je van het dier?</li>

                    {foreach $facetmenu group groupkey}
                    
                        {assign var=foo value="|"|explode:$group.label} 
                                           
                        <li class="facetgroup-btn" title="{$foo[1]}">
                            <div class="facet-btn ui-block-d">
                                <a data-facetgrouppageid="facetgrouppage{$groupkey}" href="#" data-role="button" data-corners="false" data-shadow="false">
                                    <div class="grid-iconbox" >
                                        <img src="{$projectUrls.projectMedia}__menu{$foo[0]|ucwords|regex_replace:"/\W/":""}.png" class="grid-icon" alt="" />
                                    </div>
									{assign var=blah value="/"|explode:$foo[0]}
										<div class="grid-labelbox ">{$blah[0]}{if $blah[1]}/<br>{$blah[1]}{/if}</div>
                                </a>
                            </div>
                        </li>
                                
                    {/foreach}

                </ul>                
            </div>
        </div>

		<div class="sub-header-wrapper" style="display:none" >
            <div class="sub-header">
                <div class="sub-header-inner">
                    <a href="#" class="no-text alles-wissen" onClick="resetMatrix();return false;">Alles wissen</a>
                    <div class="dit-weet-je-arrow no-text">
                            Dit weet je van het dier:
                    </div>
                    <ul class="dit-weet-je-list" id="gemaakte-keuzes" >
                    </ul>
                    <div class="clearer"></div>
                </div>            
            </div>            
        </div>
        

        <div class="content">

            <div class="result-list-wrapper">
                <div class="result-list-header">
                    <ul>
                        <li id="prev-button-container-top" style="visibility:hidden">
							<a href="#" class="first-btn" onClick="drnzkr_navigeren('eerste');return false;"></a>
							<a href="#" class="prev-btn" onClick="drnzkr_navigeren('vorige');return false;"></a>
						</li>
                        <li class="num-found" style="margin-top:-2px;"><span class="num" id="result-count-container">0</span> dieren gevonden</li>
                        <li id="next-button-container-top" style="position:relative;left:84px;visibility:visible;">
							<a href="#" class="next-btn last-child" onClick="drnzkr_navigeren('volgende');return false;"></a>
							<a href="#" class="last-btn last-child" onClick="drnzkr_navigeren('laatste');return false;"></a>
						</li>
                    </ul>
                    <div class="clearer"></div>
                </div>
                <div class="result-list-body" id='results-container' prev-id="result-list-container">
                     <div class="result-list-body-loading-container">
                         <div class="loader-img"></div>                             
                     </div>
                </div>
                <div class="result-list-footer">
                     <ul>
                        <li id="prev-button-container-bottom" style="visibility:hidden">
							<a href="#" class="first-btn" onClick="drnzkr_navigeren('eerste');return false;" style="margin-left:5px"></a>
							<a href="#" class="prev-btn" onClick="drnzkr_navigeren('vorige');return false;" style="margin-left:1px"></a>
						</li>
                        <li style="width:184px;">&nbsp;</li>
                        <li id="next-button-container-bottom" style="position:relative;left:84px;visibility:visible;">
							<a href="#" class="next-btn last-child" onClick="drnzkr_navigeren('volgende');return false;"></a>
							<a href="#" class="last-btn last-child" onClick="drnzkr_navigeren('laatste');return false;"></a>
						</li>
                    </ul>
                </div>
            </div>
            
            <div class="dier-wrapper" id="dier-content-wrapper" style="visibility:hidden">
                <div id="dier-header" class="dier-header">
                    Dier
                </div><div class="dier-content" id="dier-content">
                   <div style='height:650px;'>
                   </div>                        
                </div>
            </div>                
            
            <div class="clearer"></div>
                                      
        </div>
        
        {include file="../shared/bottom-banners.tpl"}

    </div>

	<div class="facetgrouppage-wrapper"> 
           
    {foreach $facetmenu group groupkey}

        <div class="facetgrouppage" id="facetgrouppage{$groupkey}">
			<img class="facetpage-puntje" alt="" src="{$session.app.system.urls.systemMedia}facet-puntje.png">
			<a class="no-text facetgrouppage-close-btn" href="#">Sluiten</a>

            {if $group.chars}
            {foreach $group.chars character characterkey}

			<div class="facetgrouppage-inner">
				<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$character.info}</h4>
				<div class="facetgrouppage-icons">
					<div class="helper-div">
						<div class="ui-grid-c">

							{foreach $states state statekey}
                            {if $state.characteristic_id==$character.id && $state.file_name}

							<div class="facet-btn ui-block-{if $statekey+1%4==0}d{elseif $statekey+1%3==0}c{elseif $statekey+1%2==0}b{else}a{/if}">
								<a 
                                	href="#" 
                                    data-value='{$character.prefix}:{$character.id}:{$state.id}'
                                    onclick="charClick(this)"
                                    id="state-{$state.id}">
                                    <div class="grid-iconbox">
                                        <img alt="" class="grid-icon" src="{$state.file_name}">
                                    </div>
                                    <div class="grid-labelbox ">
                                        {$state.label}
                                    </div>
								</a>
							</div>

							{/if}
							{/foreach}

						</div>
					</div>
					<div class="clearer"></div>
					<div class="facetgrouppage-bottom-shade"></div>

				</div>

			</div>
            
            {/foreach}

			{else}

			{assign var=foo value="|"|explode:$group.label} 

			<div class="facetgrouppage-inner">
				<h4 class="tagline left-tagline ie-rounded keuze-tagline">{$foo[1]}</h4>
				<div class="facetgrouppage-icons">
					<div class="helper-div">
						<div class="ui-grid-c">
                        {foreach $states state statekey}
                        {if $state.characteristic_id==$group.id && $state.file_name}
                        <div class="facet-btn ui-block-{if $statekey+1%4==0}d{elseif $statekey+1%3==0}c{elseif $statekey+1%2==0}b{else}a{/if}">
                            <a
                            	href="#" 
                                data-value='{$group.prefix}:{$group.id}:{$state.id}'
                                onclick="charClick(this)"
                                id="state-{$state.id}">
                            <div class="grid-iconbox">
                                <img alt="" class="grid-icon" src="{$state.file_name}">
                            </div>
                            <div class="grid-labelbox ">
                                {$state.label}
                            </div>
                            </a>
                        </div>
                        {/if}
                        {/foreach}

					</div>
					</div>
					<div class="clearer"></div>
					<div class="facetgrouppage-bottom-shade"></div>

				</div>  
			</div>
            
         	{/if}
            
		</div>    
	{/foreach}

	</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	{if $requestData.dier}
	drnzkr_startDier='{$requestData.dier|@escape}';
	{/if}

	setSetting({
		matrixId: {$matrix.id},
		projectId: {$session.app.project.id},
		imageRootSkin: '{$image_root_skin}',
		imageRootProject: '{$projectUrls.projectMedia}',
		useEmergingCharacters: {$settings->use_emerging_characters},
		suppressImageEnlarge: {if $settings->suppress_image_enlarge}{$settings->suppress_image_enlarge}{else}0{/if},
		defaultSpeciesImages: { portrait: '{$image_root_skin}noimage.gif', landscape: '{$image_root_skin}noimage-lndscp.gif' } ,
		imageOrientation: '{$settings->image_orientation}',
		browseStyle: '{$settings->browse_style}',
		scoreThreshold: {$settings->score_threshold},
		alwaysShowDetails: {$settings->always_show_details},
		perPage: {$settings->items_per_page},
		perLine: {$settings->items_per_line},
		generalSpeciesInfoUrl: '{$settings->species_info_url}',
		initialSortColumn: '{$settings->initial_sort_column}',
		alwaysSortByInitial: {$settings->always_sort_by_initial},
		similarSpeciesShowDistinctDetailsOnly: {if $settings->similar_species_show_distinct_details_only}{$settings->similar_species_show_distinct_details_only}{else}0{/if},
	});

	setScores($.parseJSON('{$session_scores|@addslashes}'));
	setStates($.parseJSON('{$session_states|@addslashes}'));
	setStateCount($.parseJSON('{$session_statecount|@addslashes}'));
	setCharacters($.parseJSON('{$session_characters|@addslashes}'));
	setDataSet($.parseJSON('{$full_dataset|@addslashes}'));
	 
	data.characterStates=$.parseJSON('{$states|@json_encode|@addslashes}');
			
	matrixInit();

	$('[data-facetgrouppageid^="facetgrouppage"]').click(function(e)
	{
		e.preventDefault();
		drnzkr_update_states();
		var currentstate=$("#"+$(this).attr('data-facetgrouppageid')).css("display");
		// Close all facet group pages (cleanup):
		$(".facetgrouppage").css("display", "none");
		// Show facet group page:
		$("#"+$(this).attr('data-facetgrouppageid')).css("display", currentstate=="none"?"block":"none");
		return false;           
	});
	
	$('.facetgrouppage-close-btn').click(function(e){
		e.preventDefault();
		// Hide all facet group pages:
		$(".facetgrouppage").css("display", "none");
		return false;           
	});

	$.backstretch("../../media/system/skins/dierenzoeker/background.jpg");

    drnzkr_result_style_update();

});
</script>

{include file="_inline_templates.tpl"}
{include file="_analytics_trackers.tpl"}

</body>