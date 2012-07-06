<!--
<table>
{foreach from=$matrices key=k item=v}
<tr class="highlight">
	{if $v.id==$currentMatrixId}
		<td>&#149; {$v.name}</td>
	{else}
		{if $useJavascriptLinks}
		<td class="a" style="width:200px" onclick="goMatrix({$v.id})">{$v.name}</td>
		{else}
		<td style="width:200px"><a href="../matrixkey/use_matrix.php?id={$v.id}">{$v.name}</a></td>
		{/if}
	{/if}
</tr>
{/foreach}
</table>
-->

<div id="lookup-DialogContent">
{foreach from=$matrices key=k item=v}
	{if $v.id==$currentMatrixId}
		<p class="row row-selected">{$v.name}</p>
	{else}
		<p class="row" onclick="goMatrix({$v.id})">{$v.name}</p>
	{/if}
{/foreach}

</div>
