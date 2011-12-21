{include file="../shared/admin-header.tpl"}

<div id="page-main">
<a name="top"></a>
<form method="post" id="backForm" action="search_index.php"><input type="hidden" name="action" value="repeat" /></form>
	<div id="results">
		<div id="replaceResultHeader">
			<p>
				{t}Searched for:{/t} {$search.search}
			</p>
		{if $resultData.numOfReplacements > 0}
			<p>
				{t _s1=$resultData.numOfReplacements}Found %s results.{/t}<br />
			</p>
			<p>
				<input type="button" value="{t}back{/t}" onclick="$('#backForm').submit()" />
			</p>
		{else}
			<p>
				{t}No results.{/t}
			</p>
			<p>
				<input type="button" value="{t}back{/t}" onclick="$('#backForm').submit()" />
			</p>
		{/if}
		</div>
	
	{include file="_search_results.tpl"}
	
	</div>
	<div id="result-index">
	<div id="result-index-header">RESULT INDEX</div>
	<div id="result-index-body">
		{foreach from=$resultData key=cat item=resultItems}
		{foreach from=$resultItems.results item=modules}
		{if $modules.numOfResults>0}<a href="#{$modules.label|@strtolower|@replace:' ':'_'}">{$modules.label} ({$modules.numOfResults})</a><br />{/if}
		{/foreach}	
		{/foreach}
		<br />
		<a href="#body-top">(Back to top)</a>
		<br /><br />
		<input type="button" value="{t}back to search page{/t}" onclick="$('#backForm').submit()" />
	</div>
	</div>
	</div>
</div>

{include file="../shared/admin-footer.tpl"}
