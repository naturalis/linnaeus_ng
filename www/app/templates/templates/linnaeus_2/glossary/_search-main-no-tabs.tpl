<div id="search-main-no-tabs">
    {include file="../shared/_search-box.tpl"}
    <p id="header-titles-small">
    	<span id="mini-header-title">{$term.term}</span>
    	{if $term.synonyms}
        <span id="synonyms">
({foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach})
        </span>
        {/if}
    </p>
 </div>