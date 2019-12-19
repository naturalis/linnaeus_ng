{include file="../shared/admin-header.tpl"}

<div id="page-main">
<a name="top"></a>
<form method="post" id="backForm" action="index.php"><input type="hidden" name="action" value="repeat" /></form>
	<div id="results">
		<div id="replaceResultHeader">
			<p>
				{t}Searched for:{/t} <b>{$search.search}</b>, 
		{if $resultData.numOfResults > 0}
				{t _s1=$resultData.numOfResults}found %s results.{/t}<br />
		{else}
				{t _s1=$resultData.numOfResults}found no results.{/t}<br />
		{/if}
			</p>
			<p>
				<input type="button" value="{t}back{/t}" onclick="$('#backForm').submit()" />
			</p>
		</div>
	
	{include file="_search_results.tpl"}
	
	</div>
	{if $resultData.numOfResults > 0}
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
	{/if}
	</div>
</div>

{include file="../shared/admin-footer.tpl"}
