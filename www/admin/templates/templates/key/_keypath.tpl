<div id="keypath">
	<span style="color:#888;cursor:pointer" onclick="keyToggleFullKeyPath()" title="{t}show entire path{/t}">&nabla;</span>
	<span id="keypath-title">{t}Keypath{if $keyPath[0].is_start==0} {t}(subsection){/t}{/if}{/t}: </span>
{assign var=first value=true}
{section name=i loop=$keyPath}
	{if $smarty.section.i.index==$keyPath|@count-1}
		<span class="pseudo-a" onclick="$('#pathNext').val({$keyPath[i].id});$('#pathForm').submit();">{if $keyPath[i].number}{$keyPath[i].number}. {/if}{if $keyPath[i].title}{$keyPath[i].title}{else}...{/if}</span>
	{else}	
		{if $keyPath|@count>3 && 
			$smarty.section.i.index!=0 &&
			$smarty.section.i.index!=$keyPath|@count-1 &&
			$smarty.section.i.index!=$keyPath|@count-2}
		{if $first}[...]&nbsp;&rarr;&nbsp;{/if}{assign var=first value=false}
		{else}
		<i>
			<span class="pseudo-a" onclick="$('#pathNext').val({$keyPath[i].id});$('#pathForm').submit();">{$keyPath[i].number}. {$keyPath[i].title}</span>{if $keyPath[i].choice}: {$keyPath[i].choiceTitle} 
			{/if}
		</i>
		{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}
		{/if}
	{/if}
{/section}
</div>
<div id="keypath-full" class="keypath-full-invisible">
<fieldset>
<legend>{if $keyPath[0].is_start==0}{t}Full subsection keypath:{/t}{else}{t}Full keypath{/t}{/if}</legend>
<table style="width:100%">
{section name=i loop=$keyPath}
	<tr>
	{if $smarty.section.i.index==$keyPath|@count-1}
		<td style="text-align:right;width:10px;">{if $keyPath[i].number}{$keyPath[i].number}.{/if}</td>{if $keyPath[i].title}<td>{$keyPath[i].title}</td>{else}<td colspan="2">...</td>{/if}
	{else}	
		<td style="text-align:right;width:10px;">{$keyPath[i].number}.</td>
		<td><span class="pseudo-a" onclick="$('#pathNext').val({$keyPath[i].id});$('#pathForm').submit();">{$keyPath[i].title}</span>{if $keyPath[i].choice}: {$keyPath[i].choiceTitle}{/if}<!--{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}--></td>
	{/if}
	<td></td>
</tr>
{/section}
<tr><td colspan="3" style="padding-top:10px">[<span class="pseudo-a" onclick="keyToggleFullKeyPath();">close</span>]</td></tr>
</table>
<form method="post" action="step_show.php" id="pathForm"><input type="hidden" name="id" id="pathNext" value="" /></form>
</fieldset>
</div>