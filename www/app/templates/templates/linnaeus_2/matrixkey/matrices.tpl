<div id="lookup-DialogContent">
{foreach from=$matrices key=k item=v}
	{if $v.id==$currentMatrixId}
		<p class="row row-selected">{$v.name}</p>
	{else}
		<p class="row" onclick="goMatrix({$v.id})">{$v.name}</p>
	{/if}
{/foreach}

</div>