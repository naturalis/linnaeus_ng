{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div id="results">
	<div id="header">
		<p>
			Searched for: {$search.search}<br />
			Replace by: {$search.replace}
		</p>
		<p>
			<input type="button" id="button-replace-all" value="replace all" onclick="searchDoReplaceAll()" />&nbsp;
			<input type="button" id="button-skip-all" value="skip all" onclick="searchDoSkipAll()" />
		</p>
	</div>


	{foreach from=$results key=cat item=v}
		{if $cat!='numOfResults'}
			{foreach from=$v.results item=vv}
				{capture name=moduleHeader}
				<div class="replaceModuleHeader">{$vv.numOfReplacements} found in <span class="replaceModuleTitle">{$vv.label}</span>
				{if $vv.numOfReplacements >0}
					&nbsp;
					<input
						type="button"
						id="button-replace-{$vv.label|@strtolower|@replace:' ':'_'}"
						value="replace all"
						onclick="searchDoReplaceModule('{$vv.label}')" 
					/>&nbsp;
					<input
						type="button"
						id="button-skip-{$vv.label|@strtolower|@replace:' ':'_'}"
						value="skip all"
						onclick="searchDoSkipModule('{$vv.label}')"
					/>
				{/if}
				</div>
				{/capture}
				{capture name=moduleBody}
				{assign var=printedItems value=0}
				{foreach from=$vv.data item=vvv}
					{foreach from=$vvv.replace.matches item=vvvv}
					{if $vvvv.status==''}
						<div id="replace-{$vv.label|@strtolower|@replace:' ':'_'}-id-{$vvvv.id}" class="replaceItem">
						{*<span class="replaceItemHeader">{$vvvv.column} ({if $vvvv.status}{$vvvv.status}{else}{t}to be replaced{/t}{/if}):</span><br />*}
						<span class="replaceItemHeader">{$vvvv.column}:</span><br />
						{$vvvv.highlighted}
						<p>
						<input
							type="button"
							id="button-replace-{$vv.label|@strtolower|@replace:' ':'_'}-id-{$vvvv.id}"
							value="replace"
							onclick="searchDoReplace('{$vvvv.id}')"
						/>&nbsp;
						<input
							type="button" 
							id="button-skip-{$vv.label|@strtolower|@replace:' ':'_'}-id-{$vvvv.id}"
							value="skip" 
							onclick="searchDoSkip('{$vvvv.id}')" 
						/>
						</p>
						</div>
						{assign var=printedItems value=$printedItems+1}
					{/if}
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
