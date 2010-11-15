<div id="keypath">
	<span id="keypath-title">Keypath: </span>
{section name=i loop=$keyPath}
	{if $smarty.section.i.index==$keyPath|@count-1}
		{if $keyPath[i].title}{$keyPath[i].number}. {$keyPath[i].title}{else}...{/if}
	{else}
		<i>
			<span class="pseudo-a" onclick="$('#next').val({$keyPath[i].id});$('#nextForm').submit();">{$keyPath[i].number}. {$keyPath[i].title}</span>{if $keyPath[i].choice}<!-- span class="keypath-edit" onclick="$('#id').val({$keyPath[i].id});$('#theForm').submit();">{t}edit{/t}</span -->:
				{$keyPath[i].choiceTitle} 
				<!-- span class="keypath-edit" onclick="$('#id2').val({$keyPath[i].choice});$('#choiceForm').submit();">{t}edit{/t}</span -->
			{/if}
		</i>
		{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}
	{/if}
{/section}
</div>