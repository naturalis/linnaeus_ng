<div id="lookup-DialogContent">
{foreach from=$matrices key=k item=v}
	{if $v.id==$currentMatrixId}
		<p class="row row-selected">{$v.name}</p>
	{else}
		{if $useJavascriptLinks}
		<p class="row" onclick="goMatrix({$v.id})">{$v.name}</p>
		{else}
		<p class="row"><a href="../matrixkey/use_matrix.php?id={$v.id}">{$v.name}</a></p>
		{/if}
	{/if}
{/foreach}
</div>
