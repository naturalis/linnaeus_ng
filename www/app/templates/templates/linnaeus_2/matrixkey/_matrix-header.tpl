<div id="matrix-header">
		{t _s1=$matrix.name _s2=$function}Matrix: 
		{if $matrixCount>1}
			<a class="selectIcon" href="javascript:showMatrixSelect();void(0);" title="{t}Switch to another matrix{/t}">%s</a>
		{else}
			%s
		{/if}
		{/t}	
</div>