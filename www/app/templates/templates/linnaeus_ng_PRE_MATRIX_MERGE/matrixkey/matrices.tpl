<div id="lookup-DialogContent">
{foreach $matrices v k}
	{if $v.id==$currentMatrixId}
		<p class="row row-selected">{$v.name}</p>
	{else}
    <p class="row"><a href="../matrixkey/use_matrix.php?id={$v.id}">{$v.name}</a></p>
	{/if}
{/foreach}
</div>
