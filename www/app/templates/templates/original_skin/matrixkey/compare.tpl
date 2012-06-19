{include file="../shared/header.tpl"}
{include file="_header.tpl"}
<div id="page-main">
	<div id="compare">
		 <p id="help-text">{t}Select two taxa from the lists and click Compare to compare the characters and character states for both taxa. The results show the differences and similarities for both taxa.{/t}</p>	
		<p>
			<select id="taxon-list-1">
			<option disabled="disabled" selected="selected" value="">{t}select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.label}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			</select>
		</p>
		<p>
			<select id="taxon-list-2">
			<option disabled="disabled" selected="selected" value="">{t}select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.label}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}
</option>
			{/foreach}
			</select>
		</p>
		<p>
		<input type="button" onclick="goCompare()" value="{t}compare taxa{/t}" />
		</p>
		<div id="overview" class="invisible">

			<b>{t _s1='<span id="taxon_name_1"></span>'}Unique character states for %s:{/t}</b>
			<p id="states1"></p>
			<br />
			<b>{t _s1='<span id="taxon_name_2"></span>'}Unique character states for %s:{/t}</b>
			<p id="states2"></p>
			<br />
			<b>{t}Shared character states:{/t}</b>
			<p id="statesBoth"></p>
			<br />
		</div>
		<p>
			<table id="comparison" class="invisible">
				<tr class="highlight"><td style="width:300px">{t}Unique states in{/t} <span id="taxon-1"></span>:</td><td id="count-1"></td><td></td></tr>
				<tr class="highlight"><td>{t}Unique states in{/t} <span id="taxon-2"></span>:</td><td id="count-2"></td><td></td></tr>
				<tr class="highlight"><td>{t}States present in both:{/t}</td><td id="count-both"></td><td></td></tr>
				<tr class="highlight"><td>{t}States present in neither:{/t}</td><td id="count-neither"></td><td></td></tr>
				<tr class="highlight"><td>{t}Number of available states:{/t}</td><td id="count-total"></td><td></td></tr>
				<tr class="highlight"><td>{t}Taxonomic distance:{/t}</td><td id="coefficient"></td><td id="formula" style="padding-left:5px"></td></tr>
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
