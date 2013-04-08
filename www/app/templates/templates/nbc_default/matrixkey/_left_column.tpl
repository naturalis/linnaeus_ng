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
                {foreach from=$groups item=v}
                    {assign var=openGroup value=false}
                    <li id="character-item-{$v.id}" class="closed"><a href="#" onclick="nbcToggleGroup({$v.id});return false;">{$v.label|strtolower|ucfirst}</a></li>
                    <ul id="character-group-{$v.id}" class="hidden">
                        {foreach from=$v.chars item=c key=k}
                        {assign var=foo value="|"|explode:$c.label}
                        <li class="inner{if $k==$v.chars|@count-1} last{/if}"><a class="facetLink" href="#" onclick="nbcShowStates({$c.id});return false;">{$c.label|strtolower|ucfirst}{if $c.value} {$c.value}{/if}</a>
			            {* {if $coefficients[$c.id].rank}{$coefficients[$c.id].rank}{/if} // needs to be tested with actual data! *}
                            {if $activeChars[$c.id]}{assign var=openGroup value=true}
                            <span>
                                {foreach from=$storedStates item=s key=cK}
                                {if $s.characteristic_id==$c.id}
                                <div class="facetValueHolder">
                                    {if $s.value}{$s.value} {/if}{if $s.label}{$s.label} {/if}
                                    <a href="#" class="removeBtn" onclick="nbcClearStateValue('{$cK}');return false;"><img class="removeBtnImg" src="{$nbcImageRoot}clearSelection.gif"></a>
                                </div>
                                {/if}
                                {/foreach}
                            </span>
                            {/if}
                        </li>
                        {/foreach}
                    </ul>
                    {if $openGroup}
                    <script>
                    nbcToggleGroup({$v.id});
                    </script>
                    {/if}
                {/foreach}	
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

		<div id="legendContainer" class="hidden">   
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
		</div>  

        <div id="bannerRuler">
            <hr />
        </div>
 
        <div id="nbcLogoContainer">
			<span id="logoHeader">Een initiatief van:</span>
			<a href="http://www.naturalis.nl/" target="_blank"><img id="logo-NBC" src="{$session.app.system.urls.systemMedia}nbc-logo.png" title="Naturalis Biodiversity Center" /></a>
			<a href="http://www.eis-nederland.nl/" target="_blank"><img id="logo-EIS" src="{$session.app.system.urls.systemMedia}logo-eisDEF-CMYK-2.png" title="Stichting European Invertebrate Survey Nederland" /></a>
            <span id="logo-ETI">Geïmplementeerd door ETI BioInformatics. Gebaseerd op Linnaeus NG&trade;.</span>
        </div>


	</div>
