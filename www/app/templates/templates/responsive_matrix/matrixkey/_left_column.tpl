    <div id="left">
        
        <div id="quicksearch">
            <form onsubmit="setSearch();return false;">
                <button id="inlineformsearchButton" type="submit" class="zoekknop">
                  <i class="ion-search"></i>
                </button>
                <input id="inlineformsearchInput" type="text" name="searchString" class="searchString" title="{t}Zoek op naam{/t}" value="" placeholder="{t}Zoek op naam{/t}" />
            </form>
        </div>

        <div id="facets">            
            <h2>{t}Filter op kenmerken{/t}</h2>
            <div class="scrollable" id="scrollableFilter">
                <div class="forceScrollbar">
                    <div id="facet-categories-menu">
                        <ul>
                        </ul>
                    </div> 
                    
                    {if $settings->enable_treat_unknowns_as_matches}

                    <div style="margin:5px 0 5px 0">
                        <input onchange="setState( { action:'set_unknowns', value: $('#inc_unknowns').is(':checked') ? 1 : 0 } )" type="checkbox" id="inc_unknowns" />
                        <label for="inc_unknowns">{t}Behandel onbekenden als match{/t}</label>
                    </div>

                    <div class="left-divider"></div>

                    {/if}
                    
                    <div class="facetCategories clearSelectionBtn{if $activeChars|@count==0} ghosted{/if}">
                        <a id="clearSelectionLink" href="#" onclick="resetMatrix();return false;">
                            {t}Kenmerken wissen{/t}<i class="ion-refresh"></i>
                        </a>
                    </div>
                    
                    {if $master_matrix_id}
                    <div class="facetCategories" style="margin-bottom:6px">
                        <a href="?mtrx={$master_matrix_id}">{t}Terug naar de hoofdsleutel{/t}</a>
                    </div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="left-divider"></div>

        <div id="legendContainer">
            <h2><span id="legendHeader">{t}Legenda{/t}</span></h2>
            <ul class="legend">
                <li><img class="legend-icon-image icon-nsr" alt="{t}Informatie{/t}" src="{$image_root_skin}information_grijs.png" />{t}Informatie{/t}</li>
                <li><img class="legend-icon-image icon-info" alt="{t}Kenmerken{/t}" src="{$image_root_skin}lijst_grijs.png" />{t}Kenmerken{/t}</li>
                <li><img class="legend-icon-image icon-similar" alt="{t}Gelijkende soorten{/t}" src="{$image_root_skin}gelijk_grijs.png" />{t}Gelijkende soorten{/t}</li>
                {if $settings->url_observation_page}
                <li><img class="legend-icon-image icon-waarneming" alt="{t}Waarneming invoeren{/t}" src="{$image_root_skin}waarneming_grijs.png" />{t}Waarneming invoeren{/t}</li>
                {/if}
            </ul>
        </div>  

        {if $introduction_links[$settings->introduction_topic_inline_info]}
           
            <div id="dataSourceContainer"> 
               <script>
     			$(document).ready(function()
                {   
                    $.get('../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_inline_info].page_id}&format=plain')
                    	.done(function(data) {
							$('#dataSourceContainer').html( data ); 
                    		$('.footerLogos').html( data ); 
                    	})
                	});
                </script>
            </div>
        {/if}

      <!--   {if $introduction_links[$settings->introduction_topic_versions]}
            
            <div class="facetCategories colofon">
                <a href="#" onclick="
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_versions].page_id}&format=plain' )
                .done(function(data) { colofonOverlay( data ,'Versiegeschiedenis'); } ) ;
                ">{t}Versiegeschiedenis{/t}</a>
            </div>
        {/if} -->

        <div class="left-divider"></div>

    </div>

