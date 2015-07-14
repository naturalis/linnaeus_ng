	<div id="left">

        <div id="quicksearch">

            <h2>{t}Zoek op naam{/t}</h2>
            
            <form onsubmit="setSearch();return false;">
                <input id="inlineformsearchInput" type="text" name="searchString" class="searchString" title="{t}Zoek op naam{/t}" value="" />
                <input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" />
            </form>

            <!-- div id="suggestList"></div -->
        </div>

		<div id="facets">
            <h2>{t}Zoek op kenmerken{/t}</h2>
            <span id="facet-categories-menu">
                <ul>
                </ul>
	        </span>	
			
            <div id="clearSelectionContainer" class="facetCategories clearSelectionBtn{if $activeChars|@count==0} ghosted{/if}">
                <a id="clearSelectionLink" href="#" onclick="resetMatrix();return false;">
                	<img class="reloadBtnImg" src="{$image_root_skin}reload-icon.png" style="margin-bottom:-4px">{t}opnieuw beginnen{/t}
                </a>
            </div>
			{if $master_matrix_id}
            <div class="facetCategories" style="margin-bottom:6px">
				<a href="?mtrx={$master_matrix_id}">{t}Terug naar de hoofdsleutel{/t}</a>
            </div>
			{/if}
		</div>

        <div class="left-divider"></div>

		<div id="legendContainer">
        	{*<span id="legendHeader">{t}Legenda:{/t}</span><br />*}
            <table>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-nsr" src="{$image_root_skin}information_grijs.png" /></td><td>{t}meer informatie{/t}</td></tr>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-info" src="{$image_root_skin}lijst_grijs.png" /></td><td>{t}onderscheidende kenmerken{/t}</td></tr>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-similar" src="{$image_root_skin}gelijk_grijs.png" /></td><td>{t}gelijkende soorten{/t}</td></tr>
            </table>
		</div>  

        <div class="left-divider"></div>
        
        {capture snippet}{snippet}colofon.html{/snippet}{/capture}
        
        {if $smarty.capture.snippet|@strlen>0}
		<div id="dataSourceContainer">   
		{$smarty.capture.snippet}
		</div>  
        <div class="left-divider"></div>
        {/if}


	</div>
