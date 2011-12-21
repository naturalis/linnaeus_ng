{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
	{t _s1=$matrix.matrix}Editing matrix "%s"{/t}
</span>
</p>
UNFINISHED - CURRENTY SUFFERING FROM BLACKOUT

<table>
{foreach item=v key=k from=$characteristics}
	<tr>
		<td>{$v.label} ({$v.type.name})</td>
		<td>{if $k>0}<a href="?id={$v.id}&r=u&rnd={$rnd}">&nbsp;&uarr;&nbsp;</a>{/if}</td>
		<td>{if $k<$characteristics|@count-1}<a href="?id={$v.id}&r=d&rnd={$rnd}">&nbsp;&darr;&nbsp;</a>{/if}</td>
	</tr>
{/foreach}
</table>


</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
