{include file="../shared/header.tpl"}
{include file="_header.tpl"}
<div id="page-main">
	<div id="compare">
		<p>
			<select id="taxon-list-1">
			<option disabled="disabled" selected="selected" value="">{t}select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.taxon}</option>
			{/foreach}
			</select>
		</p>
		<p>
			<select id="taxon-list-2">
			<option disabled="disabled" selected="selected" value="">{t}select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.taxon}</option>
			{/foreach}
			</select>
		</p>
		<p>
		<input type="button" onclick="goCompare()" value="{t}compare taxa{/t}" />
		</p>
		<p>
			<table id="states" class="invisible">
				<tr><td style="width:300px">Unique states in <span id="taxon-1"></span>:</td><td id="count-1"></td><td></td></tr>
				<tr><td>Unique states in <span id="taxon-2"></span>:</td><td id="count-2"></td><td></td></tr>
				<tr><td>States present in both:</td><td id="count-both"></td><td></td></tr>
				<tr><td>States present in neither:</td><td id="count-neither"></td><td></td></tr>
				<tr><td>Number of available states:</td><td id="count-total"></td><td></td></tr>
				<tr><td>Taxonomic distance:</td><td id="coefficient"></td><td id="formula" style="padding-left:5px"></td></tr>
			</table>
		</p>
	</div>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{foreach from=$characteristics key=k item=v}
	storeCharacteristic({$v.id},'{$v.label|addslashes}');
{/foreach}
{literal}
});
</script>
{/literal}


{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
