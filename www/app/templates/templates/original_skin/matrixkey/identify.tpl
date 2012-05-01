{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">
	<div id="pane-left">
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
			<input type="button" onclick="deleteSelected()" value="{t}delete{/t}" />
			<input type="button" onclick="clearSelected()" value="{t}clear all{/t}" />
		</div>
		<div id="choices">
			{t}Selected combination of characters{/t}
			({t}treat unknowns as matches:{/t}
			<label><input onchange="getScores()" type="radio" value="n" id="unknowns-n" name="unknowns" checked="checked" />{t}no{/t}</label>
			<label><input onchange="getScores()" type="radio" value="y" id="unknowns-y" name="unknowns" />{t}yes{/t}</label>
			)
			<br />
			<select size="25" id="selected">
			</select>
		</div>
	</div>
	<div id="pane-right">
		<div id="scores-taxa">
			{t}Result of this combination of characters{/t}<br />
			<select size="5" id="scores">
			{foreach from=$taxa key=k item=v}
			<option ondblclick="goTaxon({$v.id})" value="{$v.id}">{$v.taxon}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			{foreach from=$matrices key=k item=v}
			<option ondblclick="goMatrix({$v.id})" value="{$v.id}">Matrix: {$v.name}</option>
			{/foreach}
			</select>
		</div>
	</div>
{$matrices|@var_dump}
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{foreach from=$characteristics key=k item=v}
	storeCharacteristic({$v.id},'{$v.label|addslashes}','{$v.type.name}');
{/foreach}
	imagePath = '{$session.app.project.urls.project_media}';
{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
