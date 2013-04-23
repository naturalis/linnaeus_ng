{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
	{t _s1=$matrix.matrix}Editing matrix "%s"{/t}
</span>
</p>
{t}Sort characters{/t}
<table>
{foreach item=v key=k from=$characteristics}
	<tr>
		<td>{$v.show_order+1}.</td>
		<td>{$v.label} ({$v.type.name})</td>
		<td title="{t}move up{/t}">{if $k>0}<a href="?id={$v.id}&r=u&rnd={$rnd}">&nbsp;&uarr;&nbsp;</a>{/if}</td>
		<td title="{t}move down{/t}">{if $k<$characteristics|@count-1}<a href="?id={$v.id}&r=d&rnd={$rnd}">&nbsp;&darr;&nbsp;</a>{/if}</td>
	</tr>
{/foreach}
</table>
<p>
	<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" />
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
