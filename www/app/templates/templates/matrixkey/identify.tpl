{include file="../shared/header.tpl"}
{include file="_header.tpl"}

<div id="page-main">
	<div id="pane-left">
		<div id="char-states">
			<select size="5" id="characteristics" onclick="goCharacteristic()">
			{foreach from=$characteristics key=k item=v}
			{if $v.label}
			<option value="{$v.id}">{$v.label}</option>
			{/if}
			{/foreach}
			</select>
			<br />
			<select size="5" id="states" onclick="goState()">
			</select>
		</div>
		<div id="info">
		(info)
		</div>
		<div id="buttons">
			<input type="button" onclick="addSelected()" value="{t}add{/t}" />
			<input type="button" onclick="deleteSelected()" value="{t}delete{/t}" />
			<input type="button" onclick="clearSelected()" value="{t}clear all{/t}" />
		</div>
		<div id="choices">
			<select size="25" id="selected">
			</select>		
		</div>
	</div>
	<div id="pane-right">
		<div id="scores-taxa">
			<select size="5" id="scores">
			{foreach from=$taxa key=k item=v}
			<option ondblclick="goTaxon({$v.id})" value="{$v.id}">{$v.taxon}</option>
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
	imagePath = '{$session.project.urls.project_media}';
{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
