{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">

<div id="matrix-header">
	<div id="current">
		{t _s1=$matrix.name _s2=$function}Using matrix "%s", function "%s"{/t}
		({t}switch to {/t}	
			{if $function!='Identify'}<a href="identify.php">{t}Identify{/t}</a> or {/if}{if $function!='Examine'}<a href="examine.php">{t}Examine{/t}</a>{if $function!='Compare'} or {/if}{/if}{if $function!='Compare'}<a href="compare.php">{t}Compare{/t}</a>{/if})
		{if $matrixCount>1}<br /><a href="matrices.php">{t}Switch to another matrix{/t}</a>{/if}.	
	</div>
	
</div>

	<div id="search-pattern">
		<div id="char-states">
			{t}Characters{/t}<br />
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
			{foreach from=$characteristics key=k item=v}
			{if $v.label}
			<option value="{$v.id}">{$v.label}</option>
			{/if}
			{/foreach}
			</select>
			<br />
			{t}States{/t}<br />
			<select size="5" id="states" onclick="goState()" ondblclick="addSelected(this)">
			</select>
		</div>
		<div id="info">
		(info)
		</div>
		<div id="buttons">
			<input type="button" onclick="addSelected(this)" value="{t}add{/t}" />
			<input type="button" onclick="clearSelected()" value="{t}clear all{/t}" />
			<input type="button" onclick="showMatrixResults()" value="{t}search{/t}" />
		</div>
	</div>

	<div id="search-results">
		<div id="choices">
			{t}Selected combination of characters{/t}
			({t}treat unknowns as matches:{/t}
			<label><input onchange="getScores()" type="radio" value="n" id="unknowns-n" name="unknowns" checked="checked" />{t}no{/t}</label>
			<label><input onchange="getScores()" type="radio" value="y" id="unknowns-y" name="unknowns" />{t}yes{/t}</label>
			)
			<br />
			<select size="25" id="selected">
			</select>
			<div id="buttons">
				<input type="button" onclick="showMatrixPattern(this)" value="{t}add{/t}" />
				<input type="button" onclick="deleteSelected()" value="{t}delete{/t}" />
				<input type="button" onclick="clearSelected()" value="{t}clear all{/t}" />
			</div>
		</div>


		<div id="scores-taxa">
			{t}Result of this combination of characters{/t}<br />
			<select size="5" id="scores">
			{foreach from=$taxa key=k item=v}
			<option ondblclick="goTaxon({$v.id})" value="{$v.id}">{$v.label}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			{foreach from=$matrices key=k item=v}
			<option ondblclick="goMatrix({$v.id})" value="{$v.id}">Matrix: {$v.name}</option>
			{/foreach}
			</select>
		</div>
	</div>
</div>




{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{foreach from=$characteristics key=k item=v}
	storeCharacteristic({$v.id},'{$v.label|addslashes}','{$v.type.name}');
{/foreach}
	imagePath = '{$session.app.project.urls.uploadedMedia}';
	
{if $storedStates}
{foreach from=$storedStates key=k item=v}
	setSelectedState('{$v.val}'{if $v.type=='c'},{$v.id},{$v.characteristic_id},'{$v.label|addslashes}'{/if});
{/foreach}
	getScores();
	showMatrixResults();
	highlightSelected();
{/if}
	
{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
