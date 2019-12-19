{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" id="backForm" action="index.php"><input type="hidden" name="action" value="repeat" /></form>
	<div id="results">
		<div id="replaceResultHeader">
			<p>
				{t}Searched for:{/t} {$search.search}<br />
				{t}To be replaced by:{/t} {$search.replacement}
			</p>
		{if $resultData.numOfReplacements > 0}
			<p>
				{t _s1=$resultData.numOfReplacements}Found %s results.{/t}<br />
			</p>
			<p>
				<input type="button" id="button-replace-all" value="{t}replace all{/t}" onclick="searchDoReplaceAll()" />&nbsp;
				<input type="button" id="button-skip-all" value="{t}skip all{/t}" onclick="searchDoSkipAll()" />&nbsp;
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
</div>

{include file="../shared/admin-footer.tpl"}
