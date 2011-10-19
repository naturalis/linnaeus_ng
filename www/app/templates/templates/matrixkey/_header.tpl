<div id="matrix-header">
	<div id="current">
		{t _s1=$matrix.name _s2=$function}Using matrix "%s", function "%s"{/t}
		({t}switch to {/t}	
			{if $function!='Identify'}<a href="identify.php">{t}Identify{/t}</a> or {/if}{if $function!='Examine'}<a href="examine.php">{t}Examine{/t}</a>{if $function!='Compare'} or {/if}{/if}{if $function!='Compare'}<a href="compare.php">{t}Compare{/t}</a>{/if})
		{if $matrixCount>1}<br /><a href="matrices.php">{t}Switch to another matrix{/t}</a>{/if}.	
	</div>

</div>