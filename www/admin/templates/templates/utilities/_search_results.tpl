	{foreach from=$resultData key=cat item=resultItems}
		{if $cat!='numOfResults'}
			{foreach from=$resultItems.results item=modules}
				{capture name=moduleHeader}
				<div class="replaceModuleHeader">{$modules.numOfReplacements} found in <span class="replaceModuleTitle">{$modules.label}</span>
				{if $modules.numOfReplacements > 0 && $includeReplace}
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

						{if $replaceIndex[$occurrences.id]===false && $occurrences|is_array}
							<div id="replace-{$modules.label|@strtolower|@replace:' ':'_'}-id-{$occurrences.id}" class="replaceItem">
							{* $occurrences.id *}
							<span class="replaceItemHeader">
								{$k}{if $includeReplace} ({if $replaceIndex[$occurrences.id]}{$replaceIndex[$occurrences.id]}{else}{t}to be replaced{/t}{/if}){/if}:</span>
							<br />							
							{$occurrences.highlighted}
							<p>
							{if $includeReplace}
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
							{/if}
							<input
								type="button"
								value="{t}go to page{/t}" 
								onclick="window.open('{$moduleData.url}','_example');" 
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