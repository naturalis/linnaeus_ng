	<div id="left">

        <div id="quicksearch">
            <h2>{t}Zoek op naam{/t}</h2>
            
            <form id="inlineformsearch" name="inlineformsearch" action="" method="post" onsubmit="nbcDoSearch();return false;">
                <label for="searchString" accesskey="t"></label>
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
                <a id="clearSelectionLink" href="#" onclick="nbcClearStateValue();return false;">
                	<img class="reloadBtnImg" src="{$nbcImageRoot}reload-icon.png" style="margin-bottom:-4px">{t}opnieuw beginnen{/t}
                </a>
            </div>
		</div>

        <div class="left-divider"></div>

		<div id="legendContainer">
        	{*<span id="legendHeader">{t}Legenda:{/t}</span><br />*}
            <table>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-nsr" src="{$nbcImageRoot}sr_icon_grijs.png" /></td><td>Nederlands Soortenregister</td></tr>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-info" src="{$nbcImageRoot}information_grijs.png" /></td><td>onderscheidende kenmerken</td></tr>
                <tr><td class="legend-icon-cell"><img class="legend-icon-image icon-similar" src="{$nbcImageRoot}gelijkend_grijs.png" /></td><td>gelijkende soorten</td></tr>
            </table>
		</div>  

        <div class="left-divider"></div>

		<div id="dataSourceContainer">   
		{if $nbcDataSource.author || $nbcDataSource.title || $nbcDataSource.url}
            <span id="sourceHeader">{t}Gebaseerd op:{/t}</span>
            <p>
            {$nbcDataSource.author}{if $nbcDataSource.author && $nbcDataSource.title}<br />{/if}
            {$nbcDataSource.title}
            {if $nbcDataSource.url}<a href="{$nbcDataSource.url}" target="_blank">{t}meer info{/t}</a>{/if}
            </p>
            <br />
        {/if}
		{if $nbcDataSource.photoCredit}
            <p>
            {$nbcDataSource.photoCredit}
            </p>
            <br />
        {/if}
            <p>
	            <span id="logo-ETI">Geïmplementeerd door ETI BioInformatics. Gebaseerd op Linnaeus NG&trade;.</span>
			</p>
		</div>  

        <div class="left-divider"></div>

	</div>
