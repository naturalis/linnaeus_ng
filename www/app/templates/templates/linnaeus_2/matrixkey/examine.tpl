{include file="../shared/header.tpl"}
{include file="_header.tpl"}
<div id="page-main">
	<div id="examine">
		<p>
			<select onchange="goExamine()" id="taxon-list">
			<option disabled="disabled" selected="selected">{t}Select a taxon{/t}</option>
			{foreach from=$taxa key=k item=v}
			<option value="{$v.id}">{$v.label}{if $v.is_hybrid==1} {$session.app.project.hybrid_marker}{/if}</option>
			{/foreach}
			</select>
		</p>
		
		<p id="help-text">{t}Select a taxon from the list to view characters and character states of this taxon. 
			These are used for the identification process under Identify.{/t}</p>
		<p>
			<table id="states" class="invisible">
			<thead>
				<tr>
					<th>{t}Type{/t}</th>
					<th>{t}Character{/t}</th>
					<th>{t}State{/t}</th>
					<th></th>
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
{literal}
});
</script>
{/literal}


{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
