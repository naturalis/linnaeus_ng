<div id="matrix-header">
		{t}Matrix:{/t}
		{if $matrixCount>1}
			<a class="selectIcon" href="javascript:showMatrixSelect();void(0);" title="{t}Switch to another matrix{/t}">{$matrix.name}</a>
		{else}
			{$matrix.name}
		{/if}
</div>