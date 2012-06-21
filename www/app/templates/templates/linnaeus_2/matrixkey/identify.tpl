{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">

{include file="_matrix-header.tpl"}

	<div id="search-pattern">
		<div id="char-states">
			<div class="select-header">{t}Characters{/t}</div>
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
			{foreach from=$characteristics key=k item=v}
			{if $v.label}
			<option value="{$v.id}">{$v.label}</option>
			{/if}
			{/foreach}
			</select>
			
			<div class="select-header">{t}States{/t}</div>
			<select size="5" id="states" onclick="goState()" ondblclick="addSelected(this)">
			</select>
			<div id="buttons">
				<input type="button" onclick="addSelected(this)" value="{t}Add{/t}" />
				<input type="button" onclick="clearSelected()" value="{t}Clear all{/t}" />
				<input type="button" onclick="showMatrixResults()" value="{t}Search &gt;&gt;{/t}" />
			</div>
		</div>
		<div id="info">
			<div id="info-header"></div>
			<div id="info-body">(info)</div>
			<div id="info-footer"></div>
		</div>
	</div>

	<div id="search-results">
		<div id="choices">
			<div class="select-header">{t}Selected combination of characters{/t}</div>
			<select size="25" id="selected">
			</select>
			<div id="unknowns">
			<input onchange="getScores()" type="checkbox" id="inc_unknowns" name="inc_unknowns" checked="checked" />
			<label for="inc_unknowns">{t}Treat unknowns as matches{/t}</label></div>
			<div id="buttons">
				<input type="button" onclick="showMatrixPattern(this)" value="{t}Add{/t}" />
				<input type="button" onclick="deleteSelected()" value="{t}Delete{/t}" />
				<input type="button" onclick="clearSelected(); showMatrixPattern();" value="{t}Clear all{/t}" />
			</div>
		</div>

		<div id="scores-taxa">
			<div class="select-header">{t}Result of this combination of characters{/t}</div>
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
