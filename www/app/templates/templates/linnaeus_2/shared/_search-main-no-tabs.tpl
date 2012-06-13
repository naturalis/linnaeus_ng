<div id="search-main-no-tabs">
    {include file="../shared/_search-box.tpl"}
 	{if $headerTitles}
	    <p id="header-titles-small">
	        <span id="header-title">{$headerTitles.title}</span>{if $headerTitles.subtitle}<span id="header-subtitle">: {$headerTitles.subtitle}</span>{/if}
	    </p>
	{/if}
 </div>