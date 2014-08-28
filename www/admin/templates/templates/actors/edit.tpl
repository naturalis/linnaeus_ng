{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<h2>{$actor.name}</h2>
<h3>{$actor.employer_name}</h3>
</p>
<p>

<form method="post" id="theForm">
<input type="hidden" name="id" value="{$actor.id}">
<input type="hidden" name="action" id="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">
<table>

	<tr><th>naam:</th><td><input class="large" type="text" name="name" value="{$actor.name}" /></td></tr>
	{if $actor.name_alt}
	<tr><th>name_alt:</th><td><input class="small" type="text" name="name_alt" value="{$actor.name_alt}" /></td></tr>
	{/if}
	<tr>
		<th>geslacht:</th>
		<td>
			<label><input type="radio" name="gender" value="f" {if $actor.gender=='f'}checked="checked"{/if} />v</label>
			<label><input type="radio" name="gender" value="m" {if $actor.gender!='f'}checked="checked"{/if} />m</label>
		</td>
	</tr>
	<tr>
		<th>persoon of instelling:</th>
		<td>
			<label><input type="radio" name="is_company" value="0" {if $actor.is_company!='1'}checked="checked"{/if} />persoon</label>
			<label><input type="radio" name="is_company" value="1" {if $actor.is_company=='1'}checked="checked"{/if} />instelling</label>
		</td>
	</tr>
	<tr><th>homepage:</th><td><input class="large" type="text" name="homepage" value="{$actor.homepage}" /></td></tr>
	<tr>
		<th>werkt bij:</th>
		<td>
			<select id="employee_of_id" name="employee_of_id">
				<option value="" {if !$actor.employee_of_id} selected="selected"{/if}>-</option>
				{foreach from=$companies item=v key=k}
				<option value="{$v.id}" {if $v.id==$actor.employee_of_id} selected="selected"{/if}>{$v.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr><th><input type="submit" value="save" /></th><td></td></tr>
{if $actor.id}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete();return false;">actor verwijderen</a></th><td></td></tr>
{/if}
</table>

</form>
</p>
<p>
<div>
	<b>Koppelingen</b><br />
	{if $links.presences|@count==0 && $links.names|@count==0}
	(geen koppelingen)
	{/if}
	{if $links.names|@count > 0}
	<a href="#" onclick="$('#links-names').toggle();return false;">Gekoppelde namen: {$links.names|@count}</a>
	<div id="links-names" style="display:none">
		<ul class="small">
			{foreach from=$links.names item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.name}{if $v.nametype=='isValidNameOf'} ({$v.nametype_label}){else} ({$v.nametype_label} van {$v.taxon}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
	<br />
	{/if}
	{if $links.presences|@count > 0}
	<a href="#" onclick="$('#links-presences').toggle();return false;">Gekoppelde voorkomensstatussen: {$links.presences|@count}</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
	{/if}
</div>
</p>

<p>
	<a href="index.php">terug</a>
</p>


</div>

<script>
$(document).ready(function()
{
	/*
	$('[havedroplist=true]').each(function() {
		$(this).attr('autocomplete','off');
		$(this).bind('keyup', function(e) { 
			doNsrDropList({ e:e, id: $(this).attr('id') } )
		} );
	});
	*/

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
