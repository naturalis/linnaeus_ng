{include file="../shared/admin-header.tpl"}

<div id="page-main">
<span id="message-container" style="float:right;"></span>
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="{$matrix.id}" />
<input type="hidden" name="action" id="action" value="" />
<table>
{if $languages|@count > 1}
	<tr>
		<td>
		</td>
		<td>
		<span id="taxon-language-default-language">
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}{$languages[i].language}{/if}
{/section}		
		</span>
		</td>
		<td>
			<span id="project-language-tabs"></span>
		</td>
{/if}
	</tr>
	<tr>
		<td>
			{t}Matrix name:{/t}
		</td>
		<td>
			<input
				type="text"
				id="matrix-default" 
				onblur="matrixSaveMatrixName(allDefaultLanguage)"
				maxlength="64" />
		</td>
{if $languages|@count > 1}
		<td>
			<input
				type="text"
				id="matrix-other" 
				onblur="matrixSaveMatrixName(allActiveLanguage)"
				maxlength="64" />
		</td>
{/if}
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" onclick="matrixSaveMatrixNameAll();window.open('matrices.php','_top')" value="{t}save{/t}" />&nbsp;
			{if $matrix.id}<input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic()" />&nbsp;{/if}
			<input type="button" value="{t}back{/t}" onclick="window.open('{$session.admin.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>
</p>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allActiveView = 'matrixname';
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
	allActiveLanguage =  {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	
	matrixGetMatrixName(allDefaultLanguage);
	matrixGetMatrixName(allActiveLanguage);

{literal}	
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}