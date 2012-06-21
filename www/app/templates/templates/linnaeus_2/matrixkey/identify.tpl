{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">

<div id="matrix-header">
		{t _s1=$matrix.name _s2=$function}Matrix: 
		{if $matrixCount>1}
			<a class="selectIcon" href="matrices.php" title="{t}Switch to another matrix{/t}">%s</a>
		{else}
			%s
		{/if}
		{/t}	
</div>

	<div id="search-pattern">
		<div id="char-states">
			<div class="label">{t}Characters{/t}</div>
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
			{foreach from=$characteristics key=k item=v}
			{if $v.label}
			<option value="{$v.id}">{$v.label}</option>
			{/if}
			{/foreach}
			</select>
			
			<div class="label">{t}States{/t}</div>
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
			<div class="label">{t}Selected combination of characters{/t}</div>
			<select size="25" id="selected">
			</select>
			({t}treat unknowns as matches:{/t}
			<label><input onchange="getScores()" type="radio" value="n" id="unknowns-n" name="unknowns" checked="checked" /> {t}no{/t}</label>
			<label><input onchange="getScores()" type="radio" value="y" id="unknowns-y" name="unknowns" /> {t}yes{/t}</label>
			)
			<div id="buttons">
				<input type="button" onclick="showMatrixPattern(this)" value="{t}add{/t}" />
				<input type="button" onclick="deleteSelected()" value="{t}delete{/t}" />
				<input type="button" onclick="clearSelected(); showMatrixPattern();" value="{t}clear all{/t}" />
			</div>
		</div>

		<div id="scores-taxa">
			<div class="label">{t}Result of this combination of characters{/t}</div>
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
