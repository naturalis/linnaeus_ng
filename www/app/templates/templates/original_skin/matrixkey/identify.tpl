{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">
	<div id="pane-left">
		<div id="char-states">
			{t}Characters{/t} <a href="javascript:showCharacterSort();void(0);">{t}sort{/t}</a><br />
			<select size="5" id="characteristics" onclick="goCharacter()" ondblclick="addSelected(this)" >
			</select>
			<br />
			{t}States{/t}<br />
			<select size="5" id="states" onclick="goState()" ondblclick="addSelected(this)">
			</select>
		</div>
		<div id="info">
			<div id="info-header"></div>
			<div id="info-body">(info)</div>
			<div id="info-footer"></div>
		</div>
		<div id="buttons">
			<input type="button" onclick="addSelected(this)" value="{t}add{/t}" />
			<input type="button" onclick="deleteSelectedState()" value="{t}delete{/t}" />
			<input type="button" onclick="clearSelectedStates()" value="{t}clear all{/t}" />
		</div>
		<div id="choices">
			{t}Selected combination of characters{/t}
			<select size="25" id="selected">
			</select>
			{t}treat unknowns as matches:{/t}
			<label><input onchange="getScores()" type="checkbox" id="inc_unknowns" name="inc_unknowns" checked="checked" /></label>
			<br />
		</div>
	</div>
	<div id="pane-right">
		<div id="scores-taxa">
			{t}Result of this combination of characters{/t}<br />
			<select size="5" id="scores">
			{foreach from=$taxa key=k item=v}{$v.label}
			<option ondblclick="goTaxon({$v.id})" value="{$v.id}">{$v.l}{if $v.h==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			{foreach from=$matrices key=k item=v}
			<option ondblclick="goMatrix({$v.id})" value="{$v.id}">Matrix: {$v.l}</option>
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
	storeCharacter(
		{$v.id},
		'{$v.label|addslashes}',
		'{$v.type.name}',
		{literal}{{/literal}
			'alphabet':'{$v.sort_by.alphabet}',
			'separationCoefficient':{$v.sort_by.separationCoefficient},
			'characterType':'{$v.sort_by.characterType}',
			'numberOfStates':{$v.sort_by.numberOfStates},
			'entryOrder':{$v.sort_by.entryOrder}
		{literal}}{/literal}
	);
{/foreach}
	sortCharacters('entryOrder');
	imagePath = '{$session.app.project.urls.uploadedMedia}';

{if $storedStates}
{foreach from=$storedStates key=k item=v}
	setSelectedState('{$v.val}'{if $v.type=='c'},{$v.id},{$v.characteristic_id},'{$v.label|addslashes}'{/if});
{/foreach}
	getScores();
//	showMatrixResults();
	highlightSelected();
{/if}

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}