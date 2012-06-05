<div id="path">
	<div id="concise">
	<span onclick="keyToggleFullPath()" id="toggle">{t}Path:{/t}</span>
	{foreach from=$keypath key=k item=v}
	{if $v.is_start==1 || $keypath|@count<=$keyPathMaxItems || ($keypath|@count>$keyPathMaxItems && $k>=$keypath|@count-2)}
		{if $v.is_start!=1}<span class="arrow">&rarr;</span>{/if}
		{$v.step_number}. <span class="item" onclick="keyDoStep({$v.id})">{$v.step_title}{if $v.choice_marker} ({$v.choice_marker}){/if}</span>
	{/if}
	{if $v.is_start==1 && $keypath|@count>$keyPathMaxItems}<span class="arrow">&rarr;</span><span class="abbreviation">[...]</span>{/if}
	{/foreach}
	</div>
	
	<div id="path-full" class="full-invisible">
	<table>
	{foreach from=$keypath key=k item=v}
		<tr>
			<td class="number-cell">{$v.step_number}. </td>
			<td><span class="item" onclick="keyDoStep({$v.id})">{$v.step_title}{if $v.choice_marker} ({$v.choice_marker}){/if}</span></td>
		</tr>
	{/foreach}
	</table>
	</div>
</div>