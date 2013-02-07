	<div id="left">

        <div id="quicksearch">
            <h2>Zoek op naam</h2>
            <form id="inlineformsearch" name="inlineformsearch" action="" method="post" onsubmit="return nbcDoSearch();">
                <label for="searchString" accesskey="t"></label>
                <input id="inlineformsearchInput" type="text" name="searchString" class="searchString" title="Zoek op naam" value="" />
                <input id="inlineformsearchButton" type="submit" value="zoek" class="zoekknop" />
            </form>
            <div id="suggestList"></div>
        </div>

        <!-- ul id="conceptSubMenu">
            <li class="disabled">
                <span>Samenvatting</span>
            </li>
            <li class="enabled active">
                <a href="http://www.nederlandsesoorten.nl/nsr/concept/0AHCYFBRPJDD/taxonomy">Naamgeving</a>
            </li>
            <li class="enabled">
                <a href="http://www.nederlandsesoorten.nl/nsr/concept/0AHCYFBRPJDD/imagesAndSounds">Beeld en geluid</a>
            </li>
            <li class="enabled">
                <a href="http://www.nederlandsesoorten.nl/nsr/concept/0AHCYFBRPJDD/imagesAndSounds">Beeld en geluid</a>
            </li>
        </ul -->

		<div id="facets">
            <h2>Zoek op kenmerken</h2>
            <span id="facet-categories-menu">
                <ul>
                {foreach from=$groups item=v}
                    {assign var=openGroup value=false}
                    <li id="character-item-{$v.id}" class="closed"><a href="#" onclick="nbcToggleGroup({$v.id});return false;">{$v.label}</a>
                        <ul id="character-group-{$v.id}" class="hidden">
                            {foreach from=$v.chars item=c}
                            {assign var=foo value="|"|explode:$c.label}
                            {if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
                            <li><a class="facetLink" href="#" onclick="nbcShowStates({$c.id});return false;">{$cLabel}{if $c.value} {$c.value}{/if}</a>
                                {if $activeChars[$c.id]}{assign var=openGroup value=true}
                                <span>
                                    {foreach from=$storedStates item=s key=cK}
                                    {if $s.characteristic_id==$c.id}
                                    <div class="facetValueHolder">
                                        {if $s.value}{$s.value} {/if}{if $s.label}{$s.label} {/if}
                                        <a href="#" class="removeBtn" onclick="nbcClearStateValue('{$cK}');return false;"><img src="{$nbcImageRoot}clearSelection.gif"></a>
                                    </div>
                                    {/if}
                                    {/foreach}
                                </span>
                                {/if}
                            </li>
                            {/foreach}
                        </ul>
                    </li>
                    {if $openGroup}
                    <script>
                    nbcToggleGroup({$v.id});
                    </script>
                    {/if}
                {/foreach}	
            	</ul>
	        </span>	
			
            <div id="clearSelectionContainer" class="facetCategories clearSelectionBtn{if $activeChars|@count==0} ghosted{/if}">
                <a id="clearSelectionLink" href="#" onclick="nbcClearStateValue();return false;">{t}wis geselecteerde kenmerken{/t}</a></span>
            </div>

		</div>

        <div id="bannerRuler">
            <hr />
        </div>

		<div>   
            <p>
                <strong>Gebaseerd op</strong>
            </p>
            <p style="font-size:10px">
                Zeegers, Th. &amp; Th. Heijerman 2008.<br />
                De Nederlandse boktorren (Cerambycidae). (<a href="http://www.naturalis.nl/ET2" target="_blank">Meer info</a>)
            </p>
		</div>   
        
        <div id="bannerRuler">
            <hr />
        </div>
 
        <div class="banner">
			<img style="border:1px solid #eee;" border="" alt="" title="" src="{$session.app.system.urls.systemMedia}nbc-logo.png" />
        </div>

	</div>