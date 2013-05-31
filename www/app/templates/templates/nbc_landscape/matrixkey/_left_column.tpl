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
                <img class="removeBtnImg" src="{$nbcImageRoot}clearSelection.gif">{t}wis geselecteerde kenmerken{/t}</a>
            </div>

		</div>

        <div id="bannerRuler" class="hidden">
            <hr />
        </div>

		{*<div id="legendContainer" class="hidden">*}
		<div id="legendContainer">
        	<span id="legendHeader">{t}Betekenis iconen:{/t}</span><br />
            <table>
                <tr><td><img class="result-icon icon-info" src="{$nbcImageRoot}information_grijs.png" /></td><td id="legendDetails">onderscheidende kenmerken</td></tr>
                <tr><td><img class="result-icon icon-similar" src="{$nbcImageRoot}gelijkend_grijs.png" /></td><td id="legendSimilarSpecies">gelijkende soorten</td></tr>
                <tr><td><img class="result-icon icon-sr" src="{$nbcImageRoot}sr_icon_grijs.png" /></td><td id="legendExternalLink">Nederlands Soortenregister</td></tr>
			</table>
		</div>  


        <div id="bannerRuler">
            <hr />
        </div>

		<div id="dataSourceContainer">   
            <span id="sourceHeader">{t}Gebaseerd op:{/t}</span>
            <p>
            {$nbcDataSource.author}<br />
            {$nbcDataSource.title}
            <a href="{$nbcDataSource.url}" target="_blank">{t}meer info{/t}</a>
            </p>
            <br />
            <p>
            {$nbcDataSource.photoCredit}
            </p>
            <br />
            <p>
	            <span id="logo-ETI">Geïmplementeerd door ETI BioInformatics. Gebaseerd op Linnaeus NG&trade;.</span>
			</p>
		</div>  

        <div id="bannerRuler">
            <hr />
        </div>
 


	</div>
