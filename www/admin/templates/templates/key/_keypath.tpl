<div id="keypath">
	<span style="color:#888;cursor:pointer" onclick="keyToggleFullKeyPath()" title="{t}show entire path{/t}">&nabla;</span>
	<span id="keypath-title">{t}Keypath:{/t} </span>
{assign var=first value=true}
{section name=i loop=$keyPath}
	{if $smarty.section.i.index==$keyPath|@count-1}
		{if $keyPath[i].title}{$keyPath[i].number}. {$keyPath[i].title}{else}...{/if}
	{else}	
		{if $keyPath|@count>3 && 
			$smarty.section.i.index!=0 &&
			$smarty.section.i.index!=$keyPath|@count-1 &&
			$smarty.section.i.index!=$keyPath|@count-2}
		{if $first}[...]&nbsp;&rarr;&nbsp;{/if}{assign var=first value=false}
		{else}
		<i>
			<span class="pseudo-a" onclick="$('#next').val({$keyPath[i].id});$('#nextForm').submit();">{$keyPath[i].number}. {$keyPath[i].title}</span>{if $keyPath[i].choice}<!-- span class="keypath-edit" onclick="$('#id').val({$keyPath[i].id});$('#theForm').submit();">{t}edit{/t}</span -->:
				{$keyPath[i].choiceTitle} 
				<!-- span class="keypath-edit" onclick="$('#id2').val({$keyPath[i].choice});$('#choiceForm').submit();">{t}edit{/t}</span -->
			{/if}
		</i>
		{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}
		{/if}
	{/if}
{/section}
</div>
<div id="keypath-full" class="keypath-full-invisible">
<table style="width:100%">
<tr><td colspan="2">{t}Full keypath:{/t}</td><td style="width:10px;cursor:pointer;font-size:14px" onclick="keyToggleFullKeyPath()">x</td></tr>
{section name=i loop=$keyPath}
	<tr>
	{if $smarty.section.i.index==$keyPath|@count-1}
		{if $keyPath[i].title}<td style="text-align:right;width:10px;">{$keyPath[i].number}.</td><td>{$keyPath[i].title}</td>{else}<td colspan="2">...</td>{/if}
	{else}	
		<td style="text-align:right;width:10px;">{$keyPath[i].number}.</td>
		<td><span class="pseudo-a" onclick="$('#next').val({$keyPath[i].id});$('#nextForm').submit();">{$keyPath[i].title}</span>{if $keyPath[i].choice}: {$keyPath[i].choiceTitle}{/if}{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}</td>
	{/if}
	<td></td>
</tr>
{/section}
</table>
</div>