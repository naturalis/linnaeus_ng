{include file="../shared/header.tpl"}
{include file="_left_column.tpl"}
            
    <div id="content" class="title-type4">
    
        <div id="resultsHeader">
            <span>
                <div id="result-count" class="headerSelectionLabel"></div>
                <div class="headerPagination">
                    <ul id="paging-header" class="list paging"></ul>
                </div>
            </span>
        </div>
        
        <div id="results">
            <div id="similarSpeciesHeader" class="hidden"></div>
            <div id="results-container"></div>
        </div>

        <div class="footerPagination">
            <ul id="paging-footer" class="list paging"></ul>
        </div>
        
    </div>
    


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
nbcImageRoot = '{$nbcImageRoot}';
{if $nbcFullDatasetCount}nbcFullDatasetCount = {$nbcFullDatasetCount};
{/if}
{if $nbcStart}nbcStart = {$nbcStart};
{/if}
{if $nbcPerPage}nbcPerPage = {$nbcPerPage};
{/if}
{if $nbcPerLine}nbcPerLine = {$nbcPerLine};
{/if}

{if $characteristics}
{foreach from=$characteristics item=v}
{assign var=foo value="|"|explode:$v.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$v.label}{assign var=cText value=''}{/if}
nbcAddCharacter({literal}{{/literal}id: {$v.id},type:'{$v.type}',label:'{$cLabel|addslashes}',text:'{$cText|addslashes|trim}'{*
				states : [{if $v.states && $v.type!='range'}{foreach from=$v.states item=s key=k}
			{literal}{{/literal}
			id:{$s.id},
			label:'{$s.label|addslashes}',
			file_name:'{$s.file_name|addslashes}',
			label:'{$s.label|addslashes}',
			text:'{$s.text|addslashes}',
			{literal}}{/literal}{if $k!=$v.states|@count-1},{/if}
			{/foreach}
				{/if}
          ]
        *}{literal}}{/literal});
{/foreach}
{/if}
{if $nbcSimilar}
nbcShowSimilar({$nbcSimilar[0]},'{$nbcSimilar[1]}');
{else}
{if $taxa}
nbcData = $.parseJSON('{$taxa}');
nbcProcessResults(false);
{else}
nbcGetResults();
{/if}
{/if}

{literal}
});
</script>
{/literal}


{include file="../shared/footer.tpl"}