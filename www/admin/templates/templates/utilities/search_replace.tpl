{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" id="backForm" action="search_index.php"><input type="hidden" name="action" value="repeat" /></form>
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

	{foreach from=$resultData key=cat item=resultItems}
		{if $cat!='numOfResults'}
			{foreach from=$resultItems.results item=modules}
				{capture name=moduleHeader}
				<div class="replaceModuleHeader">{$modules.numOfReplacements} found in <span class="replaceModuleTitle">{$modules.label}</span>
				{if $modules.numOfReplacements >0}
					&nbsp;
					<input
						type="button"
						id="button-replace-{$modules.label|@strtolower|@replace:' ':'_'}"
						value="replace all"
						onclick="searchDoReplaceModule('{$modules.label}')" 
					/>&nbsp;
					<input
						type="button"
						id="button-skip-{$modules.label|@strtolower|@replace:' ':'_'}"
						value="skip all"
						onclick="searchDoSkipModule('{$modules.label}')"
					/>
				{/if}
				</div>
				{/capture}
				{capture name=moduleBody}

				{assign var=printedItems value=0}

				{foreach from=$modules.data item=moduleData}

					{foreach from=$moduleData.replace.matches key=k item=columns}
					
						{foreach from=$columns item=occurrences}						

						{if $replaceIndex[$occurrences.id]===false}
							<div id="replace-{$modules.label|@strtolower|@replace:' ':'_'}-id-{$occurrences.id}" class="replaceItem">
							{* $occurrences.id *}
							<span class="replaceItemHeader">
								{$k} ({if $replaceIndex[$occurrences.id]}{$replaceIndex[$occurrences.id]}{else}{t}to be replaced{/t}{/if}):</span>
							<br />
							{$occurrences.highlighted}
							<p>
							<input
								type="button"
								id="button-replace-{$modules.label|@strtolower|@replace:' ':'_'}-id-{$occurrences.id}"
								value="{t}replace{/t}"
								onclick="searchDoReplace('{$occurrences.id}')"
							/>&nbsp;
							<input
								type="button" 
								id="button-skip-{$modules.label|@strtolower|@replace:' ':'_'}-id-{$occurrences.id}"
								value="{t}skip{/t}" 
								onclick="searchDoSkip('{$occurrences.id}')" 
							/>&nbsp;
							<input
								type="button"
								value="{t}go to page{/t}" 
								onclick="window.open('{$columns.url}','_example');" 
							/>
							</p>
							</div>
							{assign var=printedItems value=$printedItems+1}
						{/if}
						{/foreach}
					{/foreach}
				{/foreach}
				{/capture}
				{if $printedItems>0}
				<div class="replaceModule">
				{$smarty.capture.moduleHeader}
				{$smarty.capture.moduleBody}
				</div>
				{/if}				
			{/foreach}
		{/if}
	{/foreach}
</div>
</div>

{include file="../shared/admin-footer.tpl"}
