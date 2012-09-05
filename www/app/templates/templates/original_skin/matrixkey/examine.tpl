{include file="../shared/header.tpl"}
{include file="_header.tpl"}
<div id="page-main">

	<p id="help-text">{t}Select a taxon from the list to view characters and character states of this taxon. 
   These are used for the identification process under Identify.{/t}</p>
   
	<div id="examine">
		<p>
			<select onchange="goExamine()" id="taxon-list">
			<option disabled="disabled" selected="selected">{t}select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.label}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			</select>
		</p>
		<p>
			<table id="states">
			<thead>
				<tr>
					<th style="width:100px">{t}type{/t}</th>
					<th style="width:250px">{t}character{/t}</th>
					<th>{t}state{/t}</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
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

{if $examineSpeciesRecall}
	goExamine({$examineSpeciesRecall});
{/if}

{literal}
});
</script>
{/literal}


{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
