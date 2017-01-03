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

	<tr><th>name:</th><td><input class="large" type="text" name="name" value="{$actor.name}" /></td></tr>
	<tr><th>alternative name:</th><td><input class="large" type="text" name="name_alt" value="{$actor.name_alt}" /></td></tr>
	<tr id="gender">
		<th>gender:</th>
		<td>
			<label><input type="radio" name="gender" value="f" {if $actor.gender=='f'}checked="checked"{/if} />f</label>
			<label><input type="radio" name="gender" value="m" {if $actor.gender=='m'}checked="checked"{/if} />m</label>
			<label><input type="radio" name="gender" id="no_gender" value="" {if $actor.gender!='f' && $actor.gender!='m'}checked="checked"{/if} />not specified</label>
		</td>
	</tr>
	<tr>
		<th>person or organisation:</th>
		<td>
			<label><input type="radio" name="is_company" value="0" {if $actor.is_company!='1'}checked="checked"{/if} />person</label>
			<label><input type="radio" name="is_company" value="1" {if $actor.is_company=='1'}checked="checked"{/if} />organisation</label>
		</td>
	</tr>
	<tr><th>homepage:</th><td><input class="large" type="text" name="homepage" value="{$actor.homepage}" /></td></tr>
	<tr id="employee_of">
		<th>employee of:</th>
		<td>
			<select id="employee_of_id" name="employee_of_id">
				<option value="" {if !$actor.employee_of_id} selected="selected"{/if}>-</option>
				{foreach from=$companies item=v key=k}
				<option value="{$v.id}" {if $v.id==$actor.employee_of_id} selected="selected"{/if}>{$v.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr id="employee_of">
		<th>NSR ID:</th>
		<td>{if $actor.id}{$actor.nsr_id}{else}(will be generated automatically){/if}</td>
	</tr>
{if ($actor.id && $CRUDstates.can_update) || (!$actor.id && $CRUDstates.can_create)}
	<tr><th><input type="submit" value="save" /></th><td></td></tr>
{/if}
{if $actor.id && $CRUDstates.can_delete}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete('Are you sure you want to delete &quot;{$actor.name|@escape}&quot;?\n{$links.presences|@count} statuses, {$links.names|@count} names and {$links.passports|@count} tabs are linked to this person.');return false;">delete actor</a></th><td></td></tr>
{/if}
</table>

</form>
</p>
<p>
<div>
	<b>Links</b><br />
	{if $links.presences|@count==0 && $links.names|@count==0 && $links.passports|@count==0 && $links.literature|@count==0}
	(no links)
	{/if}
	{if $links.names|@count > 0}
	<a href="#" onclick="$('#links-names').toggle();return false;">Linked names: {$links.names|@count}</a>
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
	<a href="#" onclick="$('#links-presences').toggle();return false;">Linked presence statuses: {$links.presences|@count}</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
	<br />
	{/if}

	{if $links.passports|@count > 0}
	<a href="#" onclick="$('#links-passports').toggle();return false;">Linked passports: {$links.passports|@count}</a>
	<div id="links-passports" style="display:none">
		<ul class="small">
        	{assign var=prev value=null}
            {foreach from=$links.passports item=v key=k}{if $v.taxon_id!=$prev}
            	</li><li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a><br />&nbsp;&nbsp;
			{else}, {/if}
            {$v.title}{assign var=prev value=$v.taxon_id}{/foreach}
			</li>
		</ul>
	</div>
	<br />
	{/if}

	{if $links.literature|@count > 0}
	<a href="#" onclick="$('#links-literature').toggle();return false;">Linked references: {$links.literature|@count}</a>
	<div id="links-literature" style="display:none">
		<ul class="small">
            {foreach from=$links.literature item=v key=k}
            <li><a href="../literature2/edit.php?id={$v.id}">{$v.label}</a></li>
            {/foreach}
		</ul>
	</div>
	<br />
	{/if}

</div>
</p>

<p>
	<a href="index.php">back</a>
</p>


</div>

<script>
$(document).ready(function()
{

	$( 'input[name=is_company]' ).on( "click", function() {
		$( 'input[name=is_company]' ).each(function(){
			if ($(this).attr('checked')=='checked')
			{
				if ($(this).val()==1)
				{
					$('#employee_of').toggle(false);
					$('#employee_of_id').val("");

					$('#gender').toggle(false);
					$('#no_gender').attr("checked","checked");
				}
				else
				{
					$('#employee_of').toggle(true);
					$('#gender').toggle(true);
				}
			}
		});
	} );

	{if $actor.is_company=='1'}
	$('#employee_of').toggle(false);
	{/if}

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
